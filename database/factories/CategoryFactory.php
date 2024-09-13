<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Category;
use Faker\Generator as Faker;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Category>
 */
class CategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // $title = fake()->sentence;
        // $slug = str_slug($title, '-');

        // return [
        //     'title' => $title,
        //     'slug' => $slug,
        //     'featured' => 1,

        // ];
    }
}
