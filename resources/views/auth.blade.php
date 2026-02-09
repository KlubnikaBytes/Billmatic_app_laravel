<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Start Free Trial</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">

<div class="bg-white w-full max-w-md rounded-2xl shadow-lg p-8" id="authCard">

    <!-- LOGO -->
    <div class="text-center mb-6">
        <span class="text-2xl font-bold text-orange-500">my</span>
        <span class="text-2xl font-bold">BillNika</span>
    </div>

    <!-- TITLE -->
    <h2 class="text-2xl font-bold mb-6 text-center">
        Start your free trial
    </h2>

    <!-- MOBILE STEP -->
    <div id="mobileStep">
        <label class="text-sm text-gray-600">Enter your mobile number</label>

        <div class="flex border rounded-lg mt-2 overflow-hidden">
            <span class="px-3 flex items-center bg-gray-100 text-sm">+91</span>
            <input
                id="mobile"
                type="text"
                maxlength="10"
                class="flex-1 px-4 py-3 focus:outline-none"
                placeholder="Mobile Number">
        </div>

        <button
            onclick="sendOtp()"
            class="w-full bg-indigo-600 text-white py-3 rounded-lg mt-6">
            Get OTP
        </button>
    </div>

    <!-- OTP STEP -->
    <div id="otpStep" class="hidden">
        <label class="text-sm text-gray-600">Enter OTP</label>

        <input
            id="otp"
            type="text"
            maxlength="6"
            class="w-full border rounded-lg px-4 py-3 mt-2 focus:outline-none"
            placeholder="OTP">

        <p id="timer" class="text-sm text-gray-500 mt-2"></p>

        <button
            onclick="verifyOtp()"
            class="w-full bg-indigo-600 text-white py-3 rounded-lg mt-6">
            Login
        </button>
    </div>

    <!-- FOOTER -->
    <div class="flex justify-between items-center text-sm text-gray-500 mt-6 border-t pt-4">
        <span>🔒 100% Secure</span>
        <span>ISO 27001 Certified</span>
    </div>

</div>

<script>
let cooldown = 0;
let timerInterval = null;

// SEND OTP
function sendOtp() {
    const mobile = document.getElementById('mobile').value;

    if (!mobile || mobile.length !== 10) {
        alert('Enter valid 10 digit mobile number');
        return;
    }

    fetch('/api/send-otp', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        body: JSON.stringify({ mobile })
    })
    .then(res => res.json())
    .then(data => {
        if (!data.success) {
            alert(data.message);
            return;
        }

        document.getElementById('mobileStep').classList.add('hidden');
        document.getElementById('otpStep').classList.remove('hidden');

        cooldown = data.cooldown_seconds;
        startTimer();
    })
    .catch(() => alert('Something went wrong'));
}

// OTP TIMER
function startTimer() {
    const timerEl = document.getElementById('timer');

    timerInterval = setInterval(() => {
        if (cooldown <= 0) {
            timerEl.innerText = "You can request another OTP";
            clearInterval(timerInterval);
            return;
        }
        timerEl.innerText = `You can request another OTP in ${cooldown} seconds`;
        cooldown--;
    }, 1000);
}

// VERIFY OTP
function verifyOtp() {
    const mobile = document.getElementById('mobile').value;
    const otp = document.getElementById('otp').value;

    if (!otp || otp.length !== 6) {
        alert('Enter valid OTP');
        return;
    }

    fetch('/api/verify-otp', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        body: JSON.stringify({ mobile, otp })
    })
    .then(res => res.json())
    .then(data => {
        if (!data.success) {
            alert(data.message);
            return;
        }

        // Save JWT
        localStorage.setItem('token', data.token);

        // 🔀 Redirect logic (DO NOT CHANGE)
        if (data.has_business_details) {
            window.location.href = '/dashboard';
        } else {
            window.location.href = '/business-details';
        }
    })
    .catch(() => alert('OTP verification failed'));
}
</script>

</body>
</html>
