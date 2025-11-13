/**
 * Checkout JavaScript
 * Handles PayStack payment integration
 */

$(document).ready(function() {
    console.log('Checkout.js loaded - PayStack integration active');
    console.log('Cart data:', window.cartData);
    
    // Handle "Pay with Paystack" button click
    $('#proceedToCheckout').on('click', function(e) {
        e.preventDefault();
        console.log('Pay with Paystack button clicked');
        initiatePaystackPayment();
    });
});

/**
 * Initiate PayStack payment flow
 */
function initiatePaystackPayment() {
    console.log('Initiating PayStack payment...');
    
    // Disable button to prevent double clicks
    $('#proceedToCheckout').prop('disabled', true).text('Initializing payment...');
    
    // Get cart data from window object (passed from PHP)
    const cartData = window.cartData || {};
    const amount = cartData.total || 0;
    const email = cartData.customerEmail || '';
    
    console.log('Payment details:', { amount, email, currency: 'GHS' });
    
    if (!amount || amount <= 0) {
        console.error('Invalid cart total:', amount);
        alert('Invalid cart total');
        $('#proceedToCheckout').prop('disabled', false).text('Pay with Paystack');
        return;
    }
    
    if (!email) {
        console.error('Customer email is required');
        alert('Customer email is required');
        $('#proceedToCheckout').prop('disabled', false).text('Pay with Paystack');
        return;
    }
    
    // Store cart data in sessionStorage for verification later
    sessionStorage.setItem('checkout_cart', JSON.stringify({
        items: cartData.items || [],
        total: amount
    }));
    
    console.log('Calling PayStack initialization API...');
    
    // Call PayStack initialization endpoint
    $.ajax({
        url: '../actions/paystack_init_transaction.php',
        method: 'POST',
        contentType: 'application/json',
        data: JSON.stringify({
            amount: amount,
            email: email
        }),
        dataType: 'json',
        success: function(response) {
            console.log('PayStack init response:', response);
            
            if (response.status === 'success' && response.authorization_url) {
                console.log('Redirecting to PayStack:', response.authorization_url);
                // Redirect to PayStack payment page
                window.location.href = response.authorization_url;
            } else {
                // Show error message
                console.error('PayStack initialization failed:', response);
                alert('Failed to initialize payment: ' + (response.message || 'Unknown error'));
                $('#proceedToCheckout').prop('disabled', false).text('Pay with Paystack');
            }
        },
        error: function(xhr, status, error) {
            console.error('PayStack initialization error:', error);
            console.error('Status:', status);
            console.error('Response:', xhr.responseText);
            console.error('Status code:', xhr.status);
            
            let errorMessage = 'An error occurred while initializing payment. Please try again.';
            
            // Try to parse error response
            try {
                const errorResponse = JSON.parse(xhr.responseText);
                if (errorResponse.message) {
                    errorMessage = errorResponse.message;
                }
            } catch (e) {
                console.error('Could not parse error response:', e);
            }
            
            alert(errorMessage);
            $('#proceedToCheckout').prop('disabled', false).text('Pay with Paystack');
        }
    });
}
