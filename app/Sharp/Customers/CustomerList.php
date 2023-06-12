<?php

namespace App\Sharp\Customers;

use App\Models\Consumer;
use App\Models\Customer;
use App\Models\User;
use Code16\Sharp\EntityList\Fields\EntityListField;
use Code16\Sharp\EntityList\Fields\EntityListFieldsContainer;
use Code16\Sharp\EntityList\Fields\EntityListFieldsLayout;
use Code16\Sharp\EntityList\SharpEntityList;
use Illuminate\Contracts\Support\Arrayable;

class CustomerList extends SharpEntityList
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
                EntityListField::make('consumers_count')
                    ->setLabel('Utilisateurs')
            )
            ->addField(
                EntityListField::make('concrete_sessions_count')
                    ->setLabel('Sessions')
            )
            ->addField(
                EntityListField::make('concrete_sessions_quantity_sum')
                    ->setLabel('Total')
            );
    }

    public function buildListLayout(EntityListFieldsLayout $fieldsLayout): void
    {
        $fieldsLayout
            ->addColumn('name', 6)
            ->addColumn('consumers_count', 2)
            ->addColumn('concrete_sessions_count', 2)
            ->addColumn('concrete_sessions_quantity_sum', 2);
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
        $customers = Customer::orderBy($this->queryParams->sortedBy(), $this->queryParams->sortedDir())
                ->when($this->queryParams->hasSearch(), function ($posts) {
                    foreach ($this->queryParams->searchWords() as $word) {
                        $posts->where(function ($query) use ($word) {
                            $query
                                ->orWhere('name', 'like', $word);
                        });
                    }
                });

        return $this
            ->setCustomTransformer('consumers_count', function ($value, Customer $customer) {
                return $customer->consumers->count();
            })
            ->setCustomTransformer('concrete_sessions_count', function ($value, Customer $customer) {
                return $customer->consumers->reduce(function (?int $carry, Consumer $consumer) {
                    return $carry + $consumer->concreteSessions()->count();
                }, 0);
            })
            ->setCustomTransformer('concrete_sessions_quantity_sum', function ($value, Customer $customer) {
                $quantity = $customer->consumers->reduce(function (?int $carry, Consumer $consumer) {
                    return $carry + $consumer->concreteSessions->reduce(function ($carry2, $item) {
                            return $carry2 + $item->quantity;
                        }, 0);
                }, 0);

                return number_format($quantity / 100, 2, ',', '') . ' m³';
            })
            ->transform($customers->get());
    }
}
