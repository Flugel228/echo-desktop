<?php

namespace App\Ship\Core\Abstracts\Criterias;

use App\Ship\Core\Abstracts\Models\AuthenticatableCore;
use App\Ship\Core\Abstracts\Models\ModelCore;
use App\Ship\Core\Abstracts\Repositories\RepositoryCore;
use Illuminate\Database\Query\Builder;

/**
 * @template TModel of ModelCore|AuthenticatableCore
 * @template TRepository of RepositoryCore
 */
abstract class CriteriaCore
{
    /**
     * Apply criteria in query repository
     *
     * @param TModel|Builder $model
     * @param TRepository $repository
     *
     * @return Builder|\Illuminate\Database\Eloquent\Builder|TModel
     */
    abstract public function apply(
        AuthenticatableCore|ModelCore|Builder|\Illuminate\Database\Eloquent\Builder $model,
        RepositoryCore $repository,
    ): Builder|\Illuminate\Database\Eloquent\Builder|ModelCore|AuthenticatableCore;
}
