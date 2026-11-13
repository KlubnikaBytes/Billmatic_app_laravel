<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Party;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class PartyActivityController extends Controller
{
    private function party(Request $request, int $partyId): Party
    {
        return Party::where('user_id', $request->user()->id)->findOrFail($partyId);
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'type' => ['required', Rule::in(['note', 'appointment'])],
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:10000',
            'scheduled_at' => 'required_if:type,appointment|nullable|date',
            'status' => ['nullable', Rule::in(['pending', 'completed', 'cancelled'])],
        ]);
    }

    private function normalize(array $data): array
    {
        if ($data['type'] === 'note') {
            $data['scheduled_at'] = null;
            $data['status'] = null;
        } else {
            $data['status'] = $data['status'] ?? 'pending';
        }
        return $data;
    }

    private function record(Request $request, ?int $partyId, int $activityId)
    {
        return DB::table('party_activities')
            ->where('user_id', $request->user()->id)
            ->where('party_id', $partyId)
            ->where('id', $activityId);
    }

    private function listing(Request $request, ?int $partyId)
    {
        $data = DB::table('party_activities')
            ->where('user_id', $request->user()->id)
            ->where('party_id', $partyId)
            ->orderByDesc('created_at')->get();
        return response()->json(['success' => true, 'data' => $data]);
    }

    private function createRecord(Request $request, ?int $partyId)
    {
        $data = $this->normalize($this->validated($request));
        $id = DB::table('party_activities')->insertGetId(array_merge($data, [
            'party_id' => $partyId,
            'user_id' => $request->user()->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]));
        return response()->json([
            'success' => true,
            'data' => DB::table('party_activities')->where('user_id', $request->user()->id)->find($id),
        ], 201);
    }

    private function updateRecord(Request $request, ?int $partyId, int $activityId)
    {
        $query = $this->record($request, $partyId, $activityId);
        abort_unless((clone $query)->exists(), 404);
        $query->update(array_merge($this->normalize($this->validated($request)), ['updated_at' => now()]));
        return response()->json([
            'success' => true,
            'data' => DB::table('party_activities')->where('user_id', $request->user()->id)->find($activityId),
        ]);
    }

    private function deleteRecord(Request $request, ?int $partyId, int $activityId)
    {
        $deleted = $this->record($request, $partyId, $activityId)->delete();
        abort_unless($deleted, 404);
        return response()->json(['success' => true]);
    }

    // Existing party-specific endpoints
    public function index(Request $request, int $partyId)
    {
        $this->party($request, $partyId);
        return $this->listing($request, $partyId);
    }

    public function store(Request $request, int $partyId)
    {
        $this->party($request, $partyId);
        return $this->createRecord($request, $partyId);
    }

    public function update(Request $request, int $partyId, int $activityId)
    {
        $this->party($request, $partyId);
        return $this->updateRecord($request, $partyId, $activityId);
    }

    public function destroy(Request $request, int $partyId, int $activityId)
    {
        $this->party($request, $partyId);
        return $this->deleteRecord($request, $partyId, $activityId);
    }

    // New general endpoints (party_id is NULL)
    public function generalIndex(Request $request)
    {
        return $this->listing($request, null);
    }

    public function generalStore(Request $request)
    {
        return $this->createRecord($request, null);
    }

    public function generalUpdate(Request $request, int $activityId)
    {
        return $this->updateRecord($request, null, $activityId);
    }

    public function generalDestroy(Request $request, int $activityId)
    {
        return $this->deleteRecord($request, null, $activityId);
    }
}
