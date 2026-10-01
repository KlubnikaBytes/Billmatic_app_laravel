<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">

    <title>Create Sales Invoice | BillNika</title>

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
        }

        input:focus,
        textarea:focus,
        select:focus {
            outline: none;
            box-shadow: 0 0 0 2px rgba(99, 102, 241, 0.15);
        }

        .signature-canvas {
            touch-action: none;
        }
    </style>
</head>

<body class="bg-gray-100 text-gray-800">

<!-- ================= JWT PROTECTION ================= -->

<script>
    if (!localStorage.getItem('token')) {
        window.location.href = '/start-free-billing';
    }
</script>


<!-- ================= TOP BAR ================= -->

<header class="h-20 bg-white border-b shadow-sm flex items-center justify-between px-6">

    <div class="flex items-center gap-4">

        <a href="{{ route('dashboard') }}"
           class="border border-gray-300 px-5 py-2 rounded-md hover:bg-gray-100">
            ← Exit
        </a>

        <h1 class="text-lg font-semibold">
            Create Sales Invoice
        </h1>

    </div>


    <div class="flex items-center gap-3">

        <!-- <button
            class="px-5 py-2 rounded-lg text-indigo-600 bg-indigo-50 font-medium">
            📝 Edit Mode
        </button>

        <button
            class="px-5 py-2 rounded-lg text-gray-400 bg-gray-100">
            👁 Preview Mode
        </button>

        <button
            class="border border-gray-300 px-3 py-2 rounded-lg">
            ⚙
        </button>

        <button
            class="bg-gray-100 text-gray-400 px-4 py-2 rounded-lg">
            Save & New
        </button> -->

        <!-- <button
            class="bg-indigo-100 text-indigo-500 px-5 py-2 rounded-lg font-medium">
            Save
        </button> -->
        <button
    type="button"
    id="saveInvoiceButton"
    onclick="saveInvoice()"
    class="bg-indigo-600 text-white px-5 py-2 rounded-lg font-medium">
    Save
</button>

    </div>

</header>


<!-- ================= MAIN ================= -->

<main class="p-2">


    <!-- ================= BILL TO + INVOICE DETAILS ================= -->

    <div class="grid grid-cols-1 lg:grid-cols-5 bg-white border-b">


        <!-- ================= BILL TO ================= -->

        <section class="lg:col-span-3 border-r">

            <div class="px-5 py-3 border-b flex justify-between">

                <span class="text-sm text-gray-500">
                    Bill To
                </span>

                <button class="border rounded-lg px-3 py-1">
                    ⚙
                </button>

            </div>


            <div class="p-5">


                <button
    type="button"
    onclick="openPartySelector()"
    class="w-52 h-36 border-2 border-dashed border-blue-400
           text-blue-500 hover:bg-blue-50
           flex items-center justify-center rounded-lg">

    + Add Party

</button>

            </div>

        </section>


    
        <!-- ================= INVOICE DETAILS ================= -->

<section class="lg:col-span-2">

    <!-- HEADER -->
    <div class="px-5 py-3 border-b flex justify-between items-center">

        <span class="text-sm text-gray-500">
            Invoice Details
        </span>

        <div class="flex items-center gap-3">

            <!-- Repeat Invoice -->
            <!-- <label class="flex items-center gap-2 text-sm text-gray-600">
                <input
                    type="checkbox"
                    id="repeatInvoice"
                    class="w-4 h-4">
                Repeat this Invoice
            </label> -->

            <!-- Settings -->
            <!-- <button
                type="button"
                id="invoiceSettingsBtn"
                class="border rounded-lg px-3 py-1 hover:bg-gray-50">
                ⚙
            </button> -->

        </div>

    </div>


    <!-- DETAILS BODY -->
    <div class="p-5">

        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

            <!-- SALES INVOICE NO -->
            <!-- <div>

                <label class="block text-sm text-gray-500 mb-2">
                    Sales Invoice No.
                </label>

                <input
                    type="text"
                    id="invoiceNumber"
                    class="w-full border rounded-lg px-4 py-3
                           focus:outline-none focus:ring-2
                           focus:ring-blue-400"
                    placeholder="Invoice Number">

            </div> -->


            <!-- SALES INVOICE DATE -->
            <!-- <div>

                <label class="block text-sm text-gray-500 mb-2">
                    Sales Invoice Date
                </label>

                <input
                    type="date"
                    id="invoiceDate"
                    class="w-full border rounded-lg px-4 py-3
                           focus:outline-none focus:ring-2
                           focus:ring-blue-400">

            </div> -->

            <!-- SALES INVOICE NO -->
<div>
    <label class="block text-sm text-gray-500 mb-2">
        Sales Invoice No.
    </label>

    <input
        type="text"
        id="invoiceNumber"
        class="w-full border rounded-lg px-4 py-3"
        placeholder="Invoice Number"
        value="INV-31">
</div>


<!-- SALES INVOICE DATE -->
<div>
    <label class="block text-sm text-gray-500 mb-2">
        Sales Invoice Date
    </label>

    <input
        type="date"
        id="invoiceDate"
        value="{{ date('Y-m-d') }}"
        class="w-full border rounded-lg px-4 py-3">
</div>

        </div>


        <!-- DUE DATE -->
        <button
            type="button"
            id="addDueDateBtn"
            class="mt-5 w-full border-2 border-dashed
                   border-blue-400 text-blue-500
                   py-3 rounded-lg hover:bg-blue-50">

            + Add Due Date

        </button>


        <!-- DUE DATE INPUT - HIDDEN INITIALLY -->
        <div
            id="dueDateContainer"
            class="mt-4 hidden">

            <label class="block text-sm text-gray-500 mb-2">
                Due Date
            </label>

            <div class="flex gap-2">

                <input
                    type="date"
                    id="dueDate"
                    class="flex-1 border rounded-lg px-4 py-3
                           focus:outline-none focus:ring-2
                           focus:ring-blue-400">

                <button
                    type="button"
                    id="removeDueDateBtn"
                    class="px-4 border rounded-lg text-red-500
                           hover:bg-red-50">
                    ✕
                </button>

            </div>

        </div>

    </div>

</section>

    </div>


    <!-- ================= ITEMS TABLE ================= -->

    <section class="bg-white mt-1">

        <div class="overflow-x-auto">

            <table class="w-full text-sm">

                <thead class="bg-gray-50 border-b">

                    <tr>

                        <th class="px-4 py-4 text-left w-16">
                            NO
                        </th>

                        <th class="px-4 py-4 text-left">
                            ITEMS
                        </th>

                        <th class="px-4 py-4 text-left">
                            HSN
                        </th>

                        <th class="px-4 py-4 text-left">
                            QTY
                        </th>

                        <th class="px-4 py-4 text-left">
                            PRICE/ITEM (₹)
                        </th>

                        <!-- <th class="px-4 py-4 text-left">
                            DISCOUNT
                        </th> -->

                        <th class="px-4 py-4 text-left">
                            TAX
                        </th>

                        <th class="px-4 py-4 text-left">
                            AMOUNT (₹)
                        </th>

                    </tr>

                </thead>


                <!-- <tbody>

                    <tr>

                        <td colspan="8" class="p-3">

                            <button
    type="button"
    onclick="openItemSelector()"
    class="w-full border-2 border-dashed
           border-blue-400 text-blue-500
           py-3 rounded-lg hover:bg-blue-50">

    + Add Item

</button>

                        </td>

                    </tr>

                </tbody> -->

                <tbody id="invoiceTableBody">

    <!-- Invoice items will be added here dynamically -->

    <tr id="emptyInvoiceRow">

        <td colspan="7" class="p-3">

            <button
                type="button"
                onclick="openItemSelector()"
                class="w-full border-2 border-dashed
                       border-blue-400 text-blue-500
                       py-3 rounded-lg hover:bg-blue-50">

                + Add Item

            </button>

        </td>

    </tr>

</tbody>


                <!-- <tfoot>

                    <tr class="border-t bg-gray-50">

                        <td colspan="4"></td>

                        <td colspan="2"
                            class="px-4 py-4 text-right font-semibold">

                            Subtotal

                        </td>

                        <td class="px-4 py-4">
                            ₹ 0
                        </td>

                        <td class="px-4 py-4 font-semibold">
                            ₹ 0
                        </td>

                    </tr>

                </tfoot> -->

                <tfoot>

    <tr class="border-t bg-gray-50">

        <td colspan="4"></td>

        <td colspan="1"
            class="px-4 py-4 text-right font-semibold">

            Subtotal

        </td>

        <td class="px-4 py-4">
            ₹ <span id="invoiceSubtotal">0.00</span>
        </td>

        <td class="px-4 py-4 font-semibold">
            ₹ <span id="invoiceTotal">0.00</span>
        </td>

    </tr>

</tfoot>

            </table>

        </div>

    </section>


    <!-- ================= BOTTOM SECTION ================= -->

    <div class="grid grid-cols-1 lg:grid-cols-5 bg-white mt-1">


        <!-- ================= LEFT ================= -->

        <section class="lg:col-span-3 border-r">


            <!-- ================= NOTES ================= -->

<div class="border-b">

    <!-- ADD NOTES BUTTON -->
    <button
        type="button"
        id="addNotesBtn"
        onclick="toggleNotes()"
        class="px-5 py-4 text-blue-500 font-medium">

        📝 Add Notes

    </button>

    <!-- NOTES INPUT -->
    <div
        id="notesContainer"
        class="hidden px-5 pb-4">

        <div class="flex justify-between items-center mb-2">

            <span class="text-sm text-gray-500">
                Notes
            </span>

            <button
                type="button"
                onclick="removeNotes()"
                class="text-gray-400 hover:text-red-500 text-lg">

                ×

            </button>

        </div>

        <textarea
            id="invoiceNotes"
            rows="3"
            placeholder="Enter notes"
            class="w-full border rounded-lg p-3 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400"></textarea>

    </div>

</div>


            <!-- TERMS -->

            <div class="p-5">

                <div class="flex justify-between mb-2">

                    <span class="text-sm text-gray-500">
                        Terms & Conditions
                    </span>

                    <button class="text-gray-400">
                        ×
                    </button>

                </div>


                <textarea
                    rows="3"
                    class="w-full border rounded-lg p-3 text-sm">1. Goods once sold will not be taken back or exchanged
2. All disputes are subject to [ENTER_YOUR_CITY_NAME] jurisdiction only</textarea>

            </div>


            <!-- BANK -->

            <div class="border-t">

                <button
                    class="px-5 py-4 text-blue-500 font-medium">

                    🏦 Add Bank Account

                </button>

            </div>


            <!-- PAYMENT QR -->

            <div class="border-t">

                <button
                    class="px-5 py-4 text-blue-500 font-medium">

                    ▦ Add Payment QR

                </button>

            </div>

        </section>


        <!-- ================= RIGHT ================= -->

        <section class="lg:col-span-2 p-5">


          <!-- ===================================================== -->
<!-- INVOICE ADJUSTMENTS -->
<!-- ===================================================== -->

<div class="space-y-2">

    <!-- ================= ADDITIONAL CHARGES ================= -->

    <button
        type="button"
        onclick="toggleAdditionalCharges()"
        class="text-blue-500 font-medium">

        ₹ Add Additional Charges

    </button>


    <!-- ADDITIONAL CHARGES CONTAINER -->

    <div
        id="additionalChargesContainer"
        class="hidden space-y-3 mt-3">

        <!-- Charges will be added here -->

    </div>


    <!-- ================= TAXABLE AMOUNT ================= -->

    <div class="flex justify-between py-3 border-t">

        <span>
            Taxable Amount
        </span>

        <span id="taxableAmountDisplay">
            ₹ 0.00
        </span>

    </div>


    <!-- ================= DISCOUNT ================= -->

    <button
        type="button"
        onclick="toggleDiscount()"
        class="text-blue-500 font-medium py-2">

        ◉ Add Discount

    </button>


    <!-- DISCOUNT BOX -->

    <div
        id="discountContainer"
        class="hidden mt-2">

        <div class="flex items-center gap-3">

            <input
                id="invoiceDiscountPercent"
                type="number"
                min="0"
                max="100"
                step="0.01"
                value="0"
                oninput="updateInvoiceTotals()"
                class="border rounded-lg px-3 py-2 w-28">

            <span>
                %
            </span>

            <span class="text-gray-500">
                Discount
            </span>

            <span
                id="discountAmountDisplay"
                class="ml-auto text-gray-600">

                ₹ 0.00

            </span>

        </div>

    </div>


    <!-- ================= TCS ================= -->

    <!-- <div class="flex items-center gap-3 py-3">

        <input
            id="applyTcs"
            type="checkbox"
            class="w-5 h-5"
            onchange="updateInvoiceTotals()">

        <label for="applyTcs">
            Apply TCS
        </label>

    </div> -->


    <!-- TCS -->

    <!-- <div
        id="tcsContainer"
        class="hidden">

        <div class="flex items-center gap-3">

            <input
                id="tcsPercent"
                type="number"
                min="0"
                step="0.01"
                value="0"
                oninput="updateInvoiceTotals()"
                class="border rounded-lg px-3 py-2 w-28">

            <span>
                %
            </span>

            <span
                id="tcsAmountDisplay"
                class="ml-auto text-gray-600">

                ₹ 0.00

            </span>

        </div>

    </div> -->


    <!-- ================= AUTO ROUND OFF =================

    <div class="flex items-center gap-3 py-3">

        <input
            id="autoRoundOff"
            type="checkbox"
            class="w-5 h-5"
            onchange="updateInvoiceTotals()">

        <label for="autoRoundOff">
            Auto Round Off
        </label>

        <select
            id="roundOffType"
            onchange="updateInvoiceTotals()"
            class="border rounded-lg px-2 py-2">

            <option value="add">
                + Add
            </option>

            <option value="less">
                - Less
            </option>

        </select>

        <input
            id="roundOffAmount"
            type="number"
            min="0"
            step="0.01"
            value="0"
            oninput="updateInvoiceTotals()"
            class="border rounded-lg px-3 py-2 w-24">

    </div> -->

   <!-- ================= AUTO ROUND OFF ================= -->

<div class="flex items-center gap-3 py-3">

    <input
        id="autoRoundOff"
        type="checkbox"
        class="w-5 h-5"
        onchange="updateInvoiceTotals()">

    <span>
        Auto Round Off
    </span>

    <select
        id="roundOffType"
        class="border rounded px-2 py-1"
        onchange="updateInvoiceTotals()">

        <option value="add">+ Add</option>
        <option value="less">- Less</option>

    </select>

    <input
        id="roundOffAmount"
        type="number"
        value="0"
        min="0"
        step="0.01"
        class="border rounded px-3 py-1 w-24"
        oninput="updateInvoiceTotals()">

</div>


    <!-- ================= TOTAL ================= -->

    <div class="border-t mt-4 pt-5">

        <div class="flex justify-between items-center">

            <span class="text-xl font-semibold">
                Total Amount:
            </span>

            <div class="flex">

                <span
                    class="border border-r-0
                           rounded-l-lg px-4 py-3 bg-gray-50">

                    ₹

                </span>

                <input
                    id="totalAmount"
                    type="number"
                    value="0"
                    readonly
                    class="border rounded-r-lg px-4 py-3 w-64 font-semibold"
                >

            </div>

        </div>


        <!-- RECEIVED -->

        <div class="flex justify-between mt-5">

            <span>
                Total Amount Received
            </span>

            <span id="receivedDisplay">
                ₹ 0.00
            </span>

        </div>


        <div class="flex mt-2">

            <span
                class="border border-r-0
                       rounded-l-lg px-4 py-3 bg-gray-50">

                ₹

            </span>

            <input
                id="receivedAmount"
                type="number"
                value="0"
                class="border px-4 py-3 flex-1"
                oninput="calculateBalance()">

            <select
                id="paymentMode"
                class="border rounded-r-lg px-4 py-3 bg-white">

                <option value="Cash">
                    Cash
                </option>

                <option value="Bank">
                    Bank
                </option>

                <option value="UPI">
                    UPI
                </option>

                <option value="Card">
                    Card
                </option>

            </select>

        </div>


        <!-- FULLY PAID -->

        <div class="flex items-center gap-3 mt-5">

            <input
                id="fullyPaid"
                type="checkbox"
                class="w-5 h-5"
                onchange="markFullyPaid()">

            <label
                for="fullyPaid"
                class="font-medium cursor-pointer">

                Mark as fully paid

            </label>

        </div>


        <!-- BALANCE -->

        <div
            class="flex justify-between items-center
                   border-t mt-5 pt-4">

            <span
                class="text-green-600 font-semibold">

                Balance Amount

            </span>

            <span
                id="balanceAmount"
                class="text-green-600 font-semibold">

                ₹ 0.00

            </span>

        </div>

    </div>

</div>


            <!-- ================= TOTAL ================= -->

            <!-- <div class="border-t mt-4 pt-5">


                <div class="flex justify-between items-center">

                    <span class="text-xl font-semibold">
                        Total Amount:
                    </span>


                    <div class="flex">

                        <span
                            class="border border-r-0
                                   rounded-l-lg px-4 py-3 bg-gray-50">

                            ₹

                        </span>

                        <input
                            id="totalAmount"
                            type="number"
                            value="0"
                            class="border rounded-r-lg px-4 py-3 w-64"
                            oninput="calculateBalance()">

                    </div>

                </div> -->


                <!-- ================= AMOUNT RECEIVED ================= -->

                <!-- <div class="flex justify-between mt-5">

                    <span>
                        Total Amount Received
                    </span>

                    <span id="receivedDisplay">
                        ₹ 0
                    </span>

                </div>


                <div class="flex mt-2">

                    <span
                        class="border border-r-0 rounded-l-lg
                               px-4 py-3 bg-gray-50">

                        ₹

                    </span>

                    <input
                        id="receivedAmount"
                        type="number"
                        value="0"
                        class="border px-4 py-3 flex-1"
                        oninput="calculateBalance()">

                    <select
                        class="border rounded-r-lg px-4 py-3 bg-white">

                        <option>Cash</option>
                        <option>Bank</option>
                        <option>UPI</option>
                        <option>Card</option>

                    </select>

                </div> -->


                <!-- ================= MARK FULLY PAID ================= -->

                <!-- <div class="flex items-center gap-3 mt-5">

                    <input
                        id="fullyPaid"
                        type="checkbox"
                        class="w-5 h-5"
                        onchange="markFullyPaid()">

                    <label
                        for="fullyPaid"
                        class="font-medium cursor-pointer">

                        Mark as fully paid

                    </label>

                </div> -->


                <!-- ================= BALANCE AMOUNT ================= -->

                <!-- <div
                    class="flex justify-between items-center
                           border-t mt-5 pt-4">

                    <span
                        class="text-green-600 font-semibold">

                        Balance Amount

                    </span>

                    <span
                        id="balanceAmount"
                        class="text-green-600 font-semibold">

                        ₹ 0

                    </span>

                </div> -->


                <!-- ================= SIGNATURE ================= -->

                <div class="border-t mt-5 pt-5">

                    <div class="flex justify-end">

                        <button
                            type="button"
                            onclick="openSignature()"
                            class="w-64 h-28 border-2 border-dashed
                                   border-blue-400 text-blue-500
                                   rounded-lg hover:bg-blue-50
                                   flex items-center justify-center">

                            ✍️ + Add Signature

                        </button>

                    </div>

                </div>

            </div>

        </section>

    </div>

</main>

<!-- ===================================================== -->
<!-- PARTY SELECTOR MODAL -->
<!-- ===================================================== -->

<div
    id="partySelectorModal"
    class="hidden fixed inset-0 bg-black/40 z-40 flex items-start justify-center pt-24">

    <div class="bg-white rounded-xl shadow-xl w-full max-w-4xl mx-4">

        <!-- SEARCH -->
        <div class="p-4 border-b">

            <div class="flex items-center gap-3">

                <input
                    type="text"
                    id="partySearch"
                    placeholder="Search party by name or number"
                    oninput="loadParties()"
                    class="flex-1 border border-indigo-300 rounded-lg px-4 py-3
                           focus:ring-2 focus:ring-indigo-200">

                <button
                    type="button"
                    onclick="closePartySelector()"
                    class="text-gray-500 text-xl px-3">
                    ×
                </button>

            </div>

        </div>

        <!-- HEADER -->
        <div class="grid grid-cols-2 bg-gray-100 px-4 py-3 text-sm font-medium">
            <span>Party Name</span>
            <span class="text-right">Balance</span>
        </div>

        <!-- PARTY LIST -->
        <div
            id="partyList"
            class="max-h-96 overflow-y-auto">

            <div class="p-6 text-center text-gray-500">
                Loading parties...
            </div>

        </div>

        <!-- CREATE PARTY -->
        <div class="p-4 border-t">

            <button
                type="button"
                onclick="openCreateParty()"
                class="w-full border-2 border-dashed border-blue-400
                       text-blue-500 py-3 rounded-lg hover:bg-blue-50">

                + Create Party

            </button>

        </div>

    </div>

</div>


<!-- ===================================================== -->
<!-- CREATE PARTY MODAL -->
<!-- ===================================================== -->

<div
    id="createPartyModal"
    class="hidden fixed inset-0 bg-black/50 z-50
           flex items-center justify-center">

    <div class="bg-white rounded-xl shadow-xl
                w-full max-w-2xl mx-4">

        <!-- HEADER -->
        <div class="flex justify-between items-center
                    px-6 py-5 border-b">

            <h2 class="text-2xl font-semibold">
                Create New Party
            </h2>

            <button
                type="button"
                onclick="closeCreateParty()"
                class="border border-gray-300 rounded-lg
                       px-4 py-2 text-xl text-gray-500">

                ×

            </button>

        </div>

        <!-- FORM -->
        <div class="p-6">

            <!-- PARTY NAME -->
            <div class="mb-5">

                <label class="block text-gray-600 mb-2">
                    Party Name <span class="text-red-500">*</span>
                </label>

                <input
                    type="text"
                    id="newPartyName"
                    placeholder="Enter party name"
                    class="w-full border rounded-lg px-4 py-3">

            </div>

            <!-- MOBILE -->
            <div class="mb-5">

                <label class="block text-gray-600 mb-2">
                    Mobile Number
                </label>

                <input
                    type="text"
                    id="newPartyMobile"
                    placeholder="Enter Mobile Number"
                    maxlength="20"
                    class="w-full border rounded-lg px-4 py-3">

            </div>

            <!-- ADDRESS -->
            <button
                type="button"
                onclick="togglePartyAddress()"
                class="text-blue-500 font-medium mb-4">

                + Add Address (Optional)

            </button>

            <div
                id="partyAddressFields"
                class="hidden grid grid-cols-2 gap-4 mb-5">

                <input
                    type="text"
                    id="partyStreet"
                    placeholder="Street / Address"
                    class="border rounded-lg px-4 py-3 col-span-2">

                <input
                    type="text"
                    id="partyCity"
                    placeholder="City"
                    class="border rounded-lg px-4 py-3">

                <input
                    type="text"
                    id="partyState"
                    placeholder="State"
                    class="border rounded-lg px-4 py-3">

                <input
                    type="text"
                    id="partyPincode"
                    placeholder="Pincode"
                    class="border rounded-lg px-4 py-3">

            </div>

            <!-- GSTIN -->
            <button
                type="button"
                onclick="togglePartyGST()"
                class="text-blue-500 font-medium mb-5">

                + Add GSTIN (Optional)

            </button>

            <div
                id="partyGSTFields"
                class="hidden mb-5">

                <input
                    type="text"
                    id="newPartyGST"
                    placeholder="Enter GSTIN"
                    maxlength="15"
                    oninput="this.value=this.value.toUpperCase()"
                    class="w-full border rounded-lg px-4 py-3">

            </div>

            <!-- INFO -->
            <div class="bg-amber-50 rounded-lg p-4 text-center text-sm text-gray-600">

                You can add Custom Fields from
                <span class="text-blue-500">
                    Party Settings
                </span>

            </div>

        </div>

        <!-- FOOTER -->
        <div class="flex justify-end gap-4
                    px-6 py-5 border-t">

            <button
                type="button"
                onclick="closeCreateParty()"
                class="border border-gray-300
                       px-6 py-3 rounded-lg">

                Cancel

            </button>

            <button
                type="button"
                onclick="createParty()"
                id="savePartyButton"
                class="bg-indigo-600 text-white
                       px-7 py-3 rounded-lg">

                Save

            </button>

        </div>

    </div>

</div>


<!-- ===================================================== -->
<!-- ITEM SELECTOR MODAL -->
<!-- ===================================================== -->

<div
    id="itemSelectorModal"
    class="hidden fixed inset-0 bg-black/50 z-50
           flex items-center justify-center">

    <div class="bg-white rounded-xl shadow-xl
                w-full max-w-7xl mx-4
                max-h-[90vh] flex flex-col">

        <!-- ================= HEADER ================= -->

        <div class="flex justify-between items-center
                    px-6 py-5 border-b">

            <h2 class="text-2xl font-semibold">
                Add Items to Bill
            </h2>

            <button
                type="button"
                onclick="closeItemSelector()"
                class="border border-gray-300 rounded-lg
                       px-4 py-2 text-xl text-gray-500">

                ×

            </button>

        </div>


        <!-- ================= SEARCH + CATEGORY ================= -->

        <div class="px-6 py-4">

            <div class="flex items-center gap-4">

                <!-- SEARCH -->

                <div class="relative flex-1">

                    <span
                        class="absolute left-4 top-1/2
                               -translate-y-1/2 text-gray-500">

                        🔍

                    </span>

                    <input
                        type="text"
                        id="itemSearch"
                        placeholder="Search by Item / Serial no. / HSN code / SKU / Custom Field / Category"
                        oninput="loadInvoiceItems()"
                        class="w-full border border-indigo-300
                               rounded-lg px-12 py-3
                               focus:ring-2 focus:ring-indigo-200">

                </div>


                <!-- CATEGORY -->

                <select
                    id="itemCategoryFilter"
                    onchange="loadInvoiceItems()"
                    class="w-64 border border-gray-300
                           rounded-lg px-4 py-3 bg-white">

                    <option value="">
                        Select Category
                    </option>

                </select>


                <!-- CREATE ITEM -->

                <button
                    type="button"
                    onclick="openCreateItemFromInvoice()"
                    class="bg-indigo-600 text-white
                           px-6 py-3 rounded-lg
                           font-medium whitespace-nowrap">

                    Create New Item

                </button>

            </div>

        </div>


        <!-- ================= TABLE ================= -->

        <div class="px-6 flex-1 overflow-hidden">

            <div class="border rounded-lg overflow-hidden">

                <!-- TABLE HEADER -->

                <div
                    class="grid grid-cols-[2fr_1fr_1fr_1fr_1fr_1fr]
                           bg-gray-100 border-b
                           px-4 py-4 text-sm font-semibold">

                    <div>
                        Item Name
                    </div>

                    <div>
                        Item Code
                    </div>

                    <div>
                        Stock
                    </div>

                    <div>
                        Sales Price
                    </div>

                    <div>
                        Purchase Price
                    </div>

                    <div>
                        Quantity
                    </div>

                </div>


                <!-- ITEM LIST -->

                <div
                    id="invoiceItemList"
                    class="max-h-[420px] overflow-y-auto">

                    <div
                        class="p-10 text-center text-gray-500">

                        Loading items...

                    </div>

                </div>


                <!-- SELECTED SUMMARY -->

                <div
                    class="grid grid-cols-3
                           bg-gray-100 border-t
                           px-4 py-4 text-sm">

                    <div
                        id="selectedItemCount"
                        class="text-blue-500">

                        Show 0 Item(s) Selected

                    </div>

                    <div class="text-center font-medium">

                        ₹ <span id="selectedItemsAmount">0</span>

                    </div>

                    <div class="text-right">

                        <span id="selectedItemsQuantity">
                            0
                        </span>

                    </div>

                </div>

            </div>

        </div>


        <!-- ================= FOOTER ================= -->

        <div
            class="flex justify-between items-center
                   px-6 py-4 border-t">

            <div class="text-sm text-gray-500">

                Keyboard Shortcuts:

                <span class="ml-4">
                    Change Quantity
                </span>

                <span
                    class="border rounded px-2 py-1 ml-2">

                    Enter

                </span>

                <span class="ml-6">
                    Move between items
                </span>

                <span
                    class="border rounded px-2 py-1 ml-2">

                    ↑

                </span>

                <span
                    class="border rounded px-2 py-1">

                    ↓

                </span>

            </div>


            <div class="flex gap-4">

                <button
                    type="button"
                    onclick="closeItemSelector()"
                    class="border border-gray-300
                           px-7 py-3 rounded-lg">

                    Cancel [ESC]

                </button>


                <button
                    type="button"
                    id="addItemsToBillButton"
                    onclick="addSelectedItemsToBill()"
                    disabled
                    class="bg-indigo-100 text-indigo-400
                           px-7 py-3 rounded-lg font-medium">

                    Add to Bill [F7]

                </button>

            </div>

        </div>

    </div>

</div>

<!-- ===================================================== -->
<!-- CREATE NEW ITEM MODAL -->
<!-- ===================================================== -->

<div
    id="createItemModal"
    class="hidden fixed inset-0 bg-black/50 z-[60]
           flex items-center justify-center p-4">

    <div
        class="bg-white rounded-xl shadow-2xl
               w-full max-w-6xl
               max-h-[95vh]
               overflow-hidden
               flex flex-col">

        <!-- ================= HEADER ================= -->

        <div class="flex justify-between items-center
                    px-6 py-5 border-b">

            <h2 class="text-2xl font-semibold">
                Create New Item
            </h2>

            <button
                type="button"
                onclick="closeCreateItem()"
                class="border border-gray-300
                       rounded-lg px-4 py-2
                       text-xl text-gray-500">

                ×

            </button>

        </div>


        <!-- ================= BODY ================= -->

        <div class="flex flex-1 overflow-hidden">

            <!-- ================= LEFT MENU ================= -->

            <div class="w-72 border-r p-4 bg-white">

                <!-- BASIC DETAILS -->

                <button
                    type="button"
                    id="itemTabBasic"
                    onclick="showItemTab('basic')"
                    class="item-tab active w-full text-left
                           px-5 py-4 rounded-lg
                           text-indigo-600 bg-indigo-50
                           font-medium mb-3">

                    📋 Basic Details *

                </button>


                <div class="px-5 py-3 font-semibold text-gray-700">
                    Advance Details
                </div>


                <!-- STOCK -->

                <button
                    type="button"
                    id="itemTabStock"
                    onclick="showItemTab('stock')"
                    class="item-tab w-full text-left
                           px-5 py-3 rounded-lg
                           text-gray-500 hover:bg-gray-50">

                    📦 Stock Details

                </button>


                <!-- PRICING -->

                <button
                    type="button"
                    id="itemTabPricing"
                    onclick="showItemTab('pricing')"
                    class="item-tab w-full text-left
                           px-5 py-3 rounded-lg
                           text-gray-500 hover:bg-gray-50">

                    🧾 Pricing Details

                </button>


                <!-- PARTY WISE -->

                <button
                    type="button"
                    id="itemTabParty"
                    onclick="showItemTab('party')"
                    class="item-tab w-full text-left
                           px-5 py-3 rounded-lg
                           text-gray-500 hover:bg-gray-50">

                    🧾 Party Wise Prices

                </button>


                <!-- CUSTOM -->

                <button
                    type="button"
                    id="itemTabCustom"
                    onclick="showItemTab('custom')"
                    class="item-tab w-full text-left
                           px-5 py-3 rounded-lg
                           text-gray-500 hover:bg-gray-50">

                    🗂 Custom Fields

                </button>

            </div>


            <!-- ================= RIGHT CONTENT ================= -->

            <div class="flex-1 overflow-y-auto p-6">


                <!-- ================================================= -->
                <!-- BASIC DETAILS -->
                <!-- ================================================= -->

                <div id="itemPanelBasic">

                    <div class="grid grid-cols-2 gap-5">


                        <!-- ITEM TYPE -->

                        <div>

                            <label class="block text-gray-500 mb-2">
                                Item Type <span class="text-red-500">*</span>
                            </label>

                            <div class="flex gap-3">

                                <button
                                    type="button"
                                    id="productTypeButton"
                                    onclick="selectItemType('product')"
                                    class="flex-1 border
                                           border-indigo-500
                                           bg-indigo-50
                                           rounded-lg px-5 py-3
                                           text-left">

                                    Product

                                    <span class="float-right">
                                        ◉
                                    </span>

                                </button>


                                <button
                                    type="button"
                                    id="serviceTypeButton"
                                    onclick="selectItemType('service')"
                                    class="flex-1 border
                                           border-gray-300
                                           rounded-lg px-5 py-3
                                           text-left">

                                    Service

                                    <span class="float-right">
                                        ○
                                    </span>

                                </button>

                            </div>

                        </div>


                        <!-- CATEGORY -->

                        <div>

                            <label class="block text-gray-500 mb-2">
                                Category
                            </label>

                            <input
                                id="newItemCategory"
                                type="text"
                                placeholder="Search Categories"
                                class="w-full border rounded-lg px-4 py-3">

                        </div>


                        <!-- ITEM NAME -->

                        <div>

                            <label class="block text-gray-500 mb-2">
                                Item Name <span class="text-red-500">*</span>
                            </label>

                            <input
                                id="newItemName"
                                type="text"
                                placeholder="ex: Maggie 20gm"
                                class="w-full border rounded-lg px-4 py-3">

                        </div>


                        <!-- ONLINE STORE -->

                        <div class="flex items-center justify-between
                                    pt-8">

                            <label>
                                Show Item in Online Store
                            </label>

                            <input
                                id="newItemOnlineStore"
                                type="checkbox"
                                class="w-5 h-5">

                        </div>


                        <!-- SALES PRICE -->

                        <div>

                            <label class="block text-gray-500 mb-2">
                                Sales Price
                            </label>

                            <div class="flex">

                                <span
                                    class="border border-r-0
                                           rounded-l-lg px-4 py-3
                                           bg-gray-50">

                                    ₹

                                </span>

                                <input
                                    id="newItemSalesPrice"
                                    type="number"
                                    step="0.01"
                                    placeholder="ex: 200"
                                    class="flex-1 border px-4 py-3">

                                <select
                                    id="newItemSalesPriceType"
                                    class="border rounded-r-lg
                                           px-3 py-3">

                                    <option value="with_tax">
                                        With Tax
                                    </option>

                                    <option value="without_tax">
                                        Without Tax
                                    </option>

                                </select>

                            </div>

                        </div>


                        <!-- GST -->

                        <div>

                            <label class="block text-gray-500 mb-2">
                                GST Tax Rate (%)
                            </label>

                            <select
                                id="newItemGst"
                                class="w-full border rounded-lg px-4 py-3">

                                <option value="">
                                    None
                                </option>

                                <option value="0">
                                    0%
                                </option>

                                <option value="5">
                                    5%
                                </option>

                                <option value="12">
                                    12%
                                </option>

                                <option value="18">
                                    18%
                                </option>

                                <option value="28">
                                    28%
                                </option>

                            </select>

                        </div>


                        <!-- UNIT -->

                        <div>

                            <label class="block text-gray-500 mb-2">
                                Measuring Unit
                            </label>

                            <select
                                id="newItemUnit"
                                class="w-full border rounded-lg px-4 py-3">

                                <option value="PCS">
                                    Pieces(PCS)
                                </option>

                                <option value="BOX">
                                    Box(BOX)
                                </option>

                                <option value="KG">
                                    Kilogram(KG)
                                </option>

                                <option value="LTR">
                                    Litre(LTR)
                                </option>

                                <option value="MTR">
                                    Meter(MTR)
                                </option>

                                <option value="NOS">
                                    Numbers(NOS)
                                </option>

                            </select>

                        </div>


                        <!-- OPENING STOCK -->

                        <div>

                            <label class="block text-gray-500 mb-2">
                                Opening Stock
                            </label>

                            <input
                                id="newItemOpeningStock"
                                type="number"
                                step="0.01"
                                placeholder="ex: 150"
                                class="w-full border rounded-lg px-4 py-3">

                        </div>

                    </div>

                </div>


                <!-- ================================================= -->
                <!-- STOCK DETAILS -->
                <!-- ================================================= -->

                <div
                    id="itemPanelStock"
                    class="hidden">

                    <div class="grid grid-cols-2 gap-5">


                        <!-- ITEM CODE -->

                        <div>

                            <label class="block text-gray-500 mb-2">
                                Item Code
                            </label>

                            <div class="flex">

                                <input
                                    id="newItemCode"
                                    type="text"
                                    placeholder="ex: ITM12549"
                                    class="flex-1 border
                                           rounded-l-lg px-4 py-3">

                                <button
                                    type="button"
                                    onclick="generateItemCode()"
                                    class="bg-blue-50
                                           text-blue-600
                                           border border-l-0
                                           rounded-r-lg px-4">

                                    Generate Barcode

                                </button>

                            </div>

                        </div>


                        <!-- HSN -->

                        <div>

                            <label class="block text-gray-500 mb-2">
                                HSN code
                            </label>

                            <input
                                id="newItemHsn"
                                type="text"
                                placeholder="ex: 4010"
                                class="w-full border
                                       rounded-lg px-4 py-3">

                            <button
                                type="button"
                                class="text-blue-500 mt-2">

                                Find HSN Code

                            </button>

                        </div>


                        <!-- UNIT -->

                        <div>

                            <label class="block text-gray-500 mb-2">
                                Measuring Unit
                            </label>

                            <select
                                id="newItemStockUnit"
                                class="w-full border rounded-lg px-4 py-3">

                                <option value="PCS">
                                    Pieces(PCS)
                                </option>

                                <option value="BOX">
                                    Box(BOX)
                                </option>

                                <option value="KG">
                                    Kilogram(KG)
                                </option>

                                <option value="LTR">
                                    Litre(LTR)
                                </option>

                            </select>

                        </div>


                        <!-- AS OF DATE -->

                        <div>

                            <label class="block text-gray-500 mb-2">
                                As of Date
                            </label>

                            <input
                                id="newItemStockDate"
                                type="date"
                                value="{{ date('Y-m-d') }}"
                                class="w-full border
                                       rounded-lg px-4 py-3">

                        </div>


                        <!-- OPENING STOCK -->

                        <div>

                            <label class="block text-gray-500 mb-2">
                                Opening Stock
                            </label>

                            <input
                                id="newItemStock"
                                type="number"
                                step="0.01"
                                placeholder="ex: 150"
                                class="w-full border
                                       rounded-lg px-4 py-3">

                        </div>


                        <!-- LOW STOCK -->

                        <div>

                            <label class="block text-gray-500 mb-2">
                                Low Stock Quantity
                            </label>

                            <input
                                id="newItemLowStock"
                                type="number"
                                step="0.01"
                                placeholder="ex: 5"
                                class="w-full border
                                       rounded-lg px-4 py-3">

                        </div>


                        <!-- DESCRIPTION -->

                        <div class="col-span-2">

                            <label class="block text-gray-500 mb-2">
                                Description
                            </label>

                            <textarea
                                id="newItemDescription"
                                rows="5"
                                placeholder="Enter Description"
                                class="w-full border
                                       rounded-lg p-4"></textarea>

                        </div>

                    </div>

                </div>


                <!-- ================================================= -->
                <!-- PRICING DETAILS -->
                <!-- ================================================= -->

                <div
                    id="itemPanelPricing"
                    class="hidden">

                    <div class="grid grid-cols-2 gap-5">


                        <!-- SALES PRICE -->

                        <div>

                            <label class="block text-gray-500 mb-2">
                                Sales Price
                            </label>

                            <div class="flex">

                                <span
                                    class="border border-r-0
                                           rounded-l-lg px-4 py-3
                                           bg-gray-50">

                                    ₹

                                </span>

                                <input
                                    id="pricingSalesPrice"
                                    type="number"
                                    step="0.01"
                                    placeholder="ex: ₹200"
                                    class="flex-1 border px-4 py-3">

                                <select
                                    class="border rounded-r-lg px-3">

                                    <option>
                                        With Tax
                                    </option>

                                    <option>
                                        Without Tax
                                    </option>

                                </select>

                            </div>

                        </div>


                        <!-- PURCHASE PRICE -->

                        <div>

                            <label class="block text-gray-500 mb-2">
                                Purchase Price
                            </label>

                            <div class="flex">

                                <span
                                    class="border border-r-0
                                           rounded-l-lg px-4 py-3
                                           bg-gray-50">

                                    ₹

                                </span>

                                <input
                                    id="newItemPurchasePrice"
                                    type="number"
                                    step="0.01"
                                    placeholder="ex: ₹200"
                                    class="flex-1 border px-4 py-3">

                                <select
                                    class="border rounded-r-lg px-3">

                                    <option>
                                        With Tax
                                    </option>

                                    <option>
                                        Without Tax
                                    </option>

                                </select>

                            </div>

                        </div>


                        <!-- GST -->

                        <div>

                            <label class="block text-gray-500 mb-2">
                                GST Tax Rate (%)
                            </label>

                            <select
                                id="pricingGst"
                                class="w-full border rounded-lg px-4 py-3">

                                <option value="">
                                    None
                                </option>

                                <option value="5">
                                    5%
                                </option>

                                <option value="12">
                                    12%
                                </option>

                                <option value="18">
                                    18%
                                </option>

                                <option value="28">
                                    28%
                                </option>

                            </select>

                        </div>


                        <!-- DISCOUNT -->

                        <div>

                            <label class="block text-gray-500 mb-2">
                                Discount on Sales Price
                            </label>

                            <div class="flex">

                                <input
                                    id="newItemDiscount"
                                    type="number"
                                    step="0.01"
                                    placeholder="ex: 12"
                                    class="flex-1 border
                                           rounded-l-lg px-4 py-3">

                                <span
                                    class="border border-l-0
                                           rounded-r-lg px-4 py-3">

                                    %

                                </span>

                            </div>

                        </div>

                    </div>

                </div>


                <!-- ================================================= -->
                <!-- PARTY WISE -->
                <!-- ================================================= -->

                <div
                    id="itemPanelParty"
                    class="hidden">

                    <div class="text-center py-20 text-gray-500">

                        Party Wise Prices

                        <p class="text-sm mt-2">
                            Party specific pricing can be added here.
                        </p>

                    </div>

                </div>


                <!-- ================================================= -->
                <!-- CUSTOM FIELDS -->
                <!-- ================================================= -->

                <div
                    id="itemPanelCustom"
                    class="hidden">

                    <div>

                        <label class="block text-gray-500 mb-2">
                            Custom Fields
                        </label>

                        <textarea
                            id="newItemCustomFields"
                            rows="6"
                            placeholder="Enter custom fields"
                            class="w-full border rounded-lg p-4"></textarea>

                    </div>

                </div>

            </div>

        </div>


        <!-- ================= FOOTER ================= -->

        <div
            class="flex justify-end gap-4
                   px-6 py-4 border-t">

            <button
                type="button"
                onclick="closeCreateItem()"
                class="border border-gray-300
                       px-7 py-3 rounded-lg">

                Cancel

            </button>


            <button
                type="button"
                onclick="saveNewItem()"
                id="saveNewItemButton"
                class="bg-indigo-600 text-white
                       px-7 py-3 rounded-lg">

                Save Item

            </button>

        </div>

    </div>

</div>


<!-- ===================================================== -->
<!-- SIGNATURE MODAL -->
<!-- ===================================================== -->

<div
    id="signatureModal"
    class="hidden fixed inset-0 bg-black/50
           flex items-center justify-center z-50">

    <div class="bg-white rounded-xl shadow-xl w-full max-w-lg p-6">

        <div class="flex justify-between items-center mb-4">

            <h2 class="text-lg font-semibold">
                Add Signature
            </h2>

            <button
                onclick="closeSignature()"
                class="text-gray-500 text-xl">

                ×

            </button>

        </div>


        <canvas
            id="signatureCanvas"
            width="600"
            height="220"
            class="signature-canvas w-full border
                   rounded-lg bg-white">
        </canvas>


        <div class="flex justify-between mt-4">

            <button
                onclick="clearSignature()"
                class="border border-gray-300 px-4 py-2 rounded-lg">

                Clear

            </button>


            <div class="flex gap-3">

                <button
                    onclick="closeSignature()"
                    class="border px-4 py-2 rounded-lg">

                    Cancel

                </button>

                <button
                    onclick="saveSignature()"
                    class="bg-indigo-600 text-white px-5 py-2 rounded-lg">

                    Save Signature

                </button>

            </div>

        </div>

    </div>

</div>


<!-- ===================================================== -->
<!-- JAVASCRIPT -->
<!-- ===================================================== -->

<script>

    /* ================= BALANCE ================= */

   /* ================= BALANCE ================= */

function calculateBalance() {

    const total =
        parseFloat(
            document.getElementById('totalAmount').value
        ) || 0;

    const received =
        parseFloat(
            document.getElementById('receivedAmount').value
        ) || 0;

    // Grand Total - Received Amount
    const balance =
        parseFloat(
            (total - received).toFixed(2)
        );

    document.getElementById('receivedDisplay').innerText =
        '₹ ' + received.toFixed(2);

    const balanceElement =
        document.getElementById('balanceAmount');

    balanceElement.innerText =
        '₹ ' + balance.toFixed(2);

    // Color
    if (balance < 0) {

        balanceElement.classList.remove('text-green-600');
        balanceElement.classList.add('text-red-600');

    } else {

        balanceElement.classList.remove('text-red-600');
        balanceElement.classList.add('text-green-600');

    }
}


    /* ================= FULLY PAID ================= */

    function markFullyPaid() {

        const checkbox =
            document.getElementById('fullyPaid');

        const totalInput =
            document.getElementById('totalAmount');

        const receivedInput =
            document.getElementById('receivedAmount');

        if (checkbox.checked) {

            const total =
                parseFloat(totalInput.value) || 0;

            receivedInput.value = total;

            receivedInput.dispatchEvent(
                new Event('input')
            );

        } else {

            receivedInput.value = 0;

            receivedInput.dispatchEvent(
                new Event('input')
            );
        }

        calculateBalance();
    }


    /* ================= SIGNATURE ================= */

    const modal =
        document.getElementById('signatureModal');

    const canvas =
        document.getElementById('signatureCanvas');

    const ctx =
        canvas.getContext('2d');

    let drawing = false;


    function openSignature() {

        modal.classList.remove('hidden');

    }


    function closeSignature() {

        modal.classList.add('hidden');

    }


    function clearSignature() {

        ctx.clearRect(
            0,
            0,
            canvas.width,
            canvas.height
        );

    }


    function getPosition(e) {

        const rect =
            canvas.getBoundingClientRect();

        let clientX;
        let clientY;

        if (e.touches && e.touches.length > 0) {

            clientX = e.touches[0].clientX;
            clientY = e.touches[0].clientY;

        } else {

            clientX = e.clientX;
            clientY = e.clientY;

        }

        return {

            x:
                (clientX - rect.left)
                * (canvas.width / rect.width),

            y:
                (clientY - rect.top)
                * (canvas.height / rect.height)

        };

    }


    canvas.addEventListener('mousedown', function(e) {

        drawing = true;

        const pos = getPosition(e);

        ctx.beginPath();

        ctx.moveTo(pos.x, pos.y);

    });


    canvas.addEventListener('mousemove', function(e) {

        if (!drawing) {
            return;
        }

        const pos = getPosition(e);

        ctx.lineWidth = 2;
        ctx.lineCap = 'round';

        ctx.lineTo(pos.x, pos.y);

        ctx.stroke();

    });


    canvas.addEventListener('mouseup', function() {

        drawing = false;

    });


    canvas.addEventListener('mouseleave', function() {

        drawing = false;

    });


    /* TOUCH SUPPORT */

    canvas.addEventListener('touchstart', function(e) {

        e.preventDefault();

        drawing = true;

        const pos = getPosition(e);

        ctx.beginPath();

        ctx.moveTo(pos.x, pos.y);

    });


    canvas.addEventListener('touchmove', function(e) {

        e.preventDefault();

        if (!drawing) {
            return;
        }

        const pos = getPosition(e);

        ctx.lineWidth = 2;
        ctx.lineCap = 'round';

        ctx.lineTo(pos.x, pos.y);

        ctx.stroke();

    });


    canvas.addEventListener('touchend', function(e) {

        e.preventDefault();

        drawing = false;

    });


    function saveSignature() {

        const signature =
            canvas.toDataURL('image/png');

        if (signature.length < 1000) {

            alert('Please add your signature first.');

            return;

        }

        const button =
            document.querySelector(
                'button[onclick="openSignature()"]'
            );

        button.innerHTML =
            '<img src="' +
            signature +
            '" class="max-h-20 mx-auto">';

        closeSignature();

    }


    /* INITIAL CALCULATION */

    calculateBalance();


    /* =========================================================
   PARTY MANAGEMENT
========================================================= */

let selectedParty = null;


/* ================================
   OPEN PARTY SELECTOR
================================ */

function openPartySelector() {

    document
        .getElementById('partySelectorModal')
        .classList.remove('hidden');

    document.getElementById('partySearch').value = '';

    loadParties();
}


/* ================================
   CLOSE PARTY SELECTOR
================================ */

function closePartySelector() {

    document
        .getElementById('partySelectorModal')
        .classList.add('hidden');
}


/* ================================
   LOAD PARTIES FROM API
================================ */

async function loadParties() {

    const search =
        document.getElementById('partySearch').value.trim();

    const list =
        document.getElementById('partyList');

    list.innerHTML = `
        <div class="p-6 text-center text-gray-500">
            Loading parties...
        </div>
    `;

    try {

        const token = localStorage.getItem('token');

        const url = search
            ? `/api/parties?search=${encodeURIComponent(search)}`
            : `/api/parties`;

        const response = await fetch(url, {

            method: 'GET',

            headers: {
                'Accept': 'application/json',
                'Authorization': `Bearer ${token}`
            }

        });

        if (!response.ok) {

            throw new Error(
                `HTTP ${response.status}`
            );

        }

        const result = await response.json();

        if (!result.success) {

            throw new Error(
                result.message || 'Unable to load parties'
            );

        }

        const parties = result.data || [];

        if (parties.length === 0) {

            list.innerHTML = `
                <div class="p-8 text-center text-gray-500">
                    No parties found.
                </div>
            `;

            return;
        }


        list.innerHTML = parties.map(party => {

            const balance =
                parseFloat(party.opening_balance || 0);

            const balanceType =
                party.opening_balance_type || 'receive';

            let balanceHTML = '₹ 0';

            if (balance > 0) {

                if (balanceType === 'receive') {

                    balanceHTML = `
                        ₹ ${balance.toLocaleString('en-IN')}
                        <span class="text-green-500 text-xl ml-2">↓</span>
                    `;

                } else {

                    balanceHTML = `
                        ₹ ${balance.toLocaleString('en-IN')}
                        <span class="text-red-500 text-xl ml-2">↑</span>
                    `;

                }

            }

            return `

                <button
                    type="button"
                    onclick='selectParty(${JSON.stringify(party)})'
                    class="w-full grid grid-cols-2
                           px-4 py-4 border-b
                           hover:bg-gray-50 text-left">

                    <span class="font-medium text-gray-700">
                        ${escapeHtml(party.party_name)}
                    </span>

                    <span class="text-right text-gray-600">
                        ${balanceHTML}
                    </span>

                </button>

            `;

        }).join('');

    } catch (error) {

        console.error('Party API Error:', error);

        list.innerHTML = `
            <div class="p-6 text-center text-red-500">
                Unable to load parties.
                <br>
                <span class="text-sm">
                    Please check your login/token.
                </span>
            </div>
        `;
    }
}


/* ================================
   SELECT PARTY
================================ */

function selectParty(party) {

    selectedParty = party;

    closePartySelector();

    const billTo =
        document.querySelector(
            'section.lg\\:col-span-3 .p-5'
        );

    if (billTo) {

        billTo.innerHTML = `

            <div class="border rounded-lg p-4 bg-white">

                <div class="flex justify-between items-start">

                    <div>

                        <h3 class="font-semibold text-lg">
                            ${escapeHtml(party.party_name)}
                        </h3>

                        ${
                            party.contact_number
                            ? `
                                <p class="text-sm text-gray-500 mt-1">
                                    ${escapeHtml(party.contact_number)}
                                </p>
                            `
                            : ''
                        }

                        ${
                            party.gst_number
                            ? `
                                <p class="text-sm text-gray-500 mt-1">
                                    GSTIN: ${escapeHtml(party.gst_number)}
                                </p>
                            `
                            : ''
                        }

                    </div>

                    <button
                        type="button"
                        onclick="openPartySelector()"
                        class="text-blue-500 text-sm">

                        Change

                    </button>

                </div>

            </div>

        `;
    }
}


/* ================================
   OPEN CREATE PARTY
================================ */

function openCreateParty() {

    document
        .getElementById('partySelectorModal')
        .classList.add('hidden');

    document
        .getElementById('createPartyModal')
        .classList.remove('hidden');

}


/* ================================
   CLOSE CREATE PARTY
================================ */

function closeCreateParty() {

    document
        .getElementById('createPartyModal')
        .classList.add('hidden');

}


/* ================================
   ADDRESS TOGGLE
================================ */

function togglePartyAddress() {

    document
        .getElementById('partyAddressFields')
        .classList.toggle('hidden');

}


/* ================================
   GST TOGGLE
================================ */

function togglePartyGST() {

    document
        .getElementById('partyGSTFields')
        .classList.toggle('hidden');

}


/* ================================
   CREATE PARTY API
================================ */

async function createParty() {

    const name =
        document.getElementById('newPartyName')
            .value.trim();

    const mobile =
        document.getElementById('newPartyMobile')
            .value.trim();

    const gst =
        document.getElementById('newPartyGST')
            .value.trim()
            .toUpperCase();

    const street =
        document.getElementById('partyStreet')
            .value.trim();

    const city =
        document.getElementById('partyCity')
            .value.trim();

    const state =
        document.getElementById('partyState')
            .value.trim();

    const pincode =
        document.getElementById('partyPincode')
            .value.trim();


    /* REQUIRED */

    if (!name) {

        alert('Please enter party name.');

        document
            .getElementById('newPartyName')
            .focus();

        return;
    }


    const button =
        document.getElementById('savePartyButton');

    button.disabled = true;

    button.innerText = 'Saving...';


    try {

        const token =
            localStorage.getItem('token');


        /*
         * IMPORTANT:
         *
         * Your existing PartyController requires:
         *
         * party_name
         * party_type
         * opening_balance_type
         *
         * Therefore we provide defaults for the
         * simple Create Party screen.
         */

        const payload = {

            party_name: name,

            contact_number:
                mobile || null,

            party_type: 'customer',

            gst_number:
                gst || null,

            billing_street:
                street || null,

            billing_city:
                city || null,

            billing_state:
                state || null,

            billing_pincode:
                pincode || null,

            opening_balance: 0,

            opening_balance_type: 'receive'

        };


        const response = await fetch(
            '/api/parties',
            {
                method: 'POST',

                headers: {

                    'Content-Type':
                        'application/json',

                    'Accept':
                        'application/json',

                    'Authorization':
                        `Bearer ${token}`

                },

                body:
                    JSON.stringify(payload)
            }
        );


        const result =
            await response.json();


        if (!response.ok) {

            console.error(
                'Create Party Error:',
                result
            );

            let message =
                result.message ||
                'Unable to create party.';


            /*
             * Laravel validation errors
             */

            if (result.errors) {

                message =
                    Object.values(result.errors)
                        .flat()
                        .join('\n');

            }

            throw new Error(message);
        }


        if (!result.success) {

            throw new Error(
                result.message ||
                'Party creation failed.'
            );

        }


        /* ================================
           PARTY CREATED
        ================================= */

        const party =
            result.data;


        selectedParty =
            party;


        closeCreateParty();


        /*
         * Clear form
         */

        resetPartyForm();


        /*
         * Put created party into invoice
         */

        selectParty(party);


        /*
         * Optional success notification
         */

        alert(
            'Party created successfully'
        );


    } catch (error) {

        console.error(
            'Create Party Error:',
            error
        );

        alert(
            error.message ||
            'Unable to create party.'
        );

    } finally {

        button.disabled = false;

        button.innerText = 'Save';

    }

}


/* ================================
   RESET PARTY FORM
================================ */

function resetPartyForm() {

    document.getElementById('newPartyName').value = '';

    document.getElementById('newPartyMobile').value = '';

    document.getElementById('newPartyGST').value = '';

    document.getElementById('partyStreet').value = '';

    document.getElementById('partyCity').value = '';

    document.getElementById('partyState').value = '';

    document.getElementById('partyPincode').value = '';

    document
        .getElementById('partyAddressFields')
        .classList.add('hidden');

    document
        .getElementById('partyGSTFields')
        .classList.add('hidden');

}


/* ================================
   HTML ESCAPE
================================ */

function escapeHtml(value) {

    if (value === null || value === undefined) {

        return '';

    }

    return String(value)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');

}


/* =========================================================
   ITEM SELECTOR
========================================================= */

let invoiceItems = [];
let selectedInvoiceItems = [];
let billItems = [];


/* =========================================================
   OPEN ITEM SELECTOR
========================================================= */

function openItemSelector() {

    document
        .getElementById('itemSelectorModal')
        .classList.remove('hidden');

    document.getElementById('itemSearch').value = '';

    selectedInvoiceItems = [];

    updateSelectedItemsSummary();

    loadInvoiceItems();
}


/* =========================================================
   CLOSE ITEM SELECTOR
========================================================= */

function closeItemSelector() {

    document
        .getElementById('itemSelectorModal')
        .classList.add('hidden');

}


/* =========================================================
   LOAD ITEMS FROM API
========================================================= */

async function loadInvoiceItems() {

    const search =
        document
            .getElementById('itemSearch')
            .value
            .trim();

    const list =
        document.getElementById('invoiceItemList');

    list.innerHTML = `
        <div class="p-10 text-center text-gray-500">
            Loading items...
        </div>
    `;


    try {

        const token =
            localStorage.getItem('token');


        const url = search
            ? `/api/items?search=${encodeURIComponent(search)}`
            : `/api/items`;


        const response =
            await fetch(url, {

                method: 'GET',

                headers: {

                    'Accept':
                        'application/json',

                    'Authorization':
                        `Bearer ${token}`

                }

            });


        if (!response.ok) {

            throw new Error(
                `HTTP ${response.status}`
            );

        }


        const result =
            await response.json();


        if (!result.success) {

            throw new Error(
                result.message ||
                'Unable to load items'
            );

        }


        invoiceItems =
            result.data || [];


        renderInvoiceItems();


    } catch (error) {

        console.error(
            'Item API Error:',
            error
        );


        list.innerHTML = `

            <div
                class="p-10 text-center text-red-500">

                Unable to load items.

                <br>

                <span class="text-sm">

                    Please check your login/token.

                </span>

            </div>

        `;

    }

}


/* =========================================================
   RENDER ITEMS
========================================================= */

function renderInvoiceItems() {

    const list =
        document.getElementById('invoiceItemList');


    if (invoiceItems.length === 0) {

        list.innerHTML = `

            <div
                class="p-10 text-center text-gray-500">

                No items found.

            </div>

        `;

        return;

    }


    list.innerHTML =
        invoiceItems.map(item => {

            const stock =
                parseFloat(
                    item.opening_stock || 0
                );

            const salesPrice =
                parseFloat(
                    item.sales_price || 0
                );

            const purchasePrice =
                parseFloat(
                    item.purchase_price || 0
                );


            const selected =
                selectedInvoiceItems.find(
                    selectedItem =>
                        selectedItem.id == item.id
                );


            const quantity =
                selected
                    ? selected.quantity
                    : 0;


            return `

                <div
                    class="grid
                           grid-cols-[2fr_1fr_1fr_1fr_1fr_1fr]
                           items-center
                           px-4 py-4
                           border-b
                           hover:bg-gray-50">


                    <!-- ITEM NAME -->

                    <div>

                        <div
                            class="font-medium
                                   text-gray-700">

                            ${escapeHtml(
                                item.name || '-'
                            )}

                        </div>

                        ${
                            item.hsn_code
                            ? `
                                <div
                                    class="text-xs
                                           text-gray-400
                                           mt-1">

                                    HSN:
                                    ${escapeHtml(
                                        item.hsn_code
                                    )}

                                </div>
                              `
                            : ''
                        }

                    </div>


                    <!-- ITEM CODE -->

                    <div
                        class="text-gray-600">

                        ${escapeHtml(
                            item.item_code || '-'
                        )}

                    </div>


                    <!-- STOCK -->

                    <div
                        class="text-gray-600">

                        ${stock}
                        ${escapeHtml(
                            item.unit || ''
                        )}

                    </div>


                    <!-- SALES PRICE -->

                    <div
                        class="text-gray-700">

                        ₹ ${salesPrice.toFixed(2)}

                    </div>


                    <!-- PURCHASE PRICE -->

                    <div
                        class="text-gray-700">

                        ₹ ${purchasePrice.toFixed(2)}

                    </div>


                    <!-- QUANTITY -->

                    <div>

                        ${
                            selected
                            ? `

                                <div
                                    class="flex items-center gap-2">

                                    <input
                                        type="number"
                                        min="1"
                                        value="${quantity}"
                                        onchange="
                                            changeInvoiceItemQuantity(
                                                ${item.id},
                                                this.value
                                            )
                                        "
                                        class="w-20 border
                                               rounded-lg
                                               px-3 py-2">

                                    <button
                                        type="button"
                                        onclick="
                                            removeInvoiceItem(
                                                ${item.id}
                                            )
                                        "
                                        class="text-red-500">

                                        ×

                                    </button>

                                </div>

                              `
                            : `

                                <button
                                    type="button"
                                    onclick="
                                        selectInvoiceItem(
                                            ${item.id}
                                        )
                                    "
                                    class="w-full
                                           bg-blue-50
                                           text-blue-600
                                           border
                                           border-blue-200
                                           px-4 py-3
                                           rounded-lg">

                                    + Add

                                </button>

                              `
                        }

                    </div>

                </div>

            `;

        }).join('');

}


/* =========================================================
   SELECT ITEM
========================================================= */

function selectInvoiceItem(itemId) {

    const item =
        invoiceItems.find(
            item => item.id == itemId
        );


    if (!item) {
        return;
    }


    const alreadySelected =
        selectedInvoiceItems.some(
            selectedItem =>
                selectedItem.id == itemId
        );


    if (alreadySelected) {
        return;
    }


    selectedInvoiceItems.push({

        ...item,

        quantity: 1

    });


    renderInvoiceItems();

    updateSelectedItemsSummary();

}


/* =========================================================
   CHANGE QUANTITY
========================================================= */

function changeInvoiceItemQuantity(
    itemId,
    quantity
) {

    quantity =
        parseFloat(quantity);


    if (!quantity || quantity < 1) {

        quantity = 1;

    }


    const item =
        selectedInvoiceItems.find(
            selectedItem =>
                selectedItem.id == itemId
        );


    if (item) {

        item.quantity =
            quantity;

    }


    renderInvoiceItems();

    updateSelectedItemsSummary();

}


/* =========================================================
   REMOVE ITEM
========================================================= */

function removeInvoiceItem(itemId) {

    selectedInvoiceItems =
        selectedInvoiceItems.filter(
            item =>
                item.id != itemId
        );


    renderInvoiceItems();

    updateSelectedItemsSummary();

}


/* =========================================================
   SELECTED ITEM SUMMARY
========================================================= */

function updateSelectedItemsSummary() {

    const count =
        selectedInvoiceItems.length;


    let totalAmount = 0;

    let totalQuantity = 0;


    selectedInvoiceItems.forEach(item => {

        const price =
            parseFloat(
                item.sales_price || 0
            );

        const quantity =
            parseFloat(
                item.quantity || 0
            );


        totalAmount +=
            price * quantity;


        totalQuantity +=
            quantity;

    });


    document
        .getElementById('selectedItemCount')
        .innerText =
            `Show ${count} Item(s) Selected`;


    document
        .getElementById('selectedItemsAmount')
        .innerText =
            totalAmount.toFixed(2);


    document
        .getElementById('selectedItemsQuantity')
        .innerText =
            totalQuantity;


    const button =
        document.getElementById(
            'addItemsToBillButton'
        );


    button.disabled =
        count === 0;


    if (count > 0) {

        button.classList.remove(
            'bg-indigo-100',
            'text-indigo-400'
        );

        button.classList.add(
            'bg-indigo-600',
            'text-white'
        );

    } else {

        button.classList.add(
            'bg-indigo-100',
            'text-indigo-400'
        );

        button.classList.remove(
            'bg-indigo-600',
            'text-white'
        );

    }

}


/* =========================================================
   ADD SELECTED ITEMS TO INVOICE
========================================================= */

// function addSelectedItemsToBill() {

//     if (
//         selectedInvoiceItems.length === 0
//     ) {

//         return;

//     }


//     console.log(
//         'Selected Invoice Items:',
//         selectedInvoiceItems
//     );


//     /*
//      * For now we close the modal.
//      *
//      * Next we will render these items
//      * inside the invoice table.
//      */

//     closeItemSelector();


//     /*
//      * TEMPORARY SUCCESS MESSAGE
//      */

//     alert(
//         `${selectedInvoiceItems.length} item(s) added to bill.`
//     );

// }

function addSelectedItemsToBill() {

    if (selectedInvoiceItems.length === 0) {
        return;
    }

    selectedInvoiceItems.forEach(selectedItem => {

        const existingItem = billItems.find(
            item => item.id == selectedItem.id
        );

        if (existingItem) {

            // If item already exists, increase quantity
            existingItem.quantity +=
                parseFloat(selectedItem.quantity || 1);

        } else {

            billItems.push({

                id: selectedItem.id,

                name: selectedItem.name,

                hsn_code: selectedItem.hsn_code || '',

                item_code: selectedItem.item_code || '',

                unit: selectedItem.unit || 'PCS',

                sales_price:
                    parseFloat(selectedItem.sales_price || 0),

                purchase_price:
                    parseFloat(selectedItem.purchase_price || 0),

                gst_percent:
                    parseFloat(selectedItem.gst_percent || 0),

                quantity:
                    parseFloat(selectedItem.quantity || 1),

                discount: 0

            });

        }

    });

    console.log('Bill Items:', billItems);

    renderBillItems();

    closeItemSelector();

    // Clear temporary selector selection
    selectedInvoiceItems = [];

    updateSelectedItemsSummary();

}

function renderBillItems() {

    const tbody =
        document.getElementById('invoiceTableBody');

    if (!tbody) {
        return;
    }

    if (billItems.length === 0) {

        tbody.innerHTML = `

            <tr id="emptyInvoiceRow">

                <td colspan="7" class="p-3">

                    <button
                        type="button"
                        onclick="openItemSelector()"
                        class="w-full border-2 border-dashed
                               border-blue-400 text-blue-500
                               py-3 rounded-lg hover:bg-blue-50">

                        + Add Item

                    </button>

                </td>

            </tr>

        `;

        updateInvoiceTotals();

        return;
    }

    tbody.innerHTML = billItems.map((item, index) => {

        const quantity =
            parseFloat(item.quantity || 0);

        const price =
            parseFloat(item.sales_price || 0);

        const discount =
            parseFloat(item.discount || 0);

        const gst =
            parseFloat(item.gst_percent || 0);

        const subtotal =
            quantity * price;

        const discountAmount =
            subtotal * discount / 100;

        const taxableAmount =
            subtotal - discountAmount;

        const taxAmount =
            taxableAmount * gst / 100;

        const amount =
            taxableAmount + taxAmount;

        return `

            <tr class="border-b">

                <!-- NO -->

                <td class="px-4 py-4 text-center">

                    ${index + 1}

                </td>


                <!-- ITEM -->

                <td class="px-4 py-4">

                    <div class="font-medium text-gray-700">

                        ${escapeHtml(item.name)}

                    </div>

                    <textarea
                        class="w-full border rounded-lg
                               px-3 py-2 mt-2 text-sm"
                        placeholder="Item description"
                        rows="2"
                    ></textarea>

                </td>


                <!-- HSN -->

                <td class="px-4 py-4">

                    ${escapeHtml(item.hsn_code || '-')}

                </td>


                <!-- QTY -->

                <td class="px-4 py-4">

                    <div class="flex">

                        <input
                            type="number"
                            min="1"
                            value="${quantity}"
                            onchange="updateBillItemQuantity(
                                ${item.id},
                                this.value
                            )"
                            class="border rounded-l-lg
                                   px-3 py-2 w-20">

                        <span
                            class="border border-l-0
                                   rounded-r-lg px-3 py-2
                                   bg-gray-50">

                            ${escapeHtml(item.unit || 'PCS')}

                        </span>

                    </div>

                </td>


                <!-- PRICE -->

                <td class="px-4 py-4">

                    <div class="flex">

                        <span
                            class="border border-r-0
                                   rounded-l-lg px-3 py-2
                                   bg-gray-50">

                            ₹

                        </span>

                        <input
                            type="number"
                            min="0"
                            step="0.01"
                            value="${price}"
                            onchange="updateBillItemPrice(
                                ${item.id},
                                this.value
                            )"
                            class="border rounded-r-lg
                                   px-3 py-2 w-full">

                    </div>

                </td>


    

              


                <!-- TAX -->

                <td class="px-4 py-4">

                    <select
                        onchange="updateBillItemTax(
                            ${item.id},
                            this.value
                        )"
                        class="border rounded-lg
                               px-3 py-2 w-full">

                        <option value="0"
                            ${gst == 0 ? 'selected' : ''}>
                            0%
                        </option>

                        <option value="5"
                            ${gst == 5 ? 'selected' : ''}>
                            5%
                        </option>

                        <option value="12"
                            ${gst == 12 ? 'selected' : ''}>
                            12%
                        </option>

                        <option value="18"
                            ${gst == 18 ? 'selected' : ''}>
                            18%
                        </option>

                        <option value="28"
                            ${gst == 28 ? 'selected' : ''}>
                            28%
                        </option>

                    </select>

                    <div class="text-xs text-gray-500 mt-1">

                        ₹ ${taxAmount.toFixed(2)}

                    </div>

                </td>


                <!-- AMOUNT -->

                <td class="px-4 py-4">

                    <div class="flex items-center gap-2">

                        <span class="font-semibold">

                            ₹ ${amount.toFixed(2)}

                        </span>

                        <button
                            type="button"
                            onclick="removeBillItem(${item.id})"
                            class="text-red-500 text-xl">

                            ×

                        </button>

                    </div>

                </td>

            </tr>

        `;

    }).join('');


    // Add Item button below items

    tbody.innerHTML += `

        <tr>

            <td colspan="7" class="p-3">

                <button
                    type="button"
                    onclick="openItemSelector()"
                    class="w-full border-2 border-dashed
                           border-blue-400 text-blue-500
                           py-3 rounded-lg hover:bg-blue-50">

                    + Add Item

                </button>

            </td>

        </tr>

    `;


    updateInvoiceTotals();

}

function updateBillItemQuantity(itemId, value) {

    const item = billItems.find(
        item => item.id == itemId
    );

    if (!item) {
        return;
    }

    let quantity = parseFloat(value);

    if (!quantity || quantity < 1) {
        quantity = 1;
    }

    item.quantity = quantity;

    renderBillItems();

}

function updateBillItemPrice(itemId, value) {

    const item = billItems.find(
        item => item.id == itemId
    );

    if (!item) {
        return;
    }

    item.sales_price =
        parseFloat(value) || 0;

    renderBillItems();

}

function updateBillItemDiscount(itemId, value) {

    const item = billItems.find(
        item => item.id == itemId
    );

    if (!item) {
        return;
    }

    item.discount =
        parseFloat(value) || 0;

    renderBillItems();

}

function updateBillItemTax(itemId, value) {

    const item = billItems.find(
        item => item.id == itemId
    );

    if (!item) {
        return;
    }

    item.gst_percent =
        parseFloat(value) || 0;

    renderBillItems();

}

function removeBillItem(itemId) {

    billItems = billItems.filter(
        item => item.id != itemId
    );

    renderBillItems();

}

function updateInvoiceTotals() {

    let subtotal = 0;

    let totalTax = 0;


    /* =====================================================
       ITEMS
    ===================================================== */

    billItems.forEach(item => {

        const quantity =
            parseFloat(item.quantity || 0);

        const price =
            parseFloat(item.sales_price || 0);

        const discount =
            parseFloat(item.discount || 0);

        const gst =
            parseFloat(item.gst_percent || 0);


        const itemSubtotal =
            quantity * price;


        const itemDiscount =
            itemSubtotal * discount / 100;


        const taxable =
            itemSubtotal - itemDiscount;


        const tax =
            taxable * gst / 100;


        subtotal += taxable;

        totalTax += tax;

    });


    subtotal =
        parseFloat(subtotal.toFixed(2));

    totalTax =
        parseFloat(totalTax.toFixed(2));

    // =====================================================
// UPDATE ITEMS TABLE SUBTOTAL / TOTAL
// =====================================================

const invoiceSubtotal =
    document.getElementById('invoiceSubtotal');

if (invoiceSubtotal) {
    invoiceSubtotal.innerText =
        subtotal.toFixed(2);
}

const invoiceTotal =
    document.getElementById('invoiceTotal');

if (invoiceTotal) {
    invoiceTotal.innerText =
        (subtotal + totalTax).toFixed(2);
}    


    /* =====================================================
       ADDITIONAL CHARGES
    ===================================================== */

    const additionalTotal =
        additionalCharges.reduce(
            (sum, charge) => {

                return sum +
                    (parseFloat(charge.amount) || 0);

            },
            0
        );


    /* =====================================================
       INVOICE DISCOUNT
    ===================================================== */

    const discountPercent =
        parseFloat(
            document.getElementById(
                'invoiceDiscountPercent'
            )?.value || 0
        );


    const discountAmount =
        (subtotal + totalTax + additionalTotal)
        * discountPercent
        / 100;


    /* =====================================================
       TCS
    ===================================================== */

    let tcsAmount = 0;


    const applyTcs =
        document.getElementById(
            'applyTcs'
        )?.checked;


    if (applyTcs) {

        const tcsPercent =
            parseFloat(
                document.getElementById(
                    'tcsPercent'
                ).value || 0
            );

        const tcsBase =
            subtotal +
            totalTax +
            additionalTotal -
            discountAmount;

        tcsAmount =
            tcsBase *
            tcsPercent /
            100;

    }


    /* =====================================================
       BEFORE ROUND OFF
    ===================================================== */

    let total =
        subtotal +
        totalTax +
        additionalTotal -
        discountAmount +
        tcsAmount;


    /* =====================================================
       AUTO ROUND OFF
    ===================================================== */

    let roundOff = 0;


    const autoRound =
        document.getElementById(
            'autoRoundOff'
        )?.checked;


    if (autoRound) {

        const roundValue =
            parseFloat(
                document.getElementById(
                    'roundOffAmount'
                ).value || 0
            );


        const roundType =
            document.getElementById(
                'roundOffType'
            ).value;


        if (roundType === 'add') {

            roundOff = roundValue;

        } else {

            roundOff = -roundValue;

        }

        total += roundOff;

    }


    total =
        parseFloat(
            total.toFixed(2)
        );


    /* =====================================================
       UPDATE UI
    ===================================================== */

    const taxableDisplay =
        document.getElementById(
            'taxableAmountDisplay'
        );

    if (taxableDisplay) {

        taxableDisplay.innerText =
            '₹ ' +
            (
                subtotal +
                totalTax +
                additionalTotal
            ).toFixed(2);

    }


    const discountDisplay =
        document.getElementById(
            'discountAmountDisplay'
        );

    if (discountDisplay) {

        discountDisplay.innerText =
            '₹ ' +
            discountAmount.toFixed(2);

    }


    const tcsDisplay =
        document.getElementById(
            'tcsAmountDisplay'
        );

    if (tcsDisplay) {

        tcsDisplay.innerText =
            '₹ ' +
            tcsAmount.toFixed(2);

    }


    /* =====================================================
       TOTAL
    ===================================================== */

    const totalAmount =
        document.getElementById(
            'totalAmount'
        );

    if (totalAmount) {

        totalAmount.value =
            total.toFixed(2);

    }


    calculateBalance();

}

/* =========================================================
   CREATE NEW ITEM
========================================================= */

let newItemType = 'product';


/* ================================
   OPEN CREATE ITEM
================================ */

function openCreateItemFromInvoice() {

    // Hide item selector
    document
        .getElementById('itemSelectorModal')
        .classList.add('hidden');

    // Show create item modal
    document
        .getElementById('createItemModal')
        .classList.remove('hidden');

    // Reset to basic tab
    showItemTab('basic');

}


/* ================================
   CLOSE CREATE ITEM
================================ */

function closeCreateItem() {

    document
        .getElementById('createItemModal')
        .classList.add('hidden');

}


/* =========================================================
   SYNC BASIC DETAILS → ADVANCE DETAILS
========================================================= */

function syncBasicItemDetails() {

    // Sales Price
    const basicSalesPrice =
        document.getElementById('newItemSalesPrice');

    const pricingSalesPrice =
        document.getElementById('pricingSalesPrice');

    if (basicSalesPrice && pricingSalesPrice) {
        pricingSalesPrice.value = basicSalesPrice.value;
    }


    // GST Tax Rate
    const basicGst =
        document.getElementById('newItemGst');

    const pricingGst =
        document.getElementById('pricingGst');

    if (basicGst && pricingGst) {
        pricingGst.value = basicGst.value;
    }


    // Opening Stock
    const basicOpeningStock =
        document.getElementById('newItemOpeningStock');

    const stockOpeningStock =
        document.getElementById('newItemStock');

    if (basicOpeningStock && stockOpeningStock) {
        stockOpeningStock.value = basicOpeningStock.value;
    }
}

document.addEventListener('DOMContentLoaded', function () {

    const salesPrice =
        document.getElementById('newItemSalesPrice');

    const gst =
        document.getElementById('newItemGst');

    const openingStock =
        document.getElementById('newItemOpeningStock');


    if (salesPrice) {
        salesPrice.addEventListener(
            'input',
            syncBasicItemDetails
        );
    }


    if (gst) {
        gst.addEventListener(
            'change',
            syncBasicItemDetails
        );
    }


    if (openingStock) {
        openingStock.addEventListener(
            'input',
            syncBasicItemDetails
        );
    }

});


/* ================================
   ITEM TABS
================================ */

function showItemTab(tab) {

    const panels = [
        'basic',
        'stock',
        'pricing',
        'party',
        'custom'
    ];

    panels.forEach(function(name) {

        const panel =
            document.getElementById(
                'itemPanel' +
                name.charAt(0).toUpperCase() +
                name.slice(1)
            );

        const button =
            document.getElementById(
                'itemTab' +
                name.charAt(0).toUpperCase() +
                name.slice(1)
            );

        if (panel) {

            panel.classList.add('hidden');

        }

        if (button) {

            button.classList.remove(
                'active',
                'text-indigo-600',
                'bg-indigo-50',
                'font-medium'
            );

            button.classList.add(
                'text-gray-500'
            );

        }

    });


    const selectedPanel =
        document.getElementById(
            'itemPanel' +
            tab.charAt(0).toUpperCase() +
            tab.slice(1)
        );

    const selectedButton =
        document.getElementById(
            'itemTab' +
            tab.charAt(0).toUpperCase() +
            tab.slice(1)
        );


    if (selectedPanel) {

        selectedPanel.classList.remove('hidden');

    }


    if (selectedButton) {

        selectedButton.classList.add(
            'active',
            'text-indigo-600',
            'bg-indigo-50',
            'font-medium'
        );

        selectedButton.classList.remove(
            'text-gray-500'
        );

    }

}


/* ================================
   ITEM TYPE
================================ */

function selectItemType(type) {

    newItemType = type;


    const productButton =
        document.getElementById(
            'productTypeButton'
        );

    const serviceButton =
        document.getElementById(
            'serviceTypeButton'
        );


    if (type === 'product') {

        productButton.classList.add(
            'border-indigo-500',
            'bg-indigo-50'
        );

        productButton.classList.remove(
            'border-gray-300'
        );


        serviceButton.classList.remove(
            'border-indigo-500',
            'bg-indigo-50'
        );

        serviceButton.classList.add(
            'border-gray-300'
        );

    } else {

        serviceButton.classList.add(
            'border-indigo-500',
            'bg-indigo-50'
        );

        serviceButton.classList.remove(
            'border-gray-300'
        );


        productButton.classList.remove(
            'border-indigo-500',
            'bg-indigo-50'
        );

        productButton.classList.add(
            'border-gray-300'
        );

    }

}


/* ================================
   GENERATE ITEM CODE
================================ */

function generateItemCode() {

    const code =
        'ITM' +
        Math.floor(
            100000 +
            Math.random() * 900000
        );

    document
        .getElementById('newItemCode')
        .value = code;

}


/* ================================
   SAVE NEW ITEM
================================ */

async function saveNewItem() {

    const button =
        document.getElementById(
            'saveNewItemButton'
        );


    const name =
        document
            .getElementById('newItemName')
            .value
            .trim();


    if (!name) {

        alert('Please enter Item Name.');

        showItemTab('basic');

        document
            .getElementById('newItemName')
            .focus();

        return;

    }


    const salesPrice =
        parseFloat(
            document
                .getElementById('newItemSalesPrice')
                .value
        ) || 0;


    const purchasePrice =
        parseFloat(
            document
                .getElementById('newItemPurchasePrice')
                .value
        ) || 0;


    const openingStock =
        parseFloat(
            document
                .getElementById('newItemOpeningStock')
                .value
        ) || 0;


    const lowStockQuantity =
        parseFloat(
            document
                .getElementById('newItemLowStock')
                .value
        ) || 0;


    const gst =
        document
            .getElementById('newItemGst')
            .value;


    const payload = {

        name: name,

        item_type: newItemType,

        inventory_tracking_by: null,

        unit:
            document
                .getElementById('newItemUnit')
                .value,

        sales_price: salesPrice,

        purchase_price: purchasePrice,

        gst_percent:
            gst === ''
                ? null
                : parseFloat(gst),

        hsn_code:
            document
                .getElementById('newItemHsn')
                .value
                .trim() || null,

        opening_stock:
            openingStock,

        stock_as_of_date:
            document
                .getElementById('newItemStockDate')
                .value || null,

        item_code:
            document
                .getElementById('newItemCode')
                .value
                .trim() || null,

        barcode:
            document
                .getElementById('newItemCode')
                .value
                .trim() || null,

        item_categories:
            document
                .getElementById('newItemCategory')
                .value
                .trim()
                ? [
                    document
                        .getElementById('newItemCategory')
                        .value
                        .trim()
                  ]
                : [],

        description:
            document
                .getElementById('newItemDescription')
                .value
                .trim() || null,

        low_stock_alert:
            lowStockQuantity > 0,

        low_stock_quantity:
            lowStockQuantity,

        show_in_online_store:
            document
                .getElementById('newItemOnlineStore')
                .checked

    };


    button.disabled = true;

    button.innerText = 'Saving...';


    try {

        const token =
            localStorage.getItem('token');


        const response =
            await fetch(
                '/api/items',
                {
                    method: 'POST',

                    headers: {

                        'Content-Type':
                            'application/json',

                        'Accept':
                            'application/json',

                        'Authorization':
                            `Bearer ${token}`

                    },

                    body:
                        JSON.stringify(payload)

                }
            );


        const result =
            await response.json();


        if (!response.ok) {

            console.error(
                'Create Item Error:',
                result
            );


            let message =
                result.message ||
                'Unable to create item.';


            if (result.errors) {

                message =
                    Object.values(
                        result.errors
                    )
                    .flat()
                    .join('\n');

            }


            throw new Error(message);

        }


        if (!result.success) {

            throw new Error(
                result.message ||
                'Item creation failed.'
            );

        }


        /* ============================
           ITEM CREATED
        ============================ */

        const createdItem =
            result.data;


        alert(
            'Item created successfully'
        );


        /*
         * Close Create Item
         */

        closeCreateItem();


        /*
         * Re-open Item Selector
         */

        document
            .getElementById(
                'itemSelectorModal'
            )
            .classList.remove('hidden');


        /*
         * Reload items
         */

        await loadInvoiceItems();


        /*
         * Automatically select
         * newly created item
         */

        if (createdItem &&
            createdItem.id) {

            invoiceItems =
                invoiceItems || [];


            const exists =
                invoiceItems.some(
                    item =>
                        item.id ==
                        createdItem.id
                );


            if (!exists) {

                invoiceItems.unshift(
                    createdItem
                );

            }


            selectInvoiceItem(
                createdItem.id
            );

        }


        /*
         * Clear form
         */

        resetCreateItemForm();


    } catch (error) {

        console.error(
            'Create Item Error:',
            error
        );


        alert(
            error.message ||
            'Unable to create item.'
        );

    } finally {

        button.disabled = false;

        button.innerText =
            'Save Item';

    }

}


/* ================================
   RESET CREATE ITEM
================================ */

function resetCreateItemForm() {

    document
        .getElementById('newItemName')
        .value = '';

    document
        .getElementById('newItemCategory')
        .value = '';

    document
        .getElementById('newItemSalesPrice')
        .value = '';

    document
        .getElementById('newItemPurchasePrice')
        .value = '';

    document
        .getElementById('newItemOpeningStock')
        .value = '';

    document
        .getElementById('newItemLowStock')
        .value = '';

    document
        .getElementById('newItemCode')
        .value = '';

    document
        .getElementById('newItemHsn')
        .value = '';

    document
        .getElementById('newItemDescription')
        .value = '';

    document
        .getElementById('newItemOnlineStore')
        .checked = false;

    document
        .getElementById('newItemGst')
        .value = '';

    document
        .getElementById('newItemUnit')
        .value = 'PCS';

    newItemType = 'product';

    selectItemType('product');

    showItemTab('basic');

}

/* =========================================================
   INVOICE ADJUSTMENTS
========================================================= */

let additionalCharges = [];


/* =========================================================
   ADDITIONAL CHARGES
========================================================= */

function toggleAdditionalCharges() {

    const container =
        document.getElementById(
            'additionalChargesContainer'
        );

    if (container.classList.contains('hidden')) {

        container.classList.remove('hidden');

        if (additionalCharges.length === 0) {
            addAdditionalCharge();
        }

    } else {

        container.classList.add('hidden');

    }

}


/* =========================================================
   ADD CHARGE ROW
========================================================= */

function addAdditionalCharge() {

    additionalCharges.push({

        id: Date.now(),

        name: '',

        amount: 0

    });

    renderAdditionalCharges();

    updateInvoiceTotals();

}


/* =========================================================
   RENDER CHARGES
========================================================= */

function renderAdditionalCharges() {

    const container =
        document.getElementById(
            'additionalChargesContainer'
        );

    if (!container) {
        return;
    }


    container.innerHTML =
        additionalCharges.map(
            (charge, index) => {

                return `

                    <div
                        class="flex items-center gap-2">

                        <input
                            type="text"
                            placeholder="Charge name"
                            value="${escapeHtml(charge.name)}"
                            oninput="
                                updateAdditionalChargeName(
                                    ${charge.id},
                                    this.value
                                )
                            "
                            class="border rounded-lg
                                   px-3 py-2 flex-1">

                        <div class="flex">

                            <span
                                class="border border-r-0
                                       rounded-l-lg
                                       px-3 py-2
                                       bg-gray-50">

                                ₹

                            </span>

                            <input
                                type="number"
                                min="0"
                                step="0.01"
                                value="${charge.amount}"
                                oninput="
                                    updateAdditionalChargeAmount(
                                        ${charge.id},
                                        this.value
                                    )
                                "
                                class="border
                                       rounded-r-lg
                                       px-3 py-2
                                       w-32">

                        </div>


                        <button
                            type="button"
                            onclick="
                                removeAdditionalCharge(
                                    ${charge.id}
                                )
                            "
                            class="text-red-500 text-xl">

                            ×

                        </button>

                    </div>

                `;

            }
        ).join('');


    container.innerHTML += `

        <button
            type="button"
            onclick="addAdditionalCharge()"
            class="text-blue-500 text-sm font-medium">

            + Add Another Charge

        </button>

    `;

}


/* =========================================================
   CHARGE NAME
========================================================= */

function updateAdditionalChargeName(
    id,
    value
) {

    const charge =
        additionalCharges.find(
            item => item.id == id
        );

    if (charge) {
        charge.name = value;
    }

}


/* =========================================================
   CHARGE AMOUNT
========================================================= */

function updateAdditionalChargeAmount(
    id,
    value
) {

    const charge =
        additionalCharges.find(
            item => item.id == id
        );

    if (!charge) {
        return;
    }

    charge.amount =
        parseFloat(value) || 0;

    updateInvoiceTotals();

}


/* =========================================================
   REMOVE CHARGE
========================================================= */

function removeAdditionalCharge(id) {

    additionalCharges =
        additionalCharges.filter(
            charge => charge.id != id
        );

    renderAdditionalCharges();

    updateInvoiceTotals();

}

/* =========================================================
   DISCOUNT
========================================================= */

function toggleDiscount() {

    const container =
        document.getElementById(
            'discountContainer'
        );

    container.classList.toggle('hidden');

    updateInvoiceTotals();

}

document
    .getElementById('applyTcs')
    .addEventListener('change', function () {

        const container =
            document.getElementById(
                'tcsContainer'
            );

        if (this.checked) {

            container.classList.remove('hidden');

        } else {

            container.classList.add('hidden');

            document.getElementById(
                'tcsPercent'
            ).value = 0;

        }

        updateInvoiceTotals();

    });

    /* =========================================================
   SAVE SALES INVOICE
========================================================= */

async function saveInvoice() {

    const button = document.getElementById('saveInvoiceButton');

    // ==========================================
    // VALIDATION
    // ==========================================

    if (!selectedParty) {

        alert('Please select a party first.');

        return;
    }

    // if (!selectedInvoiceItems || selectedInvoiceItems.length === 0) {

    //     alert('Please add at least one item.');

    //     return;
    // }
    if (!billItems || billItems.length === 0) {

    alert('Please add at least one item.');

    return;
}


    const invoiceNumber =
        document.getElementById('invoiceNumber').value.trim();

    const invoiceDate =
        document.getElementById('invoiceDate').value;

    const dueDate =
    document.getElementById('dueDate')?.value || null;    


    if (!invoiceNumber) {

        alert('Please enter invoice number.');

        return;
    }


    if (!invoiceDate) {

        alert('Please select invoice date.');

        return;
    }


    // ==========================================
    // DISABLE BUTTON
    // ==========================================

    button.disabled = true;

    button.innerText = 'Saving...';


    try {

        const token =
            localStorage.getItem('token');


        // ==========================================
        // ITEMS
        // ==========================================

        // const items = selectedInvoiceItems.map(item => {
        const items = billItems.map(item => {

            const qty =
                parseFloat(item.quantity || 1);

            const price =
                parseFloat(item.sales_price || 0);

            const discount =
                parseFloat(item.discount || 0);

            const gst =
                parseFloat(item.gst_percent || 0);


            return {

                item_id: item.id,

                description:
                    item.description ||
                    item.name ||
                    '',

                qty: qty,

                unit:
                    item.unit ||
                    'PCS',

                price: price,

                discount: discount,

                gst_percent: gst

            };

        });


       // ==========================================
// ADDITIONAL CHARGES
// ==========================================

// IMPORTANT:
// Use the global additionalCharges array directly.
// Do NOT use window.additionalCharges.
const additionalChargesForSave =
    additionalCharges.map(charge => ({
        name: charge.name || '',
        amount: parseFloat(charge.amount) || 0
    }));

        // ==========================================
        // DISCOUNT
        // ==========================================

        const discountPercent =
            parseFloat(
                document.getElementById(
                    'invoiceDiscountPercent'
                )?.value || 0
            );


        // ==========================================
        // ROUND OFF
        // ==========================================

        let roundOff = 0;

        const autoRound =
            document.getElementById(
                'autoRoundOff'
            )?.checked;


        if (autoRound) {

            const roundValue =
                parseFloat(
                    document.getElementById(
                        'roundOffAmount'
                    )?.value || 0
                );


            const roundType =
                document.getElementById(
                    'roundOffType'
                )?.value;


            if (roundType === 'add') {

                roundOff = roundValue;

            } else {

                roundOff = -roundValue;

            }

        }


        // ==========================================
        // TCS
        // ==========================================

        let tcsAmount = 0;


        const applyTcs =
            document.getElementById(
                'applyTcs'
            )?.checked;


        if (applyTcs) {

            const tcsPercent =
                parseFloat(
                    document.getElementById(
                        'tcsPercent'
                    )?.value || 0
                );


            // Calculate from current invoice total
            // if your existing total calculation is available,
            // use that value here.

            const base =
                calculateInvoiceBaseAmount();


            tcsAmount =
                base * tcsPercent / 100;

        }


        // ==========================================
        // RECEIVED AMOUNT
        // ==========================================

        const receivedAmount =
            parseFloat(
                document.getElementById(
                    'receivedAmount'
                )?.value || 0
            );


        // ==========================================
        // NOTES
        // ==========================================

        const notes =
            document.getElementById(
                'invoiceNotes'
            )?.value || '';


        // ==========================================
        // CALCULATE TOTAL
        // ==========================================

        let subtotal = 0;

        let totalTax = 0;


        items.forEach(item => {

            const itemSubtotal =
                item.qty * item.price;

            const itemDiscount =
                item.discount;

            const taxable =
                itemSubtotal - itemDiscount;

            const tax =
                taxable *
                item.gst_percent /
                100;

            subtotal += taxable;

            totalTax += tax;

        });


        subtotal =
            parseFloat(
                subtotal.toFixed(2)
            );


        totalTax =
            parseFloat(
                totalTax.toFixed(2)
            );


        const additionalTotal =
            additionalCharges.reduce(
                (sum, charge) => {

                    return sum +
                        (
                            parseFloat(
                                charge.amount
                            ) || 0
                        );

                },
                0
            );


        const discountAmount =
            (
                subtotal +
                totalTax +
                additionalTotal
            ) *
            discountPercent /
            100;


        const grandTotal =
            parseFloat(
                (
                    subtotal +
                    totalTax +
                    additionalTotal -
                    discountAmount +
                    roundOff +
                    tcsAmount
                ).toFixed(2)
            );


        const balanceAmount =
            parseFloat(
                (
                    grandTotal -
                    receivedAmount
                ).toFixed(2)
            );


        // ==========================================
        // PAYMENT MODE
        // ==========================================

        const paymentMode =
            document.querySelector(
                '#receivedAmount + select'
            )?.value || null;


        // ==========================================
        // API BODY
        // ==========================================

        const payload = {

            invoice_number:
                invoiceNumber,

            invoice_date:
                invoiceDate,

            due_date:
                dueDate,

            party_id:
                selectedParty.id,

            place_of_supply:
                selectedParty.billing_state ||
                null,

            subtotal:
                subtotal,

            additional_charges:
    additionalChargesForSave
        .filter(charge =>
            charge.name &&
            charge.amount > 0
        ),

            discount_percent:
                discountPercent,

            discount_amount:
                parseFloat(
                    discountAmount.toFixed(2)
                ),

            round_off:
                roundOff,

            tcs_amount:
                tcsAmount,

            received_amount:
                receivedAmount,

            balance_amount:
                balanceAmount,

            payment_mode:
                paymentMode,

            notes:
                notes,

            grand_total:
                grandTotal,

            items:
                items

        };


        console.log(
            'Saving Invoice:',
            payload
        );


        // ==========================================
        // POST API
        // ==========================================

        const response =
            await fetch(
                '/api/invoices',
                {
                    method: 'POST',

                    headers: {

                        'Content-Type':
                            'application/json',

                        'Accept':
                            'application/json',

                        'Authorization':
                            `Bearer ${token}`

                    },

                    body:
                        JSON.stringify(
                            payload
                        )

                }
            );


        const result =
            await response.json();


        console.log(
            'Invoice Response:',
            result
        );


        // ==========================================
        // ERROR
        // ==========================================

        if (!response.ok) {

            let message =
                result.message ||
                'Unable to create invoice.';


            if (result.errors) {

                message =
                    Object.values(
                        result.errors
                    )
                    .flat()
                    .join('\n');

            }


            throw new Error(message);

        }


        // ==========================================
        // SUCCESS
        // ==========================================

        if (
            result.success &&
            result.data
        ) {

            const invoice =
                result.data;


            // Open invoice preview
            window.location.href =
                '/invoice-preview/' +
                invoice.id;

        } else {

            throw new Error(
                'Invoice was not created.'
            );

        }


    } catch (error) {

        console.error(
            'Invoice Save Error:',
            error
        );


        alert(
            error.message ||
            'Unable to save invoice.'
        );


    } finally {

        button.disabled = false;

        button.innerText = 'Save';

    }

}

// ==========================================
// NOTES
// ==========================================

function toggleNotes() {

    const container =
        document.getElementById('notesContainer');

    const button =
        document.getElementById('addNotesBtn');

    container.classList.remove('hidden');

    button.classList.add('hidden');
}


function removeNotes() {

    const container =
        document.getElementById('notesContainer');

    const button =
        document.getElementById('addNotesBtn');

    const notes =
        document.getElementById('invoiceNotes');

    notes.value = '';

    container.classList.add('hidden');

    button.classList.remove('hidden');
}


</script>

<script>

document.addEventListener('DOMContentLoaded', function () {

    const invoiceNumberInput =
        document.getElementById('invoiceNumber');

    const invoiceDateInput =
        document.getElementById('invoiceDate');

    const addDueDateBtn =
        document.getElementById('addDueDateBtn');

    const dueDateContainer =
        document.getElementById('dueDateContainer');

    const dueDateInput =
        document.getElementById('dueDate');

    const removeDueDateBtn =
        document.getElementById('removeDueDateBtn');


    // ==========================================
    // SET TODAY'S DATE
    // ==========================================

    const today = new Date();

    const year = today.getFullYear();

    const month = String(
        today.getMonth() + 1
    ).padStart(2, '0');

    const day = String(
        today.getDate()
    ).padStart(2, '0');

    const todayDate =
        `${year}-${month}-${day}`;


    invoiceDateInput.value = todayDate;


    // ==========================================
    // LOAD NEXT INVOICE NUMBER
    // ==========================================

    loadNextInvoiceNumber();


    async function loadNextInvoiceNumber() {

        try {

            const response = await fetch(
                '/api/invoices/last-number',
                {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'Authorization':
                            'Bearer ' + localStorage.getItem('token')
                    }
                }
            );

            if (!response.ok) {
                throw new Error(
                    'Unable to load invoice number'
                );
            }

            const data = await response.json();

            const lastNumber =
                parseInt(data.last_number || 0);

            const nextNumber =
                lastNumber + 1;

            invoiceNumberInput.value = 'INV-' + nextNumber;

        } catch (error) {

            console.error(
                'Invoice number error:',
                error
            );

            // fallback
            invoiceNumberInput.value = '1';
        }
    }


    // ==========================================
    // ADD DUE DATE
    // ==========================================

    addDueDateBtn.addEventListener(
        'click',
        function () {

            dueDateContainer.classList.remove('hidden');

            addDueDateBtn.classList.add('hidden');

            // Default due date = today
            dueDateInput.value = todayDate;
        }
    );


    // ==========================================
    // REMOVE DUE DATE
    // ==========================================

    removeDueDateBtn.addEventListener(
        'click',
        function () {

            dueDateInput.value = '';

            dueDateContainer.classList.add('hidden');

            addDueDateBtn.classList.remove('hidden');
        }
    );

});

</script>


</body>
</html>