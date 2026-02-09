<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Privacy Policy | BillNika</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-50 text-gray-800">

{{-- ================= NAVBAR ================= --}}
<header class="w-full border-b bg-white">
    <div class="max-w-7xl mx-auto flex items-center justify-between px-6 py-4">
        <div class="flex items-center gap-2 font-bold text-lg">
            <span class="text-purple-600">my</span>BillNika
        </div>

        <div class="flex items-center gap-4">
            <a href="{{ route('start.free.billing') }}"
               class="border border-gray-300 px-5 py-2 rounded-full text-sm">
                Start Free Billing
            </a>
        </div>
    </div>
</header>

<div class="max-w-5xl mx-auto px-6 py-16 bg-white shadow rounded-lg">

    <h1 class="text-3xl font-bold mb-6">Privacy Policy</h1>

    <p class="text-sm text-gray-500 mb-8">
        Last updated: {{ date('d M Y') }}
    </p>

    <p class="mb-6">
        BillNika ("we", "our", "us") respects your privacy and is committed to protecting your personal data.
        This Privacy Policy explains how we collect, use, store, and protect your information when you use
        our billing and GST invoicing platform.
    </p>

    <h2 class="text-xl font-semibold mt-8 mb-3">1. Information We Collect</h2>
    <ul class="list-disc pl-6 space-y-2">
        <li>Mobile number and account details</li>
        <li>Business name, GST details, and billing information</li>
        <li>Customer and invoice data entered by you</li>
        <li>Payment-related information processed via Razorpay</li>
        <li>Device, browser, and usage information</li>
    </ul>

    <h2 class="text-xl font-semibold mt-8 mb-3">2. How We Use Your Information</h2>
    <ul class="list-disc pl-6 space-y-2">
        <li>To generate invoices and GST-compliant bills</li>
        <li>To provide payment links and collect payments securely</li>
        <li>To send important service-related notifications</li>
        <li>To improve platform performance and features</li>
        <li>To comply with legal and regulatory requirements</li>
    </ul>

    <h2 class="text-xl font-semibold mt-8 mb-3">3. Payments & Razorpay</h2>
    <p>
        BillNika uses Razorpay to process online payments. We do not store your card or banking details.
        Payment information is securely handled by Razorpay in accordance with their security standards.
    </p>

    <h2 class="text-xl font-semibold mt-8 mb-3">4. Data Security</h2>
    <p>
        We implement industry-standard security measures to protect your data from unauthorized access,
        alteration, or disclosure.
    </p>

    <h2 class="text-xl font-semibold mt-8 mb-3">5. Data Sharing</h2>
    <p>
        We do not sell or rent your personal data. Data is shared only with trusted service providers
        (such as payment gateways) when required to deliver our services.
    </p>

    <h2 class="text-xl font-semibold mt-8 mb-3">6. Your Rights</h2>
    <ul class="list-disc pl-6 space-y-2">
        <li>Access and update your information</li>
        <li>Request data deletion (subject to legal obligations)</li>
        <li>Withdraw marketing communication consent</li>
    </ul>

    <h2 class="text-xl font-semibold mt-8 mb-3">7. Changes to This Policy</h2>
    <p>
        We may update this Privacy Policy from time to time. Updated versions will be posted on this page.
    </p>

    <h2 class="text-xl font-semibold mt-8 mb-3">8. Contact Us</h2>
    <p>
        If you have any questions about this Privacy Policy, contact us at:
        <strong> nguria7@gmail.com</strong>
    </p>

</div>

{{-- ================= FOOTER ================= --}}
<footer class="bg-gray-900 text-gray-400">
    <div class="max-w-7xl mx-auto px-6 py-12 flex flex-col md:flex-row justify-between gap-4 text-sm">
        <p>© 2026 BillNika. Designed by Team KLBNKA</p>
        <div class="flex gap-6">
             <a href="{{ route('cancellation.refund.policy') }}" class="hover:text-white">Cancellation and Refund Policy</a>
            <a href="{{ route('privacy.policy') }}" class="hover:text-white">Privacy Policy</a>
            <a href="{{ route('terms.of.service') }}" class="hover:text-white">Terms of Service</a>
        </div>
    </div>
</footer>


</body>
</html>
