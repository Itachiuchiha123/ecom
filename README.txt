# Macroon Morning - E-commerce Web Application

A simple food ordering e-commerce platform built with PHP, HTML, CSS, and JavaScript for a college project.

## Features

- 🏠 Home page with hero section
- 🍰 Menu page with category filtering
- 📦 Product detail pages with ratings
- 🛒 Shopping cart with localStorage
- 📋 Order history
- 🔐 User authentication (Customer, Admin, Vendor)
- 🔒 Password hashing for security
- 💾 Session management with localStorage
- 👨‍💼 Admin panel with full CRUD operations
- 👥 User management (Create, Read, Update, Delete)
- 🍰 Product management (Create, Read, Update, Delete)
- 📦 Order management and status updates

## Project Structure

```
ecommerce/
├── assets/
│   └── images/           # Product images
├── css/
│   ├── common.css        # Shared styles (header, navigation)
│   ├── home.css          # Home page styles
│   ├── menu.css          # Menu page styles
│   ├── product.css       # Product detail styles
│   ├── cart.css          # Shopping cart styles
│   ├── orders.css        # Orders page styles
│   ├── auth.css          # Login/signup styles
│   └── admin.css         # Admin panel styles
├── js/
│   ├── main.js           # Core JavaScript utilities
│   ├── cart.js           # Cart functionality
│   ├── admin-users.js    # Admin user management
│   ├── admin-products.js # Admin product management
│   └── admin-orders.js   # Admin order management
├── pages/
│   ├── admin/
│   │   ├── index.php     # Admin dashboard
│   │   ├── users.php     # User management
│   │   ├── products.php  # Product management
│   │   └── orders.php    # Order management
│   ├── menu.php          # Menu/products page
│   ├── product.php       # Product detail page
│   ├── cart.php          # Shopping cart page
│   ├── orders.php        # Order history page
│   ├── login.php         # Login page
│   └── signup.php        # Signup page
├── php/
│   ├── config.php        # Database configuration
│   ├── auth.php          # Authentication functions
│   ├── login_handler.php # Login processing
│   ├── signup_handler.php # Signup processing
│   ├── logout_handler.php # Logout processing
│   ├── checkout_handler.php # Order processing
│   ├── users_crud.php    # User CRUD operations
│   ├── products_crud.php # Product CRUD operations
│   └── orders_crud.php   # Order management
├── index.php             # Home page
└── database_schema.sql   # Database structure
```

## Setup Instructions

### 1. Database Configuration

1. Open phpMyAdmin in XAMPP
2. Import the `database_schema.sql` file to create the database and tables
3. Update database connection in `php/config.php`:

```php
define('DB_HOST', 'YOUR_FRIENDS_LAPTOP_IP'); // e.g., '192.168.1.100' or 'localhost'
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'macroon_morning');
```

### 2. XAMPP Setup

1. Place the `ecommerce` folder in your XAMPP `htdocs` directory
   - Example: `C:\xampp\htdocs\ecommerce\`

2. Start Apache and MySQL in XAMPP Control Panel

3. Access the application:
   - Local: `http://localhost/ecommerce/`
   - Network: `http://YOUR_IP/ecommerce/`

### 3. Remote Database Connection (Friend's Laptop)

If your database is on your friend's laptop:

1. **On your friend's laptop:**
   - Open XAMPP Control Panel
   - Click "Config" next to MySQL → Select "my.ini"
   - Find the line `bind-address = 127.0.0.1`
   - Change it to `bind-address = 0.0.0.0`
   - Save and restart MySQL
   - Get the IP address: Open CMD and type `ipconfig`

2. **In your config.php:**
   - Set `DB_HOST` to your friend's IP address
   - Example: `define('DB_HOST', '192.168.1.100');`

3. **MySQL User Privileges:**
   - In phpMyAdmin on friend's laptop
   - Go to User Accounts → Edit root user
   - Change Host from "localhost" to "%"
   - Or create a new user with remote access

## Usage

### Customer Flow

1. **Browse Products**
   - Visit home page
   - Click "Order Now" or navigate to Menu

2. **Add to Cart**
   - Browse menu items
   - Click "Order Now" on product cards
   - Or view product details and click "Add to Cart"

3. **Checkout**
   - View cart
   - Login/Signup if not logged in
   - Click "Checkout Now"

4. **View Orders**
   - Navigate to Orders page
   - View order history and status

### Authentication

- **Customer Login**: For placing orders
- **Admin Login**: Access admin dashboard and manage platform
- **Vendor Login**: For managing products
- **Signup**: Create new customer account

All passwords are hashed using PHP's `password_hash()` function.

### Admin Panel

1. **Access Admin Dashboard**
   - Login with admin credentials
   - Navigate to: `/pages/admin/index.php`

2. **Manage Users**
   - View all users
   - Add new users (customer, admin, vendor)
   - Edit user information
   - Delete users
   - Change user passwords

3. **Manage Products**
   - View all products
   - Add new products with details
   - Edit product information
   - Update pricing and ratings
   - Mark products as popular
   - Delete products

4. **Manage Orders**
   - View all customer orders
   - View detailed order information
   - Update order status (pending → processing → delivered)
   - Cancel orders

## Technologies Used

- **Frontend**: HTML5, CSS3, JavaScript (ES6)
- **Backend**: PHP 7.4+
- **Database**: MySQL (XAMPP)
- **Storage**: localStorage (for cart and user session)

## Security Features

- Password hashing with PHP's `password_hash()`
- Prepared statements to prevent SQL injection
- Session management
- Input validation

## Notes

- This is a college project - simplified for educational purposes
- No payment gateway integration
- Basic validation implemented
- Responsive design included

## Troubleshooting

### Can't connect to database
- Check if MySQL is running in XAMPP
- Verify DB credentials in `php/config.php`
- Check firewall settings for remote connections

### Cart not persisting
- Check if JavaScript is enabled
- Clear browser cache and localStorage
- Check browser console for errors

### Login not working
- Verify database tables were created
- Check if user exists in database
- Verify password was hashed correctly

## Default Test Data

The database schema includes sample products:
- Chocolate Macroon ($12.99)
- Vanilla Pastry ($8.50)
- Strawberry Cake ($18.99)
- Coffee Roll ($10.99)
- Matcha Latte ($6.99)
- Red Velvet ($15.99)

Create a test user through the signup page to start ordering!
