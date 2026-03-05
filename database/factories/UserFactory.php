<?php
namespace Database\Factories;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'password' => Hash::make('password'),
        ];
    }

    public function dispatcher(): static
    {
        return $this->afterCreating(function (User $user) {
            $user->assignRole(UserRole::Dispatcher->value);
        });
    }

    public function technician(): static
    {
        return $this->afterCreating(function (User $user) {
            $user->assignRole(UserRole::Technician->value);
        });
    }
}
