<?php

use App\Models\Contact;
use App\Notifications\ContactCreatedNotification;
use App\Services\Contact\ContactService;
use App\Repositories\Contact\ContactRepository;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Queue;
use Illuminate\Notifications\SendQueuedNotifications;

beforeEach(function () {
    $this->repo = mock(ContactRepository::class);
    $this->service = new ContactService($this->repo);

    $this->data = [
        'nome' => 'Vitor Pedroso',
        'email' => 'vitor@example.com',
        'telefone' => '11999999999',
        'cep' => '01001000',
        'estado' => 'SP',
        'cidade' => 'São Paulo',
        'bairro' => 'Sé',
        'endereco' => 'Praça da Sé',
        'numero' => '100',
    ];

    $this->contact = new Contact($this->data);
    $this->contact->id = 1;
});

it('should send the ContactCreatedNotification via mail channel', function () {
    Notification::fake();

    $this->repo->shouldReceive('create')
        ->once()
        ->with($this->data)
        ->andReturn($this->contact);

    $this->service->create($this->data);

    Notification::assertSentOnDemand(ContactCreatedNotification::class, function ($notification, $channels, $notifiable) {
        expect($channels)->toContain('mail');
        expect($notifiable->routes['mail'])->toBe(env('NOTIFICATION_MAIL'));
        return true;
    });
});

it('should queue the notification job on the back_emails queue', function () {
    Queue::fake();

    $this->repo->shouldReceive('create')
        ->once()
        ->with($this->data)
        ->andReturn($this->contact);

    $this->service->create($this->data);

    Queue::assertPushed(SendQueuedNotifications::class, function ($job) {
        return $job->queue === 'back_emails';
    });
});
