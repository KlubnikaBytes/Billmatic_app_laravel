<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Invoice;
use App\Models\InvoiceItem;

class InvoiceController extends Controller
{
    public function store(Request $request)
    {
        $user = $request->user();

        $data = $request->validate([
            'invoice_number' => 'required|string|max:50|unique:invoices,invoice_number',
            'invoice_date'   => 'required|date',
            'due_date'       => 'nullable|date',
            'party_id'       => 'required|exists:parties,id',
            'place_of_supply'=> 'nullable|string|max:100',
            'notes'          => 'nullable|string',

            'items'          => 'required|array|min:1',
            'items.*.item_id'=> 'nullable|exists:items,id',
            'items.*.description' => 'nullable|string',
            'items.*.qty'         => 'required|numeric|min:0.01',
            'items.*.unit'        => 'nullable|string|max:50',
            'items.*.price'       => 'required|numeric|min:0',
            'items.*.discount'    => 'nullable|numeric|min:0',
            'items.*.gst_percent' => 'nullable|numeric|min:0',
        ]);

        return DB::transaction(function () use ($user, $data) {

            $subtotal  = 0;
            $totalTax  = 0;

            foreach ($data['items'] as &$line) {
                $qty   = $line['qty'];
                $price = $line['price'];
                $disc  = $line['discount'] ?? 0;
                $gst   = $line['gst_percent'] ?? 0;

                $lineAmount = $qty * $price - $disc;
                $gstAmount  = $lineAmount * ($gst / 100);

                $line['gst_amount'] = $gstAmount;
                $line['line_total'] = $lineAmount + $gstAmount;

                $subtotal += $lineAmount;
                $totalTax += $gstAmount;
            }

            $grandTotal = $subtotal + $totalTax;

            $invoice = Invoice::create([
                'user_id'        => $user->id,
                'invoice_number' => $data['invoice_number'],
                'invoice_date'   => $data['invoice_date'],
                'due_date'       => $data['due_date'] ?? null,
                'party_id'       => $data['party_id'],
                'place_of_supply'=> $data['place_of_supply'] ?? null,
                'subtotal'       => $subtotal,
                'total_tax'      => $totalTax,
                'grand_total'    => $grandTotal,
                'notes'          => $data['notes'] ?? null,
            ]);

            foreach ($data['items'] as $line) {
                $line['invoice_id'] = $invoice->id;
                InvoiceItem::create($line);
            }

            return response()->json([
                'success' => true,
                'message' => 'Invoice created successfully',
                'data' => $invoice->load(['items', 'party']),

            ], 201);
        });
    }

  public function lastNumber()
{
    $last = Invoice::select('invoice_number')
        ->where('invoice_number', 'LIKE', 'INV-%')
        ->orderByRaw("CAST(SUBSTRING(invoice_number, 5) AS UNSIGNED) DESC")
        ->first();

    if (!$last) {
        return response()->json(['last_number' => 0]);
    }

    // Extract numeric part from INV-XXX
    $num = intval(str_replace('INV-', '', $last->invoice_number));

    return response()->json(['last_number' => $num]);
}


}
