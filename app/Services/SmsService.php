<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class SmsService
{
    public static function sendPaymentLink($mobile, array $vars)
    {
        Http::get(
            "https://2factor.in/API/V1/" . config('services.twofactor.key') . "/ADDON_SERVICES/SEND/TSMS",
            [
                'to' => $mobile,
                'template_id' => config('services.twofactor.template_id'),

                // DLT VARIABLES (ORDER MATTERS)
                'var1' => $vars[0],
                'var2' => $vars[1],
                'var3' => $vars[2],
            ]
        );
    }
}
