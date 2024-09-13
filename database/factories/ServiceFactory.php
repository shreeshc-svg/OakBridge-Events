<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Service;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Service>
 */
class ServiceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    // protected $model = Service::class;



    public function definition(): array
    {
        $title = fake()->sentence;
        $slug = str_slug($title, '-');
        return [
            'title' => $title,
            'slug' => $slug ,
            'body' => fake()->paragraph(4),
            'user_id' => User::factory(),
            'image' => '',
            'video' => '',
            'excerpt' => fake()->paragraph(1),
            'meta_title' =>'',
            'meta_description' => '',
            'meta_keyword' => '',
            'featured'  => '1',
            'published' => '1',
            'disable_comment'=> '0',
        ];
    }
}
