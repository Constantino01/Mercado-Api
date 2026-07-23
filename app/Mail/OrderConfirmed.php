<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrderConfirmed extends Mailable
{
    use Queueable, SerializesModels;

    // Esta variável guarda os dados que enviamos do Controller
    public $encomenda;

    /**
     * Ao instanciar o email, passamos o objeto da Order
     */
    public function __construct($encomenda)
    {
        $this->encomenda = $encomenda;
    }

    /**
     * Definir o Assunto do Email
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Confirmação de Encomenda - ' . $this->encomenda->order_code,
        );
    }

    /**
     * Apontar para o template Markdown
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.order_confirmed',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}