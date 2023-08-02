<?php


namespace MattSplat\DynamicLoading;


use Illuminate\Support\Collection;
use Illuminate\Support\ServiceProvider;

class DynamicLoadingServiceProvider extends ServiceProvider
{
    public function register()
    {
        Collection::macro('dynamicLoad', function ($relation_name, $subQuery, $model_id = null, $relation_id = null, $single = false) {

            $newCollection = collect();

            if ($this->isNotEmpty()) {
                $loader =  new DynamicLoad();
                $newCollection = $loader->load($this, $relation_name, $subQuery, $model_id, $relation_id, $single);
            }

            return $newCollection;

        });
    }

    public function boot()
    {

    }
}