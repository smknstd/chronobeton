<?php

namespace App\Sharp\Customers;

use App\Enums\ColorTheme;
use App\Models\Customer;
use Code16\Sharp\Form\Eloquent\WithSharpFormEloquentUpdater;
use Code16\Sharp\Form\Fields\SharpFormSelectField;
use Code16\Sharp\Form\Fields\SharpFormTextField;
use Code16\Sharp\Form\Layout\FormLayout;
use Code16\Sharp\Form\Layout\FormLayoutColumn;
use Code16\Sharp\Form\SharpForm;
use Code16\Sharp\Utils\Fields\FieldsContainer;

class CustomerForm extends SharpForm
{
    use WithSharpFormEloquentUpdater;
    protected ?string $formValidatorClass = CustomerValidator::class;

    public function buildFormFields(FieldsContainer $formFields): void
    {
        $formFields
            ->addField(
                SharpFormTextField::make('name')
                    ->setLabel('Nom')
                    ->setMaxLength(150)
            )
            ->addField(
                SharpFormSelectField::make('color', ColorTheme::enum())
                    ->setLabel('Thème')
                    ->setDisplayAsDropdown()
            );
    }

    public function buildFormLayout(FormLayout $formLayout): void
    {
        $formLayout
            ->addColumn(7, function (FormLayoutColumn $column) {
                $column
                    ->withSingleField('name')
                    ->withSingleField('color');
            });
    }

    public function find($id): array
    {
        return $this
            ->transform(Customer::findOrFail($id));
    }

    public function update($id, array $data)
    {
        $customer = $id
            ? Customer::findOrFail($id)
            : new Customer();

        $this->save($customer, $data);

        return $customer->id;
    }

    public function delete($id): void
    {
        Customer::findOrFail($id)->delete();
    }
}
