<?php

interface PaymentGatewayInterface {
    public function charge(array $paymentData): array;
    public function refund(string $transactionReference, float $amount, string $reason = ''): array;
    public function verify(string $transactionReference): array;
}
