<?php

namespace App\Ship\Parents\Criterias;

use App\Ship\Core\Abstracts\Criterias\CriteriaCore;
use App\Ship\Core\Abstracts\Models\AuthenticatableCore;
use App\Ship\Core\Abstracts\Models\ModelCore;
use App\Ship\Core\Abstracts\Repositories\RepositoryCore;

/**
 * @template TModel of ModelCore|AuthenticatableCore
 * @template TRepository of RepositoryCore
 * @extends CriteriaCore<TModel, TRepository>
 */
abstract class CriteriaParent extends CriteriaCore
{
}
