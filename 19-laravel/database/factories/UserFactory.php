
<?php

namespace Database\factories;

use App\Models\User;
use Illuminate\Database\Eloquent\factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $gender = fake()->randomElement(['male', 'female']);

        return [
            'document' => fake()->unique()->numberBetween(100000000, 999999999),
            'fullname' => fake()->name($gender),
            'gender' => $gender,
            'birthdate' => fake()->date('Y-m-d'),
            'photo' => 'no-photo.png',
            'phone' => fake()->numerify('3#########'),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => '12345',
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the user's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
