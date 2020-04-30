<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Model\Task;
use App\Models\User;
use App\Models\Project;
use Faker\Generator as Faker;

$factory->define(Project::class, function (Faker $faker) {
    return [
        'status' => Project::STARTED,
		'taskId' => factory(Task::class)->create(),
		'userId' => factory(User::class)->create()
    ];
});
