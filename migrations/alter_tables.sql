-- Add session_id column to cart_items table to support guest users
ALTER TABLE cart_items 
ADD COLUMN session_id VARCHAR(255) NULL AFTER id,
ADD INDEX idx_cart_items_session (session_id);

-- Update unique constraint to handle both logged-in and guest users
ALTER TABLE cart_items DROP INDEX unique_cart_item;
ALTER TABLE cart_items ADD UNIQUE KEY unique_user_cart_item (user_id, product_id);
ALTER TABLE cart_items ADD UNIQUE KEY unique_session_cart_item (session_id, product_id);

-- Add shipping info to the orders table
ALTER TABLE orders 
ADD COLUMN customer_name VARCHAR(255) NULL AFTER user_id,
ADD COLUMN customer_email VARCHAR(255) NULL AFTER customer_name,
ADD COLUMN shipping_address TEXT NULL AFTER customer_email,
ADD COLUMN shipping_city VARCHAR(100) NULL AFTER shipping_address,
ADD COLUMN shipping_postal_code VARCHAR(20) NULL AFTER shipping_city,
ADD COLUMN shipping_country VARCHAR(100) NULL AFTER shipping_postal_code,
ADD COLUMN phone VARCHAR(20) NULL AFTER shipping_country,
ADD COLUMN payment_method VARCHAR(50) DEFAULT 'cash_on_delivery' AFTER status;
