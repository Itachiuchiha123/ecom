// Admin Orders Management

function viewOrderDetails(orderId) {
    fetch('../../php/orders_crud.php?action=get_details&order_id=' + orderId)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                displayOrderDetails(data.order);
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred');
        });
}

function displayOrderDetails(order) {
    let itemsHtml = '';
    order.items.forEach(item => {
        itemsHtml += `
            <div class="summary-row">
                <span>${item.product_name} x ${item.quantity}</span>
                <span>$${(item.price * item.quantity).toFixed(2)}</span>
            </div>
        `;
    });

    const html = `
        <div style="padding: 20px;">
            <h3>Order #${order.id}</h3>
            <p><strong>Customer:</strong> ${order.customer_name}</p>
            <p><strong>Email:</strong> ${order.customer_email}</p>
            <p><strong>Phone:</strong> ${order.customer_phone || 'N/A'}</p>
            <p><strong>Status:</strong> <span class="badge ${order.status}">${order.status}</span></p>
            <p><strong>Date:</strong> ${new Date(order.created_at).toLocaleString()}</p>
            
            <h4 style="margin-top: 30px; margin-bottom: 15px;">Order Items</h4>
            ${itemsHtml}
            
            <div class="summary-divider"></div>
            
            <div class="summary-row">
                <span>Subtotal</span>
                <span>$${(order.total_amount - order.tax - order.delivery_fee).toFixed(2)}</span>
            </div>
            <div class="summary-row">
                <span>Tax</span>
                <span>$${parseFloat(order.tax).toFixed(2)}</span>
            </div>
            <div class="summary-row">
                <span>Delivery Fee</span>
                <span>$${parseFloat(order.delivery_fee).toFixed(2)}</span>
            </div>
            
            <div class="summary-total">
                <span>Total</span>
                <span>$${parseFloat(order.total_amount).toFixed(2)}</span>
            </div>
        </div>
    `;

    document.getElementById('orderDetailsContent').innerHTML = html;
    document.getElementById('orderModal').classList.add('show');
}

function updateOrderStatus(orderId, currentStatus) {
    document.getElementById('orderId').value = orderId;
    document.getElementById('orderStatus').value = currentStatus;
    document.getElementById('statusModal').classList.add('show');
}

function saveOrderStatus(event) {
    event.preventDefault();

    const form = event.target;
    const formData = new FormData(form);
    formData.append('action', 'update_status');

    fetch('../../php/orders_crud.php', {
        method: 'POST',
        body: formData
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                closeStatusModal();
                location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred');
        });
}

function closeModal() {
    document.getElementById('orderModal').classList.remove('show');
}

function closeStatusModal() {
    document.getElementById('statusModal').classList.remove('show');
}

// Close modal when clicking outside
window.onclick = function (event) {
    const orderModal = document.getElementById('orderModal');
    const statusModal = document.getElementById('statusModal');

    if (event.target === orderModal) {
        closeModal();
    }
    if (event.target === statusModal) {
        closeStatusModal();
    }
};
