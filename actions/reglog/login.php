<?php
require_once '../../config.php';
session_start();

$response = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['guest'])) {
        $_SESSION['user_id'] = 0;
        $_SESSION['first_name'] = 'Гість';
        $_SESSION['last_name'] = '';
        $response['success'] = true;
        $response['redirect'] = '../../index.php';
    } else {
        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
        $password = $_POST['password'];

        $stmt = $conn->prepare("SELECT id, first_name, last_name, password FROM users WHERE email=?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($id, $first_name, $last_name, $hashed_password);
        $stmt->fetch();

        if ($stmt->num_rows > 0) {
            if (password_verify($password, $hashed_password)) {
                $_SESSION['user_id'] = $id;
                $_SESSION['first_name'] = $first_name;
                $_SESSION['last_name'] = $last_name;
                $response['success'] = true;
                $response['redirect'] = '../../index.php';
            } else {
                $response['success'] = false;
                $response['message'] = 'Невірний пароль.';
            }
        } else {
            $response['success'] = false;
            $response['message'] = 'Електронна пошта не знайдена.';
        }

        $stmt->close();
        $conn->close();
    }
    echo json_encode($response);
    exit();
}
?>
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <title>Вхід</title>
    <link rel="stylesheet" href="../../styles.css">
    <script>
        document.addEventListener('DOMContentLoaded', (event) => {
            const themeToggle = document.getElementById('theme-toggle');
            const currentTheme = localStorage.getItem('theme') || 'light';
            if (currentTheme === 'dark') {
                document.body.classList.add('dark-mode');
                themeToggle.textContent = 'Світла тема';
            } else {
                themeToggle.textContent = 'Темна тема';
            }

            themeToggle.addEventListener('click', () => {
                document.body.classList.toggle('dark-mode');
                const newTheme = document.body.classList.contains('dark-mode') ? 'dark' : 'light';
                localStorage.setItem('theme', newTheme);
                themeToggle.textContent = newTheme === 'dark' ? 'Світла тема' : 'Темна тема';
            });

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
    </script>
</head>
<body>
    <button id="theme-toggle">Темна тема</button>
    <h1>Вхід</h1>
    <form id="login-form" method="post">
        Пошта: <input type="email" name="email" required><br><br>
        Пароль: <input type="password" name="password" required><br><br>
        <div style="text-align: center;">
            <button type="submit" class="button">Увійти</button>
        </div>
    </form>
    <div id="error-message" style="color: red; text-align: center;"></div>
    <br>
    <div style="text-align: center;">
        <button id="guest-login" class="button">Увійти як гість</button> <br>
        <button type="button" class="button" onclick="window.location.href='register.php'">Реєстрація</button>
    </div>
</body>
</html>