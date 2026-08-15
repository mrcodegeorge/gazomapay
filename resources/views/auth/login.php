<main class="w-full max-w-[420px] bg-surface-container-lowest border border-outline-variant rounded-xl shadow-[0_10px_15px_-3px_rgba(15,23,42,0.08)] p-8">
    <!-- Header -->
    <div class="text-center mb-8">
        <h1 class="font-headline-md text-headline-md text-on-surface mb-2 tracking-tight">Gazoma Pay</h1>
        <p class="font-body-sm text-body-sm text-on-surface-variant">Sign in to manage your business.</p>
    </div>

    <?php if ($flashError = Response::getFlash('error')): ?>
        <div class="mb-6 p-3 bg-error-container text-on-error-container text-body-sm rounded-lg text-center font-medium border border-error/20 flex items-center justify-center gap-2">
            <span class="material-symbols-outlined text-error" style="font-size: 18px;">error</span>
            <span><?= htmlspecialchars($flashError) ?></span>
        </div>
    <?php endif; ?>

    <!-- Form -->
    <form class="space-y-6" action="/login" method="POST">
        <input type="hidden" name="csrf_token" value="<?= Auth::generateCsrfToken() ?>">

        <!-- Email Field -->
        <div>
            <label class="block font-body-sm text-body-sm text-on-surface mb-1" for="email">Business Email</label>
            <input class="w-full px-3 py-2 border border-outline-variant rounded bg-surface-container-lowest text-on-surface font-body-md text-body-md focus:outline-none focus:ring-2 focus:ring-[#3B82F6] focus:border-transparent transition-shadow duration-200" id="email" name="email" value="admin@gazomapay.com" placeholder="you@company.com" required type="email"/>
        </div>

        <!-- Password Field -->
        <div>
            <div class="flex items-center justify-between mb-1">
                <label class="block font-body-sm text-body-sm text-on-surface" for="password">Password</label>
                <a class="font-body-sm text-body-sm text-secondary hover:text-secondary-container transition-colors" href="#">Forgot Password?</a>
            </div>
            <input class="w-full px-3 py-2 border border-outline-variant rounded bg-surface-container-lowest text-on-surface font-body-md text-body-md focus:outline-none focus:ring-2 focus:ring-[#3B82F6] focus:border-transparent transition-shadow duration-200" id="password" name="password" value="password123" placeholder="••••••••" required type="password"/>
        </div>

        <!-- Submit Button -->
        <button class="w-full bg-primary-container text-on-primary font-body-md text-body-md font-medium py-3 rounded hover:bg-surface-tint transition-colors duration-200 cursor-pointer" type="submit">
            Sign in to Dashboard
        </button>
    </form>

    <!-- Footer Links & Trust Signals -->
    <div class="mt-8 text-center space-y-4">
        <p class="font-body-sm text-body-sm text-on-surface-variant">
            Don't have an account? <a class="text-secondary font-medium hover:text-secondary-container transition-colors" href="/register">Create an account</a>
        </p>
        <div class="flex items-center justify-center gap-2 text-on-surface-variant">
            <span class="material-symbols-outlined" style="font-size: 16px;">lock</span>
            <span class="font-label-caps text-label-caps uppercase tracking-wider">Secure &amp; Encrypted</span>
        </div>
    </div>
</main>
