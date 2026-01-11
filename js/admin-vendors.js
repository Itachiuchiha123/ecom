// Admin Vendor Management

function approveVendor(vendorId) {
    if (!confirm('Are you sure you want to approve this vendor?')) {
        return;
    }

    const formData = new FormData();
    formData.append('action', 'approve');
    formData.append('vendor_id', vendorId);

    fetch('../../php/vendors_crud.php', {
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

function suspendVendor(vendorId) {
    if (!confirm('Are you sure you want to suspend this vendor? They will not be able to access their account.')) {
        return;
    }

    const formData = new FormData();
    formData.append('action', 'suspend');
    formData.append('vendor_id', vendorId);

    fetch('../../php/vendors_crud.php', {
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

function rejectVendor(vendorId) {
    if (!confirm('Are you sure you want to reject and DELETE this vendor? This action cannot be undone.')) {
        return;
    }

    const formData = new FormData();
    formData.append('action', 'reject');
    formData.append('vendor_id', vendorId);

    fetch('../../php/vendors_crud.php', {
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
