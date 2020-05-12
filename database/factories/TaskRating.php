<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Task;
use App\Models\File;
use App\Models\User;
use App\Models\TaskRating;
use Illuminate\Support\Str;
use Faker\Generator as Faker;

$factory->define(TaskRating::class, function (Faker $faker) {
    return [
        'rating' => $faker->range(1, 10),
        'comment' => $faker->paragraph,
        'created_by' => factory(User::class)->create()->id,
        'given_to' => factory(User::class)->create()->id,
        'taskId' => factory(Task::class)->create()->id,
    ];
});
