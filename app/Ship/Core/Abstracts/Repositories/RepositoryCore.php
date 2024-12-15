<?php

declare(strict_types=1);

namespace App\Ship\Core\Abstracts\Repositories;

use App\Ship\Core\Abstracts\Criterias\CriteriaCore;
use App\Ship\Core\Abstracts\Models\AuthenticatableCore;
use App\Ship\Core\Abstracts\Models\ModelCore;
use App\Ship\Core\Abstracts\Models\ModelCore as Model;
use App\Ship\Exceptions\MissingSoftDeletesTraitException;
use Illuminate\Contracts\Database\Query\Expression;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection as IlluminateCollection;
use Illuminate\Pagination\LengthAwarePaginator;
use stdClass;

/**
 * @template TModel of Model|AuthenticatableCore
 * @template TFillable of array
 * @template TUpdateFillable of array
 * @template TRepository of RepositoryCore
 */
abstract class RepositoryCore
{
    /**
     * @var TModel Installed model.
     */
    private Model|AuthenticatableCore $model;

    /**
     * @var IlluminateCollection<int, CriteriaCore<TModel, TRepository>> Collection of criterias.
     */
    private IlluminateCollection $criterias;

    /**
     * @var int Limit of elements.
     */
    private int $paginationLimit = 10;

    public function __construct()
    {
        $this->criterias = new IlluminateCollection();
        $this->model = app($this->getModelClass());
    }

    /**
     * @return class-string<TModel>
     */
    abstract protected function getModelClass(): string;

    /**
     * Method of querying a table through an installed model
     *
     * @return TModel Returns a clone of the given model.
     */
    protected function startConditions(): Model|AuthenticatableCore
    {
        return clone $this->model;
    }

    /**
     * Returns a paginated list of resources.
     *
     * @param int|null $limit Limit of elements.
     * @param string[] $select Selected columns.
     * @param int $page Number of pagination page.
     * @return Collection<int, TModel> Returns a collection of models
     * in the format key => value, where key is an int and value is
     * the installed model object.
     */
    public function paginate(?int $limit = null, array $select = ['*'], int $page = 1): Collection
    {
        if ($limit) {
            $this->setPaginationLimit($limit);
        }

        /** @var TModel|Builder $model */
        $model = $this->applyCriterias();
        /** @var LengthAwarePaginator $paginator */
        $paginator = $model
            ->paginate(
                $this->paginationLimit,
                $select,
                'page',
                $page
            );
        /** @var Collection<int, TModel> $collection */
        $collection = $paginator->getCollection();
        $collection->macro('links', function () use ($paginator) {
            return $paginator->links();
        });
        $collection->macro('linksToJson', function () use ($paginator) {
            $linksToJson = new stdClass();
            $linksToJson->current_page = $paginator->currentPage();
            $linksToJson->per_page = $paginator->perPage();
            $linksToJson->total = $paginator->total();
            $linksToJson->last_page = $paginator->lastPage();
            $linksToJson->links = new stdClass();
            $linksToJson->links->prev = $paginator->previousPageUrl();
            $linksToJson->links->next = $paginator->nextPageUrl();
            return $linksToJson;
        });

        return $collection;
    }

    /**
     * Returns the first element matching the request.
     *
     * @param string $field Field to search for.
     * @param mixed $value Value to search for.
     * @param string[] $select Selected columns.
     * @return TModel|null Returns a model object.
     */
    public function findByField(string $field, mixed $value, array $select = ['*'])
    {
        $model = $this->applyCriterias();

        /** @var TModel|null $result */
        $result = $model
            ->select($select)
            ->where($field, '=', $value)
            ->first();
        return $result;
    }

    /**
     * Returns the first element matching the request,
     * but with trashed data.
     *
     * @param string $field Field to search for.
     * @param mixed $value Value to search for.
     * @param string[] $select Selected columns.
     * @return TModel|null Returns a model object.
     * @throws MissingSoftDeletesTraitException
     */
    public function findByFieldWithTrashed(string $field, mixed $value, array $select = ['*'])
    {
        // checks that model has SoftDelete trait
        $this->modelHasSoftDeleteTrait();

        $model = $this->applyCriterias();

        /** @phpstan-ignore-next-line  */
        return $model
            ->select($select)
            ->withTrashed()
            ->where($field, '=', $value)
            ->first();
    }

    /**
     * @param TFillable $data
     * @return TModel
     */
    public function create(array $data)
    {
        /** @var TModel $result */
        $result = $this->startConditions()
            ->create($data);
        return $result;
    }

    /**
     * @param TUpdateFillable $data
     * @param string $_id
     * @return bool|null
     */
    public function update(array $data, string $_id): ?bool
    {
        return $this->startConditions()
            ->find($_id)
            ?->update($data);
    }

    /**
     * @param string $_id
     * @return bool|null
     */
    public function destroy(string $_id): ?bool
    {
        return $this->startConditions()
            ->find($_id)
            ?->delete();
    }

    /**
     * @param string $_id
     * @return bool|null
     * @throws MissingSoftDeletesTraitException
     */
    public function forceDestroy(string $_id): ?bool
    {
        // checks that model has SoftDelete trait
        $this->modelHasSoftDeleteTrait();

        /** @phpstan-ignore-next-line  */
        return $this->startConditions()
            ->withTrashed()
            ->find($_id)
            ?->forceDelete();
    }

    private function setPaginationLimit(int $limit): void
    {
        $this->paginationLimit = $limit;
    }

    /**
     * @throws MissingSoftDeletesTraitException
     */
    private function modelHasSoftDeleteTrait(): void
    {
        $modelHasSoftDeleteTrait = in_array(
            'Illuminate\Database\Eloquent\SoftDeletes',
            class_uses($this->model)
        );
        if (!$modelHasSoftDeleteTrait) {
            throw new MissingSoftDeletesTraitException();
        }
    }


    /**
     * Push Criteria for filter the query
     *
     * @param CriteriaCore $criteria
     * @return static
     */
    public function pushCriteria(CriteriaCore $criteria): static
    {
        $this->criterias->push($criteria);

        return $this;
    }

    /**
     * @return Builder|\Illuminate\Database\Eloquent\Builder|TModel
     */
    private function applyCriterias(): Builder|\Illuminate\Database\Eloquent\Builder|ModelCore|AuthenticatableCore
    {
        // gets a clean query to the model
        $model = $this->startConditions();

        // gets an array of criteria
        /** @var array<CriteriaCore<TModel, TRepository>> $criterias */
        $criterias = $this->criterias->toArray();

        foreach ($criterias as $criteria) {
            /** @var TModel|Builder|\Illuminate\Database\Eloquent\Builder $model */
            $model = $criteria->apply($model, $this);
        }

        return $model;
    }

    /**
     * Returns a list of all resources.
     *
     * @param string[] $select
     *
     * @return Collection<int, TModel> Returns a collection of models
     * in the format key => value, where key is an int and value is
     * the installed model object.
     */
    public function all(array $select = ['*']): Collection
    {
        /** @var TModel|Builder $model */
        $model = $this->applyCriterias();

        /** @var Collection<int, TModel>  $model */
        $model = $model->get($select);

        return $model;
    }

    /**
     * Retrieve the minimum value of a given column.
     *
     * @param Expression|string $column
     * @return mixed
     */
    public function min(Expression|string $column): mixed
    {
        /** @var TModel|Builder $model */
        $model = $this->applyCriterias();

        return $model->min($column);
    }

    /**
     * Retrieve the maximum value of a given column.
     *
     * @param Expression|string $column
     * @return mixed
     */
    public function max(Expression|string $column): mixed
    {
        /** @var TModel|Builder $model */
        $model = $this->applyCriterias();

        return $model->max($column);
    }
}
