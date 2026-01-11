// Admin Users Management

function openAddModal() {
    document.getElementById('modalTitle').textContent = 'Add User';
    document.getElementById('action').value = 'create';
    document.getElementById('userId').value = '';
    document.getElementById('userForm').reset();
    document.getElementById('passwordRow').style.display = 'block';
    document.getElementById('userPassword').required = true;
    document.getElementById('userModal').classList.add('show');
}

function openEditModal(user) {
    document.getElementById('modalTitle').textContent = 'Edit User';
    document.getElementById('action').value = 'update';
    document.getElementById('userId').value = user.id;
    document.getElementById('userName').value = user.name;
    document.getElementById('userEmail').value = user.email;
    document.getElementById('userPhone').value = user.phone || '';
    document.getElementById('userAddress').value = user.address || '';
    document.getElementById('userType').value = user.user_type;
    document.getElementById('passwordRow').style.display = 'block';
    document.getElementById('userPassword').required = false;
    document.getElementById('userPassword').value = '';
    document.getElementById('userModal').classList.add('show');
}

function closeModal() {
    document.getElementById('userModal').classList.remove('show');
    document.getElementById('userForm').reset();
}

function saveUser(event) {
    event.preventDefault();

    const form = event.target;
    const formData = new FormData(form);

    fetch('../../php/users_crud.php', {
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

function deleteUser(userId) {
    if (!confirm('Are you sure you want to delete this user?')) {
        return;
    }

    const formData = new FormData();
    formData.append('action', 'delete');
    formData.append('user_id', userId);

    fetch('../../php/users_crud.php', {
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
    const modal = document.getElementById('userModal');
    if (event.target === modal) {
        closeModal();
    }
};
