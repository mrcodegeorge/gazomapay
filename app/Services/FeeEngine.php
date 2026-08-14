<?php

class FeeEngine {
    /**
     * Calculates fee breakdown based on gross amount and merchant/platform fee settings
     *
     * @param float $amount
     * @param float $feePercentage (default 1.5%)
     * @param float $fixedFee (default 0.50 GHS)
     * @return array [gross_amount, fee, net_amount]
     */
    public static function calculate(float $amount, float $feePercentage = 1.5, float $fixedFee = 0.50): array {
        $gross = round($amount, 2);
        $calculatedFee = round(($gross * ($feePercentage / 100)) + $fixedFee, 2);
        
        // Fee cannot exceed gross amount
        if ($calculatedFee > $gross) {
            $calculatedFee = $gross;
        }

        $net = round($gross - $calculatedFee, 2);

        return [
            'gross_amount' => $gross,
            'fee' => $calculatedFee,
            'net_amount' => $net,
            'currency' => 'GHS'
        ];
    }
}
