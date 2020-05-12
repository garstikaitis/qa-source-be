<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\File;
use Illuminate\Support\Str;
use Faker\Generator as Faker;

$factory->define(File::class, function (Faker $faker) {
    return [
        'original_filename' => Str::random(),
        'filename' => $faker->name,
        'mime' => $faker->mimeType,
    ];
});
