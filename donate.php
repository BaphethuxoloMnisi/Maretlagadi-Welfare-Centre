<?php
require_once 'includes/db.php';
require_once 'includes/paystack_config.php';
$pageTitle = "Donate";
include 'includes/header.php';
?>

<section class="py-5">
  <div class="container" style="max-width:640px;">
    <h1 class="fw-bold text-center">Make a Donation</h1>
    <p class="text-secondary text-center mt-2">
      Your contribution helps us continue our mission to empower and uplift our community.
      All donations are processed securely through Paystack.
    </p>

    <div id="formAlert"></div>

    <form id="donateForm" class="border rounded-4 p-4 mt-4">
      <h5 class="fw-semibold mb-3">Choose an amount</h5>
      <div class="d-flex gap-2 flex-wrap mb-3">
        <button type="button" class="btn btn-outline-dark rounded-pill px-4 amount-btn" data-amount="100">R100</button>
        <button type="button" class="btn btn-outline-dark rounded-pill px-4 amount-btn" data-amount="250">R250</button>
        <button type="button" class="btn btn-outline-dark rounded-pill px-4 amount-btn" data-amount="500">R500</button>
        <input type="number" id="otherAmount" class="form-control" style="max-width:160px;" placeholder="Other (R)" min="1" step="1">
      </div>
      <input type="hidden" id="amountField" required>

      <div class="mb-3">
        <label class="form-label fw-semibold">Your Name</label>
        <input type="text" id="donorName" class="form-control" placeholder="Leave blank to donate anonymously">
      </div>
      <div class="mb-3">
        <label class="form-label fw-semibold">Email Address <span class="text-danger">*</span></label>
        <input type="email" id="donorEmail" class="form-control" required placeholder="Required by Paystack for your receipt">
      </div>

      <button type="submit" id="payBtn" class="btn btn-brand w-100 rounded-pill py-2">Proceed to Secure Payment</button>
      <p class="text-secondary small mt-2 mb-0">You'll be taken to Paystack's secure checkout to complete your donation.</p>
    </form>
  </div>
</section>

<script src="https://js.paystack.co/v1/inline.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
  const amountBtns = document.querySelectorAll('.amount-btn');
  const otherInput = document.getElementById('otherAmount');
  const amountField = document.getElementById('amountField');
  const form = document.getElementById('donateForm');
  const payBtn = document.getElementById('payBtn');
  const alertBox = document.getElementById('formAlert');

  amountBtns.forEach(btn => {
    btn.addEventListener('click', function () {
      amountBtns.forEach(b => { b.classList.remove('btn-brand', 'active'); b.classList.add('btn-outline-dark'); });
      this.classList.remove('btn-outline-dark');
      this.classList.add('btn-brand', 'active');
      amountField.value = this.dataset.amount;
      otherInput.value = '';
    });
  });

  otherInput.addEventListener('input', function () {
    amountBtns.forEach(b => { b.classList.remove('btn-brand', 'active'); b.classList.add('btn-outline-dark'); });
    amountField.value = this.value;
  });

  function showAlert(type, message) {
    alertBox.innerHTML = `<div class="alert alert-${type} mt-4">${message}</div>`;
  }

  form.addEventListener('submit', function (e) {
    e.preventDefault();
    alertBox.innerHTML = '';

    const amount = parseFloat(amountField.value);
    const email = document.getElementById('donorEmail').value.trim();
    const name = document.getElementById('donorName').value.trim();

    if (!amount || amount <= 0) {
      showAlert('danger', 'Please select or enter a donation amount.');
      return;
    }
    if (!email) {
      showAlert('danger', 'Please enter your email address — Paystack needs it for your receipt.');
      return;
    }

    payBtn.disabled = true;
    payBtn.textContent = 'Opening secure checkout...';

    const reference = 'MWC_' + Date.now() + '_' + Math.floor(Math.random() * 100000);

    const handler = PaystackPop.setup({
      key: '<?php echo PAYSTACK_PUBLIC_KEY; ?>',
      email: email,
      amount: Math.round(amount * 100), // Paystack expects amount in cents
      currency: 'ZAR',
      ref: reference,
      metadata: {
        custom_fields: [
          { display_name: "Donor Name", variable_name: "donor_name", value: name || "Anonymous" }
        ]
      },
      callback: function (response) {
        // Payment succeeded on Paystack's side - now verify server-side before saving
        fetch('verify_donation.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({
            reference: response.reference,
            name: name,
            email: email,
            amount: amount
          })
        })
        .then(res => res.json())
        .then(data => {
          if (data.success) {
            window.location.href = 'donate-success.php?ref=' + encodeURIComponent(response.reference);
          } else {
            showAlert('danger', data.message || 'Payment could not be verified. Please contact us if you were charged.');
            payBtn.disabled = false;
            payBtn.textContent = 'Proceed to Secure Payment';
          }
        })
        .catch(() => {
          showAlert('danger', 'Something went wrong verifying your payment. Please contact us if you were charged.');
          payBtn.disabled = false;
          payBtn.textContent = 'Proceed to Secure Payment';
        });
      },
      onClose: function () {
        showAlert('warning', 'Payment window closed — your donation was not completed.');
        payBtn.disabled = false;
        payBtn.textContent = 'Proceed to Secure Payment';
      }
    });

    handler.openIframe();
  });
});
</script>

<?php include 'includes/footer.php'; ?>