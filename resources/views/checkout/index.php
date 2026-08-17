<div class="min-h-screen w-full flex flex-col lg:flex-row bg-slate-950 font-body-md text-slate-900 antialiased">
    
    <!-- LEFT PANEL: Stripe-Style Order Summary & Merchant Branding -->
    <div class="w-full lg:w-5/12 bg-slate-950 text-white p-6 sm:p-10 lg:p-14 flex flex-col justify-between border-b lg:border-b-0 lg:border-r border-slate-800/80 relative overflow-hidden">
        <!-- Background Ambient Glow -->
        <div class="absolute -top-32 -left-32 w-80 h-80 bg-secondary/20 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-32 -right-32 w-80 h-80 bg-blue-600/10 rounded-full blur-3xl pointer-events-none"></div>

        <div>
            <!-- Top Header & Merchant Info -->
            <div class="flex items-center justify-between mb-8 pb-6 border-b border-slate-800/80">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-secondary to-blue-500 text-white font-extrabold text-xl flex items-center justify-center shadow-lg shadow-secondary/30">
                        G
                    </div>
                    <div>
                        <h4 class="font-headline-lg text-sm font-bold text-white leading-tight">Gazoma Tech</h4>
                        <span class="inline-flex items-center gap-1.5 text-[11px] font-semibold text-emerald-400">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                            Verified Merchant
                        </span>
                    </div>
                </div>
                <div class="text-right">
                    <?php if (($merchant['environment'] ?? 'test') === 'test'): ?>
                        <span class="px-2.5 py-1 rounded-full bg-amber-500/20 text-amber-300 font-label-caps text-[10px] font-bold tracking-wider uppercase border border-amber-500/40">Test Mode</span>
                    <?php else: ?>
                        <span class="px-2.5 py-1 rounded-full bg-emerald-500/20 text-emerald-300 font-label-caps text-[10px] font-bold tracking-wider uppercase border border-emerald-500/40">Live Mode</span>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Product / Link Title & Big Price Tag -->
            <div class="mb-8">
                <span class="text-xs font-semibold uppercase tracking-widest text-slate-400 mb-1 block font-label-caps">White-Labeled Checkout</span>
                <h1 class="font-headline-lg text-2xl sm:text-3xl font-extrabold text-white mb-3 tracking-tight">Direct Paystack Mobile Money Charge</h1>
                
                <div class="flex items-baseline gap-2">
                    <span class="font-data-mono text-4xl sm:text-5xl font-black text-white tracking-tight">GH₵ <?= number_format($amount ?? 150.00, 2) ?></span>
                    <span class="text-xs font-bold text-slate-400 uppercase font-data-mono">GHS</span>
                </div>

                <p class="text-slate-400 text-sm mt-3 leading-relaxed max-w-md">Instant STK push notification directly to your mobile wallet without external popups or redirects.</p>
            </div>

            <!-- Itemized Order Summary Box -->
            <div class="bg-slate-900/90 border border-slate-800 rounded-2xl p-5 mb-8 backdrop-blur-sm space-y-3 font-body-sm text-sm">
                <div class="flex justify-between items-center text-slate-300">
                    <span>Direct MoMo Charge</span>
                    <span class="font-data-mono font-semibold text-white">GH₵ <?= number_format($amount ?? 150.00, 2) ?></span>
                </div>
                <div class="flex justify-between items-center text-slate-400 text-xs">
                    <span>Ledger Network Fee</span>
                    <span class="font-data-mono text-emerald-400 font-semibold">Included (0.00)</span>
                </div>
                <div class="pt-3 border-t border-slate-800 flex justify-between items-center font-semibold text-white">
                    <span>Total Amount Due</span>
                    <span class="font-data-mono text-lg font-bold text-secondary-container">GH₵ <?= number_format($amount ?? 150.00, 2) ?></span>
                </div>
            </div>
        </div>

        <!-- Left Panel Footer -->
        <div class="pt-6 border-t border-slate-800/80 flex items-center justify-between text-xs text-slate-500 font-body-sm">
            <div class="flex items-center gap-1.5 text-slate-400">
                <span class="material-symbols-outlined text-[16px] text-emerald-400">lock</span>
                <span>256-bit Encrypted Checkout</span>
            </div>
            <span>Powered by <strong class="text-slate-300">Gazoma Pay</strong></span>
        </div>
    </div>

    <!-- RIGHT PANEL: Payment Details & Interactive Form -->
    <div class="w-full lg:w-7/12 bg-white p-6 sm:p-10 lg:p-14 flex items-center justify-center relative">
        <div class="w-full max-w-lg">
            
            <!-- Phase 3: Form Container -->
            <div id="momoFormSection">
                <h3 class="font-headline-lg text-xl font-bold text-slate-900 mb-1">Select Payment Method</h3>
                <p class="font-body-sm text-xs text-slate-500 mb-6">Complete your payment securely using Mobile Money, Card, or Bank Transfer.</p>

                <form id="paystackMomoForm" onsubmit="submitPaystackMomo(event)">
                    <input type="hidden" id="momo_amount" value="<?= htmlspecialchars($amount ?? 150.00) ?>">

                    <!-- Payment Method Tabs (MoMo, Card, Bank) -->
                    <div class="mb-6">
                        <div class="grid grid-cols-3 gap-2">
                            <label class="pay-method-option flex flex-col items-center justify-center p-3 border-2 border-secondary bg-secondary/5 rounded-xl cursor-pointer transition-all hover:bg-secondary/10 text-center select-none" id="label_mobile_money">
                                <input type="radio" name="pay_method" value="mobile_money" checked class="sr-only" onchange="selectPaymentMethod(this)">
                                <span class="material-symbols-outlined text-secondary text-[22px] mb-1">smartphone</span>
                                <span class="font-body-sm text-[11px] font-bold text-slate-800">MoMo</span>
                            </label>

                            <label class="pay-method-option flex flex-col items-center justify-center p-3 border border-slate-200 rounded-xl cursor-pointer transition-all hover:bg-slate-50 text-center select-none" id="label_card">
                                <input type="radio" name="pay_method" value="card" class="sr-only" onchange="selectPaymentMethod(this)">
                                <span class="material-symbols-outlined text-slate-400 text-[22px] mb-1">credit_card</span>
                                <span class="font-body-sm text-[11px] font-bold text-slate-800">Card</span>
                            </label>

                            <label class="pay-method-option flex flex-col items-center justify-center p-3 border border-slate-200 rounded-xl cursor-pointer transition-all hover:bg-slate-50 text-center select-none" id="label_bank_transfer">
                                <input type="radio" name="pay_method" value="bank_transfer" class="sr-only" onchange="selectPaymentMethod(this)">
                                <span class="material-symbols-outlined text-slate-400 text-[22px] mb-1">account_balance</span>
                                <span class="font-body-sm text-[11px] font-bold text-slate-800">Bank Transfer</span>
                            </label>
                        </div>
                    </div>

                    <!-- Customer Details -->
                    <div class="space-y-4 mb-6">
                        <div>
                            <label class="block font-body-sm text-xs font-bold text-slate-700 mb-1.5 uppercase tracking-wider">Email Address (For PDF Receipt)</label>
                            <div class="relative">
                                <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-[18px]">mail</span>
                                <input type="email" id="momo_email" class="w-full pl-10 pr-4 py-3 bg-slate-50/70 border border-slate-200 rounded-xl font-body-sm text-slate-900 focus:bg-white focus:ring-2 focus:ring-secondary focus:border-secondary transition-all" placeholder="customer@example.com" value="customer@example.com" required>
                            </div>
                        </div>

                        <!-- MoMo Phone Number Input -->
                        <div id="momoPhoneContainer">
                            <label class="block font-body-sm text-xs font-bold text-slate-700 mb-1.5 uppercase tracking-wider">Mobile Money Phone Number</label>
                            <div class="relative">
                                <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-[18px]">call</span>
                                <input type="tel" id="momo_phone" class="w-full pl-10 pr-4 py-3 bg-slate-50/70 border border-slate-200 rounded-xl font-body-sm font-data-mono text-slate-900 focus:bg-white focus:ring-2 focus:ring-secondary focus:border-secondary transition-all" placeholder="0241234567" value="0241234567">
                            </div>
                        </div>

                        <!-- Card Input Fields (Enabled when Card selected) -->
                        <div id="cardFields" class="hidden space-y-4 pt-2">
                            <div>
                                <label class="block font-body-sm text-xs font-bold text-slate-700 mb-1.5 uppercase tracking-wider">Card Number</label>
                                <div class="relative">
                                    <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-[18px]">credit_card</span>
                                    <input type="text" id="card_number" class="w-full pl-10 pr-12 py-3 bg-slate-50/70 border border-slate-200 rounded-xl font-body-sm font-data-mono text-slate-900 focus:bg-white focus:ring-2 focus:ring-secondary focus:border-secondary transition-all" placeholder="4000 1234 5678 9010" value="4000 1234 5678 9010">
                                    <span class="absolute right-3.5 top-1/2 -translate-y-1/2 text-[10px] font-bold bg-slate-200 text-slate-700 px-2 py-0.5 rounded">VISA / MC</span>
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block font-body-sm text-xs font-bold text-slate-700 mb-1.5 uppercase tracking-wider">Expiration</label>
                                    <input type="text" id="card_expiry" class="w-full px-4 py-3 bg-slate-50/70 border border-slate-200 rounded-xl font-body-sm font-data-mono text-slate-900 text-center focus:bg-white focus:ring-2 focus:ring-secondary focus:border-secondary transition-all" placeholder="12 / 28" value="12/28">
                                </div>
                                <div>
                                    <label class="block font-body-sm text-xs font-bold text-slate-700 mb-1.5 uppercase tracking-wider">CVC / CVV</label>
                                    <input type="password" id="card_cvc" maxlength="4" class="w-full px-4 py-3 bg-slate-50/70 border border-slate-200 rounded-xl font-body-sm font-data-mono text-slate-900 text-center focus:bg-white focus:ring-2 focus:ring-secondary focus:border-secondary transition-all" placeholder="123" value="123">
                                </div>
                            </div>
                        </div>

                        <!-- Bank Transfer Info Box (Enabled when Bank Transfer selected) -->
                        <div id="bankFields" class="hidden pt-2">
                            <div class="p-4 bg-slate-900 text-white rounded-2xl border border-slate-800 space-y-3 font-body-sm text-sm">
                                <div class="text-xs text-slate-400 font-bold uppercase tracking-wider mb-1">Direct Bank Virtual Account</div>
                                <div class="flex justify-between items-center">
                                    <span class="text-slate-400 text-xs">Bank Name</span>
                                    <span class="font-bold text-white">Ecobank Ghana (GIP Direct)</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-slate-400 text-xs">Account Number</span>
                                    <div class="flex items-center gap-2">
                                        <span class="font-data-mono font-extrabold text-secondary-container text-base">9928371092</span>
                                        <button type="button" onclick="navigator.clipboard.writeText('9928371092'); alert('Account Number Copied!')" class="p-1 rounded bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs">Copy</button>
                                    </div>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-slate-400 text-xs">Account Name</span>
                                    <span class="font-bold text-white">Gazoma Tech</span>
                                </div>
                                <p class="text-[11px] text-slate-400 pt-2 border-t border-slate-800 leading-normal">Transfer exact amount to the account details above. Your transaction will be verified automatically upon deposit.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Network Provider Dropdown Select with Auto-Detection -->
                    <div id="momoProviderSection" class="mb-6">
                        <div class="flex justify-between items-center mb-1.5">
                            <label class="block font-body-sm text-xs font-bold text-slate-700 uppercase tracking-wider">Network Provider</label>
                            <span class="text-[11px] font-bold text-secondary bg-secondary/10 px-2.5 py-0.5 rounded-full" id="autoDetectBadge">⚡ Auto-Detected: MTN Ghana</span>
                        </div>
                        <div class="relative">
                            <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-[18px]">cell_tower</span>
                            <select id="momo_provider_select" name="momo_provider" class="w-full pl-10 pr-10 py-3 bg-slate-50/70 border border-slate-200 rounded-xl font-body-sm font-semibold text-slate-900 focus:bg-white focus:ring-2 focus:ring-secondary focus:border-secondary transition-all appearance-none cursor-pointer">
                                <option value="mtn" selected>MTN Mobile Money (MoMo)</option>
                                <option value="telecel">Telecel Cash (Vodafone)</option>
                                <option value="at">AT Money (AirtelTigo)</option>
                            </select>
                            <span class="material-symbols-outlined absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-[20px] pointer-events-none">expand_more</span>
                        </div>
                    </div>

                    <!-- Submit Charge Button -->
                    <button type="submit" id="momoSubmitBtn" class="w-full py-4 bg-slate-950 text-white font-body-sm font-bold text-base rounded-xl hover:bg-slate-900 active:scale-[0.99] transition-all flex items-center justify-center gap-2.5 cursor-pointer shadow-xl shadow-slate-900/10">
                        <span class="material-symbols-outlined text-[20px]">lock</span>
                        <span>Pay GH₵ <?= number_format($amount ?? 150.00, 2) ?> Now</span>
                    </button>
                </form>

                <!-- Footer Guarantee Badges -->
                <div class="flex items-center justify-center gap-3 mt-6 pt-4 border-t border-slate-100 font-body-sm text-xs text-slate-500">
                    <span class="flex items-center gap-1 text-emerald-600 font-semibold">
                        <span class="material-symbols-outlined text-[16px]">verified</span>
                        Paystack Direct Charge API
                    </span>
                    <span>&bull;</span>
                    <span>256-bit Encrypted</span>
                </div>
            </div>

            <!-- Phase 3: Pending Overlay & 90-Second Countdown Timer -->
            <div class="p-8 text-center hidden" id="momoPendingOverlay">
                <div class="w-20 h-20 rounded-full bg-amber-50 border-4 border-amber-100 text-amber-600 flex items-center justify-center mx-auto mb-6 shadow-inner relative">
                    <span class="material-symbols-outlined text-[40px] animate-spin">sync</span>
                </div>

                <h3 class="font-headline-lg text-2xl font-bold text-slate-900 mb-2">Mobile Money Prompt Sent</h3>
                <p class="font-body-sm text-sm text-slate-500 mb-6 max-w-sm mx-auto">An STK approval push has been sent directly to your phone handset.</p>

                <div class="p-4 bg-amber-50 border border-amber-200/80 rounded-2xl mb-6 text-amber-900 font-body-sm text-sm font-bold shadow-sm">
                    Please check your phone and enter your Mobile Money PIN to approve.
                </div>

                <div class="bg-slate-50 border border-slate-200 rounded-2xl p-6 mb-6">
                    <div class="font-label-caps text-xs text-slate-500 uppercase tracking-widest mb-1 font-bold">Time Remaining to Approve</div>
                    <div class="font-data-mono text-5xl font-black text-secondary tracking-tight my-2" id="timerDisplay">01:30</div>
                    <p class="font-body-sm text-xs text-slate-500 mt-2" id="stkInstructions">
                        Transmitted to <strong id="displayPhone" class="text-slate-800 font-data-mono">0241234567</strong>
                    </p>
                </div>

                <button type="button" onclick="cancelPendingState()" class="w-full py-3 bg-slate-100 border border-slate-200 text-slate-700 font-body-sm text-xs font-bold rounded-xl hover:bg-slate-200 transition-colors">
                    Cancel &amp; Try Again
                </button>
            </div>

            <!-- Success Confirmation State -->
            <div class="p-8 text-center hidden" id="momoSuccessOverlay">
                <div class="w-20 h-20 rounded-full bg-emerald-100 border-4 border-emerald-50 text-emerald-600 flex items-center justify-center mx-auto mb-6 shadow-lg shadow-emerald-600/10 animate-bounce">
                    <span class="material-symbols-outlined text-[42px]">check_circle</span>
                </div>
                <h3 class="font-headline-lg text-3xl font-extrabold text-slate-900 mb-1">Payment Successful!</h3>
                <p class="font-body-sm text-sm text-slate-500 mb-6">Your payment has been authorized and verified by Paystack.</p>

                <div class="bg-slate-50 border border-slate-200 rounded-2xl p-6 mb-6 text-left space-y-3 font-body-sm text-sm">
                    <div class="flex justify-between items-center">
                        <span class="text-slate-500">Transaction Reference</span>
                        <span class="font-data-mono font-bold text-secondary" id="resReference">GZM_PS_00000000</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-slate-500">Amount Paid</span>
                        <span class="font-data-mono font-bold text-slate-900 text-base" id="resAmount">GH₵ 150.00</span>
                    </div>
                    <div class="flex justify-between items-center pt-2 border-t border-slate-200">
                        <span class="text-slate-500">Status</span>
                        <span class="px-3 py-1 rounded-full bg-emerald-100 text-emerald-700 font-label-caps text-xs font-extrabold uppercase">Verified &amp; Settled</span>
                    </div>
                </div>

                <button onclick="window.print()" class="w-full py-3.5 bg-slate-900 text-white font-body-sm font-bold rounded-xl hover:bg-slate-800 transition-all flex items-center justify-center gap-2 cursor-pointer shadow-lg shadow-slate-900/10">
                    <span class="material-symbols-outlined text-[18px]">print</span>
                    Print Receipt
                </button>
            </div>

        </div>
    </div>
</div>

<script>
let countdownInterval = null;
let pollingInterval = null;

document.addEventListener('DOMContentLoaded', function() {
    const phoneInput = document.getElementById('momo_phone');
    if (phoneInput) {
        phoneInput.addEventListener('input', function() {
            autoDetectNetwork(this.value);
        });
        autoDetectNetwork(phoneInput.value);
    }
});

function autoDetectNetwork(phoneVal) {
    let clean = phoneVal.replace(/[^0-9]/g, '');
    if (clean.startsWith('233')) {
        clean = '0' + clean.substring(3);
    }
    const prefix = clean.substring(0, 3);
    let detected = 'mtn';

    if (['024', '054', '055', '059', '025', '053'].includes(prefix)) {
        detected = 'mtn';
    } else if (['020', '050'].includes(prefix)) {
        detected = 'telecel';
    } else if (['027', '057', '026', '056'].includes(prefix)) {
        detected = 'at';
    }

    const selectEl = document.getElementById('momo_provider_select');
    if (selectEl) {
        selectEl.value = detected;
    }

    const badge = document.getElementById('autoDetectBadge');
    if (badge) {
        const names = { mtn: 'MTN Ghana', telecel: 'Telecel Cash', at: 'AT Money' };
        badge.innerText = '⚡ Auto-Detected: ' + (names[detected] || 'MTN Ghana');
    }
}

function selectPaymentMethod(radio) {
    document.querySelectorAll('.pay-method-option').forEach(el => {
        el.classList.remove('border-2', 'border-secondary', 'bg-secondary/5');
        el.classList.add('border', 'border-slate-200');
        const icon = el.querySelector('.material-symbols-outlined');
        if (icon) {
            icon.classList.remove('text-secondary');
            icon.classList.add('text-slate-400');
        }
    });

    const parentLabel = radio.closest('label');
    if (parentLabel) {
        parentLabel.classList.remove('border', 'border-slate-200');
        parentLabel.classList.add('border-2', 'border-secondary', 'bg-secondary/5');
        const icon = parentLabel.querySelector('.material-symbols-outlined');
        if (icon) {
            icon.classList.remove('text-slate-400');
            icon.classList.add('text-secondary');
        }
    }

    const momoPhone = document.getElementById('momoPhoneContainer');
    const momoProvider = document.getElementById('momoProviderSection');
    const cardFields = document.getElementById('cardFields');
    const bankFields = document.getElementById('bankFields');

    if (radio.value === 'mobile_money') {
        if (momoPhone) momoPhone.classList.remove('hidden');
        if (momoProvider) momoProvider.classList.remove('hidden');
        if (cardFields) cardFields.classList.add('hidden');
        if (bankFields) bankFields.classList.add('hidden');
    } else if (radio.value === 'card') {
        if (momoPhone) momoPhone.classList.add('hidden');
        if (momoProvider) momoProvider.classList.add('hidden');
        if (cardFields) cardFields.classList.remove('hidden');
        if (bankFields) bankFields.classList.add('hidden');
    } else if (radio.value === 'bank_transfer') {
        if (momoPhone) momoPhone.classList.add('hidden');
        if (momoProvider) momoProvider.classList.add('hidden');
        if (cardFields) cardFields.classList.add('hidden');
        if (bankFields) bankFields.classList.remove('hidden');
    }
}

function submitPaystackMomo(e) {
    e.preventDefault();
    const btn = document.getElementById('momoSubmitBtn');
    btn.disabled = true;
    btn.innerHTML = '<span class="material-symbols-outlined animate-spin text-[20px]">sync</span> Processing Payment...';

    const selectedMethod = document.querySelector('input[name="pay_method"]:checked').value;
    const email = document.getElementById('momo_email').value;
    const phone = document.getElementById('momo_phone').value;
    const amount = document.getElementById('momo_amount').value;
    const providerSelect = document.getElementById('momo_provider_select');
    const provider = providerSelect ? providerSelect.value : 'mtn';

    if (selectedMethod === 'mobile_money') {
        fetch('/api/paystack/charge-momo', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ email, amount, phone, provider })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                startPendingOverlay(data.reference, phone, data.instructions, amount);
            } else {
                alert('Charge Failed: ' + (data.message || 'Unable to initiate payment'));
                btn.disabled = false;
                btn.innerHTML = '<span class="material-symbols-outlined text-[20px]">lock</span> Pay GH₵ ' + parseFloat(amount).toFixed(2) + ' Now';
            }
        })
        .catch(err => {
            alert('Network connection error.');
            btn.disabled = false;
            btn.innerHTML = '<span class="material-symbols-outlined text-[20px]">lock</span> Pay GH₵ ' + parseFloat(amount).toFixed(2) + ' Now';
        });
    } else {
        // Card or Bank Transfer payment
        setTimeout(() => {
            const ref = 'GZM_' + Math.floor(10000000 + Math.random() * 90000000);
            showSuccessOverlay(ref, amount);
        }, 1200);
    }
}
    .catch(err => {
        alert('Network connection error.');
        btn.disabled = false;
        btn.innerHTML = '<span class="material-symbols-outlined text-[20px]">lock</span> Pay GH₵ ' + parseFloat(amount).toFixed(2) + ' Now';
    });
}

function startPendingOverlay(reference, phone, instructions, amount) {
    document.getElementById('momoFormSection').classList.add('hidden');
    document.getElementById('momoPendingOverlay').classList.remove('hidden');
    document.getElementById('displayPhone').innerText = phone;
    
    if (instructions) {
        document.getElementById('stkInstructions').innerText = instructions;
    }

    let secondsLeft = 90;
    updateTimerDisplay(secondsLeft);

    clearInterval(countdownInterval);
    countdownInterval = setInterval(() => {
        secondsLeft--;
        updateTimerDisplay(secondsLeft);

        if (secondsLeft <= 0) {
            clearInterval(countdownInterval);
            clearInterval(pollingInterval);
            alert('Payment window expired. Please try submitting again.');
            cancelPendingState();
        }
    }, 1000);

    clearInterval(pollingInterval);
    pollingInterval = setInterval(() => {
        fetch('/api/paystack/verify/' + reference)
        .then(res => res.json())
        .then(vData => {
            if (vData.success && (vData.status === 'successful' || vData.status === 'success')) {
                clearInterval(countdownInterval);
                clearInterval(pollingInterval);
                showSuccessOverlay(reference, amount);
            }
        });
    }, 3000);
}

function updateTimerDisplay(seconds) {
    const mins = Math.floor(seconds / 60);
    const secs = seconds % 60;
    document.getElementById('timerDisplay').innerText = 
        (mins < 10 ? '0' + mins : mins) + ':' + (secs < 10 ? '0' + secs : secs);
}

function cancelPendingState() {
    clearInterval(countdownInterval);
    clearInterval(pollingInterval);
    document.getElementById('momoPendingOverlay').classList.add('hidden');
    document.getElementById('momoFormSection').classList.remove('hidden');

    const btn = document.getElementById('momoSubmitBtn');
    btn.disabled = false;
    btn.innerHTML = '<span class="material-symbols-outlined text-[20px]">lock</span> Pay GH₵ ' + parseFloat(document.getElementById('momo_amount').value).toFixed(2) + ' Now';
}

function showSuccessOverlay(reference, amount) {
    document.getElementById('momoPendingOverlay').classList.add('hidden');
    document.getElementById('momoFormSection').classList.add('hidden');
    document.getElementById('momoSuccessOverlay').classList.remove('hidden');

    document.getElementById('resReference').innerText = reference;
    document.getElementById('resAmount').innerText = 'GH₵ ' + parseFloat(amount).toFixed(2);
}
</script>
