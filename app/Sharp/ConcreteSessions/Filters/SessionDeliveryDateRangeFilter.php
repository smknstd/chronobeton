<?php

namespace App\Sharp\ConcreteSessions\Filters;

use Code16\Sharp\EntityList\Filters\EntityListDateRangeFilter;

class SessionDeliveryDateRangeFilter extends EntityListDateRangeFilter
{
    public function buildFilterConfig(): void
    {
        $this->configureLabel('Période')
            ->configureKey('delivered_at');
    }

    public function dateFormat()
    {
        return 'DD/MM/YYYY';
    }
}
