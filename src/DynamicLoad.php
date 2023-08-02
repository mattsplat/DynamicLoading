<?php


namespace MattSplat\DynamicLoading;


use Closure;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class DynamicLoad
{
    public function load(Collection $models, string $relation_name, Closure $subQuery, $relation_key = null, $model_key = null, $single = false): Collection
    {

        $queries = new Collection();
        if (!$model_key) {
            $model_key = $models->first()->getKeyName();
        }
        if (!$relation_key) {
            $class = class_basename($models->first());
            $class = strtolower(preg_replace('/(.)(?=[A-Z])/u', '$1' . '_', $class));
            $relation_key = $class . '_' . $model_key;
        }

        foreach ($models as $model) {

            $query = call_user_func($subQuery, $model);

            if (!$this->checkIfRelationKeyExistsInQuery($query, $relation_key)) {
                $query->addSelect(\DB::raw($model->{$model_key} . ' as ' . $relation_key));
            }

            $queries->push(
                $query
            );
        }


        /// combine union queries together
        $q = $queries->first();
        for ($x = 1; $x < count($queries); $x++) {
            $q = $q->union($queries[$x]);
        }

        try {
            $relations = $q->get()->groupBy($relation_key);
        } catch (\Exception $e) {
            return $models;
        }


        foreach ($models as $model) {
            if (isset($relations[$model->{$model_key}])) {

                if($single) {
                    $relations[$model->{$model_key}] = $relations[$model->{$model_key}]->first();
                }
                $model->setRelation($relation_name, $relations[$model->{$model_key}]);
            }
        }

        return $models;
    }

    /**
     * @param $query
     * @param string|null $relation_key
     */
    public function checkIfRelationKeyExistsInQuery($query, ?string $relation_key): bool
    {
        if ($query instanceof Builder) {
            $columns = $query->getQuery()->columns;
        } else {
            $columns = $query->columns;
        }

        if (empty($columns)) {
            $query->select('*');
            return false;
        }

        $has_relation_key = false;
        foreach ($columns as $column) {
            foreach (explode(' ', $column) as $part) {
                if ($part === $relation_key) $has_relation_key = true;
            }

        }

        return $has_relation_key;
    }
}
