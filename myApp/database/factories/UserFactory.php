<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\UserAddress;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'email' => Str::uuid() . '@example.com',
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Configure the factory to create an address after a user is created.
     */
    public function configure(): self
    {
        return $this->afterCreating(function (User $user) {
            
            $suffix = strtoupper(base_convert($user->id, 10, 36));
            $email = sprintf(
                '%s.%s%s@example.com',
                Str::of($user->first_name)->lower(),
                Str::of($user->last_name)->lower(),
                $suffix
            );

            // update the record in place
            $user->update(['email' => $email]);
            
            // Create one address record for this user
            $user->address()->create(
                UserAddress::factory()->make()->toArray()
            );
        });
    }


    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
