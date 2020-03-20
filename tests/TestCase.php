<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    public function create(string $model, array $attributes = [], $resource = true)
    {
        $resouceModel = factory("App\\$model")->create($attributes);
        $resourcesClass = "App\\Http\Resources\\$model";

        if (!$resource) {
            return $resouceModel;
        }

        return new $resourcesClass($resouceModel);
    }
}
