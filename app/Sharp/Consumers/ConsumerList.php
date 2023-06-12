<?php

namespace App\Sharp\Consumers;

use App\Models\ConcreteSession;
use App\Models\Consumer;
use App\Models\Customer;
use Code16\Sharp\EntityList\Fields\EntityListField;
use Code16\Sharp\EntityList\Fields\EntityListFieldsContainer;
use Code16\Sharp\EntityList\Fields\EntityListFieldsLayout;
use Code16\Sharp\EntityList\SharpEntityList;
use Illuminate\Contracts\Support\Arrayable;

class ConsumerList extends SharpEntityList
{
    public function buildListFields(EntityListFieldsContainer $fieldsContainer): void
    {
        $fieldsContainer
            ->addField(
                EntityListField::make('name')
                    ->setSortable()
                    ->setLabel('Nom')
            )
            ->addField(
                EntityListField::make('customer')
                    ->setLabel('Client')
            )
            ->addField(
                EntityListField::make('rfid_code')
                    ->setLabel('N° Rfid')
            )
            ->addField(
                EntityListField::make('concrete_sessions_count')
                    ->setLabel('Sessions')
            )
            ->addField(
                EntityListField::make('concrete_sessions_quantity_sum')
                    ->setLabel('Total')
            )
            ->addField(
                EntityListField::make('last_concrete_session_at')
                    ->setLabel('Dernière session')
            );
    }

    public function buildListLayout(EntityListFieldsLayout $fieldsLayout): void
    {
        $fieldsLayout
            ->addColumn('name', 2)
            ->addColumn('customer', 2)
            ->addColumn('rfid_code', 3)
            ->addColumn('concrete_sessions_count', 1)
            ->addColumn('concrete_sessions_quantity_sum', 2)
            ->addColumn('last_concrete_session_at', 2);
    }

    protected function getInstanceCommands(): ?array
    {
        return [];
    }

    public function buildListConfig(): void
    {
        $this
            ->configureDefaultSort('created_at', 'desc')
            ->configureSearchable();
    }

    public function getListData(): array|Arrayable
    {
        $consumers = Consumer::orderBy($this->queryParams->sortedBy(), $this->queryParams->sortedDir())
                ->when($this->queryParams->hasSearch(), function ($posts) {
                    foreach ($this->queryParams->searchWords() as $word) {
                        $posts->where(function ($query) use ($word) {
                            $query
                                ->orWhere('name', 'like', $word)
                                ->orWhere('rfid_code', 'like', $word);
                        });
                    }
                });

        return $this
            ->setCustomTransformer('concrete_sessions_count', function ($value, Consumer $consumer) {
                return $consumer->concreteSessions()->count();
            })
            ->setCustomTransformer('customer', function ($value, Consumer $consumer) {
                return sprintf('<span style="font-style: italic; background-color: %s" class="text-white rounded px-2 py-1">%s</span>',
                    $consumer->customer->color->value,
                    $consumer->customer->name,
                );
            })
            ->setCustomTransformer('last_concrete_session_at', function ($value, Consumer $consumer) {
                $lastSession = ConcreteSession::where('consumer_id', $consumer->id)
                    ->orderBy('delivered_at', 'desc')->first();

                return $lastSession?->delivered_at->diffForHumans() ?? '<i>Aucune</i>';
            })
            ->setCustomTransformer('concrete_sessions_quantity_sum', function ($value, Consumer $consumer) {
                $quantity = $consumer->concreteSessions->reduce(function ($carry, $item) {
                    return $carry + $item->quantity;
                }, 0);

                return number_format($quantity / 100, 2, ',', '') . ' m³';
            })
            ->transform($consumers->get());
    }
}
