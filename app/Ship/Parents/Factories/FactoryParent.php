<?php

namespace App\Ship\Parents\Factories;

use App\Ship\Core\Abstracts\Factories\FactoryCore;
use App\Ship\Parents\Models\AuthenticatableParent;
use App\Ship\Parents\Models\ModelParent;

/**
 * @template TModel of ModelParent|AuthenticatableParent
 * @extends FactoryCore<TModel>
 */
abstract class FactoryParent extends FactoryCore
{
}
