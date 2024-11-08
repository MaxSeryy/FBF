document.addEventListener('DOMContentLoaded', (event) => {
    document.getElementById('login-form').addEventListener('submit', function(event) {
        event.preventDefault();
        const formData = new FormData(this);
        fetch('login.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.href = data.redirect;
            } else {
                document.getElementById('error-message').textContent = data.message;
            }
        });
    });

    document.getElementById('guest-login').addEventListener('click', function(event) {
        event.preventDefault();
        const formData = new FormData();
        formData.append('guest', 'true');
        fetch('login.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.href = data.redirect;
            } else {
                document.getElementById('error-message').textContent = data.message;
            }
        });
    });
});