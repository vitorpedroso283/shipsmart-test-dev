<?php

namespace App\Notifications;

use App\Models\Contact;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ContactCreatedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected Contact $contact;

    /**
     * Create a new notification instance.
     */
    public function __construct(Contact $contact)
    {
        $this->onQueue('back_emails');
        $this->contact = $contact;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Novo contato cadastrado')
            ->greeting('Olá!')
            ->line('Um novo contato foi cadastrado no sistema.')
            ->line("**Nome:** {$this->contact->nome}")
            ->line("**E-mail:** {$this->contact->email}")
            ->line("**Telefone:** {$this->contact->telefone}")
            ->line("**Cidade:** {$this->contact->cidade}")
            ->line("**Estado:** {$this->contact->estado}")
            ->salutation('Atenciosamente, Equipe ShipSmart');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
