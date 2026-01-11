// Cart management using localStorage
const Cart = {
    // Get cart from localStorage
    get: function () {
        const cart = localStorage.getItem('cart');
        return cart ? JSON.parse(cart) : [];
    },

    // Save cart to localStorage
    save: function (cart) {
        localStorage.setItem('cart', JSON.stringify(cart));
        this.updateCartBadge();
    },

    // Add item to cart
    add: function (product) {
        let cart = this.get();
        const existingItem = cart.find(item => item.id === product.id);

        if (existingItem) {
            existingItem.quantity += 1;
        } else {
            cart.push({
                id: product.id,
                name: product.name,
                price: product.price,
                quantity: 1
            });
        }

        this.save(cart);
        return true;
    },

    // Remove item from cart
    remove: function (productId) {
        let cart = this.get();
        cart = cart.filter(item => item.id !== productId);
        this.save(cart);
    },

    // Update item quantity
    updateQuantity: function (productId, quantity) {
        let cart = this.get();
        const item = cart.find(item => item.id === productId);

        if (item) {
            if (quantity <= 0) {
                this.remove(productId);
            } else {
                item.quantity = quantity;
                this.save(cart);
            }
        }
    },

    // Get cart count
    getCount: function () {
        const cart = this.get();
        return cart.reduce((total, item) => total + item.quantity, 0);
    },

    // Get cart subtotal
    getSubtotal: function () {
        const cart = this.get();
        return cart.reduce((total, item) => total + (item.price * item.quantity), 0);
    },

    // Clear cart
    clear: function () {
        localStorage.removeItem('cart');
        this.updateCartBadge();
    },

    // Update cart badge in header
    updateCartBadge: function () {
        const badge = document.querySelector('.cart-badge');
        if (badge) {
            const count = this.getCount();
            badge.textContent = count;
            badge.style.display = count > 0 ? 'flex' : 'none';
        }
    }
};

// User management using localStorage
const User = {
    // Get user from localStorage
    get: function () {
        const user = localStorage.getItem('user');
        return user ? JSON.parse(user) : null;
    },

    // Save user to localStorage
    save: function (user) {
        localStorage.setItem('user', JSON.stringify(user));
    },

    // Check if user is logged in
    isLoggedIn: function () {
        return this.get() !== null;
    },

    // Logout user
    logout: function () {
        localStorage.removeItem('user');
    }
};

// Initialize app
document.addEventListener('DOMContentLoaded', function () {
    // Update cart badge on page load
    Cart.updateCartBadge();

    // Update header based on login status
    updateHeader();
});

// Update header to show user name or login button
function updateHeader() {
    const user = User.get();
    const loginDropdown = document.querySelector('.login-dropdown');

    if (loginDropdown && user) {
        loginDropdown.innerHTML = `
            <div class="user-profile">
                <div class="user-name" onclick="toggleUserDropdown()">
                    ${user.name}
                </div>
                <div class="dropdown-content" id="userDropdown">
                    <a href="pages/orders.php">My Orders</a>
                    <a href="#" onclick="logout()">Logout</a>
                </div>
            </div>
        `;
    }
}

// Toggle login dropdown
function toggleLoginDropdown() {
    const dropdown = document.getElementById('loginDropdown');
    dropdown.classList.toggle('show');
}

// Toggle user dropdown
function toggleUserDropdown() {
    const dropdown = document.getElementById('userDropdown');
    dropdown.classList.toggle('show');
}

// Close dropdown when clicking outside
window.onclick = function (event) {
    if (!event.target.matches('.login-btn') && !event.target.matches('.user-name')) {
        const dropdowns = document.getElementsByClassName('dropdown-content');
        for (let dropdown of dropdowns) {
            if (dropdown.classList.contains('show')) {
                dropdown.classList.remove('show');
            }
        }
    }
};

// Logout function
function logout() {
    User.logout();
    window.location.href = '../php/logout_handler.php';
}

// Add to cart function
function addToCart(productId, productName, productPrice) {
    Cart.add({
        id: productId,
        name: productName,
        price: parseFloat(productPrice)
    });

    // Show success message
    alert(`${productName} added to cart!`);
}

// Show message
function showMessage(message, type = 'success') {
    const messageDiv = document.createElement('div');
    messageDiv.className = `message ${type}`;
    messageDiv.textContent = message;

    const container = document.querySelector('.auth-box') || document.body;
    container.insertBefore(messageDiv, container.firstChild);

    setTimeout(() => {
        messageDiv.remove();
    }, 3000);
}

// Format currency
function formatCurrency(amount) {
    return '$' + amount.toFixed(2);
}
