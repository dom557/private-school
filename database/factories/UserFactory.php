<?php
namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition()
    {
        return [
            'nom' => $this->faker->lastName,
            'prenom' => $this->faker->firstName,
            'CIN' => $this->faker->unique()->randomNumber(8),
            'adresse' => $this->faker->address,
            'telephone' => $this->faker->phoneNumber,
            'date_naissance' => $this->faker->date,
            'sexe' => $this->faker->randomElement(['male', 'female']),
            'email' => 'admin@example.com',
            'email_verified_at' => now(),
            'password' => Hash::make('password'), // Default password
            'role' => 'admin',
            'filier' => null,
            'remember_token' => Str::random(10),
        ];
    }

    public function admin()
    {
        return $this->state(function (array $attributes) {
            return [
                'email' => 'admin@example.com',
                'password' => Hash::make('password'),
                'role' => 'admin',
            ];
        });
    }
}
