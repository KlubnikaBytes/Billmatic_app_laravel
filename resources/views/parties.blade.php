<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Parties | BillNika</title>
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

        <!-- BUSINESS -->
        <div class="p-5 border-b border-white/10">

            <div class="flex items-center gap-3">

                <div
    id="sidebarBusinessInitial"
    class="w-10 h-10 rounded-full bg-yellow-400
           flex items-center justify-center font-bold text-black">
    B
</div>

                <!-- <div>
                    <p class="font-semibold">Business Name</p>
                    <p class="text-xs text-gray-300">
                        6205857707
                    </p>
                </div> -->

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
                class="block w-full bg-indigo-500 hover:bg-indigo-600
                       text-white py-3 rounded-lg font-medium
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


    <!-- ================= MAIN ================= -->

    <main class="flex-1 p-6">

        <!-- TOP -->

        <div class="flex justify-between items-center mb-6">

            <h1 class="text-2xl font-semibold">
                Parties
            </h1>


            <div class="flex gap-3">

                <!-- <button
                    class="border border-indigo-500
                           text-indigo-600 px-5 py-2
                           rounded-lg">

                    🔗 SharedLedger Portal

                </button>


                <button
                    class="border border-blue-400
                           text-blue-600 px-5 py-2
                           rounded-lg">

                    📄 Reports

                </button>


                <button
                    class="border border-gray-300
                           px-4 py-2 rounded-lg">

                    ⚙

                </button> -->

            </div>

        </div>


        <!-- ================= SUMMARY CARDS ================= -->

       <!-- ================= SUMMARY CARDS ================= -->

<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-5">

    <!-- ALL PARTIES -->

    <button
        type="button"
        onclick="filterByBalance('all', this)"
        class="party-balance-card w-full text-left bg-white border-2 border-indigo-500 rounded-xl p-5 cursor-pointer hover:bg-indigo-50 transition">

        <p class="text-sm text-indigo-600">
            👥 All Parties
        </p>

        <p
            id="totalParties"
            class="text-2xl font-semibold mt-2">
            0
        </p>

    </button>


    <!-- TO COLLECT -->

    <button
        type="button"
        onclick="filterByBalance('receive', this)"
        class="party-balance-card w-full text-left bg-white border rounded-xl p-5 cursor-pointer hover:bg-green-50 transition">

        <p class="text-sm text-green-600">
            💰 To Collect
        </p>

        <p
            id="toCollect"
            class="text-2xl font-semibold mt-2">
            ₹ 0
        </p>

    </button>


    <!-- TO PAY -->

    <button
        type="button"
        onclick="filterByBalance('pay', this)"
        class="party-balance-card w-full text-left bg-white border rounded-xl p-5 cursor-pointer hover:bg-red-50 transition">

        <p class="text-sm text-red-500">
            💰 To Pay
        </p>

        <p
            id="toPay"
            class="text-2xl font-semibold mt-2">
            ₹ 0
        </p>

    </button>

</div>

        <!-- ================= SEARCH / ACTION ================= -->

        <div
            class="flex justify-between items-center
                   mb-4 gap-4">

            <div class="flex gap-3">

                <div class="relative">

                    <input
                        id="partySearch"
                        type="text"
                        placeholder="Search parties..."
                        class="w-80 border rounded-lg
                               px-4 py-3
                               focus:outline-none
                               focus:ring-2
                               focus:ring-indigo-400">

                </div>


                <select
                    id="categoryFilter"
                    class="border rounded-lg px-4 py-3">

                    <option value="">
                        Search Categories
                    </option>

                    <option value="customer">
                        Customer
                    </option>

                    <option value="supplier">
                        Supplier
                    </option>

                </select>

            </div>


            <button
                onclick="createParty()"
                class="bg-indigo-600 hover:bg-indigo-700
                       text-white px-6 py-3
                       rounded-lg font-medium">

                + Create Party

            </button>

        </div>


        <!-- ================= PARTY TABLE ================= -->

        <div class="bg-white rounded-xl border overflow-hidden">

            <table class="w-full text-sm">

                <thead class="bg-gray-100">

                    <tr>

                        <th
                            class="text-left px-5 py-4">
                            Party Name
                        </th>

                        <!-- <th
                            class="text-left px-5 py-4">
                            Category
                        </th> -->

                        <th
                            class="text-left px-5 py-4">
                            Mobile Number
                        </th>

                        <th
                            class="text-left px-5 py-4">
                            Party type
                        </th>

                        <th
                            class="text-left px-5 py-4">
                            Balance
                        </th>

                        <th
                            class="px-5 py-4">
                        </th>

                    </tr>

                </thead>


                <tbody id="partyTableBody">

                    <tr>

                        <td
                            colspan="5"
                            class="text-center py-10
                                   text-gray-500">

                            Loading parties...

                        </td>

                    </tr>

                </tbody>

            </table>

        </div>

    </main>

</div>


<!-- ================= JAVASCRIPT ================= -->

<script>

let allParties = [];
let selectedBalanceType = 'all';

/* ==========================================
   LOAD BUSINESS INFO
========================================== */

async function loadBusinessInfo() {

    const token =
        localStorage.getItem('token');

    if (!token) {
        return;
    }

    try {

        const response =
            await fetch('/api/business-info', {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
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
                'Unable to load business information'
            );
        }


        const business =
            result.data || {};


       const businessName =
    business.business_name || 'Business Name';


// Business Name
document.getElementById(
    'sidebarBusinessName'
).innerText =
    businessName;


// Mobile Number
document.getElementById(
    'sidebarBusinessMobile'
).innerText =
    business.mobile || '';


// Business Logo / Initial
document.getElementById(
    'sidebarBusinessInitial'
).innerText =
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


/* ==========================================
   LOAD PARTIES
========================================== */

async function loadParties() {

    const token =
        localStorage.getItem('token');

    const search =
        document.getElementById(
            'partySearch'
        ).value.trim();


    const category =
        document.getElementById(
            'categoryFilter'
        ).value;


    const table =
        document.getElementById(
            'partyTableBody'
        );


    table.innerHTML = `
        <tr>
            <td
                colspan="5"
                class="text-center py-10 text-gray-500">

                Loading parties...

            </td>
        </tr>
    `;


    try {

        const url = search
            ? `/api/parties?search=${encodeURIComponent(search)}`
            : `/api/parties`;


        const response =
            await fetch(url, {

                method: 'GET',

                headers: {
                    'Accept': 'application/json',
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
                'Unable to load parties'
            );

        }


        allParties =
            result.data || [];


        renderParties(
            category
        );


    } catch (error) {

        console.error(
            'Party API Error:',
            error
        );


        table.innerHTML = `
            <tr>
                <td
                    colspan="5"
                    class="text-center py-10 text-red-500">

                    Unable to load parties.

                </td>
            </tr>
        `;

    }

}

/* ==========================================
   BALANCE CARD FILTER
========================================== */

function filterByBalance(type, button) {

    selectedBalanceType = type;

    // Remove active style from all cards
    document
        .querySelectorAll('.party-balance-card')
        .forEach(card => {

            card.classList.remove(
                'border-2',
                'border-indigo-500',
                'bg-indigo-50'
            );

            card.classList.add('border');

        });


    // Active card
    button.classList.remove('border');

    button.classList.add(
        'border-2',
        'border-indigo-500',
        'bg-indigo-50'
    );


    // Get current category
    const category =
        document.getElementById('categoryFilter').value;


    // Re-render table
    renderParties(category);
}


/* ==========================================
   RENDER PARTIES
========================================== */

function renderParties(category = '') {

    const table =
        document.getElementById(
            'partyTableBody'
        );


    let parties = [...allParties];


// ==========================================
// CATEGORY FILTER
// ==========================================

if (category) {

    parties = parties.filter(
        party =>
            party.party_type === category
    );

}


// ==========================================
// BALANCE CARD FILTER
// ==========================================

if (selectedBalanceType === 'receive') {

    parties = parties.filter(party => {

        const balance =
            parseFloat(party.opening_balance || 0);

        return (
            party.opening_balance_type === 'receive' &&
            balance > 0
        );

    });

}


else if (selectedBalanceType === 'pay') {

    parties = parties.filter(party => {

        const balance =
            parseFloat(party.opening_balance || 0);

        return (
            party.opening_balance_type === 'pay' &&
            balance > 0
        );

    });

}


   /* SUMMARY
   Always calculate cards from ALL parties
*/

document.getElementById(
    'totalParties'
).innerText =
    allParties.length;


let collect = 0;
let pay = 0;


allParties.forEach(party => {

    const balance =
        parseFloat(party.opening_balance || 0);


    if (balance <= 0) {
        return;
    }


    if (
        party.opening_balance_type === 'pay'
    ) {

        pay += balance;

    } else {

        collect += balance;

    }

});


document.getElementById(
    'toCollect'
).innerText =
    `₹ ${collect.toLocaleString('en-IN')}`;


document.getElementById(
    'toPay'
).innerText =
    `₹ ${pay.toLocaleString('en-IN')}`;


    /* EMPTY */

    if (parties.length === 0) {

        table.innerHTML = `
            <tr>
                <td
                    colspan="5"
                    class="text-center py-10
                           text-gray-500">

                    No parties found.

                </td>
            </tr>
        `;

        return;

    }


    /* TABLE */

    table.innerHTML =
        parties.map(party => {

            const balance =
                parseFloat(
                    party.opening_balance || 0
                );


            const balanceType =
                party.opening_balance_type ||
                'receive';


            let balanceHTML =
                '₹ 0';


            if (balance > 0) {

                if (
                    balanceType ===
                    'receive'
                ) {

                    balanceHTML = `
                        <span class="text-gray-700">
                            ₹ ${balance.toLocaleString('en-IN')}
                        </span>

                        <span
                            class="text-green-500
                                   text-lg ml-2">

                            ↓

                        </span>
                    `;

                } else {

                    balanceHTML = `
                        <span class="text-gray-700">
                            ₹ ${balance.toLocaleString('en-IN')}
                        </span>

                        <span
                            class="text-red-500
                                   text-lg ml-2">

                            ↑

                        </span>
                    `;

                }

            }


            return `

                <tr
                    class="border-t
                           hover:bg-gray-50">

                    <td class="px-5 py-4">

                        ${escapeHtml(
                            party.party_name || ''
                        )}

                    </td>


                   


                    <td class="px-5 py-4">

                        ${escapeHtml(
                            party.contact_number || '-'
                        )}

                    </td>


                    <td class="px-5 py-4">

                        ${escapeHtml(
                            party.party_type || '-'
                        )}

                    </td>


                    <td class="px-5 py-4">

                        ${balanceHTML}

                    </td>


                    <td class="px-5 py-4 text-right">

    <div class="relative inline-block">

        <button
            type="button"
            onclick="partyMenu(${party.id}, this)"
            class="text-xl w-8 h-8
                   flex items-center justify-center
                   rounded-md
                   hover:bg-gray-100">

            ⋮

        </button>

    </div>

</td>

                </tr>

            `;

        }).join('');

}


/* ==========================================
   SEARCH
========================================== */

let searchTimer;

document
    .getElementById('partySearch')
    .addEventListener(
        'input',
        function () {

            clearTimeout(
                searchTimer
            );

            searchTimer =
                setTimeout(
                    loadParties,
                    300
                );

        }
    );


/* ==========================================
   CATEGORY
========================================== */

document
    .getElementById('categoryFilter')
    .addEventListener(
        'change',
        function () {

            renderParties(
                this.value
            );

        }
    );


/* ==========================================
   CREATE PARTY
========================================== */

function createParty() {
    window.location.href = "{{ route('parties.create') }}";
}


/* ==========================================
   PARTY MENU
========================================== */

function partyMenu(id, button) {

    // Close all existing menus
    document
        .querySelectorAll('.party-action-menu')
        .forEach(menu => menu.remove());


    // Find selected party
    const party =
        allParties.find(
            p => Number(p.id) === Number(id)
        );


    if (!party) {
        console.error(
            'Party not found:',
            id
        );
        return;
    }


    // Create menu
    const menu =
        document.createElement('div');


    menu.className =
        'party-action-menu absolute right-0 top-full mt-2 ' +
        'bg-white border border-gray-200 ' +
        'rounded-lg shadow-xl z-[9999] w-40';


    menu.innerHTML = `

        <button
            type="button"
            onclick="editParty(${party.id})"
            class="w-full text-left px-4 py-3
                   hover:bg-gray-100
                   text-gray-700
                   border-b
                   flex items-center gap-2">

            <span>✏️</span>
            <span>Edit</span>

        </button>


        <button
            type="button"
            onclick="deleteParty(${party.id})"
            class="w-full text-left px-4 py-3
                   hover:bg-red-50
                   text-red-600
                   flex items-center gap-2">

            <span>🗑️</span>
            <span>Delete</span>

        </button>

    `;


    // Put menu inside the button's parent
    button.parentElement.appendChild(menu);


    // Stop this click from immediately closing the menu
    menu.addEventListener(
        'click',
        function(event) {
            event.stopPropagation();
        }
    );

}

function editParty(id) {

    window.location.href =
        `/parties/${id}/edit`;

}

async function deleteParty(id) {

    const party =
        allParties.find(
            p => p.id === id
        );


    if (!party) {
        return;
    }


    const confirmed =
        confirm(
            `Are you sure you want to delete "${party.party_name}"?\n\nDeleted parties cannot be retrieved.`
        );


    if (!confirmed) {
        return;
    }


    const token =
        localStorage.getItem('token');


    if (!token) {

        window.location.href =
            '/start-free-billing';

        return;

    }


    try {

        const response =
            await fetch(
                `/api/parties/${id}`,
                {

                    method: 'DELETE',

                    headers: {

                        'Accept':
                            'application/json',

                        'Authorization':
                            `Bearer ${token}`

                    }

                }
            );


        const result =
            await response.json();


        console.log(
            'Delete Party Response:',
            result
        );


        if (!response.ok) {

            throw new Error(
                result.message ||
                'Unable to delete party.'
            );

        }


        alert(
            result.message ||
            'Party deleted successfully.'
        );


        // Reload the party list
        await loadParties();


    } catch (error) {

        console.error(
            'Delete Party Error:',
            error
        );


        alert(
            error.message ||
            'Something went wrong while deleting party.'
        );

    }

}

document.addEventListener(
    'click',
    function(event) {

        // If click is not on a three-dot button
        if (
            !event.target.closest(
                'button[onclick^="partyMenu"]'
            )
        ) {

            document
                .querySelectorAll(
                    '.party-action-menu'
                )
                .forEach(
                    menu => menu.remove()
                );

        }

    }
);


/* ==========================================
   ESCAPE HTML
========================================== */

function escapeHtml(value) {

    const div =
        document.createElement('div');

    div.textContent =
        value ?? '';

    return div.innerHTML;

}


/* ==========================================
   INITIAL LOAD
========================================== */

loadBusinessInfo();

loadParties();


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