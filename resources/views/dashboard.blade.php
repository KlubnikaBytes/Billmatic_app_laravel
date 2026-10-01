<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard | BillNika</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100">

<!-- 🔐 JWT PROTECTION (REQUIRED) -->
<script>
    if (!localStorage.getItem('token')) {
        window.location.href = '/start-free-billing';
    }
</script>

<div class="flex min-h-screen">

   <!-- ================= SIDEBAR ================= -->
<aside class="w-64 bg-gradient-to-b from-slate-900 to-slate-800 text-white flex flex-col">

    <!-- BUSINESS -->
    <div class="p-5 border-b border-white/10">

        <div class="flex items-center gap-3">

            <div id="businessInitial"
     class="w-10 h-10 rounded-full bg-yellow-400 flex items-center justify-center font-bold text-black">
    B
</div>

            <div>
                <p id="businessName" class="font-semibold">Loading...</p>
<p id="businessMobile" class="text-xs text-gray-300">Loading...</p>
            </div>

        </div>

    </div>


    <!-- CREATE SALES INVOICE -->
    <div class="p-3">

        <a
            href="{{ route('sales.invoice.create') }}"
            class="block w-full bg-indigo-500 hover:bg-indigo-600
                   text-white py-3 rounded-lg font-medium
                   text-center transition">

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
    <!-- <div class="p-4 text-xs text-gray-400 border-t border-white/10">

        🔒 100% Secure
        <br>
        ISO Certified

    </div> -->
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

        <!-- TOP BAR -->
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-semibold">Dashboard</h1>

            <button class="bg-blue-100 text-blue-700 px-4 py-2 rounded-lg font-medium">
                Book Demo
            </button>
        </div>

        <!-- BUSINESS OVERVIEW -->
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-lg font-semibold">Business Overview</h2>
            <!-- <span class="text-sm text-gray-500">
                Last Update: 06 Feb 2026 | 12:33 PM
            </span> -->
            <span id="lastUpdate" class="text-sm text-gray-500">
    Last Update: --
</span>
        </div>

        <!-- STATS -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">

            <div class="bg-green-50 p-5 rounded-xl">
                <p class="text-sm text-green-700">↓ To Collect</p>
                <!-- <p class="text-2xl font-bold">₹ 6,303</p> -->
                <p id="toCollect" class="text-2xl font-bold">₹ --</p>
            </div>

            <div class="bg-red-50 p-5 rounded-xl">
                <p class="text-sm text-red-700">↑ To Pay</p>
                <!-- <p class="text-2xl font-bold">₹ 980</p> -->
                <p id="toPay" class="text-2xl font-bold">₹ --</p>
            </div>

            <div class="bg-blue-50 p-5 rounded-xl">
                <p class="text-sm text-blue-700">🏦 Cash + Bank</p>
                <!-- <p class="text-2xl font-bold">₹ 4,441.25</p> -->
                <p id="cashBank" class="text-2xl font-bold">₹ --</p>
            </div>

        </div>

        <!-- TABLE + CHECKLIST -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            <!-- TRANSACTIONS -->
            <!-- <div class="lg:col-span-2 bg-white rounded-xl p-5">
                <h3 class="font-semibold mb-4">Latest Transactions</h3>

                <table class="w-full text-sm border">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="p-2 border">Date</th>
                            <th class="p-2 border">Type</th>
                            <th class="p-2 border">Txn No</th>
                            <th class="p-2 border">Party</th>
                            <th class="p-2 border">Amount</th>
                        </tr>
                    </thead> -->
                    <!-- <tbody>
                        <tr>
                            <td class="p-2 border">04 Feb 2026</td>
                            <td class="p-2 border">Sales Invoice</td>
                            <td class="p-2 border">14</td>
                            <td class="p-2 border">Anurag</td>
                            <td class="p-2 border">₹ 450</td>
                        </tr>
                        <tr>
                            <td class="p-2 border">04 Feb 2026</td>
                            <td class="p-2 border">Sales Invoice</td>
                            <td class="p-2 border">13</td>
                            <td class="p-2 border">Rajesh</td>
                            <td class="p-2 border">₹ 300</td>
                        </tr>
                    </tbody> -->
                    <!-- <tbody id="transactionsBody">
    <tr>
        <td colspan="5" class="p-4 text-center text-gray-500">
            Loading transactions...
        </td>
    </tr>
</tbody>
                </table>
            </div> -->

            <!-- TRANSACTIONS -->
<div class="lg:col-span-2 bg-white rounded-xl p-5">

    <div class="flex justify-between items-center mb-4">
        <h3 class="font-semibold">Latest Transactions</h3>

        <span id="transactionPageInfo"
              class="text-sm text-gray-500">
            Page 1
        </span>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm border">
            <thead class="bg-gray-100">
                <tr>
                    <th class="p-2 border">Date</th>
                    <th class="p-2 border">Type</th>
                    <th class="p-2 border">Txn No</th>
                    <th class="p-2 border">Party</th>
                    <th class="p-2 border">Amount</th>
                </tr>
            </thead>

            <tbody id="transactionsBody">
                <tr>
                    <td colspan="5"
                        class="p-4 text-center text-gray-500">
                        Loading transactions...
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- PAGINATION -->
    <div class="flex justify-between items-center mt-4">

        <button
            id="previousTransactionBtn"
            onclick="loadTransactions(currentTransactionPage - 1)"
            class="px-4 py-2 border rounded-lg text-sm
                   hover:bg-gray-100 disabled:opacity-40 disabled:cursor-not-allowed">
            ← Previous
        </button>

        <span
            id="transactionPaginationText"
            class="text-sm text-gray-600">
            Page 1 of 1
        </span>

        <button
            id="nextTransactionBtn"
            onclick="loadTransactions(currentTransactionPage + 1)"
            class="px-4 py-2 border rounded-lg text-sm
                   hover:bg-gray-100 disabled:opacity-40 disabled:cursor-not-allowed">
            Next →
        </button>

    </div>

</div>

            <!-- CHECKLIST -->
            <div class="bg-white rounded-xl p-5">
                <h3 class="font-semibold mb-4">Today's Checklist</h3>
                <ul class="space-y-3 text-sm text-gray-600">
                    <li>✅ Create first invoice</li>
                    <li>⬜ Add items</li>
                    <li>⬜ Add parties</li>
                    <li>⬜ Check reports</li>
                </ul>
            </div>

        </div>

    </main>
</div>

<script>

/* ==========================================
   LOAD BUSINESS DETAILS
========================================== */

async function loadBusinessDetails() {

    const token = localStorage.getItem('token');

    if (!token) {
        window.location.href = '/start-free-billing';
        return;
    }

    try {

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
                'Business info error:',
                result.message
            );
            return;
        }

        const business = result.data || {};

        const businessName =
            business.business_name || 'Business Name';

        const businessMobile =
            business.mobile || '';

        // Business Name
        document.getElementById('businessName').textContent =
            businessName;

        // Business Mobile
        document.getElementById('businessMobile').textContent =
            businessMobile;

        // Business Initial
        document.getElementById('businessInitial').textContent =
            businessName
                .trim()
                .charAt(0)
                .toUpperCase();

    } catch (error) {

        console.error(
            'Business details loading error:',
            error
        );
    }
}    

async function loadDashboard() {

    const token = localStorage.getItem('token');

    if (!token) {
        window.location.href = '/start-free-billing';
        return;
    }

    try {

        /* =====================================================
           LOAD DASHBOARD TOTALS
        ===================================================== */

        const totalsResponse = await fetch(
            '/api/dashboard/totals',
            {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'Authorization': 'Bearer ' + token
                }
            }
        );

        const totalsResult = await totalsResponse.json();

        console.log('Dashboard Totals:', totalsResult);


        if (!totalsResponse.ok) {
            throw new Error(
                totalsResult.message ||
                'Unable to load dashboard totals.'
            );
        }


        /*
         * Support:
         * {
         *   success: true,
         *   data: {...}
         * }
         *
         * or directly:
         * {
         *   to_collect: ...,
         *   to_pay: ...
         * }
         */

        const totals =
            totalsResult.data || totalsResult;


        /* =========================
           BUSINESS DETAILS
        ========================= */


        /* =========================
           DASHBOARD AMOUNTS
        ========================= */

        const toCollect =
            Number(
                totals.to_collect ??
                totals.toCollect ??
                totals.total_to_collect ??
                0
            );

        const toPay =
            Number(
                totals.to_pay ??
                totals.toPay ??
                totals.total_to_pay ??
                0
            );

        const cashBank =
            Number(
                totals.cash_bank ??
                totals.cashBank ??
                totals.cash_bank_balance ??
                totals.total_balance ??
                0
            );


        document.getElementById('toCollect').innerText =
            '₹ ' + toCollect.toFixed(2);

        document.getElementById('toPay').innerText =
            '₹ ' + toPay.toFixed(2);

        document.getElementById('cashBank').innerText =
            '₹ ' + cashBank.toFixed(2);


        /* =========================
           LAST UPDATE
        ========================= */

        document.getElementById('lastUpdate').innerText =
            'Last Update: ' +
            new Date().toLocaleDateString('en-IN', {
                day: '2-digit',
                month: 'short',
                year: 'numeric'
            }) +
            ' | ' +
            new Date().toLocaleTimeString('en-IN', {
                hour: '2-digit',
                minute: '2-digit'
            });


        /* =====================================================
           LOAD LATEST TRANSACTIONS
        ===================================================== */

        const transactionResponse = await fetch(
            '/api/dashboard/transactions',
            {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'Authorization': 'Bearer ' + token
                }
            }
        );

        const transactionResult =
            await transactionResponse.json();

        console.log(
            'Dashboard Transactions:',
            transactionResult
        );


        if (!transactionResponse.ok) {
            throw new Error(
                transactionResult.message ||
                'Unable to load transactions.'
            );
        }


        const transactions =
            transactionResult.data ||
            transactionResult.transactions ||
            [];


        renderTransactions(transactions);


    } catch (error) {

        console.error(
            'Dashboard Error:',
            error
        );

        document.getElementById('toCollect').innerText =
            '₹ 0.00';

        document.getElementById('toPay').innerText =
            '₹ 0.00';

        document.getElementById('cashBank').innerText =
            '₹ 0.00';

        document.getElementById('transactionsBody').innerHTML = `
            <tr>
                <td colspan="5"
                    class="p-4 text-center text-red-500">
                    Unable to load dashboard data.
                </td>
            </tr>
        `;
    }
}


/* =========================================================
   RENDER TRANSACTIONS
========================================================= */

function renderTransactions(transactions) {

    const tbody =
        document.getElementById('transactionsBody');


    if (!transactions || transactions.length === 0) {

        tbody.innerHTML = `
            <tr>
                <td colspan="5"
                    class="p-4 text-center text-gray-500">
                    No transactions yet.
                </td>
            </tr>
        `;

        return;
    }


    tbody.innerHTML =
        transactions
            .slice(0, 10)
            .map(transaction => {

                const date =
                    transaction.date ||
                    transaction.invoice_date ||
                    transaction.created_at ||
                    '';

                const type =
                    transaction.type ||
                    transaction.transaction_type ||
                    'Transaction';

                const txnNo =
                    transaction.txn_no ||
                    transaction.invoice_number ||
                    transaction.number ||
                    transaction.id ||
                    '-';

                const party =
                    transaction.party_name ||
                    transaction.party?.party_name ||
                    transaction.party?.name ||
                    transaction.name ||
                    '-';

                const amount =
                    Number(
                        transaction.amount ||
                        transaction.grand_total ||
                        transaction.total ||
                        0
                    );


                return `
                    <tr>

                        <td class="p-2 border">
                            ${formatDate(date)}
                        </td>

                        <td class="p-2 border">
                            ${escapeHtml(type)}
                        </td>

                        <td class="p-2 border">
                            ${escapeHtml(txnNo)}
                        </td>

                        <td class="p-2 border">
                            ${escapeHtml(party)}
                        </td>

                        <td class="p-2 border">
                            ₹ ${amount.toFixed(2)}
                        </td>

                    </tr>
                `;

            })
            .join('');
}


/* =========================================================
   DATE FORMAT
========================================================= */

function formatDate(value) {

    if (!value) {
        return '-';
    }

    const date = new Date(value);

    if (isNaN(date.getTime())) {
        return value;
    }

    return date.toLocaleDateString('en-IN', {
        day: '2-digit',
        month: 'short',
        year: 'numeric'
    });
}


/* =========================================================
   ESCAPE HTML
========================================================= */

function escapeHtml(value) {

    const div =
        document.createElement('div');

    div.textContent =
        value ?? '';

    return div.innerHTML;
}


/* =========================================================
   LOAD DASHBOARD
========================================================= */
loadBusinessDetails();
loadDashboard();

</script>

<script>

let currentTransactionPage = 1;
let lastTransactionPage = 1;


// ============================================
// LOAD DASHBOARD TRANSACTIONS
// ============================================
async function loadTransactions(page = 1) {

    const token = localStorage.getItem('token');

    if (!token) {
        window.location.href = '/start-free-billing';
        return;
    }

    const tbody = document.getElementById('transactionsBody');

    tbody.innerHTML = `
        <tr>
            <td colspan="5"
                class="p-4 text-center text-gray-500">
                Loading transactions...
            </td>
        </tr>
    `;

    try {

        const response = await fetch(
            `/api/dashboard/transactions?page=${page}`,
            {
                method: 'GET',
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json'
                }
            }
        );

        if (!response.ok) {
            throw new Error('Failed to load transactions');
        }

        const result = await response.json();

        const transactions = result.data || [];

        currentTransactionPage = result.current_page || 1;
        lastTransactionPage = result.last_page || 1;


        // ============================================
        // NO TRANSACTIONS
        // ============================================
        if (transactions.length === 0) {

            tbody.innerHTML = `
                <tr>
                    <td colspan="5"
                        class="p-4 text-center text-gray-500">
                        No transactions found
                    </td>
                </tr>
            `;

        } else {

            // ============================================
            // CREATE TABLE ROWS
            // ============================================
            tbody.innerHTML = transactions.map(txn => {

                const date = txn.date
                    ? new Date(txn.date).toLocaleDateString(
                        'en-IN',
                        {
                            day: '2-digit',
                            month: 'short',
                            year: 'numeric'
                        }
                    )
                    : '-';


                const type = txn.type
                    ? txn.type.charAt(0).toUpperCase() +
                      txn.type.slice(1)
                    : '-';


                const number = txn.number || '-';

                const party = txn.party_name || '-';


                // Invoice / Purchase amount
                let amount = 0;

                if (txn.grand_total !== undefined) {
                    amount = Number(txn.grand_total);
                } else if (txn.amount !== undefined) {
                    amount = Number(txn.amount);
                }


                return `
                    <tr class="hover:bg-gray-50">

                        <td class="p-2 border">
                            ${date}
                        </td>

                        <td class="p-2 border">
                            ${type}
                        </td>

                        <td class="p-2 border">
                            ${number}
                        </td>

                        <td class="p-2 border">
                            ${party}
                        </td>

                        <td class="p-2 border">
                            ₹ ${amount.toFixed(2)}
                        </td>

                    </tr>
                `;

            }).join('');
        }


        // ============================================
        // PAGINATION INFO
        // ============================================
        document.getElementById(
            'transactionPageInfo'
        ).textContent =
            `Page ${currentTransactionPage} of ${lastTransactionPage}`;


        document.getElementById(
            'transactionPaginationText'
        ).textContent =
            `Page ${currentTransactionPage} of ${lastTransactionPage}`;


        // ============================================
        // ENABLE / DISABLE BUTTONS
        // ============================================
        document.getElementById(
            'previousTransactionBtn'
        ).disabled = currentTransactionPage <= 1;


        document.getElementById(
            'nextTransactionBtn'
        ).disabled =
            currentTransactionPage >= lastTransactionPage;


    } catch (error) {

        console.error('Transactions error:', error);

        tbody.innerHTML = `
            <tr>
                <td colspan="5"
                    class="p-4 text-center text-red-500">
                    Failed to load transactions
                </td>
            </tr>
        `;
    }
}


// ============================================
// LOAD FIRST 10 TRANSACTIONS
// ============================================
document.addEventListener(
    'DOMContentLoaded',
    () => {
        loadTransactions(1);
    }
);


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
