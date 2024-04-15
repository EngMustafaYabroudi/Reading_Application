<?php

namespace Database\Factories;

use App\Models\Book;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Review>
 */
class ReviewFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $users = collect(User::all()->modelKeys());
        $books = collect(Book::all()->modelKeys());

        return [
            'rating' => $this->faker->numberBetween(1, 5),
            'review' => $this->faker->text(500),
            'user_id' => $users->random(), // Assign to a random user
            'book_id' => $books->random(), // Assign to a random book
        ];
    }
}
