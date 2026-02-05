<?php

namespace App\Services;

use Razorpay\Api\Api;

class PaymentLinkService
{
    public static function create($invoice, $party)
    {
        $api = new Api(
            config('services.razorpay.key'),
            config('services.razorpay.secret')
        );

        $link = $api->paymentLink->create([
            'amount'       => (int) ($invoice->balance_amount * 100), // paise
            'currency'     => 'INR',
            'description'  => 'Invoice ' . $invoice->invoice_number,

            // 🔥 UNIQUE reference_id (VERY IMPORTANT)
            'reference_id' => 'INV-' . $invoice->id,

            'customer' => [
                'name'    => $party->party_name,
                'contact' => $party->contact_number,
            ],

            'notify' => [
                'sms'   => false,
                'email' => false,
            ],
        ]);

        return $link['short_url'];
    }
}
