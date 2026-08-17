<?php

require_once __DIR__ . '/PaymentProviderInterface.php';
require_once __DIR__ . '/SandboxPaymentProvider.php';
require_once __DIR__ . '/PaystackPaymentProvider.php';
require_once __DIR__ . '/HubtelPaymentProvider.php';
require_once __DIR__ . '/../../config/env.php';

class PaymentProviderResolver {

    /**
     * Resolve the active payment provider.
     */
    public static function resolve(?string $requestedProvider = null): PaymentProviderInterface {
        $mode = Env::get('GAZOMA_PAYMENT_MODE', 'sandbox');

        if ($requestedProvider === 'paystack' && Env::get('PAYSTACK_ENABLED', false)) {
            return new PaystackPaymentProvider();
        }

        if ($requestedProvider === 'hubtel' && Env::get('HUBTEL_ENABLED', false)) {
            return new HubtelPaymentProvider();
        }

        // Default to Sandbox for test mode or fallback
        if ($mode === 'sandbox' || empty($requestedProvider) || $requestedProvider === 'sandbox') {
            return new SandboxPaymentProvider();
        }

        // Live mode fallback resolution
        if (Env::get('PAYSTACK_ENABLED', false)) {
            return new PaystackPaymentProvider();
        }

        return new SandboxPaymentProvider();
    }
}
