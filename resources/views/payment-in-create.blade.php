<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Create Payment In</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: Arial, Helvetica, sans-serif;
            background: #f5f6fa;
            color: #172033;
        }

        .page {
            min-height: 100vh;
        }

        /* =========================
           TOP HEADER
        ========================= */

        .topbar {
            height: 68px;
            background: #fff;
            border-bottom: 1px solid #e5e7eb;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 24px;
        }

        .title-area {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .back-btn {
            text-decoration: none;
            color: #172033;
            font-size: 30px;
            line-height: 1;
        }

        .page-title {
            font-size: 21px;
            font-weight: 700;
        }

        .top-actions {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .top-btn {
            height: 42px;
            padding: 0 25px;
            border-radius: 7px;
            border: 1px solid #d9dce3;
            background: white;
            font-weight: 600;
            cursor: pointer;
        }

        .save-btn {
            background: #5144d9;
            color: white;
            border-color: #5144d9;
        }

        /* =========================
           CONTENT
        ========================= */

        .content {
            padding: 20px 26px;
        }

        .top-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 18px;
        }

        .card {
            background: white;
            border: 1px solid #e0e3e8;
            border-radius: 9px;
            padding: 20px;
        }

        .field-label {
            display: block;
            font-size: 14px;
            color: #667085;
            margin-bottom: 9px;
        }

        .form-control {
            width: 100%;
            height: 42px;
            border: 1px solid #d8dce4;
            border-radius: 6px;
            padding: 0 13px;
            font-size: 14px;
            outline: none;
            background: white;
        }

        .form-control:focus {
            border-color: #6557ed;
            box-shadow: 0 0 0 1px #6557ed;
        }

        .row-2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 18px;
            margin-top: 18px;
        }

        .row-3 {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 18px;
        }

        .notes {
            margin-top: 18px;
        }

        textarea.form-control {
            height: 76px;
            padding-top: 12px;
            resize: vertical;
        }

        /* =========================
           PARTY DROPDOWN
        ========================= */

        .party-dropdown {
            position: relative;
        }

        .party-input-wrapper {
            position: relative;
        }

        .party-input {
            width: 100%;
            height: 44px;
            border: 1px solid #d8dce4;
            border-radius: 6px;
            padding: 0 42px 0 13px;
            font-size: 14px;
            outline: none;
            background: white;
        }

        .party-input:focus {
            border-color: #6557ed;
            box-shadow: 0 0 0 1px #6557ed;
        }

        .party-arrow {
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 17px;
            color: #667085;
            pointer-events: none;
        }

        .party-menu {
            display: none;
            position: absolute;
            left: 0;
            right: 0;
            top: calc(100% + 3px);
            background: white;
            border: 1px solid #d8dce4;
            border-radius: 5px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.12);
            z-index: 9999;
            overflow: hidden;
        }

        .party-menu.show {
            display: block;
        }

        .party-header {
            background: #f0f1f3;
            padding: 10px 13px;
            font-size: 12px;
            font-weight: 600;
            color: #374151;
        }

        .party-list {
            max-height: 220px;
            overflow-y: auto;
        }

        .party-option {
            padding: 12px 13px;
            cursor: pointer;
            font-size: 14px;
            color: #374151;
        }

        .party-option:hover {
            background: #f5f5ff;
        }

        .party-create {
            margin: 9px 8px;
            height: 38px;
            border: 1px dashed #369cff;
            color: #1683e8;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            font-size: 13px;
            border-radius: 4px;
            background: white;
        }

        .party-create:hover {
            background: #f5faff;
        }

        .no-party-found {
            padding: 14px;
            text-align: center;
            color: #98a2b3;
            font-size: 13px;
        }

        /* =========================
           EMPTY TRANSACTIONS
        ========================= */

        .transactions {
            margin-top: 18px;
            background: white;
            border: 1px solid #e0e3e8;
            border-radius: 9px;
            min-height: 420px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* =========================================================
   SETTLE INVOICE TABLE
========================================================= */

.invoice-row {
    border-bottom: 1px solid #e0e3e8;
}

.invoice-row:hover {
    background: #fafaff;
}

.invoice-row td {
    padding: 12px;
    font-size: 14px;
    color: #344054;
}

.invoice-check {
    width: 18px;
    height: 18px;
    cursor: pointer;
}

.settle-amount-input {
    width: 130px;
    height: 36px;
    border: 1px solid #d8dce4;
    border-radius: 5px;
    padding: 0 10px;
    text-align: right;
    font-size: 14px;
}

.settle-amount-input:focus {
    outline: none;
    border-color: #6557ed;
    box-shadow: 0 0 0 1px #6557ed;
}

.invoice-number {
    color: #5144d9;
    font-weight: 600;
}

        .empty-state {
            text-align: center;
        }

        .empty-icon {
            width: 78px;
            height: 78px;
            border: 1px solid #dfe3e8;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 18px;
            color: #98a2b3;
            font-size: 34px;
        }

        .empty-title {
            font-size: 18px;
            font-weight: 700;
            margin-bottom: 8px;
        }

        .empty-text {
            color: #7a8496;
            font-size: 14px;
        }

        .select-party-btn {
            margin-top: 20px;
            background: #5144d9;
            color: white;
            border: 0;
            border-radius: 6px;
            height: 40px;
            padding: 0 35px;
            font-weight: 600;
            cursor: pointer;
        }

        /* =========================================================
   CREATE PARTY MODAL
========================================================= */

.create-party-modal {
    display: none;
    position: fixed;
    inset: 0;
    background: rgba(0, 0, 0, 0.50);
    z-index: 99999;

    align-items: center;
    justify-content: center;

    padding: 20px;
}

.create-party-modal.show {
    display: flex;
}

.create-party-modal-box {
    width: 100%;
    max-width: 520px;
    max-height: 90vh;

    background: #fff;
    border-radius: 10px;

    box-shadow: 0 20px 50px rgba(0, 0, 0, 0.25);

    overflow: hidden;

    display: flex;
    flex-direction: column;
}

.create-party-modal-header {
    display: flex;
    align-items: center;
    justify-content: space-between;

    padding: 18px 20px;

    border-bottom: 1px solid #e5e7eb;
}

.create-party-modal-header h2 {
    margin: 0;
    font-size: 20px;
    font-weight: 600;
    color: #172033;
}

.create-party-close {
    width: 38px;
    height: 38px;

    border: 1px solid #d9dce3;
    border-radius: 6px;

    background: white;

    font-size: 24px;
    color: #667085;

    cursor: pointer;
}

.create-party-close:hover {
    background: #f5f6fa;
}

.create-party-modal-body {
    padding: 20px;

    overflow-y: auto;
    max-height: 70vh;
}

.create-party-modal-footer {
    display: flex;
    justify-content: flex-end;
    gap: 12px;

    padding: 16px 20px;

    border-top: 1px solid #e5e7eb;
}

.create-party-modal-footer button {
    height: 40px;
    padding: 0 22px;

    border-radius: 6px;

    cursor: pointer;

    font-weight: 600;
}

.create-party-cancel {
    background: white;
    border: 1px solid #d9dce3;
    color: #344054;
}

.create-party-save {
    background: #5144d9;
    border: 1px solid #5144d9;
    color: white;
}

.create-party-save:hover {
    background: #4338ca;
}

        /* =========================
           RESPONSIVE
        ========================= */

        @media (max-width: 900px) {
            .top-grid {
                grid-template-columns: 1fr;
            }

            .row-3 {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 600px) {
            .content {
                padding: 12px;
            }

            .topbar {
                padding: 0 12px;
            }

            .top-actions .settings-btn {
                display: none;
            }

            .row-2 {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>

<div class="page">

    <!-- =========================
         HEADER
    ========================== -->

    <div class="topbar">

        <div class="title-area">

            <a href="{{ route('payment.in') }}" class="back-btn">
                ←
            </a>

            <div class="page-title">
                Record Payment In
            </div>

        </div>

        <div class="top-actions">

            <button type="button" class="top-btn settings-btn">
                ⚙ Settings
            </button>

            <a href="{{ route('payment.in') }}"
               class="top-btn"
               style="text-decoration:none;color:#172033;display:flex;align-items:center;">
                Cancel
            </a>

            <button type="button"
                    class="top-btn save-btn"
                    id="savePaymentBtn">
                Save
            </button>

        </div>

    </div>


    <!-- =========================
         CONTENT
    ========================== -->

    <div class="content">

        <div class="top-grid">

            <!-- =========================
                 LEFT CARD
            ========================== -->

            <div class="card">

                <div>

                    <label class="field-label">
                        Party Name
                    </label>

                    <div class="party-dropdown">

                        <div class="party-input-wrapper">

                            <input
                                type="text"
                                id="partySearch"
                                class="party-input"
                                placeholder="Search party by name or number"
                                autocomplete="off">

                            <span class="party-arrow">
                                ▾
                            </span>

                        </div>


                        <!-- PARTY DROPDOWN -->

                        <div id="partyMenu" class="party-menu">

                            <div class="party-header">
                                Party Name
                            </div>

                            <div id="partyList" class="party-list">

                                <!-- Parties loaded from API -->

                            </div>

                            <div
                                id="createPartyBtn"
                                class="party-create">
                                + Create Party
                            </div>

                        </div>

                    </div>

                    <!-- Selected party ID -->

                    <input
                        type="hidden"
                        id="party_id">

                </div>


                <div class="row-2">

                    <div>

                        <label class="field-label">
                            Amount Received
                        </label>

                        <input
                            type="number"
                            id="amount"
                            value="0"
                            min="0"
                            step="0.01"
                            class="form-control">

                    </div>


                    <div>

                        <label class="field-label">
                            Payment In Discount ⓘ
                        </label>

                        <input
                            type="number"
                            id="discount"
                            value="0"
                            min="0"
                            step="0.01"
                            class="form-control">

                    </div>

                </div>

            </div>


            <!-- =========================
                 RIGHT CARD
            ========================== -->

            <div class="card">

                <div class="row-3">

                    <div>

                        <label class="field-label">
                            Payment Date
                        </label>

                        <input
                            type="date"
                            id="payment_date"
                            value="{{ date('Y-m-d') }}"
                            class="form-control">

                    </div>


                    <div>

                        <label class="field-label">
                            Payment Mode
                        </label>

                        <select
                            id="payment_mode"
                            class="form-control">

                            <option value="Cash">
                                Cash
                            </option>

                            <option value="Card">
                                Card
                            </option>

                            <option value="UPI">
                                UPI
                            </option>

                            <option value="Bank">
                                Bank
                            </option>

                        </select>

                    </div>


                    <div>

                        <label class="field-label">
                            Payment In Number
                        </label>

                        <input
                            type="text"
                            id="payment_number"
                            class="form-control"
                            readonly>

                    </div>

                </div>


                <div class="notes">

                    <label class="field-label">
                        Notes
                    </label>

                    <textarea
                        id="notes"
                        class="form-control"
                        placeholder="Enter Notes"></textarea>

                </div>

            </div>

        </div>


        <!-- =========================
             TRANSACTIONS
        ========================== -->
<!-- 
        <div class="transactions">

            <div class="empty-state">

                <div class="empty-icon">
                    ▤
                </div>

                <div class="empty-title">
                    No party selected!
                </div>

                <div class="empty-text">
                    Select Party Name to view transactions
                </div>

                <button
                    type="button"
                    class="select-party-btn"
                    id="selectPartyBtn">
                    Select Party
                </button>

            </div>

        </div> -->

        <!-- =========================================================
     TRANSACTIONS
========================================================= -->

<div class="transactions" id="transactionsSection">

    <!-- EMPTY STATE -->
    <div class="empty-state" id="emptyTransactionState">

        <div class="empty-icon">
            ▤
        </div>

        <div class="empty-title">
            No party selected!
        </div>

        <div class="empty-text">
            Select Party Name to view transactions
        </div>

        <button
            type="button"
            class="select-party-btn"
            id="selectPartyBtn">
            Select Party
        </button>

    </div>


    <!-- SETTLE INVOICES -->
    <div
        id="settleInvoicesSection"
        style="display:none;width:100%;height:100%;">

        <!-- HEADER -->

        <div
            style="
                display:flex;
                align-items:center;
                justify-content:space-between;
                padding:18px 14px;
                border-bottom:1px solid #e0e3e8;
            ">

            <div
                style="
                    font-size:15px;
                    font-weight:700;
                    color:#172033;
                ">
                Settle invoices with this payment
            </div>


            <div
                style="
                    display:flex;
                    align-items:center;
                    gap:10px;
                ">

                <!-- SEARCH -->

                <button
                    type="button"
                    id="invoiceSearchBtn"
                    style="
                        width:36px;
                        height:36px;
                        border:1px solid #d8dce4;
                        background:white;
                        border-radius:6px;
                        cursor:pointer;
                        font-size:16px;
                    ">
                    🔍
                </button>


                <!-- DATE RANGE -->

                <button
                    type="button"
                    id="invoiceDateRangeBtn"
                    style="
                        height:36px;
                        padding:0 14px;
                        border:1px solid #d8dce4;
                        background:white;
                        border-radius:6px;
                        cursor:pointer;
                        color:#667085;
                    ">
                    Select Date Range
                    <span style="margin-left:20px;">▣</span>
                </button>

            </div>

        </div>


        <!-- TABLE -->

        <div style="overflow-x:auto;">

            <table
                style="
                    width:100%;
                    border-collapse:collapse;
                    min-width:900px;
                ">

                <thead>

                    <tr
                        style="
                            background:#f0f1f3;
                            border-bottom:1px solid #dfe3e8;
                        ">

                        <!-- SELECT ALL -->

                        <th
                            style="
                                width:45px;
                                padding:12px;
                                text-align:center;
                            ">

                            <input
                                type="checkbox"
                                id="selectAllInvoices"
                                style="
                                    width:18px;
                                    height:18px;
                                    cursor:pointer;
                                ">

                        </th>


                        <th style="padding:12px;text-align:left;">
                            Date
                        </th>


                        <th style="padding:12px;text-align:left;">
                            Due Date
                        </th>


                        <th style="padding:12px;text-align:left;">
                            Invoice #
                        </th>


                        <th style="padding:12px;text-align:right;">
                            Invoice Amount
                        </th>


                        <th style="padding:12px;text-align:right;">
                            Discount
                        </th>


                        <th style="padding:12px;text-align:right;">
                            Amount Received
                        </th>

                    </tr>

                </thead>


                <tbody id="invoiceTableBody">

                    <!-- INVOICES WILL LOAD HERE -->

                </tbody>

            </table>

        </div>


        <!-- NO INVOICES -->

        <div
            id="noInvoicesMessage"
            style="
                display:none;
                text-align:center;
                padding:70px 20px;
                color:#667085;
            ">

            <div
                style="
                    font-size:18px;
                    font-weight:600;
                    margin-bottom:8px;
                ">
                No Transactions yet!
            </div>

            <div style="font-size:14px;">
                No unpaid invoices for this party
            </div>

        </div>

    </div>

</div>

    </div>

</div>

<!-- ===================================================== -->
<!-- CREATE PARTY MODAL -->
<!-- ===================================================== -->

<!-- ===================================================== -->
<!-- CREATE PARTY MODAL -->
<!-- ===================================================== -->

<div id="createPartyModal" class="create-party-modal">

    <div class="create-party-modal-box">

        <!-- HEADER -->
        <div class="create-party-modal-header">

            <h2>
                Create New Party
            </h2>

            <button
                type="button"
                onclick="closeCreateParty()"
                class="create-party-close">

                ×

            </button>

        </div>


        <!-- BODY -->
        <div class="create-party-modal-body">

            <!-- PARTY NAME -->
            <div style="margin-bottom:20px;">

                <label class="field-label">
                    Party Name
                    <span style="color:red;">*</span>
                </label>

                <input
                    type="text"
                    id="newPartyName"
                    placeholder="Enter party name"
                    class="form-control">

            </div>


            <!-- MOBILE NUMBER -->
            <div style="margin-bottom:20px;">

                <label class="field-label">
                    Mobile Number
                </label>

                <input
                    type="text"
                    id="newPartyMobile"
                    placeholder="Enter Mobile Number"
                    maxlength="20"
                    class="form-control">

            </div>


            <!-- ADD ADDRESS -->
            <button
                type="button"
                onclick="togglePartyAddress()"
                style="
                    border:0;
                    background:none;
                    color:#1683e8;
                    cursor:pointer;
                    padding:0;
                    margin-bottom:20px;
                    font-size:14px;
                ">

                + Add Address (Optional)

            </button>


            <!-- ADDRESS FIELDS -->
            <div
                id="partyAddressFields"
                style="display:none; margin-bottom:20px;">

                <!-- BILLING ADDRESS -->
                <div style="margin-bottom:16px;">

                    <label class="field-label">
                        BILLING ADDRESS
                    </label>

                    <textarea
                        id="partyStreet"
                        rows="3"
                        placeholder="Enter billing address"
                        class="form-control"
                        style="height:80px;padding-top:12px;"></textarea>

                </div>


                <!-- STATE + PINCODE -->
                <div class="row-2"
                     style="margin-top:0;">

                    <div>

                        <label class="field-label">
                            STATE
                        </label>

                        <input
                            type="text"
                            id="partyState"
                            placeholder="Enter State"
                            class="form-control">

                    </div>


                    <div>

                        <label class="field-label">
                            PINCODE
                        </label>

                        <input
                            type="text"
                            id="partyPincode"
                            placeholder="Enter Pincode"
                            maxlength="6"
                            class="form-control">

                    </div>

                </div>


                <!-- CITY -->
                <div style="margin-top:16px;">

                    <label class="field-label">
                        CITY
                    </label>

                    <input
                        type="text"
                        id="partyCity"
                        placeholder="Enter City"
                        class="form-control">

                </div>


                <!-- SHIPPING SAME -->
                <label
                    style="
                        display:flex;
                        align-items:center;
                        gap:8px;
                        margin-top:16px;
                        font-size:14px;
                        color:#667085;
                    ">

                    <input
                        type="checkbox"
                        id="shippingSame"
                        checked>

                    Shipping address same as billing address

                </label>

            </div>


            <!-- ADD GSTIN -->
            <button
                type="button"
                onclick="togglePartyGST()"
                style="
                    border:0;
                    background:none;
                    color:#1683e8;
                    cursor:pointer;
                    padding:0;
                    margin-bottom:20px;
                    font-size:14px;
                ">

                + Add GSTIN (Optional)

            </button>


            <!-- GSTIN -->
            <div
                id="partyGSTFields"
                style="display:none; margin-bottom:20px;">

                <label class="field-label">
                    GSTIN
                </label>

                <input
                    type="text"
                    id="newPartyGST"
                    placeholder="ex: 29XXXXXX4381XX"
                    maxlength="15"
                    oninput="this.value=this.value.toUpperCase()"
                    class="form-control">

            </div>


            <!-- INFO -->
            <div
                style="
                    background:#fff7df;
                    border-radius:6px;
                    padding:12px;
                    text-align:center;
                    font-size:12px;
                    color:#667085;
                ">

                You can add Custom Fields from
                <span style="color:#1683e8;">
                    Party Settings
                </span>

            </div>

        </div>


        <!-- FOOTER -->
        <div class="create-party-modal-footer">

            <button
                type="button"
                onclick="closeCreateParty()"
                class="create-party-cancel">

                Cancel

            </button>


            <button
                type="button"
                id="savePartyButton"
                onclick="createParty()"
                class="create-party-save">

                Save

            </button>

        </div>

    </div>

</div>


<script>

    /* =========================================================
   CREATE PARTY MODAL
========================================================= */

function closeCreateParty() {

    document
        .getElementById('createPartyModal')
        .classList.remove('show');

}


/* =========================================================
   ADDRESS TOGGLE
========================================================= */

function togglePartyAddress() {

    const fields =
        document.getElementById('partyAddressFields');

    if (fields.style.display === 'none' || fields.style.display === '') {
        fields.style.display = 'block';
    } else {
        fields.style.display = 'none';
    }

}


/* =========================================================
   GST TOGGLE
========================================================= */

function togglePartyGST() {

    const fields =
        document.getElementById('partyGSTFields');

    if (fields.style.display === 'none' || fields.style.display === '') {
        fields.style.display = 'block';
    } else {
        fields.style.display = 'none';
    }

}

/* =========================================================
   CREATE PARTY API
========================================================= */

async function createParty() {

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

    const city =
        document
            .getElementById('partyCity')
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
            localStorage.getItem('token') ||
            localStorage.getItem('access_token');


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


        /* =================================================
           PARTY CREATED
        ================================================= */

        const party =
            result.data;


        /* Close modal */

        closeCreateParty();


        /* Reset form */

        resetPartyForm();


        /* Add new party to current payment */

        /* Add new party to current payment */

selectParty(
    party.id,
    party.party_name || party.name
);


        /* Reload parties */

        await loadParties();


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

/* =========================================================
   RESET PARTY FORM
========================================================= */

function resetPartyForm() {

    document
        .getElementById('newPartyName')
        .value = '';

    document
        .getElementById('newPartyMobile')
        .value = '';

    document
        .getElementById('newPartyGST')
        .value = '';

    document
        .getElementById('partyStreet')
        .value = '';

    document
        .getElementById('partyCity')
        .value = '';

    document
        .getElementById('partyState')
        .value = '';

    document
        .getElementById('partyPincode')
        .value = '';


   document
    .getElementById('partyAddressFields')
    .style.display = 'none';

document
    .getElementById('partyGSTFields')
    .style.display = 'none';
}

    /* =========================================================
       ELEMENTS
    ========================================================= */

    const partySearch = document.getElementById('partySearch');
    const partyMenu = document.getElementById('partyMenu');
    const partyList = document.getElementById('partyList');
    const partyId = document.getElementById('party_id');
    const createPartyBtn = document.getElementById('createPartyBtn');
    const selectPartyBtn = document.getElementById('selectPartyBtn');


    let parties = [];


    /* =========================================================
       OPEN PARTY DROPDOWN
    ========================================================= */

    partySearch.addEventListener('focus', function () {

        partyMenu.classList.add('show');

        loadParties();

    });


    partySearch.addEventListener('click', function () {

        partyMenu.classList.add('show');

    });


    /* =========================================================
       SEARCH PARTY
    ========================================================= */

    partySearch.addEventListener('input', function () {

        const search = this.value.toLowerCase().trim();

        renderParties(search);

        partyMenu.classList.add('show');

    });


    /* =========================================================
       LOAD PARTIES
    ========================================================= */

    async function loadParties() {

        partyList.innerHTML = `
            <div class="no-party-found">
                Loading parties...
            </div>
        `;

        try {

            const token =
                localStorage.getItem('token') ||
                localStorage.getItem('access_token');

            const headers = {
                'Accept': 'application/json'
            };

            if (token) {
                headers['Authorization'] = 'Bearer ' + token;
            }


            const response = await fetch('/api/parties', {
                method: 'GET',
                headers: headers
            });


            if (!response.ok) {
                throw new Error('Unable to load parties');
            }


            const result = await response.json();


            /*
             * Your PartyController normally returns:
             *
             * {
             *     success: true,
             *     data: [...]
             * }
             */

            parties = result.data || [];


            renderParties(partySearch.value.trim().toLowerCase());

        } catch (error) {

            console.error('Party loading error:', error);

            partyList.innerHTML = `
                <div class="no-party-found">
                    Unable to load parties
                </div>
            `;

        }

    }


    /* =========================================================
       RENDER PARTY LIST
    ========================================================= */

    function renderParties(search = '') {

        partyList.innerHTML = '';


        /*
         * CASH SALE
         * Keep this as the system/default party.
         */

        if (
            !search ||
            'cash sale'.includes(search)
        ) {

            const cashSale = document.createElement('div');

            cashSale.className = 'party-option';

            cashSale.textContent = 'Cash Sale';

            cashSale.addEventListener('click', function () {

                selectParty('', 'Cash Sale');

            });

            partyList.appendChild(cashSale);

        }


        /* =========================
           NORMAL PARTIES
        ========================== */

        const filtered = parties.filter(function (party) {

            const name =
                party.party_name ||
                party.name ||
                '';

            const phone =
                party.phone ||
                party.mobile ||
                party.mobile_number ||
                party.phone_number ||
                '';

            return (
                name.toLowerCase().includes(search) ||
                String(phone).toLowerCase().includes(search)
            );

        });


        filtered.forEach(function (party) {

            const name =
                party.party_name ||
                party.name ||
                'Unnamed Party';

            const phone =
                party.phone ||
                party.mobile ||
                party.mobile_number ||
                party.phone_number ||
                '';


            const option = document.createElement('div');

            option.className = 'party-option';


            if (phone) {

                option.innerHTML = `
                    <div style="font-weight:500;">
                        ${escapeHtml(name)}
                    </div>

                    <div style="font-size:12px;color:#98a2b3;margin-top:3px;">
                        ${escapeHtml(phone)}
                    </div>
                `;

            } else {

                option.textContent = name;

            }


            option.addEventListener('click', function () {

                selectParty(
                    party.id,
                    name
                );

            });


            partyList.appendChild(option);

        });


        if (partyList.children.length === 0) {

            partyList.innerHTML = `
                <div class="no-party-found">
                    No party found
                </div>
            `;

        }

    }


    /* =========================================================
       SELECT PARTY
    ========================================================= */

    function selectParty(id, name) {

        partyId.value = id;

        partySearch.value = name;

        partyMenu.classList.remove('show');


        /*
         * Later we can call:
         *
         * /api/parties/{id}/all-invoices
         *
         * and display pending invoices here.
         */

        if (id) {

            console.log('Selected Party ID:', id);

            loadPartyTransactions(id);

        }

    }


    /* =========================================================
       PARTY TRANSACTIONS
    ========================================================= */

    // async function loadPartyTransactions(id) {

        // try {

        //     const token =
        //         localStorage.getItem('token') ||
        //         localStorage.getItem('access_token');

        //     const headers = {
        //         'Accept': 'application/json'
        //     };

        //     if (token) {
        //         headers['Authorization'] = 'Bearer ' + token;
        //     }


        //     const response = await fetch(
        //         `/api/parties/${id}/all-invoices`,
        //         {
        //             method: 'GET',
        //             headers: headers
        //         }
        //     );


        //     if (!response.ok) {
        //         return;
        //     }


        //     const result = await response.json();

        //     console.log(
        //         'Party invoices:',
        //         result
        //     );


            /*
             * We will use this response next to display:
             *
             * Invoice Number
             * Invoice Date
             * Invoice Amount
             * Paid Amount
             * Balance
             * Checkbox / amount input
             *
             * inside the transaction section.
             */

    //     } catch (error) {

    //         console.error(
    //             'Transaction loading error:',
    //             error
    //         );

    //     }

    // }

    async function loadPartyTransactions(id) {

    const transactionsSection =
        document.getElementById('transactionsSection');

    const emptyState =
        document.getElementById('emptyTransactionState');

    const settleSection =
        document.getElementById('settleInvoicesSection');

    const tableBody =
        document.getElementById('invoiceTableBody');

    const noInvoicesMessage =
        document.getElementById('noInvoicesMessage');

    try {

        /* =====================================================
           AUTH TOKEN
        ===================================================== */

        const token =
            localStorage.getItem('token') ||
            localStorage.getItem('access_token');


        const headers = {
            'Accept': 'application/json'
        };


        if (token) {

            headers['Authorization'] =
                'Bearer ' + token;

        }


        /* =====================================================
           SHOW LOADING
        ===================================================== */

        emptyState.style.display = 'none';

        settleSection.style.display = 'block';

        tableBody.innerHTML = `
            <tr>
                <td
                    colspan="7"
                    style="
                        text-align:center;
                        padding:50px;
                        color:#667085;
                    ">
                    Loading invoices...
                </td>
            </tr>
        `;

        noInvoicesMessage.style.display = 'none';


        /* =====================================================
           GET PENDING INVOICES
        ===================================================== */

        const response = await fetch(
            `/api/parties/${id}/invoices`,
            {
                method: 'GET',
                headers: headers
            }
        );


        if (!response.ok) {

            const errorText =
                await response.text();

            console.error(
                'Invoice API Error:',
                errorText
            );

            throw new Error(
                'Unable to load party invoices.'
            );

        }


        const result =
            await response.json();


        console.log(
            'Party invoices:',
            result
        );


        /* =====================================================
           GET INVOICES
        ===================================================== */

        const invoices =
            result.invoices || [];


        /* =====================================================
           CURRENT PARTY BALANCE
        ===================================================== */

        if (result.party) {

            console.log(
                'Party Balance:',
                result.party.opening_balance
            );

        }


        /* =====================================================
           NO INVOICES
        ===================================================== */

        if (invoices.length === 0) {

            tableBody.innerHTML = '';

            noInvoicesMessage.style.display =
                'block';

            return;

        }


        noInvoicesMessage.style.display =
            'none';


        /* =====================================================
           RENDER INVOICES
        ===================================================== */

        tableBody.innerHTML = '';


        invoices.forEach(function (invoice) {

            const invoiceAmount =
                parseFloat(invoice.grand_total) || 0;

            const receivedAmount =
                parseFloat(invoice.received_amount) || 0;

            const balanceAmount =
                parseFloat(invoice.balance_amount) || 0;


            const row =
                document.createElement('tr');

            row.className =
                'invoice-row';


            row.dataset.invoiceId =
                invoice.id;


            row.dataset.balance =
                balanceAmount;


            row.innerHTML = `

                <!-- CHECKBOX -->

                <td
                    style="
                        text-align:center;
                    ">

                    <input
                        type="checkbox"
                        class="invoice-check"
                        data-invoice-id="${invoice.id}">

                </td>


                <!-- DATE -->

                <td>

                    ${formatInvoiceDate(
                        invoice.invoice_date
                    )}

                </td>


                <!-- DUE DATE -->

                <td>

                    ${
                        invoice.due_date
                            ? formatInvoiceDate(
                                invoice.due_date
                            )
                            : '-'
                    }

                </td>


                <!-- INVOICE NUMBER -->

                <td>

                    <span class="invoice-number">

                        ${escapeHtml(
                            invoice.invoice_number ||
                            ''
                        )}

                    </span>

                </td>


                <!-- INVOICE AMOUNT -->

                <td
                    style="
                        text-align:right;
                    ">

                    ₹ ${invoiceAmount.toFixed(2)}

                </td>


                <!-- DISCOUNT -->

                <td
                    style="
                        text-align:right;
                    ">

                    <input
                        type="number"
                        class="settle-discount-input"
                        data-invoice-id="${invoice.id}"
                        value="0"
                        min="0"
                        step="0.01"
                        style="
                            width:90px;
                            height:34px;
                            border:1px solid #d8dce4;
                            border-radius:5px;
                            padding:0 8px;
                            text-align:right;
                        ">

                </td>


                <!-- AMOUNT RECEIVED -->

                <td
                    style="
                        text-align:right;
                    ">

                    <input
                        type="number"
                        class="settle-amount-input"
                        data-invoice-id="${invoice.id}"
                        value="0"
                        min="0"
                        max="${balanceAmount}"
                        step="0.01"
                        placeholder="0.00">

                    <div
                        style="
                            font-size:11px;
                            color:#98a2b3;
                            margin-top:3px;
                        ">

                        Balance:
                        ₹ ${balanceAmount.toFixed(2)}

                    </div>

                </td>

            `;


            tableBody.appendChild(row);

        });


        /* =====================================================
           CHECKBOX EVENTS
        ===================================================== */

        document
            .querySelectorAll('.invoice-check')
            .forEach(function (checkbox) {

                checkbox.addEventListener(
                    'change',
                    function () {

                        const invoiceId =
                            this.dataset.invoiceId;


                        const amountInput =
                            document.querySelector(
                                `.settle-amount-input[data-invoice-id="${invoiceId}"]`
                            );


                        const row =
                            document.querySelector(
                                `.invoice-row[data-invoice-id="${invoiceId}"]`
                            );


                        const balance =
                            parseFloat(
                                row.dataset.balance
                            ) || 0;


                        if (this.checked) {

                            /*
                             * Automatically fill the
                             * invoice's remaining balance.
                             */

                            amountInput.value =
                                balance.toFixed(2);

                        } else {

                            amountInput.value =
                                '0';

                        }


                        updateTotalSettled();

                    });

            });


        /* =====================================================
           AMOUNT INPUT EVENTS
        ===================================================== */

        document
            .querySelectorAll('.settle-amount-input')
            .forEach(function (input) {

                input.addEventListener(
                    'input',
                    function () {

                        const invoiceId =
                            this.dataset.invoiceId;


                        const checkbox =
                            document.querySelector(
                                `.invoice-check[data-invoice-id="${invoiceId}"]`
                            );


                        const row =
                            document.querySelector(
                                `.invoice-row[data-invoice-id="${invoiceId}"]`
                            );


                        const balance =
                            parseFloat(
                                row.dataset.balance
                            ) || 0;


                        let amount =
                            parseFloat(
                                this.value
                            ) || 0;


                        if (amount < 0) {

                            amount = 0;

                        }


                        if (amount > balance) {

                            amount = balance;

                        }


                        this.value =
                            amount > 0
                                ? amount
                                : '';


                        checkbox.checked =
                            amount > 0;


                        updateTotalSettled();

                    });

            });


        /* =====================================================
           SELECT ALL
        ===================================================== */

        const selectAll =
            document.getElementById(
                'selectAllInvoices'
            );


        selectAll.checked = false;


        selectAll.onchange =
            function () {

                document
                    .querySelectorAll('.invoice-check')
                    .forEach(function (checkbox) {

                        checkbox.checked =
                            selectAll.checked;

                        const invoiceId =
                            checkbox.dataset.invoiceId;


                        const row =
                            document.querySelector(
                                `.invoice-row[data-invoice-id="${invoiceId}"]`
                            );


                        const amountInput =
                            document.querySelector(
                                `.settle-amount-input[data-invoice-id="${invoiceId}"]`
                            );


                        const balance =
                            parseFloat(
                                row.dataset.balance
                            ) || 0;


                        if (selectAll.checked) {

                            amountInput.value =
                                balance.toFixed(2);

                        } else {

                            amountInput.value =
                                '0';

                        }

                    });


                updateTotalSettled();

            };


        updateTotalSettled();


    } catch (error) {

        console.error(
            'Transaction loading error:',
            error
        );


        tableBody.innerHTML = `
            <tr>
                <td
                    colspan="7"
                    style="
                        text-align:center;
                        padding:50px;
                        color:#dc2626;
                    ">
                    Unable to load invoices.
                </td>
            </tr>
        `;

    }

}


/* =========================================================
   FORMAT INVOICE DATE
========================================================= */

function formatInvoiceDate(dateString) {

    if (!dateString) {
        return '-';
    }


    const date =
        new Date(dateString);


    if (isNaN(date.getTime())) {
        return dateString;
    }


    return date.toLocaleDateString(
        'en-GB',
        {
            day: '2-digit',
            month: 'short',
            year: 'numeric'
        }
    );

}


/* =========================================================
   TOTAL SETTLED
========================================================= */

function updateTotalSettled() {

    let total = 0;


    document
        .querySelectorAll('.settle-amount-input')
        .forEach(function (input) {

            total +=
                parseFloat(input.value) || 0;

        });


    console.log(
        'Total invoice settlement:',
        total.toFixed(2)
    );


    /*
     * Do not automatically change Amount Received.
     * The user can enter the payment amount separately.
     */

}


/* =========================================================
   ESCAPE HTML
========================================================= */

function escapeHtml(value) {

    return String(value)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');

}


    

    /* =========================================================
   CREATE PARTY
========================================================= */

createPartyBtn.addEventListener('click', function (event) {

    event.preventDefault();
    event.stopPropagation();

    // Close party dropdown
    partyMenu.classList.remove('show');

    // Open Create Party modal
    const modal =
        document.getElementById('createPartyModal');

    modal.classList.add('show');

    // Automatically focus Party Name
    setTimeout(function () {

        document
            .getElementById('newPartyName')
            .focus();

    }, 100);

});


    /* =========================================================
       SELECT PARTY BUTTON
    ========================================================= */

    selectPartyBtn.addEventListener('click', function () {

        partySearch.focus();

        partyMenu.classList.add('show');

        loadParties();

    });


    /* =========================================================
       CLOSE DROPDOWN WHEN CLICKING OUTSIDE
    ========================================================= */

    document.addEventListener('click', function (event) {

        const dropdown =
            document.querySelector('.party-dropdown');

        if (!dropdown.contains(event.target)) {

            partyMenu.classList.remove('show');

        }

    });


    /* =========================================================
       ESCAPE HTML
    ========================================================= */

    function escapeHtml(value) {

        return String(value)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');

    }


    /* =========================================================
       LOAD NEXT PAYMENT NUMBER
    ========================================================= */

    async function loadNextPaymentNumber() {

        try {

            const token =
                localStorage.getItem('token') ||
                localStorage.getItem('access_token');

            const headers = {
                'Accept': 'application/json'
            };

            if (token) {
                headers['Authorization'] =
                    'Bearer ' + token;
            }


            const response = await fetch(
                '/api/payments/next-number',
                {
                    method: 'GET',
                    headers: headers
                }
            );


            if (!response.ok) {
                return;
            }


            const result = await response.json();


            if (result.next_number !== undefined) {

                document.getElementById(
                    'payment_number'
                ).value = result.next_number;

            }

        } catch (error) {

            console.error(
                'Payment number error:',
                error
            );

        }

    }

    /* =========================================================
   SAVE PAYMENT IN
========================================================= */

const savePaymentBtn = document.getElementById('savePaymentBtn');

savePaymentBtn.addEventListener('click', async function () {

    /* -------------------------
       GET FORM VALUES
    ------------------------- */

    const party_id =
        document.getElementById('party_id').value;

    const payment_date =
        document.getElementById('payment_date').value;

    const amount =
        parseFloat(document.getElementById('amount').value) || 0;

    const payment_mode =
        document.getElementById('payment_mode').value;


    /* -------------------------
       VALIDATION
    ------------------------- */

    if (!party_id) {
        alert('Please select a party.');
        partySearch.focus();
        return;
    }

    if (amount <= 0) {
        alert('Please enter a valid amount received.');
        document.getElementById('amount').focus();
        return;
    }

    if (!payment_date) {
        alert('Please select payment date.');
        return;
    }


    /* -------------------------
       DISABLE BUTTON
    ------------------------- */

    savePaymentBtn.disabled = true;
    savePaymentBtn.innerText = 'Saving...';


    try {

        /* -------------------------
           AUTH TOKEN
        ------------------------- */

        const token =
            localStorage.getItem('token') ||
            localStorage.getItem('access_token');


        /* -------------------------
           PAYMENT DATA
        ------------------------- */

        const payload = {

            party_id: parseInt(party_id),

            payment_date: payment_date,

            amount: amount,

            payment_mode: payment_mode,

            invoices: []

        };


        console.log('Saving Payment In:', payload);


        /* -------------------------
           API CALL
        ------------------------- */

        const response = await fetch('/api/payments', {

            method: 'POST',

            headers: {

                'Content-Type': 'application/json',

                'Accept': 'application/json',

                'Authorization': 'Bearer ' + token

            },

            body: JSON.stringify(payload)

        });


        const result = await response.json();


        console.log('Payment API Response:', result);


        /* -------------------------
           API ERROR
        ------------------------- */

        if (!response.ok) {

            console.error(
                'Payment Save Error:',
                result
            );


            let message =
                result.message ||
                'Unable to save payment.';


            if (result.errors) {

                message =
                    Object.values(result.errors)
                        .flat()
                        .join('\n');

            }


            throw new Error(message);

        }


        /* -------------------------
           SUCCESS
        ------------------------- */

        if (result.success) {

            alert(
                'Payment In saved successfully. Payment No: ' +
                result.payment_no
            );


            /* Redirect to Payment In list */

            window.location.href =
                "{{ route('payment.in') }}";

        } else {

            throw new Error(
                result.message ||
                'Payment could not be saved.'
            );

        }


    } catch (error) {

        console.error(
            'Save Payment Error:',
            error
        );


        alert(
            error.message ||
            'Unable to save payment.'
        );


    } finally {

        savePaymentBtn.disabled = false;
        savePaymentBtn.innerText = 'Save';

    }

});


    loadNextPaymentNumber();

</script>

</body>
</html>