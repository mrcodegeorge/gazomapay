<?php

class PdfGenerator {
    public static function renderInvoiceHtml(array $invoice, array $items, array $merchant): string {
        $logo = $merchant['logo'] ?: '';
        $mchName = htmlspecialchars($merchant['name']);
        $mchEmail = htmlspecialchars($merchant['email']);
        $mchAddress = nl2br(htmlspecialchars($merchant['address'] ?: 'Accra, Ghana'));
        
        $invNum = htmlspecialchars($invoice['invoice_number']);
        $custName = htmlspecialchars($invoice['customer_name']);
        $custEmail = htmlspecialchars($invoice['customer_email']);
        $date = date('F j, Y', strtotime($invoice['created_at']));
        $dueDate = date('F j, Y', strtotime($invoice['due_date']));
        $status = strtoupper($invoice['status']);

        $itemsHtml = '';
        foreach ($items as $item) {
            $desc = htmlspecialchars($item['description']);
            $qty = (int)$item['quantity'];
            $price = number_format((float)$item['unit_price'], 2);
            $amt = number_format((float)$item['amount'], 2);
            $itemsHtml .= "<tr>
                <td style='padding: 12px; border-bottom: 1px solid #e2e8f0;'>{$desc}</td>
                <td style='padding: 12px; border-bottom: 1px solid #e2e8f0; text-align: center;'>{$qty}</td>
                <td style='padding: 12px; border-bottom: 1px solid #e2e8f0; text-align: right;'>GH₵ {$price}</td>
                <td style='padding: 12px; border-bottom: 1px solid #e2e8f0; text-align: right; font-weight: 600;'>GH₵ {$amt}</td>
            </tr>";
        }

        $subtotal = number_format((float)$invoice['subtotal'], 2);
        $tax = number_format((float)$invoice['tax'], 2);
        $total = number_format((float)$invoice['total'], 2);

        return "<!DOCTYPE html>
<html>
<head>
    <meta charset='utf-8'>
    <title>Invoice {$invNum} - Gazoma Pay</title>
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; color: #1e293b; background: #fff; margin: 0; padding: 40px; }
        .header { display: flex; justify-content: space-between; align-items: flex-start; border-bottom: 2px solid #2563eb; padding-bottom: 20px; margin-bottom: 30px; }
        .brand { font-size: 24px; font-weight: 800; color: #0f172a; }
        .brand span { color: #2563eb; }
        .inv-title { font-size: 28px; font-weight: 700; color: #2563eb; text-align: right; }
        .details { display: flex; justify-content: space-between; margin-bottom: 30px; }
        .box { width: 48%; }
        .box h4 { margin: 0 0 8px 0; color: #64748b; text-transform: uppercase; font-size: 11px; letter-spacing: 0.5px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
        th { background: #f8fafc; padding: 12px; text-align: left; font-size: 12px; text-transform: uppercase; color: #64748b; border-bottom: 2px solid #e2e8f0; }
        .totals { width: 300px; margin-left: auto; }
        .totals-row { display: flex; justify-content: space-between; padding: 8px 0; font-size: 14px; }
        .grand-total { font-size: 18px; font-weight: 700; color: #2563eb; border-top: 2px solid #e2e8f0; padding-top: 12px; margin-top: 8px; }
        .footer { text-align: center; margin-top: 50px; font-size: 12px; color: #94a3b8; border-top: 1px solid #e2e8f0; padding-top: 20px; }
    </style>
</head>
<body onload='window.print()'>
    <div class='header'>
        <div>
            <div class='brand'>Gazoma<span>Pay</span></div>
            <p style='margin: 4px 0 0 0; color: #64748b; font-size: 13px;'>{$mchName}<br>{$mchEmail}</p>
        </div>
        <div>
            <div class='inv-title'>INVOICE</div>
            <p style='margin: 4px 0 0 0; text-align: right; font-weight: 600;'>#{$invNum}</p>
            <p style='margin: 2px 0 0 0; text-align: right; color: #16a34a; font-weight: 700;'>Status: {$status}</p>
        </div>
    </div>

    <div class='details'>
        <div class='box'>
            <h4>Billed To</h4>
            <p style='margin: 0; font-weight: 600; font-size: 16px;'>{$custName}</p>
            <p style='margin: 4px 0 0 0; color: #64748b;'>{$custEmail}</p>
        </div>
        <div class='box' style='text-align: right;'>
            <h4>Invoice Date</h4>
            <p style='margin: 0; font-weight: 600;'>{$date}</p>
            <h4 style='margin-top: 12px;'>Due Date</h4>
            <p style='margin: 0; font-weight: 600; color: #dc2626;'>{$dueDate}</p>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Description</th>
                <th style='text-align: center;'>Qty</th>
                <th style='text-align: right;'>Unit Price</th>
                <th style='text-align: right;'>Amount</th>
            </tr>
        </thead>
        <tbody>
            {$itemsHtml}
        </tbody>
    </table>

    <div class='totals'>
        <div class='totals-row'><span>Subtotal:</span> <span>GH₵ {$subtotal}</span></div>
        <div class='totals-row'><span>Tax (0%):</span> <span>GH₵ {$tax}</span></div>
        <div class='totals-row grand-total'><span>Total Due:</span> <span>GH₵ {$total}</span></div>
    </div>

    <div class='footer'>
        <p>Thank you for choosing {$mchName}. Powered by Gazoma Pay Infrastructure.</p>
    </div>
</body>
</html>";
    }
}
