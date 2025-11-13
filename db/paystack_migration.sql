-- PayStack Payment Integration Migration
-- Add PayStack-specific columns to payment table
-- Simple version for MySQL 5.7+ without prepared statements

-- Add payment_method column
ALTER TABLE payment 
ADD COLUMN payment_method VARCHAR(50) DEFAULT NULL COMMENT 'Payment method: paystack, cash, bank_transfer, etc.';

-- Add transaction_ref column
ALTER TABLE payment 
ADD COLUMN transaction_ref VARCHAR(100) DEFAULT NULL COMMENT 'Paystack transaction reference';

-- Add authorization_code column
ALTER TABLE payment 
ADD COLUMN authorization_code VARCHAR(100) DEFAULT NULL COMMENT 'Authorization code from payment gateway';

-- Add payment_channel column
ALTER TABLE payment 
ADD COLUMN payment_channel VARCHAR(50) DEFAULT NULL COMMENT 'Payment channel: card, mobile_money, etc.';

-- Add indexes for better query performance
ALTER TABLE payment ADD INDEX idx_transaction_ref (transaction_ref);
ALTER TABLE payment ADD INDEX idx_payment_method (payment_method);

