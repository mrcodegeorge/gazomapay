<?php

class Format {
    public static function currency(float $amount, string $symbol = 'GH₵'): string {
        return $symbol . ' ' . number_format($amount, 2, '.', ',');
    }

    public static function date(?string $dateStr, string $format = 'M j, Y h:i A'): string {
        if (!$dateStr) return 'N/A';
        return date($format, strtotime($dateStr));
    }

    public static function dateShort(?string $dateStr): string {
        if (!$dateStr) return 'N/A';
        return date('M j, Y', strtotime($dateStr));
    }

    public static function statusBadge(string $status): string {
        $statusLower = strtolower($status);
        $badgeClass = 'bg-gray-100 text-gray-700';
        $label = ucfirst($status);

        switch ($statusLower) {
            case 'successful':
            case 'completed':
            case 'active':
            case 'paid':
            case 'delivered':
                $badgeClass = 'badge-success'; // Soft green pill
                $label = ($statusLower === 'successful') ? 'Successful' : ucfirst($statusLower);
                break;
            case 'pending':
            case 'processing':
            case 'sent':
            case 'viewed':
            case 'retrying':
                $badgeClass = 'badge-warning'; // Soft yellow/orange pill
                break;
            case 'failed':
            case 'cancelled':
            case 'expired':
            case 'disabled':
            case 'overdue':
                $badgeClass = 'badge-danger'; // Soft red pill
                break;
            case 'inactive':
            case 'draft':
                $badgeClass = 'badge-secondary';
                break;
            case 'refunded':
                $badgeClass = 'badge-info';
                break;
        }

        return "<span class=\"badge {$badgeClass}\"><span class=\"badge-dot\"></span> {$label}</span>";
    }
}
