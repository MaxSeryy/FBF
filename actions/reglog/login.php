<?php
require_once '../../config.php';
session_start();

$response = [];

function sanitize_input($data) {
    return htmlspecialchars(trim($data));
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['guest'])) {
        $_SESSION['user_id'] = 0;
        $_SESSION['first_name'] = 'Гість';
        $_SESSION['last_name'] = '';
        $response['success'] = true;
        $response['redirect'] = '../../index.php';
    } else {
        $email = sanitize_input(filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL));
        $password = sanitize_input($_POST['password']);

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
    <script src="../../scripts/theme.js" defer></script>
    <script src="../../scripts/message.js" defer></script>
    <script src="../../scripts/login.js" defer></script>
</head>
<body>
    <button id="theme-toggle">Темна тема</button>
    <h1>Вхід</h1>
    <form id="login-form" method="post">
        Пошта: <input type="email" name="email" required><br><br>
        Пароль: <input type="password" name="password" required><br><br>
        <div class="center-text">
            <button type="submit" class="button">Увійти</button>
        </div>
    </form>
    <div style="text-align: center;">
        <button id="guest-login" class="button">Увійти як гість</button> <br>
        <button type="button" class="button" onclick="window.location.href='register.php'">Реєстрація</button>
        <div id="error-message" class="error-message"></div>
    </div>
</body>
</html>