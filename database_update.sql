-- Database Update Script for Vendor Approval Feature
-- Run this script on your existing database to add new fields
-- Date: January 11, 2026

USE macroon_morning;

-- Add is_approved field to users table
-- Vendors will need approval, customers and admins are auto-approved
ALTER TABLE users 
ADD COLUMN is_approved BOOLEAN DEFAULT TRUE AFTER user_type;

-- Update existing vendors to be approved (for backward compatibility)
UPDATE users SET is_approved = TRUE WHERE user_type IN ('customer', 'admin');

-- Add vendor_id field to products table to track product ownership
ALTER TABLE products 
ADD COLUMN vendor_id INT AFTER category_id,
ADD CONSTRAINT fk_products_vendor FOREIGN KEY (vendor_id) REFERENCES users(id) ON DELETE SET NULL;

-- Set existing products to NULL vendor_id (admin-created products)
UPDATE products SET vendor_id = NULL WHERE vendor_id IS NULL;

-- Optional: Create an index for faster vendor product queries
CREATE INDEX idx_products_vendor ON products(vendor_id);

-- Optional: Create an index for faster user approval queries
CREATE INDEX idx_users_approval ON users(is_approved, user_type);

SELECT 'Database schema updated successfully!' as status;
