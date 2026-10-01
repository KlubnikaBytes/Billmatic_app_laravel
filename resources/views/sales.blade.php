<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <title>
        Sales Invoices | BillNika
    </title>

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1">

    <script src="https://cdn.tailwindcss.com"></script>

    <style>

    /* =========================================
       SALES SIDEBAR
    ========================================= */

    .sales-section {
        background: rgba(255, 255, 255, 0.04);
        border-radius: 8px;
        padding: 5px;
        margin-bottom: 4px;
    }

    .sales-title {
        padding: 10px 10px;
        font-weight: 600;
        color: white;
        cursor: default;
    }

    .sales-child {
        display: block;
        padding: 10px 25px;
        border-radius: 6px;
        color: #c8ccda;
        text-decoration: none;
        cursor: pointer;
        margin-bottom: 2px;
    }

    .sales-child:hover {
        background: rgba(255, 255, 255, 0.08);
        color: white;
    }

    .sales-child.active {
        background: #5548ed;
        color: white;
    }

</style>

</head>


<body class="bg-gray-100">


<!-- =====================================================
     JWT PROTECTION
===================================================== -->

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


        <!-- USER INFO -->

        <div
            class="p-5
                   border-b
                   border-white/10">

            <div
                class="flex
                       items-center
                       gap-3">

               <div
    id="sidebarBusinessInitial"
    class="w-10
           h-10
           rounded-full
           bg-yellow-400
           flex
           items-center
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

        <div>

            <a
                href="{{ route('sales.invoice.create') }}"
                class="block
                       w-full
                       bg-indigo-500
                       hover:bg-indigo-600
                       text-white
                       py-3
                       rounded-lg
                       font-medium
                       text-center
                       transition">

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

    <!-- {{-- Sales --}} -->
    <!-- <a href="{{ route('sales') }}"
       class="flex items-center gap-3 px-3 py-2 rounded-lg
       {{ request()->routeIs('sales') ? 'bg-indigo-600 text-white' : 'hover:bg-white/10 text-white' }}">
        🧾 Sales
    </a> -->

     <!-- SALES -->
        <!-- <div class="sales-section">

            <div class="sales-title">
                🏷️ Sales
            </div>

            <div class="sales-child"
                 onclick="window.location.href='/sales'">
                Sales Invoices
            </div>

            <div class="sales-child active"
                 onclick="window.location.href='/payment-in'">
                Payment In
            </div>

        </div> -->

        {{-- SALES --}}
<div class="sales-section">

    <div class="sales-title">
        🏷️ Sales
    </div>

    <a
        href="{{ route('sales') }}"
        class="sales-child
        {{ request()->routeIs('sales') ? 'active' : '' }}">
        Sales Invoices
    </a>

    <a
        href="{{ route('payment.in') }}"
        class="sales-child
        {{ request()->routeIs('payment.in') || request()->routeIs('payment.in.create') ? 'active' : '' }}">
        Payment In
    </a>

</div>

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
         MAIN CONTENT
    ================================================== -->

    <main
        class="flex-1
               p-5
               overflow-hidden">


        <div
            class="bg-white
                   border
                   rounded-xl
                   min-h-[calc(100vh-40px)]
                   p-5">


            <!-- =========================================
                 HEADER
            ========================================== -->

            <div
                class="flex
                       items-center
                       justify-between
                       mb-6">

                <h1
                    class="text-xl
                           font-semibold
                           text-gray-800">

                    Sales Invoices

                </h1>


                <div
                    class="flex
                           items-center
                           gap-3">

                    <!-- <button
                        type="button"
                        class="border
                               border-blue-400
                               text-blue-600
                               px-5
                               py-2
                               rounded-lg
                               hover:bg-blue-50">

                        📊 Reports⌄

                    </button>


                    <button
                        type="button"
                        class="border
                               w-10
                               h-10
                               rounded-lg
                               hover:bg-gray-50">

                        ⚙

                    </button> -->

                </div>

            </div>



            <!-- =========================================
                 SUMMARY CARDS
            ========================================== -->

            <div
                class="grid
                       grid-cols-1
                       md:grid-cols-4
                       gap-4
                       mb-5">


                <!-- TOTAL SALES -->

                <button
                    type="button"
                    onclick="changeStatus('all', this)"
                    data-status-button
                    class="sales-status-button
                           text-left
                           border-2
                           border-indigo-500
                           bg-indigo-50
                           rounded-xl
                           p-4">

                    <p
                        class="text-sm
                               text-indigo-600">

                        🧾 Total Sales

                    </p>

                    <p
                        id="totalSales"
                        class="text-2xl
                               font-semibold
                               mt-2">

                        ₹ 0.00

                    </p>

                </button>



                <!-- PAID -->

                <button
                    type="button"
                    onclick="changeStatus('paid', this)"
                    data-status-button
                    class="sales-status-button
                           text-left
                           border
                           rounded-xl
                           p-4
                           hover:bg-green-50">

                    <p
                        class="text-sm
                               text-green-600">

                        ₹ Paid

                    </p>

                    <p
                        id="paidSales"
                        class="text-2xl
                               font-semibold
                               mt-2">

                        ₹ 0.00

                    </p>

                </button>



                <!-- UNPAID -->

                <button
                    type="button"
                    onclick="changeStatus('unpaid', this)"
                    data-status-button
                    class="sales-status-button
                           text-left
                           border
                           rounded-xl
                           p-4
                           hover:bg-red-50">

                    <p
                        class="text-sm
                               text-red-500">

                        ₹ Unpaid

                    </p>

                    <p
                        id="unpaidSales"
                        class="text-2xl
                               font-semibold
                               mt-2">

                        ₹ 0.00

                    </p>

                </button>



                <!-- CANCELLED -->

                <button
                    type="button"
                    onclick="changeStatus('cancelled', this)"
                    data-status-button
                    class="sales-status-button
                           text-left
                           border
                           rounded-xl
                           p-4">

                    <p
                        class="text-sm
                               text-gray-600">

                        ₹ Cancelled

                    </p>

                    <p
                        id="cancelledSales"
                        class="text-2xl
                               font-semibold
                               mt-2">

                        ₹ 0.00

                    </p>

                </button>


            </div>



            <!-- =========================================
                 FILTER BAR
            ========================================== -->

            <div
                class="flex
                       flex-wrap
                       justify-between
                       items-center
                       gap-4
                       mb-4">


                <div
                    class="flex
                           items-center
                           gap-3">


                    <!-- SEARCH -->

                    <div
                        class="relative">

                        <span
                            class="absolute
                                   left-3
                                   top-2.5
                                   text-gray-400">

                            🔍

                        </span>

                        <input
                            id="salesSearch"
                            type="text"
                            placeholder="Search"
                            oninput="renderInvoices()"
                            class="border
                                   rounded-lg
                                   pl-10
                                   pr-4
                                   py-2
                                   w-64
                                   outline-none
                                   focus:border-indigo-500">

                    </div>



                    <!-- DATE RANGE -->

                    <select
                        id="salesRange"
                        onchange="loadSales()"
                        class="border
                               rounded-lg
                               px-4
                               py-2
                               bg-white
                               outline-none">

                        <option value="last_365_days">
                            📅 Last 365 Days
                        </option>

                        <option value="today">
                            Today
                        </option>

                        <option value="yesterday">
                            Yesterday
                        </option>

                        <option value="last_7_days">
                            Last 7 Days
                        </option>

                        <option value="this_week">
                            This Week
                        </option>

                        <option value="last_week">
                            Last Week
                        </option>

                        <option value="this_month">
                            This Month
                        </option>

                        <option value="last_month">
                            Last Month
                        </option>

                        <option value="current_fy">
                            Current Financial Year
                        </option>

                        <option value="previous_fy">
                            Previous Financial Year
                        </option>

                    </select>


                </div>



                <div
                    class="flex
                           items-center
                           gap-3">


                    <!-- <button
                        type="button"
                        class="border
                               px-5
                               py-2
                               rounded-lg
                               text-gray-600">

                        ▱ Bulk Actions⌄

                    </button> -->


                    <a
                        href="{{ route('sales.invoice.create') }}"
                        class="bg-indigo-600
                               hover:bg-indigo-700
                               text-white
                               px-6
                               py-2
                               rounded-lg
                               font-medium">

                        Create Sales Invoice

                    </a>


                </div>


            </div>



            <!-- =========================================
                 TABLE
            ========================================== -->

            <div
                class="border
                       rounded-lg
                       overflow-hidden">


                <div
                    class="overflow-x-auto">


                    <table
                        class="w-full
                               text-sm">


                        <thead
                            class="bg-gray-50
                                   border-b">

                            <tr>

                                <th
                                    class="w-12
                                           px-4
                                           py-3">

                                    <input
                                        type="checkbox">

                                </th>


                                <th
                                    class="text-left
                                           px-4
                                           py-3
                                           font-medium
                                           text-gray-600">

                                    Date ↕️

                                </th>


                                <th
                                    class="text-left
                                           px-4
                                           py-3
                                           font-medium
                                           text-gray-600">

                                    Invoice Number

                                </th>


                                <th
                                    class="text-left
                                           px-4
                                           py-3
                                           font-medium
                                           text-gray-600">

                                    Party Name

                                </th>


                                <th
                                    class="text-left
                                           px-4
                                           py-3
                                           font-medium
                                           text-gray-600">

                                    Due In

                                </th>


                                <th
                                    class="text-left
                                           px-4
                                           py-3
                                           font-medium
                                           text-gray-600">

                                    Amount ↕️

                                </th>


                                <th
                                    class="text-left
                                           px-4
                                           py-3
                                           font-medium
                                           text-gray-600">

                                    Status

                                </th>


                            </tr>

                        </thead>



                        <tbody
                            id="salesTableBody">

                        </tbody>


                    </table>


                </div>



                <!-- EMPTY STATE -->

                <div
                    id="salesEmptyState"
                    class="hidden
                           min-h-[470px]
                           flex
                           flex-col
                           items-center
                           justify-center
                           text-gray-500">

                    <div
                        class="text-6xl
                               mb-5">

                        🧾

                    </div>

                    <p>
                        No Transactions Matching the current filter
                    </p>

                </div>


            </div>


        </div>


    </main>


</div>



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

        if (response.status === 401) {
            localStorage.removeItem('token');
            window.location.href = '/start-free-billing';
            return;
        }

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


// Business Name
document.getElementById('sidebarBusinessName').innerText =
    businessName;


// Mobile Number
document.getElementById('sidebarBusinessMobile').innerText =
    business.mobile || '';


// Business Logo / Initial
document.getElementById('sidebarBusinessInitial').innerText =
    businessName.trim().charAt(0).toUpperCase();

    } catch (error) {

        console.error(
            'Business Info Error:',
            error
        );

    }
}    


/* =====================================================
   DATA
===================================================== */

let allSalesInvoices = [];

let selectedStatus = 'all';



/* =====================================================
   LOAD SALES
===================================================== */

async function loadSales() {

    const token =
        localStorage.getItem('token');

    const range =
        document
            .getElementById('salesRange')
            .value;


    try {

        const response =
            await fetch(
                `/api/sales-summary?range=${encodeURIComponent(range)}&status=all`,
                {
                    headers: {

                        'Accept':
                            'application/json',

                        'Authorization':
                            'Bearer ' + token

                    }
                }
            );


        if (response.status === 401) {

            localStorage.removeItem('token');

            window.location.href =
                '/start-free-billing';

            return;

        }


        const result =
            await response.json();


        if (!response.ok || !result.success) {

            throw new Error(
                result.message ||
                'Unable to load sales invoices.'
            );

        }


        allSalesInvoices =
            result.data?.invoices || [];


        updateSummaryCards();

        renderInvoices();


    } catch (error) {

        console.error(
            'Sales Invoice Error:',
            error
        );

        allSalesInvoices = [];

        updateSummaryCards();

        renderInvoices();

    }

}



/* =====================================================
   SUMMARY CARDS
===================================================== */

function updateSummaryCards() {

    let total = 0;

    let paid = 0;

    let unpaid = 0;

    let cancelled = 0;


    allSalesInvoices.forEach(invoice => {

        const amount =
            Number(invoice.amount || 0);

        const status =
            String(invoice.status || '')
                .toLowerCase();


        if (status !== 'cancelled') {

            total += amount;

        }


        if (status === 'paid') {

            paid += amount;

        }


        if (
            status === 'unpaid' ||
            status === 'partial'
        ) {

            unpaid +=
                Number(
                    invoice.balance || 0
                );

        }


        if (status === 'cancelled') {

            cancelled += amount;

        }

    });


    document
        .getElementById('totalSales')
        .innerText =
        '₹ ' + formatMoney(total);


    document
        .getElementById('paidSales')
        .innerText =
        '₹ ' + formatMoney(paid);


    document
        .getElementById('unpaidSales')
        .innerText =
        '₹ ' + formatMoney(unpaid);


    document
        .getElementById('cancelledSales')
        .innerText =
        '₹ ' + formatMoney(cancelled);

}



/* =====================================================
   STATUS FILTER
===================================================== */

function changeStatus(status, button) {

    selectedStatus = status;


    document
        .querySelectorAll(
            '[data-status-button]'
        )
        .forEach(element => {

            element.classList.remove(
                'border-2',
                'border-indigo-500',
                'bg-indigo-50'
            );

            element.classList.add(
                'border'
            );

        });


    button.classList.remove(
        'border'
    );

    button.classList.add(
        'border-2',
        'border-indigo-500',
        'bg-indigo-50'
    );


    renderInvoices();

}



/* =====================================================
   RENDER INVOICES
===================================================== */

function renderInvoices() {

    const search =
        document
            .getElementById('salesSearch')
            .value
            .trim()
            .toLowerCase();


    let invoices =
        allSalesInvoices.filter(invoice => {


            const status =
                String(
                    invoice.status || ''
                )
                .toLowerCase();


            let statusMatches = true;


            if (selectedStatus === 'paid') {

                statusMatches =
                    status === 'paid';

            }


            if (selectedStatus === 'unpaid') {

                statusMatches =
                    status === 'unpaid' ||
                    status === 'partial';

            }


            if (selectedStatus === 'cancelled') {

                statusMatches =
                    status === 'cancelled';

            }


            const searchable =
                [
                    invoice.invoice_number,
                    invoice.party_name,
                    invoice.invoice_date,
                    invoice.amount,
                    invoice.status
                ]
                .join(' ')
                .toLowerCase();


            const searchMatches =
                !search ||
                searchable.includes(search);


            return (
                statusMatches &&
                searchMatches
            );

        });


    const tbody =
        document
            .getElementById(
                'salesTableBody'
            );


    const emptyState =
        document
            .getElementById(
                'salesEmptyState'
            );


    if (invoices.length === 0) {

        tbody.innerHTML = '';

        emptyState
            .classList
            .remove('hidden');

        return;

    }


    emptyState
        .classList
        .add('hidden');


    tbody.innerHTML =
        invoices.map(invoice => {

            return `

                <tr
                    class="border-b
                           hover:bg-gray-50
                           cursor-pointer"
                    onclick="openInvoice(
                        ${Number(invoice.invoice_id)}
                    )">


                    <td
                        class="px-4
                               py-4"
                        onclick="event.stopPropagation()">

                        <input
                            type="checkbox">

                    </td>


                    <td
                        class="px-4
                               py-4">

                        ${escapeHtml(
                            formatDate(
                                invoice.invoice_date
                            )
                        )}

                    </td>


                    <td
                        class="px-4
                               py-4
                               font-medium
                               text-indigo-600">

                        ${escapeHtml(
                            invoice.invoice_number || '-'
                        )}

                    </td>


                    <td
                        class="px-4
                               py-4">

                        ${escapeHtml(
                            invoice.party_name || '-'
                        )}

                    </td>


                    <td
                        class="px-4
                               py-4">

                        ${escapeHtml(
                            getDueText(
                                invoice.due_date
                            )
                        )}

                    </td>


                    <td
                        class="px-4
                               py-4
                               font-medium">

                        ₹ ${formatMoney(
                            invoice.amount
                        )}

                    </td>


                    <td
                        class="px-4
                               py-4">

                        ${statusBadge(
                            invoice.status
                        )}

                    </td>


                </tr>

            `;

        })
        .join('');

}



/* =====================================================
   OPEN INVOICE
===================================================== */

function openInvoice(id) {

    window.location.href =
        `/invoice-preview/${id}`;

}



/* =====================================================
   STATUS BADGE
===================================================== */

function statusBadge(status) {

    const value =
        String(status || 'unpaid')
            .toLowerCase();


    if (value === 'paid') {

        return `
            <span
                class="inline-flex
                       px-3
                       py-1
                       rounded-full
                       bg-green-100
                       text-green-700
                       text-xs
                       font-medium">

                Paid

            </span>
        `;

    }


    if (value === 'partial') {

        return `
            <span
                class="inline-flex
                       px-3
                       py-1
                       rounded-full
                       bg-yellow-100
                       text-yellow-700
                       text-xs
                       font-medium">

                Partial

            </span>
        `;

    }


    if (value === 'cancelled') {

        return `
            <span
                class="inline-flex
                       px-3
                       py-1
                       rounded-full
                       bg-gray-100
                       text-gray-600
                       text-xs
                       font-medium">

                Cancelled

            </span>
        `;

    }


    return `
        <span
            class="inline-flex
                   px-3
                   py-1
                   rounded-full
                   bg-red-100
                   text-red-600
                   text-xs
                   font-medium">

            Unpaid

        </span>
    `;

}



/* =====================================================
   DUE DATE
===================================================== */

function getDueText(dueDate) {

    if (!dueDate) {
        return '-';
    }


    const due =
        new Date(
            String(dueDate)
                .substring(0, 10) +
            'T00:00:00'
        );


    const today =
        new Date();


    today.setHours(
        0,
        0,
        0,
        0
    );


    const difference =
        Math.round(
            (
                due.getTime() -
                today.getTime()
            ) /
            86400000
        );


    if (difference === 0) {

        return 'Today';

    }


    if (difference > 0) {

        return `In ${difference} day${difference === 1 ? '' : 's'}`;

    }


    const overdue =
        Math.abs(difference);


    return `${overdue} day${overdue === 1 ? '' : 's'} overdue`;

}



/* =====================================================
   DATE
===================================================== */

function formatDate(value) {

    if (!value) {
        return '-';
    }


    const date =
        new Date(
            String(value)
                .substring(0, 10) +
            'T00:00:00'
        );


    return date.toLocaleDateString(
        'en-IN',
        {
            day: '2-digit',
            month: 'short',
            year: 'numeric'
        }
    );

}



/* =====================================================
   MONEY
===================================================== */

function formatMoney(value) {

    return Number(
        value || 0
    )
    .toLocaleString(
        'en-IN',
        {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        }
    );

}



/* =====================================================
   ESCAPE HTML
===================================================== */

function escapeHtml(value) {

    return String(value ?? '')
        .replace(
            /[&<>"']/g,
            function(character) {

                return {

                    '&': '&amp;',
                    '<': '&lt;',
                    '>': '&gt;',
                    '"': '&quot;',
                    "'": '&#039;'

                }[character];

            }
        );

}



/* =====================================================
   START
===================================================== */

loadBusinessInfo();

loadSales();

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