<?php

namespace App\Sharp\ConcreteSessions;

use App\Models\ConcreteSession;
use App\Sharp\ConcreteSessions\Commands\DownloadPdfCommand;
use App\Sharp\ConcreteSessions\Filters\SessionDeliveryDateRangeFilter;
use Code16\Sharp\EntityList\Fields\EntityListField;
use Code16\Sharp\EntityList\Fields\EntityListFieldsContainer;
use Code16\Sharp\EntityList\Fields\EntityListFieldsLayout;
use Code16\Sharp\EntityList\SharpEntityList;
use Code16\Sharp\Utils\Links\LinkToShowPage;
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
                EntityListField::make('consumer')
                    ->setLabel('Utilisateur')
            )
            ->addField(
                EntityListField::make('file_name')
                    ->setLabel('Fichier')
            )
            ->addField(
                EntityListField::make('quantity')
                    ->setLabel('Qté')
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
            ->addColumn('consumer', 3)
            ->addColumn('concrete_type', 5)
            ->addColumn('quantity', 1);
    }

    protected function getInstanceCommands(): ?array
    {
        return [
            DownloadPdfCommand::class,
        ];
    }

    protected function getFilters(): ?array
    {
        return [
            SessionDeliveryDateRangeFilter::class,
        ];
    }

    public function buildListConfig(): void
    {
        $this
            ->configurePaginated()
            ->configureSearchable();
    }

    public function getListData(): array|Arrayable
    {
        $concreteSessions = ConcreteSession::orderBy('delivered_at', 'desc')
            ->when($this->queryParams->hasSearch(), function ($posts) {
                foreach ($this->queryParams->searchWords() as $word) {
                    $posts->where(function ($query) use ($word) {
                        $query
                            ->orWhere('file_name', 'like', $word)
                            ->orWhere('concrete_type', 'like', $word);
                    });
                }
            });

        if ($range = $this->queryParams->filterFor('delivered_at')) {
            $concreteSessions->whereBetween(
                'delivered_at',
                [$range['start'], $range['end']]
            );
        }

        return $this
            ->setCustomTransformer('delivered_at', function ($value, ConcreteSession $concreteSession) {
                return $concreteSession->delivered_at->isoFormat('LLLL');
            })
            ->setCustomTransformer('quantity', function ($value, ConcreteSession $concreteSession) {
                return number_format($concreteSession->quantity / 100, 2, ',', '') . ' m³';
            })
            ->setCustomTransformer('consumer', function ($value, ConcreteSession $concreteSession) {
                return LinkToShowPage::make('consumers', $concreteSession->consumer->id)
                    ->renderAsText($concreteSession->consumer->name) . sprintf('<div class="mt-1"><span style="font-style: italic; background-color: %s" class="text-white rounded px-2 py-1">%s</span></div',
                        $concreteSession->consumer->customer->color->value,
                        $concreteSession->consumer->customer->name,
                    );
            })
            ->transform($concreteSessions->paginate(50));
    }
}
