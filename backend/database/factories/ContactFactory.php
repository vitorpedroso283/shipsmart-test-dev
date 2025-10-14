<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Contact>
 */
class ContactFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nome'     => $this->faker->name(),
            'email'    => $this->faker->unique()->safeEmail(),
            'telefone' => $this->faker->optional()->phoneNumber(),
            'cep'      => $this->faker->numerify('########'),
            'estado'   => $this->faker->stateAbbr(),
            'cidade'   => $this->faker->city(),
            'bairro'   => $this->faker->streetName(),
            'endereco' => $this->faker->streetAddress(),
            'numero'   => (string) $this->faker->numberBetween(1, 9999),
        ];
    }
}
