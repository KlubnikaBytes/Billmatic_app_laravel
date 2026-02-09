<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <title>Contact Us – BillNika</title>
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
<section class="bg-gray-50">
    <div class="max-w-7xl mx-auto px-6 py-20">
        <h1 class="text-5xl font-bold mb-4">Contact Us</h1>
        <p class="text-gray-600 max-w-2xl">
            We'd love to hear from you. Reach out for support, sales, partnerships,
            GST billing queries or payment-link assistance.
        </p>
    </div>
</section>

{{-- ================= CONTACT INFO ================= --}}
<section class="max-w-7xl mx-auto px-6 py-20">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-12">

        {{-- Phone --}}
        <div class="flex gap-4 items-start">
            <div class="w-12 h-12 bg-purple-100 text-purple-600 rounded-full flex items-center justify-center text-xl">
                📞
            </div>
            <div>
                <h4 class="font-semibold text-lg">Phone / WhatsApp</h4>
                <a href="tel:+917400417400" class="text-purple-600 block mt-1">
                    +91 8436558183
                </a>
                <p class="text-sm text-gray-500 mt-1">Mon–Sat · 9:30 AM – 6:30 PM</p>
            </div>
        </div>

        {{-- Email --}}
        <div class="flex gap-4 items-start">
            <div class="w-12 h-12 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center text-xl">
                ✉️
            </div>
            <div>
                <h4 class="font-semibold text-lg">Email</h4>
                <a href="mailto:support@billnika.com" class="text-purple-600 block mt-1">
                    nguria7@gmail.com
                </a>
                <p class="text-sm text-gray-500 mt-1">We usually respond within 24 hours</p>
            </div>
        </div>

        {{-- Address --}}
        <div class="flex gap-4 items-start">
            <div class="w-12 h-12 bg-green-100 text-green-600 rounded-full flex items-center justify-center text-xl">
                📍
            </div>
            <div>
                <h4 class="font-semibold text-lg">Office Address</h4>
                <p class="text-gray-600 mt-1 text-sm leading-relaxed">
                    BillNika, Gobindapur, Chandrakona, West Bengal 721201
                </p>
            </div>
        </div>

    </div>
</section>

{{-- ================= COLLABORATION ================= --}}
<section class="bg-gray-50 py-24">
    <div class="max-w-7xl mx-auto px-6 grid grid-cols-1 md:grid-cols-2 gap-12">

        {{-- Guest Blogging --}}
        <div class="bg-white rounded-3xl p-10 shadow-sm">
            <h3 class="text-2xl font-bold mb-4">
                Write for Us – Guest Blogging
            </h3>
            <p class="text-gray-600 mb-6">
                Do you write about billing, GST, MSMEs or digital payments?
                Contribute original blogs and get featured on BillNika with backlinks
                and audience exposure.
            </p>
            <a href="#"
               class="inline-block bg-indigo-600 text-white px-8 py-3 rounded-full font-medium">
                Submit Your Pitch
            </a>
        </div>

        {{-- Social Collaboration --}}
        <div class="bg-indigo-50 rounded-3xl p-10">
            <h3 class="text-2xl font-bold mb-4">
                Let’s Collaborate on Social Media
            </h3>
            <p class="text-gray-600 mb-6">
                Creators with a business audience—partner with us for reels,
                shoutouts and sponsored content to empower Indian MSMEs.
            </p>
            <a href="#"
               class="inline-block bg-indigo-600 text-white px-8 py-3 rounded-full font-medium">
                Apply for Collaboration
            </a>
        </div>

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
