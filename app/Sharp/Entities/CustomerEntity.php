<?php

namespace App\Sharp\Entities;

use App\Sharp\Customers\CustomerForm;
use App\Sharp\Customers\CustomerList;
use Code16\Sharp\Utils\Entities\SharpEntity;

class CustomerEntity extends SharpEntity
{
    protected string $label = 'Clients';
    protected ?string $list = CustomerList::class;
    protected ?string $form = CustomerForm::class;
}
