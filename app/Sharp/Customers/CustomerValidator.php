<?php

namespace App\Sharp\Customers;

use Code16\Sharp\Form\Validator\SharpFormRequest;
use Illuminate\Validation\Rule;

class CustomerValidator extends SharpFormRequest
{
    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'max:150',
            ],
        ];
    }
}
