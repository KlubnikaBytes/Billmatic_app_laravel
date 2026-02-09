<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Terms of Service | BillNika</title>
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

    <h1 class="text-3xl font-bold mb-6">Terms of Service</h1>

    <p class="text-sm text-gray-500 mb-8">
        Last updated: {{ date('d M Y') }}
    </p>

    <p class="mb-6">
        By accessing or using BillNika, you agree to be bound by these Terms of Service.
        Please read them carefully.
    </p>

    <h2 class="text-xl font-semibold mt-8 mb-3">1. Service Description</h2>
    <p>
        BillNika provides billing, GST invoicing, payment collection, and business management tools
        for small and medium businesses.
    </p>

    <h2 class="text-xl font-semibold mt-8 mb-3">2. User Responsibilities</h2>
    <ul class="list-disc pl-6 space-y-2">
        <li>You are responsible for the accuracy of data entered</li>
        <li>You must comply with GST and tax regulations</li>
        <li>You must keep your login credentials secure</li>
    </ul>

    <h2 class="text-xl font-semibold mt-8 mb-3">3. Payments & Fees</h2>
    <p>
        Subscription fees, if applicable, are billed as described on our pricing page.
        Payment processing is handled by Razorpay.
    </p>

    <h2 class="text-xl font-semibold mt-8 mb-3">4. Prohibited Use</h2>
    <ul class="list-disc pl-6 space-y-2">
        <li>Illegal or fraudulent activities</li>
        <li>Uploading malicious code</li>
        <li>Misuse of payment systems</li>
    </ul>

    <h2 class="text-xl font-semibold mt-8 mb-3">5. Data & Privacy</h2>
    <p>
        Your use of BillNika is also governed by our Privacy Policy.
    </p>

    <h2 class="text-xl font-semibold mt-8 mb-3">6. Limitation of Liability</h2>
    <p>
        BillNika is not liable for indirect or consequential damages arising from the use of the platform.
    </p>

    <h2 class="text-xl font-semibold mt-8 mb-3">7. Termination</h2>
    <p>
        We may suspend or terminate accounts that violate these terms.
    </p>

    <h2 class="text-xl font-semibold mt-8 mb-3">8. Changes to Terms</h2>
    <p>
        We reserve the right to modify these Terms at any time.
    </p>

    <h2 class="text-xl font-semibold mt-8 mb-3">9. Contact</h2>
    <p>
        For questions regarding these Terms, contact:
        <strong>nguria7@gmail.com</strong>
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
