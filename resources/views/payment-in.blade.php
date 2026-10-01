<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Payment In</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f5f6fa;
            color: #202124;
        }

        .layout {
            display: flex;
            min-height: 100vh;
        }

        /* =========================
           SIDEBAR
        ========================= */

        .sidebar {
            width: 225px;
            background: #111a2d;
            color: white;
            min-height: 100vh;
            padding: 20px 12px;
        }

        .business {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px;
            margin-bottom: 20px;
        }

        .business-logo {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            background: #ffd400;
            color: #111;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 20px;
        }

        .business-name {
            font-weight: bold;
        }

        .business-type {
            font-size: 12px;
            color: #c8ccda;
            margin-top: 4px;
        }

        .create-btn {
            width: 100%;
            border: none;
            border-radius: 8px;
            background: #5548ed;
            color: white;
            padding: 14px;
            font-size: 15px;
            font-weight: bold;
            cursor: pointer;
            margin-bottom: 15px;
        }

        .menu-item {
            padding: 12px 15px;
            border-radius: 8px;
            margin-bottom: 4px;
            cursor: pointer;
            color: #e8eaf2;
        }

        .menu-item:hover {
            background: #202b44;
        }

        .menu-item.active {
            background: #5548ed;
            color: white;
        }

        .sales-section {
            background: #1b2438;
            border-radius: 8px;
            padding: 5px;
            margin-bottom: 5px;
        }

        .sales-title {
            padding: 12px 10px;
            font-weight: bold;
        }

        .sales-child {
            padding: 11px 25px;
            color: #c8ccda;
            border-radius: 6px;
            cursor: pointer;
        }

        .sales-child:hover {
            background: #28334c;
        }

        .sales-child.active {
            background: #5548ed;
            color: white;
        }

        /* =========================
           CONTENT
        ========================= */

        .content {
            flex: 1;
            padding: 25px;
        }

        .card {
            background: white;
            border-radius: 12px;
            padding: 20px;
            min-height: calc(100vh - 50px);
            box-shadow: 0 1px 4px rgba(0,0,0,0.08);
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .header h2 {
            margin: 0;
            font-size: 23px;
        }

        .create-payment-btn {
            border: none;
            background: #5548ed;
            color: white;
            padding: 13px 22px;
            border-radius: 7px;
            font-weight: bold;
            cursor: pointer;
        }

        .toolbar {
            display: flex;
            gap: 10px;
            margin-bottom: 15px;
        }

        .search {
            width: 240px;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 7px;
            font-size: 14px;
        }

        .date-filter {
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 7px;
            background: white;
        }

        /* =========================
           TABLE
        ========================= */

        .table-wrapper {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        thead {
            background: #f3f4f6;
        }

        th,
        td {
            padding: 15px 12px;
            text-align: left;
            border-bottom: 1px solid #e5e7eb;
            font-size: 14px;
        }

        th {
            font-weight: 600;
            color: #444;
        }

        .payment-number {
            color: #5146e5;
            font-weight: 600;
            cursor: pointer;
        }

        .amount {
            font-weight: 600;
        }

        .empty {
            text-align: center;
            padding: 100px 20px;
            color: #7b8497;
        }

        .empty-icon {
            font-size: 50px;
            margin-bottom: 15px;
        }

        @media(max-width: 800px) {

            .sidebar {
                width: 180px;
            }

            .content {
                padding: 10px;
            }

            .header {
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
            }
        }
    </style>
</head>

<body>

<div class="layout">

    <!-- =========================
         SIDEBAR
    ========================== -->

    <aside class="sidebar">

        <div class="business">
    <div class="business-logo" id="businessLogo">B</div>

    <div>
        <div class="business-name" id="businessName">
            Loading...
        </div>

        <div class="business-type" id="businessNumber">
            Loading...
        </div>
    </div>
</div>

        <button class="create-btn"
                onclick="window.location.href='/sales-invoice/create'">
            + Create Sales Invoice
        </button>

        <div class="menu-item"
             onclick="window.location.href='/dashboard'">
            📊 Dashboard
        </div>

        <div class="menu-item"
             onclick="window.location.href='/parties'">
            👥 Parties
        </div>

        <div class="menu-item"
             onclick="window.location.href='/items'">
            📦 Items
        </div>

        <!-- SALES -->
        <div class="sales-section">

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

        </div>

        <div class="menu-item"
             onclick="window.location.href='/purchases'">
            🛒 Purchases
        </div>

        <!-- <div class="menu-item">
            📄 Reports
        </div> -->

    </aside>


    <!-- =========================
         MAIN CONTENT
    ========================== -->

    <main class="content">

        <div class="card">

            <div class="header">

                <h2>Payment In</h2>

                <button class="create-payment-btn"
                        onclick="window.location.href='/payment-in/create'">
                    Create Payment In
                </button>

            </div>


            <div class="toolbar">

                <input
                    type="text"
                    id="search"
                    class="search"
                    placeholder="🔍 Search"
                    oninput="filterPayments()"
                >

                <select id="dateFilter" class="date-filter">
                    <option value="365">Last 365 Days</option>
                    <option value="30">Last 30 Days</option>
                    <option value="90">Last 90 Days</option>
                    <option value="all">All</option>
                </select>

            </div>


            <div class="table-wrapper">

                <table>

                    <thead>

                    <tr>
                        <th>Date</th>
                        <th>Payment Number</th>
                        <th>Party Name</th>
                        <th>Total Amount Settled</th>
                        <th>Amount Received</th>
                        <th>Payment Mode</th>
                    </tr>

                    </thead>

                    <tbody id="paymentTableBody">

                    </tbody>

                </table>

            </div>

        </div>

    </main>

</div>


<script>

/* =====================================
   LOAD BUSINESS DETAILS
===================================== */

async function loadBusinessDetails() {

    try {

        const token = localStorage.getItem('token');

        const response = await fetch('/api/business-info', {
            headers: {
                'Accept': 'application/json',
                'Authorization': token
                    ? `Bearer ${token}`
                    : ''
            }
        });

        const result = await response.json();

        if (!response.ok) {
            console.error('Business details error:', result);
            return;
        }

        const business = result.data || result;

        const businessName =
            business.business_name ||
            business.name ||
            'Business Name';

        const businessNumber =
            business.business_number ||
            business.phone ||
            business.mobile ||
            business.contact_number ||
            '';

        document.getElementById('businessName').textContent =
            businessName;

        document.getElementById('businessNumber').textContent =
            businessNumber;

        // First letter of business name
        document.getElementById('businessLogo').textContent =
            businessName.charAt(0).toUpperCase();

    } catch (error) {

        console.error(
            'Business details loading error:',
            error
        );

    }
}    

let payments = [];


/* =====================================
   LOAD PAYMENTS
===================================== */

async function loadPayments() {

    try {

        const token = localStorage.getItem('token');

        const response = await fetch('/api/payments', {

            headers: {
                'Accept': 'application/json',
                'Authorization': token
                    ? `Bearer ${token}`
                    : ''
            }

        });

        const result = await response.json();

        if (!response.ok) {

            console.error(result);

            return;
        }

        payments = result.data || [];

        renderPayments();

    } catch (error) {

        console.error('Payment loading error:', error);

    }
}


/* =====================================
   RENDER
===================================== */

function renderPayments(list = payments) {

    const tbody =
        document.getElementById('paymentTableBody');

    tbody.innerHTML = '';

    if (!list.length) {

        tbody.innerHTML = `
            <tr>
                <td colspan="6">
                    <div class="empty">

                        <div class="empty-icon">
                            📄
                        </div>

                        <div>
                            No transactions matching the current filter
                        </div>

                    </div>
                </td>
            </tr>
        `;

        return;
    }


    list.forEach(payment => {

        const row = document.createElement('tr');

        row.innerHTML = `

            <td>
                ${formatDate(payment.payment_date)}
            </td>

            <td>
                <span
                    class="payment-number"
                    onclick="openPayment(${payment.id})">
                    PAY-${payment.payment_number}
                </span>
            </td>

            <td>
                ${escapeHtml(payment.party_name || '')}
            </td>

            <td class="amount">
                ₹ ${formatAmount(payment.total_amount_settled)}
            </td>

            <td class="amount">
                ₹ ${formatAmount(payment.amount_received)}
            </td>

            <td>
                ${escapeHtml(payment.payment_mode || '')}
            </td>

        `;

        tbody.appendChild(row);

    });

}


/* =====================================
   SEARCH
===================================== */

function filterPayments() {

    const search =
        document
            .getElementById('search')
            .value
            .toLowerCase()
            .trim();


    const filtered = payments.filter(payment => {

        return (

            String(payment.payment_number || '')
                .toLowerCase()
                .includes(search)

            ||

            String(payment.party_name || '')
                .toLowerCase()
                .includes(search)

            ||

            String(payment.payment_mode || '')
                .toLowerCase()
                .includes(search)

        );

    });

    renderPayments(filtered);

}


/* =====================================
   OPEN PAYMENT
===================================== */

function openPayment(id) {

    window.location.href =
        `/payment-in/${id}`;

}


/* =====================================
   HELPERS
===================================== */

function formatAmount(value) {

    return Number(value || 0)
        .toLocaleString('en-IN', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });

}


function formatDate(date) {

    if (!date) return '-';

    const d = new Date(date);

    return d.toLocaleDateString('en-IN', {
        day: '2-digit',
        month: 'short',
        year: 'numeric'
    });

}


function escapeHtml(value) {

    const div = document.createElement('div');

    div.textContent = value;

    return div.innerHTML;

}


/* =====================================
   INITIAL LOAD
===================================== */

loadBusinessDetails();
loadPayments();

</script>

</body>
</html>