<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <title>Cancellation & Refund Policy – BillNika</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-white text-gray-800">

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

{{-- ================= HERO ================= --}}
<section class="bg-gray-50 border-b">
    <div class="max-w-5xl mx-auto px-6 py-16">
        <h1 class="text-4xl font-bold mb-4">Cancellation & Refund Policy</h1>
        <p class="text-gray-600">
            This policy explains how cancellations and refunds are handled for BillNika subscriptions.
        </p>
    </div>
</section>

{{-- ================= CONTENT ================= --}}
<section class="max-w-5xl mx-auto px-6 py-16 space-y-12 text-sm leading-relaxed">

    <div>
        <h2 class="text-xl font-semibold mb-3">1. Subscription & Payments</h2>
        <p>
            BillNika is a subscription-based billing software that allows users to generate invoices,
            manage GST compliance, and accept payments using integrated payment gateways such as Razorpay.
            Payments made on BillNika are processed securely via third-party payment providers.
        </p>
    </div>

    <div>
        <h2 class="text-xl font-semibold mb-3">2. Cancellation Policy</h2>
        <p>
            Users may cancel their BillNika subscription at any time from their account dashboard
            or by contacting our support team.
        </p>
        <ul class="list-disc pl-6 mt-3 space-y-2">
            <li>Subscription cancellation will stop future billing cycles.</li>
            <li>Access to paid features will remain active until the end of the current billing period.</li>
            <li>No partial refunds are provided for unused subscription periods.</li>
        </ul>
    </div>

    <div>
        <h2 class="text-xl font-semibold mb-3">3. Refund Policy</h2>
        <p>
            BillNika follows a transparent and fair refund policy:
        </p>
        <ul class="list-disc pl-6 mt-3 space-y-2">
            <li>Subscription fees are generally non-refundable once the service has been activated.</li>
            <li>Refunds may be considered only in cases of duplicate payment or technical error.</li>
            <li>Approved refunds will be processed back to the original payment method.</li>
        </ul>
    </div>

    <div>
        <h2 class="text-xl font-semibold mb-3">4. Refund Processing Time</h2>
        <p>
            If a refund is approved, it will be processed within <strong>7–10 business days</strong>.
            The time taken for the refund to reflect in your account depends on your bank or payment provider.
        </p>
    </div>

    <div>
        <h2 class="text-xl font-semibold mb-3">5. Payment Gateway Charges</h2>
        <p>
            Payment gateway charges (including Razorpay fees, taxes, or bank charges) are non-refundable
            and will be deducted from the refund amount, if applicable.
        </p>
    </div>

    <div>
        <h2 class="text-xl font-semibold mb-3">6. Contact for Refund Queries</h2>
        <p>
            For any questions related to cancellations or refunds, please contact us at:
        </p>
        <p class="mt-2">
            📧 <strong>Email:</strong>  nguria7@gmail.com<br>
            📞 <strong>Phone:</strong> +91 8436558183
        </p>
    </div>

    <div>
        <h2 class="text-xl font-semibold mb-3">7. Policy Updates</h2>
        <p>
            BillNika reserves the right to modify this Cancellation & Refund Policy at any time.
            Any changes will be updated on this page.
        </p>
    </div>

</section>

{{-- ================= FOOTER ================= --}}
<footer class="bg-gray-900 text-gray-400">
    <div class="max-w-7xl mx-auto px-6 py-10 flex flex-col md:flex-row justify-between gap-4 text-sm">
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
