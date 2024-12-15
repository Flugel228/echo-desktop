<?php

namespace App\Ship\Core\Loaders;

use Generator;

class ProvidersLoader extends BaseLoader
{
    /**
     * @return Generator<string>
     */
    public function loadProviders(): Generator
    {

        foreach ($this->getContainers() as $container) {
            $providersDir = "$container/Providers";
            if ($this->isDir($providersDir)) {
                $providersDir = scandir($providersDir);
                if ($providersDir !== false) {
                    foreach ($providersDir as $provider) {
                        if ($this->isPhpFile($provider) && $this->isNotLink($provider)) {
                            yield $this->getClassName("$container/Providers/$provider");
                        }
                    }
                }
            }
        }

        yield from $this->loadFromShip('Providers');
    }
}
