<?php

namespace App\Ship\Core\Abstracts\Factories;

use App\Ship\Core\Abstracts\Models\AuthenticatableCore;
use App\Ship\Core\Abstracts\Models\ModelCore;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @template TModel of ModelCore|AuthenticatableCore
 * @extends Factory<TModel>
 */
abstract class FactoryCore extends Factory
{
}
