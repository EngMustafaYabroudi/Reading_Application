<?php

namespace Database\Factories;

use App\Models\Book;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ReadingGroup>
 */
class ReadingGroupFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {   
        $books = collect(Book::all()->modelKeys());
        return [
            'name' => $this->faker->sentence(2),
            'description' => $this->faker->text(255),
            'book_id' =>$books->random(), 
            'start_date' => $this->faker->dateTimeBetween('-1 month', '+1 month'),
            'end_date' => $this->faker->dateTimeBetween('+1 week', '+3 months'),
        ];
    }
}
