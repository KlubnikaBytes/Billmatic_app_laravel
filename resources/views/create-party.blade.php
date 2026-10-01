<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <title>Create Party | BillNika</title>

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1">

    <script src="https://cdn.tailwindcss.com"></script>

</head>


<body class="bg-gray-100">


<!-- JWT PROTECTION -->

<script>

    if (!localStorage.getItem('token')) {

        window.location.href =
            '/start-free-billing';

    }

</script>


<div class="flex min-h-screen">


    <!-- =====================================================
         SIDEBAR
    ====================================================== -->

    <aside
        class="w-64 bg-gradient-to-b from-slate-900
               to-slate-800 text-white flex flex-col">


        <!-- BUSINESS -->

        <div class="p-5 border-b border-white/10">

            <div class="flex items-center gap-3">

               <div
    id="sidebarBusinessInitial"
    class="w-10 h-10 rounded-full
           bg-purple-600
           flex items-center justify-center
           font-bold">

    B

</div>


                <!-- <div>

                    <p class="font-semibold">
                        Business Name
                    </p>

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
                class="block w-full
                       bg-indigo-500
                       hover:bg-indigo-600
                       text-white py-3
                       rounded-lg font-medium
                       text-center">

                + Create Sales Invoice

            </a>

        </div>


        <!-- MENU -->

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

        <div
            class="p-4 text-xs text-gray-400
                   border-t border-white/10">

            🔒 100% Secure
            <br>

            ISO Certified

        </div>

    </aside>


    <!-- =====================================================
         MAIN CONTENT
    ====================================================== -->

    <main class="flex-1 bg-gray-50">


        <!-- =================================================
             TOP BAR
        ================================================== -->

        <div
            class="h-16 bg-white border-b
                   flex items-center justify-between
                   px-6">


            <div class="flex items-center gap-3">

                <button
                    onclick="goBack()"
                    class="text-2xl text-gray-600">

                    ←

                </button>


                <h1
    id="pageTitle"
    class="text-xl font-medium">

    Create Party

</h1>

            </div>


            <div class="flex items-center gap-3">

<!-- 
                <button
                    class="border px-5 py-2
                           rounded-lg text-sm">

                    ⌨

                </button>


                <button
                    class="border px-5 py-2
                           rounded-lg text-sm">

                    Party Settings ⚙

                </button> -->


                <button
    id="saveNewButton"
    onclick="saveParty(true)"
    class="border border-indigo-600
           text-indigo-600
           px-6 py-2 rounded-lg">

    Save & New

</button>


               <button
    id="saveButton"
    onclick="saveParty(false)"
    class="bg-indigo-600
           hover:bg-indigo-700
           text-white
           px-8 py-2 rounded-lg">

    Save

</button>

            </div>

        </div>


        <!-- =================================================
             FORM
        ================================================== -->

        <div
            class="p-6 space-y-5"
            id="partyForm">


            <!-- =================================================
                 GENERAL DETAILS
            ================================================== -->

            <section
                class="bg-white border rounded-lg">


                <div
                    class="px-5 py-3
                           border-b bg-gray-50">

                    <h2 class="font-medium text-gray-700">

                        General Details

                    </h2>

                </div>


                <div class="p-5">


                    <!-- FIRST ROW -->

                    <div
                        class="grid grid-cols-4 gap-5">


                        <!-- PARTY NAME -->

                        <div>

                            <label
                                class="block text-sm
                                       text-gray-500 mb-2">

                                Party Name
                                <span class="text-red-500">*</span>

                            </label>


                            <input
                                id="party_name"
                                type="text"
                                placeholder="Enter name"
                                class="w-full border
                                       rounded-md px-3 py-3
                                       focus:outline-none
                                       focus:ring-2
                                       focus:ring-indigo-400">

                        </div>


                        <!-- MOBILE -->

                        <div>

                            <label
                                class="block text-sm
                                       text-gray-500 mb-2">

                                Mobile Number

                            </label>


                            <input
                                id="contact_number"
                                type="text"
                                maxlength="20"
                                placeholder="Enter mobile number"
                                class="w-full border
                                       rounded-md px-3 py-3">

                        </div>


                        <!-- EMAIL -->

                        <div>

                            <label
                                class="block text-sm
                                       text-gray-500 mb-2">

                                Email

                            </label>


                            <input
                                id="email"
                                type="email"
                                placeholder="Enter email"
                                class="w-full border
                                       rounded-md px-3 py-3">

                        </div>


                        <!-- OPENING BALANCE -->

                        <div>

                            <label
                                class="block text-sm
                                       text-gray-500 mb-2">

                                Opening Balance

                            </label>


                            <div class="flex">


                                <span
                                    class="flex items-center
                                           px-3
                                           border border-r-0
                                           rounded-l-md
                                           bg-gray-50">

                                    ₹

                                </span>


                                <input
                                    id="opening_balance"
                                    type="number"
                                    step="0.01"
                                    value="0"
                                    class="w-full border
                                           px-3 py-3">


                                <select
                                    id="opening_balance_type"
                                    class="border
                                           rounded-r-md
                                           px-3
                                           bg-white">

                                    <option value="receive">
                                        To Collect
                                    </option>

                                    <option value="pay">
                                        To Pay
                                    </option>

                                </select>

                            </div>

                        </div>

                    </div>


                    <!-- SECOND ROW -->

                    <div
                        class="grid grid-cols-3 gap-5
                               mt-6">


                        <!-- GST -->

                        <div>

                            <label
                                class="block text-sm
                                       text-gray-500 mb-2">

                                GSTIN

                            </label>


                            <div class="flex gap-3">

                                <input
                                    id="gst_number"
                                    type="text"
                                    maxlength="15"
                                    placeholder="ex: 29XXXXX9438X1XX"
                                    class="flex-1 border
                                           rounded-md px-3 py-3
                                           uppercase">


                                <button
                                    type="button"
                                    onclick="getGSTDetails()"
                                    class="px-5
                                           bg-indigo-100
                                           text-indigo-600
                                           rounded-md">

                                    Get Details

                                </button>

                            </div>


                            <p
                                class="text-xs
                                       text-gray-500 mt-2">

                                Note: You can auto populate
                                party details from GSTIN

                            </p>

                        </div>


                        <!-- PAN -->

                        <div>

                            <label
                                class="block text-sm
                                       text-gray-500 mb-2">

                                PAN Number

                            </label>


                            <input
                                id="pan_number"
                                type="text"
                                maxlength="10"
                                placeholder="Enter party PAN Number"
                                class="w-full border
                                       rounded-md px-3 py-3
                                       uppercase">

                        </div>


                        <!-- EMPTY -->

                        <div></div>

                    </div>


                    <hr class="my-7">


                    <!-- PARTY TYPE / CATEGORY -->

                    <div
                        class="grid grid-cols-1 gap-5">


                        <div>

                            <label
                                class="block text-sm
                                       text-gray-500 mb-2">

                                Party Type
                                <span class="text-red-500">*</span>

                            </label>


                            <select
                                id="party_type"
                                class="w-full border
                                       rounded-md px-3 py-3">

                                <option value="customer">
                                    Customer
                                </option>

                                <option value="supplier">
                                    Supplier
                                </option>

                            </select>

                        </div>


                        <!-- <div>

                            <label
                                class="block text-sm
                                       text-gray-500 mb-2">

                                Party Category

                            </label>


                            <input
                                id="party_category_id"
                                type="number"
                                placeholder="Category ID"
                                class="w-full border
                                       rounded-md px-3 py-3">

                        </div> -->

                    </div>

                </div>

            </section>


            <!-- =================================================
                 ADDRESS
            ================================================== -->

            <section
                class="bg-white border rounded-lg">


                <div
                    class="px-5 py-3
                           border-b bg-gray-50">

                    <h2 class="font-medium text-gray-700">

                        Address

                    </h2>

                </div>


                <div class="p-5">


                    <div
                        class="grid grid-cols-2 gap-6">


                        <!-- BILLING -->

                        <div>

                            <label
                                class="block text-sm
                                       text-gray-500 mb-2">

                                Billing Address

                            </label>


                            <textarea
                                id="billing_street"
                                rows="4"
                                placeholder="Enter billing address"
                                class="w-full border
                                       rounded-md px-3 py-3"></textarea>

                        </div>


                        <!-- SHIPPING -->

                        <div>

                            <div
                                class="flex justify-between
                                       items-center mb-2">


                                <label
                                    class="text-sm
                                           text-gray-500">

                                    Shipping Address

                                </label>


                                <label
                                    class="flex items-center
                                           gap-2 text-sm">

                                    <input
                                        id="same_shipping"
                                        type="checkbox"
                                        checked
                                        onchange="copyBillingAddress()">

                                    Same as Billing address

                                </label>

                            </div>


                            <textarea
                                id="shipping_address"
                                rows="4"
                                placeholder="Enter shipping address"
                                class="w-full border
                                       rounded-md px-3 py-3
                                       bg-gray-100"></textarea>

                        </div>

                    </div>


                    <!-- CITY STATE PINCODE -->

                    <div
                        class="grid grid-cols-3 gap-5 mt-5">


                        <div>

                            <label
                                class="block text-sm
                                       text-gray-500 mb-2">

                                City

                            </label>


                            <input
                                id="billing_city"
                                type="text"
                                class="w-full border
                                       rounded-md px-3 py-3">

                        </div>


                        <div>

                            <label
                                class="block text-sm
                                       text-gray-500 mb-2">

                                State

                            </label>


                            <input
                                id="billing_state"
                                type="text"
                                class="w-full border
                                       rounded-md px-3 py-3">

                        </div>


                        <div>

                            <label
                                class="block text-sm
                                       text-gray-500 mb-2">

                                Pincode

                            </label>


                            <input
                                id="billing_pincode"
                                type="text"
                                maxlength="10"
                                class="w-full border
                                       rounded-md px-3 py-3">

                        </div>

                    </div>


                    <hr class="my-7">


                    <!-- CREDIT -->

                    <div
                        class="grid grid-cols-3 gap-5">


                        <div>

                            <label
                                class="block text-sm
                                       text-gray-500 mb-2">

                                Credit Period

                            </label>


                            <div class="flex">

                                <input
                                    id="credit_period_days"
                                    type="number"
                                    value="30"
                                    class="w-full border
                                           rounded-l-md
                                           px-3 py-3">


                                <span
                                    class="border
                                           px-4 py-3
                                           bg-gray-50
                                           rounded-r-md">

                                    Days

                                </span>

                            </div>

                        </div>


                        <div>

                            <label
                                class="block text-sm
                                       text-gray-500 mb-2">

                                Credit Limit

                            </label>


                            <div class="flex">

                                <span
                                    class="border
                                           border-r-0
                                           px-3 py-3
                                           bg-gray-50
                                           rounded-l-md">

                                    ₹

                                </span>


                                <input
                                    id="credit_limit"
                                    type="number"
                                    step="0.01"
                                    value="0"
                                    class="w-full border
                                           rounded-r-md
                                           px-3 py-3">

                            </div>

                        </div>

                    </div>

                </div>

            </section>


            <!-- =================================================
                 CONTACT PERSON
            ================================================== -->

            <section
                class="bg-white border rounded-lg">


                <div
                    class="px-5 py-3
                           border-b bg-gray-50">

                    <h2 class="font-medium text-gray-700">

                        Contact Person Details

                    </h2>

                </div>


                <div class="p-5">


                    <div
                        class="grid grid-cols-3 gap-5">


                        <div>

                            <label
                                class="block text-sm
                                       text-gray-500 mb-2">

                                Contact Person Name

                            </label>


                            <input
                                id="contact_person_name"
                                type="text"
                                placeholder="Ex: Ankit Mishra"
                                class="w-full border
                                       rounded-md px-3 py-3">

                        </div>


                        <div>

                            <label
                                class="block text-sm
                                       text-gray-500 mb-2">

                                Date of Birth

                            </label>


                            <input
                                id="dob"
                                type="date"
                                class="w-full border
                                       rounded-md px-3 py-3">

                        </div>

                    </div>

                </div>

            </section>


            <!-- =================================================
                 PARTY BANK ACCOUNT
            ================================================== -->

            <!-- <section
                class="bg-white border rounded-lg">


                <div
                    class="px-5 py-3
                           border-b bg-gray-50">

                    <h2 class="font-medium text-gray-700">

                        Party Bank Account

                    </h2>

                </div>


                <div
                    class="py-12 text-center
                           text-gray-500">


                    <div class="text-5xl mb-4">
                        🏦
                    </div>


                    <p>
                        Add party bank information
                        to manage transactions
                    </p>


                    <button
                        type="button"
                        class="mt-5 text-indigo-600
                               font-medium">

                        + Add Bank Account

                    </button>

                </div>

            </section>
 -->

            <!-- =================================================
                 CUSTOM FIELD
            ================================================== -->
<!-- 
            <section
                class="bg-white border rounded-lg">


                <div
                    class="px-5 py-3
                           border-b bg-gray-50">

                    <h2 class="font-medium text-gray-700">

                        Custom Field

                    </h2>

                </div>


                <div
                    class="py-10 text-center
                           text-gray-500">


                    <p>
                        Store more information about
                        your parties by adding custom
                        fields from Party Settings
                    </p>


                    <button
                        type="button"
                        class="mt-5 bg-indigo-600
                               text-white
                               px-6 py-3
                               rounded-lg">

                        Add Custom Fields

                    </button>

                </div>

            </section> -->

        </div>

    </main>

</div>


<!-- =========================================================
     JAVASCRIPT
========================================================= -->

<script>

    /* =========================================================
   LOAD BUSINESS INFO
========================================================= */

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
                'Authorization': `Bearer ${token}`
            }
        });

        if (!response.ok) {
            throw new Error(`HTTP ${response.status}`);
        }

        const result = await response.json();

        if (!result.success) {
            throw new Error(
                result.message ||
                'Unable to load business information'
            );
        }

        const business = result.data || {};

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

    /* =========================================================
   CREATE / EDIT MODE
========================================================= */

const pathParts =
    window.location.pathname
        .split('/')
        .filter(Boolean);


const isEditMode =
    pathParts.length >= 3 &&
    pathParts[0] === 'parties' &&
    pathParts[2] === 'edit';


const editPartyId =
    isEditMode
        ? pathParts[1]
        : null;


/* =========================================================
   BACK
========================================================= */

function goBack() {

    window.location.href =
        "{{ route('parties') }}";

}


/* =========================================================
   COPY BILLING → SHIPPING
========================================================= */

function copyBillingAddress() {

    const checkbox =
        document.getElementById(
            'same_shipping'
        );

    const shipping =
        document.getElementById(
            'shipping_address'
        );


    if (checkbox.checked) {

        shipping.value =
            document.getElementById(
                'billing_street'
            ).value;

        shipping.disabled = true;

        shipping.classList.add(
            'bg-gray-100'
        );

    } else {

        shipping.disabled = false;

        shipping.classList.remove(
            'bg-gray-100'
        );

    }

}


/* =========================================================
   KEEP SHIPPING UPDATED
========================================================= */

document
    .getElementById('billing_street')
    .addEventListener(
        'input',
        function () {

            const checkbox =
                document.getElementById(
                    'same_shipping'
                );


            if (checkbox.checked) {

                document.getElementById(
                    'shipping_address'
                ).value =
                    this.value;

            }

        }
    );


/* =========================================================
   GST
========================================================= */

document
    .getElementById('gst_number')
    .addEventListener(
        'input',
        function () {

            this.value =
                this.value
                    .toUpperCase()
                    .replace(/\s/g, '');

        }
    );


/* =========================================================
   PAN
========================================================= */

document
    .getElementById('pan_number')
    .addEventListener(
        'input',
        function () {

            this.value =
                this.value
                    .toUpperCase()
                    .replace(/\s/g, '');

        }
    );


/* =========================================================
   GST DETAILS
========================================================= */

/* =========================================================
   GST DETAILS + AUTO POPULATE PARTY
========================================================= */

async function getGSTDetails() {

    const gstInput = document.getElementById('gst_number');

    const gst = gstInput.value
        .trim()
        .toUpperCase()
        .replace(/\s/g, '');

    // ==========================================
    // VALIDATE GSTIN
    // ==========================================

    if (!gst) {
        alert('Please enter GSTIN first.');
        gstInput.focus();
        return;
    }

    // Basic GSTIN format validation
    const gstRegex =
        /^[0-3][0-9][A-Z]{5}[0-9]{4}[A-Z][1-9A-Z]Z[0-9A-Z]$/;

    if (!gstRegex.test(gst)) {
        alert('Please enter a valid GSTIN.');
        gstInput.focus();
        return;
    }

    // Keep GSTIN uppercase
    gstInput.value = gst;

    const token = localStorage.getItem('token');

    if (!token) {
        window.location.href = '/start-free-billing';
        return;
    }

    // ==========================================
    // GET BUTTON
    // ==========================================

    const button =
        document.querySelector(
            'button[onclick="getGSTDetails()"]'
        );

    const originalText = button
        ? button.innerText
        : 'Get Details';

    if (button) {
        button.disabled = true;
        button.innerText = 'Fetching...';
        button.classList.add('opacity-60', 'cursor-not-allowed');
    }

    try {

        // ==========================================
        // CALL GST VERIFICATION API
        // ==========================================

        const response = await fetch(
            '/api/verify-gstin',
            {
                method: 'POST',

                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'Authorization': `Bearer ${token}`
                },

                body: JSON.stringify({
                    gstin: gst
                })
            }
        );

        const result = await response.json();

        console.log('GST Verification Response:', result);

        // ==========================================
        // API ERROR
        // ==========================================

        if (!response.ok) {

            throw new Error(
                result.message ||
                'Unable to verify GSTIN.'
            );
        }

        // ==========================================
        // GET GST DATA
        // ==========================================

        const data = result.data || {};

        console.log('GST Data:', data);

        // ==========================================
        // SUPPORT DIFFERENT RESPONSE STRUCTURES
        // ==========================================

        /*
         * Different GST APIs return slightly different
         * field names.
         *
         * We check the common GST field names.
         */

        const legalName =
            data.lgnm ||
            data.legal_name ||
            data.legalName ||
            data.legal_name_of_business ||
            data.business_name ||
            data.name ||
            '';

        const tradeName =
            data.tradeNam ||
            data.trade_name ||
            data.tradeName ||
            '';

        const partyName =
            tradeName ||
            legalName ||
            '';

        // ==========================================
        // PAN
        // ==========================================

        /*
         * GSTIN structure contains PAN.
         *
         * Example:
         * 19BEXPG5219M1ZQ
         *
         * PAN:
         * BEXPG5219M
         */

        const pan =
            data.pan ||
            data.pan_number ||
            data.panNumber ||
            (
                gst.length >= 12
                    ? gst.substring(2, 12)
                    : ''
            );

        // ==========================================
        // CONTACT DETAILS
        // ==========================================

        const mobile =
            data.mobile ||
            data.mobile_number ||
            data.mobileNumber ||
            data.contact_number ||
            data.contactNumber ||
            '';

        const email =
            data.email ||
            data.email_id ||
            data.emailId ||
            '';

        // ==========================================
        // ADDRESS
        // ==========================================

        const address =
            data.pradr ||
            data.address ||
            data.registered_address ||
            data.registeredAddress ||
            {};

        // GST address may be inside pradr.addr
        const addressData =
            address.addr ||
            address;

        // ==========================================
        // ADDRESS FIELDS
        // ==========================================

        const buildingName =
            addressData.bnm ||
            addressData.building_name ||
            addressData.buildingName ||
            '';

        const street =
            addressData.st ||
            addressData.street ||
            addressData.street_name ||
            '';

        const location =
            addressData.loc ||
            addressData.location ||
            '';

        const district =
            addressData.dst ||
            addressData.district ||
            '';

        const city =
            addressData.city ||
            addressData.city_name ||
            location ||
            district ||
            '';

        const state =
            addressData.stcd ||
            addressData.state ||
            addressData.state_name ||
            '';

        const pincode =
            addressData.pncd ||
            addressData.pincode ||
            addressData.pin_code ||
            '';

        // ==========================================
        // CREATE FULL BILLING ADDRESS
        // ==========================================

        const addressParts = [
            buildingName,
            street,
            location,
            district
        ].filter(Boolean);

        const fullAddress =
            addressParts.join(', ');

        // ==========================================
        // FILL PARTY NAME
        // ==========================================

        if (partyName) {
            document.getElementById('party_name').value =
                partyName;
        }

        // ==========================================
        // FILL MOBILE
        // ==========================================

        if (mobile) {
            document.getElementById('contact_number').value =
                mobile;
        }

        // ==========================================
        // FILL EMAIL
        // ==========================================

        if (email) {
            document.getElementById('email').value =
                email;
        }

        // ==========================================
        // GSTIN
        // ==========================================

        document.getElementById('gst_number').value =
            gst;

        // ==========================================
        // PAN
        // ==========================================

        if (pan) {
            document.getElementById('pan_number').value =
                pan.toUpperCase();
        }

        // ==========================================
        // BILLING ADDRESS
        // ==========================================

        if (fullAddress) {

            document.getElementById('billing_street').value =
                fullAddress;
        }

        // ==========================================
        // CITY
        // ==========================================

        if (city) {

            document.getElementById('billing_city').value =
                city;
        }

        // ==========================================
        // STATE
        // ==========================================

        if (state) {

            document.getElementById('billing_state').value =
                state;
        }

        // ==========================================
        // PINCODE
        // ==========================================

        if (pincode) {

            document.getElementById('billing_pincode').value =
                pincode;
        }

        // ==========================================
        // SHIPPING ADDRESS
        // ==========================================

        const sameShipping =
            document.getElementById('same_shipping');

        if (sameShipping && sameShipping.checked) {

            copyBillingAddress();
        }

        // ==========================================
        // SUCCESS
        // ==========================================

        alert(
            result.message ||
            'GSTIN verified and party details populated successfully.'
        );

    } catch (error) {

        console.error(
            'GST Details Error:',
            error
        );

        alert(
            error.message ||
            'Unable to verify GSTIN.'
        );

    } finally {

        // ==========================================
        // RESTORE BUTTON
        // ==========================================

        if (button) {

            button.disabled = false;
            button.innerText = originalText;

            button.classList.remove(
                'opacity-60',
                'cursor-not-allowed'
            );
        }
    }
}

// async function getGSTDetails() {

//     const gst =
//         document
//             .getElementById('gst_number')
//             .value
//             .trim()
//             .toUpperCase();


//     if (!gst) {

//         alert(
//             'Please enter GSTIN first.'
//         );

//         return;

//     }


//     const token =
//         localStorage.getItem('token');


//     try {

//         const response =
//             await fetch(
//                 '/api/verify-gstin',
//                 {

//                     method: 'POST',

//                     headers: {

//                         'Content-Type':
//                             'application/json',

//                         'Accept':
//                             'application/json',

//                         'Authorization':
//                             `Bearer ${token}`

//                     },

//                     body: JSON.stringify({

//                         gstin: gst

//                     })

//                 }
//             );


//         const result =
//             await response.json();


//         if (!response.ok) {

//             throw new Error(
//                 result.message ||
//                 'Unable to verify GSTIN'
//             );

//         }


//         /*
//          * We don't assume a fixed third-party
//          * GST response structure here.
//          *
//          * If your verifyGSTIN endpoint returns
//          * business name/address, we can map those
//          * exact fields next.
//          */


//         alert(
//             result.message ||
//             'GSTIN verified successfully.'
//         );


//     } catch (error) {

//         console.error(
//             'GST Error:',
//             error
//         );


//         alert(
//             error.message ||
//             'Unable to verify GSTIN.'
//         );

//     }

// }


/* =========================================================
   SAVE PARTY
========================================================= */

/* =========================================================
   SAVE / UPDATE PARTY
========================================================= */

async function saveParty(saveAndNew = false) {

    const partyName =
        document
            .getElementById('party_name')
            .value
            .trim();


    if (!partyName) {

        alert(
            'Please enter Party Name.'
        );

        document
            .getElementById('party_name')
            .focus();

        return;

    }


    const token =
        localStorage.getItem('token');


    if (!token) {

        window.location.href =
            '/start-free-billing';

        return;

    }


    /* =====================================================
       COLLECT FORM DATA
    ===================================================== */

    const data = {

        party_name:
            partyName,


        contact_number:
            document
                .getElementById('contact_number')
                .value
                .trim(),


        party_type:
            document
                .getElementById('party_type')
                .value,


        gst_number:
            document
                .getElementById('gst_number')
                .value
                .trim()
                .toUpperCase() || null,


        pan_number:
            document
                .getElementById('pan_number')
                .value
                .trim()
                .toUpperCase() || null,


        billing_street:
            document
                .getElementById('billing_street')
                .value
                .trim() || null,


        billing_state:
            document
                .getElementById('billing_state')
                .value
                .trim() || null,


        billing_pincode:
            document
                .getElementById('billing_pincode')
                .value
                .trim() || null,


        billing_city:
            document
                .getElementById('billing_city')
                .value
                .trim() || null,


        opening_balance:
            parseFloat(
                document
                    .getElementById('opening_balance')
                    .value
            ) || 0,


        opening_balance_type:
            document
                .getElementById('opening_balance_type')
                .value,


        credit_period_days:
            parseInt(
                document
                    .getElementById('credit_period_days')
                    .value
            ) || 0,


        credit_limit:
            parseFloat(
                document
                    .getElementById('credit_limit')
                    .value
            ) || 0,


        // party_category_id:
        //     document
        //         .getElementById('party_category_id')
        //         .value || null,


        contact_person_name:
            document
                .getElementById('contact_person_name')
                .value
                .trim() || null,


        dob:
            document
                .getElementById('dob')
                .value || null

    };


    /* =====================================================
       URL + METHOD
    ===================================================== */

    const url =
        isEditMode
            ? `/api/parties/${editPartyId}`
            : '/api/parties';


    const method =
        isEditMode
            ? 'PUT'
            : 'POST';


    try {

        const response =
            await fetch(
                url,
                {

                    method: method,

                    headers: {

                        'Content-Type':
                            'application/json',

                        'Accept':
                            'application/json',

                        'Authorization':
                            `Bearer ${token}`

                    },

                    body:
                        JSON.stringify(data)

                }
            );


        const result =
            await response.json();


        console.log(
            isEditMode
                ? 'Update Party Response:'
                : 'Create Party Response:',
            result
        );


        /* =================================================
           ERROR
        ================================================= */

        if (!response.ok) {

            if (result.errors) {

                const messages =
                    Object.values(
                        result.errors
                    )
                    .flat()
                    .join('\n');


                throw new Error(
                    messages
                );

            }


            throw new Error(
                result.message ||
                (
                    isEditMode
                        ? 'Unable to update party.'
                        : 'Unable to create party.'
                )
            );

        }


        /* =================================================
           SUCCESS
        ================================================= */

        alert(
            result.message ||
            (
                isEditMode
                    ? 'Party updated successfully.'
                    : 'Party created successfully.'
            )
        );


        /* =================================================
           EDIT MODE
        ================================================= */

        if (isEditMode) {

            window.location.href =
                "{{ route('parties') }}";

            return;

        }


        /* =================================================
           CREATE → SAVE & NEW
        ================================================= */

        if (saveAndNew) {

            document
                .getElementById('partyForm')
                .querySelectorAll(
                    'input, textarea'
                )
                .forEach(
                    element => {

                        if (
                            element.type ===
                            'checkbox'
                        ) {

                            element.checked =
                                true;

                        } else {

                            element.value =
                                '';

                        }

                    }
                );


            document
                .getElementById(
                    'opening_balance'
                )
                .value = '0';


            document
                .getElementById(
                    'credit_period_days'
                )
                .value = '30';


            document
                .getElementById(
                    'credit_limit'
                )
                .value = '0';


            document
                .getElementById(
                    'party_type'
                )
                .value = 'customer';


            document
                .getElementById(
                    'opening_balance_type'
                )
                .value = 'receive';


            copyBillingAddress();


        } else {

            window.location.href =
                "{{ route('parties') }}";

        }


    } catch (error) {

        console.error(
            'Save Party Error:',
            error
        );


        alert(
            error.message ||
            'Something went wrong.'
        );

    }

}

/* =========================================================
   LOAD PARTY FOR EDIT
========================================================= */

async function loadPartyForEdit() {

    if (!isEditMode) {
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
                `/api/parties/${editPartyId}`,
                {

                    method: 'GET',

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


        if (!response.ok) {

            throw new Error(
                result.message ||
                'Unable to load party.'
            );

        }


        const party =
            result.data;


        if (!party) {

            throw new Error(
                'Party data not found.'
            );

        }


        /* =================================================
           PAGE TITLE
        ================================================= */

        document
            .getElementById('pageTitle')
            .innerText =
                'Edit Party';


        /* =================================================
           BUTTONS
        ================================================= */

        document
            .getElementById('saveButton')
            .innerText =
                'Save Changes';


        document
            .getElementById('saveNewButton')
            .style.display =
                'none';


        /* =================================================
           PARTY DATA
        ================================================= */

        document
            .getElementById('party_name')
            .value =
                party.party_name || '';


        document
            .getElementById('contact_number')
            .value =
                party.contact_number || '';


        document
            .getElementById('gst_number')
            .value =
                party.gst_number || '';


        document
            .getElementById('pan_number')
            .value =
                party.pan_number || '';


        document
            .getElementById('party_type')
            .value =
                party.party_type || 'customer';


        // document
        //     .getElementById('party_category_id')
        //     .value =
        //         party.party_category_id || '';


        document
            .getElementById('billing_street')
            .value =
                party.billing_street || '';


        document
            .getElementById('billing_city')
            .value =
                party.billing_city || '';


        document
            .getElementById('billing_state')
            .value =
                party.billing_state || '';


        document
            .getElementById('billing_pincode')
            .value =
                party.billing_pincode || '';


        document
            .getElementById('opening_balance')
            .value =
                party.opening_balance ?? 0;


        document
            .getElementById('opening_balance_type')
            .value =
                party.opening_balance_type ||
                'receive';


        document
            .getElementById('credit_period_days')
            .value =
                party.credit_period_days ?? 0;


        document
            .getElementById('credit_limit')
            .value =
                party.credit_limit ?? 0;


        document
            .getElementById('contact_person_name')
            .value =
                party.contact_person_name || '';


        document
            .getElementById('dob')
            .value =
                party.dob || '';


        copyBillingAddress();


    } catch (error) {

        console.error(
            'Load Party Error:',
            error
        );


        alert(
            error.message ||
            'Unable to load party.'
        );


        window.location.href =
            "{{ route('parties') }}";

    }

}


/* =========================================================
   INITIAL SHIPPING STATE
========================================================= */

loadBusinessInfo();

copyBillingAddress();

loadPartyForEdit();

</script>


</body>

</html>