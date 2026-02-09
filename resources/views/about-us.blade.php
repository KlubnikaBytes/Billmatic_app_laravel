<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <title>About Us – BillNika</title>
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

        <nav class="hidden md:flex items-center gap-8 text-sm">
            <a href="#" class="hover:text-purple-600">Features</a>
            <a href="#" class="hover:text-purple-600">Pricing</a>
        </nav>

        <div class="flex items-center gap-4">
            <a href="{{ route('start.free.billing') }}"
               class="border border-gray-300 px-5 py-2 rounded-full text-sm">
                Start Free Billing
            </a>
            <a href="#" class="bg-gray-900 text-white px-5 py-2 rounded-full text-sm">
                Book Free Demo
            </a>
        </div>
    </div>
</header>

{{-- ================= HERO ================= --}}
<section class="bg-gradient-to-r from-indigo-600 to-purple-700 text-white">
    <div class="max-w-7xl mx-auto px-6 py-20">
        <h1 class="text-5xl font-bold mb-4">About BillNika</h1>
        <p class="max-w-2xl text-lg opacity-90">
            BillNika is a smart GST billing and payment platform built to simplify
            invoicing, compliance, and collections for Indian businesses.
        </p>
    </div>
</section>

{{-- ================= WHO WE ARE ================= --}}
<section class="max-w-7xl mx-auto px-6 py-20">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-16 items-center">

        <div>
            <h2 class="text-3xl font-bold mb-6">Who We Are</h2>
            <p class="text-gray-600 mb-4">
                BillNika is designed for MSMEs, retailers, service providers,
                wholesalers, and professionals who want a fast, accurate,
                and GST-compliant billing solution.
            </p>
            <p class="text-gray-600">
                From generating invoices to sending payment links via Razorpay,
                tracking dues, and managing GST reports — BillNika brings everything
                into one simple platform.
            </p>
        </div>

        <div class="bg-indigo-50 rounded-3xl p-10">
            <ul class="space-y-4 text-gray-700">
                <li>✔ GST-compliant billing & reports</li>
                <li>✔ Online payment links via Razorpay</li>
                <li>✔ WhatsApp & SMS invoice sharing</li>
                <li>✔ Secure cloud-based data storage</li>
            </ul>
        </div>

    </div>
</section>

{{-- ================= MISSION & VISION ================= --}}
<section class="bg-gray-50 py-20">
    <div class="max-w-7xl mx-auto px-6 grid grid-cols-1 md:grid-cols-2 gap-12">

        <div class="bg-white rounded-3xl p-10 shadow-sm">
            <h3 class="text-2xl font-bold mb-4">Our Mission</h3>
            <p class="text-gray-600">
                To empower Indian businesses with simple, affordable, and
                reliable billing software that improves cash flow,
                ensures compliance, and saves time.
            </p>
        </div>

        <div class="bg-white rounded-3xl p-10 shadow-sm">
            <h3 class="text-2xl font-bold mb-4">Our Vision</h3>
            <p class="text-gray-600">
                To become India’s most trusted digital billing ecosystem
                for MSMEs by enabling smarter invoicing and faster payments.
            </p>
        </div>

    </div>
</section>

{{-- ================= TRUST & SECURITY ================= --}}
<section class="max-w-7xl mx-auto px-6 py-20 text-center">
    <h2 class="text-3xl font-bold mb-12">Trusted & Secure</h2>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-8 text-sm text-gray-600">
        <div>🔐 100% Data Privacy</div>
        <div>📄 GST & Audit Compliant</div>
        <div>💳 Razorpay Secure Payments</div>
        <div>⭐ Trusted by Growing Businesses</div>
    </div>
</section>

{{-- ================= CTA ================= --}}
<section class="bg-indigo-600 text-white py-20 text-center">
    <h2 class="text-4xl font-bold mb-6">
        Ready to simplify your billing?
    </h2>
    <div class="flex justify-center gap-6">
        <a href="{{ route('start.free.billing') }}"
           class="bg-white text-indigo-600 px-8 py-4 rounded-full font-medium">
            Start Free Billing
        </a>
        <a href="{{ route('contact.us') }}"
           class="border border-white px-8 py-4 rounded-full font-medium">
            Contact Us
        </a>
    </div>
</section>

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
