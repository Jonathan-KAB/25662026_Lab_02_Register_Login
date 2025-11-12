/**
 * Checkout JavaScript
 * Handles checkout page functionality including payment simulation
 */

$(document).ready(function() {
    // Handle "Proceed to Checkout" button click (show payment modal)
    $('#proceedToCheckout').on('click', function(e) {
        e.preventDefault();
        showPaymentModal();
    });

    // Handle payment confirmation
    $('#confirmPayment').on('click', function() {
        processCheckout();
    });

    // Handle payment cancellation
    $('#cancelPayment').on('click', function() {
        hidePaymentModal();
    });

    // Close modal when clicking outside
    $(window).on('click', function(e) {
        if ($(e.target).is('#paymentModal')) {
            hidePaymentModal();
        }
    });
});

/**
 * Show the payment simulation modal
 */
function showPaymentModal() {
    const modal = $('#paymentModal');
    
    if (modal.length === 0) {
        // Create modal if it doesn't exist
        const modalHTML = `
            <div id="paymentModal" class="modal" style="display: none;">
                <div class="modal-content">
                    <h2>Simulate Payment</h2>
                    <p>This is a simulated payment for demonstration purposes.</p>
                    <div class="payment-summary">
                        <p><strong>Total Amount:</strong> <span id="modalTotal"></span></p>
                        <p><strong>Currency:</strong> GHS</p>
                    </p>
                    <div class="modal-actions">
                        <button type="button" id="confirmPayment" class="btn btn-success">
                            Yes, I've Paid
                        </button>
                        <button type="button" id="cancelPayment" class="btn btn-secondary">
                            Cancel
                        </button>
                    </div>
                    <div id="paymentMessage"></div>
                </div>
            </div>
        `;
        $('body').append(modalHTML);
        
        // Re-attach event listeners
        $('#confirmPayment').on('click', processCheckout);
        $('#cancelPayment').on('click', hidePaymentModal);
    }
    
    // Update modal total from page
    const total = $('#cartTotal').text() || $('#totalAmount').text();
    $('#modalTotal').text(total);
    
    // Show modal
    $('#paymentModal').fadeIn(300);
}

/**
 * Hide the payment modal
 */
function hidePaymentModal() {
    $('#paymentModal').fadeOut(300);
    $('#paymentMessage').html('');
}

/**
 * Process the checkout
 */
function processCheckout() {
    // Disable confirm button to prevent double submission
    $('#confirmPayment').prop('disabled', true).text('Processing...');
    
    // Show loading message
    $('#paymentMessage').html('<p class="text-info">Processing your order...</p>');
    
    // Send AJAX request to process_checkout_action.php
    $.ajax({
        url: '../actions/process_checkout_action.php',
        method: 'POST',
        dataType: 'json',
        success: function(response) {
            if (response.status === 'success') {
                // Show success message
                $('#paymentMessage').html(
                    `<div class="alert alert-success">
                        <strong>Success!</strong> ${response.message}<br>
                        <strong>Order ID:</strong> ${response.order_id}<br>
                        <strong>Invoice Number:</strong> ${response.invoice_no}<br>
                        <strong>Total:</strong> GHS ${response.total_amount}
                    </div>`
                );
                
                // Redirect to order confirmation page after 2 seconds
                setTimeout(function() {
                    window.location.href = 'order_confirmation.php?order_id=' + response.order_id;
                }, 2000);
            } else {
                // Show error message
                $('#paymentMessage').html(
                    `<div class="alert alert-error">
                        <strong>Error!</strong> ${response.message}
                    </div>`
                );
                
                // Re-enable button
                $('#confirmPayment').prop('disabled', false).text('Yes, I\'ve Paid');
                
                // If redirect is needed (e.g., not logged in)
                if (response.redirect) {
                    setTimeout(function() {
                        window.location.href = response.redirect;
                    }, 2000);
                }
            }
        },
        error: function(xhr, status, error) {
            console.error('Checkout error:', error);
            console.error('Response:', xhr.responseText);
            
            $('#paymentMessage').html(
                `<div class="alert alert-error">
                    <strong>Error!</strong> An error occurred during checkout. Please try again.
                </div>`
            );
            
            // Re-enable button
            $('#confirmPayment').prop('disabled', false).text('Yes, I\'ve Paid');
        }
    });
}

/**
 * Handle smooth transitions between cart → checkout → confirmation
 */
function transitionToCheckout() {
    // Add fade-out animation
    $('body').addClass('fade-out');
    
    setTimeout(function() {
        window.location.href = 'checkout.php';
    }, 300);
}
