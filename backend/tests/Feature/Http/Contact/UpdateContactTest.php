<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Contact;

uses(RefreshDatabase::class);

it('updates a contact successfully', function () {
    $contact = Contact::factory()->create([
        'nome' => 'Vitor Pedroso',
        'email' => 'vitor@example.com',
        'cep' => '01001000',
    ]);

    $payload = [
        'nome' => 'Vitor Atualizado',
        'email' => 'vitor.new@example.com',
        'cep' => '01310930',
    ];

    $response = $this->putJson("/api/contacts/{$contact->id}", $payload);

    $response->assertOk()
        ->assertJsonFragment(['email' => 'vitor.new@example.com']);

    $this->assertDatabaseHas('contatos', ['email' => 'vitor.new@example.com']);
});

it('returns 404 when updating a non-existent contact', function () {
    $response = $this->putJson('/api/contacts/999999', [
        'nome' => 'Ghost',
        'email' => 'ghost@example.com',
        'cep' => '01001000',
    ]);

    $response->assertStatus(404);
});
