<?php

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| Here you may define all of your model factories. Model factories give
| you a convenient way to create models for testing and seeding your
| database. Just tell the factory how a default model should look.
|
*/

$factory->define(App\User::class, function (Faker\Generator $faker) {
    return [
        'name'           => $faker->name,
        'email'          => $faker->email,
        'password'       => bcrypt(str_random(10)),
        'remember_token' => str_random(10),
    ];
});

$factory->define(App\Ping::class, function (Faker\Generator $faker) {
    $env = $faker->randomElement(['production', 'demo', 'staging', 'local']);

    return [
        'name'            => $faker->slug(2) . '-' . $env,
        'description'     => $faker->sentence(5),
        'tags'            => $faker->word . ', ' . $env,
        'frequency'       => $faker->randomElement(['day', 'hour', 'minute']),
        'frequency_value' => $faker->numberBetween(1, 10),
        'active'          => $faker->boolean(80),
        'error'           => $faker->boolean(20),
        'last_ping'       => $faker->dateTimeBetween('-1 week', 'now'),
        'created_by'      => 0,
        'updated_by'      => 0,
        'deleted_at'      => null,
    ];
});

$factory->define(App\Contact::class, function (Faker\Generator $faker) {
    return [
        'name'          => $faker->name,
        'email'         => $faker->email,
        'active'        => true,
        'filter_tags'   => '',
        'slack_channel' => '',
        'sns_topic'     => '',
        'deleted_at'    => null,
    ];
});