<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ReminderSetting;
use Illuminate\Http\Request;

class ReminderSettingController extends Controller
{
    public function index(Request $request)
    {
        $settings = ReminderSetting::firstOrCreate(
            ['user_id' => $request->user()->id]
        );

        return response()->json([
            'success' => true,
            'data' => $settings,
        ]);
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'sms_on_transaction' => 'sometimes|boolean',
            'payment_reminder_due' => 'sometimes|boolean',
            'payment_reminder' => 'sometimes|boolean',
            'daily_outstanding' => 'sometimes|boolean',
            'daily_sales' => 'sometimes|boolean',
            'low_stock' => 'sometimes|boolean',
            'service_reminder' => 'sometimes|boolean',
            'whatsapp_alert' => 'sometimes|boolean',
        ]);

        $settings = ReminderSetting::firstOrCreate(
            ['user_id' => $request->user()->id]
        );

        $settings->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Reminder settings updated',
            'data' => $settings->fresh(),
        ]);
    }
}
