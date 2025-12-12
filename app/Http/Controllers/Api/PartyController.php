<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Party;

class PartyController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $parties = Party::where('user_id', $user->id)
            ->orderBy('party_name')
            ->get();

        return response()->json([
            'success' => true,
            'data'    => $parties,
        ]);
    }

    public function store(Request $request)
    {
        $user = $request->user();

        $data = $request->validate([
            'party_name'          => 'required|string|max:255',
            'contact_number'      => 'nullable|string|max:20',
            'party_type'          => 'required|in:customer,supplier',

            'gst_number'          => 'nullable|string|max:50',
            'pan_number'          => 'nullable|string|max:50',

            'billing_street'      => 'nullable|string|max:255',
            'billing_state'       => 'nullable|string|max:100',
            'billing_pincode'     => 'nullable|string|max:10',
            'billing_city'        => 'nullable|string|max:100',

            'opening_balance'     => 'nullable|numeric',
            'credit_period_days'  => 'nullable|integer',
            'credit_limit'        => 'nullable|numeric',

            'party_category_id'   => 'nullable|exists:party_categories,id',
            'contact_person_name' => 'nullable|string|max:255',
            'dob'                 => 'nullable|date',
        ]);

        $data['user_id'] = $user->id;

        $party = Party::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Party created successfully',
            'data'    => $party,
        ], 201);
    }
}
