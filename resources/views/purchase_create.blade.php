<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create Purchase Invoice | BillNika</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif; }
        .modal-backdrop { background: rgba(15,23,42,.48); }
        .table-input { width:100%; border:1px solid #e5e7eb; border-radius:6px; padding:7px 8px; font-size:13px; }
        .table-input:focus { border-color:#6366f1; box-shadow:0 0 0 2px rgba(99,102,241,.12); outline:none; }
    </style>
</head>
<body class="bg-gray-100 text-gray-800">
<script>if(!localStorage.getItem('token')) window.location.href='/start-free-billing';</script>
<div class="min-h-screen">

<main class="w-full min-w-0">
<header class="h-16 bg-white border-b flex items-center justify-between px-5 sticky top-0 z-20">
    <div class="flex items-center gap-4"><a href="{{ route('purchases') }}" class="border rounded-lg px-4 py-2 text-sm hover:bg-gray-50">← Exit</a><h1 class="font-semibold text-gray-900">Create Purchase Invoice</h1></div>
    <div class="flex items-center gap-2">
        <button id="editModeBtn" type="button" class="px-4 py-2 rounded-lg bg-indigo-50 text-indigo-600 text-sm font-medium">✎ Edit Mode</button>
        <button id="previewModeBtn" type="button" class="px-4 py-2 rounded-lg text-gray-400 text-sm">◉ Preview Mode</button>
        <button type="button" class="border rounded-lg px-3 py-2">⚙</button>
        <button id="saveNewBtn" type="button" class="border rounded-lg px-4 py-2 text-sm hover:bg-gray-50">Save &amp; New</button>
        <button id="saveBtn" type="button" class="bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2 rounded-lg text-sm font-medium">Save</button>
    </div>
</header>
<div class="p-2 md:p-4">
<section class="bg-white border rounded-lg overflow-hidden">
<div class="grid grid-cols-1 lg:grid-cols-5">
<div class="lg:col-span-3 border-b lg:border-b-0 lg:border-r">
    <div class="px-4 py-3 border-b text-xs font-semibold text-gray-500">Bill From</div>
    
    <div class="p-4">

    <!-- ADD PARTY BUTTON -->
    <button
        id="addPartyBtn"
        type="button"
        class="w-full md:w-56 h-32 border-2 border-dashed border-blue-300 rounded-lg text-blue-600 hover:bg-blue-50 flex flex-col items-center justify-center gap-2"
    >
        <span class="text-2xl">+</span>
        <span>Add Party</span>
    </button>

    <!-- PARTY SELECTOR -->
    <div id="partySelector" class="hidden">

        <!-- Search -->
        <div class="relative">
            <input
                id="partySearchInline"
                type="text"
                placeholder="Search party by name or number"
                autocomplete="off"
                class="w-full border border-indigo-400 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-200"
            >
        </div>

        <!-- Party list -->
        <div class="border border-gray-200 border-t-0 rounded-b-lg bg-white">

            <!-- Header -->
            <div class="grid grid-cols-[1fr_120px] px-3 py-2 bg-gray-50 text-xs font-medium text-gray-700">
                <span>Party Name</span>
                <span class="text-right">Balance</span>
            </div>

            <!-- Dynamic party records -->
            <div
                id="partyInlineList"
                class="max-h-48 overflow-y-auto"
            ></div>

            <!-- Create Party -->
            <button
                id="createPartyBtn"
                type="button"
                class="w-full border-t border-dashed border-blue-300 py-2 text-sm text-blue-600 hover:bg-blue-50"
            >
                + Create Party
            </button>

        </div>

    </div>

    <!-- SELECTED PARTY -->
    <div
        id="selectedPartyCard"
        class="hidden border rounded-lg p-4 bg-indigo-50 border-indigo-200"
    >
        <div class="flex items-start justify-between gap-3">

            <div>
                <div
                    id="selectedPartyName"
                    class="font-semibold text-gray-900"
                ></div>

                <div
                    id="selectedPartyPhone"
                    class="text-sm text-gray-600 mt-1"
                ></div>

                <div
                    id="selectedPartyGst"
                    class="text-sm text-gray-600 mt-1"
                ></div>

                <div
                    id="selectedPartyAddress"
                    class="text-sm text-gray-600 mt-1"
                ></div>
            </div>

            <button
                id="changePartyBtn"
                type="button"
                class="text-indigo-600 text-sm font-medium"
            >
                Change
            </button>

        </div>
    </div>


    </div>
</div>
<div class="lg:col-span-2">
    <div class="px-4 py-3 border-b text-xs font-semibold text-gray-500">Invoice Details</div>
    <div class="p-4 grid grid-cols-1 md:grid-cols-3 gap-3">
        <div><label class="block text-xs text-gray-500 mb-1">Purchase Invoice No.</label><input id="purchaseNumber" readonly class="w-full border rounded-lg px-3 py-2 text-sm"></div>
        <div><label class="block text-xs text-gray-500 mb-1">Purchase Invoice Date</label><input id="purchaseDate" type="date" class="w-full border rounded-lg px-3 py-2 text-sm"></div>
        <div><label class="block text-xs text-gray-500 mb-1">Original Invoice No.</label><input id="originalInvoiceNo" placeholder="Original Invoice No." class="w-full border rounded-lg px-3 py-2 text-sm"></div>
        <div class="md:col-span-3"><button id="dueDateToggle" type="button" class="w-full border-2 border-dashed border-blue-300 rounded-lg py-2 text-blue-600 hover:bg-blue-50">+ Add Due Date</button><input id="dueDate" type="date" class="hidden w-full border rounded-lg px-3 py-2 text-sm mt-2"></div>
    </div>
</div></div></div></section>
<section class="bg-white border rounded-lg mt-2 overflow-hidden"><div class="overflow-x-auto">
<table class="w-full min-w-[1050px] text-sm"><thead class="bg-gray-50 border-b"><tr class="text-left text-xs text-gray-500">
<th class="px-3 py-3 w-12">NO</th><th class="px-3 py-3 min-w-[260px]">ITEMS</th><th class="px-3 py-3 w-28">HSN</th><th class="px-3 py-3 w-24">QTY</th><th class="px-3 py-3 w-32">PRICE/ITEM (₹)</th><th class="px-3 py-3 w-32">DISCOUNT</th><th class="px-3 py-3 w-28">TAX</th><th class="px-3 py-3 w-32">AMOUNT (₹)</th><th class="px-3 py-3 w-10"></th>
</tr></thead><tbody id="itemsBody"></tbody><tfoot class="border-t bg-gray-50"><tr><td colspan="3"></td><td class="px-3 py-3 font-semibold">Subtotal</td><td></td><td></td><td></td><td id="subtotalDisplay" class="px-3 py-3 font-semibold">₹ 0.00</td><td></td></tr></tfoot></table></div></section>
<section class="bg-white border rounded-lg mt-2"><div class="grid grid-cols-1 lg:grid-cols-5">
<div class="lg:col-span-3 border-b lg:border-b-0 lg:border-r">
    <button id="notesToggle" type="button" class="px-4 py-4 text-blue-600 text-sm font-medium">📝 Add Notes</button>
    <div id="notesBox" class="hidden px-4 pb-4"><textarea id="notes" rows="3" class="w-full border rounded-lg p-3 text-sm" placeholder="Enter notes"></textarea></div>
    <div class="border-t px-4 py-3"><span class="text-xs font-semibold text-gray-500">Terms &amp; Conditions</span><textarea id="terms" rows="3" class="w-full border rounded-lg p-3 text-sm text-gray-600 mt-2">1. Goods once sold will not be taken back or exchanged
2. All disputes are subject to [ENTER_YOUR_CITY_NAME] jurisdiction only</textarea></div>
</div>


<div class="lg:col-span-2 border-l">
    <div class="p-4">

        <!-- ========================================= -->
        <!-- ADDITIONAL CHARGES -->
        <!-- ========================================= -->

        <div>

            <button
                id="addChargeBtn"
                type="button"
                class="flex items-center gap-1
                       text-blue-600
                       text-sm
                       font-medium
                       mb-3"
            >
                ⊕ Add Additional Charges
            </button>

            <!-- Charges will appear here -->

            <div
                id="chargesList"
                class="space-y-2 mb-3"
            ></div>

        </div>


        <!-- ========================================= -->
        <!-- TAXABLE AMOUNT -->
        <!-- ========================================= -->

        <div
            class="flex items-center
                   justify-between
                   text-sm
                   py-2"
        >

            <span>
                Taxable Amount
            </span>

            <span
                id="taxableAmount"
            >
                ₹ 0.00
            </span>

        </div>


        <!-- ========================================= -->
        <!-- DISCOUNT -->
        <!-- ========================================= -->

        <div>

            <div
                class="flex items-center
                       justify-between
                       text-sm
                       py-2"
            >

                <button
                    id="discountBtn"
                    type="button"
                    class="text-blue-600
                           font-medium"
                >
                    ⊘ Add Discount
                </button>

                <span
                    id="discountDisplay"
                >
                    ₹ 0.00
                </span>

            </div>


           <!-- DISCOUNT EDITOR -->
<div
    id="discountEditor"
    class="hidden pb-3"
>
    <input
        id="discountPercent"
        type="number"
        min="0"
        max="100"
        step="0.01"
        placeholder="Discount %"
        class="w-full
               border
               rounded-lg
               px-3
               py-2
               text-sm"
    >
</div>

        </div>


        <!-- ========================================= -->
        <!-- AUTO ROUND OFF -->
        <!-- ========================================= -->

        <div
            class="border-b
                   pb-3
                   pt-1"
        >

            <label
                class="flex items-center
                       gap-2
                       text-sm
                       cursor-pointer"
            >

                <input
                    id="autoRoundOff"
                    type="checkbox"
                    checked
                    class="w-4 h-4
                           accent-indigo-600"
                >

                <span>
                    Auto Round Off
                </span>

            </label>


            <!-- MANUAL ROUND OFF -->

            <div
                id="manualRoundWrap"
                class="hidden mt-3"
            >

                <label
                    class="block
                           text-xs
                           text-gray-500
                           mb-1"
                >
                    Round Off
                </label>

                <input
                    id="roundOff"
                    type="number"
                    step="0.01"
                    value="0"
                    class="w-full
                           border
                           rounded-lg
                           px-3
                           py-2
                           text-sm"
                >

            </div>

        </div>


        <!-- ========================================= -->
        <!-- TOTAL AMOUNT -->
        <!-- ========================================= -->

        <div
            class="flex items-center
                   justify-between
                   py-4"
        >

            <span
                class="text-lg
                       font-semibold"
            >
                Total Amount:
            </span>

            <span
                id="grandTotalDisplay"
                class="text-lg
                       font-semibold"
            >
                ₹ 0.00
            </span>

        </div>


        <!-- ========================================= -->
        <!-- PAYMENT AMOUNT -->
        <!-- ========================================= -->

        <div
            class="flex items-center
                   border
                   rounded-lg
                   overflow-hidden
                   mb-3"
        >

            <span
                class="px-3
                       text-sm
                       text-gray-500"
            >
                ₹
            </span>

            <input
                id="receivedAmount"
                type="number"
                min="0"
                step="0.01"
                placeholder="Enter payment amount"
                class="flex-1
                       border-0
                       px-2
                       py-3
                       text-sm
                       focus:outline-none"
            >

        </div>


        <!-- ========================================= -->
        <!-- TOTAL AMOUNT PAID -->
        <!-- ========================================= -->

        <div
            class="flex items-center
                   justify-between
                   text-sm
                   mb-2"
        >

            <span>
                Total Amount Paid
            </span>

            <span
                id="receivedDisplay"
            >
                ₹ 0.00
            </span>

        </div>


        <!-- ========================================= -->
        <!-- PAYMENT MODE -->
        <!-- ========================================= -->

        <select
            id="paymentModeSelect"
            class="w-full
                   border
                   rounded-lg
                   px-3
                   py-3
                   text-sm
                   mb-3"
        >

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

            <option value="Other">
                Other
            </option>

        </select>


        <!-- ========================================= -->
        <!-- FULLY PAID -->
        <!-- ========================================= -->

        <label
            class="flex items-center
                   gap-2
                   text-sm
                   pb-3"
        >

            <input
                id="markFullyPaid"
                type="checkbox"
                class="w-4 h-4
                       accent-indigo-600"
            >

            <span>
                Mark as fully paid
            </span>

        </label>


        <!-- ========================================= -->
        <!-- BALANCE -->
        <!-- ========================================= -->

        <div
            class="border-t
                   pt-4
                   flex items-center
                   justify-between
                   text-sm"
        >

            <span
                class="font-semibold"
            >
                Balance Amount
            </span>

            <span
                id="balanceDisplay"
                class="font-semibold
                       text-green-600"
            >
                ₹ 0.00
            </span>

        </div>


       

    </div>
</div>
</div></section>
</div></main></div>

<!-- PARTY MODAL -->

<!-- CREATE PARTY MODAL -->
<!-- ===================================================== -->

<div
    id="createPartyModal"
    class="hidden fixed inset-0 z-[70] bg-black/50
           flex items-center justify-center p-4"
>

    <div
        class="bg-white rounded-xl shadow-2xl
               w-full max-w-md
               max-h-[85vh]
               overflow-hidden"
    >

        <!-- HEADER -->
        <div
            class="flex items-center justify-between
                   px-5 py-4 border-b"
        >

            <h2 class="text-lg font-semibold text-gray-800">
                Create New Party
            </h2>

            <button
                type="button"
                id="closeCreatePartyBtn"
                class="border border-gray-300
                       rounded-md px-3 py-1
                       text-xl text-gray-600
                       hover:bg-gray-50"
            >
                ×
            </button>

        </div>


        <!-- SCROLLABLE BODY -->
        <div class="overflow-y-auto max-h-[65vh]">

            <div class="p-5">

                <!-- PARTY NAME -->
                <div class="mb-4">

                    <label class="block text-sm text-gray-600 mb-2">
                        Party Name
                        <span class="text-red-500">*</span>
                    </label>

                    <input
                        id="newPartyName"
                        type="text"
                        placeholder="Enter party name"
                        class="w-full border border-gray-300
                               rounded-md px-3 py-2.5 text-sm
                               focus:outline-none
                               focus:ring-2 focus:ring-indigo-200
                               focus:border-indigo-400"
                    >

                </div>


                <!-- MOBILE -->
                <div class="mb-4">

                    <label class="block text-sm text-gray-600 mb-2">
                        Mobile Number
                    </label>

                    <input
                        id="newPartyMobile"
                        type="text"
                        maxlength="20"
                        placeholder="Enter Mobile Number"
                        class="w-full border border-gray-300
                               rounded-md px-3 py-2.5 text-sm
                               focus:outline-none
                               focus:ring-2 focus:ring-indigo-200
                               focus:border-indigo-400"
                    >

                </div>


                <!-- ADD ADDRESS -->
                <div class="mb-4">

                    <button
                        type="button"
                        id="togglePartyAddressBtn"
                        class="text-blue-500 text-sm font-medium"
                    >
                        + Add Address (Optional)
                    </button>

                </div>


                <!-- ADDRESS FIELDS -->
                <div
                    id="partyAddressFields"
                    class="hidden mb-4"
                >

                    <div
                        class="flex items-center justify-between
                               bg-gray-50 px-3 py-2 mb-3"
                    >

                        <span class="text-sm text-gray-700">
                            Address (Optional)
                        </span>

                        <button
                            type="button"
                            id="removePartyAddressBtn"
                            class="text-red-500 text-xs"
                        >
                            Remove
                        </button>

                    </div>


                    <!-- BILLING ADDRESS -->
                    <div class="mb-3">

                        <label class="block text-xs text-gray-500 mb-1">
                            BILLING ADDRESS
                        </label>

                        <textarea
                            id="partyStreet"
                            rows="3"
                            placeholder="Enter billing address"
                            class="w-full border border-gray-300
                                   rounded-md px-3 py-2.5 text-sm
                                   resize-none
                                   focus:outline-none
                                   focus:ring-2 focus:ring-indigo-200"
                        ></textarea>

                    </div>


                    <!-- STATE + PINCODE -->
                    <div class="grid grid-cols-2 gap-2 mb-3">

                        <div>

                            <label class="block text-xs text-gray-500 mb-1">
                                STATE
                            </label>

                            <input
                                id="partyState"
                                type="text"
                                placeholder="Enter State"
                                class="w-full border border-gray-300
                                       rounded-md px-3 py-2.5 text-sm"
                            >

                        </div>


                        <div>

                            <label class="block text-xs text-gray-500 mb-1">
                                PINCODE
                            </label>

                            <input
                                id="partyPincode"
                                type="text"
                                maxlength="10"
                                placeholder="Enter Pincode"
                                class="w-full border border-gray-300
                                       rounded-md px-3 py-2.5 text-sm"
                            >

                        </div>

                    </div>


                    <!-- CITY -->
                    <div>

                        <label class="block text-xs text-gray-500 mb-1">
                            CITY
                        </label>

                        <input
                            id="partyCity"
                            type="text"
                            placeholder="Enter City"
                            class="w-full border border-gray-300
                                   rounded-md px-3 py-2.5 text-sm"
                        >

                    </div>

                </div>


                <!-- ADD GSTIN -->
                <div class="mb-4">

                    <button
                        type="button"
                        id="togglePartyGSTBtn"
                        class="text-blue-500 text-sm font-medium"
                    >
                        + Add GSTIN (Optional)
                    </button>

                </div>


                <!-- GSTIN -->
                <div
                    id="partyGSTFields"
                    class="hidden mb-4"
                >

                    <div
                        class="flex items-center justify-between
                               bg-gray-50 px-3 py-2 mb-3"
                    >

                        <span class="text-sm text-gray-700">
                            GSTIN (Optional)
                        </span>

                        <button
                            type="button"
                            id="removePartyGSTBtn"
                            class="text-red-500 text-xs"
                        >
                            Remove
                        </button>

                    </div>

                    <label class="block text-xs text-gray-500 mb-1">
                        GSTIN
                    </label>

                    <input
                        id="newPartyGST"
                        type="text"
                        maxlength="15"
                        placeholder="ex: 29XXXXX9438X1XX"
                        class="w-full border border-gray-300
                               rounded-md px-3 py-2.5 text-sm
                               uppercase"
                    >

                </div>


                <!-- INFO -->
                <div
                    class="bg-amber-50
                           rounded-md
                           px-3 py-3
                           text-center
                           text-xs
                           text-gray-600"
                >
                    You can add Custom Fields from
                    <span class="text-blue-500">
                        Party Settings
                    </span>
                </div>

            </div>

        </div>


        <!-- FOOTER -->
        <div
            class="flex justify-end gap-3
                   px-5 py-3 border-t
                   bg-white"
        >

            <button
                type="button"
                id="cancelCreatePartyBtn"
                class="border border-gray-300
                       px-5 py-2 rounded-md
                       text-sm text-gray-600
                       hover:bg-gray-50"
            >
                Cancel
            </button>


            <button
                type="button"
                id="savePartyButton"
                class="bg-indigo-600
                       hover:bg-indigo-700
                       text-white
                       px-6 py-2
                       rounded-md
                       text-sm
                       font-medium"
            >
                Save
            </button>

        </div>

    </div>

</div>

<!-- ===================================================== -->
<!-- CREATE NEW ITEM MODAL -->
<!-- ===================================================== -->

<div
    id="createItemModal"
    class="hidden fixed inset-0 z-[80] bg-black/50
           flex items-center justify-center p-4"
>
    <div
        class="bg-white rounded-xl shadow-2xl
               w-full max-w-5xl
               max-h-[90vh]
               overflow-hidden
               flex flex-col"
    >

        <!-- HEADER -->

        <div
            class="flex items-center justify-between
                   px-5 py-4 border-b"
        >

            <h2 class="text-xl font-semibold text-gray-800">
                Create New Item
            </h2>

            <button
                type="button"
                id="closeCreateItemBtn"
                class="border border-gray-300
                       rounded-md px-3 py-1
                       text-xl text-gray-600
                       hover:bg-gray-50"
            >
                ×
            </button>

        </div>


        <!-- BODY -->

        <div
            class="flex-1 overflow-y-auto p-5"
        >

            <div
                class="grid grid-cols-1 md:grid-cols-[225px_1fr]
                       gap-4"
            >

                <!-- ================================= -->
                <!-- LEFT MENU -->
                <!-- ================================= -->

                <div
                    class="border rounded-lg overflow-hidden
                           h-fit"
                >

                    <!-- BASIC -->

                    <button
                        type="button"
                        class="create-item-tab active
                               w-full text-left
                               px-4 py-4
                               border-b
                               bg-indigo-50
                               text-indigo-600
                               font-medium"
                        data-tab="basic"
                    >
                        🗂️ &nbsp; Basic Details
                        <span class="text-red-500">*</span>
                    </button>


                    <div
                        class="px-4 py-3
                               text-sm
                               font-semibold
                               text-gray-700
                               border-b"
                    >
                        Advance Details
                    </div>


                    <!-- STOCK -->

                    <button
                        type="button"
                        class="create-item-tab
                               w-full text-left
                               px-4 py-4
                               text-gray-600
                               hover:bg-gray-50
                               border-b"
                        data-tab="stock"
                    >
                        📦 &nbsp; Stock Details
                    </button>


                    <!-- PRICING -->

                    <button
                        type="button"
                        class="create-item-tab
                               w-full text-left
                               px-4 py-4
                               text-gray-600
                               hover:bg-gray-50
                               border-b"
                        data-tab="pricing"
                    >
                        🧾 &nbsp; Pricing Details
                    </button>


                    <!-- PARTY -->

                    <button
                        type="button"
                        class="create-item-tab
                               w-full text-left
                               px-4 py-4
                               text-gray-600
                               hover:bg-gray-50
                               border-b"
                        data-tab="party"
                    >
                        🧾 &nbsp; Party Wise Prices
                    </button>


                    <!-- CUSTOM -->

                    <button
                        type="button"
                        class="create-item-tab
                               w-full text-left
                               px-4 py-4
                               text-gray-600
                               hover:bg-gray-50"
                        data-tab="custom"
                    >
                        ▤ &nbsp; Custom Fields
                    </button>

                </div>


                <!-- ================================= -->
                <!-- RIGHT CONTENT -->
                <!-- ================================= -->

                <div
                    class="border rounded-lg p-5"
                >

                    <!-- ================================= -->
                    <!-- BASIC DETAILS -->
                    <!-- ================================= -->

                    <div
                        id="createItemTabBasic"
                        class="create-item-panel"
                    >

                        <div
                            class="grid grid-cols-1 md:grid-cols-2
                                   gap-4"
                        >

                            <!-- ITEM TYPE -->

                            <div class="md:col-span-2">

                                <label
                                    class="block text-sm
                                           text-gray-500 mb-2"
                                >
                                    Item Type
                                    <span class="text-red-500">*</span>
                                </label>

                                <div class="grid grid-cols-2 gap-3">

                                    <label
                                        class="border rounded-lg
                                               px-4 py-3
                                               flex items-center
                                               justify-between
                                               cursor-pointer"
                                    >
                                        <span>Product</span>

                                        <input
                                            type="radio"
                                            name="newItemType"
                                            value="product"
                                            checked
                                            class="w-5 h-5 accent-indigo-600"
                                        >
                                    </label>


                                    <label
                                        class="border rounded-lg
                                               px-4 py-3
                                               flex items-center
                                               justify-between
                                               cursor-pointer"
                                    >
                                        <span>Service</span>

                                        <input
                                            type="radio"
                                            name="newItemType"
                                            value="service"
                                            class="w-5 h-5 accent-indigo-600"
                                        >
                                    </label>

                                </div>

                            </div>


                            <!-- CATEGORY -->

                            <div>

                                <label
                                    class="block text-sm
                                           text-gray-500 mb-2"
                                >
                                    Category
                                </label>

                                <select
                                    id="newItemCategory"
                                    class="w-full border
                                           rounded-lg px-3 py-3"
                                >
                                    <option value="">
                                        Search Categories
                                    </option>
                                </select>

                            </div>


                            <!-- ONLINE STORE -->

                            <div>

                                <label
                                    class="block text-sm
                                           text-gray-500 mb-2"
                                >
                                    Show Item in Online Store
                                </label>

                                <label
                                    class="flex items-center
                                           gap-3 pt-3"
                                >
                                    <input
                                        type="checkbox"
                                        id="newItemOnlineStore"
                                        class="w-5 h-5
                                               accent-indigo-600"
                                    >

                                    <span class="text-sm">
                                        Enable
                                    </span>

                                </label>

                            </div>


                            <!-- ITEM NAME -->

                            <div class="md:col-span-2">

                                <label
                                    class="block text-sm
                                           text-gray-500 mb-2"
                                >
                                    Item Name
                                    <span class="text-red-500">*</span>
                                </label>

                                <input
                                    id="newItemName"
                                    type="text"
                                    placeholder="ex: Maggie 20gm"
                                    class="w-full border
                                           border-indigo-300
                                           rounded-lg px-3 py-3
                                           focus:outline-none
                                           focus:ring-2
                                           focus:ring-indigo-200"
                                >

                            </div>


                            <!-- PURCHASE PRICE -->

                            <div>

                                <label
                                    class="block text-sm
                                           text-gray-500 mb-2"
                                >
                                    Purchase Price
                                </label>

                                <div class="flex">

                                    <span
                                        class="border border-r-0
                                               rounded-l-lg
                                               px-4 py-3
                                               text-gray-500"
                                    >
                                        ₹
                                    </span>

                                    <input
                                        id="newItemPurchasePrice"
                                        type="number"
                                        min="0"
                                        step="0.01"
                                        placeholder="ex: 200"
                                        class="flex-1 border
                                               rounded-r-lg
                                               px-3 py-3"
                                    >

                                </div>

                            </div>


                            <!-- GST -->

                            <div>

                                <label
                                    class="block text-sm
                                           text-gray-500 mb-2"
                                >
                                    GST Tax Rate(%)
                                </label>

                                <select
                                    id="newItemGst"
                                    class="w-full border
                                           rounded-lg px-3 py-3"
                                >
                                    <option value="0">None</option>
                                    <option value="0">0%</option>
                                    <option value="5">5%</option>
                                    <option value="12">12%</option>
                                    <option value="18">18%</option>
                                    <option value="28">28%</option>
                                </select>

                            </div>


                            <!-- MEASURING UNIT -->

                            <div>

                                <label
                                    class="block text-sm
                                           text-gray-500 mb-2"
                                >
                                    Measuring Unit
                                </label>

                                <select
                                    id="newItemUnit"
                                    class="w-full border
                                           rounded-lg px-3 py-3"
                                >
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

                                </select>

                            </div>


                            <!-- OPENING STOCK -->

                            <div>

                                <label
                                    class="block text-sm
                                           text-gray-500 mb-2"
                                >
                                    Opening Stock
                                </label>

                                <div class="flex">

                                    <input
                                        id="newItemOpeningStock"
                                        type="number"
                                        min="0"
                                        step="0.01"
                                        placeholder="ex: 150 PCS"
                                        class="flex-1 border
                                               rounded-l-lg
                                               px-3 py-3"
                                    >

                                    <span
                                        id="newItemUnitLabel"
                                        class="border border-l-0
                                               rounded-r-lg
                                               px-4 py-3
                                               text-gray-500"
                                    >
                                        PCS
                                    </span>

                                </div>

                            </div>

                        </div>

                    </div>


                    <!-- ================================= -->
                    <!-- STOCK DETAILS -->
                    <!-- ================================= -->

                    <div
                        id="createItemTabStock"
                        class="create-item-panel hidden"
                    >

                        <div
                            class="grid grid-cols-1 md:grid-cols-2
                                   gap-4"
                        >

                            <!-- ITEM CODE -->

                            <div>

                                <label
                                    class="block text-sm
                                           text-gray-500 mb-2"
                                >
                                    Item Code
                                </label>

                                <div class="flex">

                                    <input
                                        id="newItemCode"
                                        type="text"
                                        placeholder="ex: ITM12549"
                                        class="flex-1 border
                                               rounded-l-lg
                                               px-3 py-3"
                                    >

                                    <button
                                        type="button"
                                        id="generateBarcodeBtn"
                                        class="border border-l-0
                                               bg-blue-50
                                               text-blue-600
                                               px-4 rounded-r-lg"
                                    >
                                        Generate Barcode
                                    </button>

                                </div>

                            </div>


                            <!-- HSN -->

                            <div>

                                <label
                                    class="block text-sm
                                           text-gray-500 mb-2"
                                >
                                    HSN Code
                                </label>

                                <input
                                    id="newItemHsn"
                                    type="text"
                                    placeholder="ex: 4010"
                                    class="w-full border
                                           rounded-lg px-3 py-3"
                                >

                                <div
                                    class="text-blue-600
                                           text-sm mt-2"
                                >
                                    Find HSN Code
                                </div>

                            </div>


                            <!-- UNIT -->

                            <div>

                                <label
                                    class="block text-sm
                                           text-gray-500 mb-2"
                                >
                                    Measuring Unit
                                </label>

                                <select
                                    id="newItemStockUnit"
                                    class="w-full border
                                           rounded-lg px-3 py-3"
                                >
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

                                </select>

                            </div>


                            <!-- ALTERNATIVE UNIT -->

                            <div
                                class="flex items-end"
                            >

                                <button
                                    type="button"
                                    class="text-blue-600
                                           text-sm"
                                    onclick="
                                        toast(
                                            'Alternative unit can be configured later.'
                                        )
                                    "
                                >
                                    + Alternative Unit
                                </button>

                            </div>


                            <!-- OPENING STOCK -->

                            <div>

                                <label
                                    class="block text-sm
                                           text-gray-500 mb-2"
                                >
                                    Opening Stock
                                </label>

                                <div class="flex">

                                    <input
                                        id="newItemStock"
                                        type="number"
                                        min="0"
                                        step="0.01"
                                        placeholder="ex: 150 PCS"
                                        class="flex-1 border
                                               rounded-l-lg
                                               px-3 py-3"
                                    >

                                    <span
                                        id="newItemStockUnitLabel"
                                        class="border border-l-0
                                               rounded-r-lg
                                               px-4 py-3
                                               text-gray-500"
                                    >
                                        PCS
                                    </span>

                                </div>

                            </div>


                            <!-- DATE -->

                            <div>

                                <label
                                    class="block text-sm
                                           text-gray-500 mb-2"
                                >
                                    As of Date
                                </label>

                                <input
                                    id="newItemStockDate"
                                    type="date"
                                    class="w-full border
                                           rounded-lg px-3 py-3"
                                >

                            </div>


                            <!-- LOW STOCK -->

                            <div class="md:col-span-2">

                                <label
                                    class="flex items-center gap-2
                                           text-blue-600 text-sm"
                                >
                                    <input
                                        type="checkbox"
                                        id="newItemLowStock"
                                        class="w-4 h-4
                                               accent-indigo-600"
                                    >

                                    Enable Low Stock Quantity Warning
                                </label>

                                <input
                                    id="newItemLowStockQty"
                                    type="number"
                                    min="0"
                                    step="0.01"
                                    placeholder="Low stock quantity"
                                    class="hidden mt-3
                                           w-full border
                                           rounded-lg px-3 py-3"
                                >

                            </div>


                            <!-- DESCRIPTION -->

                            <div class="md:col-span-2">

                                <label
                                    class="block text-sm
                                           text-gray-500 mb-2"
                                >
                                    Description
                                </label>

                                <textarea
                                    id="newItemDescription"
                                    rows="4"
                                    placeholder="Enter Description"
                                    class="w-full border
                                           rounded-lg px-3 py-3
                                           resize-none"
                                ></textarea>

                            </div>

                        </div>

                    </div>


                    <!-- ================================= -->
                    <!-- PRICING DETAILS -->
                    <!-- ================================= -->

                    <div
                        id="createItemTabPricing"
                        class="create-item-panel hidden"
                    >

                        <div
                            class="grid grid-cols-1 md:grid-cols-2
                                   gap-4"
                        >

                            <!-- SALES PRICE -->

                            <div>

                                <label
                                    class="block text-sm
                                           text-gray-500 mb-2"
                                >
                                    Sales Price
                                </label>

                                <div class="flex">

                                    <span
                                        class="border border-r-0
                                               rounded-l-lg
                                               px-4 py-3
                                               text-gray-500"
                                    >
                                        ₹
                                    </span>

                                    <input
                                        id="newItemSalesPrice"
                                        type="number"
                                        min="0"
                                        step="0.01"
                                        placeholder="ex: 200"
                                        class="flex-1 border
                                               rounded-r-lg
                                               px-3 py-3"
                                    >

                                </div>

                            </div>


                            <!-- PURCHASE PRICE -->

                            <div>

                                <label
                                    class="block text-sm
                                           text-gray-500 mb-2"
                                >
                                    Purchase Price
                                </label>

                                <div class="flex">

                                    <span
                                        class="border border-r-0
                                               rounded-l-lg
                                               px-4 py-3
                                               text-gray-500"
                                    >
                                        ₹
                                    </span>

                                    <input
                                        id="newItemPricingPurchasePrice"
                                        type="number"
                                        min="0"
                                        step="0.01"
                                        placeholder="ex: 200"
                                        class="flex-1 border
                                               rounded-r-lg
                                               px-3 py-3"
                                    >

                                </div>

                            </div>


                            <!-- GST -->

                            <div>

                                <label
                                    class="block text-sm
                                           text-gray-500 mb-2"
                                >
                                    GST Tax Rate(%)
                                </label>

                                <select
                                    id="newItemPricingGst"
                                    class="w-full border
                                           rounded-lg px-3 py-3"
                                >
                                    <option value="0">
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

                                <label
                                    class="block text-sm
                                           text-gray-500 mb-2"
                                >
                                    Discount on Sales Price
                                </label>

                                <div class="flex">

                                    <input
                                        id="newItemSalesDiscount"
                                        type="number"
                                        min="0"
                                        max="100"
                                        step="0.01"
                                        placeholder="ex: 12"
                                        class="flex-1 border
                                               rounded-l-lg
                                               px-3 py-3"
                                    >

                                    <span
                                        class="border border-l-0
                                               rounded-r-lg
                                               px-4 py-3"
                                    >
                                        %
                                    </span>

                                </div>

                            </div>

                        </div>

                    </div>


                    <!-- ================================= -->
                    <!-- PARTY WISE PRICES -->
                    <!-- ================================= -->

                    <div
                        id="createItemTabParty"
                        class="create-item-panel hidden"
                    >

                        <div
                            class="min-h-[350px]
                                   flex items-center
                                   justify-center"
                        >

                            <div
                                class="bg-gray-50
                                       rounded-xl
                                       px-10 py-10
                                       text-center
                                       max-w-md"
                            >

                                <div
                                    class="text-5xl mb-5"
                                >
                                    🧾
                                </div>

                                <p
                                    class="text-gray-600"
                                >
                                    To enable Party Wise Prices
                                    and set custom prices for
                                    parties, please save the
                                    item first.
                                </p>

                            </div>

                        </div>

                    </div>


                    <!-- ================================= -->
                    <!-- CUSTOM FIELDS -->
                    <!-- ================================= -->

                    <div
                        id="createItemTabCustom"
                        class="create-item-panel hidden"
                    >

                        <div
                            class="bg-amber-50
                                   rounded-lg
                                   px-4 py-3
                                   text-sm"
                        >
                            To add/manage item custom fields
                            go to
                            <span
                                class="text-blue-600
                                       font-medium"
                            >
                                Item Settings
                            </span>
                        </div>


                        <div
                            class="min-h-[300px]
                                   flex items-center
                                   justify-center"
                        >

                            <div
                                class="text-center
                                       text-gray-500"
                            >

                                <div
                                    class="text-5xl mb-4"
                                >
                                    📄
                                </div>

                                <p>
                                    You don't have any custom
                                    fields created yet
                                </p>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>


        <!-- FOOTER -->

        <div
            class="flex items-center
                   justify-between
                   px-5 py-4
                   border-t bg-white"
        >

            <button
                type="button"
                id="cancelCreateItemBtn"
                class="border border-gray-300
                       px-6 py-3
                       rounded-lg
                       text-sm"
            >
                Cancel
            </button>


            <div class="flex gap-3">

                <button
                    type="button"
                    id="saveAndNewItemBtn"
                    class="border border-indigo-200
                           text-indigo-600
                           px-6 py-3
                           rounded-lg
                           text-sm
                           font-medium"
                >
                    Save & New
                </button>


                <button
                    type="button"
                    id="saveItemBtn"
                    class="bg-indigo-600
                           hover:bg-indigo-700
                           text-white
                           px-7 py-3
                           rounded-lg
                           text-sm
                           font-medium"
                >
                    Save Item
                </button>

            </div>

        </div>

    </div>
</div>


            <!-- ===================================================== -->
<!-- ADD ITEMS TO BILL MODAL -->
<!-- ===================================================== -->

<div
    id="itemModal"
    class="hidden fixed inset-0 z-[50] bg-black/50
           flex items-center justify-center p-4"
>
    <div
        class="bg-white rounded-xl shadow-2xl
               w-full max-w-6xl
               max-h-[90vh]
               overflow-hidden
               flex flex-col"
    >

        <!-- ================= HEADER ================= -->

        <div
            class="flex justify-between items-center
                   px-6 py-5 border-b"
        >
            <h2 class="text-2xl font-semibold">
                Add Items to Bill
            </h2>

            <button
                type="button"
                onclick="closeItemModal()"
                class="border border-gray-300
                       rounded-lg px-4 py-2
                       text-xl text-gray-500
                       hover:bg-gray-50"
            >
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
                               -translate-y-1/2
                               text-gray-500"
                    >
                        🔍
                    </span>

                    <input
                        type="text"
                        id="itemSearch"
                        placeholder="Search by Item / Serial no. / HSN code / SKU / Custom Field / Category"
                        class="w-full border border-indigo-300
                               rounded-lg px-12 py-3
                               focus:outline-none
                               focus:ring-2
                               focus:ring-indigo-200"
                    >

                </div>


                <!-- CATEGORY -->

                <select
                    id="itemCategoryFilter"
                    class="w-64 border border-gray-300
                           rounded-lg px-4 py-3
                           bg-white"
                >
                    <option value="">
                        Select Category
                    </option>
                </select>


                <!-- CREATE NEW ITEM -->

                <button
                    type="button"
                    onclick="openCreateItemFromPurchase()"
                    class="bg-indigo-600
                           hover:bg-indigo-700
                           text-white
                           px-6 py-3
                           rounded-lg
                           font-medium
                           whitespace-nowrap"
                >
                    Create New Item
                </button>

            </div>

        </div>


        <!-- ================= TABLE ================= -->

        <div class="px-6 flex-1 overflow-hidden">

            <div
                class="border rounded-lg
                       overflow-hidden"
            >

                <!-- TABLE HEADER -->

                <div
                    class="grid
                           grid-cols-[2fr_1fr_1fr_1fr_1fr_1fr]
                           bg-gray-100
                           border-b
                           px-4 py-4
                           text-sm
                           font-semibold"
                >

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
                    id="itemList"
                    class="max-h-[420px]
                           overflow-y-auto"
                >

                    <div
                        class="p-10
                               text-center
                               text-gray-500"
                    >
                        Loading items...
                    </div>

                </div>


                <!-- SELECTED SUMMARY -->

                <div
                    class="grid grid-cols-3
                           bg-gray-100
                           border-t
                           px-4 py-4
                           text-sm"
                >

                    <div
                        id="selectedItemCount"
                        class="text-blue-500"
                    >
                        Show 0 Item(s) Selected
                    </div>


                    <div
                        class="text-center font-medium"
                    >
                        ₹
                        <span id="selectedItemsAmount">
                            0
                        </span>
                    </div>


                    <div
                        class="text-right"
                    >
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
                   px-6 py-4
                   border-t"
        >

            <div
                class="text-sm text-gray-500"
            >
                Keyboard Shortcuts:

                <span class="ml-4">
                    Change Quantity
                </span>

                <span
                    class="border rounded px-2 py-1 ml-2"
                >
                    Enter
                </span>

                <span class="ml-6">
                    Move between items
                </span>

                <span
                    class="border rounded px-2 py-1 ml-2"
                >
                    ↑
                </span>

                <span
                    class="border rounded px-2 py-1"
                >
                    ↓
                </span>
            </div>


            <div class="flex gap-4">

                <button
                    type="button"
                    onclick="closeItemModal()"
                    class="border border-gray-300
                           px-7 py-3
                           rounded-lg"
                >
                    Cancel [ESC]
                </button>


                <button
                    type="button"
                    id="addItemsToBillButton"
                    onclick="addSelectedItemsToBill()"
                    disabled
                    class="bg-indigo-100
                           text-indigo-400
                           px-7 py-3
                           rounded-lg
                           font-medium"
                >
                    Add to Bill [F7]
                </button>

            </div>

        </div>

    </div>
</div>
<!-- CHARGE MODAL --><div id="chargeModal" class="hidden fixed inset-0 z-50 modal-backdrop flex items-center justify-center p-4"><div class="bg-white rounded-xl w-full max-w-md shadow-2xl"><div class="p-4 border-b flex justify-between"><h2 class="font-semibold">Additional Charge</h2><button type="button" onclick="closeChargeModal()" class="text-gray-500 text-xl">×</button></div><div class="p-4 space-y-3"><input id="chargeName" placeholder="Charge name e.g. Transport" class="w-full border rounded-lg px-3 py-2"><input id="chargeAmount" type="number" min="0" step="0.01" placeholder="Amount" class="w-full border rounded-lg px-3 py-2"><button id="saveChargeBtn" type="button" class="w-full bg-indigo-600 text-white rounded-lg py-2">Add Charge</button></div></div></div>
<div id="toast" class="hidden fixed bottom-5 right-5 z-[60] px-5 py-3 rounded-lg shadow-lg text-white text-sm"></div>

<script>
const API='/api'; 
const token=localStorage.getItem('token');
let parties = [];
let items = [];
let invoiceItems = [];
let additionalCharges = [];
let selectedParty = null;
let discountPercent = 0;
let discountAmount = 0;
let roundOff = 0;
function authHeaders(extra={}){return {'Authorization':'Bearer '+token,'Accept':'application/json',...extra};}
async function apiFetch(url,opt={}){const r=await fetch(url,{...opt,headers:authHeaders(opt.headers||{})});if(r.status===401){localStorage.removeItem('token');location.href='/start-free-billing';throw Error('Session expired');}const text=await r.text();let d={};try{d=text?JSON.parse(text):{};}catch(_){}if(!r.ok)throw Error(d.message||(d.errors?Object.values(d.errors).flat().join(', '):'Request failed'));return d;}
function money(v){return '₹ '+Number(v||0).toLocaleString('en-IN',{minimumFractionDigits:2,maximumFractionDigits:2});}
function esc(v){return String(v??'').replace(/[&<>"']/g,c=>({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'}[c]));}
function toast(msg,ok=true){const e=document.getElementById('toast');e.textContent=msg;e.className='fixed bottom-5 right-5 z-[60] px-5 py-3 rounded-lg shadow-lg text-white text-sm '+(ok?'bg-green-600':'bg-red-600');setTimeout(()=>e.classList.add('hidden'),3500);}
// =====================================================
// CREATE NEW ITEM
// =====================================================

let createItemSaveAndNew = false;


// OPEN CREATE ITEM

function openCreateItemFromPurchase() {

    // Hide item selector
    document
        .getElementById('itemModal')
        .classList.add('hidden');

    resetCreateItemForm();

    document
        .getElementById('createItemModal')
        .classList.remove('hidden');

    activateCreateItemTab('basic');

    setTimeout(() => {

        document
            .getElementById('newItemName')
            .focus();

    }, 100);
}


// CLOSE CREATE ITEM

function closeCreateItemModal() {

    document
        .getElementById('createItemModal')
        .classList.add('hidden');

}


// RESET FORM

function resetCreateItemForm() {

    document.getElementById('newItemName').value = '';

    document.getElementById('newItemCategory').value = '';

    document.getElementById('newItemPurchasePrice').value = '';

    document.getElementById('newItemSalesPrice').value = '';

    document.getElementById('newItemPricingPurchasePrice').value = '';

    document.getElementById('newItemGst').value = '0';

    document.getElementById('newItemPricingGst').value = '0';

    document.getElementById('newItemUnit').value = 'PCS';

    document.getElementById('newItemStockUnit').value = 'PCS';

    document.getElementById('newItemOpeningStock').value = '';

    document.getElementById('newItemStock').value = '';

    document.getElementById('newItemStockDate').value = today();

    document.getElementById('newItemCode').value = '';

    document.getElementById('newItemHsn').value = '';

    document.getElementById('newItemDescription').value = '';

    document.getElementById('newItemSalesDiscount').value = '';

    document.getElementById('newItemLowStock').checked = false;

    document.getElementById('newItemLowStockQty').value = '';

    document.getElementById('newItemLowStockQty').classList.add('hidden');

    document.getElementById('newItemOnlineStore').checked = false;

    document.getElementById('newItemUnitLabel').textContent = 'PCS';

    document.getElementById('newItemStockUnitLabel').textContent = 'PCS';

    loadCreateItemCategories();

    activateCreateItemTab('basic');
}


// LOAD CATEGORIES DYNAMICALLY

function loadCreateItemCategories() {

    const select =
        document.getElementById('newItemCategory');

    const categories = new Set();

    items.forEach(item => {

        let values = item.item_categories;

        if (!values) return;

        if (typeof values === 'string') {

            try {
                values = JSON.parse(values);
            } catch (_) {
                values = [values];
            }

        }

        if (Array.isArray(values)) {

            values.forEach(category => {

                if (category) {
                    categories.add(String(category));
                }

            });

        }

    });


    select.innerHTML =
        '<option value="">Search Categories</option>' +
        Array.from(categories)
            .sort()
            .map(category =>
                `<option value="${esc(category)}">
                    ${esc(category)}
                </option>`
            )
            .join('');
}


// TAB SWITCH

function activateCreateItemTab(tab) {

    document
        .querySelectorAll('.create-item-panel')
        .forEach(panel => {
            panel.classList.add('hidden');
        });


    document
        .querySelectorAll('.create-item-tab')
        .forEach(button => {

            button.classList.remove(
                'bg-indigo-50',
                'text-indigo-600',
                'font-medium'
            );

            button.classList.add(
                'text-gray-600'
            );

        });


    const panel =
        document.getElementById(
            'createItemTab' +
            tab.charAt(0).toUpperCase() +
            tab.slice(1)
        );

    if (panel) {
        panel.classList.remove('hidden');
    }


    const button =
        document.querySelector(
            `.create-item-tab[data-tab="${tab}"]`
        );

    if (button) {

        button.classList.add(
            'bg-indigo-50',
            'text-indigo-600',
            'font-medium'
        );

        button.classList.remove(
            'text-gray-600'
        );

    }

}


// GENERATE ITEM CODE / BARCODE

function generateCreateItemCode() {

    const code =
        'ITM' +
        Date.now().toString().slice(-7);

    document
        .getElementById('newItemCode')
        .value = code;
}


// SYNC UNIT

function syncCreateItemUnit() {

    const unit =
        document.getElementById(
            'newItemUnit'
        ).value;

    document
        .getElementById(
            'newItemUnitLabel'
        )
        .textContent = unit;

    document
        .getElementById(
            'newItemStockUnit'
        )
        .value = unit;

    document
        .getElementById(
            'newItemStockUnitLabel'
        )
        .textContent = unit;
}


// SYNC PRICES

function syncCreateItemPrices() {

    const basicPurchase =
        document.getElementById(
            'newItemPurchasePrice'
        ).value;

    const pricingPurchase =
        document.getElementById(
            'newItemPricingPurchasePrice'
        );

    if (!pricingPurchase.value) {
        pricingPurchase.value = basicPurchase;
    }


    const salesPrice =
        document.getElementById(
            'newItemSalesPrice'
        ).value;

    if (!salesPrice) {
        document.getElementById(
            'newItemSalesPrice'
        ).value = '';
    }

}


// SYNC GST

function syncCreateItemGst() {

    document.getElementById(
        'newItemPricingGst'
    ).value =
        document.getElementById(
            'newItemGst'
        ).value;

}


// CREATE ITEM PAYLOAD

function buildCreateItemPayload() {

    const name =
        document
            .getElementById('newItemName')
            .value
            .trim();

    if (!name) {

        activateCreateItemTab('basic');

        throw new Error(
            'Please enter item name.'
        );
    }


    const itemType =
        document.querySelector(
            'input[name="newItemType"]:checked'
        )?.value || 'product';


    const category =
        document
            .getElementById('newItemCategory')
            .value
            .trim();


    const unit =
        document
            .getElementById('newItemUnit')
            .value;


    const purchasePrice =
        Number(
            document
                .getElementById(
                    'newItemPurchasePrice'
                )
                .value || 0
        );


    const pricingPurchasePrice =
        Number(
            document
                .getElementById(
                    'newItemPricingPurchasePrice'
                )
                .value || 0
        );


    const salesPrice =
        Number(
            document
                .getElementById(
                    'newItemSalesPrice'
                )
                .value || 0
        );


    const gst =
        Number(
            document
                .getElementById(
                    'newItemGst'
                )
                .value || 0
        );


    const openingStock =
        Number(
            document
                .getElementById(
                    'newItemOpeningStock'
                )
                .value ||
            document
                .getElementById(
                    'newItemStock'
                )
                .value ||
            0
        );


    const lowStock =
        document
            .getElementById(
                'newItemLowStock'
            )
            .checked;


    const lowStockQuantity =
        Number(
            document
                .getElementById(
                    'newItemLowStockQty'
                )
                .value || 0
        );


    return {

        name: name,

        item_type: itemType,

        unit: unit,

        sales_price: salesPrice,

        purchase_price:
            pricingPurchasePrice ||
            purchasePrice,

        gst_percent: gst,

        hsn_code:
            document
                .getElementById(
                    'newItemHsn'
                )
                .value
                .trim() || null,

        item_code:
            document
                .getElementById(
                    'newItemCode'
                )
                .value
                .trim() || null,

        opening_stock:
            openingStock,

        stock_as_of_date:
            document
                .getElementById(
                    'newItemStockDate'
                )
                .value || null,

        item_categories:
            category ? [category] : [],

        description:
            document
                .getElementById(
                    'newItemDescription'
                )
                .value
                .trim() || null,

        low_stock_alert:
            lowStock,

        low_stock_quantity:
            lowStockQuantity,

        show_in_online_store:
            document
                .getElementById(
                    'newItemOnlineStore'
                )
                .checked,

        custom_fields: [],

        imei_list: []

    };
}


// SAVE ITEM

async function createPurchaseItem(
    saveAndNew = false
) {

    try {

        const payload =
            buildCreateItemPayload();


        const saveButton =
            document.getElementById(
                'saveItemBtn'
            );

        const saveNewButton =
            document.getElementById(
                'saveAndNewItemBtn'
            );


        saveButton.disabled = true;

        saveNewButton.disabled = true;


        const response =
            await apiFetch(
                API + '/items',
                {
                    method: 'POST',

                    headers: {
                        'Content-Type':
                            'application/json'
                    },

                    body:
                        JSON.stringify(payload)
                }
            );


        const createdItem =
            response.data;


        toast(
            response.message ||
            'Item created successfully.'
        );


        // Refresh catalog

        await loadItems();


        // If item was created successfully,
        // add it directly to current purchase

        if (createdItem?.id) {

            invoiceItems.push({

                item_id:
                    Number(createdItem.id),

                description:
                    createdItem.name || payload.name,

                hsn:
                    createdItem.hsn_code ||
                    payload.hsn_code ||
                    '',

                qty: 1,

                unit:
                    createdItem.unit ||
                    payload.unit ||
                    'PCS',

                price:
                    Number(
                        createdItem.purchase_price ??
                        payload.purchase_price ??
                        0
                    ),

                discount: 0,

                gst_percent:
                    Number(
                        createdItem.gst_percent ??
                        payload.gst_percent ??
                        0
                    )

            });


            renderInvoiceItems();

            recalculate();
        }


        if (saveAndNew) {

            resetCreateItemForm();

            document
                .getElementById(
                    'newItemName'
                )
                .focus();

        } else {

            closeCreateItemModal();

        }

    } catch (error) {

        toast(
            error.message ||
            'Unable to create item.',
            false
        );

    } finally {

        document
            .getElementById(
                'saveItemBtn'
            )
            .disabled = false;

        document
            .getElementById(
                'saveAndNewItemBtn'
            )
            .disabled = false;

    }

}

// =====================================================
// CREATE ITEM EVENTS
// =====================================================

document
    .querySelectorAll('.create-item-tab')
    .forEach(button => {

        button.addEventListener(
            'click',
            () => {
                activateCreateItemTab(
                    button.dataset.tab
                );
            }
        );

    });


document
    .getElementById('closeCreateItemBtn')
    .onclick =
    closeCreateItemModal;


document
    .getElementById('cancelCreateItemBtn')
    .onclick =
    closeCreateItemModal;


document
    .getElementById('saveItemBtn')
    .onclick =
    () => createPurchaseItem(false);


document
    .getElementById('saveAndNewItemBtn')
    .onclick =
    () => createPurchaseItem(true);


document
    .getElementById('generateBarcodeBtn')
    .onclick =
    generateCreateItemCode;


document
    .getElementById('newItemUnit')
    .addEventListener(
        'change',
        syncCreateItemUnit
    );


document
    .getElementById('newItemGst')
    .addEventListener(
        'change',
        syncCreateItemGst
    );


document
    .getElementById('newItemPurchasePrice')
    .addEventListener(
        'input',
        syncCreateItemPrices
    );


document
    .getElementById('newItemLowStock')
    .addEventListener(
        'change',
        function () {

            document
                .getElementById(
                    'newItemLowStockQty'
                )
                .classList.toggle(
                    'hidden',
                    !this.checked
                );

        }
    );

function today(){const d=new Date(),o=d.getTimezoneOffset();return new Date(d.getTime()-o*60000).toISOString().slice(0,10);}
function purchaseNo(){return 'PUR-'+Date.now();}
async function loadBusiness(){try{const r=await apiFetch(API+'/business-info'),d=r.data||{},n=d.business_name||d.name||'Business Name';document.getElementById('sidebarBusinessName').textContent=n;document.getElementById('businessInitial').textContent=n.trim().charAt(0).toUpperCase()||'B';try{const me=await apiFetch(API+'/me');document.getElementById('sidebarMobile').textContent=me.mobile||me.phone||'—';}catch(_){}}catch(e){console.warn(e.message);}}
async function loadParties(search=''){const r=await apiFetch(API+'/parties'+(search?'?search='+encodeURIComponent(search):''));parties=r.data||[];renderParties();}

function renderParties() {

    const list = document.getElementById('partyInlineList');

    if (!parties.length) {

        list.innerHTML = `
            <div class="text-center py-6 text-gray-500 text-sm">
                No parties found.
            </div>
        `;

        return;
    }

    list.innerHTML = parties.map(p => {

        const balance = Number(
            p.balance ??
            p.current_balance ??
            p.balance_amount ??
            0
        );

        return `
            <button
                type="button"
                onclick="selectParty(${Number(p.id)})"
                class="w-full grid grid-cols-[1fr_120px] items-center text-left px-3 py-2 border-b border-gray-100 hover:bg-indigo-50"
            >

                <div>
                    <div class="font-medium text-gray-800">
                        ${esc(p.party_name || '')}
                    </div>

                    <div class="text-xs text-gray-400 mt-1">
                        ${esc(p.contact_number || '')}
                    </div>
                </div>

                <div class="text-right text-sm text-gray-700">
                    ${money(balance)}
                </div>

            </button>
        `;

    }).join('');
}


function openPartySelector() {

    const addButton = document.getElementById('addPartyBtn');
    const selector = document.getElementById('partySelector');
    const search = document.getElementById('partySearchInline');

    // Hide Add Party box
    addButton.classList.add('hidden');

    // Show inline party selector
    selector.classList.remove('hidden');

    // Clear search
    search.value = '';

    // Load parties
    loadParties()
        .catch(e => toast(e.message, false));

    // Focus search box
    setTimeout(() => {
        search.focus();
    }, 50);
}

function closePartySelector() {

    document
        .getElementById('partySelector')
        .classList.add('hidden');

}


function selectParty(id) {

    selectedParty = parties.find(
        p => Number(p.id) === Number(id)
    );

    if (!selectedParty) return;

    document.getElementById('selectedPartyName').textContent =
        selectedParty.party_name || '';

    document.getElementById('selectedPartyPhone').textContent =
        selectedParty.contact_number
            ? '📞 ' + selectedParty.contact_number
            : '';

    document.getElementById('selectedPartyGst').textContent =
        selectedParty.gst_number
            ? 'GSTIN: ' + selectedParty.gst_number
            : '';

    document.getElementById('selectedPartyAddress').textContent =
        [
            selectedParty.billing_street,
            selectedParty.billing_city,
            selectedParty.billing_state,
            selectedParty.billing_pincode
        ]
        .filter(Boolean)
        .join(', ');

    // Hide selector
    closePartySelector();

    // Hide Add Party
    document
        .getElementById('addPartyBtn')
        .classList.add('hidden');

    // Show selected party
    document
        .getElementById('selectedPartyCard')
        .classList.remove('hidden');
}
async function loadItems(search=''){const r=await apiFetch(API+'/items'+(search?'?search='+encodeURIComponent(search):''));items=r.data||[];renderItems();}


let selectedCatalogItems = [];

function renderItems() {

    const list = document.getElementById('itemList');

    if (!items.length) {

        list.innerHTML = `
            <div class="p-10 text-center text-gray-500">
                No items found.
            </div>
        `;

        updateSelectedCatalogSummary();
        return;
    }

    list.innerHTML = items.map(item => {

        const stock =
            parseFloat(item.opening_stock || 0);

        const salesPrice =
            parseFloat(item.sales_price || 0);

        const purchasePrice =
            parseFloat(item.purchase_price || 0);

        const selected =
            selectedCatalogItems.find(
                x => Number(x.id) === Number(item.id)
            );

        const quantity =
            selected ? selected.quantity : 0;

        return `
            <div
                class="grid
                       grid-cols-[2fr_1fr_1fr_1fr_1fr_1fr]
                       items-center
                       px-4 py-4
                       border-b
                       hover:bg-gray-50"
            >

                <!-- ITEM NAME -->

                <div>

                    <div
                        class="font-medium text-gray-700"
                    >
                        ${esc(item.name || '-')}
                    </div>

                    ${
                        item.hsn_code
                        ? `
                            <div
                                class="text-xs
                                       text-gray-400
                                       mt-1"
                            >
                                HSN:
                                ${esc(item.hsn_code)}
                            </div>
                        `
                        : ''
                    }

                </div>


                <!-- ITEM CODE -->

                <div class="text-gray-600">
                    ${esc(item.item_code || '-')}
                </div>


                <!-- STOCK -->

                <div class="text-gray-600">
                    ${stock}
                    ${esc(item.unit || '')}
                </div>


                <!-- SALES PRICE -->

                <div class="text-gray-700">
                    ₹ ${salesPrice.toFixed(2)}
                </div>


                <!-- PURCHASE PRICE -->

                <div class="text-gray-700">
                    ₹ ${purchasePrice.toFixed(2)}
                </div>


                <!-- QUANTITY -->

                <div>

                    ${
                        selected
                        ? `
                            <div
                                class="flex items-center gap-2"
                            >

                                <input
                                    type="number"
                                    min="1"
                                    step="1"
                                    value="${quantity}"
                                    onchange="
                                        changeCatalogItemQuantity(
                                            ${item.id},
                                            this.value
                                        )
                                    "
                                    class="w-20
                                           border
                                           rounded-lg
                                           px-3 py-2"
                                >

                                <button
                                    type="button"
                                    onclick="
                                        removeCatalogItem(
                                            ${item.id}
                                        )
                                    "
                                    class="text-red-500"
                                >
                                    ×
                                </button>

                            </div>
                        `
                        : `
                            <button
                                type="button"
                                onclick="
                                    selectCatalogItem(
                                        ${item.id}
                                    )
                                "
                                class="w-full
                                       bg-blue-50
                                       text-blue-600
                                       border
                                       border-blue-200
                                       px-4 py-3
                                       rounded-lg
                                       hover:bg-blue-100"
                            >
                                + Add
                            </button>
                        `
                    }

                </div>

            </div>
        `;

    }).join('');

    updateSelectedCatalogSummary();
}

function selectCatalogItem(id) {

    const item = items.find(
        x => Number(x.id) === Number(id)
    );

    if (!item) return;

    const exists =
        selectedCatalogItems.find(
            x => Number(x.id) === Number(id)
        );

    if (exists) return;

    selectedCatalogItems.push({
        ...item,
        quantity: 1
    });

    renderItems();
}


function changeCatalogItemQuantity(id, value) {

    const item =
        selectedCatalogItems.find(
            x => Number(x.id) === Number(id)
        );

    if (!item) return;

    const quantity =
        Math.max(1, Number(value || 1));

    item.quantity = quantity;

    renderItems();
}


function removeCatalogItem(id) {

    selectedCatalogItems =
        selectedCatalogItems.filter(
            x => Number(x.id) !== Number(id)
        );

    renderItems();
}


function updateSelectedCatalogSummary() {

    const count =
        selectedCatalogItems.length;

    const quantity =
        selectedCatalogItems.reduce(
            (sum, item) =>
                sum + Number(item.quantity || 0),
            0
        );

    const amount =
        selectedCatalogItems.reduce(
            (sum, item) =>
                sum +
                (
                    Number(item.purchase_price || 0) *
                    Number(item.quantity || 0)
                ),
            0
        );

    const countElement =
        document.getElementById(
            'selectedItemCount'
        );

    const amountElement =
        document.getElementById(
            'selectedItemsAmount'
        );

    const quantityElement =
        document.getElementById(
            'selectedItemsQuantity'
        );

    if (countElement) {
        countElement.textContent =
            `Show ${count} Item(s) Selected`;
    }

    if (amountElement) {
        amountElement.textContent =
            amount.toLocaleString(
                'en-IN',
                {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                }
            );
    }

    if (quantityElement) {
        quantityElement.textContent =
            quantity;
    }


    const addButton =
        document.getElementById(
            'addItemsToBillButton'
        );

    if (addButton) {

        addButton.disabled =
            count === 0;

        if (count > 0) {

            addButton.className =
                'bg-indigo-600 hover:bg-indigo-700 ' +
                'text-white px-7 py-3 rounded-lg font-medium';

        } else {

            addButton.className =
                'bg-indigo-100 text-indigo-400 ' +
                'px-7 py-3 rounded-lg font-medium';

        }
    }
}


function addSelectedItemsToBill() {

    if (!selectedCatalogItems.length) {
        return;
    }

    selectedCatalogItems.forEach(item => {

        const alreadyAdded =
            invoiceItems.find(
                x => Number(x.item_id) === Number(item.id)
            );

        if (alreadyAdded) {

            alreadyAdded.qty +=
                Number(item.quantity || 1);

            return;
        }

        invoiceItems.push({

            item_id: Number(item.id),

            description:
                item.name || '',

            hsn:
                item.hsn_code || '',

            qty:
                Number(item.quantity || 1),

            unit:
                item.unit || 'PCS',

            price:
                Number(item.purchase_price || 0),

            discount: 0,

            gst_percent:
                Number(item.gst_percent || 0)

        });

    });


    selectedCatalogItems = [];

    closeItemModal();

    renderInvoiceItems();

    recalculate();
}

function openItemModal() {

    document
        .getElementById('itemModal')
        .classList.remove('hidden');

    document
        .getElementById('itemSearch')
        .value = '';

    selectedCatalogItems = [];

    updateSelectedCatalogSummary();

    loadItems()
        .catch(e => toast(e.message, false));

    setTimeout(() => {

        document
            .getElementById('itemSearch')
            .focus();

    }, 100);
}
function closeItemModal()
{
    document.getElementById('itemModal').classList.add('hidden');

}
function addItemFromCatalog(id)
{
const i=items.find(x=>Number(x.id)===Number(id));
if(!i)return;invoiceItems.push({item_id:Number(i.id),description:i.name||'',hsn:i.hsn_code||'',qty:1,unit:i.unit||'PCS',price:Number(i.purchase_price||0),discount:0,gst_percent:Number(i.gst_percent||0)});
closeItemModal();renderInvoiceItems();recalculate();
}
function removeItem(i){invoiceItems.splice(i,1);renderInvoiceItems();recalculate();}
function updateItem(i,f,v){if(!invoiceItems[i])return;invoiceItems[i][f]=['qty','price','discount','gst_percent'].includes(f)?Number(v||0):v;recalculate();updateLineAmount(i);}
function calcLine(r){const q=Number(r.qty||0),p=Number(r.price||0),d=Number(r.discount||0),g=Number(r.gst_percent||0),base=Math.max(q*p-d,0),tax=base*g/100;return {base,tax,amount:base+tax};}
function updateLineAmount(i){const row=document.querySelector(`[data-row-index="${i}"]`);if(!row)return;const c=calcLine(invoiceItems[i]);row.querySelector('.line-amount').textContent=money(c.amount);}
function renderInvoiceItems(){const b=document.getElementById('itemsBody');if(!invoiceItems.length){b.innerHTML='<tr><td colspan="9" class="p-4"><button type="button" id="addItemBtn" class="w-full border-2 border-dashed border-blue-300 rounded-lg py-3 text-blue-600 hover:bg-blue-50">+ Add Item</button></td></tr>';document.getElementById('addItemBtn').onclick=openItemModal;return;}b.innerHTML=invoiceItems.map((r,i)=>{const c=calcLine(r);return `<tr class="border-b" data-row-index="${i}"><td class="px-3 py-3">${i+1}</td><td class="px-3 py-3"><input class="table-input" value="${esc(r.description)}" onchange="updateItem(${i},'description',this.value)"></td><td class="px-3 py-3"><input class="table-input" value="${esc(r.hsn)}" onchange="updateItem(${i},'hsn',this.value)"></td><td class="px-3 py-3"><input class="table-input" type="number" min="0.01" step="0.01" value="${r.qty}" onchange="updateItem(${i},'qty',this.value)"></td><td class="px-3 py-3"><input class="table-input" type="number" min="0" step="0.01" value="${r.price}" onchange="updateItem(${i},'price',this.value)"></td><td class="px-3 py-3"><input class="table-input" type="number" min="0" step="0.01" value="${r.discount}" onchange="updateItem(${i},'discount',this.value)"></td><td class="px-3 py-3"><input class="table-input" type="number" min="0" step="0.01" value="${r.gst_percent}" onchange="updateItem(${i},'gst_percent',this.value)"></td><td class="px-3 py-3 font-medium line-amount">${money(c.amount)}</td><td class="px-3 py-3"><button type="button" onclick="removeItem(${i})" class="text-red-500">✕</button></td></tr>`;}).join('')+`<tr><td colspan="9" class="p-3"><button type="button" id="addItemBtn" class="w-full border-2 border-dashed border-blue-300 rounded-lg py-3 text-blue-600 hover:bg-blue-50">+ Add Item</button></td></tr>`;document.getElementById('addItemBtn').onclick=openItemModal;}
function recalculate() {
    let subtotal = 0;
    let totalTax = 0;

    invoiceItems.forEach(r => {
        const c = calcLine(r);
        subtotal += c.base;
        totalTax += c.tax;
    });

    const additionalTotal = additionalCharges.reduce(
        (s, c) => s + Number(c.amount || 0),
        0
    );

    // Discount is calculated after tax + additional charges
    const discountBase = subtotal + totalTax + additionalTotal;

    discountAmount =
        discountBase * (Number(discountPercent || 0) / 100);

    let total =
        subtotal +
        totalTax +
        additionalTotal -
        discountAmount;

    // Auto Round Off
    if (document.getElementById('autoRoundOff').checked) {
        const rounded = Math.round(total);

        roundOff = rounded - total;

        document.getElementById('roundOff').value =
            roundOff.toFixed(2);
    } else {
        roundOff = Number(
            document.getElementById('roundOff').value || 0
        );
    }

    // TCS removed
    tcsAmount = 0;

    total = Number(
        (total + roundOff).toFixed(2)
    );

    const received = Number(
        document.getElementById('receivedAmount').value || 0
    );

    const balance = Number(
        (total - received).toFixed(2)
    );

    document.getElementById('subtotalDisplay').textContent =
        money(subtotal);

    document.getElementById('taxableAmount').textContent =
        money(subtotal + totalTax);

    document.getElementById('discountDisplay').textContent =
        money(discountAmount);

    document.getElementById('grandTotalDisplay').textContent =
        money(total);

    document.getElementById('receivedDisplay').textContent =
        money(received);

    const balanceElement =
        document.getElementById('balanceDisplay');

    balanceElement.textContent =
        balance < 0
            ? '-₹ ' + Math.abs(balance).toLocaleString('en-IN', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            })
            : '₹ ' + balance.toLocaleString('en-IN', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });

    balanceElement.className =
        'font-semibold ' +
        (balance < 0
            ? 'text-red-600'
            : 'text-green-600');

    renderCharges();

    return {
        subtotal,
        totalTax,
        additionalTotal,
        total,
        received,
        balance
    };
}
function openChargeModal(){document.getElementById('chargeName').value='';document.getElementById('chargeAmount').value='';document.getElementById('chargeModal').classList.remove('hidden');}function closeChargeModal(){document.getElementById('chargeModal').classList.add('hidden');}function saveCharge(){const n=document.getElementById('chargeName').value.trim(),a=Number(document.getElementById('chargeAmount').value||0);if(!n)return toast('Enter charge name.',false);if(a<0)return toast('Charge amount cannot be negative.',false);additionalCharges.push({name:n,amount:a});closeChargeModal();recalculate();}function removeCharge(i){additionalCharges.splice(i,1);recalculate();}
function renderCharges() {
    // Charges are edited directly inside the invoice panel.
    // Do not rebuild the DOM during recalculation.
}
// function renderCharges(){document.getElementById('chargesList').innerHTML=additionalCharges.map((c,i)=>`<div class="flex justify-between bg-gray-50 border rounded-lg px-3 py-2 text-sm"><span>${esc(c.name)}</span><span class="flex gap-3"><span>${money(c.amount)}</span><button type="button" onclick="removeCharge(${i})" class="text-red-500">✕</button></span></div>`).join('');}
function buildPayload()
{
    const c=recalculate();
    if(!selectedParty)throw Error('Please select a supplier / party.');
    if(!invoiceItems.length)throw Error('Please add at least one item.');
    for(const r of invoiceItems)
        {if(!r.item_id)throw Error('Every item must be selected from your item list.');
    if(Number(r.qty)<=0)throw Error('Quantity must be greater than 0.');
    if(Number(r.price)<0)throw Error('Price cannot be negative.');
}
return {
    purchase_number: document.getElementById('purchaseNumber').value,
    purchase_date: document.getElementById('purchaseDate').value,
    due_date: document.getElementById('dueDate').value || null,
    place_of_supply: selectedParty.billing_state || null,
    party_id: Number(selectedParty.id),

    notes: document.getElementById('notes').value || null,

    additional_charges: additionalCharges.map(x => ({
        name: x.name,
        amount: Number(x.amount)
    })),

    discount_percent: Number(discountPercent || 0),
    discount_amount: Number(discountAmount.toFixed(2)),
    round_off: Number(roundOff.toFixed(2)),

    received_amount: Number(c.received.toFixed(2)),
    payment_mode: document.getElementById('paymentModeSelect').value,

    items: invoiceItems.map(r => ({
        item_id: Number(r.item_id),
        description: r.description || null,
        qty: Number(r.qty),
        unit: r.unit || 'PCS',
        price: Number(r.price),
        discount: Number(r.discount || 0),
        gst_percent: Number(r.gst_percent || 0)
    }))
};
}
async function savePurchase(andNew=false){
    try{const payload=buildPayload();
    document.getElementById('saveBtn').disabled=true;
    document.getElementById('saveNewBtn').disabled=true;
    const r=await apiFetch(API+'/purchases',
    {method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify(payload)});
    toast(r.message||'Purchase created successfully.');
    if(andNew)
        {resetForm();}
    else{const id=r.data?.id;setTimeout(()=>location.href=id?'/purchase-preview/'+id:'/purchases',600);

    }}catch(e){toast(e.message||'Unable to save purchase.',false);}
    finally{document.getElementById('saveBtn').disabled=false;
    document.getElementById('saveNewBtn').disabled=false;}
}
function resetForm() {
    selectedParty = null;
    invoiceItems = [];
    additionalCharges = [];

    discountPercent = 0;
    discountAmount = 0;
    roundOff = 0;
    tcsAmount = 0;

    document.getElementById('purchaseNumber').value =
        purchaseNo();

    document.getElementById('purchaseDate').value =
        today();

    document.getElementById('dueDate').value = '';

    document.getElementById('originalInvoiceNo').value =
        '';

    document.getElementById('notes').value = '';

    document.getElementById('discountPercent').value =
        '';

    document.getElementById('receivedAmount').value =
        '';

    document.getElementById('roundOff').value =
        '0';

    document.getElementById('selectedPartyCard')
        .classList.add('hidden');

    document.getElementById('addPartyBtn')
        .classList.remove('hidden');

    renderInvoiceItems();
    recalculate();
}
// document.getElementById('addPartyBtn').onclick=openPartyModal;
// document.getElementById('changePartyBtn').onclick=openPartyModal;
document.getElementById('addPartyBtn').onclick =
    openPartySelector;

document.getElementById('changePartyBtn').onclick =
    openPartySelector;

document.getElementById('partySearchInline').addEventListener(
    'input',
    e => {
        loadParties(e.target.value.trim())
            .catch(x => toast(x.message, false));
    }
);

// document.getElementById('createPartyBtn').onclick = () => {
//     window.location.href = "{{ route('parties.create') }}";
// };   

document.getElementById('createPartyBtn').onclick =
    openCreateParty;

// =====================================================
// CREATE PARTY MODAL
// =====================================================

function openCreateParty() {

    // Hide party selector
    document
        .getElementById('partySelector')
        .classList.add('hidden');

    // Reset form
    resetPartyForm();

    // Show modal
    document
        .getElementById('createPartyModal')
        .classList.remove('hidden');

    // Focus party name
    setTimeout(() => {
        document
            .getElementById('newPartyName')
            .focus();
    }, 100);
}


function closeCreateParty() {

    document
        .getElementById('createPartyModal')
        .classList.add('hidden');
}


function resetPartyForm() {

    document.getElementById('newPartyName').value = '';
    document.getElementById('newPartyMobile').value = '';
    document.getElementById('newPartyGST').value = '';

    document.getElementById('partyStreet').value = '';
    document.getElementById('partyState').value = '';
    document.getElementById('partyPincode').value = '';
    document.getElementById('partyCity').value = '';

    document
        .getElementById('partyAddressFields')
        .classList.add('hidden');

    document
        .getElementById('partyGSTFields')
        .classList.add('hidden');
}


// =====================================================
// ADDRESS TOGGLE
// =====================================================

document
    .getElementById('togglePartyAddressBtn')
    .onclick = () => {

        document
            .getElementById('partyAddressFields')
            .classList.remove('hidden');

    };


document
    .getElementById('removePartyAddressBtn')
    .onclick = () => {

        document
            .getElementById('partyAddressFields')
            .classList.add('hidden');

    };


// =====================================================
// GSTIN TOGGLE
// =====================================================

document
    .getElementById('togglePartyGSTBtn')
    .onclick = () => {

        document
            .getElementById('partyGSTFields')
            .classList.remove('hidden');

    };


document
    .getElementById('removePartyGSTBtn')
    .onclick = () => {

        document
            .getElementById('partyGSTFields')
            .classList.add('hidden');

    };


// =====================================================
// CLOSE BUTTONS
// =====================================================

document
    .getElementById('closeCreatePartyBtn')
    .onclick = closeCreateParty;


document
    .getElementById('cancelCreatePartyBtn')
    .onclick = closeCreateParty;


// =====================================================
// CREATE PARTY API
// =====================================================

document
    .getElementById('savePartyButton')
    .onclick = createPurchaseParty;


async function createPurchaseParty() {

    const name =
        document
            .getElementById('newPartyName')
            .value
            .trim();

    const mobile =
        document
            .getElementById('newPartyMobile')
            .value
            .trim();

    const gst =
        document
            .getElementById('newPartyGST')
            .value
            .trim()
            .toUpperCase();

    const street =
        document
            .getElementById('partyStreet')
            .value
            .trim();

    const state =
        document
            .getElementById('partyState')
            .value
            .trim();

    const pincode =
        document
            .getElementById('partyPincode')
            .value
            .trim();

    const city =
        document
            .getElementById('partyCity')
            .value
            .trim();


    if (!name) {

        toast(
            'Please enter party name.',
            false
        );

        document
            .getElementById('newPartyName')
            .focus();

        return;
    }


    const button =
        document
            .getElementById('savePartyButton');

    button.disabled = true;
    button.innerText = 'Saving...';


    try {

        const payload = {

            party_name: name,

            contact_number:
                mobile || null,

            // IMPORTANT:
            // Purchase invoice = supplier
            party_type: 'supplier',

            gst_number:
                gst || null,

            billing_street:
                street || null,

            billing_state:
                state || null,

            billing_pincode:
                pincode || null,

            billing_city:
                city || null,

            opening_balance: 0,

            // Supplier balance is payable
            opening_balance_type: 'pay'

        };


        const response =
            await fetch(
                API + '/parties',
                {
                    method: 'POST',

                    headers: {
                        'Content-Type':
                            'application/json',

                        'Accept':
                            'application/json',

                        'Authorization':
                            'Bearer ' + token
                    },

                    body:
                        JSON.stringify(payload)
                }
            );


        const result =
            await response.json();


        if (!response.ok) {

            let message =
                result.message ||
                'Unable to create party.';

            if (result.errors) {

                message =
                    Object
                        .values(result.errors)
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


        // Newly created party
        const party =
            result.data;


        // Add it to current party array
        parties.push(party);


        // Close create modal
        closeCreateParty();


        // Select newly created supplier
        selectParty(party.id);


        toast(
            'Party created successfully.'
        );


    } catch (error) {

        console.error(
            'Create Party Error:',
            error
        );

        toast(
            error.message ||
            'Unable to create party.',
            false
        );


    } finally {

        button.disabled = false;
        button.innerText = 'Save';

    }
}    

    
// document.getElementById('addChargeBtn').onclick=openChargeModal;

document.getElementById('addChargeBtn').onclick = function () {

    const list = document.getElementById('chargesList');

    const row = document.createElement('div');

    row.className =
        'grid grid-cols-[1fr_110px_150px_32px] gap-2 items-center mb-2';

    row.innerHTML = `
        <input
            type="text"
            placeholder="Enter charge"
            class="border rounded-lg px-3 py-2 text-sm"
        >

        <div class="flex items-center border rounded-lg overflow-hidden">

            <span class="px-2 text-gray-500 text-sm">
                ₹
            </span>

            <input
                type="number"
                min="0"
                step="0.01"
                value="0"
                class="charge-amount w-full border-0 px-2 py-2 text-sm focus:outline-none"
            >

        </div>

        <select
            class="charge-tax border rounded-lg px-3 py-2 text-sm"
        >
            <option value="0">
                No Tax Applicable
            </option>

            <option value="5">
                GST 5%
            </option>

            <option value="12">
                GST 12%
            </option>

            <option value="18">
                GST 18%
            </option>

            <option value="28">
                GST 28%
            </option>
        </select>

        <button
            type="button"
            class="remove-charge text-gray-400 hover:text-red-500"
        >
            ×
        </button>
    `;


    list.appendChild(row);


    const nameInput =
        row.querySelector('input[type="text"]');

    const amountInput =
        row.querySelector('.charge-amount');


    function updateCharge() {

        const name =
            nameInput.value.trim();

        const amount =
            Number(amountInput.value || 0);


        const index =
            Array.from(
                list.children
            ).indexOf(row);


        if (index >= 0) {

            additionalCharges[index] = {
                name: name,
                amount: amount
            };

        } else {

            additionalCharges.push({
                name: name,
                amount: amount
            });

        }

        recalculate();

    }


    nameInput.addEventListener(
        'input',
        updateCharge
    );

    amountInput.addEventListener(
        'input',
        updateCharge
    );


    row
        .querySelector('.remove-charge')
        .onclick = function () {

            const index =
                Array.from(
                    list.children
                ).indexOf(row);

            if (index >= 0) {
                additionalCharges.splice(
                    index,
                    1
                );
            }

            row.remove();

            recalculate();

        };


    // Create corresponding data item

    additionalCharges.push({
        name: '',
        amount: 0
    });

    recalculate();

    nameInput.focus();

};
document.getElementById('saveChargeBtn').onclick=saveCharge;

document.getElementById('itemSearch').addEventListener('input',e=>loadItems(e.target.value.trim()).catch(x=>toast(x.message,false)));
//document.getElementById('discountBtn').onclick=()=>document.getElementById('discountEditor').classList.toggle('hidden');
//document.getElementById('applyDiscountBtn').onclick=()=>{discountPercent=Math.max(0,Math.min(100,Number(document.getElementById('discountPercent').value||0)));
//recalculate();};

document.getElementById('discountBtn').onclick = () => {
    document.getElementById('discountEditor').classList.toggle('hidden');

    if (!document.getElementById('discountEditor').classList.contains('hidden')) {
        document.getElementById('discountPercent').focus();
    }
};

document.getElementById('discountPercent').oninput = () => {
    discountPercent = Math.max(
        0,
        Math.min(
            100,
            Number(document.getElementById('discountPercent').value || 0)
        )
    );

    recalculate();
};
document.getElementById('autoRoundOff').onchange=e=>{document.getElementById('manualRoundWrap').classList.toggle('hidden',e.target.checked);recalculate();};
document.getElementById('roundOff').oninput=recalculate;

document.getElementById('receivedAmount').oninput=recalculate;
document.getElementById('markFullyPaid').onchange=e=>{if(e.target.checked){const c=recalculate();
document.getElementById('receivedAmount').value=c.total.toFixed(2);}recalculate();};
document.getElementById('dueDateToggle').onclick=()=>document.getElementById('dueDate').classList.toggle('hidden');
document.getElementById('notesToggle').onclick=()=>document.getElementById('notesBox').classList.toggle('hidden');document.getElementById('saveBtn').onclick=()=>savePurchase(false);document.getElementById('saveNewBtn').onclick=()=>savePurchase(true);

window.closePartySelector = closePartySelector;
window.selectParty=selectParty;
window.closeItemModal=closeItemModal;
window.addItemFromCatalog=addItemFromCatalog;window.removeItem=removeItem;window.updateItem=updateItem;window.closeChargeModal=closeChargeModal;window.removeCharge=removeCharge;
document.getElementById('purchaseNumber').value=purchaseNo();document.getElementById('purchaseDate').value=today();loadBusiness();renderInvoiceItems();recalculate();
</script>
</body>
</html>
