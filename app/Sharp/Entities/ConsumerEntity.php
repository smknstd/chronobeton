<?php

namespace App\Sharp\Entities;

use App\Sharp\Consumers\ConsumerForm;
use App\Sharp\Consumers\ConsumerList;
use App\Sharp\Consumers\ConsumerShow;
use Code16\Sharp\Utils\Entities\SharpEntity;

class ConsumerEntity extends SharpEntity
{
    protected string $label = 'Utilisateurs';
    protected ?string $list = ConsumerList::class;
    protected ?string $show = ConsumerShow::class;
    protected ?string $form = ConsumerForm::class;
}
