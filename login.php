<?php
session_start();

if (isset($_POST['username'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    if (empty($username)) {
        $username = 'user';
    }

    if ($username === 'admin' && $password === '123') {
        $_SESSION['username'] = 'admin';
        header('Location: index.php');
        exit();
    } elseif ($username === 'user' && empty($password)) {
        $_SESSION['username'] = 'user';
        header('Location: index.php');
        exit();
    } else {
        $error = 'Невірне ім’я користувача або пароль!';
    }
}
?>

<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="styles.css">
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
        });
    </script>
    
    <title>Login</title>
</head>
<body>
    <h2>Вхід</h2>
    <form method="post">
        <button id="theme-toggle">Темна тема</button>
        <label for="username">Ім'я користувача:</label>
        <input type="text" id="username" name="username"><br><br>
        <label for="password">Пароль:</label>
        <input type="password" id="password" name="password"><br><br>
        <div style="text-align: center;">
            <button type="submit" class="button">Увійти</button>
        </div>
    </form>
    <?php if (isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
</body>
</html>