<?php

use Faker\Generator as Faker;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

$factory->define(Laravel\Nova\Tests\Fixtures\Post::class, function (Faker $faker) {
    return [
        'user_id' => factory(Laravel\Nova\Tests\Fixtures\User::class),
        'title' => $faker->word,
        'word_count' => random_int(100, 500),
        // 'created_at' => now()->subDays(random_int(1, 30)),
    ];
});
