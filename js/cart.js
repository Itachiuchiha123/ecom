// Cart page specific functionality
document.addEventListener('DOMContentLoaded', function () {
    renderCart();
});

function renderCart() {
    const cart = Cart.get();
    const cartContent = document.getElementById('cartContent');
    const itemCount = document.getElementById('itemCount');

    if (cart.length === 0) {
        cartContent.innerHTML = `
            <div class="empty-cart">
                <div class="empty-cart-icon">😢</div>
                <div class="empty-cart-text">Your cart is empty</div>
                <button class="start-shopping-btn" onclick="window.location.href='menu.php'">Start Shopping</button>
            </div>
        `;
        itemCount.textContent = '0';
        updateSummary(0);
        return;
    }

    itemCount.textContent = cart.length;

    let html = '';
    cart.forEach(item => {
        html += `
            <div class="cart-item">
                <div class="cart-item-image"></div>
                <div class="cart-item-info">
                    <div class="cart-item-name">${item.name}</div>
                    <div class="cart-item-price">${formatCurrency(item.price)}</div>
                </div>
                <div class="cart-item-actions">
                    <div class="quantity-control">
                        <button class="quantity-btn" onclick="updateItemQuantity(${item.id}, ${item.quantity - 1})">-</button>
                        <span>${item.quantity}</span>
                        <button class="quantity-btn" onclick="updateItemQuantity(${item.id}, ${item.quantity + 1})">+</button>
                    </div>
                    <button class="remove-btn" onclick="removeItem(${item.id})">Remove</button>
                </div>
            </div>
        `;
    });

    cartContent.innerHTML = html;

    const subtotal = Cart.getSubtotal();
    updateSummary(subtotal);
}

function updateItemQuantity(productId, newQuantity) {
    Cart.updateQuantity(productId, newQuantity);
    renderCart();
}

function removeItem(productId) {
    if (confirm('Remove this item from cart?')) {
        Cart.remove(productId);
        renderCart();
    }
}

function updateSummary(subtotal) {
    const tax = subtotal * 0.1;
    const delivery = subtotal > 0 ? 3.99 : 0;
    const total = subtotal + tax + delivery;

    document.getElementById('subtotal').textContent = formatCurrency(subtotal);
    document.getElementById('tax').textContent = formatCurrency(tax);
    document.getElementById('delivery').textContent = formatCurrency(delivery);
    document.getElementById('total').textContent = formatCurrency(total);
}

function checkout() {
    const user = User.get();

    if (!user) {
        alert('Please login to checkout');
        window.location.href = 'login.php?type=customer';
        return;
    }

    const cart = Cart.get();

    if (cart.length === 0) {
        alert('Your cart is empty');
        return;
    }

    // Submit order
    const subtotal = Cart.getSubtotal();
    const tax = subtotal * 0.1;
    const delivery = 3.99;
    const total = subtotal + tax + delivery;

    const formData = new FormData();
    formData.append('user_id', user.id);
    formData.append('total_amount', total);
    formData.append('tax', tax);
    formData.append('delivery_fee', delivery);
    formData.append('items', JSON.stringify(cart));

    fetch('../php/checkout_handler.php', {
        method: 'POST',
        body: formData
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Order placed successfully!');
                Cart.clear();
                window.location.href = 'orders.php';
            } else {
                alert('Error placing order: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error placing order');
        });
}
