<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Purchase Invoice - {{ $purchase->purchase_number }}</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            background: #f3f4f6;
            font-family: Arial, Helvetica, sans-serif;
            color: #111827;
        }

        .top-bar {
            position: sticky;
            top: 0;
            z-index: 100;
            background: white;
            border-bottom: 1px solid #ddd;
            padding: 14px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .top-title {
            font-size: 18px;
            font-weight: 600;
        }

        .actions {
            display: flex;
            gap: 8px;
        }

        .btn {
            border: 1px solid #d1d5db;
            background: white;
            padding: 9px 16px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
        }

        .btn:hover {
            background: #f3f4f6;
        }

        .btn-primary {
            background: #4f46e5;
            color: white;
            border-color: #4f46e5;
        }

        .page-wrapper {
            padding: 25px;
        }

        .invoice {
            width: 210mm;
            min-height: 297mm;
            margin: 0 auto;
            background: white;
            padding: 18mm;
            box-shadow: 0 2px 10px rgba(0,0,0,.08);
        }

        .business-name {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 5px;
        }

        .business-info {
            font-size: 13px;
            line-height: 1.6;
        }

        .invoice-header {
            margin-top: 12px;
            border-top: 6px solid #111;
            background: #e5e5e5;
            padding: 12px 14px;
            display: flex;
            justify-content: space-between;
            font-size: 14px;
        }

        .two-column {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 50px;
            margin-top: 12px;
            margin-bottom: 15px;
        }

        .section-title {
            font-weight: 700;
            font-size: 14px;
            margin-bottom: 8px;
        }

        .section-value {
            font-size: 14px;
            line-height: 1.6;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
        }

        thead {
            border-top: 2px solid #111;
            border-bottom: 2px solid #111;
        }

        th {
            padding: 10px 6px;
            text-align: left;
            font-weight: 700;
        }

        td {
            padding: 9px 6px;
            border-bottom: 1px solid #ddd;
            vertical-align: top;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .item-name {
            font-weight: 600;
        }

        .item-description {
            font-size: 11px;
            color: #555;
            margin-top: 3px;
        }

        .subtotal-row td {
            border-top: 2px solid #111;
            font-weight: 700;
            padding-top: 12px;
        }

        .bottom-section {
            display: grid;
            grid-template-columns: 1.4fr 1fr;
            gap: 30px;
            margin-top: 25px;
        }

        .terms {
            font-size: 12px;
            line-height: 1.5;
        }

        .terms-title {
            font-weight: 700;
            margin-bottom: 8px;
        }

        .totals {
            width: 100%;
        }

        .total-line {
            display: flex;
            justify-content: space-between;
            padding: 5px 0;
            font-size: 13px;
        }

        .total-line.discount {
            color: #111;
        }

        .total-line.grand {
            border-top: 2px solid #111;
            border-bottom: 2px solid #111;
            padding: 9px 0;
            margin-top: 5px;
            font-weight: 700;
            font-size: 15px;
        }

        .total-line.balance {
            font-weight: 700;
        }

        .negative {
            color: #dc2626;
        }

        .positive {
            color: #059669;
        }

        .words {
            margin-top: 28px;
            text-align: right;
            font-size: 13px;
        }

        .words-title {
            font-weight: 700;
            margin-bottom: 5px;
        }

        .footer {
            margin-top: 35px;
            padding-top: 10px;
            border-top: 1px solid #ddd;
            text-align: center;
            font-size: 11px;
            color: #777;
        }

        @media print {
            body {
                background: white;
            }

            .top-bar {
                display: none;
            }

            .page-wrapper {
                padding: 0;
            }

            .invoice {
                width: 210mm;
                min-height: 297mm;
                margin: 0;
                box-shadow: none;
                padding: 15mm;
            }
        }

        @media(max-width: 900px) {
            .invoice {
                width: 100%;
                min-height: auto;
                padding: 20px;
            }

            .two-column,
            .bottom-section {
                grid-template-columns: 1fr;
                gap: 20px;
            }

            .invoice-header {
                flex-direction: column;
                gap: 6px;
            }

            .top-bar {
                flex-direction: column;
                gap: 10px;
            }
        }
    </style>
</head>

<body>

@php

    /*
    |--------------------------------------------------------------------------
    | Number To Words
    |--------------------------------------------------------------------------
    */

    $numberToWords = function ($number) use (&$numberToWords) {

        $number = round((float) $number, 2);

        $ones = [
            '',
            'One',
            'Two',
            'Three',
            'Four',
            'Five',
            'Six',
            'Seven',
            'Eight',
            'Nine',
            'Ten',
            'Eleven',
            'Twelve',
            'Thirteen',
            'Fourteen',
            'Fifteen',
            'Sixteen',
            'Seventeen',
            'Eighteen',
            'Nineteen'
        ];

        $tens = [
            '',
            '',
            'Twenty',
            'Thirty',
            'Forty',
            'Fifty',
            'Sixty',
            'Seventy',
            'Eighty',
            'Ninety'
        ];

        $convert = function ($n) use (&$convert, $ones, $tens) {

            if ($n < 20) {
                return $ones[$n];
            }

            if ($n < 100) {
                return $tens[intdiv($n, 10)] .
                    (($n % 10) ? ' ' . $ones[$n % 10] : '');
            }

            if ($n < 1000) {
                return $ones[intdiv($n, 100)] .
                    ' Hundred' .
                    (($n % 100) ? ' ' . $convert($n % 100) : '');
            }

            if ($n < 100000) {
                return $convert(intdiv($n, 1000)) .
                    ' Thousand' .
                    (($n % 1000) ? ' ' . $convert($n % 1000) : '');
            }

            if ($n < 10000000) {
                return $convert(intdiv($n, 100000)) .
                    ' Lakh' .
                    (($n % 100000) ? ' ' . $convert($n % 100000) : '');
            }

            return $convert(intdiv($n, 10000000)) .
                ' Crore' .
                (($n % 10000000) ? ' ' . $convert($n % 10000000) : '');
        };

        $rupees = (int) floor($number);
        $paise = (int) round(($number - $rupees) * 100);

        $result = '';

        if ($rupees > 0) {
            $result .= $convert($rupees) . ' Rupees';
        } else {
            $result .= 'Zero Rupees';
        }

        if ($paise > 0) {
            $result .= ' and ' . $convert($paise) . ' Paise';
        }

        return $result . ' Only';
    };

    /*
    |--------------------------------------------------------------------------
    | Values
    |--------------------------------------------------------------------------
    */

    $subtotal = (float) ($purchase->subtotal ?? 0);
    $totalTax = (float) ($purchase->total_tax ?? 0);
    $discount = (float) ($purchase->discount_amount ?? 0);
    $roundOff = (float) ($purchase->round_off ?? 0);
    $grandTotal = (float) ($purchase->grand_total ?? 0);
    $received = (float) ($purchase->received_amount ?? 0);
    $balance = (float) ($purchase->balance_amount ?? 0);

    $additionalCharges = $purchase->additionalCharges ?? collect();

@endphp


<!-- ========================================================= -->
<!-- TOP BAR -->
<!-- ========================================================= -->

<div class="top-bar">

    <div class="top-title">
        Create Purchase Invoice
    </div>

    <div class="actions">

        <button
    class="btn"
    onclick="window.location.href='{{ route('purchases') }}'">
     ✓ Done
</button>

        <button
            class="btn"
            onclick="window.print()">
            🖨 Print
        </button>

        <button
            class="btn"
            onclick="downloadInvoice()">
            ⬇ Download
        </button>

        <button
            class="btn btn-primary"
            onclick="shareInvoice()">
            ↗ Share
        </button>

    </div>

</div>


<div class="page-wrapper">

    <div class="invoice" id="invoice">


        <!-- ========================================================= -->
        <!-- BUSINESS DETAILS -->
        <!-- ========================================================= -->

        <div>

            <div class="business-name">
                {{ $business->business_name ?? 'Business Name' }}
            </div>

            @if(!empty($business->address))
                <div class="business-info">
                    {{ $business->address }}
                </div>
            @endif

            <div class="business-info">

                @if(!empty($business->mobile))
                    Mobile: {{ $business->mobile }}
                @endif

                @if(!empty($business->gstin))
                    &nbsp;&nbsp;&nbsp;
                    GSTIN: {{ $business->gstin }}
                @endif

            </div>

        </div>


        <!-- ========================================================= -->
        <!-- PURCHASE DETAILS -->
        <!-- ========================================================= -->

        <div class="invoice-header">

            <div>
                <strong>Purchase No.:</strong>
                {{ $purchase->purchase_number }}
            </div>

            <div>
                <strong>Purchase Date:</strong>
                {{ $purchase->purchase_date
                    ? \Carbon\Carbon::parse($purchase->purchase_date)->format('d/m/Y')
                    : '-' }}
            </div>

        </div>


        <!-- ========================================================= -->
        <!-- BILL FROM / SHIP FROM -->
        <!-- ========================================================= -->

        <div class="two-column">

            <div>

                <div class="section-title">
                    BILL FROM
                </div>

                <div class="section-value">

                    <strong>
                        {{ $purchase->party->party_name ?? 'Cash Purchase' }}
                    </strong>

                    @if(!empty($purchase->party->contact_number))
                        <br>
                        Mobile: {{ $purchase->party->contact_number }}
                    @endif

                    @if(!empty($purchase->place_of_supply))
                        <br>
                        Place of Supply:
                        {{ $purchase->place_of_supply }}
                    @endif

                </div>

            </div>


            <div>

                <div class="section-title">
                    SHIP FROM
                </div>

                <div class="section-value">

                    {{ $business->business_name ?? '-' }}

                    @if(!empty($business->address))
                        <br>
                        {{ $business->address }}
                    @endif

                </div>

            </div>

        </div>


        <!-- ========================================================= -->
        <!-- ITEMS -->
        <!-- ========================================================= -->

        <table>

            <thead>

                <tr>

                    <th style="width: 6%;">
                        NO.
                    </th>

                    <th style="width: 39%;">
                        ITEMS
                    </th>

                    <th style="width: 12%;">
                        HSN
                    </th>

                    <th class="text-center" style="width: 10%;">
                        QTY.
                    </th>

                    <th class="text-right" style="width: 12%;">
                        RATE
                    </th>

                    <th class="text-right" style="width: 10%;">
                        TAX
                    </th>

                    <th class="text-right" style="width: 14%;">
                        AMOUNT
                    </th>

                </tr>

            </thead>

            <tbody>

                @forelse($purchase->items as $index => $purchaseItem)

                    @php

                        $qty = (float) ($purchaseItem->qty ?? 0);
                        $price = (float) ($purchaseItem->price ?? 0);
                        $gstPercent = (float) ($purchaseItem->gst_percent ?? 0);
                        $gstAmount = (float) ($purchaseItem->gst_amount ?? 0);

                        $lineTotal = (float) (
                            $purchaseItem->line_total
                            ?? (($qty * $price) + $gstAmount)
                        );

                        $itemName =
                            $purchaseItem->description
                            ?? $purchaseItem->item->name
                            ?? 'Item';

                        $hsn =
                            $purchaseItem->hsn
                            ?? $purchaseItem->item->hsn_code
                            ?? '';

                    @endphp

                    <tr>

                        <td>
                            {{ $index + 1 }}
                        </td>

                        <td>

                            <div class="item-name">
                                {{ $itemName }}
                            </div>

                        </td>

                        <td>
                            {{ $hsn ?: '-' }}
                        </td>

                        <td class="text-center">
                            {{ rtrim(rtrim(number_format($qty, 2), '0'), '.') }}
                            {{ $purchaseItem->unit ?? '' }}
                        </td>

                        <td class="text-right">
                            ₹ {{ number_format($price, 2) }}
                        </td>

                        <td class="text-right">

                            @if($gstPercent > 0)

                                {{ rtrim(rtrim(number_format($gstPercent, 2), '0'), '.') }}%

                                <br>

                                ₹ {{ number_format($gstAmount, 2) }}

                            @else

                                -

                            @endif

                        </td>

                        <td class="text-right">
                            ₹ {{ number_format($lineTotal, 2) }}
                        </td>

                    </tr>

                @empty

                    <tr>

                        <td colspan="7" class="text-center">
                            No items found
                        </td>

                    </tr>

                @endforelse


                <!-- SUBTOTAL -->

                <tr class="subtotal-row">

                    <td colspan="5">
                        SUBTOTAL
                    </td>

                    <td class="text-right">
                        -
                    </td>

                    <td class="text-right">
                        ₹ {{ number_format($subtotal, 2) }}
                    </td>

                </tr>

            </tbody>

        </table>


        <!-- ========================================================= -->
        <!-- BOTTOM -->
        <!-- ========================================================= -->

        <div class="bottom-section">


            <!-- ===================================================== -->
            <!-- TERMS -->
            <!-- ===================================================== -->

            <div class="terms">

                <div class="terms-title">
                    TERMS AND CONDITIONS
                </div>

                @if(!empty($purchase->notes))

                    {!! nl2br(e($purchase->notes)) !!}

                @else

                    1. Goods once sold will not be taken back or exchanged
                    <br>
                    2. All disputes are subject to jurisdiction only

                @endif

            </div>


            <!-- ===================================================== -->
            <!-- TOTALS -->
            <!-- ===================================================== -->

            <div class="totals">


                <!-- ADDITIONAL CHARGES -->

                @foreach($additionalCharges as $charge)

                    <div class="total-line">

                        <span>
                            {{ $charge->name }}
                        </span>

                        <span>
                            ₹ {{ number_format((float) $charge->amount, 2) }}
                        </span>

                    </div>

                @endforeach


                <!-- TAX -->

                @if($totalTax != 0)

                    <div class="total-line">

                        <span>
                            Tax
                        </span>

                        <span>
                            ₹ {{ number_format($totalTax, 2) }}
                        </span>

                    </div>

                @endif


                <!-- DISCOUNT -->

                @if($discount > 0)

                    <div class="total-line discount">

                        <span>
                            Discount
                        </span>

                        <span>
                            - ₹ {{ number_format($discount, 2) }}
                        </span>

                    </div>

                @endif


                <!-- ROUND OFF -->

                @if(abs($roundOff) >= 0.01)

                    <div class="total-line">

                        <span>
                            Round Off
                        </span>

                        <span>
                            {{ $roundOff < 0 ? '- ' : '' }}
                            ₹ {{ number_format(abs($roundOff), 2) }}
                        </span>

                    </div>

                @endif


                <!-- GRAND TOTAL -->

                <div class="total-line grand">

                    <span>
                        Total Amount
                    </span>

                    <span>
                        ₹ {{ number_format($grandTotal, 2) }}
                    </span>

                </div>


                <!-- PAID -->

                <div class="total-line">

                    <span>
                        Paid Amount
                    </span>

                    <span>
                        ₹ {{ number_format($received, 2) }}
                    </span>

                </div>


                <!-- BALANCE -->

                <div class="total-line balance">

                    <span>
                        Balance
                    </span>

                    <span class="{{ $balance < 0 ? 'negative' : 'positive' }}">

                        @if($balance < 0)

                            - ₹ {{ number_format(abs($balance), 2) }}

                        @else

                            ₹ {{ number_format($balance, 2) }}

                        @endif

                    </span>

                </div>

            </div>

        </div>


        <!-- ========================================================= -->
        <!-- AMOUNT IN WORDS -->
        <!-- ========================================================= -->

        <div class="words">

            <div class="words-title">
                Total Amount (in words)
            </div>

            <div>
                {{ $numberToWords($grandTotal) }}
            </div>

        </div>


        <!-- ========================================================= -->
        <!-- FOOTER -->
        <!-- ========================================================= -->

        <div class="footer">

            Purchase invoice generated from Billnika

        </div>


    </div>

</div>


<script>

    function downloadInvoice() {

        /*
         * Browser print dialog allows:
         * Save as PDF
         */

        window.print();

    }


    async function shareInvoice() {

        const url = window.location.href;

        if (navigator.share) {

            try {

                await navigator.share({
                    title: 'Purchase Invoice',
                    text: 'Purchase Invoice {{ $purchase->purchase_number }}',
                    url: url
                });

            } catch (error) {

                console.log('Share cancelled');

            }

        } else {

            try {

                await navigator.clipboard.writeText(url);

                alert('Invoice link copied.');

            } catch (error) {

                alert('Unable to share invoice.');

            }

        }

    }

</script>

</body>
</html>