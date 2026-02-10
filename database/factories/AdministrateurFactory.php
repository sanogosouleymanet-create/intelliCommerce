<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Administrateur;

class AdministrateurFactory extends Factory
{
    protected $model = Administrateur::class;

    public function definition()
    {
        return [
            'nomAdmi' => $this->faker->name(),
            'prenomAdmi' => $this->faker->firstName(),
            'emailAdmi' => $this->faker->unique()->safeEmail(),
            'password' => bcrypt('password'),
            'telephoneAdmi' => $this->faker->phoneNumber(),
            'bloque' => false,
        ];
    }
}
