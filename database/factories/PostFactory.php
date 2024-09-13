<?php

namespace Database\Factories;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;


/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Post>
 */
class PostFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
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
