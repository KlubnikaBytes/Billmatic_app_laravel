<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use App\Models\BusinessDetail;
use Illuminate\Support\Facades\Auth;
use App\Models\ManagedUser;



class AuthController extends Controller
{
    public function sendOtp(Request $request)
{
    $request->validate([
        'mobile' => 'required|digits:10'
    ]);

    $mobile = $request->mobile;

    // find user
    $user = User::where('mobile', $mobile)->first();

    // If user exists, check cooldown
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

    // Generate new OTP
    $otp = rand(100000, 999999);

    // Create or update user
    $user = User::updateOrCreate(
        ['mobile' => $mobile],
        [
            'otp' => $otp,
            'otp_sent_at' => now(),
            'otp_expires_at' => now()->addMinutes(10),
            'is_verified' => false
        ]
    );

    // Send OTP via 2Factor
    $apiKey = env('TWO_FACTOR_API_KEY');
    $url = "https://2factor.in/API/V1/$apiKey/SMS/$mobile/$otp";
    Http::get($url);

    return response()->json([
        'success' => true,
        'message' => 'OTP sent successfully',
        'otp_debug' => $otp,  // remove in production
        'cooldown_seconds' => 30
    ]);
}

//    public function sendOtp(Request $request)
// {
//     $request->validate([
//         'mobile' => 'required|digits:10'
//     ]);

//     $mobile = $request->mobile;

//     // Find user
//     $user = User::where('mobile', $mobile)->first();

//     // Cooldown check (30 sec)
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

//     // Generate OTP
//     $otp = rand(100000, 999999);

//     // Create / Update user
//     $user = User::updateOrCreate(
//         ['mobile' => $mobile],
//         [
//             'otp' => $otp,
//             'otp_sent_at' => now(),
//             'otp_expires_at' => now()->addMinutes(10),
//             'is_verified' => false
//         ]
//     );

//     // ✅ SEND OTP USING JIO DLT APPROVED TEMPLATE
//     $apiKey = env('TWO_FACTOR_API_KEY');
//     $templateName = env('TWO_FACTOR_TEMPLATE_NAME'); // Exact name from 2Factor panel

//     Http::asForm()->post(
//         "https://2factor.in/API/V1/$apiKey/ADDON_SERVICES/SEND/TSMS",
//         [
//             'From' => 'KLBNKA',       // Sender ID (DLT approved)
//             'To' => $mobile,
//             'TemplateName' => $templateName,
//             'VAR1' => $otp            // {#var#} in template
//         ]
//     );

//     return response()->json([
//         'success' => true,
//         'message' => 'OTP sent successfully',
//         'cooldown_seconds' => 30
//     ]);
// }




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


//need to add //

public function sendManagedOtp(Request $request)
{
    $validated = $request->validate([
        'mobile' => 'required|digits:10',
    ]);

    // Only allow phone numbers already registered as managed users.
    $managedUser = ManagedUser::where(
        'phone',
        $validated['mobile']
    )->first();

    if (!$managedUser) {
        return response()->json([
            'success' => false,
            'message' => 'This phone number is not registered as a managed user.',
        ], 404);
    }

    // Prevent repeated OTP requests within 30 seconds.
    if ($managedUser->otp_sent_at) {
        $secondsPassed = now()->diffInSeconds(
            $managedUser->otp_sent_at
        );

        if ($secondsPassed < 30) {
            return response()->json([
                'success' => false,
                'message' => 'Please wait before requesting another OTP.',
                'seconds_left' => 30 - $secondsPassed,
            ], 429);
        }
    }

    $otp = (string) random_int(100000, 999999);

    $managedUser->update([
        'otp' => $otp,
        'otp_sent_at' => now(),
        'otp_expires_at' => now()->addMinutes(10),
        'is_verified' => false,
    ]);

    // Use the same SMS provider as the existing owner login.
    $apiKey = env('TWO_FACTOR_API_KEY');

    try {
        $response = Http::timeout(15)->get(
            "https://2factor.in/API/V1/{$apiKey}/SMS/{$validated['mobile']}/{$otp}"
        );

        if (!$response->successful()) {
            $managedUser->update([
                'otp' => null,
                'otp_expires_at' => null,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Unable to send OTP. Please try again.',
            ], 502);
        }
    } catch (\Throwable $e) {
        $managedUser->update([
            'otp' => null,
            'otp_expires_at' => null,
        ]);

        return response()->json([
            'success' => false,
            'message' => 'OTP service is temporarily unavailable.',
        ], 502);
    }

    return response()->json([
        'success' => true,
        'message' => 'OTP sent successfully.',
        'cooldown_seconds' => 30,
    ]);
}


public function verifyManagedOtp(Request $request)
{
    $validated = $request->validate([
        'mobile' => 'required|digits:10',
        'otp' => 'required|digits:6',
    ]);

    $managedUser = ManagedUser::where(
        'phone',
        $validated['mobile']
    )
        ->where('otp', $validated['otp'])
        ->where('otp_expires_at', '>=', now())
        ->first();

    if (!$managedUser) {
        return response()->json([
            'success' => false,
            'message' => 'Invalid or expired OTP.',
        ], 401);
    }

    $managedUser->update([
        'is_verified' => true,
        'otp' => null,
        'otp_expires_at' => null,
    ]);

    // Issue a token using the separate managed-user guard.
    $token = auth('managed-api')->login($managedUser);

    return response()->json([
        'success' => true,
        'message' => 'Managed-user login successful.',
        'token' => $token,
        'token_type' => 'Bearer',
        'user' => [
            'id' => $managedUser->id,
            'name' => $managedUser->name,
            'phone' => $managedUser->phone,
            'email' => $managedUser->email,
            'role' => $managedUser->role,
            'owner_user_id' => $managedUser->owner_user_id,
        ],
    ]);
}




//     public function updateBusinessDetails(Request $request)
// {
//     $request->validate([
//         'city' => 'required',
//         'billing_requirement' => 'required',
//         'language' => 'required',
//         'business_type' => 'required|array',
//         'industry' => 'required',
//         'gst_registered' => 'required|boolean',
//         'gst_number' => 'nullable|string',
//         'invoice_format' => 'nullable|string'
//     ]);

//     $details = BusinessDetail::updateOrCreate(
//         ['user_id' => auth()->id()],
//         $request->all()
//     );

//     return response()->json([
//         'success' => true,
//         'message' => 'Business details saved successfully',
//         'data' => $details
//     ]);
// }

public function updateBusinessDetails(Request $request)
{
    $request->validate([
        'city' => 'required',
        'billing_requirement' => 'required',
        'language' => 'required',

        'business_type' => 'required|array',
        'industry' => 'required',

        'gst_registered' => 'required|boolean',

        'gst_number' => 'nullable|string|max:15',

        'business_name' => 'nullable|string|max:255',
        'business_address' => 'nullable|string',
        'gst_status' => 'nullable|string|max:50',
        'gst_legal_name' => 'nullable|string|max:255',

        'pan_cin' => 'nullable|string',
        'trade_license_or_udyam' => 'nullable|string',

        'invoice_format' => 'nullable|string',
    ]);

    $details = BusinessDetail::updateOrCreate(
        [
            'user_id' => auth()->id()
        ],
        $request->only([
            'city',
            'billing_requirement',
            'language',
            'business_type',
            'industry',

            'gst_registered',
            'gst_number',

            'business_name',
            'business_address',
            'gst_status',
            'gst_legal_name',

            'pan_cin',
            'trade_license_or_udyam',

            'invoice_format',
        ])
    );

    return response()->json([
        'success' => true,
        'message' => 'Business details saved successfully',
        'data' => $details
    ]);
}


//need to add
// public function businessInfo(Request $request)
// {
//     $business = BusinessDetail::where('user_id', $request->user()->id)->first();

//     return response()->json([
//         'success' => true,
//         'data' => [
//             'business_name' => $business->business_name ?? 'My Business',
//             'mobile' => $user->mobile ?? '',
//         ]
//     ]);
// }


// need to add
public function businessInfo(Request $request)
{
    $user = $request->user();

    $business = BusinessDetail::where(
        'user_id',
        $user->id
    )->first();

    return response()->json([
        'success' => true,
        'data' => [
            'business_name' => $business->business_name ?? 'Business Name',
            'mobile' => $user->mobile ?? '',
            //need to add //
             'language' => $business->language ?? 'English',
        ]
    ]);
}

public function refresh()
{
    try {
        $newToken = JWTAuth::refresh(JWTAuth::getToken());

        return response()->json([
            'success' => true,
            'token' => $newToken,
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Token refresh failed'
        ], 401);
    }
}

public function logout(Request $request)
{
    try {
        JWTAuth::invalidate(JWTAuth::getToken());

        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully'
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Failed to logout'
        ], 500);
    }
}


public function verifyGSTIN(Request $request)
{
    $request->validate([
        'gstin' => [
            'required',
            'string',
            'size:15',
            'regex:/^[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z][1-9A-Z]Z[0-9A-Z]$/'
        ],
    ]);

    $gstin = strtoupper(trim($request->gstin));

    try {

        $url = rtrim(
            env('GSTIN_API_URL'),
            '/'
        ) . '/' . $gstin;

        $response = Http::withHeaders([
            'x-api-key' => env('GSTIN_API_KEY'),
            'Accept' => 'application/json',
        ])
        ->timeout(15)
        ->get($url);

        if (!$response->successful()) {

            return response()->json([
                'success' => false,
                'message' => 'Unable to verify GSTIN right now.',
            ], 422);
        }

        $result = $response->json();

        if (
            !isset($result['success']) ||
            $result['success'] !== true
        ) {

            return response()->json([
                'success' => false,
                'message' =>
                    $result['message'] ??
                    'GSTIN could not be verified.',
            ], 422);
        }

        $data = $result['data'] ?? $result;

        $tradeName =
            $data['trade_name'] ??
            null;

        $legalName =
            $data['legal_name'] ??
            null;

        $address =
            $data['address'] ??
            null;

        $status =
            $data['status'] ??
            null;

        return response()->json([
            'success' => true,

            'data' => [
                'gstin' =>
                    $data['gstin'] ??
                    $gstin,

                'business_name' =>
                    $tradeName ??
                    $legalName ??
                    '',

                'legal_name' =>
                    $legalName ??
                    '',

                'business_address' =>
                    $address ??
                    '',

                'gst_status' =>
                    $status ??
                    '',
            ],
        ]);

    } catch (\Throwable $e) {

        return response()->json([
            'success' => false,
            'message' => 'GST verification failed.',
            'error' => $e->getMessage(),
        ], 500);
    }
}

//need to add //

public function updateIndustry(Request $request)
{
    $validated = $request->validate([
        'industry' => 'required|string|max:255',
    ]);

    $business = BusinessDetail::where(
        'user_id',
        $request->user()->id
    )->first();

    if (!$business) {
        return response()->json([
            'success' => false,
            'message' => 'Business details not found.',
        ], 404);
    }

    $business->industry = $validated['industry'];
    $business->save();

    return response()->json([
        'success' => true,
        'message' => 'Industry updated successfully.',
        'data' => [
            'industry' => $business->industry,
        ],
    ]);
}

//need to add//

public function businessDetails(Request $request)
{
    $user = $request->user();

    $business = BusinessDetail::where(
        'user_id',
        $user->id
    )->first();

    if (!$business) {
        return response()->json([
            'success' => false,
            'message' => 'Business details not found.',
        ], 404);
    }

    return response()->json([
        'success' => true,
        'data' => [
            'business_name' => $business->business_name,
            'industry' => $business->industry,
            'business_email' => $business->business_email,
               //need to add //
              // Business phone number
            'business_phone_number' =>$business->business_phone_number ?? '',
            'show_phone_on_invoice' =>(bool) $business->show_phone_on_invoice,
            'gst_number' => $business->gst_number,
            'pan_cin' => $business->pan_cin,
            'business_address' => $business->business_address,
            'city' => $business->city,
            'billing_requirement' => $business->billing_requirement,
            'language' => $business->language,
            'business_type' => $business->business_type,
            'gst_registered' => $business->gst_registered,
            'gst_status' => $business->gst_status,
            'gst_legal_name' => $business->gst_legal_name,
            'trade_license_or_udyam' => $business->trade_license_or_udyam,
            'invoice_format' => $business->invoice_format,
            //need to add//
            'invoice_theme' => $business->invoice_theme ?? 'None',
            'invoice_template' => $business->invoice_template ?? 'luxury',
            'invoice_theme_color' => $business->invoice_theme_color ?? '#C62828',
        ],
    ]);
}


public function updateBusinessType(Request $request)
{
    $validated = $request->validate([
        'business_type' => 'required|array|min:1',
        'business_type.*' => [
            'required',
            'string',
            'in:Retailer,Wholesaler,Distributor,Manufacturer,Services',
        ],
    ]);

    $business = BusinessDetail::where(
        'user_id',
        $request->user()->id
    )->first();

    if (!$business) {
        return response()->json([
            'success' => false,
            'message' => 'Business details not found.',
        ], 404);
    }

    $business->business_type = $validated['business_type'];
    $business->save();

    // Return the value saved in the database.
    $business->refresh();

    return response()->json([
        'success' => true,
        'message' => 'Business Type saved successfully.',
        'data' => [
            'business_type' => $business->business_type,
        ],
    ]);
}


public function updateBusinessTaxIdentifiers(Request $request)
{
    $validated = $request->validate([
        'gst_number' => 'sometimes|nullable|string|max:15',
        'pan_cin' => 'sometimes|nullable|string|max:21',
    ]);

    if (
        !$request->exists('gst_number') &&
        !$request->exists('pan_cin')
    ) {
        return response()->json([
            'success' => false,
            'message' => 'No tax details were provided.',
        ], 422);
    }

    $business = BusinessDetail::where(
        'user_id',
        $request->user()->id
    )->first();

    if (!$business) {
        return response()->json([
            'success' => false,
            'message' => 'Business details not found.',
        ], 404);
    }

    if (array_key_exists('gst_number', $validated)) {
        $business->gst_number = $validated['gst_number'];
    }

    if (array_key_exists('pan_cin', $validated)) {
        $business->pan_cin = $validated['pan_cin'];
    }

    $business->save();
    $business->refresh();

    return response()->json([
        'success' => true,
        'message' => 'Tax details saved successfully.',
        'data' => [
            'gst_number' => $business->gst_number,
            'pan_cin' => $business->pan_cin,
        ],
    ]);
}

public function updateBusinessAddress(Request $request)
{
    $validated = $request->validate([
        'business_address' => 'required|string|max:1000',
        'business_state' => 'required|string|max:100',
        'business_pincode' => 'required|digits:6',
        'city' => 'required|string|max:100',
    ]);

    $business = BusinessDetail::where(
        'user_id',
        $request->user()->id
    )->first();

    if (!$business) {
        return response()->json([
            'success' => false,
            'message' => 'Business details not found.',
        ], 404);
    }

    $business->business_address = $validated['business_address'];
    $business->business_state = $validated['business_state'];
    $business->business_pincode = $validated['business_pincode'];
    $business->city = $validated['city'];
    $business->save();

    return response()->json([
        'success' => true,
        'message' => 'Business address saved successfully.',
        'data' => [
            'business_address' => $business->business_address,
            'business_state' => $business->business_state,
            'business_pincode' => $business->business_pincode,
            'city' => $business->city,
        ],
    ]);
}

public function updateBusinessEmail(Request $request)
{
    $validated = $request->validate([
        'business_email' => 'required|email|max:255',
    ]);

    $business = BusinessDetail::where(
        'user_id',
        $request->user()->id
    )->first();

    if (!$business) {
        return response()->json([
            'success' => false,
            'message' => 'Business details not found.',
        ], 404);
    }

    $business->business_email = $validated['business_email'];
    $business->save();
    $business->refresh();

    return response()->json([
        'success' => true,
        'message' => 'Business email saved successfully.',
        'data' => [
            'business_email' => $business->business_email,
        ],
    ]);
}


public function updateBusinessCustomFields(Request $request)
{
    $validated = $request->validate([
        'custom_fields' => 'present|array|max:100',
        'custom_fields.*.name' => 'required|string|max:100',
        'custom_fields.*.value' => 'required|string|max:1000',
    ]);

    $business = BusinessDetail::where(
        'user_id',
        $request->user()->id
    )->first();

    if (!$business) {
        return response()->json([
            'success' => false,
            'message' => 'Business details not found.',
        ], 404);
    }

    $business->custom_fields = $validated['custom_fields'];
    $business->save();
    $business->refresh();

    return response()->json([
        'success' => true,
        'message' => 'Business details saved successfully.',
        'data' => [
            'custom_fields' => $business->custom_fields ?? [],
        ],
    ]);
}


public function businessPhone(Request $request)
{
    $business = BusinessDetail::where(
        'user_id',
        $request->user()->id
    )->first();

    if (!$business) {
        return response()->json([
            'success' => false,
            'message' => 'Business details not found.',
        ], 404);
    }

    return response()->json([
        'success' => true,
        'data' => [
            'business_phone_number' =>
                $business->business_phone_number ?? '',
            'show_phone_on_invoice' =>
                (bool) $business->show_phone_on_invoice,
        ],
    ]);
}

public function updateBusinessPhone(Request $request)
{
    $validated = $request->validate([
        'business_phone_number' => [
            'required',
            'string',
            'regex:/^[0-9]{10}$/',
        ],
        'show_phone_on_invoice' => 'required|boolean',
    ]);

    $business = BusinessDetail::where(
        'user_id',
        $request->user()->id
    )->first();

    if (!$business) {
        return response()->json([
            'success' => false,
            'message' => 'Business details not found.',
        ], 404);
    }

    $business->business_phone_number =
        $validated['business_phone_number'];

    $business->show_phone_on_invoice =
        $validated['show_phone_on_invoice'];

    $business->save();
    $business->refresh();

    return response()->json([
        'success' => true,
        'message' => 'Business phone number saved successfully.',
        'data' => [
            'business_phone_number' =>
                $business->business_phone_number,
            'show_phone_on_invoice' =>
                (bool) $business->show_phone_on_invoice,
        ],
    ]);
}


public function updateBusinessName(Request $request)
{
    $validated = $request->validate([
        'business_name' => 'required|string|max:255',
    ]);

    $business = BusinessDetail::where(
        'user_id',
        $request->user()->id
    )->first();

    if (!$business) {
        return response()->json([
            'success' => false,
            'message' => 'Business details not found.',
        ], 404);
    }

    $business->business_name = $validated['business_name'];
    $business->save();
    $business->refresh();

    return response()->json([
        'success' => true,
        'message' => 'Business name saved successfully.',
        'data' => [
            'business_name' => $business->business_name,
        ],
    ]);
}



public function businessTerms(Request $request)
{
    $business = BusinessDetail::where(
        'user_id',
        $request->user()->id
    )->first();

    if (!$business) {
        return response()->json([
            'success' => false,
            'message' => 'Business details not found.',
        ], 404);
    }

    return response()->json([
        'success' => true,
        'data' => [
            'terms_invoice_type' =>
                $business->terms_invoice_type ?? 'Sales Invoice',
            'terms_and_conditions' =>
                $business->terms_and_conditions ?? '',
        ],
    ]);
}

public function updateBusinessTerms(Request $request)
{
    $validated = $request->validate([
        'terms_invoice_type' => [
            'required',
            'string',
            'in:Sales Invoice,Purchase Invoice,Sales Return,Purchase Return,Estimate,Delivery Challan',
        ],
        'terms_and_conditions' => 'required|string|max:10000',
    ]);

    $business = BusinessDetail::where(
        'user_id',
        $request->user()->id
    )->first();

    if (!$business) {
        return response()->json([
            'success' => false,
            'message' => 'Business details not found.',
        ], 404);
    }

    $business->terms_invoice_type =
        $validated['terms_invoice_type'];

    $business->terms_and_conditions =
        $validated['terms_and_conditions'];

    $business->save();
    $business->refresh();

    return response()->json([
        'success' => true,
        'message' => 'Terms and Conditions saved successfully.',
        'data' => [
            'terms_invoice_type' =>
                $business->terms_invoice_type,
            'terms_and_conditions' =>
                $business->terms_and_conditions,
        ],
    ]);
}


public function businessSignature(Request $request)
{
    $business = BusinessDetail::where(
        'user_id',
        $request->user()->id
    )->first();

    if (!$business) {
        return response()->json([
            'success' => false,
            'message' => 'Business details not found.',
        ], 404);
    }

    return response()->json([
        'success' => true,
        'data' => [
            'signature_image' => $business->signature_image,
            'show_empty_signature_box' =>
                (bool) $business->show_empty_signature_box,
            'enable_receiver_signature' =>
                (bool) $business->enable_receiver_signature,
        ],
    ]);
}

public function updateBusinessSignature(Request $request)
{
    $validated = $request->validate([
        'signature_image' => 'nullable|string|max:5000000',
        'show_empty_signature_box' => 'required|boolean',
        'enable_receiver_signature' => 'required|boolean',
    ]);

    $business = BusinessDetail::where(
        'user_id',
        $request->user()->id
    )->first();

    if (!$business) {
        return response()->json([
            'success' => false,
            'message' => 'Business details not found.',
        ], 404);
    }

    $business->signature_image =
        $validated['signature_image'] ?? null;

    $business->show_empty_signature_box =
        $validated['show_empty_signature_box'];

    $business->enable_receiver_signature =
        $validated['enable_receiver_signature'];

    $business->save();
    $business->refresh();

    return response()->json([
        'success' => true,
        'message' => 'Signature settings saved successfully.',
        'data' => [
            'signature_image' => $business->signature_image,
            'show_empty_signature_box' =>
                (bool) $business->show_empty_signature_box,
            'enable_receiver_signature' =>
                (bool) $business->enable_receiver_signature,
        ],
    ]);
}


public function discountType(Request $request)
{
    $business = BusinessDetail::where(
        'user_id',
        $request->user()->id
    )->first();

    if (!$business) {
        return response()->json([
            'success' => false,
            'message' => 'Business details not found.',
        ], 404);
    }

    return response()->json([
        'success' => true,
        'data' => [
            'discount_type' => $business->discount_type ?? 'after_tax',
        ],
    ]);
}

public function updateDiscountType(Request $request)
{
    $validated = $request->validate([
        'discount_type' => 'required|in:before_tax,after_tax',
    ]);

    $business = BusinessDetail::where(
        'user_id',
        $request->user()->id
    )->first();

    if (!$business) {
        return response()->json([
            'success' => false,
            'message' => 'Business details not found.',
        ], 404);
    }

    $business->discount_type = $validated['discount_type'];
    $business->save();
    $business->refresh();

    return response()->json([
        'success' => true,
        'message' => 'Discount type saved successfully.',
        'data' => [
            'discount_type' => $business->discount_type,
        ],
    ]);
}


public function invoiceTheme(Request $request)
{
    $business = BusinessDetail::where(
        'user_id',
        $request->user()->id
    )->first();

    if (!$business) {
        return response()->json([
            'success' => false,
            'message' => 'Business details not found. Please complete business details first.',
        ], 404);
    }

    return response()->json([
        'success' => true,
        'data' => [
            'invoice_theme' => $business->invoice_theme ?? 'None',
            'invoice_template' => $business->invoice_template ?? 'luxury',
            'invoice_theme_color' => $business->invoice_theme_color ?? '#C62828',
        ],
    ]);
}

public function updateInvoiceTheme(Request $request)
{
    $validated = $request->validate([
        'invoice_theme' => 'required|string|max:100',
        'invoice_template' => 'required|in:luxury,stylish,advance_gst,advance_gst_tally',
        'invoice_theme_color' => [
            'required',
            'string',
            'regex:/^#[0-9A-Fa-f]{6}$/',
        ],
    ]);

    $business = BusinessDetail::where(
        'user_id',
        $request->user()->id
    )->first();

    if (!$business) {
        return response()->json([
            'success' => false,
            'message' => 'Business details not found. Please complete business details first.',
        ], 404);
    }

    $business->invoice_theme = $validated['invoice_theme'];
    $business->invoice_template = $validated['invoice_template'];
    $business->invoice_theme_color = strtoupper(
        $validated['invoice_theme_color']
    );

    $business->save();
    $business->refresh();

    return response()->json([
        'success' => true,
        'message' => 'Invoice theme saved successfully.',
        'data' => [
            'invoice_theme' => $business->invoice_theme,
            'invoice_template' => $business->invoice_template,
            'invoice_theme_color' => $business->invoice_theme_color,
        ],
    ]);
}


public function invoiceNumberPrefix(Request $request)
{
    $business = BusinessDetail::where(
        'user_id',
        $request->user()->id
    )->first();

    if (!$business) {
        return response()->json([
            'success' => false,
            'message' => 'Business details not found. Please complete business details first.',
        ], 404);
    }

    return response()->json([
        'success' => true,
        'data' => [
            'invoice_number_prefix' =>
                $business->invoice_number_prefix ?? 'INV-',

            'starting_serial_number' =>
                (int) ($business->starting_serial_number ?? 1),
        ],
    ]);
}

public function updateInvoiceNumberPrefix(Request $request)
{
    $validated = $request->validate([
        'invoice_number_prefix' => 'required|string|max:20',
        'starting_serial_number' => 'required|integer|min:1',
    ]);

    $business = BusinessDetail::where(
        'user_id',
        $request->user()->id
    )->first();

    if (!$business) {
        return response()->json([
            'success' => false,
            'message' => 'Business details not found. Please complete business details first.',
        ], 404);
    }

    $business->invoice_number_prefix =
        trim($validated['invoice_number_prefix']);

    $business->starting_serial_number =
        $validated['starting_serial_number'];

    $business->save();
    $business->refresh();

    return response()->json([
        'success' => true,
        'message' => 'Invoice number prefix saved successfully.',
        'data' => [
            'invoice_number_prefix' =>
                $business->invoice_number_prefix,

            'starting_serial_number' =>
                (int) $business->starting_serial_number,
        ],
    ]);
}












}
