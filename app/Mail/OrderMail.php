<?php

namespace App\Mail;

use App\Models\Order;
use App\Models\CompanyDetails;
use App\Models\BundleProduct;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Barryvdh\DomPDF\Facade\Pdf;

class OrderMail extends Mailable
{
    use Queueable, SerializesModels;

    public $orderData;
    public $recipientType;
    public $order;

    public function __construct(Order $order, $recipientType = 'customer')
    {
        $this->order = $order;
        $this->recipientType = $recipientType;
        $this->orderData = [
            'invoice' => $order->invoice,
            'purchase_date' => $order->purchase_date,
            'customer_name' => $order->name . ' ' . $order->surname,
            'customer_email' => $order->email,
            'customer_phone' => $order->phone,
            'address' => $order->address_first_line . ', ' . $order->address_second_line . ', ' . $order->town . ' ' . $order->postcode,
            'payment_method' => ucfirst(str_replace('_', ' ', $order->payment_method)),
            'subtotal' => '£' . number_format($order->subtotal_amount, 2),
            'shipping' => '£' . number_format($order->shipping_amount, 2),
            'vat' => '£' . number_format($order->vat_amount, 2),
            'total' => '£' . number_format($order->net_amount, 2),
            'order_notes' => $order->note,
            'order_id' => $order->id,
        ];
    }

    public function build()
    {
        $subject = $this->recipientType === 'admin' 
            ? 'New Order Received: ' . $this->orderData['invoice']
            : 'Order Confirmation: ' . $this->orderData['invoice'];

        // Generate PDF dynamically
        $data = [
            'order' => $this->order,
            'currency' => CompanyDetails::value('currency') ?? '£',
            'bundleProduct' => $this->order->bundle_product_id ? BundleProduct::find($this->order->bundle_product_id) : null,
        ];

        $pdf = PDF::loadView('frontend.order_pdf', $data);

        return $this->subject($subject)
                    ->view('emails.order')
                    ->attachData($pdf->output(), 'Order-' . $this->orderData['invoice'] . '.pdf', [
                        'mime' => 'application/pdf',
                    ]);
    }
}