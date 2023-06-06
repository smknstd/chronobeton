<?php

namespace App\Sharp\Entities;

use App\Sharp\ConcreteSessions\ConcreteSessionList;
use Code16\Sharp\Utils\Entities\SharpEntity;

class ConcreteSessionEntity extends SharpEntity
{
    protected string $label = 'Utilisateurs';
    protected ?string $list = ConcreteSessionList::class;
    protected array $prohibitedActions = ['view','create', 'update', 'delete'];
}
