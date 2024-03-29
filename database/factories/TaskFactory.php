<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Task;
use App\Models\Company;
use App\Models\File;
use App\Models\Project;
use Faker\Generator as Faker;

$factory->define(Task::class, function (Faker $faker) {
    return [
        'name' => $faker->title,
        'description' => $faker->text,
        'type' => 'Alpha',
        'companyId' => factory(Company::class)->create()->id,
        'file_id' => factory(File::class)->create()->id,
    ];
});
