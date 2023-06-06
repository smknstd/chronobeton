<?php

namespace App\Sharp\Consumers;

use App\Models\Consumer;
use Code16\Sharp\Form\Eloquent\WithSharpFormEloquentUpdater;
use Code16\Sharp\Form\Fields\SharpFormTextField;
use Code16\Sharp\Form\Layout\FormLayout;
use Code16\Sharp\Form\Layout\FormLayoutColumn;
use Code16\Sharp\Form\SharpForm;
use Code16\Sharp\Utils\Fields\FieldsContainer;

class ConsumerForm extends SharpForm
{
    use WithSharpFormEloquentUpdater;
    protected ?string $formValidatorClass = ConsumerValidator::class;

    public function buildFormFields(FieldsContainer $formFields): void
    {
        $formFields
            ->addField(
                SharpFormTextField::make('name')
                    ->setLabel('Nom')
                    ->setMaxLength(150)
            )
            ->addField(
                SharpFormTextField::make('rfid_code')
                    ->setLabel('N° RFID')
                    ->setMaxLength(150)
            );
    }

    public function buildFormLayout(FormLayout $formLayout): void
    {
        $formLayout
            ->addColumn(7, function (FormLayoutColumn $column) {
                $column
                    ->withSingleField('name')
                    ->withSingleField('rfid_code');
            });
    }

    public function find($id): array
    {
        return $this
            ->transform(Consumer::findOrFail($id));
    }

    public function update($id, array $data)
    {
        $consumer = $id
            ? Consumer::findOrFail($id)
            : new Consumer();

        $this->save($consumer, $data);

        return $consumer->id;
    }

    public function delete($id): void
    {
        Consumer::findOrFail($id)->delete();
    }
}
