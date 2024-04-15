<?php

namespace Database\Factories;

use App\Models\Author;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Book>
 */
class BookFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {    $authors = collect(Author::all()->modelKeys());
        return [
            'title' => $this->faker->sentence(4),
            'description' => $this->faker->text(1000),
            'genre' => $this->faker->randomElement(['Fantasy', 'Sci-Fi', 'Mystery', 'Romance', 'Thriller']),
            'published_year' => $this->faker->year,
            'image' => $this->faker->imageUrl(250, 350),
            'author_id' =>$authors->random(), 
        ];    
    }
}
