<?php

namespace App\Ship\Parents\Providers;

use App\Ship\Core\Abstracts\Providers\ProviderCore;
use Illuminate\Pagination\Paginator;

abstract class ProviderParent extends ProviderCore
{
    public function boot(): void
    {
        Paginator::useBootstrapFive();
        Paginator::useBootstrapFour();
    }
}
