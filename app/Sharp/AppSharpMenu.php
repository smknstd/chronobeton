<?php

namespace App\Sharp;

use Code16\Sharp\Utils\Menu\SharpMenu;

class AppSharpMenu extends SharpMenu
{
    public function build(): SharpMenu
    {
        return $this
            ->addEntityLink('concrete_sessions', 'Sessions', 'fa-truck')
            ->addEntityLink('consumers', 'Utilisateurs', 'fa-user')
            ->addEntityLink('customers', 'Clients', 'fa-building');
    }
}
