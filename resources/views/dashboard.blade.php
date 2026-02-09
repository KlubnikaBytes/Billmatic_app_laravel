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

        <!-- USER INFO -->
        <div class="p-5 border-b border-white/10">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-yellow-400 flex items-center justify-center font-bold text-black">
                    B
                </div>
                <div>
                    <p class="font-semibold">Business Name</p>
                    <p class="text-xs text-gray-300">6205857707</p>
                </div>
            </div>
        </div>

        <!-- CREATE INVOICE -->
        <div class="p-4">
            <button class="w-full bg-indigo-500 hover:bg-indigo-600 text-white py-2 rounded-lg font-medium">
                + Create Sales Invoice
            </button>
        </div>

        <!-- MENU -->
        <nav class="flex-1 px-3 space-y-1 text-sm">
            <a href="#" class="flex items-center gap-3 px-3 py-2 rounded-lg bg-indigo-600">
                📊 Dashboard
            </a>
            <a href="#" class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-white/10">
                👥 Parties
            </a>
            <a href="#" class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-white/10">
                📦 Items
            </a>
            <a href="#" class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-white/10">
                🧾 Sales
            </a>
            <a href="#" class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-white/10">
                🛒 Purchases
            </a>
            <a href="#" class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-white/10">
                📑 Reports
            </a>
        </nav>

        <!-- FOOTER -->
        <div class="p-4 text-xs text-gray-400 border-t border-white/10">
            🔒 100% Secure <br>
            ISO Certified
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

        <!-- BOOK DEMO CARD -->
        <div class="bg-amber-50 rounded-xl p-6 flex justify-between items-center mb-8">
            <div>
                <h3 class="font-semibold text-lg mb-1">Book Free Demo</h3>
                <p class="text-sm text-gray-600">
                    Get a personalised tour from our solution expert
                </p>
                <a href="#" class="text-indigo-600 font-medium mt-2 inline-block">
                    Book Demo Now →
                </a>
            </div>
            <img src="https://dummyimage.com/160x100/ddd/000&text=Demo" class="rounded-lg">
        </div>

        <!-- BUSINESS OVERVIEW -->
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-lg font-semibold">Business Overview</h2>
            <span class="text-sm text-gray-500">
                Last Update: 06 Feb 2026 | 12:33 PM
            </span>
        </div>

        <!-- STATS -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">

            <div class="bg-green-50 p-5 rounded-xl">
                <p class="text-sm text-green-700">↓ To Collect</p>
                <p class="text-2xl font-bold">₹ 6,303</p>
            </div>

            <div class="bg-red-50 p-5 rounded-xl">
                <p class="text-sm text-red-700">↑ To Pay</p>
                <p class="text-2xl font-bold">₹ 980</p>
            </div>

            <div class="bg-blue-50 p-5 rounded-xl">
                <p class="text-sm text-blue-700">🏦 Cash + Bank</p>
                <p class="text-2xl font-bold">₹ 4,441.25</p>
            </div>

        </div>

        <!-- TABLE + CHECKLIST -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            <!-- TRANSACTIONS -->
            <div class="lg:col-span-2 bg-white rounded-xl p-5">
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
                    </thead>
                    <tbody>
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
                    </tbody>
                </table>
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

</body>
</html>
