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
            );
    }

    public function buildListLayout(EntityListFieldsLayout $fieldsLayout): void
    {
        $fieldsLayout
            ->addColumn('delivered_at', 6)
            ->addColumn('file_name', 6);
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
            ->where('consumer_id', $this->queryParams->filterFor('consumer_id'));

        return $this
            ->setCustomTransformer('delivered_at', function ($value, ConcreteSession $concreteSession) {
                return $concreteSession->delivered_at->isoFormat('LLLL');
            })
            ->transform($concreteSessions->get());
    }
}
