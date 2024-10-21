function enableEdit() {

    document.getElementById('username-display').style.display = 'none';
    document.getElementById('password-display').style.display = 'none';
    document.getElementById('username-input').style.display = 'inline';
    document.getElementById('password-input').style.display = 'inline';


    document.getElementById('username-input').value = document.getElementById('username-display').innerText;
    document.getElementById('password-input').value = 'password';  

    document.getElementById('edit-button').style.display = 'none';
    document.querySelector('.edit-actions').style.display = 'block';
}

function saveChanges() {
    document.getElementById('username-display').innerText = document.getElementById('username-input').value;
    document.getElementById('password-display').innerText = '********';  

    document.getElementById('username-input').style.display = 'none';
    document.getElementById('password-input').style.display = 'none';
    document.getElementById('username-display').style.display = 'inline';
    document.getElementById('password-display').style.display = 'inline';

    document.getElementById('edit-button').style.display = 'block';
    document.querySelector('.edit-actions').style.display = 'none';
}

function cancelEdit() {
    document.getElementById('username-input').style.display = 'none';
    document.getElementById('password-input').style.display = 'none';
    document.getElementById('username-display').style.display = 'inline';
    document.getElementById('password-display').style.display = 'inline';

    document.getElementById('edit-button').style.display = 'block';
    document.querySelector('.edit-actions').style.display = 'none';
}