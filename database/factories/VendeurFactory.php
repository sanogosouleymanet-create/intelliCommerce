<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Vendeur;

class VendeurFactory extends Factory
{
    protected $model = Vendeur::class;

    public function definition()
    {
        return [
            'nomVendeur' => $this->faker->name(),
            'prenomVendeur' => $this->faker->firstName(),
            'emailVendeur' => $this->faker->unique()->safeEmail(),
            'password' => bcrypt('password'),
            'telephoneVendeur' => $this->faker->phoneNumber(),
            'bloque' => false,
        ];
    }
}
