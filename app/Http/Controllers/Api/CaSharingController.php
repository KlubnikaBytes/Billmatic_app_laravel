<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ManagedUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class CaSharingController extends Controller
{
    private const REPORTS = ['gstr1', 'gstr2', 'bill-wise-profit', 'sales-summary', 'profit-loss', 'balance-sheet'];

    public function show(Request $request)
    {
        $ownerId = $request->user()->id;
        $row = DB::table('ca_sharing_settings')->where('user_id', $ownerId)->first();
        $cas = ManagedUser::where('owner_user_id', $ownerId)->where('role', 'CA')
            ->orderBy('id')->get(['id', 'name', 'phone', 'email']);
        $selected = $cas->firstWhere('id', $row->ca_managed_user_id ?? null) ?? $cas->first();

        return response()->json([
            'success' => true,
            'data' => [
                'ca_name' => $selected?->name ?? ($row->ca_name ?? ''),
                'ca_phone' => $selected?->phone ?? ($row->ca_phone ?? ''),
                'ca_email' => $selected?->email ?? ($row->ca_email ?? ''),
                'ca_managed_user_id' => $selected?->id,
                'cas' => $cas,
                'enabled' => (bool) ($row->enabled ?? false),
                'reports' => $row ? (json_decode($row->reports ?: '[]', true) ?: []) : [],
            ],
        ]);
    }

    public function update(Request $request)
    {
        $ownerId = $request->user()->id;
        $validated = $request->validate([
            'ca_managed_user_id' => [
                'required', 'integer',
                Rule::exists('managed_users', 'id')->where(fn ($q) => $q
                    ->where('owner_user_id', $ownerId)->where('role', 'CA')),
            ],
            'enabled' => 'required|boolean',
            'reports' => 'required|array',
            'reports.*' => ['string', Rule::in(self::REPORTS)],
        ]);

        if ($validated['enabled'] && count($validated['reports']) === 0) {
            return response()->json(['success' => false, 'message' => 'Select at least one report.'], 422);
        }

        $ca = ManagedUser::where('owner_user_id', $ownerId)->where('role', 'CA')
            ->findOrFail($validated['ca_managed_user_id']);

        DB::table('ca_sharing_settings')->updateOrInsert(
            ['user_id' => $ownerId],
            [
                'ca_managed_user_id' => $ca->id,
                'ca_name' => $ca->name,
                'ca_phone' => $ca->phone,
                'ca_email' => $ca->email,
                'enabled' => $validated['enabled'],
                'reports' => json_encode(array_values(array_unique($validated['reports']))),
                'updated_at' => now(),
            ]
        );

        return response()->json(['success' => true, 'message' => 'CA sharing settings saved successfully.']);
    }
}
