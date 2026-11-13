<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrderPaid extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Order $order,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'تأیید پرداخت سفارش '.$this->order->number,
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.orders.paid',
            with: [
                'order' => $this->order->loadMissing('items'),
                'invoiceUrl' => route('orders.invoice', $this->order),
            ],
        );
    }
}
