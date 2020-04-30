<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Company;
use Illuminate\Support\Str;
use Faker\Generator as Faker;

$factory->define(Company::class, function (Faker $faker) {

	$name = $faker->company;
	
	$slug = Str::slug($name, '-');
	
    return [
        'name' => $name,
		'slug' => $slug,
		'credits_remaining' => 100
    ];
});
