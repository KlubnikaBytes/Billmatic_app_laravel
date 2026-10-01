<!-- <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Business Setup | BillNika</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100"> -->

<!-- 🔐 JWT CHECK -->
<!-- <script>
    if (!localStorage.getItem('token')) {
        window.location.href = '/start-free-billing';
    }
</script> -->

<!-- <div class="min-h-screen flex items-center justify-center p-6"> -->
    <!-- <div class="bg-white w-full max-w-6xl rounded-2xl shadow-lg grid grid-cols-1 md:grid-cols-2 overflow-hidden"> -->

        <!-- LEFT -->
        <!-- <div class="flex flex-col justify-center items-center p-10 text-center">
            <h1 class="text-4xl font-bold mb-4">
                Welcome 🙏 to
            </h1>

            <div class="text-4xl font-bold mb-6">
                <span class="text-orange-500">my</span>BillNika
            </div>

            <p class="text-gray-500 text-sm mb-6">
                Trusted by <strong>1 Crore+</strong> Businesses
            </p>

            <div class="flex gap-4 text-sm text-gray-500">
                <span>🔒 100% Secure</span>
                <span>ISO Certified</span>
            </div>
        </div> -->

        <!-- RIGHT -->
        <!-- <div class="p-10 border-l"> -->

            <!-- STEP 1 -->
            <!-- <div id="step1">
                <h2 class="text-2xl font-semibold mb-6">
                    Let's set up myBillNika for your business
                </h2>

                <label class="block mb-2 font-medium">Your Business Name *</label>
                <input id="business_name"
                       class="w-full border rounded-lg px-4 py-3 mb-4"
                       placeholder="Enter Business Name">

                <label class="block mb-2 font-medium">Which City? *</label>
                <input id="city"
                       class="w-full border rounded-lg px-4 py-3 mb-6"
                       placeholder="Search Cities">

                <label class="block mb-3 font-medium">
                    Select your billing requirement *
                </label>

                <div class="space-y-3 mb-6">
                    <label class="flex items-center gap-3 border p-4 rounded-lg cursor-pointer">
                        <input type="radio" name="billing" value="android" checked>
                        Basic Billing on Android App
                    </label>

                    <label class="flex items-center gap-3 border p-4 rounded-lg cursor-pointer">
                        <input type="radio" name="billing" value="web">
                        Billing, Stock & Collections on Laptop & App
                    </label>
                </div>

                <button onclick="goStep2()"
                        class="bg-indigo-600 text-white px-6 py-3 rounded-lg float-right">
                    Continue
                </button>
            </div> -->

            <!-- STEP 2 -->
            <!-- <div id="step2" class="hidden">
                <h2 class="text-2xl font-semibold mb-6">
                    for your business
                </h2>

                <label class="block mb-2 font-medium">
                    Who are your major customers? *
                </label>

                <div class="space-y-3 mb-6">
                    <label class="flex items-center gap-3 border p-4 rounded-lg">
                        <input type="radio" name="business_type" value="retail" checked>
                        Retail Customers
                    </label>

                    <label class="flex items-center gap-3 border p-4 rounded-lg">
                        <input type="radio" name="business_type" value="wholesale">
                        Distributors / Wholesalers
                    </label>
                </div>

                <label class="block mb-2 font-medium">
                    Which language are you most comfortable in?
                </label>

                <select id="language"
                        class="w-full border rounded-lg px-4 py-3 mb-6">
                    <option>English</option>
                    <option selected>Hindi</option>
                    <option>Tamil</option>
                    <option>Telugu</option>
                    <option>Kannada</option>
                    <option>Malayalam</option>
                </select>

                <div class="flex justify-between">
                    <button onclick="backStep1()"
                            class="border px-6 py-3 rounded-lg">
                        Back
                    </button>

                    <button onclick="saveBusinessDetails()"
                            class="bg-indigo-600 text-white px-6 py-3 rounded-lg">
                        Finish Setup
                    </button>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
function goStep2() {
    document.getElementById('step1').classList.add('hidden');
    document.getElementById('step2').classList.remove('hidden');
}

function backStep1() {
    document.getElementById('step2').classList.add('hidden');
    document.getElementById('step1').classList.remove('hidden');
} -->

<!-- /* ✅ THIS IS WHERE API IS CALLED */ -->
<!-- function saveBusinessDetails() {
    const data = {
        city: document.getElementById('city').value,
        billing_requirement: document.querySelector('input[name="billing"]:checked').value,
        language: document.getElementById('language').value,
        business_type: [
            document.querySelector('input[name="business_type"]:checked').value
        ],
        industry: document.getElementById('business_name').value,
        gst_registered: false
    };

    fetch('/api/business-details', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'Authorization': 'Bearer ' + localStorage.getItem('token')
        },
        body: JSON.stringify(data)
    })
    .then(res => res.json())
    .then(res => {
        if (res.success) {
            window.location.href = '/dashboard';
        } else {
            alert('Failed to save business details');
        }
    });
}
</script>

</body>
</html> -->


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Business Setup | BillNika</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        body {
            font-family: Arial, sans-serif;
        }

        .option-card {
            transition: all 0.2s ease;
        }

        .option-card:hover {
            border-color: #6366f1;
            background: #fafaff;
        }

        .option-card.selected {
            border-color: #4f46e5;
            background: #f5f3ff;
        }

        .industry-item {
            transition: all 0.2s ease;
        }

        .industry-item:hover {
            background: #fafafa;
        }

        .industry-item.selected {
            background: #f5f3ff;
        }

        .format-tab {
            transition: all 0.2s ease;
        }

        .format-tab.active {
            border-color: #4f46e5;
            background: #f5f3ff;
            color: #4338ca;
        }

        .invoice-style {
            transition: all 0.2s ease;
            cursor: pointer;
        }

        .invoice-style:hover {
            border-color: #6366f1;
        }

        .invoice-style.selected {
            border: 3px solid #4f46e5;
            background: #f5f3ff;
        }

        .invoice-preview {
            height: 250px;
            background: white;
            border: 1px solid #d1d5db;
            padding: 15px;
            position: relative;
        }

        .invoice-preview .invoice-title {
            font-size: 16px;
            font-weight: bold;
        }

        .invoice-preview .invoice-line {
            height: 7px;
            background: #e5e7eb;
            margin-top: 7px;
        }

        .invoice-preview .invoice-table {
            margin-top: 20px;
            border: 1px solid #d1d5db;
        }

        .invoice-preview .invoice-row {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            border-bottom: 1px solid #e5e7eb;
        }

        .invoice-preview .invoice-row:last-child {
            border-bottom: none;
        }

        .invoice-preview .invoice-cell {
            height: 18px;
            border-right: 1px solid #e5e7eb;
        }

        .invoice-preview .invoice-cell:last-child {
            border-right: none;
        }

        .style-1 .invoice-title {
            text-align: left;
        }

        .style-2 .invoice-title {
            text-align: center;
        }

        .style-3 .invoice-title {
            text-align: right;
        }

        .style-4 .invoice-preview {
            border-radius: 12px;
        }

        .style-5 .invoice-preview {
            border-top: 8px solid #111827;
        }

        .style-6 .invoice-preview {
            border-left: 8px solid #111827;
        }

        .style-7 .invoice-preview {
            border-bottom: 8px solid #111827;
        }

        .style-8 .invoice-preview {
            background: #f9fafb;
        }

        .style-9 .invoice-preview {
            border: 2px solid #111827;
        }

        .style-10 .invoice-preview {
            border-radius: 20px;
        }
    </style>
</head>

<body class="bg-gray-100">

<!-- JWT CHECK -->
<script>
    if (!localStorage.getItem('token')) {
        window.location.href = '/start-free-billing';
    }
</script>

<div class="min-h-screen flex items-center justify-center p-6">

    <div class="bg-white w-full max-w-6xl rounded-2xl shadow-lg
                grid grid-cols-1 md:grid-cols-2 overflow-hidden">

        <!-- ================= LEFT ================= -->
        <div class="hidden md:flex flex-col justify-center items-center
                    p-10 text-center">

            <h1 class="text-4xl font-bold mb-4">
                Welcome 🙏 to
            </h1>

            <div class="text-4xl font-bold mb-6">
                <span class="text-orange-500">my</span>BillNika
            </div>

            <p class="text-gray-500 text-sm mb-6">
                Trusted by <strong>1 Crore+</strong> Businesses
            </p>

            <div class="flex gap-4 text-sm text-gray-500">
                <span>🔒 100% Secure</span>
                <span>ISO Certified</span>
            </div>

        </div>

        <!-- ================= RIGHT ================= -->
        <div class="p-8 md:p-10 border-l">

            <!-- ================================================= -->
            <!-- STEP 1 : BUSINESS DETAILS -->
            <!-- ================================================= -->
            <div id="step1">

                <h2 class="text-2xl font-semibold mb-6">
                    Let's set up myBillNika for your business
                </h2>

                <label class="block mb-2 font-medium">
                    Your Business Name *
                </label>

                <input
                    id="business_name"
                    type="text"
                    class="w-full border rounded-lg px-4 py-3 mb-4"
                    placeholder="Enter Business Name">

                <label class="block mb-2 font-medium">
                    Which City? *
                </label>

                <input
                    id="city"
                    type="text"
                    class="w-full border rounded-lg px-4 py-3 mb-6"
                    placeholder="Enter your Cities Name">

                <label class="block mb-3 font-medium">
                    Select your billing requirement *
                </label>

                <div class="space-y-3 mb-6">

                    <label class="option-card flex items-center gap-3
                                  border p-4 rounded-lg cursor-pointer">

                        <input
                            type="radio"
                            name="billing"
                            value="android"
                            checked>

                        <span>
                            Basic Billing on Android App
                        </span>

                    </label>

                    <label class="option-card flex items-center gap-3
                                  border p-4 rounded-lg cursor-pointer">

                        <input
                            type="radio"
                            name="billing"
                            value="web">

                        <span>
                            Billing, Stock & Collections on Laptop & App
                        </span>

                    </label>

                    <label class="block mb-2 font-medium">
    Which language are you most comfortable in?
</label>

<select
    id="language"
    class="w-full border rounded-lg px-4 py-3 mb-6">
    
    <option value="English">English</option>
    <option value="Hindi" selected>Hindi</option>
    <option value="Tamil">Tamil</option>
    <option value="Telugu">Telugu</option>
    <option value="Kannada">Kannada</option>
    <option value="Malayalam">Malayalam</option>

</select>

                </div>

                <button
                    onclick="goStep3()"
                    class="bg-indigo-600 text-white px-6 py-3
                           rounded-lg float-right">

                    Continue

                </button>

            </div>


            <!-- ================================================= -->
            <!-- STEP 2 : CUSTOMERS + LANGUAGE -->
            <!-- ================================================= -->
            <!-- <div id="step2" class="hidden">

                <h2 class="text-2xl font-semibold mb-6">
                    for your business
                </h2>

                <label class="block mb-2 font-medium">
                    Who are your major customers? *
                </label>

                <div class="space-y-3 mb-6">

                    <label class="option-card flex items-center gap-3
                                  border p-4 rounded-lg cursor-pointer">

                        <input
                            type="radio"
                            name="business_customer"
                            value="retail"
                            checked>

                        Retail Customers

                    </label>

                    <label class="option-card flex items-center gap-3
                                  border p-4 rounded-lg cursor-pointer">

                        <input
                            type="radio"
                            name="business_customer"
                            value="wholesale">

                        Distributors / Wholesalers

                    </label>

                </div>

                <label class="block mb-2 font-medium">
                    Which language are you most comfortable in?
                </label>

                <select
                    id="language"
                    class="w-full border rounded-lg px-4 py-3 mb-6">

                    <option value="English">English</option>
                    <option value="Hindi" selected>Hindi</option>
                    <option value="Tamil">Tamil</option>
                    <option value="Telugu">Telugu</option>
                    <option value="Kannada">Kannada</option>
                    <option value="Malayalam">Malayalam</option>

                </select>

                <div class="flex justify-between">

                    <button
                        onclick="backStep1()"
                        class="border px-6 py-3 rounded-lg">

                        Back

                    </button>

                    <button
                        onclick="goStep3()"
                        class="bg-indigo-600 text-white px-6 py-3 rounded-lg">

                        Continue

                    </button>

                </div>

            </div> -->


            <!-- ================================================= -->
            <!-- STEP 3 : TYPE OF BUSINESS -->
            <!-- ================================================= -->
            <div id="step3" class="hidden">

                <h2 class="text-2xl font-semibold mb-6">
                    Select Type of Business
                </h2>

                <div class="space-y-4">

                    <label class="option-card flex items-center gap-4
                                  border p-5 rounded-xl cursor-pointer">

                        <input
                            type="checkbox"
                            name="business_category"
                            value="Retailer"
                            class="w-5 h-5">

                        <span class="text-lg font-medium">
                            Retailer
                        </span>

                    </label>

                    <label class="option-card flex items-center gap-4
                                  border p-5 rounded-xl cursor-pointer">

                        <input
                            type="checkbox"
                            name="business_category"
                            value="Wholesaler"
                            class="w-5 h-5">

                        <span class="text-lg font-medium">
                            Wholesaler
                        </span>

                    </label>

                    <label class="option-card flex items-center gap-4
                                  border p-5 rounded-xl cursor-pointer">

                        <input
                            type="checkbox"
                            name="business_category"
                            value="Distributor"
                            class="w-5 h-5">

                        <span class="text-lg font-medium">
                            Distributor
                        </span>

                    </label>

                    <label class="option-card flex items-center gap-4
                                  border p-5 rounded-xl cursor-pointer">

                        <input
                            type="checkbox"
                            name="business_category"
                            value="Manufacturer"
                            class="w-5 h-5">

                        <span class="text-lg font-medium">
                            Manufacturer
                        </span>

                    </label>

                    <label class="option-card flex items-center gap-4
                                  border p-5 rounded-xl cursor-pointer">

                        <input
                            type="checkbox"
                            name="business_category"
                            value="Services"
                            class="w-5 h-5">

                        <span class="text-lg font-medium">
                            Services
                        </span>

                    </label>

                </div>

                <div class="flex justify-between mt-8">

                    <button
                        onclick="backStep1()"
                        class="border px-6 py-3 rounded-lg">

                        Back

                    </button>

                    <button
                        onclick="goStep4()"
                        class="bg-indigo-600 text-white px-6 py-3 rounded-lg">

                        Continue

                    </button>

                </div>

            </div>


            <!-- ================================================= -->
            <!-- STEP 4 : INDUSTRY -->
            <!-- ================================================= -->
            <div id="step4" class="hidden">

                <h2 class="text-2xl font-semibold">
                    Select Industry
                </h2>

                <p class="text-gray-500 mt-2 mb-5">
                    It helps us customize app according to your industry
                </p>

                <input
                    id="industry_search"
                    type="text"
                    onkeyup="searchIndustry()"
                    class="w-full border rounded-lg px-4 py-3 mb-5"
                    placeholder="Search by Industry Type">

             <div id="industryList"
     class="border rounded-xl overflow-hidden">

    <!-- =========================
         POPULAR INDUSTRIES
    ========================== -->
    <div class="bg-gray-100 px-4 py-3 font-bold">
        Popular Industries
    </div>

    <label class="industry-item flex justify-between items-center p-4 border-b cursor-pointer">
        <div>
            <div class="font-semibold text-lg">Electronics</div>
            <div class="text-gray-500 text-sm">
                ACs, TVs, Laptops, Geysers, Washing machines
            </div>
        </div>
        <input type="radio" name="industry" value="Electronics" class="w-5 h-5">
    </label>

    <label class="industry-item flex justify-between items-center p-4 border-b cursor-pointer">
        <div>
            <div class="font-semibold text-lg">FMCG</div>
            <div class="text-gray-500 text-sm">
                Rice, Oils, Dals, Tea, Beverages, Cosmetics
            </div>
        </div>
        <input type="radio" name="industry" value="FMCG" class="w-5 h-5">
    </label>

    <label class="industry-item flex justify-between items-center p-4 border-b cursor-pointer">
        <div>
            <div class="font-semibold text-lg">Garment/Clothing</div>
            <div class="text-gray-500 text-sm">
                T-shirts, Shirts, Pants, Suits, Sarees, Dresses
            </div>
        </div>
        <input type="radio" name="industry" value="Garment/Clothing" class="w-5 h-5">
    </label>

    <label class="industry-item flex justify-between items-center p-4 border-b cursor-pointer">
        <div>
            <div class="font-semibold text-lg">Hardware</div>
            <div class="text-gray-500 text-sm">
                Pipes, PVC tubes, Pumps, Fittings, Bolts
            </div>
        </div>
        <input type="radio" name="industry" value="Hardware" class="w-5 h-5">
    </label>

    <label class="industry-item flex justify-between items-center p-4 border-b cursor-pointer">
        <div>
            <div class="font-semibold text-lg">Mobile and accessories</div>
            <div class="text-gray-500 text-sm">
                Smartphones, Chargers, Earphones
            </div>
        </div>
        <input type="radio" name="industry" value="Mobile and accessories" class="w-5 h-5">
    </label>


    <!-- =========================
         SERVICES
    ========================== -->
    <div class="bg-gray-100 px-4 py-3 font-bold">
        Services
    </div>

    <label class="industry-item flex justify-between items-center p-4 border-b cursor-pointer">
        <div>
            <div class="font-semibold text-lg">Accounting and Financial Services</div>
            <div class="text-gray-500 text-sm">
                GST filing, GST returns, Income tax filing
            </div>
        </div>
        <input type="radio" name="industry" value="Accounting and Financial Services" class="w-5 h-5">
    </label>

    <label class="industry-item flex justify-between items-center p-4 border-b cursor-pointer">
        <div>
            <div class="font-semibold text-lg">Consulting</div>
        </div>
        <input type="radio" name="industry" value="Consulting" class="w-5 h-5">
    </label>

    <label class="industry-item flex justify-between items-center p-4 border-b cursor-pointer">
        <div>
            <div class="font-semibold text-lg">Doctor / Clinic / Hospital</div>
        </div>
        <input type="radio" name="industry" value="Doctor / Clinic / Hospital" class="w-5 h-5">
    </label>

    <label class="industry-item flex justify-between items-center p-4 border-b cursor-pointer">
        <div>
            <div class="font-semibold text-lg">Education-Schooling/Coaching</div>
        </div>
        <input type="radio" name="industry" value="Education-Schooling/Coaching" class="w-5 h-5">
    </label>

    <label class="industry-item flex justify-between items-center p-4 border-b cursor-pointer">
        <div>
            <div class="font-semibold text-lg">Event planning and management</div>
        </div>
        <input type="radio" name="industry" value="Event planning and management" class="w-5 h-5">
    </label>

    <label class="industry-item flex justify-between items-center p-4 border-b cursor-pointer">
        <div>
            <div class="font-semibold text-lg">Fitness - Gym and Spa</div>
            <div class="text-gray-500 text-sm">
                Fitness centres, Yoga studios, Nutrition, Dance studios
            </div>
        </div>
        <input type="radio" name="industry" value="Fitness - Gym and Spa" class="w-5 h-5">
    </label>

    <label class="industry-item flex justify-between items-center p-4 border-b cursor-pointer">
        <div>
            <div class="font-semibold text-lg">Home services</div>
            <div class="text-gray-500 text-sm">
                Plumbing, Painting, Carpentry, Electrician, Cleaning
            </div>
        </div>
        <input type="radio" name="industry" value="Home services" class="w-5 h-5">
    </label>

    <label class="industry-item flex justify-between items-center p-4 border-b cursor-pointer">
        <div>
            <div class="font-semibold text-lg">Hotels and Hospitality</div>
        </div>
        <input type="radio" name="industry" value="Hotels and Hospitality" class="w-5 h-5">
    </label>

    <label class="industry-item flex justify-between items-center p-4 border-b cursor-pointer">
        <div>
            <div class="font-semibold text-lg">Other services</div>
        </div>
        <input type="radio" name="industry" value="Other services" class="w-5 h-5">
    </label>

    <label class="industry-item flex justify-between items-center p-4 border-b cursor-pointer">
        <div>
            <div class="font-semibold text-lg">Photography</div>
            <div class="text-gray-500 text-sm">
                Passport photos, Wedding photos, Photoshoots
            </div>
        </div>
        <input type="radio" name="industry" value="Photography" class="w-5 h-5">
    </label>

    <label class="industry-item flex justify-between items-center p-4 border-b cursor-pointer">
        <div>
            <div class="font-semibold text-lg">Real estate - Rentals and Lease</div>
        </div>
        <input type="radio" name="industry" value="Real estate - Rentals and Lease" class="w-5 h-5">
    </label>

    <label class="industry-item flex justify-between items-center p-4 border-b cursor-pointer">
        <div>
            <div class="font-semibold text-lg">Restaurants/ Cafe/ Catering</div>
        </div>
        <input type="radio" name="industry" value="Restaurants/ Cafe/ Catering" class="w-5 h-5">
    </label>

    <label class="industry-item flex justify-between items-center p-4 border-b cursor-pointer">
        <div>
            <div class="font-semibold text-lg">Salon</div>
            <div class="text-gray-500 text-sm">
                Beauty parlour, Spa
            </div>
        </div>
        <input type="radio" name="industry" value="Salon" class="w-5 h-5">
    </label>

    <label class="industry-item flex justify-between items-center p-4 border-b cursor-pointer">
        <div>
            <div class="font-semibold text-lg">Service Centres</div>
            <div class="text-gray-500 text-sm">
                Auto repairs, Mobile repairs, Laptop repairs, Cycle repairs
            </div>
        </div>
        <input type="radio" name="industry" value="Service Centres" class="w-5 h-5">
    </label>

    <label class="industry-item flex justify-between items-center p-4 border-b cursor-pointer">
        <div>
            <div class="font-semibold text-lg">Tailoring/ Boutique</div>
        </div>
        <input type="radio" name="industry" value="Tailoring/ Boutique" class="w-5 h-5">
    </label>

    <label class="industry-item flex justify-between items-center p-4 border-b cursor-pointer">
        <div>
            <div class="font-semibold text-lg">Tours and Travel</div>
        </div>
        <input type="radio" name="industry" value="Tours and Travel" class="w-5 h-5">
    </label>

    <label class="industry-item flex justify-between items-center p-4 border-b cursor-pointer">
        <div>
            <div class="font-semibold text-lg">Transport and Logistics</div>
        </div>
        <input type="radio" name="industry" value="Transport and Logistics" class="w-5 h-5">
    </label>


    <!-- =========================
         ALL INDUSTRIES
    ========================== -->
    <div class="bg-gray-100 px-4 py-3 font-bold">
        All Industries
    </div>

    <label class="industry-item flex justify-between items-center p-4 border-b cursor-pointer">
        <div>
            <div class="font-semibold text-lg">Agriculture</div>
            <div class="text-gray-500 text-sm">
                Grains, Fertilisers, Seeds, Pesticides, Poultry
            </div>
        </div>
        <input type="radio" name="industry" value="Agriculture" class="w-5 h-5">
    </label>

    <label class="industry-item flex justify-between items-center p-4 border-b cursor-pointer">
        <div>
            <div class="font-semibold text-lg">Automobile</div>
            <div class="text-gray-500 text-sm">
                Tyres, Spare parts, Lubricants, Accessories
            </div>
        </div>
        <input type="radio" name="industry" value="Automobile" class="w-5 h-5">
    </label>

    <label class="industry-item flex justify-between items-center p-4 border-b cursor-pointer">
        <div>
            <div class="font-semibold text-lg">Battery</div>
            <div class="text-gray-500 text-sm">
                Inverter, UPS
            </div>
        </div>
        <input type="radio" name="industry" value="Battery" class="w-5 h-5">
    </label>

    <label class="industry-item flex justify-between items-center p-4 border-b cursor-pointer">
        <div>
            <div class="font-semibold text-lg">Broadband/ cable/ internet</div>
            <div class="text-gray-500 text-sm">
                Settop box, Routers, Cables, Wifi
            </div>
        </div>
        <input type="radio" name="industry" value="Broadband/ cable/ internet" class="w-5 h-5">
    </label>

    <label class="industry-item flex justify-between items-center p-4 border-b cursor-pointer">
        <div>
            <div class="font-semibold text-lg">Building Material and Construction</div>
            <div class="text-gray-500 text-sm">
                Sand, Concrete, Bricks, Cement
            </div>
        </div>
        <input type="radio" name="industry" value="Building Material and Construction" class="w-5 h-5">
    </label>

    <label class="industry-item flex justify-between items-center p-4 border-b cursor-pointer">
        <div>
            <div class="font-semibold text-lg">Cleaning and Pest Control</div>
        </div>
        <input type="radio" name="industry" value="Cleaning and Pest Control" class="w-5 h-5">
    </label>

    <label class="industry-item flex justify-between items-center p-4 border-b cursor-pointer">
        <div>
            <div class="font-semibold text-lg">Dairy (Milk)</div>
            <div class="text-gray-500 text-sm">
                Milk, Ghee, Paneer, Lassi
            </div>
        </div>
        <input type="radio" name="industry" value="Dairy (Milk)" class="w-5 h-5">
    </label>

    <label class="industry-item flex justify-between items-center p-4 border-b cursor-pointer">
        <div>
            <div class="font-semibold text-lg">Electrical works</div>
            <div class="text-gray-500 text-sm">
                Wires, Switches, Fanbox, Bulbs, Coils
            </div>
        </div>
        <input type="radio" name="industry" value="Electrical works" class="w-5 h-5">
    </label>

    <label class="industry-item flex justify-between items-center p-4 border-b cursor-pointer">
        <div>
            <div class="font-semibold text-lg">Engineering</div>
            <div class="text-gray-500 text-sm">
                Rolling shutters, Fabrications, Grills, Gates, Steel
            </div>
        </div>
        <input type="radio" name="industry" value="Engineering" class="w-5 h-5">
    </label>

    <label class="industry-item flex justify-between items-center p-4 border-b cursor-pointer">
        <div>
            <div class="font-semibold text-lg">Footwear</div>
            <div class="text-gray-500 text-sm">
                Sandals, Shoes, Slippers, Boots
            </div>
        </div>
        <input type="radio" name="industry" value="Footwear" class="w-5 h-5">
    </label>

    <label class="industry-item flex justify-between items-center p-4 border-b cursor-pointer">
        <div>
            <div class="font-semibold text-lg">Fruits and Vegetables</div>
        </div>
        <input type="radio" name="industry" value="Fruits and Vegetables" class="w-5 h-5">
    </label>

    <label class="industry-item flex justify-between items-center p-4 border-b cursor-pointer">
        <div>
            <div class="font-semibold text-lg">Furniture</div>
            <div class="text-gray-500 text-sm">
                Mattress, Cupboards, Chair, Sofas, Tables, Beds
            </div>
        </div>
        <input type="radio" name="industry" value="Furniture" class="w-5 h-5">
    </label>

    <label class="industry-item flex justify-between items-center p-4 border-b cursor-pointer">
        <div>
            <div class="font-semibold text-lg">General Store(Kirana)</div>
            <div class="text-gray-500 text-sm">
                Snacks, Household items, Daily needs
            </div>
        </div>
        <input type="radio" name="industry" value="General Store(Kirana)" class="w-5 h-5">
    </label>

    <label class="industry-item flex justify-between items-center p-4 border-b cursor-pointer">
        <div>
            <div class="font-semibold text-lg">Gift Shop</div>
            <div class="text-gray-500 text-sm">
                Gift boxes, Greeting cards, Keychains
            </div>
        </div>
        <input type="radio" name="industry" value="Gift Shop" class="w-5 h-5">
    </label>

    <label class="industry-item flex justify-between items-center p-4 border-b cursor-pointer">
        <div>
            <div class="font-semibold text-lg">Information Technology</div>
            <div class="text-gray-500 text-sm">
                Softwares, Printers, Computers
            </div>
        </div>
        <input type="radio" name="industry" value="Information Technology" class="w-5 h-5">
    </label>

    <label class="industry-item flex justify-between items-center p-4 border-b cursor-pointer">
        <div>
            <div class="font-semibold text-lg">Interiors</div>
            <div class="text-gray-500 text-sm">
                Modular kitchen, Wardrobe interiors, Curtains, Blinds
            </div>
        </div>
        <input type="radio" name="industry" value="Interiors" class="w-5 h-5">
    </label>

    <label class="industry-item flex justify-between items-center p-4 border-b cursor-pointer">
        <div>
            <div class="font-semibold text-lg">Safety Equipments</div>
            <div class="text-gray-500 text-sm">
                Helmets, Belts, Alarms, Extinguishers, Detectors
            </div>
        </div>
        <input type="radio" name="industry" value="Safety Equipments" class="w-5 h-5">
    </label>

    <label class="industry-item flex justify-between items-center p-4 border-b cursor-pointer">
        <div>
            <div class="font-semibold text-lg">Scrap</div>
            <div class="text-gray-500 text-sm">
                Iron scrap, Plastic scrap, Metal scrap
            </div>
        </div>
        <input type="radio" name="industry" value="Scrap" class="w-5 h-5">
    </label>

    <label class="industry-item flex justify-between items-center p-4 border-b cursor-pointer">
        <div>
            <div class="font-semibold text-lg">Sports Equipments</div>
            <div class="text-gray-500 text-sm">
                Bats, Rackets, Tennis balls, Swimwear
            </div>
        </div>
        <input type="radio" name="industry" value="Sports Equipments" class="w-5 h-5">
    </label>

    <label class="industry-item flex justify-between items-center p-4 border-b cursor-pointer">
        <div>
            <div class="font-semibold text-lg">Stationery</div>
            <div class="text-gray-500 text-sm">
                Books, Pencils, Pens, Dusters, Envelopes
            </div>
        </div>
        <input type="radio" name="industry" value="Stationery" class="w-5 h-5">
    </label>

    <label class="industry-item flex justify-between items-center p-4 border-b cursor-pointer">
        <div>
            <div class="font-semibold text-lg">Textiles</div>
            <div class="text-gray-500 text-sm">
                Mats, Yarn, Fabrics, Handlooms, Dyes
            </div>
        </div>
        <input type="radio" name="industry" value="Textiles" class="w-5 h-5">
    </label>

    <label class="industry-item flex justify-between items-center p-4 border-b cursor-pointer">
        <div>
            <div class="font-semibold text-lg">Tiles/Sanitary Ware</div>
            <div class="text-gray-500 text-sm">
                Tiles, Marbles, Ceramics, Sanitary tanks, Wash basins, Fittings
            </div>
        </div>
        <input type="radio" name="industry" value="Tiles/Sanitary Ware" class="w-5 h-5">
    </label>

    <label class="industry-item flex justify-between items-center p-4 border-b cursor-pointer">
        <div>
            <div class="font-semibold text-lg">Utensils</div>
            <div class="text-gray-500 text-sm">
                Cookers, Bowls, Tawa, Spoons, Glasses
            </div>
        </div>
        <input type="radio" name="industry" value="Utensils" class="w-5 h-5">
    </label>

    <label class="industry-item flex justify-between items-center p-4 border-b cursor-pointer">
        <div>
            <div class="font-semibold text-lg">Meat</div>
        </div>
        <input type="radio" name="industry" value="Meat" class="w-5 h-5">
    </label>

    <label class="industry-item flex justify-between items-center p-4 border-b cursor-pointer">
        <div>
            <div class="font-semibold text-lg">Medical Devices</div>
            <div class="text-gray-500 text-sm">
                Medical Equipment, Surgical Instruments, Hospital Supplies
            </div>
        </div>
        <input type="radio" name="industry" value="Medical Devices" class="w-5 h-5">
    </label>

    <label class="industry-item flex justify-between items-center p-4 border-b cursor-pointer">
        <div>
            <div class="font-semibold text-lg">Medicine(Pharma)</div>
            <div class="text-gray-500 text-sm">
                Medicine, Chemist, Biomedical, Medical equipments
            </div>
        </div>
        <input type="radio" name="industry" value="Medicine(Pharma)" class="w-5 h-5">
    </label>

    <label class="industry-item flex justify-between items-center p-4 border-b cursor-pointer">
        <div>
            <div class="font-semibold text-lg">Oil And Gas</div>
        </div>
        <input type="radio" name="industry" value="Oil And Gas" class="w-5 h-5">
    </label>

    <label class="industry-item flex justify-between items-center p-4 border-b cursor-pointer">
        <div>
            <div class="font-semibold text-lg">Opticals</div>
            <div class="text-gray-500 text-sm">
                Glasses, Lenses, Frames, Sunglasses
            </div>
        </div>
        <input type="radio" name="industry" value="Opticals" class="w-5 h-5">
    </label>

    <label class="industry-item flex justify-between items-center p-4 border-b cursor-pointer">
        <div>
            <div class="font-semibold text-lg">Others</div>
        </div>
        <input type="radio" name="industry" value="Others" class="w-5 h-5">
    </label>

    <label class="industry-item flex justify-between items-center p-4 border-b cursor-pointer">
        <div>
            <div class="font-semibold text-lg">Packaging</div>
            <div class="text-gray-500 text-sm">
                Polythene covers, Bubble wraps and Protection sheets
            </div>
        </div>
        <input type="radio" name="industry" value="Packaging" class="w-5 h-5">
    </label>

    <label class="industry-item flex justify-between items-center p-4 cursor-pointer">
        <div>
            <div class="font-semibold text-lg">Paints</div>
            <div class="text-gray-500 text-sm">
                Wall paints, Primer, Putty, Textures
            </div>
        </div>
        <input type="radio" name="industry" value="Paints" class="w-5 h-5">
    </label>

    <label class="industry-item flex justify-between items-center p-4 cursor-pointer">
        <div>
            <div class="font-semibold text-lg">Plywood</div>
        </div>
        <input type="radio" name="industry" value="Plywood" class="w-5 h-5">
    </label>

</div>

                <div class="flex justify-between mt-8">

                    <button
                        onclick="backStep3()"
                        class="border px-6 py-3 rounded-lg">

                        Back

                    </button>

                    <button
                        onclick="goStep5()"
                        class="bg-indigo-600 text-white px-6 py-3 rounded-lg">

                        Continue

                    </button>

                </div>

            </div>


            <!-- ================================================= -->
            <!-- STEP 5 : GST REGISTERED -->
            <!-- ================================================= -->
            <div id="step5" class="hidden">

                <h2 class="text-3xl font-bold leading-tight">
                    Is your Business GST
                    <br>
                    Registered?
                </h2>

                <p class="text-gray-500 mt-4 mb-7">
                    Automatically fill business details by adding gst number
                </p>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                    <label
                        id="gstYesCard"
                        onclick="selectGST(true)"
                        class="option-card flex items-center gap-4
                               border-2 p-6 rounded-xl cursor-pointer">

                        <input
                            type="radio"
                            name="gst_registered"
                            value="yes"
                            class="w-5 h-5">

                        <span class="text-xl">
                            Yes
                        </span>

                    </label>

                    <label
                        id="gstNoCard"
                        onclick="selectGST(false)"
                        class="option-card flex items-center gap-4
                               border-2 p-6 rounded-xl cursor-pointer">

                        <input
                            type="radio"
                            name="gst_registered"
                            value="no"
                            class="w-5 h-5">

                        <span class="text-xl">
                            No
                        </span>

                    </label>

                </div>

                <div class="mt-8 text-indigo-600 font-semibold cursor-pointer">
                    Add Referral Code
                </div>

                <div class="mt-6 text-gray-500">
                    🔒
                    <span class="text-green-600 font-semibold">
                        Your data is safe.
                    </span>
                    We do not share your data.
                </div>

                <div class="flex justify-between mt-6">

                    <button
                        onclick="backStep4()"
                        class="border px-6 py-3 rounded-lg">

                        Back

                    </button>

                    <button
                        onclick="continueGST()"
                        class="bg-indigo-600 text-white px-6 py-3 rounded-lg">

                        Continue

                    </button>

                </div>

            </div>


            <!-- ================================================= -->
            <!-- STEP 6 : GST YES -->
            <!-- ================================================= -->
            <!-- <div id="step6" class="hidden">

                <h2 class="text-3xl font-bold leading-tight">
                    Is your Business GST
                    <br>
                    Registered?
                </h2>

                <p class="text-gray-500 mt-4 mb-7">
                    Automatically fill business details by adding gst number
                </p>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-7">

                    <div class="option-card selected flex items-center gap-4
                                border-2 p-6 rounded-xl">

                        <span class="text-indigo-600 text-2xl">
                            ◉
                        </span>

                        <span class="text-xl">
                            Yes
                        </span>

                    </div>

                    <button
                        onclick="goStep7()"
                        class="option-card flex items-center gap-4
                               border-2 p-6 rounded-xl">

                        <span class="text-gray-500 text-2xl">
                            ○
                        </span>

                        <span class="text-xl">
                            No
                        </span>

                    </button>

                </div>

                <label class="block mb-2 font-medium">
                    GST Number
                </label>

                <input
                    id="gst_number"
                    type="text"
                    maxlength="15"
                    oninput="this.value=this.value.toUpperCase()"
                    class="w-full border rounded-lg px-4 py-4 mb-5"
                    placeholder="Eg: 22AAAAA0000A1Z5">

                <div class="text-indigo-600 font-semibold">
                    Add Referral Code
                </div>

                <div class="mt-5 text-gray-500">
                    🔒
                    <span class="text-green-600 font-semibold">
                        Your data is safe.
                    </span>
                    We do not share your data.
                </div>

                <div class="flex justify-between mt-6">

                    <button
                        onclick="backStep5()"
                        class="border px-6 py-3 rounded-lg">

                        Back

                    </button>

                    <button
                        onclick="goStep8()"
                        class="bg-indigo-600 text-white px-6 py-3 rounded-lg">

                        Continue

                    </button>

                </div>

            </div> -->

            <!-- ================================================= -->
<!-- STEP 6 : GST YES -->
<!-- ================================================= -->
<div id="step6" class="hidden">

    <h2 class="text-3xl font-bold leading-tight">
        Is your Business GST
        <br>
        Registered?
    </h2>

    <p class="text-gray-500 mt-4 mb-7">
        Automatically fill business details by adding gst number
    </p>

    <!-- YES / NO -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-7">

        <div class="option-card selected flex items-center gap-4
                    border-2 p-6 rounded-xl">

            <span class="text-indigo-600 text-2xl">
                ◉
            </span>

            <span class="text-xl">
                Yes
            </span>

        </div>

        <button
            type="button"
            onclick="goStep7()"
            class="option-card flex items-center gap-4
                   border-2 p-6 rounded-xl">

            <span class="text-gray-500 text-2xl">
                ○
            </span>

            <span class="text-xl">
                No
            </span>

        </button>

    </div>

    <!-- GST NUMBER -->
    <label class="block mb-2 font-medium">
        GST Number
    </label>

    <div class="relative">

        <input
            id="gst_number"
            type="text"
            maxlength="15"
            oninput="handleGSTInput(this)"
            class="w-full border rounded-lg px-4 py-4 pr-14 mb-2"
            placeholder="Eg: 22AAAAA0000A1Z5">

        <!-- SUCCESS ICON -->
        <span
            id="gstSuccessIcon"
            class="hidden absolute right-4 top-1/2
                   -translate-y-1/2 text-green-600 text-2xl">
            ✓
        </span>

    </div>

    <!-- GST ERROR -->
    <p
        id="gstError"
        class="hidden text-red-500 text-sm mb-4">
    </p>

    <!-- GST LOADING -->
    <p
        id="gstLoading"
        class="hidden text-gray-500 text-sm mb-4">
        Verifying GSTIN and fetching business details...
    </p>


    <!-- FETCHED BUSINESS DETAILS -->
    <div
        id="gstBusinessDetails"
        class="hidden border rounded-xl p-5 mt-5 mb-6 bg-white shadow-sm">

        <!-- BUSINESS NAME -->
        <div class="mb-5">

            <div class="text-gray-400 text-sm mb-1">
                Business Name
            </div>

            <div class="flex justify-between items-start gap-3">

                <div
                    id="gstBusinessName"
                    class="text-lg font-semibold text-gray-900">
                </div>

                <span class="text-green-600 text-xl">
                    ✓
                </span>

            </div>

        </div>


        <!-- BUSINESS ADDRESS -->
        <div class="mb-5">

            <div class="text-gray-400 text-sm mb-1">
                Business Address
            </div>

            <div class="flex justify-between items-start gap-3">

                <div
                    id="gstBusinessAddress"
                    class="text-base text-gray-900 leading-7">
                </div>

                <span class="text-green-600 text-xl">
                    ✓
                </span>

            </div>

        </div>


        <!-- GST STATUS -->
        <div class="flex items-center gap-2">

            <span class="text-green-600 text-xl">
                ✓
            </span>

            <span class="text-green-600 font-semibold">
                GST Status:
            </span>

            <span
                id="gstStatus"
                class="text-green-600 font-semibold">
            </span>

        </div>

    </div>


    <!-- REFERRAL -->
    <div class="text-indigo-600 font-semibold">
        Add Referral Code
    </div>

    <!-- SAFETY -->
    <div class="mt-5 text-gray-500">
        🔒
        <span class="text-green-600 font-semibold">
            Your data is safe.
        </span>
        We do not share your data.
    </div>


    <!-- BUTTONS -->
    <div class="flex justify-between mt-6">

        <button
            type="button"
            onclick="backStep5()"
            class="border px-6 py-3 rounded-lg">

            Back

        </button>

        <button
            type="button"
            onclick="goStep8()"
            class="bg-indigo-600 text-white px-6 py-3 rounded-lg">

            Continue

        </button>

    </div>

</div>


            <!-- ================================================= -->
            <!-- STEP 7 : GST NO -->
            <!-- ================================================= -->
            <div id="step7" class="hidden">

                <h2 class="text-3xl font-bold leading-tight">
                    Is your Business GST
                    <br>
                    Registered?
                </h2>

                <p class="text-gray-500 mt-4 mb-7">
                    Automatically fill business details by adding gst number
                </p>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-7">

                    <button
                        onclick="goStep6()"
                        class="option-card flex items-center gap-4
                               border-2 p-6 rounded-xl">

                        <span class="text-gray-500 text-2xl">
                            ○
                        </span>

                        <span class="text-xl">
                            Yes
                        </span>

                    </button>

                    <div
                        class="option-card selected flex items-center gap-4
                               border-2 p-6 rounded-xl">

                        <span class="text-indigo-600 text-2xl">
                            ◉
                        </span>

                        <span class="text-xl">
                            No
                        </span>

                    </div>

                </div>

                <label class="block mb-2 font-medium">
                    Business / Personal PAN or Company CIN
                </label>

                <input
                    id="pan_cin"
                    type="text"
                    class="w-full border rounded-lg px-4 py-4 mb-5"
                    placeholder="Enter PAN or CIN">

                <label class="block mb-2 font-medium">
                    Trade License / Udyam Registration Number
                </label>

                <input
                    id="trade_license_udyam"
                    type="text"
                    class="w-full border rounded-lg px-4 py-4 mb-5"
                    placeholder="Enter Trade License or Udyam">

                <div class="text-indigo-600 font-semibold">
                    Add Referral Code
                </div>

                <div class="mt-5 text-gray-500">
                    🔒
                    <span class="text-green-600 font-semibold">
                        Your data is safe.
                    </span>
                    We do not share your data.
                </div>

                <div class="flex justify-between mt-6">

                    <button
                        onclick="backStep5()"
                        class="border px-6 py-3 rounded-lg">

                        Back

                    </button>

                    <button
                        onclick="goStep8()"
                        class="bg-indigo-600 text-white px-6 py-3 rounded-lg">

                        Continue

                    </button>

                </div>

            </div>


            <!-- ================================================= -->
            <!-- STEP 8 : INVOICE FORMAT -->
            <!-- ================================================= -->
            <div id="step8" class="hidden">

                <h2 class="text-3xl font-bold mb-6">
                    Select your Invoice Format
                </h2>

                <!-- FORMAT -->
                <div class="grid grid-cols-3 gap-3 mb-7">

                    <button
                        onclick="selectFormat('A4', this)"
                        class="format-tab active border-2 rounded-xl p-3">

                        <div class="font-semibold">
                            A4 Size
                        </div>

                    </button>

                    <button
                        onclick="selectFormat('A5', this)"
                        class="format-tab border-2 rounded-xl p-3">

                        <div class="font-semibold">
                            A5 Size
                        </div>

                    </button>

                    <button
                        onclick="selectFormat('Thermal', this)"
                        class="format-tab border-2 rounded-xl p-3">

                        <div class="font-semibold">
                            Thermal Printer
                        </div>

                    </button>

                </div>

                <div class="text-center mb-5">

                    <h3 class="text-xl font-bold">
                        Style <span id="styleNumber">1</span>
                    </h3>

                    <p class="text-gray-500">
                        <span id="styleCount">1</span>/10
                    </p>

                </div>

                <!-- INVOICE STYLES -->
                <div
                    id="invoiceStyles"
                    class="grid grid-cols-1 md:grid-cols-2 gap-5">

                    <!-- STYLE 1 -->
                    <div
                        onclick="selectStyle(1, this)"
                        class="invoice-style selected rounded-xl p-3">

                        <div class="invoice-preview style-1">

                            <div class="invoice-title">
                                INVOICE
                            </div>

                            <div class="invoice-line w-1/2"></div>

                            <div class="invoice-table">

                                <div class="invoice-row">
                                    <div class="invoice-cell"></div>
                                    <div class="invoice-cell"></div>
                                    <div class="invoice-cell"></div>
                                </div>

                                <div class="invoice-row">
                                    <div class="invoice-cell"></div>
                                    <div class="invoice-cell"></div>
                                    <div class="invoice-cell"></div>
                                </div>

                                <div class="invoice-row">
                                    <div class="invoice-cell"></div>
                                    <div class="invoice-cell"></div>
                                    <div class="invoice-cell"></div>
                                </div>

                                <div class="invoice-row">
                                    <div class="invoice-cell"></div>
                                    <div class="invoice-cell"></div>
                                    <div class="invoice-cell"></div>
                                </div>

                            </div>

                        </div>

                        <p class="text-center font-semibold mt-3">
                            Style 1
                        </p>

                    </div>


                    <!-- STYLE 2 -->
                    <div
                        onclick="selectStyle(2, this)"
                        class="invoice-style rounded-xl p-3">

                        <div class="invoice-preview style-2">

                            <div class="invoice-title">
                                INVOICE
                            </div>

                            <div class="invoice-line w-full"></div>

                            <div class="invoice-table">

                                <div class="invoice-row">
                                    <div class="invoice-cell"></div>
                                    <div class="invoice-cell"></div>
                                    <div class="invoice-cell"></div>
                                </div>

                                <div class="invoice-row">
                                    <div class="invoice-cell"></div>
                                    <div class="invoice-cell"></div>
                                    <div class="invoice-cell"></div>
                                </div>

                                <div class="invoice-row">
                                    <div class="invoice-cell"></div>
                                    <div class="invoice-cell"></div>
                                    <div class="invoice-cell"></div>
                                </div>

                            </div>

                        </div>

                        <p class="text-center font-semibold mt-3">
                            Style 2
                        </p>

                    </div>


                    <!-- STYLE 3 -->
                    <div
                        onclick="selectStyle(3, this)"
                        class="invoice-style rounded-xl p-3">

                        <div class="invoice-preview style-3">

                            <div class="invoice-title">
                                INVOICE
                            </div>

                            <div class="invoice-line ml-auto w-1/2"></div>

                            <div class="invoice-table">

                                <div class="invoice-row">
                                    <div class="invoice-cell"></div>
                                    <div class="invoice-cell"></div>
                                    <div class="invoice-cell"></div>
                                </div>

                                <div class="invoice-row">
                                    <div class="invoice-cell"></div>
                                    <div class="invoice-cell"></div>
                                    <div class="invoice-cell"></div>
                                </div>

                                <div class="invoice-row">
                                    <div class="invoice-cell"></div>
                                    <div class="invoice-cell"></div>
                                    <div class="invoice-cell"></div>
                                </div>

                            </div>

                        </div>

                        <p class="text-center font-semibold mt-3">
                            Style 3
                        </p>

                    </div>


                    <!-- STYLE 4 -->
                    <div
                        onclick="selectStyle(4, this)"
                        class="invoice-style rounded-xl p-3">

                        <div class="invoice-preview style-4">

                            <div class="invoice-title">
                                INVOICE
                            </div>

                            <div class="invoice-line w-2/3"></div>

                            <div class="invoice-table">

                                <div class="invoice-row">
                                    <div class="invoice-cell"></div>
                                    <div class="invoice-cell"></div>
                                    <div class="invoice-cell"></div>
                                </div>

                                <div class="invoice-row">
                                    <div class="invoice-cell"></div>
                                    <div class="invoice-cell"></div>
                                    <div class="invoice-cell"></div>
                                </div>

                                <div class="invoice-row">
                                    <div class="invoice-cell"></div>
                                    <div class="invoice-cell"></div>
                                    <div class="invoice-cell"></div>
                                </div>

                            </div>

                        </div>

                        <p class="text-center font-semibold mt-3">
                            Style 4
                        </p>

                    </div>


                    <!-- STYLE 5 -->
                    <div
                        onclick="selectStyle(5, this)"
                        class="invoice-style rounded-xl p-3">

                        <div class="invoice-preview style-5">

                            <div class="invoice-title">
                                INVOICE
                            </div>

                            <div class="invoice-line w-1/2"></div>

                            <div class="invoice-table">

                                <div class="invoice-row">
                                    <div class="invoice-cell"></div>
                                    <div class="invoice-cell"></div>
                                    <div class="invoice-cell"></div>
                                </div>

                                <div class="invoice-row">
                                    <div class="invoice-cell"></div>
                                    <div class="invoice-cell"></div>
                                    <div class="invoice-cell"></div>
                                </div>

                            </div>

                        </div>

                        <p class="text-center font-semibold mt-3">
                            Style 5
                        </p>

                    </div>


                    <!-- STYLE 6 -->
                    <div
                        onclick="selectStyle(6, this)"
                        class="invoice-style rounded-xl p-3">

                        <div class="invoice-preview style-6">

                            <div class="invoice-title">
                                INVOICE
                            </div>

                            <div class="invoice-line w-1/2"></div>

                            <div class="invoice-table">

                                <div class="invoice-row">
                                    <div class="invoice-cell"></div>
                                    <div class="invoice-cell"></div>
                                    <div class="invoice-cell"></div>
                                </div>

                                <div class="invoice-row">
                                    <div class="invoice-cell"></div>
                                    <div class="invoice-cell"></div>
                                    <div class="invoice-cell"></div>
                                </div>

                            </div>

                        </div>

                        <p class="text-center font-semibold mt-3">
                            Style 6
                        </p>

                    </div>


                    <!-- STYLE 7 -->
                    <div
                        onclick="selectStyle(7, this)"
                        class="invoice-style rounded-xl p-3">

                        <div class="invoice-preview style-7">

                            <div class="invoice-title">
                                INVOICE
                            </div>

                            <div class="invoice-line w-1/2"></div>

                            <div class="invoice-table">

                                <div class="invoice-row">
                                    <div class="invoice-cell"></div>
                                    <div class="invoice-cell"></div>
                                    <div class="invoice-cell"></div>
                                </div>

                                <div class="invoice-row">
                                    <div class="invoice-cell"></div>
                                    <div class="invoice-cell"></div>
                                    <div class="invoice-cell"></div>
                                </div>

                            </div>

                        </div>

                        <p class="text-center font-semibold mt-3">
                            Style 7
                        </p>

                    </div>


                    <!-- STYLE 8 -->
                    <div
                        onclick="selectStyle(8, this)"
                        class="invoice-style rounded-xl p-3">

                        <div class="invoice-preview style-8">

                            <div class="invoice-title">
                                INVOICE
                            </div>

                            <div class="invoice-line w-1/2"></div>

                            <div class="invoice-table">

                                <div class="invoice-row">
                                    <div class="invoice-cell"></div>
                                    <div class="invoice-cell"></div>
                                    <div class="invoice-cell"></div>
                                </div>

                                <div class="invoice-row">
                                    <div class="invoice-cell"></div>
                                    <div class="invoice-cell"></div>
                                    <div class="invoice-cell"></div>
                                </div>

                            </div>

                        </div>

                        <p class="text-center font-semibold mt-3">
                            Style 8
                        </p>

                    </div>


                    <!-- STYLE 9 -->
                    <div
                        onclick="selectStyle(9, this)"
                        class="invoice-style rounded-xl p-3">

                        <div class="invoice-preview style-9">

                            <div class="invoice-title">
                                INVOICE
                            </div>

                            <div class="invoice-line w-1/2"></div>

                            <div class="invoice-table">

                                <div class="invoice-row">
                                    <div class="invoice-cell"></div>
                                    <div class="invoice-cell"></div>
                                    <div class="invoice-cell"></div>
                                </div>

                                <div class="invoice-row">
                                    <div class="invoice-cell"></div>
                                    <div class="invoice-cell"></div>
                                    <div class="invoice-cell"></div>
                                </div>

                            </div>

                        </div>

                        <p class="text-center font-semibold mt-3">
                            Style 9
                        </p>

                    </div>


                    <!-- STYLE 10 -->
                    <div
                        onclick="selectStyle(10, this)"
                        class="invoice-style rounded-xl p-3">

                        <div class="invoice-preview style-10">

                            <div class="invoice-title">
                                INVOICE
                            </div>

                            <div class="invoice-line w-1/2"></div>

                            <div class="invoice-table">

                                <div class="invoice-row">
                                    <div class="invoice-cell"></div>
                                    <div class="invoice-cell"></div>
                                    <div class="invoice-cell"></div>
                                </div>

                                <div class="invoice-row">
                                    <div class="invoice-cell"></div>
                                    <div class="invoice-cell"></div>
                                    <div class="invoice-cell"></div>
                                </div>

                            </div>

                        </div>

                        <p class="text-center font-semibold mt-3">
                            Style 10
                        </p>

                    </div>

                </div>


                <div class="flex justify-between mt-8">

                    <button
                        onclick="backGSTStep()"
                        class="border px-6 py-3 rounded-lg">

                        Back

                    </button>

                    <button
                        onclick="saveBusinessDetails()"
                        id="finishButton"
                        class="bg-indigo-600 text-white px-6 py-3 rounded-lg">

                        Create bill with this format

                    </button>

                </div>

            </div>

        </div>
    </div>
</div>


<script>

let currentStep = 1;

let gstRegistered = null;

let gstVerified = false;

let gstFetchedBusinessName = '';
let gstFetchedLegalName = '';
let gstFetchedBusinessAddress = '';
let gstFetchedStatus = '';

let selectedInvoiceFormat = 'A4';
let selectedInvoiceStyle = 1;


/* =========================================================
   STEP DISPLAY
========================================================= */

function showStep(step) {

    for (let i = 1; i <= 8; i++) {

        const element = document.getElementById('step' + i);

        if (element) {
            element.classList.add('hidden');
        }
    }

    const activeStep = document.getElementById('step' + step);

    if (activeStep) {
        activeStep.classList.remove('hidden');
    }

    currentStep = step;

    window.scrollTo({
        top: 0,
        behavior: 'smooth'
    });
}


/* =========================================================
   STEP 1
========================================================= */

function goStep3() {

    const businessName =
        document.getElementById('business_name').value.trim();

    const city =
        document.getElementById('city').value.trim();

    const language =
        document.getElementById('language').value;

    if (!businessName) {
        alert('Please enter Business Name');
        return;
    }

    if (!city) {
        alert('Please enter City');
        return;
    }

    if (!language) {
        alert('Please select your language');
        return;
    }

    showStep(3);
}


/* =========================================================
   STEP 2
========================================================= */

// function backStep1() {
//     showStep(1);
// }

// function goStep3() {

//     const customer =
//         document.querySelector(
//             'input[name="business_customer"]:checked'
//         );

//     if (!customer) {
//         alert('Please select your major customers');
//         return;
//     }

//     showStep(3);
// }


/* =========================================================
   STEP 3
========================================================= */

// function backStep2() {
//     showStep(2);
// }

function goStep4() {

    const selected =
        document.querySelectorAll(
            'input[name="business_category"]:checked'
        );

    if (selected.length === 0) {
        alert('Please select at least one type of business');
        return;
    }

    showStep(4);
}


/* =========================================================
   STEP 4
========================================================= */

function backStep3() {
    showStep(3);
}

function goStep5() {

    const industry =
        document.querySelector(
            'input[name="industry"]:checked'
        );

    if (!industry) {
        alert('Please select your industry');
        return;
    }

    showStep(5);
}


/* =========================================================
   INDUSTRY SEARCH
========================================================= */

function searchIndustry() {

    const search =
        document.getElementById('industry_search')
            .value
            .toLowerCase();

    const items =
        document.querySelectorAll('.industry-item');

    items.forEach(item => {

        const text =
            item.innerText.toLowerCase();

        if (text.includes(search)) {
            item.style.display = 'flex';
        } else {
            item.style.display = 'none';
        }

    });
}


/* =========================================================
   STEP 5 GST
========================================================= */

function backStep4() {
    showStep(4);
}

function selectGST(value) {

    gstRegistered = value;

    document
        .getElementById('gstYesCard')
        .classList.remove('selected');

    document
        .getElementById('gstNoCard')
        .classList.remove('selected');

    if (value === true) {

        document
            .getElementById('gstYesCard')
            .classList.add('selected');

        document
            .querySelector(
                'input[name="gst_registered"][value="yes"]'
            )
            .checked = true;

    } else {

        document
            .getElementById('gstNoCard')
            .classList.add('selected');

        document
            .querySelector(
                'input[name="gst_registered"][value="no"]'
            )
            .checked = true;
    }
}

function continueGST() {

    if (gstRegistered === null) {

        const selected =
            document.querySelector(
                'input[name="gst_registered"]:checked'
            );

        if (!selected) {
            alert('Please select Yes or No');
            return;
        }

        gstRegistered =
            selected.value === 'yes';
    }

    if (gstRegistered) {
        showStep(6);
    } else {
        showStep(7);
    }
}


/* =========================================================
   STEP 6 GST YES
========================================================= */

function backStep5() {
    showStep(5);
}

function goStep6() {
    showStep(6);
}

/* =========================================================
   GST INPUT + AUTO VERIFICATION
========================================================= */

function handleGSTInput(input) {

    const gst = input.value
        .toUpperCase()
        .replace(/\s/g, '');

    input.value = gst;

    // Reset if user changes GST
    gstVerified = false;

    document
        .getElementById('gstBusinessDetails')
        .classList.add('hidden');

    document
        .getElementById('gstSuccessIcon')
        .classList.add('hidden');

    document
        .getElementById('gstError')
        .classList.add('hidden');

    document
        .getElementById('gstLoading')
        .classList.add('hidden');


    // Only verify after complete 15-character GSTIN
    if (gst.length === 15) {
        getGSTDetails(gst);
    }
}


/* =========================================================
   FETCH GST DETAILS
========================================================= */

async function getGSTDetails(gst) {

    const token =
        localStorage.getItem('token');

    if (!token) {
        window.location.href =
            '/start-free-billing';

        return;
    }


    const loading =
        document.getElementById('gstLoading');

    const error =
        document.getElementById('gstError');

    const details =
        document.getElementById('gstBusinessDetails');

    const successIcon =
        document.getElementById('gstSuccessIcon');


    loading.classList.remove('hidden');

    error.classList.add('hidden');

    details.classList.add('hidden');

    successIcon.classList.add('hidden');


    try {

        const response =
            await fetch(
                '/api/verify-gstin',
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

                    body: JSON.stringify({
                        gstin: gst
                    })
                }
            );


        const result =
            await response.json();


        if (!response.ok) {

            throw new Error(
                result.message ||
                'Unable to verify GSTIN'
            );
        }


        if (!result.success || !result.data) {

            throw new Error(
                result.message ||
                'GSTIN could not be verified.'
            );
        }


        const data =
            result.data;


        /*
         * SAVE GST DETAILS
         */

        gstFetchedBusinessName =
            data.business_name ||
            data.legal_name ||
            '';

        gstFetchedLegalName =
            data.legal_name ||
            '';

        gstFetchedBusinessAddress =
            data.business_address ||
            '';

        gstFetchedStatus =
            data.gst_status ||
            'Active';


        /*
         * SHOW BUSINESS NAME
         */

        document
            .getElementById('gstBusinessName')
            .innerText =
                gstFetchedBusinessName ||
                'Business name not available';


        /*
         * SHOW BUSINESS ADDRESS
         */

        document
            .getElementById('gstBusinessAddress')
            .innerText =
                gstFetchedBusinessAddress ||
                'Business address not available';


        /*
         * SHOW GST STATUS
         */

        document
            .getElementById('gstStatus')
            .innerText =
                gstFetchedStatus;


        /*
         * SHOW SUCCESS
         */

        details.classList.remove('hidden');

        successIcon.classList.remove('hidden');

        gstVerified = true;


    } catch (error) {

        console.error(
            'GST Verification Error:',
            error
        );

        gstVerified = false;

        error = error || {};

        document
            .getElementById('gstError')
            .innerText =
                error.message ||
                'Unable to verify GSTIN.';

        document
            .getElementById('gstError')
            .classList.remove('hidden');

    } finally {

        loading.classList.add('hidden');
    }
}


/* =========================================================
   STEP 7 GST NO
========================================================= */

function goStep7() {
    showStep(7);
}


/* =========================================================
   STEP 8 INVOICE FORMAT
========================================================= */

function goStep8() {

    if (gstRegistered === true) {

        const gst =
            document
                .getElementById('gst_number')
                .value
                .trim()
                .toUpperCase();


        if (gst.length !== 15) {

            alert(
                'Please enter a valid 15 digit GST Number'
            );

            return;
        }


        if (!gstVerified) {

            alert(
                'Please wait for GST verification to complete.'
            );

            return;
        }


    } else if (gstRegistered === false) {

        const pan =
            document
                .getElementById('pan_cin')
                .value
                .trim();

        const trade =
            document
                .getElementById('trade_license_udyam')
                .value
                .trim();


        if (!pan && !trade) {

            alert(
                'Please enter PAN/CIN or Trade License/Udyam Registration Number'
            );

            return;
        }
    }


    showStep(8);
}


/* =========================================================
   INVOICE FORMAT
========================================================= */

function selectFormat(format, button) {

    selectedInvoiceFormat = format;

    document
        .querySelectorAll('.format-tab')
        .forEach(tab => {
            tab.classList.remove('active');
        });

    button.classList.add('active');
}


/* =========================================================
   INVOICE STYLE
========================================================= */

function selectStyle(style, element) {

    selectedInvoiceStyle = style;

    document
        .querySelectorAll('.invoice-style')
        .forEach(item => {
            item.classList.remove('selected');
        });

    element.classList.add('selected');

    document.getElementById('styleNumber').innerText = style;

    document.getElementById('styleCount').innerText = style;
}


/* =========================================================
   BACK FROM INVOICE FORMAT
========================================================= */

function backGSTStep() {

    if (gstRegistered === true) {
        showStep(6);
    } else {
        showStep(7);
    }
}


/* =========================================================
   SAVE BUSINESS DETAILS
========================================================= */

function saveBusinessDetails() {

    const businessName =
        document.getElementById('business_name')
            .value
            .trim();

    const city =
        document.getElementById('city')
            .value
            .trim();

    const billingRequirement =
        document.querySelector(
            'input[name="billing"]:checked'
        )?.value;

    const customerType =
        document.querySelector(
            'input[name="business_customer"]:checked'
        )?.value;

    const language =
        document.getElementById('language')
            .value;

    const businessCategories =
        Array.from(
            document.querySelectorAll(
                'input[name="business_category"]:checked'
            )
        ).map(input => input.value);

    const industry =
        document.querySelector(
            'input[name="industry"]:checked'
        )?.value;

    const gstNumber =
        document.getElementById('gst_number')
            ?.value
            .trim() || null;

    const panCin =
        document.getElementById('pan_cin')
            ?.value
            .trim() || null;

    const tradeLicense =
        document.getElementById('trade_license_udyam')
            ?.value
            .trim() || null;


    const data = {

    business_name:
        gstRegistered === true &&
        gstFetchedBusinessName
            ? gstFetchedBusinessName
            : businessName,

    business_address:
        gstRegistered === true
            ? gstFetchedBusinessAddress || null
            : null,

    gst_legal_name:
        gstRegistered === true
            ? gstFetchedLegalName || null
            : null,

    gst_status:
        gstRegistered === true
            ? gstFetchedStatus || null
            : null,

    city: city,

    billing_requirement:
        billingRequirement,

    business_type:
        businessCategories,

    language:
        language,

    industry:
        industry,

    gst_registered:
        gstRegistered,

    gst_number:
        gstNumber,

    pan_cin:
        panCin,

    trade_license_udyam:
        tradeLicense,

    invoice_format:
        selectedInvoiceFormat,

    invoice_style:
        selectedInvoiceStyle
};


    const button =
        document.getElementById('finishButton');

    button.disabled = true;

    button.innerText = 'Saving...';


    fetch('/api/business-details', {

        method: 'POST',

        headers: {

            'Content-Type': 'application/json',

            'Accept': 'application/json',

            'Authorization':
                'Bearer ' +
                localStorage.getItem('token')
        },

        body: JSON.stringify(data)

    })

    .then(async response => {

        const result = await response.json();

        if (!response.ok) {

            throw new Error(
                result.message ||
                'Failed to save business details'
            );
        }

        return result;

    })

    .then(result => {

        if (result.success) {

            window.location.href = '/dashboard';

        } else {

            alert(
                result.message ||
                'Failed to save business details'
            );

            button.disabled = false;

            button.innerText =
                'Create bill with this format';
        }

    })

    .catch(error => {

        console.error(error);

        alert(error.message);

        button.disabled = false;

        button.innerText =
            'Create bill with this format';
    });
}

</script>

</body>
</html>
