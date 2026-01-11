-- Create database
CREATE DATABASE IF NOT EXISTS macroon_morning;
USE macroon_morning;

-- Users table
CREATE TABLE IF NOT EXISTS users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    address TEXT,
    user_type ENUM('customer', 'admin', 'vendor') DEFAULT 'customer',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Categories table
CREATE TABLE IF NOT EXISTS categories (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(50) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Products table
CREATE TABLE IF NOT EXISTS products (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    price DECIMAL(10, 2) NOT NULL,
    category_id INT,
    image VARCHAR(255),
    rating DECIMAL(2, 1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id)
);

-- Orders table
CREATE TABLE IF NOT EXISTS orders (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    total_amount DECIMAL(10, 2) NOT NULL,
    delivery_fee DECIMAL(10, 2) DEFAULT 3.99,
    status ENUM('pending', 'processing', 'delivered', 'cancelled') DEFAULT 'pending',
    delivery_address TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Order items table
CREATE TABLE IF NOT EXISTS order_items (
    id INT PRIMARY KEY AUTO_INCREMENT,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id),
    FOREIGN KEY (product_id) REFERENCES products(id)
);

-- Insert sample categories
INSERT INTO categories (name) VALUES 
('Cakes'),
('Pastries'),
('Drinks');

-- Insert sample products
INSERT INTO products (name, description, price, category_id, rating, image) VALUES 
('Chocolate Macroon', 'Delicious chocolate macroon with rich chocolate filling', 12.99, 1, 4.8, 'chocolate-macroon.jpg'),
('Vanilla Pastry', 'Light and fluffy vanilla pastry', 8.50, 2, 4.5, 'vanilla-pastry.jpg'),
('Strawberry Cake', 'Fresh strawberry cake with cream', 18.99, 1, 4.7, 'strawberry-cake.jpg'),
('Coffee Roll', 'Coffee flavored swiss roll', 10.99, 2, 4.3, 'coffee-roll.jpg'),
('Matcha Latte', 'Premium matcha green tea latte', 6.99, 3, 4.6, 'matcha-latte.jpg'),
('Red Velvet', 'Classic red velvet cake', 15.99, 1, 4.9, 'red-velvet.jpg');
