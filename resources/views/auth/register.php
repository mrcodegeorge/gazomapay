<main class="w-full max-w-[440px] bg-surface-container-lowest border border-outline-variant rounded-xl shadow-[0_10px_15px_-3px_rgba(15,23,42,0.08)] p-8">
    <!-- Header -->
    <div class="text-center mb-8">
        <h1 class="font-headline-md text-headline-md text-on-surface mb-2 tracking-tight">Gazoma Pay</h1>
        <p class="font-body-sm text-body-sm text-on-surface-variant">Register your business account</p>
    </div>

    <?php if ($flashError = Response::getFlash('error')): ?>
        <div class="mb-6 p-3 bg-error-container text-on-error-container text-body-sm rounded-lg text-center font-medium border border-error/20 flex items-center justify-center gap-2">
            <span class="material-symbols-outlined text-error" style="font-size: 18px;">error</span>
            <span><?= htmlspecialchars($flashError) ?></span>
        </div>
    <?php endif; ?>

    <!-- Form -->
    <form class="space-y-5" action="/register" method="POST">
        <input type="hidden" name="csrf_token" value="<?= Auth::generateCsrfToken() ?>">

        <!-- Business Name -->
        <div>
            <label class="block font-body-sm text-body-sm text-on-surface mb-1" for="company_name">Company / Legal Entity Name</label>
            <input class="w-full px-3 py-2 border border-outline-variant rounded bg-surface-container-lowest text-on-surface font-body-md text-body-md focus:outline-none focus:ring-2 focus:ring-[#3B82F6] focus:border-transparent transition-shadow duration-200" id="company_name" name="company_name" placeholder="Gazoma Tech Ltd" required type="text"/>
        </div>

        <!-- Full Name -->
        <div>
            <label class="block font-body-sm text-body-sm text-on-surface mb-1" for="name">Account Representative Name</label>
            <input class="w-full px-3 py-2 border border-outline-variant rounded bg-surface-container-lowest text-on-surface font-body-md text-body-md focus:outline-none focus:ring-2 focus:ring-[#3B82F6] focus:border-transparent transition-shadow duration-200" id="name" name="name" placeholder="John Mensah" required type="text"/>
        </div>

        <!-- Email Field -->
        <div>
            <label class="block font-body-sm text-body-sm text-on-surface mb-1" for="email">Business Email</label>
            <input class="w-full px-3 py-2 border border-outline-variant rounded bg-surface-container-lowest text-on-surface font-body-md text-body-md focus:outline-none focus:ring-2 focus:ring-[#3B82F6] focus:border-transparent transition-shadow duration-200" id="email" name="email" placeholder="you@company.com" required type="email"/>
        </div>

        <!-- Password Field -->
        <div>
            <label class="block font-body-sm text-body-sm text-on-surface mb-1" for="password">Password</label>
            <input class="w-full px-3 py-2 border border-outline-variant rounded bg-surface-container-lowest text-on-surface font-body-md text-body-md focus:outline-none focus:ring-2 focus:ring-[#3B82F6] focus:border-transparent transition-shadow duration-200" id="password" name="password" placeholder="••••••••" required type="password"/>
        </div>

        <!-- Submit Button -->
        <button class="w-full bg-primary-container text-on-primary font-body-md text-body-md font-medium py-3 rounded hover:bg-surface-tint transition-colors duration-200 cursor-pointer mt-2" type="submit">
            Create Merchant Account
        </button>
    </form>

    <!-- Footer Links -->
    <div class="mt-8 text-center space-y-4">
        <p class="font-body-sm text-body-sm text-on-surface-variant">
            Already registered? <a class="text-secondary font-medium hover:text-secondary-container transition-colors" href="/login">Sign in here</a>
        </p>
        <div class="flex items-center justify-center gap-2 text-on-surface-variant">
            <span class="material-symbols-outlined" style="font-size: 16px;">verified_user</span>
            <span class="font-label-caps text-label-caps uppercase tracking-wider">PCI-DSS Compliant Infrastructure</span>
        </div>
    </div>
</main>
