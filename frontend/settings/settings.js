function validateForm() {
    const password = document.getElementById('password-input').value;
    if (password.length < 8) {
        alert('Password must be at least 8 characters long.');
        return false;
    }
    return true;
}

