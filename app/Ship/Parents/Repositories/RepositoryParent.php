<?php

namespace App\Ship\Parents\Repositories;

use App\Ship\Core\Abstracts\Repositories\RepositoryCore;
use App\Ship\Parents\Models\AuthenticatableParent;
use App\Ship\Parents\Models\ModelParent;

/**
 * @template TModel of ModelParent|AuthenticatableParent
 * @template TFillable of array
 * @template TUpdateFillable of array
 * @template TRepository of RepositoryCore
 * @extends RepositoryCore<TModel,TFillable,TUpdateFillable, TRepository>
 */
abstract class RepositoryParent extends RepositoryCore
{
}
