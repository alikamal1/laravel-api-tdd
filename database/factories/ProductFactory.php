<?php

use Faker\Generator as Faker;

$factory->define(App\Product::class, function (Faker $faker) {
    $name = $faker->name;
    return [
        'name' => $name,
        'slug' => str_slug($name),
        'price' => random_int(10, 100),
    ];
});
