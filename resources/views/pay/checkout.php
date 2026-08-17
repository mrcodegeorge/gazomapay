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
                        <?= strtoupper(substr($link['merchant_name'], 0, 1)) ?>
                    </div>
                    <div>
                        <h4 class="font-headline-lg text-sm font-bold text-white leading-tight"><?= htmlspecialchars($link['merchant_name']) ?></h4>
                        <span class="inline-flex items-center gap-1.5 text-[11px] font-semibold text-emerald-400">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                            Verified Merchant
                        </span>
                    </div>
                </div>
                <div class="text-right">
                    <span class="px-2.5 py-1 rounded-full bg-slate-800/90 text-slate-300 font-label-caps text-[10px] font-bold tracking-wider uppercase border border-slate-700">Test Mode</span>
                </div>
            </div>

            <!-- Product / Link Title & Big Price Tag -->
            <div class="mb-8">
                <span class="text-xs font-semibold uppercase tracking-widest text-slate-400 mb-1 block font-label-caps">Pay For Order</span>
                <h1 class="font-headline-lg text-2xl sm:text-3xl font-extrabold text-white mb-3 tracking-tight"><?= htmlspecialchars($link['name']) ?></h1>
                
                <div class="flex items-baseline gap-2">
                    <span class="font-data-mono text-4xl sm:text-5xl font-black text-white tracking-tight">GH₵ <?= number_format($link['amount'], 2) ?></span>
                    <span class="text-xs font-bold text-slate-400 uppercase font-data-mono">GHS</span>
                </div>

                <?php if (!empty($link['description'])): ?>
                    <p class="text-slate-400 text-sm mt-3 leading-relaxed max-w-md"><?= htmlspecialchars($link['description']) ?></p>
                <?php endif; ?>
            </div>

            <!-- Itemized Order Summary Box -->
            <div class="bg-slate-900/90 border border-slate-800 rounded-2xl p-5 mb-8 backdrop-blur-sm space-y-3 font-body-sm text-sm">
                <div class="flex justify-between items-center text-slate-300">
                    <span><?= htmlspecialchars($link['name']) ?></span>
                    <span class="font-data-mono font-semibold text-white">GH₵ <?= number_format($link['amount'], 2) ?></span>
                </div>
                <div class="flex justify-between items-center text-slate-400 text-xs">
                    <span>Processing &amp; Ledger Fee</span>
                    <span class="font-data-mono text-emerald-400 font-semibold">Included (0.00)</span>
                </div>
                <div class="pt-3 border-t border-slate-800 flex justify-between items-center font-semibold text-white">
                    <span>Total Amount Due</span>
                    <span class="font-data-mono text-lg font-bold text-secondary-container">GH₵ <?= number_format($link['amount'], 2) ?></span>
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
            
            <!-- Phase 3: Main Payment Form Container -->
            <div id="checkoutFormContainer">
                <h3 class="font-headline-lg text-xl font-bold text-slate-900 mb-1">Select Payment Method</h3>
                <p class="font-body-sm text-xs text-slate-500 mb-6">Complete your payment securely using Mobile Money or Card.</p>

                <form id="paymentForm" onsubmit="handleSandboxPayment(event, '<?= $link['token'] ?>')">
                    
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

                    <!-- Customer Information Inputs -->
                    <div class="space-y-4 mb-6">
                        <div>
                            <label class="block font-body-sm text-xs font-bold text-slate-700 mb-1.5 uppercase tracking-wider">Full Name</label>
                            <div class="relative">
                                <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-[18px]">person</span>
                                <input type="text" id="cust_name" class="w-full pl-10 pr-4 py-3 bg-slate-50/70 border border-slate-200 rounded-xl font-body-sm text-slate-900 focus:bg-white focus:ring-2 focus:ring-secondary focus:border-secondary transition-all" placeholder="Ama Serwaa" value="Ama Serwaa" required>
                            </div>
                        </div>

                        <div>
                            <label class="block font-body-sm text-xs font-bold text-slate-700 mb-1.5 uppercase tracking-wider">Email Address (For PDF Receipt)</label>
                            <div class="relative">
                                <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-[18px]">mail</span>
                                <input type="email" id="cust_email" class="w-full pl-10 pr-4 py-3 bg-slate-50/70 border border-slate-200 rounded-xl font-body-sm text-slate-900 focus:bg-white focus:ring-2 focus:ring-secondary focus:border-secondary transition-all" placeholder="ama.serwaa@example.com" value="ama.serwaa@example.com" required>
                            </div>
                        </div>

                        <!-- MoMo Phone Field -->
                        <div id="momoPhoneContainer">
                            <label class="block font-body-sm text-xs font-bold text-slate-700 mb-1.5 uppercase tracking-wider">Mobile Phone Number</label>
                            <div class="relative">
                                <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-[18px]">call</span>
                                <input type="tel" id="cust_phone" class="w-full pl-10 pr-4 py-3 bg-slate-50/70 border border-slate-200 rounded-xl font-body-sm font-data-mono text-slate-900 focus:bg-white focus:ring-2 focus:ring-secondary focus:border-secondary transition-all" placeholder="0241234567" value="0241234567">
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
                                    <span class="font-bold text-white"><?= htmlspecialchars($link['merchant_name']) ?></span>
                                </div>
                                <p class="text-[11px] text-slate-400 pt-2 border-t border-slate-800 leading-normal">Transfer exact amount to the account details above. Your transaction will be verified automatically upon deposit.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Network Provider Dropdown Select with Auto-Detection -->
                    <div id="momoProviderSection" class="mb-6">
                        <div class="flex justify-between items-center mb-1.5">
                            <label class="block font-body-sm text-xs font-bold text-slate-700 uppercase tracking-wider">Mobile Money Network</label>
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

                    <!-- Submit Pay Button -->
                    <button type="submit" id="payBtn" class="w-full py-4 bg-slate-950 text-white font-body-sm font-bold text-base rounded-xl hover:bg-slate-900 active:scale-[0.99] transition-all flex items-center justify-center gap-2.5 cursor-pointer shadow-xl shadow-slate-900/10">
                        <span class="material-symbols-outlined text-[20px]">lock</span>
                        <span>Pay GH₵ <?= number_format($link['amount'], 2) ?></span>
                    </button>
                </form>

                <!-- Footer Guarantee Badges -->
                <div class="flex items-center justify-center gap-3 mt-6 pt-4 border-t border-slate-100 font-body-sm text-xs text-slate-500">
                    <span class="flex items-center gap-1 text-emerald-600 font-semibold">
                        <span class="material-symbols-outlined text-[16px]">verified</span>
                        Bank-Grade Security
                    </span>
                    <span>&bull;</span>
                    <span>Instant Settlement</span>
                </div>
            </div>

                <!-- Pending Overlay & 90s Countdown for Mobile Money -->
            <div class="p-8 text-center hidden" id="momoPendingOverlay">
                <div class="w-20 h-20 rounded-full bg-amber-50 border-4 border-amber-100 text-amber-600 flex items-center justify-center mx-auto mb-6 shadow-inner relative">
                    <span class="material-symbols-outlined text-[40px] animate-spin">sync</span>
                </div>

                <h3 class="font-headline-lg text-2xl font-bold text-slate-900 mb-2">Mobile Money Prompt Sent</h3>
                <p class="font-body-sm text-sm text-slate-500 mb-6 max-w-sm mx-auto">An STK approval push has been sent directly to your phone handset.</p>

                <div class="p-4 bg-amber-50 border border-amber-200/80 rounded-2xl mb-4 text-amber-900 font-body-sm text-sm font-bold shadow-sm">
                    Please check your phone and enter your Mobile Money PIN to approve.
                </div>

                <!-- Sandbox STK Simulation Button -->
                <button type="button" onclick="simulateMomoApprovalNow()" class="w-full mb-6 py-3 px-4 bg-emerald-600 hover:bg-emerald-700 text-white font-body-sm text-xs font-bold rounded-xl transition-all flex items-center justify-center gap-2 shadow-md shadow-emerald-600/20 cursor-pointer">
                    <span class="material-symbols-outlined text-[18px]">verified</span>
                    <span>⚡ Simulate STK PIN Approval (Sandbox)</span>
                </button>

                <div class="bg-slate-50 border border-slate-200 rounded-2xl p-6 mb-6">
                    <div class="font-label-caps text-xs text-slate-500 uppercase tracking-widest mb-1 font-bold">Time Remaining to Approve</div>
                    <div class="font-data-mono text-5xl font-black text-secondary tracking-tight my-2" id="timerDisplay">01:30</div>
                    <p class="font-body-sm text-xs text-slate-500 mt-2" id="stkInstructions">
                        Transmitted to <strong id="displayPhone" class="text-slate-800 font-data-mono">024 123 4567</strong>
                    </p>
                </div>

                <button type="button" onclick="cancelPendingState()" class="w-full py-3 bg-slate-100 border border-slate-200 text-slate-700 font-body-sm text-xs font-bold rounded-xl hover:bg-slate-200 transition-colors">
                    Cancel &amp; Return
                </button>
            </div>

            <!-- 3D Secure Card OTP Modal Container -->
            <div class="p-8 text-center hidden" id="threeDsModalContainer">
                <div class="w-16 h-16 rounded-2xl bg-blue-50 border-2 border-blue-200 text-blue-600 flex items-center justify-center mx-auto mb-5 shadow-sm">
                    <span class="material-symbols-outlined text-[36px]">shield_person</span>
                </div>

                <h3 class="font-headline-lg text-2xl font-bold text-slate-900 mb-1">3D Secure Verification</h3>
                <p class="font-body-sm text-xs text-slate-500 mb-6">Your issuing bank requires One-Time Password (OTP) authorization to complete this card charge.</p>

                <div class="bg-slate-50 border border-slate-200 rounded-2xl p-5 mb-6 text-left space-y-3 font-body-sm text-xs">
                    <div class="flex justify-between items-center text-slate-600">
                        <span>Card Brand</span>
                        <span class="font-bold text-slate-900" id="threeDsCardBrand">VISA</span>
                    </div>
                    <div class="flex justify-between items-center text-slate-600">
                        <span>Card Number</span>
                        <span class="font-data-mono font-bold text-slate-900" id="threeDsMaskedCard">**** **** **** 9010</span>
                    </div>
                    <div class="flex justify-between items-center text-slate-600 pt-2 border-t border-slate-200">
                        <span>Sandbox Test OTP</span>
                        <span class="font-data-mono font-extrabold text-blue-600 bg-blue-100 px-2 py-0.5 rounded">123456</span>
                    </div>
                </div>

                <form onsubmit="submit3DsOtp(event)" class="space-y-4">
                    <div>
                        <label class="block font-body-sm text-xs font-bold text-slate-700 mb-1.5 uppercase tracking-wider text-left">Enter 6-Digit OTP</label>
                        <input type="text" id="threeDsOtpInput" maxlength="6" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl font-data-mono text-2xl font-black text-center text-slate-900 focus:bg-white focus:ring-2 focus:ring-blue-500 transition-all tracking-widest" placeholder="123456" value="123456" required>
                    </div>

                    <button type="submit" id="threeDsSubmitBtn" class="w-full py-3.5 bg-blue-600 hover:bg-blue-700 text-white font-body-sm font-bold text-sm rounded-xl transition-all flex items-center justify-center gap-2 cursor-pointer shadow-lg shadow-blue-600/20">
                        <span class="material-symbols-outlined text-[18px]">verified_user</span>
                        <span>Authorize Card Payment</span>
                    </button>

                    <button type="button" onclick="cancelPendingState()" class="w-full py-2.5 bg-transparent text-slate-500 font-body-sm text-xs font-bold rounded-xl hover:text-slate-800 transition-colors">
                        Cancel Transaction
                    </button>
                </form>
            </div>

            <!-- Success Confirmation Screen -->
            <div class="p-8 text-center hidden" id="successContainer">
                <div class="w-20 h-20 rounded-full bg-emerald-100 border-4 border-emerald-50 text-emerald-600 flex items-center justify-center mx-auto mb-6 shadow-lg shadow-emerald-600/10 animate-bounce">
                    <span class="material-symbols-outlined text-[42px]">check_circle</span>
                </div>
                <h3 class="font-headline-lg text-3xl font-extrabold text-slate-900 mb-1">Payment Successful!</h3>
                <p class="font-body-sm text-sm text-slate-500 mb-6">Your transaction has been processed and verified by Gazoma Ledger.</p>

                <div class="bg-slate-50 border border-slate-200 rounded-2xl p-6 mb-6 text-left space-y-3 font-body-sm text-sm">
                    <div class="flex justify-between items-center">
                        <span class="text-slate-500">Transaction Reference</span>
                        <span class="font-data-mono font-bold text-secondary" id="res_ref">GZM_00000000</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-slate-500">Amount Paid</span>
                        <span class="font-data-mono font-bold text-slate-900 text-base" id="res_amt">GH₵ 0.00</span>
                    </div>
                    <div class="flex justify-between items-center pt-2 border-t border-slate-200">
                        <span class="text-slate-500">Status</span>
                        <span class="px-3 py-1 rounded-full bg-emerald-100 text-emerald-700 font-label-caps text-xs font-extrabold uppercase">Verified &amp; Settled</span>
                    </div>
                </div>

                <button onclick="window.print()" class="w-full py-3.5 bg-slate-900 text-white font-body-sm font-bold rounded-xl hover:bg-slate-800 transition-all flex items-center justify-center gap-2 cursor-pointer shadow-lg shadow-slate-900/10">
                    <span class="material-symbols-outlined text-[18px]">print</span>
                    Print Official Receipt
                </button>
            </div>

        </div>
    </div>
</div>

<script>
let countdownInterval = null;
let pollingInterval = null;
let activePendingReference = null;

document.addEventListener('DOMContentLoaded', function() {
    const phoneInput = document.getElementById('cust_phone');
    if (phoneInput) {
        phoneInput.addEventListener('input', function() {
            autoDetectNetwork(this.value);
        });
        autoDetectNetwork(phoneInput.value);
    }

    const cardNumInput = document.getElementById('card_number');
    if (cardNumInput) {
        cardNumInput.addEventListener('input', function() {
            autoDetectCardBrand(this.value);
        });
    }

    const cardExpInput = document.getElementById('card_expiry');
    if (cardExpInput) {
        cardExpInput.addEventListener('input', function(e) {
            let val = this.value.replace(/[^0-9]/g, '');
            if (val.length >= 2) {
                this.value = val.substring(0, 2) + '/' + val.substring(2, 4);
            } else {
                this.value = val;
            }
        });
    }
});

function autoDetectCardBrand(cardNum) {
    let clean = cardNum.replace(/[^0-9]/g, '');
    let brand = 'VISA / MC';
    if (clean.startsWith('4')) {
        brand = 'VISA';
    } else if (clean.startsWith('51') || clean.startsWith('52') || clean.startsWith('53') || clean.startsWith('54') || clean.startsWith('55')) {
        brand = 'MASTERCARD';
    } else if (clean.startsWith('34') || clean.startsWith('37')) {
        brand = 'AMEX';
    }
    const badge = document.querySelector('#cardFields span.absolute');
    if (badge) {
        badge.innerText = brand;
    }
}

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

function handleSandboxPayment(e, token) {
    e.preventDefault();
    const btn = document.getElementById('payBtn');
    btn.disabled = true;
    btn.innerHTML = '<span class="material-symbols-outlined animate-spin text-[20px]">sync</span> Processing Payment...';

    const formData = new FormData();
    formData.append('customer_name', document.getElementById('cust_name').value);
    formData.append('customer_email', document.getElementById('cust_email').value);
    formData.append('customer_phone', document.getElementById('cust_phone').value);
    
    const selectedMethod = document.querySelector('input[name="pay_method"]:checked').value;
    formData.append('payment_method', selectedMethod);

    if (selectedMethod === 'card') {
        formData.append('card_number', document.getElementById('card_number').value);
        formData.append('card_expiry', document.getElementById('card_expiry').value);
        formData.append('card_cvc', document.getElementById('card_cvc').value);
        formData.append('require_3ds', '1');
    }

    const providerSelect = document.getElementById('momo_provider_select');
    const provider = providerSelect ? providerSelect.value : 'mtn';
    formData.append('provider', provider);

    fetch('/pay/' + token, {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            activePendingReference = data.reference;
            if (data.requires_3ds || data.status === 'pending_3ds') {
                show3DsModal(data.reference, data.card_brand || 'VISA', data.masked_card || '**** **** **** 9010');
            } else if (data.status === 'pending' || selectedMethod === 'mobile_money') {
                startMomoPendingOverlay(data.reference, document.getElementById('cust_phone').value, data.instructions);
            } else {
                showSuccessReceipt(data.reference, data.amount);
            }
        } else {
            alert('Payment Failed: ' + (data.message || 'Error processing request'));
            btn.disabled = false;
            btn.innerHTML = '<span class="material-symbols-outlined text-[20px]">lock</span> Pay GH₵ ' + parseFloat(<?= $link['amount'] ?>).toFixed(2);
        }
    })
    .catch(err => {
        alert('Error processing payment.');
        btn.disabled = false;
        btn.innerHTML = '<span class="material-symbols-outlined text-[20px]">lock</span> Pay GH₵ ' + parseFloat(<?= $link['amount'] ?>).toFixed(2);
    });
}

function show3DsModal(reference, brand, maskedCard) {
    document.getElementById('checkoutFormContainer').classList.add('hidden');
    document.getElementById('momoPendingOverlay').classList.add('hidden');
    document.getElementById('threeDsModalContainer').classList.remove('hidden');
    
    document.getElementById('threeDsCardBrand').innerText = brand;
    document.getElementById('threeDsMaskedCard').innerText = maskedCard;
}

function submit3DsOtp(e) {
    e.preventDefault();
    const otp = document.getElementById('threeDsOtpInput').value;
    const btn = document.getElementById('threeDsSubmitBtn');
    btn.disabled = true;
    btn.innerHTML = '<span class="material-symbols-outlined animate-spin text-[18px]">sync</span> Verifying OTP...';

    fetch('/api/v1/card/verify-3ds', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            reference: activePendingReference,
            otp: otp
        })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            document.getElementById('threeDsModalContainer').classList.add('hidden');
            showSuccessReceipt(activePendingReference, <?= $link['amount'] ?>);
        } else {
            alert('3DS Error: ' + (data.message || 'OTP verification failed.'));
            btn.disabled = false;
            btn.innerHTML = '<span class="material-symbols-outlined text-[18px]">verified_user</span> Authorize Card Payment';
        }
    })
    .catch(err => {
        alert('Verification failed.');
        btn.disabled = false;
        btn.innerHTML = '<span class="material-symbols-outlined text-[18px]">verified_user</span> Authorize Card Payment';
    });
}

function simulateMomoApprovalNow() {
    if (!activePendingReference) return;
    
    fetch('/api/v1/momo/simulate-approval', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ reference: activePendingReference })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            clearInterval(countdownInterval);
            clearInterval(pollingInterval);
            showSuccessReceipt(activePendingReference, <?= $link['amount'] ?>);
        } else {
            alert('Simulation Error: ' + (data.message || 'Failed to simulate approval.'));
        }
    });
}

function startMomoPendingOverlay(reference, phone, instructions) {
    document.getElementById('checkoutFormContainer').classList.add('hidden');
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
            alert('Payment window expired. Please try again.');
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
                showSuccessReceipt(reference, <?= $link['amount'] ?>);
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
    document.getElementById('threeDsModalContainer').classList.add('hidden');
    document.getElementById('checkoutFormContainer').classList.remove('hidden');

    const btn = document.getElementById('payBtn');
    btn.disabled = false;
    btn.innerHTML = '<span class="material-symbols-outlined text-[20px]">lock</span> Pay GH₵ ' + parseFloat(<?= $link['amount'] ?>).toFixed(2);
}

function showSuccessReceipt(reference, amount) {
    document.getElementById('momoPendingOverlay').classList.add('hidden');
    document.getElementById('threeDsModalContainer').classList.add('hidden');
    document.getElementById('checkoutFormContainer').classList.add('hidden');
    document.getElementById('successContainer').classList.remove('hidden');

    document.getElementById('res_ref').innerText = reference;
    document.getElementById('res_amt').innerText = 'GH₵ ' + parseFloat(amount).toFixed(2);
}
</script>
