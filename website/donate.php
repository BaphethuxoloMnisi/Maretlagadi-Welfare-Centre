<?php

// ============================================================
// DONATE PAGE
// Maretlagadi Welfare Centre
// ============================================================

// Database connection
require_once 'includes/db.php';

// Paystack configuration
require_once 'includes/paystack_config.php';

// Page title
$pageTitle = "Donate";

// Website header
include 'includes/header.php';

?>

<!-- ============================================================
     DONATION SECTION
     ============================================================ -->

<section class="py-5">

    <div class="container" style="max-width: 640px;">

        <!-- ====================================================
             PAGE HEADING
             ==================================================== -->

        <h1 class="fw-bold text-center">
            Make a Donation
        </h1>

        <p class="text-secondary text-center mt-2">
            Your contribution helps us continue our mission to
            empower and uplift our community.
            All donations are processed securely through Paystack.
        </p>

        <!-- ====================================================
             LISTEN BUTTONS
             ==================================================== -->

        <div class="text-center mt-3">

            <button
                type="button"
                class="btn btn-outline-dark rounded-pill px-4"
                data-listen-toggle
                aria-pressed="false"
            >
                🔊 Hear how donations work
            </button>

            <button
                type="button"
                class="btn btn-link text-secondary"
                data-listen-stop
            >
                Stop
            </button>

        </div>

        <!-- ====================================================
             ALERT AREA
             ==================================================== -->

        <div id="formAlert"></div>

        <!-- ====================================================
             DONATION FORM
             ==================================================== -->

        <form
            id="donateForm"
            class="border rounded-4 p-4 mt-4"
        >

            <!-- ==================================================
                 AMOUNT SELECTION
                 ================================================== -->

            <h5 class="fw-semibold mb-3">
                Choose an amount
            </h5>

            <div class="d-flex gap-2 flex-wrap mb-3">

                <!-- R100 -->
                <button
                    type="button"
                    class="btn btn-outline-dark rounded-pill px-4 amount-btn"
                    data-amount="100"
                >
                    R100
                </button>

                <!-- R250 -->
                <button
                    type="button"
                    class="btn btn-outline-dark rounded-pill px-4 amount-btn"
                    data-amount="250"
                >
                    R250
                </button>

                <!-- R500 -->
                <button
                    type="button"
                    class="btn btn-outline-dark rounded-pill px-4 amount-btn"
                    data-amount="500"
                >
                    R500
                </button>

                <!-- Custom amount -->
                <input
                    type="number"
                    id="otherAmount"
                    class="form-control"
                    style="max-width:160px;"
                    placeholder="Other (R)"
                    min="1"
                    step="1"
                >

            </div>

            <!-- ==================================================
                 HIDDEN AMOUNT FIELD

                 JavaScript puts the selected amount here.
                 ================================================== -->

            <input
                type="hidden"
                id="amountField"
                required
            >

            <!-- ==================================================
                 DONOR NAME
                 ================================================== -->

            <div class="mb-3">

                <label
                    for="donorName"
                    class="form-label fw-semibold"
                >
                    Your Name
                </label>

                <input
                    type="text"
                    id="donorName"
                    class="form-control"
                    placeholder="Leave blank to donate anonymously"
                >

            </div>

            <!-- ==================================================
                 DONOR EMAIL
                 ================================================== -->

            <div class="mb-3">

                <label
                    for="donorEmail"
                    class="form-label fw-semibold"
                >
                    Email Address
                    <span class="text-danger">*</span>
                </label>

                <input
                    type="email"
                    id="donorEmail"
                    class="form-control"
                    required
                    placeholder="Required by Paystack for your receipt"
                >

            </div>

            <!-- ==================================================
                 PAYMENT BUTTON
                 ================================================== -->

            <button
                type="submit"
                id="payBtn"
                class="btn btn-brand w-100 rounded-pill py-2"
            >
                Proceed to Secure Payment
            </button>

            <p class="text-secondary small mt-2 mb-0">
                You'll be taken to Paystack's secure checkout
                to complete your donation.
            </p>

        </form>

    </div>

</section>


<!-- ============================================================
     PAYSTACK JAVASCRIPT
     ============================================================ -->

<script src="https://js.paystack.co/v1/inline.js"></script>


<script>

document.addEventListener('DOMContentLoaded', function () {

    // ========================================================
    // GET HTML ELEMENTS
    // ========================================================

    const amountBtns = document.querySelectorAll('.amount-btn');

    const otherInput = document.getElementById('otherAmount');

    const amountField = document.getElementById('amountField');

    const form = document.getElementById('donateForm');

    const payBtn = document.getElementById('payBtn');

    const alertBox = document.getElementById('formAlert');


    // ========================================================
    // MAKE SURE PAYSTACK LOADED
    // ========================================================

    if (typeof PaystackPop === 'undefined') {

        showAlert(
            'danger',
            'Paystack could not be loaded. Please check your internet connection and try again.'
        );

        return;
    }


    // ========================================================
    // SHOW ALERT MESSAGE
    // ========================================================

    function showAlert(type, message) {

        alertBox.innerHTML = `
            <div class="alert alert-${type} mt-4">
                ${message}
            </div>
        `;

    }


    // ========================================================
    // SELECT PRESET AMOUNT
    // ========================================================

    amountBtns.forEach(function (button) {

        button.addEventListener('click', function () {

            // Remove selected state from all buttons
            amountBtns.forEach(function (btn) {

                btn.classList.remove(
                    'btn-brand',
                    'active'
                );

                btn.classList.add(
                    'btn-outline-dark'
                );

            });


            // Make clicked button selected
            this.classList.remove(
                'btn-outline-dark'
            );

            this.classList.add(
                'btn-brand',
                'active'
            );


            // Get selected amount
            const selectedAmount = this.dataset.amount;


            // Put amount into hidden field
            amountField.value = selectedAmount;


            // Clear custom amount
            otherInput.value = '';


            // Clear previous alert
            alertBox.innerHTML = '';

        });

    });


    // ========================================================
    // CUSTOM AMOUNT
    // ========================================================

    otherInput.addEventListener('input', function () {

        // Remove selected state from preset buttons
        amountBtns.forEach(function (btn) {

            btn.classList.remove(
                'btn-brand',
                'active'
            );

            btn.classList.add(
                'btn-outline-dark'
            );

        });


        // Put custom amount into hidden field
        amountField.value = this.value;


        // Clear previous alert
        alertBox.innerHTML = '';

    });


    // ========================================================
    // SUBMIT DONATION FORM
    // ========================================================

    form.addEventListener('submit', function (event) {

        // Stop normal form submission
        event.preventDefault();


        // Clear previous alerts
        alertBox.innerHTML = '';


        // ====================================================
        // GET FORM VALUES
        // ====================================================

        const amount = parseFloat(
            amountField.value
        );

        const email = document
            .getElementById('donorEmail')
            .value
            .trim();

        const name = document
            .getElementById('donorName')
            .value
            .trim();


        // ====================================================
        // VALIDATE AMOUNT
        // ====================================================

        if (
            isNaN(amount) ||
            amount <= 0
        ) {

            showAlert(
                'danger',
                'Please select or enter a donation amount.'
            );

            return;
        }


        // ====================================================
        // VALIDATE EMAIL
        // ====================================================

        if (!email) {

            showAlert(
                'danger',
                'Please enter your email address.'
            );

            return;
        }


        // ====================================================
        // VALIDATE EMAIL FORMAT
        // ====================================================

        const emailPattern =
            /^[^\s@]+@[^\s@]+\.[^\s@]+$/;


        if (!emailPattern.test(email)) {

            showAlert(
                'danger',
                'Please enter a valid email address.'
            );

            return;
        }


        // ====================================================
        // CHECK PAYSTACK
        // ====================================================

        if (typeof PaystackPop === 'undefined') {

            showAlert(
                'danger',
                'Paystack is not available. Please refresh the page and try again.'
            );

            return;
        }


        // ====================================================
        // SAVE ORIGINAL BUTTON TEXT
        // ====================================================

        const originalButtonText =
            payBtn.textContent;


        // Disable button
        payBtn.disabled = true;

        payBtn.textContent =
            'Opening secure checkout...';


        // ====================================================
        // CREATE UNIQUE PAYMENT REFERENCE
        // ====================================================

        const reference =
            'MWC_' +
            Date.now() +
            '_' +
            Math.floor(
                Math.random() * 100000
            );


        // ====================================================
        // INITIALIZE PAYSTACK
        // ====================================================

        let handler;

        try {

            handler = PaystackPop.setup({

                // ------------------------------------------------
                // PAYSTACK PUBLIC KEY
                // ------------------------------------------------

                key: '<?php echo htmlspecialchars(PAYSTACK_PUBLIC_KEY, ENT_QUOTES, 'UTF-8'); ?>',


                // ------------------------------------------------
                // CUSTOMER EMAIL
                // ------------------------------------------------

                email: email,


                // ------------------------------------------------
                // PAYMENT AMOUNT
                //
                // Paystack uses the smallest currency unit.
                // R100 = 10000
                // ------------------------------------------------

                amount: Math.round(
                    amount * 100
                ),


                // ------------------------------------------------
                // CURRENCY
                // ------------------------------------------------

                currency: 'ZAR',


                // ------------------------------------------------
                // PAYMENT REFERENCE
                // ------------------------------------------------

                ref: reference,


                // ------------------------------------------------
                // DONOR INFORMATION
                // ------------------------------------------------

                metadata: {

                    custom_fields: [

                        {
                            display_name: 'Donor Name',

                            variable_name: 'donor_name',

                            value:
                                name ||
                                'Anonymous'
                        }

                    ]

                },


                // =================================================
                // PAYMENT CALLBACK
                // =================================================

                callback: function (response) {

                    console.log(
                        'Paystack payment successful:',
                        response
                    );


                    // ---------------------------------------------
                    // Tell user verification is happening
                    // ---------------------------------------------

                    payBtn.textContent =
                        'Verifying payment...';


                    // ---------------------------------------------
                    // Send payment reference to PHP
                    // ---------------------------------------------

                    fetch(
                        'verify_donation.php',
                        {

                            method: 'POST',

                            headers: {
                                'Content-Type':
                                    'application/json'
                            },

                            body: JSON.stringify({

                                reference:
                                    response.reference,

                                name:
                                    name,

                                email:
                                    email,

                                amount:
                                    amount

                            })

                        }
                    )


                    // ---------------------------------------------
                    // Convert response to JSON
                    // ---------------------------------------------

.then(function (res) {

    console.log('Verification HTTP status:', res.status);

    return res.text();

})

.then(function (text) {

    console.log('Raw verification response:', text);

    let data;

    try {

        data = JSON.parse(text);

    } catch (error) {

        console.error(
            'PHP did not return valid JSON:',
            error
        );

        throw new Error(
            'Server returned an invalid response: ' + text
        );

    }

    console.log(
        'Verification response:',
        data
    );

    if (data.success) {

        window.location.href =
            'donate-success.php?ref=' +
            encodeURIComponent(
                response.reference
            );

    } else {

        showAlert(
            'danger',
            data.message ||
            'Payment could not be verified.'
        );

        payBtn.disabled = false;

        payBtn.textContent =
            originalButtonText;
    }

})


                    // ---------------------------------------------
                    // Process verification result
                    // ---------------------------------------------

                    .then(function (data) {

                        console.log(
                            'Verification response:',
                            data
                        );


                        if (data.success) {

                            // -------------------------------------
                            // Payment verified and saved
                            // -------------------------------------

                            window.location.href =
                                'donate-success.php?ref=' +
                                encodeURIComponent(
                                    response.reference
                                );

                        } else {

                            // -------------------------------------
                            // Verification failed
                            // -------------------------------------

                            showAlert(
                                'danger',
                                data.message ||
                                'Payment could not be verified. Please contact us if you were charged.'
                            );


                            // Re-enable button
                            payBtn.disabled = false;

                            payBtn.textContent =
                                originalButtonText;

                        }

                    })


                    // ---------------------------------------------
                    // Handle verification/network error
                    // ---------------------------------------------

                    .catch(function (error) {

                        console.error(
                            'Donation verification error:',
                            error
                        );


                        /*showAlert(
                            'danger',
                            'Something went wrong verifying your payment. Please contact us if you were charged.'
                        );*/


                        // Re-enable button
                        payBtn.disabled = false;

                        payBtn.textContent =
                            originalButtonText;

                    });

                },


                // =================================================
                // PAYSTACK CHECKOUT CLOSED
                // =================================================

                onClose: function () {

                    console.log(
                        'Paystack checkout closed.'
                    );


                    // Re-enable button
                    payBtn.disabled = false;

                    payBtn.textContent =
                        originalButtonText;

                }

            });


            // ====================================================
            // OPEN PAYSTACK CHECKOUT
            // ====================================================

            handler.openIframe();


        } catch (error) {

            // ====================================================
            // HANDLE PAYSTACK INITIALIZATION ERROR
            // ====================================================

            console.error(
                'Paystack initialization error:',
                error
            );


            showAlert(
                'danger',
                'Unable to open Paystack checkout. Please check your Paystack configuration.'
            );


            // Re-enable button
            payBtn.disabled = false;

            payBtn.textContent =
                originalButtonText;

        }

    });

});

</script>


<?php

// ============================================================
// WEBSITE FOOTER
// ============================================================

include 'includes/footer.php';

?>
