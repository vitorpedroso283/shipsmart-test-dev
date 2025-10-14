<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Contact;

uses(RefreshDatabase::class);

it('deletes a contact successfully', function () {
    $contact = Contact::factory()->create([
        'nome' => 'Vitor Pedroso',
        'email' => 'vitor@example.com',
        'cep' => '01001000',
    ]);

    $response = $this->deleteJson("/api/contacts/{$contact->id}");

    $response->assertNoContent(); // 204

    $this->assertSoftDeleted('contatos', ['id' => $contact->id]);
});

it('returns 404 when trying to delete a non-existent contact', function () {
    $response = $this->deleteJson('/api/contacts/999999');
    $response->assertStatus(404);
});
