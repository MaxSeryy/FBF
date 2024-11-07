<?php
require_once '../../config.php';
session_start();

$response = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $first_name = filter_input(INPUT_POST, 'first_name', FILTER_SANITIZE_STRING);
    $last_name = filter_input(INPUT_POST, 'last_name', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    if (!preg_match("/^[a-zA-Zа-яА-ЯёЁіІїЇєЄ'-]+$/u", $first_name) || !preg_match("/^[a-zA-Zа-яА-ЯёЁіЇїЄє'-]+$/u", $last_name)) {
        $response['success'] = false;
        $response['message'] = "Ім'я та прізвище можуть містити лише літери.";
        echo json_encode($response);
        exit();
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $response['success'] = false;
        $response['message'] = "Невірний формат електронної пошти.";
        echo json_encode($response);
        exit();
    }

    $stmt = $conn->prepare("SELECT id FROM users WHERE email=?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        $response['success'] = false;
        $response['message'] = "Ця електронна пошта вже зареєстрована.";
        echo json_encode($response);
        exit();
    }
    $stmt->close();

    if (empty($first_name) || empty($last_name) || empty($email) || empty($password)) {
        $response['success'] = false;
        $response['message'] = "Будь ласка, заповніть всі поля.";
        echo json_encode($response);
        exit();
    }

    $stmt = $conn->prepare("INSERT INTO users (first_name, last_name, email, password) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $first_name, $last_name, $email, $password);

    if ($stmt->execute()) {
        $_SESSION['user_id'] = $stmt->insert_id;
        $_SESSION['first_name'] = $first_name;
        $_SESSION['last_name'] = $last_name;
        $response['success'] = true;
        $response['redirect'] = '../../index.php';
    } else {
        $response['success'] = false;
        $response['message'] = "Помилка: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
    echo json_encode($response);
    exit();
}
?>
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <title>Реєстрація</title>
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

            document.getElementById('register-form').addEventListener('submit', function(event) {
                event.preventDefault();
                const formData = new FormData(this);
                fetch('register.php', {
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
    <h1>Реєстрація</h1>
    <form id="register-form" method="post">
        Ім'я: <input type="text" name="first_name" required><br><br>
        Прізвище: <input type="text" name="last_name" required><br><br>
        Пошта: <input type="email" name="email" required><br><br>
        Пароль: <input type="password" name="password" required><br><br>
        <div style="text-align: center;">
            <button type="submit" class="button">Зареєструватися</button>
        </div>
    </form>
    <div id="error-message" style="color: red; text-align: center;"></div>
    <br>
    <div style="text-align: center;">
        <button class="button" onclick="window.location.href='login.php'">Вхід</button>
    </div>
</body>
</html>