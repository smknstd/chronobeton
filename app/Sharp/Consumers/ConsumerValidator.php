<?php

namespace App\Sharp\Consumers;

use Code16\Sharp\Form\Validator\SharpFormRequest;
use Illuminate\Validation\Rule;

class ConsumerValidator extends SharpFormRequest
{
    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'max:150',
            ],
            'rfid_code' => [
                'required',
                'max:150',
                Rule::unique('consumers', 'rfid_code')
                    ->ignore(currentSharpRequest()->instanceId()),
            ],
        ];
    }
}
