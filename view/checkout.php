<?php
session_start();
require_once __DIR__ . '/../settings/core.php';
require_once __DIR__ . '/../controllers/cart_controller.php';

// Check if user is logged in
if (!isset($_SESSION['customer_id'])) {
    header('Location: ../login/login.php');
    exit;
}

$ipAddress = $_SERVER['REMOTE_ADDR'];
$customerId = $_SESSION['customer_id'];

// Get cart items
$cartItems = get_cart_items_ctr($ipAddress, $customerId);
$cartTotal = get_cart_total_ctr($ipAddress, $customerId);
$cartCount = count($cartItems);

// Redirect if cart is empty
if (empty($cartItems)) {
    header('Location: cart.php');
    exit;
}

// Get customer info
require_once __DIR__ . '/../controllers/customer_controller.php';
$customer = get_customer_by_id_ctr($customerId);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - SeamLink</title>
    <link rel="stylesheet" href="../css/app.css">
    <style>
        .form-label {
            display: block;
            margin-bottom: 6px;
            font-weight: 500;
            font-size: 0.875rem;
            color: var(--gray-700);
        }
        .form-group {
            margin-bottom: 0;
        }
        .form-input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(25, 135, 84, 0.1);
        }
    </style>
</head>
<body>
    <?php include __DIR__ . '/includes/menu.php'; ?>

    <div class="page-header">
        <div class="container">
            <h1>Checkout</h1>
            <p>Complete your order</p>
        </div>
    </div>

    <div class="container" style="margin-top: 40px; margin-bottom: 60px;">
        <div class="cart-container">
            <!-- Checkout Form -->
            <div>
                <div class="card">
                    <div class="card-header" style="background: var(--gray-50); border-bottom: 1px solid var(--gray-200); padding: 16px 20px;">
                        <h3 style="font-size: 1.125rem; font-weight: 600; margin: 0; color: var(--gray-900);">Shipping Information</h3>
                    </div>
                    <div class="card-body" style="padding: 24px;">
                        <form id="checkout-form" method="POST" action="../actions/checkout_action.php">
                            <div style="display: grid; gap: 20px;">
                                <div class="form-group">
                                    <label for="shipping_name" class="form-label">Full Name</label>
                                    <input type="text" id="shipping_name" name="shipping_name" 
                                           value="<?= htmlspecialchars($customer['customer_name'] ?? '') ?>"
                                           required class="form-input"
                                           style="width: 100%; padding: 10px 14px; border: 1px solid var(--gray-300); border-radius: var(--radius-md); font-size: 0.9375rem;">
                                </div>

                                <div class="form-group">
                                    <label for="shipping_contact" class="form-label">Phone Number</label>
                                    <input type="tel" id="shipping_contact" name="shipping_contact" 
                                           value="<?= htmlspecialchars($customer['customer_contact'] ?? '') ?>"
                                           required class="form-input"
                                           style="width: 100%; padding: 10px 14px; border: 1px solid var(--gray-300); border-radius: var(--radius-md); font-size: 0.9375rem;">
                                </div>

                                <div class="form-group">
                                    <label for="shipping_address" class="form-label">Street Address</label>
                                    <textarea id="shipping_address" name="shipping_address" 
                                              required class="form-input" rows="3"
                                              style="width: 100%; padding: 10px 14px; border: 1px solid var(--gray-300); border-radius: var(--radius-md); font-size: 0.9375rem; resize: vertical;"><?= htmlspecialchars($customer['customer_address'] ?? '') ?></textarea>
                                </div>

                                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                                    <div class="form-group">
                                        <label for="shipping_city" class="form-label">City</label>
                                        <input type="text" id="shipping_city" name="shipping_city" 
                                               value="<?= htmlspecialchars($customer['customer_city'] ?? '') ?>"
                                               required class="form-input"
                                               style="width: 100%; padding: 10px 14px; border: 1px solid var(--gray-300); border-radius: var(--radius-md); font-size: 0.9375rem;">
                                    </div>

                                    <div class="form-group">
                                        <label for="shipping_country" class="form-label">Country</label>
                                        <input type="text" id="shipping_country" name="shipping_country" 
                                               value="<?= htmlspecialchars($customer['customer_country'] ?? '') ?>"
                                               required class="form-input"
                                               style="width: 100%; padding: 10px 14px; border: 1px solid var(--gray-300); border-radius: var(--radius-md); font-size: 0.9375rem;">
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Order Summary -->
            <div class="cart-summary">
                <div class="card">
                    <div class="card-header" style="background: var(--gray-50); border-bottom: 1px solid var(--gray-200); padding: 16px 20px;">
                        <h3 style="font-size: 1.125rem; font-weight: 600; margin: 0; color: var(--gray-900);">Order Summary</h3>
                    </div>
                    <div class="card-body" style="padding: 24px;">
                        <div style="margin-bottom: 20px;">
                            <?php foreach ($cartItems as $item): ?>
                                <div style="display: flex; justify-content: space-between; padding: 12px 0; border-bottom: 1px solid var(--gray-200);">
                                    <div style="flex: 1;">
                                        <div style="font-weight: 500; margin-bottom: 4px;"><?= htmlspecialchars($item['product_title']) ?></div>
                                        <div style="font-size: 0.875rem; color: var(--gray-600);">Qty: <?= $item['qty'] ?></div>
                                    </div>
                                    <div style="font-weight: 600;">
                                        GH₵ <?= number_format($item['product_price'] * $item['qty'], 2) ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <div class="summary-divider"></div>
                        
                        <div class="summary-row">
                            <span>Subtotal</span>
                            <span>GH₵ <?= number_format($cartTotal, 2) ?></span>
                        </div>
                        <div class="summary-row">
                            <span>Shipping</span>
                            <span>Free</span>
                        </div>
                        <div class="summary-divider"></div>
                        <div class="summary-row summary-total">
                            <span>Total</span>
                            <span id="totalAmount">GH₵ <?= number_format($cartTotal, 2) ?></span>
                        </div>

                        <button type="button" id="proceedToCheckout" class="btn btn-primary btn-block" style="margin-top: 24px;">
                            Simulate Payment
                        </button>
                        
                        <a href="cart.php" class="btn btn-outline-secondary btn-block" style="margin-top: 12px;">
                            Back to Cart
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Payment Modal (will be created by checkout.js) -->
    <div id="paymentModal" class="modal" style="display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; overflow: auto; background-color: rgba(0,0,0,0.4);">
        <div class="modal-content" style="background-color: #fefefe; margin: 10% auto; padding: 0; border-radius: 8px; width: 90%; max-width: 500px; box-shadow: 0 4px 20px rgba(0,0,0,0.2);">
            <div style="padding: 24px; border-bottom: 1px solid #ddd;">
                <h2 style="margin: 0; font-size: 24px; color: #333;">Simulate Payment</h2>
            </div>
            <div style="padding: 24px;">
                <p style="color: #666; margin-bottom: 20px;">This is a simulated payment for demonstration purposes.</p>
                <div class="payment-summary" style="background: #f9f9f9; padding: 16px; border-radius: 6px; margin-bottom: 24px;">
                    <p style="margin: 8px 0;"><strong>Total Amount:</strong> <span id="modalTotal" style="font-size: 20px; color: #28a745;">GH₵ <?= number_format($cartTotal, 2) ?></span></p>
                    <p style="margin: 8px 0;"><strong>Currency:</strong> GHS</p>
                    <p style="margin: 8px 0;"><strong>Items:</strong> <?= array_sum(array_column($cartItems, 'qty')) ?></p>
                </div>
                <div id="paymentMessage" style="margin-bottom: 16px;"></div>
                <div class="modal-actions" style="display: flex; gap: 12px;">
                    <button type="button" id="confirmPayment" class="btn btn-success" style="flex: 1; padding: 12px;">
                        Yes, I've Paid
                    </button>
                    <button type="button" id="cancelPayment" class="btn btn-secondary" style="flex: 1; padding: 12px;">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="../js/checkout.js"></script>

    <style>
        .modal {
            animation: fadeIn 0.3s;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        
        .modal-content {
            animation: slideDown 0.3s;
        }
        
        @keyframes slideDown {
            from {
                transform: translateY(-50px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }
        
        .alert {
            padding: 12px 16px;
            border-radius: 6px;
            margin-bottom: 16px;
        }
        
        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .text-info {
            color: #0c5460;
            background: #d1ecf1;
            padding: 12px;
            border-radius: 6px;
        }
    </style>
</body>
</html>
