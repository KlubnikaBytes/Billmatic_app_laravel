<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1">

    <title>
        Invoice {{ $invoice->invoice_number }}
    </title>

    <script src="https://cdn.tailwindcss.com"></script>

    <style>

        body {
            font-family: Arial, Helvetica, sans-serif;
            background: #f3f4f6;
        }

        .invoice-page {
            width: 794px;
            min-height: 1123px;
            margin: 30px auto;
            background: white;
            padding: 42px 48px;
            box-sizing: border-box;
        }

        @media print {

            body {
                background: white;
            }

            .no-print {
                display: none !important;
            }

            .invoice-page {
                width: 100%;
                min-height: auto;
                margin: 0;
                padding: 30px 40px;
            }

        }

    </style>

</head>


<body>

@php

    $grandTotal = round(
        (float) ($invoice->grand_total ?? 0),
        2
    );

    $receivedAmount = round(
        (float) ($invoice->received_amount ?? 0),
        2
    );

    // Grand Total - Received Amount
    $calculatedBalance = round(
        $grandTotal - $receivedAmount,
        2
    );

@endphp

<!-- =====================================================
     TOP ACTION BAR
===================================================== -->

<div class="no-print bg-white border-b shadow-sm">

    <div class="max-w-5xl mx-auto px-5 py-4
                flex items-center justify-between">

        <div>

            <h1 class="text-lg font-semibold">
                Invoice Preview
            </h1>

            <p class="text-sm text-gray-500">
                {{ $invoice->invoice_number }}
            </p>

        </div>


        <div class="flex gap-3">

            <button
                onclick="window.print()"
                class="px-5 py-2 rounded-lg
                       bg-indigo-600 text-white
                       hover:bg-indigo-700">

                🖨 Print

            </button>


            <button
                onclick="downloadInvoice()"
                class="px-5 py-2 rounded-lg
                       border border-gray-300
                       hover:bg-gray-100">

                ⬇ Download

            </button>


            <button
                onclick="shareInvoice()"
                class="px-5 py-2 rounded-lg
                       border border-gray-300
                       hover:bg-gray-100">

                ↗ Share

            </button>


           <button
    type="button"
    onclick="window.location.href='{{ route('dashboard') }}'"
    class="px-5 py-2 rounded-lg
           border border-gray-300
           hover:bg-gray-100">

    ✓ Done

</button>

        </div>

    </div>

</div>



<!-- =====================================================
     INVOICE
===================================================== -->

<div class="invoice-page" id="invoiceDocument">


    <!-- =================================================
         HEADER
    ================================================= -->

    <div class="flex justify-between items-start">

        <div>

            <div class="text-sm font-semibold
                        tracking-wide">

                {{ strtoupper($business->industry ?? 'BUSINESS') }}

            </div>


            <h1 class="text-2xl font-bold mt-4">

                {{ $business->business_name
                    ?? $business->name
                    ?? 'Business Name' }}

            </h1>


            @if($business)

                <div class="text-sm text-gray-700 mt-2">

                    @if($business->address)
                        {{ $business->address }}
                    @endif

                    @if($business->city)
                        <br>
                        {{ $business->city }}
                    @endif

                </div>


                @if($business->mobile)

                    <div class="text-sm mt-1">

                        Mobile:
                        {{ $business->mobile }}

                    </div>

                @endif


                @if($business->gst_number)

                    <div class="text-sm mt-1">

                        GSTIN:
                        {{ $business->gst_number }}

                    </div>

                @endif


                @if($business->pan_cin)

                    <div class="text-sm mt-1">

                        PAN Number:
                        {{ $business->pan_cin }}

                    </div>

                @endif

            @endif

        </div>


        <div class="text-right">

            <div class="text-2xl font-bold">

                INVOICE

            </div>

            <div class="text-sm text-gray-500 mt-2">

                ORIGINAL FOR RECIPIENT

            </div>

        </div>

    </div>



    <!-- =================================================
         BLACK LINE
    ================================================= -->

    <div class="border-t-4 border-black mt-5"></div>



    <!-- =================================================
         INVOICE DETAILS
    ================================================= -->

    <div class="bg-gray-100 px-4 py-3
                flex justify-between text-sm">

        <div>

            <span class="font-semibold">
                Invoice No.:
            </span>

            {{ $invoice->invoice_number }}

        </div>


        <div>

            <span class="font-semibold">
                Invoice Date:
            </span>

            {{ \Carbon\Carbon::parse(
                $invoice->invoice_date
            )->format('d/m/Y') }}

        </div>

    </div>



    <!-- =================================================
         BILL TO / SHIP TO
    ================================================= -->

    <div class="grid grid-cols-2 gap-10 mt-5">

        <!-- BILL TO -->

        <div>

            <h3 class="font-semibold text-sm">
                BILL TO
            </h3>


            <div class="mt-2 text-sm">

                <div class="font-bold">

                    {{ $invoice->party->party_name
                        ?? $invoice->party->name
                        ?? 'Cash Sale' }}

                </div>


                @if($invoice->party)

                    @if($invoice->party->contact_number)

                        <div>
                            Mobile:
                            {{ $invoice->party->contact_number }}
                        </div>

                    @endif


                    @if($invoice->party->gst_number)

                        <div>
                            GSTIN:
                            {{ $invoice->party->gst_number }}
                        </div>

                    @endif


                    @if($invoice->party->billing_address)

                        <div>
                            {{ $invoice->party->billing_address }}
                        </div>

                    @endif

                @endif


                @if($invoice->place_of_supply)

                    <div class="mt-1">

                        Place of Supply:
                        {{ $invoice->place_of_supply }}

                    </div>

                @endif

            </div>

        </div>


        <!-- SHIP TO -->

        <div>

            <h3 class="font-semibold text-sm">
                SHIP TO
            </h3>


            <div class="mt-2 text-sm">

                <div class="font-bold">

                    {{ $invoice->party->party_name
                        ?? $invoice->party->name
                        ?? 'Business Name' }}

                </div>


                @if($invoice->party)

                    @if($invoice->party->billing_address)

                        <div>
                            {{ $invoice->party->billing_address }}
                        </div>

                    @endif

                    @if($invoice->place_of_supply)

                        <div>
                            {{ $invoice->place_of_supply }}
                        </div>

                    @endif

                @endif

            </div>

        </div>

    </div>



    <!-- =================================================
         ITEMS TABLE
    ================================================= -->

    <div class="mt-5">

        <table class="w-full text-sm">

            <thead>

                <tr class="border-t-2 border-b-2 border-black">

                    <th class="text-left py-3">
                        ITEMS
                    </th>

                    <th class="text-left py-3">
                        HSN
                    </th>

                    <th class="text-right py-3">
                        QTY.
                    </th>

                    <th class="text-right py-3">
                        RATE
                    </th>

                    <th class="text-right py-3">
                        DISCOUNT
                    </th>

                    <th class="text-right py-3">
                        TAX
                    </th>

                    <th class="text-right py-3">
                        AMOUNT
                    </th>

                </tr>

            </thead>


            <tbody>

                @foreach($invoice->items as $row)

                    <tr class="border-b">

                        <td class="py-3">

                            <div class="font-medium">

                                {{ $row->item->name
                                    ?? $row->description
                                    ?? '' }}

                            </div>


                            @if($row->description)

                                <div class="text-xs text-gray-500">

                                    {{ $row->description }}

                                </div>

                            @endif

                        </td>


                        <td class="py-3">

                            {{ $row->item->hsn_code
                                ?? '' }}

                        </td>


                        <td class="py-3 text-right">

                            {{ $row->qty }}

                            {{ $row->unit ?? 'PCS' }}

                        </td>


                        <td class="py-3 text-right">

                            ₹ {{ number_format(
                                $row->price,
                                2
                            ) }}

                        </td>


                        <td class="py-3 text-right">

                            ₹ {{ number_format(
                                $row->discount ?? 0,
                                2
                            ) }}

                        </td>


                        <td class="py-3 text-right">

                            {{ $row->gst_percent ?? 0 }}%

                            <div class="text-xs text-gray-500">

                                ₹ {{ number_format(
                                    $row->gst_amount ?? 0,
                                    2
                                ) }}

                            </div>

                        </td>


                        <td class="py-3 text-right font-medium">

                            ₹ {{ number_format(
                                $row->line_total,
                                2
                            ) }}

                        </td>

                    </tr>

                @endforeach

            </tbody>

        </table>

    </div>



    <!-- =================================================
         SUMMARY
    ================================================= -->

    <div class="grid grid-cols-2 mt-8">

        <!-- TERMS -->

        <div class="pr-8">

            @if($invoice->notes)

                <div class="font-semibold text-sm mb-2">

                    TERMS AND CONDITIONS

                </div>

                <div class="text-xs whitespace-pre-line">

                    {{ $invoice->notes }}

                </div>

            @else

                <div class="font-semibold text-sm mb-2">

                    TERMS AND CONDITIONS

                </div>

                <div class="text-xs">

                    1. Goods once sold will not be taken back or exchanged.

                    <br>

                    2. All disputes are subject to
                    jurisdiction only.

                </div>

            @endif

        </div>



        <!-- TOTALS -->

        <div>

            <div class="flex justify-between py-1">

                <span>
                    Subtotal
                </span>

                <span>
                    ₹ {{ number_format(
                        $invoice->subtotal,
                        2
                    ) }}
                </span>

            </div>


            @if($invoice->total_tax > 0)

                <div class="flex justify-between py-1">

                    <span>
                        Tax
                    </span>

                    <span>
                        ₹ {{ number_format(
                            $invoice->total_tax,
                            2
                        ) }}
                    </span>

                </div>

            @endif



            <!-- ADDITIONAL CHARGES -->

            @foreach($invoice->additionalCharges as $charge)

                <div class="flex justify-between py-1">

                    <span>
                        {{ $charge->name }}
                    </span>

                    <span>
                        ₹ {{ number_format(
                            $charge->amount,
                            2
                        ) }}
                    </span>

                </div>

            @endforeach



            <!-- DISCOUNT -->

            @if(($invoice->discount_amount ?? 0) > 0)

                <div class="flex justify-between py-1">

                    <span>

                        Discount

                        @if(($invoice->discount_percent ?? 0) > 0)

                            ({{ $invoice->discount_percent }}%)

                        @endif

                    </span>

                    <span>

                        - ₹ {{ number_format(
                            $invoice->discount_amount,
                            2
                        ) }}

                    </span>

                </div>

            @endif



            <!-- TCS -->

            @if(($invoice->tcs_amount ?? 0) > 0)

                <div class="flex justify-between py-1">

                    <span>
                        TCS
                    </span>

                    <span>
                        ₹ {{ number_format(
                            $invoice->tcs_amount,
                            2
                        ) }}
                    </span>

                </div>

            @endif



            <!-- ROUND OFF -->

            @if(($invoice->round_off ?? 0) != 0)

                <div class="flex justify-between py-1">

                    <span>
                        Round Off
                    </span>

                    <span>

                        {{ $invoice->round_off > 0 ? '+' : '-' }}

                        ₹ {{ number_format(
                            abs($invoice->round_off),
                            2
                        ) }}

                    </span>

                </div>

            @endif



            <!-- TOTAL -->

            <div class="border-t-2 border-black
                        mt-3 pt-3
                        flex justify-between
                        font-bold text-lg">

                <span>
                    Total Amount
                </span>

                <span>
                    ₹ {{ number_format(
                        $invoice->grand_total,
                        2
                    ) }}
                </span>

            </div>



            <!-- RECEIVED -->

            <div class="flex justify-between mt-4">

                <span>
                    Received Amount
                </span>

                <span>

                    ₹ {{ number_format(
                        $invoice->received_amount ?? 0,
                        2
                    ) }}

                </span>

            </div>



           <!-- BALANCE -->

<div class="flex justify-between mt-2 font-semibold">

    <span>
        Balance
    </span>

    <span>
    @if($calculatedBalance < 0)
        -₹ {{ number_format(abs($calculatedBalance), 2) }}
    @else
        ₹ {{ number_format($calculatedBalance, 2) }}
    @endif
</span>
</div>

            </div>

        </div>

    </div>



    <!-- =================================================
         AMOUNT IN WORDS
    ================================================= -->

    <div class="border-t mt-8 pt-4">

        <div class="text-sm">

            <span class="font-semibold">
                Total Amount (in words):
            </span>

            {{ \App\Helpers\NumberToWords::convert(
                $invoice->grand_total
            ) }}

            Rupees Only

        </div>

    </div>



    <!-- =================================================
         PAYMENT INFORMATION
    ================================================= -->

    @if($invoice->payment_mode)

        <div class="mt-4 text-sm">

            <span class="font-semibold">
                Payment Mode:
            </span>

            {{ $invoice->payment_mode }}

        </div>

    @endif



    <!-- =================================================
         DUE DATE
    ================================================= -->

    @if($invoice->due_date)

        <div class="mt-2 text-sm">

            <span class="font-semibold">
                Due Date:
            </span>

            {{ \Carbon\Carbon::parse(
                $invoice->due_date
            )->format('d/m/Y') }}

        </div>

    @endif



    <!-- =================================================
         FOOTER
    ================================================= -->

    <div class="mt-12 pt-4 border-t
                flex justify-between text-xs text-gray-500">

        <span>
            Thank you for your business.
        </span>

        <span>

            {{ $invoice->invoice_number }}

        </span>

    </div>


</div>



<!-- =====================================================
     JAVASCRIPT
===================================================== -->

<script>

function downloadInvoice()
{
    window.print();
}


async function shareInvoice()
{
    const url = window.location.href;

    if (navigator.share) {

        try {

            await navigator.share({
                title: 'Invoice {{ $invoice->invoice_number }}',
                text: 'Invoice {{ $invoice->invoice_number }}',
                url: url
            });

        } catch (error) {

            console.log(error);

        }

    } else {

        await navigator.clipboard.writeText(url);

        alert('Invoice link copied.');

    }
}

</script>


</body>

</html>