<?php

interface PaymentProviderInterface {
    /**
     * Initialize a payment session or return checkout details.
     */
    public function initializePayment(array $params): array;

    /**
     * Execute a direct payment charge (Card or Mobile Money).
     */
    public function charge(array $params): array;

    /**
     * Verify a transaction status by reference.
     */
    public function verifyPayment(string $reference): array;

    /**
     * Execute full or partial refund.
     */
    public function refund(string $reference, float $amount): array;

    /**
     * Fetch raw provider transaction status.
     */
    public function getTransactionStatus(string $reference): array;

    /**
     * Process & validate incoming provider webhook payload.
     */
    public function handleWebhook(array $headers, string $rawPayload): array;

    /**
     * Return provider string identifier (sandbox, paystack, hubtel).
     */
    public function getProviderName(): string;
}
