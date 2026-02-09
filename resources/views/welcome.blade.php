<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <title>{{ config('app.name', 'Bill App') }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

  <script src="https://cdn.tailwindcss.com"></script>

</head>

<body class="bg-white text-gray-800">

{{-- ================= NAVBAR ================= --}}
<header class="w-full border-b">
    <div class="max-w-7xl mx-auto flex items-center justify-between px-6 py-4">
        <div class="flex items-center gap-2 font-bold text-lg">
            <span class="text-purple-600">my</span>BillNika
        </div>

        <nav class="hidden md:flex items-center gap-8 text-sm">
            <a href="#" class="hover:text-purple-600">Pricing</a>
            <a href="#" class="hover:text-purple-600">Features</a>
        </nav>

        <div class="flex items-center gap-4">
            <!-- <a href="#" class="text-sm text-purple-600 font-medium">Start Free Billing</a> -->
                    <a href="{{ route('start.free.billing') }}"
   class="border border-white px-6 py-3 rounded-full font-medium">
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
    <div class="max-w-7xl mx-auto px-6 py-20 text-center">
        <h1 class="text-4xl md:text-5xl font-bold mb-6">
            India's No.1 BillNika Software
        </h1>

        <div class="flex justify-center gap-4 mb-6">
            <a href="#" class="bg-white text-purple-700 px-6 py-3 rounded-full font-medium">
                Book Free Demo
            </a>
                 <a href="{{ route('start.free.billing') }}"
   class="border border-white px-6 py-3 rounded-full font-medium">
    Start Free Billing
</a>
        </div>

        <p class="text-sm opacity-90 mb-6">
            Plans starting from <strong>₹34/month</strong>
        </p>

        <p class="text-sm opacity-90">
            Trusted by more than <strong>1 Crore+</strong> businesses
        </p>

        {{-- Badges --}}
        <div class="flex flex-wrap justify-center gap-6 mt-10 text-xs opacity-90">
            <span>🏆 Best Tech Brands 2025</span>
            <span>⭐ 4.7 Google Play</span>
            <span>🔐 100% Data Privacy</span>
            <span>📄 GST Excellence 2025</span>
        </div>
    </div>
</section>

{{-- ================= STATS ================= --}}
<section class="bg-gray-50 py-12">
    <div class="max-w-7xl mx-auto px-6 grid grid-cols-1 md:grid-cols-3 gap-8 text-center">
        <div class="flex items-center justify-center gap-4">
            <div class="w-14 h-14 bg-yellow-400 rounded-xl flex items-center justify-center text-white text-xl">❤️</div>
            <div class="text-left">
                <p class="font-semibold text-lg">~5 lacs saving</p>
                <p class="text-sm text-gray-600">from billing errors</p>
            </div>
        </div>

        <div class="flex items-center justify-center gap-4">
            <div class="w-14 h-14 bg-blue-500 rounded-xl flex items-center justify-center text-white text-xl">₹</div>
            <div class="text-left">
                <p class="font-semibold text-lg">3x reduction</p>
                <p class="text-sm text-gray-600">in overdue payments</p>
            </div>
        </div>

        <div class="flex items-center justify-center gap-4">
            <div class="w-14 h-14 bg-orange-500 rounded-xl flex items-center justify-center text-white text-xl">🏢</div>
            <div class="text-left">
                <p class="font-semibold text-lg">65% faster</p>
                <p class="text-sm text-gray-600">order processing</p>
            </div>
        </div>
    </div>
</section>

{{-- ================= 3 STEPS ================= --}}
<section class="py-20">
    <div class="max-w-7xl mx-auto px-6">
        <h2 class="text-3xl md:text-4xl font-bold text-center mb-16">
            <!-- Generate GST bills in <span class="text-purple-600">3 simple steps</span> -->
               Generate bills in <span class="text-purple-600">3 simple steps</span>
        </h2>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-12 items-center">

            {{-- Left (Video placeholder) --}}
            <div class="relative">
                <div class="rounded-2xl overflow-hidden shadow-lg">
                    <img src="{{ asset('images/product-tour.jpg') }}"
                         alt="Product Tour"
                         class="w-full">
                </div>

                <button class="absolute inset-0 flex items-center justify-center">
                    <div class="w-16 h-16 bg-orange-500 rounded-full flex items-center justify-center text-white text-xl">
                        ▶
                    </div>
                </button>
            </div>

            {{-- Right (Steps) --}}
            <div class="space-y-10">

                <div class="flex gap-6">
                    <div class="text-4xl font-bold text-purple-300">1</div>
                    <div>
                        <h4 class="font-semibold text-lg mb-1">Select Customer</h4>
                        <p class="text-sm text-gray-600">
                            Select contact or create a new customer with GST, PAN & address details.
                        </p>
                    </div>
                </div>

                <div class="flex gap-6">
                    <div class="text-4xl font-bold text-purple-300">2</div>
                    <div>
                        <h4 class="font-semibold text-lg mb-1">Add Items</h4>
                        <p class="text-sm text-gray-600">
                            Add items with price, discount, tax & category.
                        </p>
                    </div>
                </div>

                <div class="flex gap-6">
                    <div class="text-4xl font-bold text-purple-300">3</div>
                    <div>
                        <h4 class="font-semibold text-lg mb-1">Share Invoice</h4>
                        <p class="text-sm text-gray-600">
                            Print, download or share invoices via WhatsApp/SMS.
                        </p>
                    </div>
                </div>

            </div>
        </div>
    </div>
</section>

{{-- ================= REDUCE OVERDUE PAYMENTS ================= --}}
<section class="bg-white py-24">
    <div class="max-w-7xl mx-auto px-6 text-center">

        <h2 class="text-4xl md:text-5xl font-bold text-gray-800 mb-16">
            Reduce overdue payments by <span class="text-gray-900">3x</span>
        </h2>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-16">

            {{-- Card 1 --}}
            <div class="bg-white rounded-2xl shadow-md p-8 text-left">
                <div class="w-12 h-12 bg-green-100 text-green-700 font-bold flex items-center justify-center rounded-lg mb-6">
                    1
                </div>
                <p class="text-lg text-gray-800">
                    Share error-free Bills quickly on
                    <strong>Whatsapp, Email, SMS or Print</strong>
                </p>
            </div>

            {{-- Card 2 --}}
            <div class="bg-white rounded-2xl shadow-md p-8 text-left">
                <div class="w-12 h-12 bg-green-100 text-green-700 font-bold flex items-center justify-center rounded-lg mb-6">
                    2
                </div>
                <p class="text-lg text-gray-800">
                    Send payment reminders
                    <strong>(automatic or manual)</strong>
                </p>
            </div>

            {{-- Card 3 --}}
            <div class="bg-white rounded-2xl shadow-md p-8 text-left">
                <div class="w-12 h-12 bg-green-100 text-green-700 font-bold flex items-center justify-center rounded-lg mb-6">
                    3
                </div>
                <p class="text-lg text-gray-800">
                    Track party-wise due payments
                    <strong>with accurate reports</strong>
                </p>
            </div>

        </div>

        {{-- CTA --}}
        <div class="flex justify-center gap-6 mb-6">
            <!-- <a href="#" class="bg-indigo-600 text-white px-10 py-4 rounded-full font-medium">
                Start Free Billing
            </a> -->
            <a href="{{ route('start.free.billing') }}"
   class="border border-white px-6 py-3 rounded-full font-medium">
    Start Free Billing
</a>

            <a href="#" class="bg-indigo-500 text-white px-10 py-4 rounded-full font-medium">
                Book Free Demo
            </a>
        </div>

        <p class="text-indigo-600 text-lg font-medium">
            Plans starting from ₹34/month
        </p>

    </div>
</section>

{{-- ================= GOOGLE REVIEWS ================= --}}
<section class="bg-indigo-50 py-20">
    <div class="max-w-4xl mx-auto text-center px-6">

        <div class="flex justify-center items-center gap-3 mb-4">
            <img src="{{ asset('images/google-play.png') }}" class="w-6" alt="">
            <span class="text-lg font-semibold">Google Reviews</span>
        </div>

        <div class="flex justify-center gap-1 mb-2 text-yellow-400 text-xl">
            ★ ★ ★ ★ ★
        </div>

        <p class="text-sm text-gray-600 mb-8">
            1.3L Total Reviews
        </p>

        <blockquote class="text-xl text-gray-800 italic">
            "Superb customer service. They are easily available on phone and Whatsapp."
        </blockquote>

    </div>
</section>

{{-- ================= GST COMPLIANCE ================= --}}
<section class="bg-white py-24">
    <div class="max-w-7xl mx-auto px-6">

        <h2 class="text-4xl md:text-5xl font-bold text-center mb-20">
            Stay 100% GST and audit compliant
        </h2>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-16 items-center">

            {{-- Left Image --}}
            <div class="rounded-3xl overflow-hidden shadow-lg">
                <img src="{{ asset('images/gst-compliance.jpg') }}" alt="GST Compliance">
            </div>

            {{-- Right Features --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-10">

                <div class="flex gap-4">
                    <div class="w-12 h-12 bg-green-100 text-green-600 rounded-xl flex items-center justify-center">
                        📄
                    </div>
                    <p class="text-lg">GST 1, 2 and 3b reports</p>
                </div>

                <div class="flex gap-4">
                    <div class="w-12 h-12 bg-orange-100 text-orange-600 rounded-xl flex items-center justify-center">
                        🏆
                    </div>
                    <p class="text-lg">eWay Bills and B2B e-Invoices</p>
                </div>

                <div class="flex gap-4">
                    <div class="w-12 h-12 bg-blue-100 text-blue-600 rounded-xl flex items-center justify-center">
                        🛒
                    </div>
                    <p class="text-lg">Audit trail</p>
                </div>

                <div class="flex gap-4">
                    <div class="w-12 h-12 bg-pink-100 text-pink-600 rounded-xl flex items-center justify-center">
                        🎁
                    </div>
                    <p class="text-lg">Auto send monthly reports to CA</p>
                </div>

                <div class="flex gap-4">
                    <div class="w-12 h-12 bg-yellow-100 text-yellow-600 rounded-xl flex items-center justify-center">
                        🔧
                    </div>
                    <p class="text-lg">Export JSON to import into GST portal</p>
                </div>

                <div class="flex gap-4">
                    <div class="w-12 h-12 bg-orange-100 text-orange-600 rounded-xl flex items-center justify-center">
                        💳
                    </div>
                    <p class="text-lg">Powered by GST Suvidha Provider</p>
                </div>

            </div>
        </div>
    </div>
</section>

{{-- ================= TESTIMONIALS ================= --}}
<section class="bg-gray-50 py-24">
    <div class="max-w-7xl mx-auto px-6">

        <h2 class="text-4xl md:text-5xl font-bold text-center mb-20">
            Try India’s easiest GST billing platform
            <!-- Try India’s easiest GST billing platform -->
             Try India’s easiest billing platform
        </h2>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">

            {{-- Card 1 --}}
            <div class="bg-white rounded-3xl shadow-md p-8">
                <p class="text-lg mb-8">
                    “myBillNika has helped us to quickly respond for price estimates and close new orders”
                </p>

                <div class="flex items-center gap-4">
                    <img src="{{ asset('images/user1.jpg') }}" class="w-12 h-12 rounded-full">
                    <div>
                        <p class="font-semibold">R V Shetty</p>
                        <p class="text-sm text-gray-500">RGP Stone Suppliers – Vijayawada</p>
                    </div>
                </div>
            </div>

            {{-- Card 2 --}}
            <div class="bg-white rounded-3xl shadow-md p-8">
                <p class="text-lg mb-8">
                    “myBillNika has simplified our GST reconciliation. We are now getting full Input Tax Credit”
                </p>

                <div class="flex items-center gap-4">
                    <img src="{{ asset('images/user2.jpg') }}" class="w-12 h-12 rounded-full">
                    <div>
                        <p class="font-semibold">Mohan Vij</p>
                        <p class="text-sm text-gray-500">SS Electrical Appliances – Noida</p>
                    </div>
                </div>
            </div>

            {{-- Card 3 --}}
            <div class="bg-white rounded-3xl shadow-md p-8">
                <p class="text-lg mb-8">
                    “With myBillNika we have managed to increase festival sales by 35%”
                </p>

                <div class="flex items-center gap-4">
                    <img src="{{ asset('images/user3.jpg') }}" class="w-12 h-12 rounded-full">
                    <div>
                        <p class="font-semibold">Sai Raman</p>
                        <p class="text-sm text-gray-500">Mobile Shoppee – Bangalore</p>
                    </div>
                </div>
            </div>

        </div>

        <div class="text-center mt-16">
            <a href="#" class="bg-indigo-600 text-white px-12 py-4 rounded-full text-lg">
                Start Free Billing
            </a>
        </div>
    </div>
</section>

{{-- ================= PRICING ================= --}}
<section class="bg-gradient-to-r from-indigo-600 to-purple-700 py-28 text-white">
    <div class="max-w-7xl mx-auto px-6">

        <h2 class="text-4xl md:text-5xl font-bold text-center mb-20">
            Pricing Plans
        </h2>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">

            {{-- Diamond --}}
            <div class="bg-white text-gray-800 rounded-3xl p-10">
                <h3 class="text-2xl font-bold text-orange-600 mb-2">Diamond</h3>
                <p class="text-4xl font-bold mb-4">₹217</p>
                <p class="text-sm mb-6">Per month. Billed annually. Excl. GST @18%</p>

                <a href="#" class="block text-center bg-indigo-600 text-white py-3 rounded-full mb-8">
                    Start Free Billing
                </a>

                <ul class="space-y-3">
                    <li>✔ Add up to 1 business + 1 user</li>
                    <li>✔ Unlimited invoices</li>
                    <li>✔ Inventory management</li>
                    <li>✔ Priority customer support</li>
                </ul>
            </div>

            {{-- Platinum --}}
            <div class="bg-white text-gray-800 rounded-3xl p-10">
                <h3 class="text-2xl font-bold text-blue-600 mb-2">Platinum</h3>
                <p class="text-4xl font-bold mb-4">₹250</p>
                <p class="text-sm mb-6">Per month. Billed annually. Excl. GST @18%</p>

                <a href="#" class="block text-center bg-indigo-600 text-white py-3 rounded-full mb-8">
                    Book Free Demo
                </a>

                <ul class="space-y-3">
                    <li>✔ 2 business + 2 users</li>
                    <li>✔ 50 e-Way bills/year</li>
                    <li>✔ Staff attendance & payroll</li>
                    <li>✔ WhatsApp & SMS marketing</li>
                </ul>
            </div>

            {{-- Enterprise --}}
            <div class="bg-white text-gray-800 rounded-3xl p-10">
                <h3 class="text-2xl font-bold text-green-600 mb-2">Enterprise</h3>
                <p class="text-4xl font-bold mb-4">₹417</p>
                <p class="text-sm mb-6">Per month. Billed annually. Excl. GST @18%</p>

                <a href="#" class="block text-center bg-indigo-600 text-white py-3 rounded-full mb-8">
                    Book Free Demo
                </a>

                <ul class="space-y-3">
                    <li>✔ Custom invoice themes</li>
                    <li>✔ Online store</li>
                    <li>✔ Barcode printing</li>
                    <li>✔ Unlimited e-Invoices</li>
                </ul>
            </div>

        </div>
    </div>
</section>

{{-- ================= SILVER PLAN ================= --}}
<section class="bg-white py-24">
    <div class="max-w-7xl mx-auto px-6 grid grid-cols-1 md:grid-cols-2 gap-16 items-center">

        <div>
            <h2 class="text-4xl font-bold mb-6">
                Silver Plan at just <span class="text-indigo-600">₹399</span>/per year
            </h2>

            <ul class="space-y-4 text-lg">
                <li>✔ Android device (1 device)</li>
                <li>✔ 1 business & 1 user</li>
            </ul>
        </div>

        <div class="text-center">
            <img src="{{ asset('images/app-preview.png') }}" class="mx-auto mb-6">
            <img src="{{ asset('images/google-play.png') }}" class="mx-auto w-48">
        </div>

    </div>
</section>

{{-- ================= FOOTER ================= --}}
<footer class="relative bg-gradient-to-br from-black via-black to-red-900 text-gray-300 overflow-hidden">

    {{-- Top Content --}}
    <div class="max-w-7xl mx-auto px-6 py-20 relative z-10">

        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-12">

            {{-- Product --}}
            <div>
                <p class="text-sm uppercase tracking-widest text-gray-400 mb-6">// Product</p>
                <ul class="space-y-3 text-sm">
                    <li><a href="#" class="hover:text-white">What’s New</a></li>
                    <li><a href="#" class="hover:text-white">Design</a></li>
                    <li><a href="#" class="hover:text-white">Collaboration</a></li>
                    <li><a href="#" class="hover:text-white">Prototyping</a></li>
                    <li><a href="#" class="hover:text-white">Developer Handoff</a></li>
                    <li><a href="#" class="hover:text-white">All Features</a></li>
                </ul>
            </div>

            {{-- Support --}}
            <div>
                <p class="text-sm uppercase tracking-widest text-gray-400 mb-6">// Support</p>
                <ul class="space-y-3 text-sm">
                    <li><a href="#" class="hover:text-white">Download and Install</a></li>
                    <li><a href="#" class="hover:text-white">Help Center</a></li>
                    <li><a href="#" class="hover:text-white">Support Community</a></li>
                    <li><a href="#" class="hover:text-white">Enterprise Support</a></li>
                    <li><a href="#" class="hover:text-white">Documentation</a></li>
                    <li><a href="#" class="hover:text-white">Community Forum</a></li>
                </ul>
            </div>

            {{-- Resources --}}
            <div>
                <p class="text-sm uppercase tracking-widest text-gray-400 mb-6">// Resources</p>
                <ul class="space-y-3 text-sm">
                    <li><a href="#" class="hover:text-white">Our Blog</a></li>
                    <li><a href="#" class="hover:text-white">Extensions & Plugins</a></li>
                    <li><a href="#" class="hover:text-white">Pricing</a></li>
                    <li><a href="#" class="hover:text-white">Roadmap</a></li>
                    <li><a href="#" class="hover:text-white">Free for Education</a></li>
                    <li><a href="#" class="hover:text-white">Newsletter</a></li>
                </ul>
            </div>

            {{-- About --}}
            <div>
                <p class="text-sm uppercase tracking-widest text-gray-400 mb-6">// About</p>
                <ul class="space-y-3 text-sm">
                    <li><a href="{{ route('about.us') }}" class="hover:text-white">About Us</a></li>
                    <li><a href="{{ route('contact.us') }}" class="hover:text-white">Contact Us</a></li>
                    <li><a href="#" class="hover:text-white">Events</a></li>
                    <li><a href="#" class="hover:text-white">Partners</a></li>
                    <li><a href="#" class="hover:text-white">Security</a></li>
                </ul>
            </div>

        </div>

        {{-- Divider --}}
        <div class="border-t border-white/10 my-16"></div>

        {{-- Newsletter --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-12 items-center">

            <div>
                <h3 class="text-xl font-semibold text-white mb-3">
                    Never miss an update
                </h3>
                <p class="text-sm text-gray-400 max-w-md">
                    Get all the latest news, blog posts and product updates from BillNika.
                    Delivered directly to your inbox. We’ll rarely send more than one email a month.
                </p>
            </div>

            <div>
                <div class="flex items-center gap-3 mb-3">
                    <input
                        type="email"
                        placeholder="example@gmail.com"
                        class="w-full bg-white/10 text-white placeholder-gray-400 px-4 py-3 rounded-lg focus:outline-none"
                    >
                    <button class="bg-white text-black px-6 py-3 rounded-lg font-medium">
                        Join
                    </button>
                </div>

                <label class="flex items-center gap-2 text-xs text-gray-400">
                    <input type="checkbox" class="accent-white">
                    I agree to receive marketing emails from BillNika
                </label>
            </div>

        </div>

        {{-- Bottom --}}
        <div class="flex flex-col md:flex-row justify-between items-center mt-16 text-xs text-gray-400 gap-4">
            <p>© 2026 BillNika. Designed by Team KLBNKA</p>

            <div class="flex gap-6">
               <a href="{{ route('cancellation.refund.policy') }}" class="hover:text-white">Cancellation and Refund Policy</a>

              <a href="{{ route('privacy.policy') }}" class="hover:text-white">Privacy Policy</a>
              <a href="{{ route('terms.of.service') }}" class="hover:text-white">Terms of Service</a>

            </div>
        </div>

    </div>

    {{-- Decorative Grid --}}
    <div class="absolute inset-0 bg-[radial-gradient(circle_at_bottom,rgba(255,255,255,0.06)_1px,transparent_1px)] bg-[length:18px_18px] opacity-30"></div>

</footer>


<script>
if (!localStorage.getItem('token')) {
    window.location.href = '/start-free-billing';
}
</script>

</body>
</html>
