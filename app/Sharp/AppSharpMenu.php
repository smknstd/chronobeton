<?php

namespace App\Sharp;

use Code16\Sharp\Utils\Menu\SharpMenu;

class AppSharpMenu extends SharpMenu
{
    public function build(): SharpMenu
    {
        return $this
            ->addEntityLink('consumers', 'Utilisateurs', 'fa-user');
    }
}
