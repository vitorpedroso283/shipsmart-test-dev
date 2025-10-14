<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;

uses(RefreshDatabase::class);

it('creates a contact successfully with valid cep', function () {
    Http::fake([
        'viacep.com.br/*' => Http::response([
            'cep' => '01001-000',
            'logradouro' => 'Praça da Sé',
            'bairro' => 'Sé',
            'localidade' => 'São Paulo',
            'uf' => 'SP',
        ], 200),
    ]);

    $payload = [
        'nome' => 'Vitor Teste',
        'email' => 'vitor@example.com',
        'cep' => '01001000',
    ];

    $response = $this->postJson('/api/contacts', $payload);

    $response->assertCreated()
        ->assertJsonFragment(['email' => 'vitor@example.com']);

    $this->assertDatabaseHas('contatos', ['email' => 'vitor@example.com']);
});

it('rejects invalid cep', function () {
    Http::fake([
        'viacep.com.br/*' => Http::response(['erro' => true], 200),
    ]);

    $payload = [
        'nome' => 'Vitor Teste',
        'email' => 'vitor@example.com',
        'cep' => '99999999',
    ];

    $response = $this->postJson('/api/contacts', $payload);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['cep']);
});
