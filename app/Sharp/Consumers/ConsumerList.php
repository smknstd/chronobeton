<?php

namespace App\Sharp\Consumers;

use App\Models\Consumer;
use App\Models\User;
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
                EntityListField::make('created_at')
                    ->setSortable()
                    ->setLabel('Création')
            )
            ->addField(
                EntityListField::make('name')
                    ->setSortable()
                    ->setLabel('Nom')
            )
            ->addField(
                EntityListField::make('rfid_code')
                    ->setLabel('N° Rfid')
            )
            ->addField(
                EntityListField::make('concrete_sessions_count')
                    ->setLabel('Nb. sessions')
            );
    }

    public function buildListLayout(EntityListFieldsLayout $fieldsLayout): void
    {
        $fieldsLayout
            ->addColumn('created_at', 3)
            ->addColumn('name', 3)
            ->addColumn('rfid_code', 3)
            ->addColumn('concrete_sessions_count', 3);
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
            ->setCustomTransformer('created_at', function ($value, Consumer $consumer) {
                return $consumer->created_at->format('d/m/y H:i');
            })
            ->setCustomTransformer('concrete_sessions_count', function ($value, Consumer $consumer) {
                return $consumer->concreteSessions()->count();
            })
            ->transform($consumers->get());
    }
}
