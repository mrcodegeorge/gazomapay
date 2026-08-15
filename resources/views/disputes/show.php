<div class="space-y-6 max-w-6xl mx-auto">
    
    <!-- Flash Messages -->
    <?php if (!empty($_SESSION['flash_success'])): ?>
        <div class="p-4 bg-emerald-50 border border-emerald-200 rounded-2xl text-emerald-900 font-body-sm text-sm flex items-center gap-3">
            <span class="material-symbols-outlined text-emerald-600 text-[20px]">check_circle</span>
            <span><?= htmlspecialchars($_SESSION['flash_success']) ?></span>
        </div>
        <?php unset($_SESSION['flash_success']); ?>
    <?php endif; ?>

    <?php if (!empty($_SESSION['flash_error'])): ?>
        <div class="p-4 bg-rose-50 border border-rose-200 rounded-2xl text-rose-900 font-body-sm text-sm flex items-center gap-3">
            <span class="material-symbols-outlined text-rose-600 text-[20px]">error</span>
            <span><?= htmlspecialchars($_SESSION['flash_error']) ?></span>
        </div>
        <?php unset($_SESSION['flash_error']); ?>
    <?php endif; ?>

    <!-- Back Navigation & Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <a href="/disputes" class="inline-flex items-center gap-1 font-body-sm text-xs font-bold text-on-surface-variant hover:text-primary mb-2 transition-colors">
                <span class="material-symbols-outlined text-[16px]">arrow_back</span>
                <span>Back to Disputes List</span>
            </a>
            <h1 class="font-headline-lg text-2xl font-bold text-on-surface dark:text-inverse-on-surface flex items-center gap-3">
                <span>Dispute <?= htmlspecialchars($dispute['dispute_code']) ?></span>
                <?php
                $statusBadges = [
                    'needs_response' => 'bg-amber-100 text-amber-800 border-amber-200',
                    'under_review' => 'bg-blue-100 text-blue-800 border-blue-200',
                    'won' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
                    'lost' => 'bg-rose-100 text-rose-800 border-rose-200',
                    'accepted' => 'bg-slate-100 text-slate-700 border-slate-200'
                ];
                $badgeClass = $statusBadges[$dispute['status']] ?? 'bg-slate-100 text-slate-700';
                ?>
                <span class="px-3 py-1 rounded-full font-label-caps text-xs font-extrabold uppercase border <?= $badgeClass ?>">
                    <?= str_replace('_', ' ', $dispute['status']) ?>
                </span>
            </h1>
        </div>

        <div class="flex items-center gap-3">
            <?php if ($dispute['status'] === 'needs_response'): ?>
                <form method="POST" action="/disputes/<?= $dispute['id'] ?>/accept" onsubmit="return confirm('Are you sure you want to accept this dispute? The disputed amount will be refunded to the cardholder.')">
                    <button type="submit" class="px-4 py-2.5 bg-rose-600 hover:bg-rose-700 text-white font-body-sm text-xs font-bold rounded-xl transition-all flex items-center gap-2 shadow-sm cursor-pointer">
                        <span class="material-symbols-outlined text-[18px]">check</span>
                        <span>Accept Chargeback</span>
                    </button>
                </form>
            <?php endif; ?>
        </div>
    </div>

    <!-- Main Grid: Left Evidence & Right Details -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- LEFT COLUMN: Action Banner & Evidence Submission -->
        <div class="lg:col-span-2 space-y-6">
            
            <!-- Deadline Alert Banner -->
            <?php if ($dispute['status'] === 'needs_response'): ?>
                <div class="p-5 bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-900/60 rounded-2xl text-amber-900 dark:text-amber-200 space-y-2">
                    <div class="flex items-center gap-2 font-bold text-sm">
                        <span class="material-symbols-outlined text-amber-600 text-[20px]">timer</span>
                        <span>Response Deadline: <?= date('F d, Y', strtotime($dispute['due_date'])) ?></span>
                    </div>
                    <p class="font-body-sm text-xs text-amber-800/90 dark:text-amber-300 leading-relaxed">
                        The issuing bank requires compelling evidence to challenge this chargeback. If no response is submitted by the due date, the dispute will be automatically surrendered.
                    </p>
                </div>
            <?php elseif ($dispute['status'] === 'under_review'): ?>
                <div class="p-5 bg-blue-50 dark:bg-blue-950/40 border border-blue-200 dark:border-blue-900/60 rounded-2xl text-blue-900 dark:text-blue-200 space-y-2">
                    <div class="flex items-center gap-2 font-bold text-sm">
                        <span class="material-symbols-outlined text-blue-600 text-[20px]">published_with_changes</span>
                        <span>Evidence Under Review</span>
                    </div>
                    <p class="font-body-sm text-xs text-blue-800/90 dark:text-blue-300 leading-relaxed">
                        Your evidence has been transmitted to the card network and issuing bank. Bank decisions typically take 7 to 14 business days.
                    </p>
                </div>
            <?php endif; ?>

            <!-- Evidence Form / Timeline Card -->
            <div class="bg-surface border border-outline-variant rounded-2xl p-6 shadow-sm space-y-6">
                <h3 class="font-headline-lg text-lg font-bold text-on-surface">Merchant Evidence &amp; Documentation</h3>

                <?php if ($dispute['status'] === 'needs_response'): ?>
                    <form method="POST" action="/disputes/<?= $dispute['id'] ?>/evidence" class="space-y-4">
                        <div>
                            <label class="block font-body-sm text-xs font-bold text-on-surface-variant uppercase tracking-wider mb-1.5">
                                Explanation &amp; Rebuttal Statement *
                            </label>
                            <textarea name="evidence_text" rows="5" required 
                                      placeholder="Provide detailed justification explaining why this charge is valid (e.g., customer account activity, agreement terms, delivery confirmation...)" 
                                      class="w-full p-3.5 bg-surface-container-low border border-outline-variant rounded-xl font-body-sm text-sm text-on-surface focus:bg-surface focus:ring-2 focus:ring-secondary transition-all"></textarea>
                        </div>

                        <div>
                            <label class="block font-body-sm text-xs font-bold text-on-surface-variant uppercase tracking-wider mb-1.5">
                                Waybill / Tracking Number (If Physical Goods Delivered)
                            </label>
                            <input type="text" name="tracking_number" placeholder="e.g. WB-99201923" 
                                   class="w-full px-4 py-3 bg-surface-container-low border border-outline-variant rounded-xl font-body-sm font-data-mono text-sm text-on-surface focus:bg-surface focus:ring-2 focus:ring-secondary transition-all">
                        </div>

                        <div class="pt-2">
                            <button type="submit" class="w-full sm:w-auto px-6 py-3 bg-primary hover:bg-primary/90 text-on-primary font-body-sm font-bold text-sm rounded-xl transition-all flex items-center justify-center gap-2 cursor-pointer shadow-md">
                                <span class="material-symbols-outlined text-[20px]">send</span>
                                <span>Submit Dispute Evidence</span>
                            </button>
                        </div>
                    </form>

                <?php else: ?>

                    <!-- Submitted Evidence Log -->
                    <div class="bg-surface-container-low border border-outline-variant rounded-xl p-4 space-y-3 font-body-sm text-xs">
                        <div class="font-bold text-on-surface text-sm flex items-center gap-2">
                            <span class="material-symbols-outlined text-emerald-600 text-[18px]">verified</span>
                            <span>Submitted Evidence Record</span>
                        </div>
                        <div class="text-on-surface font-body-sm text-sm leading-relaxed whitespace-pre-line bg-surface p-3.5 rounded-lg border border-outline-variant">
                            <?= htmlspecialchars($dispute['evidence_text'] ?? 'No written evidence submitted.') ?>
                        </div>
                        <?php if ($dispute['resolved_at']): ?>
                            <div class="text-on-surface-variant text-[11px]">
                                Dispute Resolved on: <strong class="text-on-surface font-data-mono"><?= date('F d, Y H:i', strtotime($dispute['resolved_at'])) ?></strong>
                            </div>
                        <?php endif; ?>
                    </div>

                <?php endif; ?>
            </div>
        </div>

        <!-- RIGHT COLUMN: Transaction & Customer Details -->
        <div class="space-y-6">
            
            <!-- Disputed Amount Card -->
            <div class="bg-surface border border-outline-variant rounded-2xl p-6 shadow-sm text-center">
                <span class="font-label-caps text-xs font-bold text-on-surface-variant uppercase tracking-wider block mb-1">Disputed Amount</span>
                <div class="font-data-mono text-3xl font-black text-on-surface mb-2">
                    GH₵ <?= number_format($dispute['amount'], 2) ?>
                </div>
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-surface-container-high text-on-surface font-body-sm text-xs font-semibold">
                    <span class="material-symbols-outlined text-[16px] text-amber-500">info</span>
                    Reason: <strong class="capitalize"><?= str_replace('_', ' ', $dispute['reason']) ?></strong>
                </span>
            </div>

            <!-- Customer Details Card -->
            <div class="bg-surface border border-outline-variant rounded-2xl p-6 shadow-sm space-y-4">
                <h4 class="font-headline-lg text-sm font-bold text-on-surface border-b border-outline-variant pb-3">Customer Information</h4>
                
                <div class="space-y-3 font-body-sm text-xs">
                    <div>
                        <span class="text-on-surface-variant text-[11px] block">Customer Name</span>
                        <span class="font-bold text-on-surface text-sm"><?= htmlspecialchars($dispute['customer_name'] ?? 'Guest Customer') ?></span>
                    </div>

                    <div>
                        <span class="text-on-surface-variant text-[11px] block">Email Address</span>
                        <span class="font-data-mono font-semibold text-on-surface"><?= htmlspecialchars($dispute['customer_email'] ?? 'n/a') ?></span>
                    </div>

                    <?php if (!empty($dispute['customer_phone'])): ?>
                        <div>
                            <span class="text-on-surface-variant text-[11px] block">Phone Number</span>
                            <span class="font-data-mono font-semibold text-on-surface"><?= htmlspecialchars($dispute['customer_phone']) ?></span>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Associated Transaction Details -->
            <div class="bg-surface border border-outline-variant rounded-2xl p-6 shadow-sm space-y-4">
                <h4 class="font-headline-lg text-sm font-bold text-on-surface border-b border-outline-variant pb-3">Original Transaction</h4>
                
                <div class="space-y-3 font-body-sm text-xs">
                    <div class="flex justify-between items-center">
                        <span class="text-on-surface-variant">Reference</span>
                        <a href="/transactions?search=<?= urlencode($dispute['transaction_reference']) ?>" class="font-data-mono font-bold text-primary hover:underline">
                            <?= htmlspecialchars($dispute['transaction_reference']) ?>
                        </a>
                    </div>

                    <div class="flex justify-between items-center">
                        <span class="text-on-surface-variant">Payment Method</span>
                        <span class="font-bold text-on-surface capitalize"><?= str_replace('_', ' ', $dispute['payment_method']) ?></span>
                    </div>

                    <div class="flex justify-between items-center">
                        <span class="text-on-surface-variant">Transaction Date</span>
                        <span class="font-data-mono text-on-surface"><?= date('M d, Y H:i', strtotime($dispute['transaction_date'])) ?></span>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
