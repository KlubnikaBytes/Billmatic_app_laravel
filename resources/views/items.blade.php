<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <title>Items | BillNika</title>

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1">

    <script src="https://cdn.tailwindcss.com"></script>

</head>


<body class="bg-gray-100">


<!-- ================= JWT PROTECTION ================= -->

<script>

    if (!localStorage.getItem('token')) {

        window.location.href =
            '/start-free-billing';

    }

</script>


<div class="flex min-h-screen">


    <!-- =================================================
         SIDEBAR
    ================================================== -->

    <aside
        class="w-64
               bg-gradient-to-b
               from-slate-900
               to-slate-800
               text-white
               flex
               flex-col">


        <!-- BUSINESS -->

        <div class="p-5 border-b border-white/10">

            <div class="flex items-center gap-3">

                <div
    id="sidebarBusinessInitial"
    class="w-10 h-10
           rounded-full
           bg-yellow-400
           flex items-center
           justify-center
           font-bold
           text-black">

    B

</div>

               <div>

    <p
        id="sidebarBusinessName"
        class="font-semibold">
        Business Name
    </p>

    <p
        id="sidebarBusinessMobile"
        class="text-xs text-gray-300">
        Loading...
    </p>

</div>

            </div>

        </div>



        <!-- CREATE SALES INVOICE -->

        <div class="p-3">

            <a
                href="{{ route('sales.invoice.create') }}"
                class="block w-full
                       bg-indigo-500
                       hover:bg-indigo-600
                       text-white
                       py-3
                       rounded-lg
                       font-medium
                       text-center">

                + Create Sales Invoice

            </a>

        </div>



     <nav class="flex-1 px-3 space-y-1 text-sm">

    {{-- Dashboard --}}
    <a href="{{ route('dashboard') }}"
       class="flex items-center gap-3 px-3 py-2 rounded-lg
       {{ request()->routeIs('dashboard') ? 'bg-indigo-600 text-white' : 'hover:bg-white/10 text-white' }}">
        📊 Dashboard
    </a>

    {{-- Parties --}}
    <a href="{{ route('parties') }}"
       class="flex items-center gap-3 px-3 py-2 rounded-lg
       {{ request()->routeIs('parties') || request()->routeIs('parties.*') ? 'bg-indigo-600 text-white' : 'hover:bg-white/10 text-white' }}">
        👥 Parties
    </a>

    {{-- Items --}}
    <a href="{{ route('items') }}"
       class="flex items-center gap-3 px-3 py-2 rounded-lg
       {{ request()->routeIs('items') ? 'bg-indigo-600 text-white' : 'hover:bg-white/10 text-white' }}">
        📦 Items
    </a>

    {{-- Sales --}}
    <a href="{{ route('sales') }}"
       class="flex items-center gap-3 px-3 py-2 rounded-lg
       {{ request()->routeIs('sales') ? 'bg-indigo-600 text-white' : 'hover:bg-white/10 text-white' }}">
        🧾 Sales
    </a>

    {{-- Purchases --}}
    <a href="{{ route('purchases') }}"
       class="flex items-center gap-3 px-3 py-2 rounded-lg
       {{ request()->routeIs('purchases') || request()->routeIs('purchases.*') ? 'bg-indigo-600 text-white' : 'hover:bg-white/10 text-white' }}">
        🛒 Purchases
    </a>

    {{-- Reports --}}
    <!-- <a href="#"
       class="flex items-center gap-3 px-3 py-2 rounded-lg
       hover:bg-white/10 text-white">
        📑 Reports
    </a> -->

</nav>



        <!-- FOOTER -->
<div class="border-t border-white/10 p-3">

    <!-- LOGOUT -->
    <button
        type="button"
        onclick="logout()"
        class="w-full flex items-center gap-3 px-3 py-2 rounded-lg
               text-red-300 hover:bg-red-500/10 hover:text-red-200
               transition text-sm font-medium">
        🚪
        <span>Logout</span>
    </button>

    <!-- SECURITY -->
    <div class="mt-3 px-3 text-xs text-gray-400">
        🔒 100% Secure
        <br>
        ISO Certified
    </div>

</div>

    </aside>



    <!-- =================================================
         MAIN
    ================================================== -->

    <main class="flex-1 p-6">


        <!-- TOP BAR -->

        <div
            class="flex
                   justify-between
                   items-center
                   mb-6">


            <h1 class="text-2xl font-semibold">

                Items

            </h1>



            <div class="flex gap-3">


                <!-- <button
                    type="button"
                    class="border
                           border-indigo-500
                           text-indigo-600
                           px-5 py-2
                           rounded-lg">

                    ◉ Manage Offer

                </button>


                <button
                    type="button"
                    class="border
                           border-blue-500
                           text-blue-600
                           px-5 py-2
                           rounded-lg">

                    📊 Reports

                </button>


                <button
                    type="button"
                    class="border
                           px-4 py-2
                           rounded-lg">

                    ⚙

                </button>


                <button
                    type="button"
                    class="border
                           px-4 py-2
                           rounded-lg">

                    ▤

                </button> -->

            </div>

        </div>



        <!-- =================================================
             STOCK CARDS
        ================================================== -->

        <div
            class="grid
                   grid-cols-1
                   md:grid-cols-2
                   gap-4
                   mb-5">


            <!-- STOCK VALUE -->

            <div
                class="bg-white
                       border
                       rounded-xl
                       p-5">

                <div
                    class="flex
                           justify-between
                           items-center">


                    <div>

                        <p
                            class="text-sm
                                   text-gray-500">

                            📈 Stock Value

                        </p>


                        <p
                            id="stockValue"
                            class="text-2xl
                                   font-medium
                                   mt-2">

                            ₹ 0.00

                        </p>

                    </div>


                    <span class="text-gray-500">

                        ⓘ

                    </span>

                </div>

            </div>



            <!-- LOW STOCK -->

            <div
                class="bg-white
                       border
                       rounded-xl
                       p-5">

                <p
                    class="text-sm
                           text-orange-500">

                    📦 Low Stock

                </p>


                <p
                    id="lowStockCount"
                    class="text-2xl
                           font-medium
                           mt-2">

                    0

                </p>

            </div>

        </div>



        <!-- =================================================
             SEARCH / FILTER
        ================================================== -->

        <div
            class="flex
                   justify-between
                   items-center
                   mb-4">


            <div class="flex gap-4">


                <!-- SEARCH -->

                <input
                    id="searchInput"
                    type="text"
                    placeholder="Search by Item Name"
                    class="w-64
                           border
                           rounded-lg
                           px-4 py-3
                           bg-white
                           focus:outline-none
                           focus:ring-2
                           focus:ring-indigo-400">


                <!-- CATEGORY -->

                <select
                    id="categoryFilter"
                    class="w-56
                           border
                           rounded-lg
                           px-4 py-3
                           bg-white">

                    <option value="">
                        Search Categories
                    </option>

                </select>


                <!-- LOW STOCK -->

                <button
                    id="lowStockButton"
                    type="button"
                    class="border
                           bg-white
                           px-5
                           py-3
                           rounded-lg">

                    📦 Low Stock

                </button>

            </div>



            <!-- CREATE ITEM -->

            <button
                type="button"
                onclick="createItem()"
                class="bg-indigo-600
                       hover:bg-indigo-700
                       text-white
                       px-6 py-3
                       rounded-lg
                       font-medium">

                + Create Item

            </button>

        </div>



        <!-- =================================================
             ITEMS TABLE
        ================================================== -->

        <div
            class="bg-white
                   border
                   rounded-xl
                   overflow-visible">


            <table class="w-full text-sm">


                <thead
                    class="bg-gray-100
                           border-b">

                    <tr>

                        <th
                            class="p-4
                                   text-left
                                   w-12">

                            <input
                                type="checkbox"
                                id="selectAll">

                        </th>


                        <th
                            class="p-4
                                   text-left">

                            Item Name

                        </th>


                        <th
                            class="p-4
                                   text-left">

                            Item Code

                        </th>


                        <th
                            class="p-4
                                   text-left">

                            Stock QTY

                        </th>


                        <th
                            class="p-4
                                   text-left">

                            Selling Price

                        </th>


                        <th
                            class="p-4
                                   text-left">

                            Purchase Price

                        </th>


                        <th class="p-4 w-16">

                        </th>

                    </tr>

                </thead>


                <tbody
                    id="itemsTableBody">

                    <tr>

                        <td
                            colspan="7"
                            class="p-10
                                   text-center
                                   text-gray-500">

                            Loading items...

                        </td>

                    </tr>

                </tbody>

            </table>

        </div>


    </main>

</div>


<!-- =========================================================
     CREATE NEW ITEM MODAL
========================================================= -->

<div
    id="createItemModal"
    class="fixed inset-0 z-[9999] hidden bg-slate-900/70
           flex items-center justify-center p-4">

    <div
        class="bg-white rounded-xl shadow-2xl
               w-full max-w-5xl
               max-h-[92vh]
               flex flex-col overflow-hidden">


        <!-- =================================================
             MODAL HEADER
        ================================================== -->

        <div
            class="flex items-center justify-between
                   px-5 py-4
                   border-b
                   bg-white">

           <h2
    id="itemModalTitle"
    class="text-xl font-semibold text-gray-800">

    Create New Item

</h2>


            <button
                type="button"
                onclick="closeCreateItemModal()"
                class="w-9 h-9
                       border
                       rounded-lg
                       text-gray-600
                       hover:bg-gray-100
                       text-xl">

                ×

            </button>

        </div>



        <!-- =================================================
             MODAL BODY
        ================================================== -->

        <div class="flex flex-1 min-h-0">


            <!-- =============================================
                 LEFT SIDEBAR
            ============================================== -->

            <div
                class="w-56
                       border-r
                       bg-white
                       flex-shrink-0">


                <!-- BASIC DETAILS -->

                <button
                    type="button"
                    onclick="showItemTab('basic')"
                    id="tabBasic"
                    class="item-tab
                           w-full
                           text-left
                           px-5 py-4
                           bg-indigo-50
                           text-indigo-600
                           font-medium
                           border-b">

                    📄 Basic Details
                    <span class="text-red-500">*</span>

                </button>


                <div
                    class="px-5 py-5
                           text-sm
                           font-semibold
                           text-gray-700">

                    Advance Details

                </div>


                <!-- STOCK -->

                <button
                    type="button"
                    onclick="showItemTab('stock')"
                    id="tabStock"
                    class="item-tab
                           w-full
                           text-left
                           px-5 py-3
                           text-gray-600
                           hover:bg-gray-50">

                    📦 Stock Details

                </button>


                <!-- PRICING -->

                <button
                    type="button"
                    onclick="showItemTab('pricing')"
                    id="tabPricing"
                    class="item-tab
                           w-full
                           text-left
                           px-5 py-3
                           text-gray-600
                           hover:bg-gray-50">

                    🧾 Pricing Details

                </button>


                <!-- PARTY -->

                <button
                    type="button"
                    onclick="showItemTab('party')"
                    id="tabParty"
                    class="item-tab
                           w-full
                           text-left
                           px-5 py-3
                           text-gray-600
                           hover:bg-gray-50">

                    🧾 Party Wise Prices

                </button>


                <!-- CUSTOM -->

                <button
                    type="button"
                    onclick="showItemTab('custom')"
                    id="tabCustom"
                    class="item-tab
                           w-full
                           text-left
                           px-5 py-3
                           text-gray-600
                           hover:bg-gray-50">

                    ▤ Custom Fields

                </button>

            </div>



            <!-- =============================================
                 RIGHT CONTENT
            ============================================== -->

            <div
                class="flex-1
                       overflow-y-auto
                       p-5">


                <!-- =================================================
                     BASIC DETAILS
                ================================================== -->

                <div
                    id="itemTabBasic"
                    class="item-tab-content">


                    <div
                        class="grid
                               grid-cols-1
                               md:grid-cols-2
                               gap-5">


                        <!-- ITEM TYPE -->

                        <div>

                            <label
                                class="block text-sm
                                       text-gray-600
                                       mb-2">

                                Item Type
                                <span class="text-red-500">*</span>

                            </label>


                            <div
                                class="flex gap-3">


                                <label
                                    class="flex-1
                                           border
                                           rounded-lg
                                           px-4 py-3
                                           cursor-pointer">

                                    <input
                                        type="radio"
                                        name="item_type"
                                        value="product"
                                        checked
                                        class="mr-2">

                                    Product

                                </label>


                                <label
                                    class="flex-1
                                           border
                                           rounded-lg
                                           px-4 py-3
                                           cursor-pointer">

                                    <input
                                        type="radio"
                                        name="item_type"
                                        value="service"
                                        class="mr-2">

                                    Service

                                </label>

                            </div>

                        </div>



                        <!-- CATEGORY -->

                        <div>

                            <label
                                class="block text-sm
                                       text-gray-600
                                       mb-2">

                                Category

                            </label>


                            <input
                                id="itemCategory"
                                type="text"
                                placeholder="Search Categories"
                                class="w-full
                                       border
                                       rounded-lg
                                       px-4 py-3
                                       focus:outline-none
                                       focus:ring-2
                                       focus:ring-indigo-400">

                        </div>



                        <!-- ITEM NAME -->

                        <div>

                            <label
                                class="block text-sm
                                       text-gray-600
                                       mb-2">

                                Item Name
                                <span class="text-red-500">*</span>

                            </label>


                            <input
                                id="itemName"
                                type="text"
                                placeholder="ex: Maggie 20gm"
                                class="w-full
                                       border
                                       rounded-lg
                                       px-4 py-3
                                       focus:outline-none
                                       focus:ring-2
                                       focus:ring-indigo-400">

                        </div>



                        <!-- ONLINE STORE -->

                        <div
                            class="flex
                                   items-end
                                   pb-2">

                            <label
                                class="flex
                                       items-center
                                       justify-between
                                       w-full">

                                <span
                                    class="text-sm
                                           text-gray-600">

                                    Show Item in Online Store

                                </span>


                                <input
                                    id="showOnlineStore"
                                    type="checkbox"
                                    class="w-5 h-5">

                            </label>

                        </div>



                        <!-- SALES PRICE -->

                        <div>

                            <label
                                class="block text-sm
                                       text-gray-600
                                       mb-2">

                                Sales Price

                            </label>


                            <input
                                id="salesPrice"
                                type="number"
                                step="0.01"
                                min="0"
                                placeholder="₹ ex: ₹200"
                                class="w-full
                                       border
                                       rounded-lg
                                       px-4 py-3">

                        </div>



                        <!-- GST -->

                        <div>

                            <label
                                class="block text-sm
                                       text-gray-600
                                       mb-2">

                                GST Tax Rate(%)

                            </label>


                            <select
                                id="gstPercent"
                                class="w-full
                                       border
                                       rounded-lg
                                       px-4 py-3">

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



                        <!-- MEASURING UNIT -->

                        <div>

                            <label
                                class="block text-sm
                                       text-gray-600
                                       mb-2">

                                Measuring Unit

                            </label>


                            <select
                                id="itemUnit"
                                class="w-full
                                       border
                                       rounded-lg
                                       px-4 py-3">

                                <option value="PCS">
                                    Pieces(PCS)
                                </option>

                                <option value="BOX">
                                    Box(BOX)
                                </option>

                                <option value="KG">
                                    Kilogram(KG)
                                </option>

                                <option value="GM">
                                    Gram(GM)
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

                            <label
                                class="block text-sm
                                       text-gray-600
                                       mb-2">

                                Opening Stock

                            </label>


                            <input
                                id="openingStock"
                                type="number"
                                step="0.01"
                                min="0"
                                placeholder="ex: 150 PCS"
                                class="w-full
                                       border
                                       rounded-lg
                                       px-4 py-3">

                        </div>

                    </div>

                </div>



                <!-- =================================================
                     STOCK DETAILS
                ================================================== -->

                <div
                    id="itemTabStock"
                    class="item-tab-content hidden">


                    <div
                        class="grid
                               grid-cols-1
                               md:grid-cols-2
                               gap-5">


                        <!-- ITEM CODE -->

                        <div>

                            <label
                                class="block text-sm
                                       text-gray-600
                                       mb-2">

                                Item Code

                            </label>


                            <div class="flex">

                                <input
                                    id="itemCode"
                                    type="text"
                                    placeholder="ex: ITM12549"
                                    class="flex-1
                                           border
                                           rounded-l-lg
                                           px-4 py-3">


                                <button
                                    type="button"
                                    onclick="generateItemCode()"
                                    class="border
                                           border-l-0
                                           rounded-r-lg
                                           px-4
                                           text-blue-600
                                           bg-blue-50">

                                    Generate

                                </button>

                            </div>

                        </div>



                        <!-- HSN -->

                        <div>

                            <label
                                class="block text-sm
                                       text-gray-600
                                       mb-2">

                                HSN Code

                            </label>


                            <input
                                id="hsnCode"
                                type="text"
                                placeholder="ex: 4010"
                                class="w-full
                                       border
                                       rounded-lg
                                       px-4 py-3">

                        </div>



                        <!-- UNIT -->

                        <div>

                            <label
                                class="block text-sm
                                       text-gray-600
                                       mb-2">

                                Measuring Unit

                            </label>


                            <select
                                id="stockUnit"
                                class="w-full
                                       border
                                       rounded-lg
                                       px-4 py-3">

                                <option value="PCS">
                                    Pieces(PCS)
                                </option>

                                <option value="BOX">
                                    Box(BOX)
                                </option>

                                <option value="KG">
                                    Kilogram(KG)
                                </option>

                                <option value="GM">
                                    Gram(GM)
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
                                       text-gray-600
                                       mb-2">

                                Opening Stock

                            </label>


                            <input
                                id="stockOpeningStock"
                                type="number"
                                step="0.01"
                                min="0"
                                placeholder="ex: 150"
                                class="w-full
                                       border
                                       rounded-lg
                                       px-4 py-3">

                        </div>



                        <!-- AS OF DATE -->

                        <div>

                            <label
                                class="block text-sm
                                       text-gray-600
                                       mb-2">

                                As of Date

                            </label>


                            <input
                                id="stockAsOfDate"
                                type="date"
                                class="w-full
                                       border
                                       rounded-lg
                                       px-4 py-3">

                        </div>



                        <!-- LOW STOCK -->

                        <div
                            class="flex
                                   items-center
                                   pt-7">


                            <label
                                class="flex
                                       items-center
                                       gap-3
                                       cursor-pointer">

                                <input
                                    id="lowStockAlert"
                                    type="checkbox"
                                    class="w-5 h-5">

                                <span
                                    class="text-sm
                                           text-blue-600">

                                    Enable Low Stock Quantity Warning

                                </span>

                            </label>

                        </div>



                        <!-- LOW STOCK QUANTITY -->

                        <div>

                            <label
                                class="block text-sm
                                       text-gray-600
                                       mb-2">

                                Low Stock Quantity

                            </label>


                            <input
                                id="lowStockQuantity"
                                type="number"
                                step="0.01"
                                min="0"
                                placeholder="ex: 10"
                                class="w-full
                                       border
                                       rounded-lg
                                       px-4 py-3">

                        </div>



                        <!-- DESCRIPTION -->

                        <div class="md:col-span-2">

                            <label
                                class="block text-sm
                                       text-gray-600
                                       mb-2">

                                Description

                            </label>


                            <textarea
                                id="itemDescription"
                                rows="5"
                                placeholder="Enter Description"
                                class="w-full
                                       border
                                       rounded-lg
                                       px-4 py-3"></textarea>

                        </div>

                    </div>

                </div>



                <!-- =================================================
                     PRICING DETAILS
                ================================================== -->

                <div
                    id="itemTabPricing"
                    class="item-tab-content hidden">


                    <div
                        class="grid
                               grid-cols-1
                               md:grid-cols-2
                               gap-5">


                        <!-- SALES PRICE -->

                        <div>

                            <label
                                class="block text-sm
                                       text-gray-600
                                       mb-2">

                                Sales Price

                            </label>


                            <input
                                id="pricingSalesPrice"
                                type="number"
                                step="0.01"
                                min="0"
                                placeholder="₹ ex: ₹200"
                                class="w-full
                                       border
                                       rounded-lg
                                       px-4 py-3">

                        </div>



                        <!-- PURCHASE PRICE -->

                        <div>

                            <label
                                class="block text-sm
                                       text-gray-600
                                       mb-2">

                                Purchase Price

                            </label>


                            <input
                                id="purchasePrice"
                                type="number"
                                step="0.01"
                                min="0"
                                placeholder="₹ ex: ₹200"
                                class="w-full
                                       border
                                       rounded-lg
                                       px-4 py-3">

                        </div>



                        <!-- GST -->

                        <div>

                            <label
                                class="block text-sm
                                       text-gray-600
                                       mb-2">

                                GST Tax Rate(%)

                            </label>


                            <select
                                id="pricingGstPercent"
                                class="w-full
                                       border
                                       rounded-lg
                                       px-4 py-3">

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



                        <!-- DISCOUNT -->

                        <div>

                            <label
                                class="block text-sm
                                       text-gray-600
                                       mb-2">

                                Discount on Sales Price

                            </label>


                            <div class="flex">

                                <input
                                    id="itemDiscount"
                                    type="number"
                                    step="0.01"
                                    min="0"
                                    placeholder="ex: 12"
                                    class="flex-1
                                           border
                                           rounded-l-lg
                                           px-4 py-3">


                                <span
                                    class="border
                                           border-l-0
                                           rounded-r-lg
                                           px-4 py-3
                                           bg-gray-50">

                                    %

                                </span>

                            </div>

                        </div>

                    </div>

                </div>



                <!-- =================================================
                     PARTY WISE PRICES
                ================================================== -->

                <div
                    id="itemTabParty"
                    class="item-tab-content hidden">


                    <div
                        class="min-h-[380px]
                               flex
                               items-center
                               justify-center">


                        <div
                            class="bg-gray-50
                                   rounded-xl
                                   p-10
                                   text-center
                                   max-w-lg">


                            <div
                                class="text-6xl
                                       mb-5">

                                🧾

                            </div>


                            <h3
                                class="font-semibold
                                       text-gray-700
                                       mb-3">

                                Party Wise Prices

                            </h3>


                            <p
                                class="text-sm
                                       text-gray-500">

                                To enable Party Wise Prices
                                and set custom prices for parties,
                                please save the item first.

                            </p>

                        </div>

                    </div>

                </div>



                <!-- =================================================
                     CUSTOM FIELDS
                ================================================== -->

                <div
                    id="itemTabCustom"
                    class="item-tab-content hidden">


                    <div
                        class="bg-amber-50
                               border
                               border-amber-100
                               rounded-lg
                               px-4 py-3
                               text-sm
                               text-gray-700
                               mb-6">

                        ℹ️ To add/manage item custom fields,
                        go to <strong>Item Settings</strong>.

                    </div>


                    <div
                        class="min-h-[320px]
                               flex
                               items-center
                               justify-center">


                        <div class="text-center">

                            <div
                                class="text-6xl
                                       mb-4">

                                📋

                            </div>


                            <p
                                class="text-gray-500">

                                You don't have any custom
                                fields created yet

                            </p>

                        </div>

                    </div>

                </div>


            </div>

        </div>



        <!-- =================================================
             FOOTER
        ================================================== -->

        <div
            class="flex
                   items-center
                   justify-between
                   px-5 py-4
                   border-t
                   bg-white">


            <button
                type="button"
                onclick="closeCreateItemModal()"
                class="border
                       px-8 py-3
                       rounded-lg
                       text-gray-700
                       hover:bg-gray-50">

                Cancel

            </button>


            <div class="flex gap-3">


                <button
                    type="button"
                    onclick="saveItem(true)"
                    id="saveNewItemButton"
                    disabled
                    class="px-7 py-3
                           rounded-lg
                           bg-gray-200
                           text-gray-400
                           font-medium">

                    Save & New

                </button>


                <button
                    type="button"
                    onclick="saveItem(false)"
                    id="saveItemButton"
                    disabled
                    class="px-8 py-3
                           rounded-lg
                           bg-indigo-100
                           text-indigo-300
                           font-medium">

                    Save Item

                </button>

            </div>

        </div>

    </div>

</div>



<!-- =====================================================
     JAVASCRIPT
====================================================== -->

<script>

/* =====================================================
   LOAD BUSINESS INFO
===================================================== */

async function loadBusinessInfo() {

    const token = localStorage.getItem('token');

    if (!token) {
        window.location.href = '/start-free-billing';
        return;
    }

    try {

        const response = await fetch('/api/business-info', {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'Authorization': 'Bearer ' + token
            }
        });

        if (!response.ok) {
            throw new Error('Unable to load business information');
        }

        const result = await response.json();

        if (!result.success) {
            throw new Error(
                result.message || 'Unable to load business information'
            );
        }

        const business = result.data || {};

        const businessName =
    business.business_name || 'Business Name';

const businessMobile =
    business.mobile || '';


// Business Name
document.getElementById('sidebarBusinessName').innerText =
    businessName;


// Mobile Number
document.getElementById('sidebarBusinessMobile').innerText =
    businessMobile;


// Business Logo / Initial
document.getElementById('sidebarBusinessInitial').innerText =
    businessName
        .trim()
        .charAt(0)
        .toUpperCase();

    } catch (error) {

        console.error(
            'Business Info Error:',
            error
        );

    }
}    


let allItems = [];

let lowStockOnly = false;


// Currently edited item ID
let editingItemId = null;



/* =====================================================
   LOAD ITEMS
===================================================== */

async function loadItems() {

    const token =
        localStorage.getItem('token');


    try {

        const response =
            await fetch(
                '/api/items',
                {
                    method: 'GET',

                    headers: {
                        'Accept':
                            'application/json',

                        'Authorization':
                            'Bearer ' + token
                    }
                }
            );


        if (!response.ok) {

            throw new Error(
                'Unable to load items'
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


        allItems =
            result.data || [];


        renderItems();


        loadStockSummary();


    } catch (error) {

        console.error(
            'Items API Error:',
            error
        );


        document
            .getElementById(
                'itemsTableBody'
            )
            .innerHTML = `

                <tr>

                    <td
                        colspan="7"
                        class="p-10
                               text-center
                               text-red-500">

                        Unable to load items.

                    </td>

                </tr>

            `;

    }

}

/* =====================================================
   CREATE ITEM MODAL
===================================================== */

function closeCreateItemModal() {

    document
        .getElementById('createItemModal')
        .classList.add('hidden');

    document.body.classList.remove('overflow-hidden');

}



/* =====================================================
   ITEM TABS
===================================================== */

function showItemTab(tab) {

    document
        .querySelectorAll('.item-tab-content')
        .forEach(content => {

            content.classList.add('hidden');

        });


    document
        .querySelectorAll('.item-tab')
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


    const content =
        document.getElementById(
            'itemTab' +
            tab.charAt(0).toUpperCase() +
            tab.slice(1)
        );


    const button =
        document.getElementById(
            'tab' +
            tab.charAt(0).toUpperCase() +
            tab.slice(1)
        );


    if (content) {

        content.classList.remove('hidden');

    }


    if (button) {

        button.classList.remove(
            'text-gray-600'
        );

        button.classList.add(
            'bg-indigo-50',
            'text-indigo-600',
            'font-medium'
        );

    }

}



/* =====================================================
   RESET FORM
===================================================== */

function resetCreateItemForm() {

    document
        .getElementById('itemName')
        .value = '';

    document
        .getElementById('itemCategory')
        .value = '';

    document
        .getElementById('salesPrice')
        .value = '';

    document
        .getElementById('purchasePrice')
        .value = '';

    document
        .getElementById('gstPercent')
        .value = '';

    document
        .getElementById('itemUnit')
        .value = 'PCS';

    document
        .getElementById('openingStock')
        .value = '';

    document
        .getElementById('itemCode')
        .value = '';

    document
        .getElementById('hsnCode')
        .value = '';

    document
        .getElementById('stockUnit')
        .value = 'PCS';

    document
        .getElementById('stockOpeningStock')
        .value = '';

    document
        .getElementById('stockAsOfDate')
        .value =
        new Date()
            .toISOString()
            .split('T')[0];

    document
        .getElementById('lowStockAlert')
        .checked = false;

    document
        .getElementById('lowStockQuantity')
        .value = '';

    document
        .getElementById('itemDescription')
        .value = '';

    document
        .getElementById('pricingSalesPrice')
        .value = '';

    document
        .getElementById('pricingGstPercent')
        .value = '';

    document
        .getElementById('itemDiscount')
        .value = '';

    document
        .getElementById('showOnlineStore')
        .checked = false;


    document
        .querySelector(
            'input[name="item_type"][value="product"]'
        )
        .checked = true;


    updateSaveButtons();

}



/* =====================================================
   ENABLE / DISABLE SAVE
===================================================== */

document
    .getElementById('itemName')
    .addEventListener(
        'input',
        updateSaveButtons
    );


function updateSaveButtons() {

    const name =
        document
            .getElementById('itemName')
            .value
            .trim();


    const enabled =
        name.length > 0;


    const saveButton =
        document
            .getElementById(
                'saveItemButton'
            );


    const saveNewButton =
        document
            .getElementById(
                'saveNewItemButton'
            );


    saveButton.disabled =
        !enabled;

    saveNewButton.disabled =
        !enabled;


    if (enabled) {

        saveButton.classList.remove(
            'bg-indigo-100',
            'text-indigo-300'
        );

        saveButton.classList.add(
            'bg-indigo-600',
            'text-white',
            'hover:bg-indigo-700'
        );


        saveNewButton.classList.remove(
            'bg-gray-200',
            'text-gray-400'
        );

        saveNewButton.classList.add(
            'bg-white',
            'border',
            'text-indigo-600'
        );

    } else {

        saveButton.classList.add(
            'bg-indigo-100',
            'text-indigo-300'
        );

        saveButton.classList.remove(
            'bg-indigo-600',
            'text-white',
            'hover:bg-indigo-700'
        );


        saveNewButton.classList.add(
            'bg-gray-200',
            'text-gray-400'
        );

        saveNewButton.classList.remove(
            'bg-white',
            'border',
            'text-indigo-600'
        );

    }

}



/* =====================================================
   GENERATE ITEM CODE
===================================================== */

function generateItemCode() {

    const code =
        'ITM' +
        Math.floor(
            100000 +
            Math.random() * 900000
        );


    document
        .getElementById('itemCode')
        .value = code;

}



/* =====================================================
   SYNC BASIC / PRICING / STOCK VALUES
===================================================== */

document
    .getElementById('salesPrice')
    .addEventListener(
        'input',
        function() {

            document
                .getElementById(
                    'pricingSalesPrice'
                )
                .value = this.value;

        }
    );


document
    .getElementById('pricingSalesPrice')
    .addEventListener(
        'input',
        function() {

            document
                .getElementById(
                    'salesPrice'
                )
                .value = this.value;

        }
    );


document
    .getElementById('gstPercent')
    .addEventListener(
        'change',
        function() {

            document
                .getElementById(
                    'pricingGstPercent'
                )
                .value = this.value;

        }
    );


document
    .getElementById('pricingGstPercent')
    .addEventListener(
        'change',
        function() {

            document
                .getElementById(
                    'gstPercent'
                )
                .value = this.value;

        }
    );


document
    .getElementById('itemUnit')
    .addEventListener(
        'change',
        function() {

            document
                .getElementById(
                    'stockUnit'
                )
                .value = this.value;

        }
    );


document
    .getElementById('stockUnit')
    .addEventListener(
        'change',
        function() {

            document
                .getElementById(
                    'itemUnit'
                )
                .value = this.value;

        }
    );


document
    .getElementById('openingStock')
    .addEventListener(
        'input',
        function() {

            document
                .getElementById(
                    'stockOpeningStock'
                )
                .value = this.value;

        }
    );


document
    .getElementById('stockOpeningStock')
    .addEventListener(
        'input',
        function() {

            document
                .getElementById(
                    'openingStock'
                )
                .value = this.value;

        }
    );



/* =====================================================
   SAVE ITEM
===================================================== */

async function saveItem(saveAndNew = false) {

    const name =
        document
            .getElementById('itemName')
            .value
            .trim();


    if (!name) {

        alert(
            'Please enter Item Name.'
        );

        showItemTab('basic');

        return;

    }


    const token =
        localStorage.getItem('token');


    const itemType =
        document
            .querySelector(
                'input[name="item_type"]:checked'
            )
            ?.value || 'product';


    const salesPrice =
        parseFloat(
            document
                .getElementById(
                    'salesPrice'
                )
                .value
        ) || 0;


    const purchasePrice =
        parseFloat(
            document
                .getElementById(
                    'purchasePrice'
                )
                .value
        ) || 0;


    const gstValue =
        document
            .getElementById(
                'gstPercent'
            )
            .value;


    const openingStock =
        parseFloat(
            document
                .getElementById(
                    'openingStock'
                )
                .value
        ) || 0;


    const itemCode =
        document
            .getElementById(
                'itemCode'
            )
            .value
            .trim();


    const hsnCode =
        document
            .getElementById(
                'hsnCode'
            )
            .value
            .trim();


    const category =
        document
            .getElementById(
                'itemCategory'
            )
            .value
            .trim();


    const description =
        document
            .getElementById(
                'itemDescription'
            )
            .value
            .trim();


    const lowStockQuantity =
        parseFloat(
            document
                .getElementById(
                    'lowStockQuantity'
                )
                .value
        ) || 0;


    const stockAsOfDate =
        document
            .getElementById(
                'stockAsOfDate'
            )
            .value;


    const unit =
        document
            .getElementById(
                'itemUnit'
            )
            .value;


    const payload = {

        name: name,

        item_type: itemType,

        inventory_tracking_by:
            itemType === 'product'
                ? 'quantity'
                : null,

        unit: unit,

        sales_price: salesPrice,

        purchase_price: purchasePrice,

        gst_percent:
        gstValue === ''
        ? 0
        : Number(gstValue),

        hsn_code:
            hsnCode || null,

        opening_stock:
            openingStock,

        stock_as_of_date:
            stockAsOfDate || null,

        item_code:
            itemCode || null,

        item_categories:
            category
                ? [category]
                : [],

        description:
            description || null,

        low_stock_alert:
            document
                .getElementById(
                    'lowStockAlert'
                )
                .checked,

        low_stock_quantity:
            lowStockQuantity,

        show_in_online_store:
            document
                .getElementById(
                    'showOnlineStore'
                )
                .checked,

    };


    const saveButton =
        document
            .getElementById(
                'saveItemButton'
            );


    saveButton.disabled = true;

    saveButton.innerText =
        'Saving...';


    try {

        const url =
    editingItemId
        ? `/api/items/${editingItemId}`
        : '/api/items';

const method =
    editingItemId
        ? 'PUT'
        : 'POST';

const response =
    await fetch(
        url,
        {
            method: method,

            headers: {
                'Accept':
                    'application/json',

                'Content-Type':
                    'application/json',

                'Authorization':
                    'Bearer ' + token
            },

            body:
                JSON.stringify(
                    payload
                )
        }
    );


        const result =
            await response.json();


        if (!response.ok) {

            let message =
                result.message ||
                'Unable to create item.';


            if (
                result.errors
            ) {

                const errors =
                    Object.values(
                        result.errors
                    )
                    .flat();


                if (errors.length) {

                    message =
                        errors.join('\n');

                }

            }


            throw new Error(
                message
            );

        }


       alert(
    result.message ||
    (
        editingItemId
            ? 'Item updated successfully.'
            : 'Item created successfully.'
    )
);

       editingItemId = null;

       document
    .getElementById('itemModalTitle')
    .innerText = 'Create New Item';


        if (saveAndNew) {

            resetCreateItemForm();

            showItemTab('basic');

        } else {

            closeCreateItemModal();

        }


        await loadItems();


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

        saveButton.disabled = false;

        saveButton.innerText =
            'Save Item';

        updateSaveButtons();

    }

}



/* =====================================================
   ESCAPE KEY
===================================================== */

document.addEventListener(
    'keydown',
    function(event) {

        if (
            event.key === 'Escape'
        ) {

            const modal =
                document
                    .getElementById(
                        'createItemModal'
                    );


            if (
                modal &&
                !modal.classList.contains(
                    'hidden'
                )
            ) {

                closeCreateItemModal();

            }

        }

    }
);



/* =====================================================
   STOCK SUMMARY
===================================================== */

async function loadStockSummary() {

    const token =
        localStorage.getItem('token');


    try {

        const response =
            await fetch(
                '/api/stock-summary',
                {
                    headers: {
                        'Accept':
                            'application/json',

                        'Authorization':
                            'Bearer ' + token
                    }
                }
            );


        const result =
            await response.json();


        if (!result.success) {
            return;
        }


        const data =
            result.data || {};


        document
            .getElementById(
                'stockValue'
            )
            .innerText =
            '₹ ' +
            Number(
                data.total_stock_value || 0
            ).toLocaleString(
                'en-IN',
                {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                }
            );


        const items =
            data.items || [];


        const lowCount =
            items.filter(item => {

                const qty =
                    Number(
                        item.quantity || 0
                    );

                const lowQty =
                    Number(
                        item.low_stock_quantity || 0
                    );

                return (
                    item.low_stock_alert &&
                    qty <= lowQty
                );

            }).length;


        document
            .getElementById(
                'lowStockCount'
            )
            .innerText =
            lowCount;

    } catch (error) {

        console.error(
            'Stock Summary Error:',
            error
        );

    }

}



/* =====================================================
   RENDER ITEMS
===================================================== */

function renderItems() {

    const search =
        document
            .getElementById(
                'searchInput'
            )
            .value
            .trim()
            .toLowerCase();


    let items =
        allItems.filter(item => {

            const matchesSearch =
                !search ||
                String(
                    item.name || ''
                )
                .toLowerCase()
                .includes(search);


            const stock =
                Number(
                    item.opening_stock || 0
                );


            const lowQty =
                Number(
                    item.low_stock_quantity || 0
                );


            const matchesLowStock =
                !lowStockOnly ||
                (
                    item.low_stock_alert &&
                    stock <= lowQty
                );


            return (
                matchesSearch &&
                matchesLowStock
            );

        });


    const tbody =
        document
            .getElementById(
                'itemsTableBody'
            );


    if (items.length === 0) {

        tbody.innerHTML = `

            <tr>

                <td
                    colspan="7"
                    class="p-10
                           text-center
                           text-gray-500">

                    No items found.

                </td>

            </tr>

        `;

        return;

    }


    tbody.innerHTML =
        items.map(item => {


            const stock =
                Number(
                    item.opening_stock || 0
                );


            const salesPrice =
                Number(
                    item.sales_price || 0
                );


            const purchasePrice =
                Number(
                    item.purchase_price || 0
                );


            const lowQty =
                Number(
                    item.low_stock_quantity || 0
                );


            const isLow =
                item.low_stock_alert &&
                stock <= lowQty;


            return `

                <tr
                    class="border-b
                           hover:bg-gray-50">


                    <!-- CHECKBOX -->

                    <td class="p-4">

                        <input
                            type="checkbox"
                            class="itemCheckbox"
                            value="${item.id}">

                    </td>


                    <!-- ITEM NAME -->

                    <td
                        class="p-4
                               font-medium
                               text-gray-700">

                        ${escapeHtml(
                            item.name || '-'
                        )}

                    </td>


                    <!-- ITEM CODE -->

                    <td class="p-4">

                        ${escapeHtml(
                            item.item_code || '-'
                        )}

                    </td>


                    <!-- STOCK -->

                    <td class="p-4">

                        <span
                            class="${
                                isLow
                                ? 'text-red-600 font-semibold'
                                : ''
                            }">

                            ${formatNumber(stock)}
                            ${escapeHtml(
                                item.unit || ''
                            )}

                        </span>

                    </td>


                    <!-- SELLING PRICE -->

                    <td class="p-4">

                        ₹ ${formatMoney(
                            salesPrice
                        )}

                    </td>


                    <!-- PURCHASE PRICE -->

                    <td class="p-4">

                        ₹ ${formatMoney(
                            purchasePrice
                        )}

                    </td>


                    <!-- MENU -->

                    <td
                        class="p-4
                               text-right
                               relative">


                        <button
                            type="button"
                            onclick="toggleItemMenu(
                                ${item.id},
                                this
                            )"
                            class="text-xl
                                   text-gray-500
                                   hover:text-gray-800
                                   w-8 h-8">

                            ⋮

                        </button>


                    </td>

                </tr>

            `;

        })
        .join('');

}



/* =====================================================
   ITEM MENU
===================================================== */

function toggleItemMenu(id, button) {


    document
        .querySelectorAll(
            '.item-action-menu'
        )
        .forEach(
            menu => menu.remove()
        );


    const menu =
        document.createElement('div');


    menu.className =
        'item-action-menu ' +
        'absolute right-4 top-12 ' +
        'bg-white border ' +
        'rounded-lg shadow-xl ' +
        'z-[9999] w-36';


    menu.innerHTML = `

        <button
            type="button"
            onclick="editItem(${id})"
            class="w-full
                   text-left
                   px-4 py-3
                   hover:bg-gray-100">

            ✏️ Edit

        </button>


        <button
            type="button"
            onclick="deleteItem(${id})"
            class="w-full
                   text-left
                   px-4 py-3
                   hover:bg-red-50
                   text-red-600">

            🗑️ Delete

        </button>

    `;


    button.parentElement
        .appendChild(menu);

}



/* =====================================================
   EDIT ITEM
===================================================== */

/* =====================================================
   EDIT ITEM
===================================================== */

async function editItem(id) {

    const token =
        localStorage.getItem('token');

    try {

        const response =
            await fetch(
                `/api/items/${id}`,
                {
                    method: 'GET',

                    headers: {
                        'Accept':
                            'application/json',

                        'Authorization':
                            'Bearer ' + token
                    }
                }
            );

        const result =
            await response.json();

        if (!response.ok || !result.success) {

            throw new Error(
                result.message ||
                'Unable to load item.'
            );

        }

        const item =
            result.data;

        // Set edit mode
        editingItemId = id;

        // Change modal title
        document
            .getElementById('itemModalTitle')
            .innerText = 'Edit Item';

        // =========================
        // BASIC DETAILS
        // =========================

        document
            .getElementById('itemName')
            .value =
            item.name || '';

        document
            .getElementById('itemCategory')
            .value =
            Array.isArray(item.item_categories)
                ? (item.item_categories[0] || '')
                : '';

        document
            .getElementById('salesPrice')
            .value =
            item.sales_price ?? '';

        document
            .getElementById('purchasePrice')
            .value =
            item.purchase_price ?? '';

        document
            .getElementById('gstPercent')
            .value =
            item.gst_percent ?? 0;

        document
            .getElementById('itemUnit')
            .value =
            item.unit || 'PCS';

        document
            .getElementById('openingStock')
            .value =
            item.opening_stock ?? '';

        document
            .getElementById('showOnlineStore')
            .checked =
            Boolean(item.show_in_online_store);

        // Item type
        const itemTypeRadio =
            document.querySelector(
                `input[name="item_type"][value="${item.item_type || 'product'}"]`
            );

        if (itemTypeRadio) {
            itemTypeRadio.checked = true;
        }

        // =========================
        // STOCK DETAILS
        // =========================

        document
            .getElementById('itemCode')
            .value =
            item.item_code || '';

        document
            .getElementById('hsnCode')
            .value =
            item.hsn_code || '';

        document
            .getElementById('stockUnit')
            .value =
            item.unit || 'PCS';

        document
            .getElementById('stockOpeningStock')
            .value =
            item.opening_stock ?? '';

        document
            .getElementById('stockAsOfDate')
            .value =
            item.stock_as_of_date
                ? String(item.stock_as_of_date).substring(0, 10)
                : '';

        document
            .getElementById('lowStockAlert')
            .checked =
            Boolean(item.low_stock_alert);

        document
            .getElementById('lowStockQuantity')
            .value =
            item.low_stock_quantity ?? '';

        document
            .getElementById('itemDescription')
            .value =
            item.description || '';

        // =========================
        // PRICING DETAILS
        // =========================

        document
            .getElementById('pricingSalesPrice')
            .value =
            item.sales_price ?? '';

        document
            .getElementById('pricingGstPercent')
            .value =
            item.gst_percent ?? 0;

        document
            .getElementById('itemDiscount')
            .value =
            item.discount_percent ?? '';

        // Open modal
        document
            .getElementById('createItemModal')
            .classList.remove('hidden');

        document.body
            .classList.add('overflow-hidden');

        showItemTab('basic');

        updateSaveButtons();

    } catch (error) {

        console.error(
            'Edit Item Error:',
            error
        );

        alert(
            error.message ||
            'Unable to load item.'
        );
    }
}



/* =====================================================
   CREATE ITEM
===================================================== */

// function createItem() {

//     window.location.href =
//         '/items/create';

// }

function createItem() {

    // New item mode
    editingItemId = null;

    document
        .getElementById('itemModalTitle')
        .innerText = 'Create New Item';

    document
        .getElementById('createItemModal')
        .classList.remove('hidden');

    document.body
        .classList.add('overflow-hidden');

    resetCreateItemForm();

    showItemTab('basic');
}



/* =====================================================
   DELETE ITEM
===================================================== */

async function deleteItem(id) {


    const item =
        allItems.find(
            item =>
                Number(item.id) ===
                Number(id)
        );


    if (!item) {
        return;
    }


    const confirmed =
        confirm(
            `Are you sure you want to delete "${item.name}"?`
        );


    if (!confirmed) {
        return;
    }


    const token =
        localStorage.getItem('token');


    try {

        const response =
            await fetch(
                `/api/items/${id}`,
                {
                    method: 'DELETE',

                    headers: {
                        'Accept':
                            'application/json',

                        'Authorization':
                            'Bearer ' + token
                    }
                }
            );


        const result =
            await response.json();


        if (!response.ok) {

            throw new Error(
                result.message ||
                'Unable to delete item'
            );

        }


        alert(
            result.message ||
            'Item deleted successfully'
        );


        await loadItems();


    } catch (error) {

        console.error(
            'Delete Item Error:',
            error
        );


        alert(
            error.message ||
            'Unable to delete item'
        );

    }

}



/* =====================================================
   SEARCH
===================================================== */

document
    .getElementById(
        'searchInput'
    )
    .addEventListener(
        'input',
        renderItems
    );



/* =====================================================
   LOW STOCK
===================================================== */

document
    .getElementById(
        'lowStockButton'
    )
    .addEventListener(
        'click',
        function() {

            lowStockOnly =
                !lowStockOnly;


            this.classList.toggle(
                'bg-orange-100',
                lowStockOnly
            );


            renderItems();

        }
    );



/* =====================================================
   SELECT ALL
===================================================== */

document
    .getElementById(
        'selectAll'
    )
    .addEventListener(
        'change',
        function() {

            document
                .querySelectorAll(
                    '.itemCheckbox'
                )
                .forEach(
                    checkbox =>
                        checkbox.checked =
                        this.checked
                );

        }
    );



/* =====================================================
   CLOSE MENU
===================================================== */

document.addEventListener(
    'click',
    function(event) {

        if (
            !event.target.closest(
                'button[onclick^="toggleItemMenu"]'
            )
        ) {

            document
                .querySelectorAll(
                    '.item-action-menu'
                )
                .forEach(
                    menu => menu.remove()
                );

        }

    }
);



/* =====================================================
   HELPERS
===================================================== */

function formatMoney(value) {

    return Number(
        value || 0
    ).toLocaleString(
        'en-IN',
        {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        }
    );

}


function formatNumber(value) {

    return Number(
        value || 0
    ).toLocaleString(
        'en-IN'
    );

}


function escapeHtml(value) {

    return String(value ?? '')
        .replace(
            /[&<>"']/g,
            function(char) {

                const entities = {

                    '&': '&amp;',
                    '<': '&lt;',
                    '>': '&gt;',
                    '"': '&quot;',
                    "'": '&#039;'

                };

                return entities[char];

            }
        );

}



/* =====================================================
   INITIAL LOAD
===================================================== */


loadBusinessInfo();

loadItems();

async function logout() {
    const token = localStorage.getItem('token');

    try {
        if (token) {
            await fetch('/api/logout', {
                method: 'POST',
                headers: {
                    'Authorization': 'Bearer ' + token,
                    'Accept': 'application/json'
                }
            });
        }
    } catch (error) {
        console.error('Logout error:', error);
    }

    // Always remove local login token
    localStorage.removeItem('token');

    // Also remove any other saved auth data if present
    localStorage.removeItem('user');

    // Go back to login/start page
    window.location.href = '/start-free-billing';
}

</script>


</body>
</html>