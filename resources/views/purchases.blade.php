<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">

    <title>Purchase Invoices | BillNika</title>

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100">

<!-- JWT PROTECTION -->
<script>
    if (!localStorage.getItem('token')) {
        window.location.href = '/start-free-billing';
    }
</script>

<div class="flex min-h-screen">

    <!-- ================= SIDEBAR ================= -->
    <aside class="w-64 bg-gradient-to-b from-slate-900 to-slate-800 text-white flex flex-col">

        <!-- USER INFO -->
        <div class="p-5 border-b border-white/10">

            <div class="flex items-center gap-3">

                <div
    id="businessLogo"
    class="w-10 h-10 rounded-full bg-yellow-400 flex items-center justify-center font-bold text-black">
    B
</div>

<div>
    <p id="businessName" class="font-semibold">
        Loading...
    </p>

    <p id="businessNumber" class="text-xs text-gray-300">
        Loading...
    </p>
</div>
            </div>

        </div>


        <!-- CREATE SALES INVOICE -->
        <div class="p-4">

            <a
                href="{{ route('sales.invoice.create') }}"
                class="block w-full bg-indigo-500 hover:bg-indigo-600 text-white py-3 rounded-lg font-medium text-center transition">

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


    <!-- ================= MAIN CONTENT ================= -->

    <main class="flex-1 p-6">

        <!-- HEADER -->
        <div class="flex justify-between items-center mb-6">

            <h1 class="text-2xl font-semibold text-gray-800">
                Purchase Invoices
            </h1>

            <div class="flex gap-3">

                <!-- <button
                    class="border border-blue-300 text-blue-600 px-5 py-2 rounded-lg hover:bg-blue-50">
                    📄 Reports
                </button> -->

                <button
                    onclick="window.location.href='/purchases/create'"
                    class="bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2 rounded-lg font-medium">
                    Create Purchase Invoice
                </button>

            </div>

        </div>


        <!-- ================= SUMMARY CARDS ================= -->

       <div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-6">

    <!-- TOTAL PURCHASES -->

    <button
        type="button"
        onclick="changePurchaseStatus('all', this)"
        class="purchase-status-card w-full text-left bg-white border-2 border-indigo-500 rounded-xl p-5 cursor-pointer hover:bg-indigo-50 transition">

        <p class="text-sm text-indigo-600 mb-2">
            🛍 Total Purchases
        </p>

        <p
            id="totalPurchases"
            class="text-2xl font-semibold text-gray-700">
            ₹ 0
        </p>

    </button>


    <!-- PAID -->

    <button
        type="button"
        onclick="changePurchaseStatus('paid', this)"
        class="purchase-status-card w-full text-left bg-white border rounded-xl p-5 cursor-pointer hover:bg-green-50 transition">

        <p class="text-sm text-green-600 mb-2">
            💵 Paid
        </p>

        <p
            id="paidAmount"
            class="text-2xl font-semibold text-gray-700">
            ₹ 0
        </p>

    </button>


    <!-- UNPAID -->

    <button
        type="button"
        onclick="changePurchaseStatus('unpaid', this)"
        class="purchase-status-card w-full text-left bg-white border rounded-xl p-5 cursor-pointer hover:bg-red-50 transition">

        <p class="text-sm text-red-600 mb-2">
            🧾 Unpaid
        </p>

        <p
            id="unpaidAmount"
            class="text-2xl font-semibold text-gray-700">
            ₹ 0
        </p>

    </button>

</div>


        <!-- ================= FILTER BAR ================= -->

        <div
            class="bg-white rounded-xl border p-4 mb-4 flex flex-wrap gap-3 items-center">

            <!-- SEARCH -->

            <div class="relative">

                <span
                    class="absolute left-3 top-2.5 text-gray-400">
                    🔍
                </span>

                <input
                    id="searchInput"
                    type="text"
                    placeholder="Search purchase..."
                    class="border rounded-lg pl-9 pr-4 py-2 w-64 focus:outline-none focus:ring-2 focus:ring-indigo-300">

            </div>


            <!-- DATE -->

            <select
                id="dateFilter"
                class="border rounded-lg px-4 py-2 text-gray-600">

                <option value="365">
                    Last 365 Days
                </option>

                <option value="30">
                    Last 30 Days
                </option>

                <option value="7">
                    Last 7 Days
                </option>

                <option value="all">
                    All Time
                </option>

            </select>

        </div>


        <!-- ================= TABLE ================= -->

        <div
            class="bg-white rounded-xl border overflow-hidden">

            <div class="overflow-x-auto">

                <table
                    class="w-full text-sm">

                    <thead
                        class="bg-gray-100 border-b">

                        <tr>

                            <th
                                class="text-left px-4 py-3">
                                Date
                            </th>

                            <th
                                class="text-left px-4 py-3">
                                Purchase Invoice Number
                            </th>

                            <th
                                class="text-left px-4 py-3">
                                Party Name
                            </th>

                            <th
                                class="text-left px-4 py-3">
                                Due In
                            </th>

                            <th
                                class="text-left px-4 py-3">
                                Amount
                            </th>

                            <th
                                class="text-left px-4 py-3">
                                Status
                            </th>

                        </tr>

                    </thead>


                    <tbody
                        id="purchaseTableBody">

                        <!-- DATA LOADED FROM API -->

                    </tbody>

                </table>

            </div>


            <!-- EMPTY STATE -->

            <div
                id="emptyState"
                class="hidden py-40 text-center">

                <div class="text-6xl mb-5">
                    🛒
                </div>

                <p class="text-gray-500">
                    No Transactions Matching the current filter
                </p>

            </div>

        </div>

    </main>

</div>


<script>

/* ===============================
   LOAD BUSINESS DETAILS
================================ */

async function loadBusinessDetails() {

    try {

        const token = localStorage.getItem('token');

        if (!token) {
            window.location.href = '/start-free-billing';
            return;
        }

        const response = await fetch('/api/business-info', {
            method: 'GET',
            headers: {
                'Authorization': 'Bearer ' + token,
                'Accept': 'application/json'
            }
        });

        if (response.status === 401) {

            localStorage.removeItem('token');

            window.location.href = '/start-free-billing';

            return;
        }

        const result = await response.json();

        console.log('Business Info:', result);

        if (!result.success) {

            console.error(
                'Business information error:',
                result.message
            );

            return;
        }

        const business = result.data || {};

        const businessName =
            business.business_name || 'Business Name';

        const businessNumber =
            business.mobile || '';

        // Business Name
        document.getElementById('businessName').textContent =
            businessName;

        // Mobile Number
        document.getElementById('businessNumber').textContent =
            businessNumber;

        // First letter of Business Name
        document.getElementById('businessLogo').textContent =
            businessName.charAt(0).toUpperCase();

    } catch (error) {

        console.error(
            'Business details loading error:',
            error
        );
    }
}    

let allPurchases = [];
let selectedStatus = 'all';


/* ===============================
   FORMAT MONEY
================================ */

function money(value) {

    return '₹ ' + Number(value || 0).toLocaleString(
        'en-IN',
        {
            minimumFractionDigits: 0,
            maximumFractionDigits: 2
        }
    );
}


/* ===============================
   FORMAT DATE
================================ */

function formatDate(date) {

    if (!date) return '-';

    const d = new Date(date);

    return d.toLocaleDateString(
        'en-GB',
        {
            day: '2-digit',
            month: 'short',
            year: 'numeric'
        }
    );
}


/* ===============================
   LOAD PURCHASES
================================ */

async function loadPurchases() {

    try {

        const token = localStorage.getItem('token');

        const response = await fetch('/api/purchases', {

            headers: {
                'Authorization': 'Bearer ' + token,
                'Accept': 'application/json'
            }

        });


        if (response.status === 401) {

            localStorage.removeItem('token');

            window.location.href =
                '/start-free-billing';

            return;
        }


        const result = await response.json();


        if (!result.success) {

            throw new Error(
                result.message || 'Unable to load purchases'
            );

        }


        allPurchases =
            result.data.purchases || [];


        /* SUMMARY */

        document.getElementById('totalPurchases')
            .textContent =
            money(result.data.total_purchases);


        document.getElementById('paidAmount')
            .textContent =
            money(result.data.paid);


        document.getElementById('unpaidAmount')
            .textContent =
            money(result.data.unpaid);


        renderPurchases();

    }

    catch (error) {

        console.error(
            'Purchase loading error:',
            error
        );

    }

}

/* ===============================
   STATUS FILTER
================================ */

function changePurchaseStatus(status, button) {

    selectedStatus = status;

    // Remove active style from all cards
    document
        .querySelectorAll('.purchase-status-card')
        .forEach(card => {

            card.classList.remove(
                'border-2',
                'border-indigo-500',
                'bg-indigo-50'
            );

            card.classList.add('border');

        });


    // Add active style to clicked card
    button.classList.remove('border');

    button.classList.add(
        'border-2',
        'border-indigo-500',
        'bg-indigo-50'
    );


    // Re-render table
    renderPurchases();
}


/* ===============================
   RENDER
================================ */

function renderPurchases() {

    const search =
        document.getElementById('searchInput')
            .value
            .toLowerCase()
            .trim();


    const days =
        document.getElementById('dateFilter').value;


    let purchases =
        [...allPurchases];

            /* STATUS FILTER */

    if (selectedStatus === 'paid') {

        purchases = purchases.filter(p =>
            String(p.status || '').toLowerCase() === 'paid'
        );

    }

    else if (selectedStatus === 'unpaid') {

        purchases = purchases.filter(p => {

            const status =
                String(p.status || '').toLowerCase();

            return (
                status === 'unpaid' ||
                status === 'partial'
            );

        });

    }


    /* SEARCH */

    if (search) {

        purchases =
            purchases.filter(p =>

                String(p.purchase_number || '')
                    .toLowerCase()
                    .includes(search)

                ||

                String(p.party_name || '')
                    .toLowerCase()
                    .includes(search)

            );

    }


    /* DATE FILTER */

    if (days !== 'all') {

        const cutoff =
            new Date();

        cutoff.setDate(
            cutoff.getDate() - Number(days)
        );


        purchases =
            purchases.filter(p => {

                if (!p.purchase_date)
                    return false;

                return new Date(p.purchase_date)
                    >= cutoff;

            });

    }


    const tbody =
        document.getElementById(
            'purchaseTableBody'
        );


    const empty =
        document.getElementById(
            'emptyState'
        );


    tbody.innerHTML = '';


    if (purchases.length === 0) {

        empty.classList.remove('hidden');

        return;

    }


    empty.classList.add('hidden');


    purchases.forEach(purchase => {

        let statusClass =
            'bg-gray-100 text-gray-600';


        if (purchase.status === 'paid') {

            statusClass =
                'bg-green-100 text-green-700';

        }
        else if (
            purchase.status === 'partial'
        ) {

            statusClass =
                'bg-yellow-100 text-yellow-700';

        }
        else if (
            purchase.status === 'unpaid'
        ) {

            statusClass =
                'bg-red-100 text-red-700';

        }


        const row =
            document.createElement('tr');


        row.className =
            'border-b hover:bg-gray-50 cursor-pointer';


        row.onclick = function () {

            window.location.href =
                '/purchase-preview/' + purchase.id;

        };


        row.innerHTML = `

            <td class="px-4 py-3">
                ${formatDate(purchase.purchase_date)}
            </td>

            <td class="px-4 py-3 font-medium text-gray-700">
                ${purchase.purchase_number || '-'}
            </td>

            <td class="px-4 py-3">
                ${purchase.party_name || 'Cash Purchase'}
            </td>

            <td class="px-4 py-3">

                ${
                    purchase.due_date
                        ? formatDate(purchase.due_date)
                        : '-'
                }

            </td>

            <td class="px-4 py-3 font-medium">
                ${money(purchase.amount)}
            </td>

            <td class="px-4 py-3">

                <span
                    class="px-3 py-1 rounded-full text-xs font-medium ${statusClass}">

                    ${
                        purchase.status
                            ? purchase.status
                                .charAt(0)
                                .toUpperCase()
                                +
                                purchase.status
                                    .slice(1)
                            : '-'
                    }

                </span>

            </td>

        `;


        tbody.appendChild(row);

    });

}


/* ===============================
   EVENTS
================================ */

document
    .getElementById('searchInput')
    .addEventListener(
        'input',
        renderPurchases
    );


document
    .getElementById('dateFilter')
    .addEventListener(
        'change',
        renderPurchases
    );


/* ===============================
   START
================================ */
loadBusinessDetails();
loadPurchases();

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