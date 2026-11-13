<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReminderSetting extends Model
{
    protected $fillable = [
        'user_id',
        'sms_on_transaction',
        'payment_reminder_due',
        'payment_reminder',
        'daily_outstanding',
        'daily_sales',
        'low_stock',
        'service_reminder',
        'whatsapp_alert',
    ];

    protected $casts = [
        'sms_on_transaction' => 'boolean',
        'payment_reminder_due' => 'boolean',
        'payment_reminder' => 'boolean',
        'daily_outstanding' => 'boolean',
        'daily_sales' => 'boolean',
        'low_stock' => 'boolean',
        'service_reminder' => 'boolean',
        'whatsapp_alert' => 'boolean',
    ];
}
