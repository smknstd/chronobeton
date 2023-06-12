<?php

namespace App\Sharp\Entities;

use App\Sharp\Consumers\Lists\ConsumerConcreteSessionList;
use Code16\Sharp\Utils\Entities\SharpEntity;

class ConsumerConcreteSessionEntity extends SharpEntity
{
    protected string $label = 'Utilisateurs';
    protected ?string $list = ConsumerConcreteSessionList::class;
    protected array $prohibitedActions = ['view','create', 'update', 'delete'];
}
