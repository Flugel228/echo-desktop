<?php

use App\Ship\Core\Loaders\ProvidersLoader;

return [
    \App\Ship\Providers\ShipServiceProvider::class,
    ...(new ProvidersLoader())->loadProviders()
];
