<?php

namespace App\Ship\Core\Loaders;

use Generator;

class RoutesLoader extends BaseLoader
{
    /**
     * @return Generator<string>
     */
    public function loadWebRoutes(): Generator
    {

        foreach ($this->getContainers() as $container) {
            $routesDir = "$container/UI/WEB/Routes/";
            if ($this->isDir($routesDir)) {
                $routesDir = scandir("$container/UI/WEB/Routes");
                if ($routesDir !== false) {
                    foreach ($routesDir as $route) {
                        if ($this->isNotLink($route)) {
                            yield "$container/UI/WEB/Routes/$route";
                        }
                    }
                }
            }
        }
    }

    /**
     * @return Generator<string>
     */
    public function loadApiRoutes(): Generator
    {
        foreach ($this->getContainers() as $container) {
            $routesDir = "$container/UI/API/Routes";
            if ($this->isDir($routesDir)) {
                $routesDir = scandir($routesDir);
                if ($routesDir !== false) {
                    foreach ($routesDir as $route) {
                        if ($this->isNotLink($route)) {
                            yield "$container/UI/API/Routes/$route";
                        }
                    }
                }
            }
        }
    }
}
