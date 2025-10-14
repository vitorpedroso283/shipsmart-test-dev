<?php

use App\Models\Contact;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('lists contacts paginated', function () {
    // Arrange
    Contact::factory()->count(15)->create();

    // Act
    $response = $this->getJson('/api/contacts?per_page=10');

    // Assert
    $response->assertOk()
        ->assertJsonStructure([
            'current_page',
            'data' => [
                '*' => ['id', 'nome', 'email', 'telefone', 'cep', 'estado', 'cidade', 'bairro', 'endereco', 'numero']
            ],
            'first_page_url',
            'from',
            'last_page',
            'last_page_url',
            'links',
            'next_page_url',
            'path',
            'per_page',
            'prev_page_url',
            'to',
            'total',
        ])
        ->assertJsonFragment(['per_page' => 10]);
});
