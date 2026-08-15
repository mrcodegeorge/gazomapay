<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Merchant Onboarding | Gazoma Pay</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@500;700&display=swap">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .font-data-mono { font-family: 'JetBrains Mono', monospace; }
    </style>
</head>
<body class="bg-slate-950 text-slate-100 min-h-screen">
<div class="min-h-screen bg-slate-950 flex flex-col justify-center items-center p-4 sm:p-6 lg:p-8">
    
    <!-- Top Branding Logo Header -->
    <div class="mb-8 text-center">
        <a href="/" class="inline-flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-600 to-indigo-700 flex items-center justify-center text-white font-black text-xl shadow-lg shadow-blue-500/20">
                G
            </div>
            <span class="font-headline text-2xl font-black tracking-tight text-white">Gazoma<span class="text-blue-500">Pay</span></span>
        </a>
        <p class="font-body-sm text-sm text-slate-400 mt-1">Merchant Verification & Business Setup Wizard</p>
    </div>

    <!-- Onboarding Container Card -->
    <div class="w-full max-w-3xl bg-slate-900/80 backdrop-blur-xl border border-slate-800 rounded-3xl p-6 sm:p-10 shadow-2xl relative overflow-hidden">
        
        <!-- Flash Alert Messages -->
        <?php if ($msg = Response::getFlash('success')): ?>
            <div class="p-4 mb-6 bg-emerald-500/10 border border-emerald-500/30 text-emerald-300 rounded-2xl text-xs font-bold flex items-center gap-3">
                <span class="material-symbols-outlined text-emerald-400 text-[18px]">check_circle</span>
                <span><?= htmlspecialchars($msg) ?></span>
            </div>
        <?php endif; ?>

        <?php if ($msg = Response::getFlash('error')): ?>
            <div class="p-4 mb-6 bg-rose-500/10 border border-rose-500/30 text-rose-300 rounded-2xl text-xs font-bold flex items-center gap-3">
                <span class="material-symbols-outlined text-rose-400 text-[18px]">error</span>
                <span><?= htmlspecialchars($msg) ?></span>
            </div>
        <?php endif; ?>

        <!-- Multi-Step Progress Header Bar -->
        <?php $currentStep = (int)($merchant['onboarding_step'] ?? 1); ?>
        <div class="mb-10">
            <div class="flex items-center justify-between relative">
                <!-- Progress Line -->
                <div class="absolute left-0 right-0 top-1/2 -translate-y-1/2 h-1 bg-slate-800 z-0"></div>
                <div class="absolute left-0 top-1/2 -translate-y-1/2 h-1 bg-blue-600 transition-all duration-500 z-0" style="width: <?= (($currentStep - 1) / 3) * 100 ?>%"></div>

                <!-- Step 1 Indicator -->
                <div class="relative z-10 flex flex-col items-center">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center text-xs font-bold font-data-mono transition-all <?= $currentStep >= 1 ? 'bg-blue-600 text-white shadow-lg shadow-blue-500/30 ring-4 ring-slate-900' : 'bg-slate-800 text-slate-400' ?>">
                        <?= $currentStep > 1 ? '<span class="material-symbols-outlined text-[18px]">check</span>' : '1' ?>
                    </div>
                    <span class="text-[11px] font-bold mt-2 uppercase tracking-wider <?= $currentStep >= 1 ? 'text-blue-400' : 'text-slate-500' ?>">Business</span>
                </div>

                <!-- Step 2 Indicator -->
                <div class="relative z-10 flex flex-col items-center">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center text-xs font-bold font-data-mono transition-all <?= $currentStep >= 2 ? 'bg-blue-600 text-white shadow-lg shadow-blue-500/30 ring-4 ring-slate-900' : 'bg-slate-800 text-slate-400' ?>">
                        <?= $currentStep > 2 ? '<span class="material-symbols-outlined text-[18px]">check</span>' : '2' ?>
                    </div>
                    <span class="text-[11px] font-bold mt-2 uppercase tracking-wider <?= $currentStep >= 2 ? 'text-blue-400' : 'text-slate-500' ?>">Payout Bank</span>
                </div>

                <!-- Step 3 Indicator -->
                <div class="relative z-10 flex flex-col items-center">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center text-xs font-bold font-data-mono transition-all <?= $currentStep >= 3 ? 'bg-blue-600 text-white shadow-lg shadow-blue-500/30 ring-4 ring-slate-900' : 'bg-slate-800 text-slate-400' ?>">
                        <?= $currentStep > 3 ? '<span class="material-symbols-outlined text-[18px]">check</span>' : '3' ?>
                    </div>
                    <span class="text-[11px] font-bold mt-2 uppercase tracking-wider <?= $currentStep >= 3 ? 'text-blue-400' : 'text-slate-500' ?>">Verification</span>
                </div>

                <!-- Step 4 Indicator -->
                <div class="relative z-10 flex flex-col items-center">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center text-xs font-bold font-data-mono transition-all <?= $currentStep >= 4 ? 'bg-emerald-500 text-white shadow-lg shadow-emerald-500/30 ring-4 ring-slate-900' : 'bg-slate-800 text-slate-400' ?>">
                        4
                    </div>
                    <span class="text-[11px] font-bold mt-2 uppercase tracking-wider <?= $currentStep >= 4 ? 'text-emerald-400' : 'text-slate-500' ?>">Activation</span>
                </div>
            </div>
        </div>

        <!-- Step 1 Form: Business Details -->
        <?php if ($currentStep === 1): ?>
            <div class="space-y-6">
                <div>
                    <h3 class="text-xl font-bold text-white mb-1">Step 1: Registered Business Details</h3>
                    <p class="text-xs text-slate-400">Provide legal entity details to enable online payment collection across Ghana.</p>
                </div>

                <form action="/onboarding/step" method="POST" class="space-y-4">
                    <input type="hidden" name="csrf_token" value="<?= Auth::generateCsrfToken() ?>">
                    <input type="hidden" name="step" value="1">

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-1">Registered Legal Name *</label>
                            <input type="text" name="legal_name" value="<?= htmlspecialchars($merchant['legal_name'] ?? $merchant['name'] ?? '') ?>" class="w-full px-4 py-3 bg-slate-950 border border-slate-800 rounded-xl text-sm text-white focus:outline-none focus:border-blue-500" placeholder="e.g. Gazoma Tech Ghana Limited" required>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-1">Trading Name (DBA)</label>
                            <input type="text" name="trading_name" value="<?= htmlspecialchars($merchant['trading_name'] ?? $merchant['name'] ?? '') ?>" class="w-full px-4 py-3 bg-slate-950 border border-slate-800 rounded-xl text-sm text-white focus:outline-none focus:border-blue-500" placeholder="e.g. Gazoma Pay">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-1">Business Structure *</label>
                            <select name="business_type" class="w-full px-4 py-3 bg-slate-950 border border-slate-800 rounded-xl text-sm text-white focus:outline-none focus:border-blue-500 cursor-pointer">
                                <option value="limited_company" <?= ($merchant['business_type'] ?? '') === 'limited_company' ? 'selected' : '' ?>>Limited Liability Company (LTD)</option>
                                <option value="sole_proprietorship" <?= ($merchant['business_type'] ?? '') === 'sole_proprietorship' ? 'selected' : '' ?>>Sole Proprietorship / Enterprise</option>
                                <option value="partnership" <?= ($merchant['business_type'] ?? '') === 'partnership' ? 'selected' : '' ?>>Partnership</option>
                                <option value="registered_charity" <?= ($merchant['business_type'] ?? '') === 'registered_charity' ? 'selected' : '' ?>>NGO / Registered Charity</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-1">Registration Number (TIN / RGD)</label>
                            <input type="text" name="business_registration_number" value="<?= htmlspecialchars($merchant['business_registration_number'] ?? '') ?>" class="w-full px-4 py-3 bg-slate-950 border border-slate-800 rounded-xl text-sm text-white font-data-mono focus:outline-none focus:border-blue-500" placeholder="CS-892019284">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-1">Official Business Phone *</label>
                            <input type="tel" name="phone" value="<?= htmlspecialchars($merchant['phone'] ?? '') ?>" class="w-full px-4 py-3 bg-slate-950 border border-slate-800 rounded-xl text-sm text-white focus:outline-none focus:border-blue-500" placeholder="+233 24 123 4567" required>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-1">Operating Address</label>
                            <input type="text" name="address" value="<?= htmlspecialchars($merchant['address'] ?? '') ?>" class="w-full px-4 py-3 bg-slate-950 border border-slate-800 rounded-xl text-sm text-white focus:outline-none focus:border-blue-500" placeholder="15 Independence Avenue, Ridge, Accra">
                        </div>
                    </div>

                    <div class="pt-4 flex justify-end">
                        <button type="submit" class="px-6 py-3 bg-blue-600 hover:bg-blue-500 text-white text-xs font-bold rounded-xl shadow-lg shadow-blue-500/25 transition-all flex items-center gap-2 cursor-pointer">
                            <span>Save & Continue to Payout Account</span>
                            <span class="material-symbols-outlined text-[16px]">arrow_forward</span>
                        </button>
                    </div>
                </form>
            </div>
        <?php endif; ?>

        <!-- Step 2 Form: Payout Bank Account -->
        <?php if ($currentStep === 2): ?>
            <div class="space-y-6">
                <div>
                    <h3 class="text-xl font-bold text-white mb-1">Step 2: Settlement Payout Destination</h3>
                    <p class="text-xs text-slate-400">Configure where automated customer payouts and settlement funds will be transferred.</p>
                </div>

                <form action="/onboarding/step" method="POST" class="space-y-4">
                    <input type="hidden" name="csrf_token" value="<?= Auth::generateCsrfToken() ?>">
                    <input type="hidden" name="step" value="2">

                    <div>
                        <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-1">Select Bank / Mobile Money Provider *</label>
                        <select name="bank_name" class="w-full px-4 py-3 bg-slate-950 border border-slate-800 rounded-xl text-sm text-white focus:outline-none focus:border-blue-500 cursor-pointer" required>
                            <option value="">-- Choose Financial Institution --</option>
                            <optgroup label="Ghanaian Commercial Banks">
                                <option value="GCB Bank Ghana" selected>GCB Bank Ghana</option>
                                <option value="Stanbic Bank Ghana">Stanbic Bank Ghana</option>
                                <option value="Ecobank Ghana">Ecobank Ghana</option>
                                <option value="Absa Bank Ghana">Absa Bank Ghana</option>
                                <option value="Fidelity Bank Ghana">Fidelity Bank Ghana</option>
                                <option value="CalBank Ghana">CalBank Ghana</option>
                                <option value="Zenith Bank Ghana">Zenith Bank Ghana</option>
                            </optgroup>
                            <optgroup label="Mobile Money Wallets">
                                <option value="MTN Mobile Money Merchant">MTN Mobile Money Merchant Wallet</option>
                                <option value="Telecel Cash Merchant">Telecel Cash Merchant Wallet</option>
                                <option value="AT Money Merchant">AT Money Merchant Wallet</option>
                            </optgroup>
                        </select>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-1">Account Number / Wallet ID *</label>
                            <input type="text" name="account_number" class="w-full px-4 py-3 bg-slate-950 border border-slate-800 rounded-xl text-sm text-white font-data-mono focus:outline-none focus:border-blue-500" placeholder="1011129384728" required>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-1">Account Name *</label>
                            <input type="text" name="account_name" value="<?= htmlspecialchars($merchant['legal_name'] ?? $merchant['name'] ?? '') ?>" class="w-full px-4 py-3 bg-slate-950 border border-slate-800 rounded-xl text-sm text-white focus:outline-none focus:border-blue-500" placeholder="Gazoma Tech Ltd" required>
                        </div>
                    </div>

                    <div class="pt-4 flex justify-between items-center">
                        <a href="/onboarding" class="text-xs font-bold text-slate-400 hover:text-white transition-colors flex items-center gap-1">
                            <span class="material-symbols-outlined text-[16px]">arrow_back</span> Back
                        </a>
                        <button type="submit" class="px-6 py-3 bg-blue-600 hover:bg-blue-500 text-white text-xs font-bold rounded-xl shadow-lg shadow-blue-500/25 transition-all flex items-center gap-2 cursor-pointer">
                            <span>Save Bank Account & Verify</span>
                            <span class="material-symbols-outlined text-[16px]">arrow_forward</span>
                        </button>
                    </div>
                </form>
            </div>
        <?php endif; ?>

        <!-- Step 3 Form: Identity Verification -->
        <?php if ($currentStep === 3): ?>
            <div class="space-y-6">
                <div>
                    <h3 class="text-xl font-bold text-white mb-1">Step 3: Director Identity & KYB Verification</h3>
                    <p class="text-xs text-slate-400">Compliance check in accordance with Bank of Ghana anti-money laundering guidelines.</p>
                </div>

                <form action="/onboarding/step" method="POST" class="space-y-4">
                    <input type="hidden" name="csrf_token" value="<?= Auth::generateCsrfToken() ?>">
                    <input type="hidden" name="step" value="3">

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-1">Director Identification Type *</label>
                            <select name="id_type" class="w-full px-4 py-3 bg-slate-950 border border-slate-800 rounded-xl text-sm text-white focus:outline-none focus:border-blue-500 cursor-pointer">
                                <option value="ghana_card" selected>Ghana Card (GHA-XXXXXXXXX-X)</option>
                                <option value="passport">Ghanaian Passport</option>
                                <option value="drivers_license">Driver's License</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-1">ID Document Number *</label>
                            <input type="text" name="id_number" class="w-full px-4 py-3 bg-slate-950 border border-slate-800 rounded-xl text-sm text-white font-data-mono focus:outline-none focus:border-blue-500" placeholder="GHA-789201928-3" required>
                        </div>
                    </div>

                    <div class="p-4 rounded-2xl bg-blue-500/10 border border-blue-500/20 text-blue-200 text-xs leading-relaxed flex items-start gap-3">
                        <span class="material-symbols-outlined text-blue-400 text-[20px] shrink-0 mt-0.5">verified_user</span>
                        <span>Your identity documents are encrypted end-to-end and submitted to Bank of Ghana verification databases for instant KYB clearance.</span>
                    </div>

                    <div class="pt-4 flex justify-between items-center">
                        <a href="/onboarding" class="text-xs font-bold text-slate-400 hover:text-white transition-colors flex items-center gap-1">
                            <span class="material-symbols-outlined text-[16px]">arrow_back</span> Back
                        </a>
                        <button type="submit" class="px-6 py-3 bg-blue-600 hover:bg-blue-500 text-white text-xs font-bold rounded-xl shadow-lg shadow-blue-500/25 transition-all flex items-center gap-2 cursor-pointer">
                            <span>Submit Verification & Continue</span>
                            <span class="material-symbols-outlined text-[16px]">arrow_forward</span>
                        </button>
                    </div>
                </form>
            </div>
        <?php endif; ?>

        <!-- Step 4 Form: Final Activation & API Credentials -->
        <?php if ($currentStep >= 4): ?>
            <div class="space-y-6 text-center">
                <div class="w-16 h-16 rounded-full bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 flex items-center justify-center mx-auto shadow-xl shadow-emerald-500/10">
                    <span class="material-symbols-outlined text-[36px]">rocket_launch</span>
                </div>

                <div>
                    <h3 class="text-2xl font-bold text-white mb-2">Ready for Live Processing!</h3>
                    <p class="text-xs text-slate-400 max-w-lg mx-auto leading-relaxed">Your business details, payout bank account, and director verification have been validated. Click below to launch your live merchant workspace.</p>
                </div>

                <div class="p-6 rounded-2xl bg-slate-950 border border-slate-800 text-left space-y-3">
                    <div class="flex justify-between items-center text-xs border-b border-slate-800 pb-2">
                        <span class="text-slate-400">Business Name</span>
                        <span class="font-bold text-white"><?= htmlspecialchars($merchant['legal_name'] ?? $merchant['name']) ?></span>
                    </div>
                    <div class="flex justify-between items-center text-xs border-b border-slate-800 pb-2">
                        <span class="text-slate-400">KYC Status</span>
                        <span class="px-2 py-0.5 rounded-full bg-emerald-500/10 text-emerald-400 font-bold">Approved & Verified</span>
                    </div>
                    <div class="flex justify-between items-center text-xs">
                        <span class="text-slate-400">Enabled Channels</span>
                        <span class="font-bold text-blue-400">Mobile Money, Cards, Bank Transfer</span>
                    </div>
                </div>

                <form action="/onboarding/complete" method="POST">
                    <input type="hidden" name="csrf_token" value="<?= Auth::generateCsrfToken() ?>">
                    <button type="submit" class="w-full py-4 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-white font-bold text-sm rounded-2xl shadow-xl shadow-blue-600/30 transition-all cursor-pointer flex items-center justify-center gap-2">
                        <span>Launch Gazoma Pay Merchant Dashboard</span>
                        <span class="material-symbols-outlined text-[18px]">east</span>
                    </button>
                </form>
            </div>
        <?php endif; ?>

    </div>
</div>
</body>
</html>
