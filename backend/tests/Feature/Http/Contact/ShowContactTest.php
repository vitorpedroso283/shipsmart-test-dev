<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Contact;

uses(RefreshDatabase::class);

it('retrieves a contact by id', function () {
    $contact = Contact::factory()->create([
        'nome' => 'Vitor Pedroso',
        'email' => 'vitor@example.com',
        'cep' => '01001000',
    ]);

    $response = $this->getJson("/api/contacts/{$contact->id}");

    $response->assertOk()
        ->assertJsonFragment(['email' => 'vitor@example.com']);
});

it('returns 404 when contact not found', function () {
    $response = $this->getJson('/api/contacts/999999');
    $response->assertStatus(404);
});
