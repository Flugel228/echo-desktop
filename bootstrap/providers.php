<?php

use App\Ship\Core\Loaders\ProvidersLoader;

return [
    App\Providers\AppServiceProvider::class,
    ...(new ProvidersLoader())->loadProviders()
];
