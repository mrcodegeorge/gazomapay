<div class="checkout-card">
    <div class="checkout-header">
        <div class="checkout-merchant-name"><?= htmlspecialchars($link['merchant_name']) ?></div>
        <h2 style="font-size: 20px; font-weight: 700; margin-top: 4px;"><?= htmlspecialchars($link['name']) ?></h2>
        <div class="checkout-amount">GH₵ <?= number_format($link['amount'], 2) ?></div>
        <p style="font-size: 12px; color: #94a3b8; margin-top: 4px;"><?= htmlspecialchars($link['description'] ?: 'Secure Instant Payment') ?></p>
    </div>

    <div style="padding: 28px;" id="checkoutFormContainer">
        <form id="paymentForm" onsubmit="handleSandboxPayment(event, '<?= $link['token'] ?>')">
            <div class="form-group">
                <label class="form-label">Full Name</label>
                <input type="text" id="cust_name" class="form-control" placeholder="Ama Serwaa" required>
            </div>

            <div class="form-group">
                <label class="form-label">Email Address</label>
                <input type="email" id="cust_email" class="form-control" placeholder="ama.serwaa@example.com" required>
            </div>

            <div class="form-group">
                <label class="form-label">Phone Number (Mobile Money / SMS Receipt)</label>
                <input type="tel" id="cust_phone" class="form-control" placeholder="+233 24 000 0000" required>
            </div>

            <div class="form-group">
                <label class="form-label">Select Payment Method</label>
                <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 10px; margin-top: 8px;">
                    <label style="border: 2px solid var(--primary-blue); border-radius: 8px; padding: 10px; cursor: pointer; text-align: center; font-size: 13px; font-weight: 600; background: #eff6ff;">
                        <input type="radio" name="pay_method" value="card" checked style="margin-right: 6px;"> Card
                    </label>
                    <label style="border: 1px solid var(--border-color); border-radius: 8px; padding: 10px; cursor: pointer; text-align: center; font-size: 13px; font-weight: 600;">
                        <input type="radio" name="pay_method" value="mobile_money" style="margin-right: 6px;"> Mobile Money
                    </label>
                    <label style="border: 1px solid var(--border-color); border-radius: 8px; padding: 10px; cursor: pointer; text-align: center; font-size: 13px; font-weight: 600;">
                        <input type="radio" name="pay_method" value="bank_transfer" style="margin-right: 6px;"> Bank Transfer
                    </label>
                    <label style="border: 1px solid var(--border-color); border-radius: 8px; padding: 10px; cursor: pointer; text-align: center; font-size: 13px; font-weight: 600;">
                        <input type="radio" name="pay_method" value="wallet" style="margin-right: 6px;"> Gazoma Wallet
                    </label>
                </div>
            </div>

            <button type="submit" id="payBtn" class="btn btn-primary" style="width: 100%; padding: 14px; font-size: 16px; margin-top: 12px;">
                Pay GH₵ <?= number_format($link['amount'], 2) ?>
            </button>
        </form>

        <div style="display: flex; align-items: center; justify-content: center; gap: 8px; margin-top: 20px; font-size: 12px; color: var(--text-muted);">
            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
            <span>Secured by Gazoma Pay Infrastructure (256-bit Encryption)</span>
        </div>
    </div>

    <!-- Success Confirmation View -->
    <div style="padding: 40px 28px; text-align: center; display: none;" id="successContainer">
        <div style="width: 60px; height: 60px; background: #dcfce7; color: #16a34a; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px auto;">
            <svg width="32" height="32" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
        </div>
        <h3 style="font-size: 22px; font-weight: 800; color: #0f172a;">Payment Successful!</h3>
        <p style="color: var(--text-muted); font-size: 14px; margin-top: 6px;">Thank you for your payment.</p>
        
        <div style="background: #f8fafc; border: 1px solid var(--border-color); border-radius: 12px; padding: 16px; margin: 20px 0; text-align: left; font-size: 13px;">
            <div style="display: flex; justify-content: space-between; margin-bottom: 6px;">
                <span>Reference:</span> <strong id="res_ref">GZM_00000000</strong>
            </div>
            <div style="display: flex; justify-content: space-between; margin-bottom: 6px;">
                <span>Amount Paid:</span> <strong id="res_amt">GH₵ 0.00</strong>
            </div>
            <div style="display: flex; justify-content: space-between;">
                <span>Status:</span> <span class="badge badge-success">Successful</span>
            </div>
        </div>

        <button onclick="window.print()" class="btn btn-outline" style="width: 100%;">Print Receipt</button>
    </div>
</div>

<script>
function handleSandboxPayment(e, token) {
    e.preventDefault();
    const btn = document.getElementById('payBtn');
    btn.disabled = true;
    btn.innerHTML = 'Processing Payment...';

    const formData = new FormData();
    formData.append('customer_name', document.getElementById('cust_name').value);
    formData.append('customer_email', document.getElementById('cust_email').value);
    formData.append('customer_phone', document.getElementById('cust_phone').value);
    
    const selectedMethod = document.querySelector('input[name="pay_method"]:checked').value;
    formData.append('payment_method', selectedMethod);

    setTimeout(() => {
        fetch('/pay/' + token, {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                document.getElementById('checkoutFormContainer').style.display = 'none';
                document.getElementById('successContainer').style.display = 'block';
                document.getElementById('res_ref').innerText = data.reference;
                document.getElementById('res_amt').innerText = 'GH₵ ' + parseFloat(data.amount).toFixed(2);
            } else {
                alert('Payment Failed: ' + data.message);
                btn.disabled = false;
                btn.innerHTML = 'Pay Now';
            }
        })
        .catch(err => {
            alert('Error processing payment.');
            btn.disabled = false;
            btn.innerHTML = 'Pay Now';
        });
    }, 1200);
}
</script>
