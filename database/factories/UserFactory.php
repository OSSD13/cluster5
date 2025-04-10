<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

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
        // Determine the role for this user.
        $role = fake()->randomElement(['ceo', 'supervisor', 'sale']);

        // For sales, assign a manager from existing users (if any); for others, keep manager as null.
        $managerId = null;
        if ($role === 'sale') {
            $existingUserIds = DB::table('users')->where('role_name', '=', 'supervisor')->pluck('user_id')->toArray();
            if (!empty($existingUserIds)) {
                $managerId = fake()->randomElement($existingUserIds);
            }
        }

        return [
            'email'          => fake()->unique()->safeEmail(),
            'password'       => static::$password ??= Hash::make('password'),
            'user_status'    => 'normal',
            'role_name'      => $role,
            'name'           => fake()->name(),
            // 'remember_token' => Str::random(10),
            'manager'        => $managerId,
            'created_at'     => fake()->dateTime(),
            'updated_at'     => fake()->dateTime(),
        ];
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
