<?php

use Faker\Generator;
use NiNaCoder\Translation\Language;

$factory->define(Language::class, function (Generator $faker) {
    return [
        'language' => $faker->word,
        'name' => $faker->word,
    ];
});
