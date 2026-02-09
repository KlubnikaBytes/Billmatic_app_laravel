<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Business Setup | BillNika</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100">

<!-- 🔐 JWT CHECK -->
<script>
    if (!localStorage.getItem('token')) {
        window.location.href = '/start-free-billing';
    }
</script>

<div class="min-h-screen flex items-center justify-center p-6">
    <div class="bg-white w-full max-w-6xl rounded-2xl shadow-lg grid grid-cols-1 md:grid-cols-2 overflow-hidden">

        <!-- LEFT -->
        <div class="flex flex-col justify-center items-center p-10 text-center">
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

        <!-- RIGHT -->
        <div class="p-10 border-l">

            <!-- STEP 1 -->
            <div id="step1">
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
            </div>

            <!-- STEP 2 -->
            <div id="step2" class="hidden">
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
}

/* ✅ THIS IS WHERE API IS CALLED */
function saveBusinessDetails() {
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
</html>
