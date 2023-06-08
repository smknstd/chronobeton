<?php

namespace App\Sharp\ConcreteSessions;

use App\Models\ConcreteSession;
use App\Sharp\ConcreteSessions\Commands\DownloadPdfCommand;
use Code16\Sharp\EntityList\Fields\EntityListField;
use Code16\Sharp\EntityList\Fields\EntityListFieldsContainer;
use Code16\Sharp\EntityList\Fields\EntityListFieldsLayout;
use Code16\Sharp\EntityList\SharpEntityList;
use Illuminate\Contracts\Support\Arrayable;

class ConcreteSessionList extends SharpEntityList
{
    public function buildListFields(EntityListFieldsContainer $fieldsContainer): void
    {
        $fieldsContainer
            ->addField(
                EntityListField::make('delivered_at')
                    ->setLabel('Date')
            )
            ->addField(
                EntityListField::make('file_name')
                    ->setLabel('Fichier')
            )
            ->addField(
                EntityListField::make('quantity')
                    ->setLabel('Quantité')
            )
            ->addField(
                EntityListField::make('concrete_type')
                    ->setLabel('Type de béton')
            );
    }

    public function buildListLayout(EntityListFieldsLayout $fieldsLayout): void
    {
        $fieldsLayout
            ->addColumn('delivered_at', 3)
            ->addColumn('concrete_type', 3)
            ->addColumn('quantity', 2)
            ->addColumn('file_name', 4);
    }

    protected function getInstanceCommands(): ?array
    {
        return [
            DownloadPdfCommand::class,
        ];
    }

    public function buildListConfig(): void
    {
        $this
            ->configureSearchable();
    }

    public function getListData(): array|Arrayable
    {
        $concreteSessions = ConcreteSession::orderBy('delivered_at', 'desc')
            ->where('consumer_id', $this->queryParams->filterFor('consumer_id'))
            ->when($this->queryParams->hasSearch(), function ($posts) {
                foreach ($this->queryParams->searchWords() as $word) {
                    $posts->where(function ($query) use ($word) {
                        $query
                            ->orWhere('file_name', 'like', $word)
                            ->orWhere('concrete_type', 'like', $word);
                    });
                }
            });

        return $this
            ->setCustomTransformer('delivered_at', function ($value, ConcreteSession $concreteSession) {
                return $concreteSession->delivered_at->isoFormat('LLLL');
            })
            ->setCustomTransformer('quantity', function ($value, ConcreteSession $concreteSession) {
                return number_format($concreteSession->quantity / 100, 2, ',', '') . ' m³';
            })
            ->transform($concreteSessions->get());
    }
}
