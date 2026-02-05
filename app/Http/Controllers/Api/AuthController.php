<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use App\Models\BusinessDetail;
use Illuminate\Support\Facades\Auth;


class AuthController extends Controller
{
//     public function sendOtp(Request $request)
// {
//     $request->validate([
//         'mobile' => 'required|digits:10'
//     ]);

//     $mobile = $request->mobile;

//     // find user
//     $user = User::where('mobile', $mobile)->first();

//     // If user exists, check cooldown
//     if ($user && $user->otp_sent_at) {
//         $secondsPassed = now()->diffInSeconds($user->otp_sent_at);

//         if ($secondsPassed < 30) {
//             return response()->json([
//                 'success' => false,
//                 'message' => 'Please wait before requesting another OTP',
//                 'seconds_left' => 30 - $secondsPassed
//             ], 429);
//         }
//     }

//     // Generate new OTP
//     $otp = rand(100000, 999999);

//     // Create or update user
//     $user = User::updateOrCreate(
//         ['mobile' => $mobile],
//         [
//             'otp' => $otp,
//             'otp_sent_at' => now(),
//             'otp_expires_at' => now()->addMinutes(10),
//             'is_verified' => false
//         ]
//     );

//     // Send OTP via 2Factor
//     $apiKey = env('TWO_FACTOR_API_KEY');
//     $url = "https://2factor.in/API/V1/$apiKey/SMS/$mobile/$otp";
//     Http::get($url);

//     return response()->json([
//         'success' => true,
//         'message' => 'OTP sent successfully',
//         'otp_debug' => $otp,  // remove in production
//         'cooldown_seconds' => 30
//     ]);
// }

   public function sendOtp(Request $request)
{
    $request->validate([
        'mobile' => 'required|digits:10'
    ]);

    $mobile = $request->mobile;

    // Find user
    $user = User::where('mobile', $mobile)->first();

    // Cooldown check (30 sec)
    if ($user && $user->otp_sent_at) {
        $secondsPassed = now()->diffInSeconds($user->otp_sent_at);

        if ($secondsPassed < 30) {
            return response()->json([
                'success' => false,
                'message' => 'Please wait before requesting another OTP',
                'seconds_left' => 30 - $secondsPassed
            ], 429);
        }
    }

    // Generate OTP
    $otp = rand(100000, 999999);

    // Create / Update user
    $user = User::updateOrCreate(
        ['mobile' => $mobile],
        [
            'otp' => $otp,
            'otp_sent_at' => now(),
            'otp_expires_at' => now()->addMinutes(10),
            'is_verified' => false
        ]
    );

    // ✅ SEND OTP USING JIO DLT APPROVED TEMPLATE
    $apiKey = env('TWO_FACTOR_API_KEY');
    $templateName = env('TWO_FACTOR_TEMPLATE_NAME'); // Exact name from 2Factor panel

    Http::asForm()->post(
        "https://2factor.in/API/V1/$apiKey/ADDON_SERVICES/SEND/TSMS",
        [
            'From' => 'KLBNKA',       // Sender ID (DLT approved)
            'To' => $mobile,
            'TemplateName' => $templateName,
            'VAR1' => $otp            // {#var#} in template
        ]
    );

    return response()->json([
        'success' => true,
        'message' => 'OTP sent successfully',
        'cooldown_seconds' => 30
    ]);
}




   public function verifyOtp(Request $request)
{
    $request->validate([
        'mobile' => 'required|digits:10',
        'otp' => 'required|digits:6'
    ]);

    $user = User::where('mobile', $request->mobile)
        ->where('otp', $request->otp)
        ->where('otp_expires_at', '>=', now())
        ->first();

    if (!$user) {
        return response()->json(['success' => false, 'message' => 'Invalid OTP'], 401);
    }

    // OTP verified
    $user->update([
        'is_verified' => true,
        'otp' => null
    ]);

    // Generate JWT token
    $token = JWTAuth::fromUser($user);

    // IMPORTANT — Correct check for business details
    $hasBusiness = BusinessDetail::where('user_id', $user->id)->exists();

    return response()->json([
        'success' => true,
        'message' => 'OTP verified successfully',
        'token' => $token,
        'user' => $user,
        'has_business_details' => $hasBusiness   // TRUE only if business details filled
    ]);
}



    public function updateBusinessDetails(Request $request)
{
    $request->validate([
        'city' => 'required',
        'billing_requirement' => 'required',
        'language' => 'required',
        'business_type' => 'required|array',
        'industry' => 'required',
        'gst_registered' => 'required|boolean',
        'gst_number' => 'nullable|string',
        'invoice_format' => 'nullable|string'
    ]);

    $details = BusinessDetail::updateOrCreate(
        ['user_id' => auth()->id()],
        $request->all()
    );

    return response()->json([
        'success' => true,
        'message' => 'Business details saved successfully',
        'data' => $details
    ]);
}

public function businessInfo(Request $request)
{
    $business = BusinessDetail::where('user_id', $request->user()->id)->first();

    return response()->json([
        'success' => true,
        'data' => [
            'business_name' => $business->industry ?? 'My Business',
        ]
    ]);
}


}
