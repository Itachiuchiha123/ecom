// Vendor Products Management

function openAddModal() {
    document.getElementById('modalTitle').textContent = 'Add Product';
    document.getElementById('action').value = 'create';
    document.getElementById('productId').value = '';
    document.getElementById('existingImage').value = '';
    document.getElementById('productForm').reset();
    document.getElementById('currentImageText').textContent = '';
    document.getElementById('productModal').classList.add('show');
}

function openEditModal(product) {
    document.getElementById('modalTitle').textContent = 'Edit Product';
    document.getElementById('action').value = 'update';
    document.getElementById('productId').value = product.id;
    document.getElementById('productName').value = product.name;
    document.getElementById('productDescription').value = product.description || '';
    document.getElementById('productPrice').value = product.price;
    document.getElementById('productCategory').value = product.category_id;
    document.getElementById('productRating').value = product.rating;
    document.getElementById('existingImage').value = product.image || '';

    // Show current image name
    if (product.image) {
        document.getElementById('currentImageText').textContent = 'Current: ' + product.image;
    } else {
        document.getElementById('currentImageText').textContent = '';
    }

    document.getElementById('productModal').classList.add('show');
}

function closeModal() {
    document.getElementById('productModal').classList.remove('show');
    document.getElementById('productForm').reset();
}

function saveProduct(event) {
    event.preventDefault();

    const form = event.target;
    const formData = new FormData(form);

    fetch('../../php/vendor_products_crud.php', {
        method: 'POST',
        body: formData
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                closeModal();
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

function deleteProduct(productId) {
    if (!confirm('Are you sure you want to delete this product?')) {
        return;
    }

    const formData = new FormData();
    formData.append('action', 'delete');
    formData.append('product_id', productId);

    fetch('../../php/vendor_products_crud.php', {
        method: 'POST',
        body: formData
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
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

// Close modal when clicking outside
window.onclick = function (event) {
    const modal = document.getElementById('productModal');
    if (event.target === modal) {
        closeModal();
    }
};
