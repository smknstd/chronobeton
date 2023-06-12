<?php

namespace App\Sharp\Consumers;

use App\Models\Consumer;
use Code16\Sharp\Show\Fields\SharpShowEntityListField;
use Code16\Sharp\Show\Fields\SharpShowTextField;
use Code16\Sharp\Show\Layout\ShowLayout;
use Code16\Sharp\Show\Layout\ShowLayoutColumn;
use Code16\Sharp\Show\Layout\ShowLayoutSection;
use Code16\Sharp\Show\SharpShow;
use Code16\Sharp\Utils\Fields\FieldsContainer;

class ConsumerShow extends SharpShow
{
    public function buildShowFields(FieldsContainer $showFields): void
    {
        $showFields
            ->addField(SharpShowTextField::make('name')->setLabel('Nom'))
            ->addField(SharpShowTextField::make('customer')->setLabel('Client'))
            ->addField(SharpShowTextField::make('rfid_code')->setLabel('N° RFID'))
            ->addField(
                SharpShowEntityListField::make('consumer_concrete_sessions', 'consumer_concrete_sessions')
                    ->setLabel('Sessions')
                    ->showCount()
                    ->setShowIfEmpty()
                    ->hideFilterWithValue('consumer_id', function ($instanceId) {
                        return $instanceId;
                    })
            );;
    }

    public function buildShowConfig(): void
    {
        $this
            ->configureBreadcrumbCustomLabelAttribute('name');
    }

    public function buildShowLayout(ShowLayout $showLayout): void
    {
        $showLayout
            ->addSection('', function (ShowLayoutSection $section) {
                $section
                    ->addColumn(6, function (ShowLayoutColumn $column) {
                        $column
                            ->withSingleField('name')
                            ->withSingleField('customer')
                            ->withSingleField('rfid_code');
                    });
            })
            ->addEntityListSection('consumer_concrete_sessions');
    }

    public function find(mixed $id): array
    {
        return $this
            ->setCustomTransformer('customer', function ($value, Consumer $consumer) {
                return sprintf('<span style="font-style: italic; background-color: %s" class="text-white rounded px-2 py-1">%s</span>',
                    $consumer->customer->color->value,
                    $consumer->customer->name,
                );
            })
            ->transform(Consumer::findOrFail($id));
    }
}
