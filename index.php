<?php
session_start();

if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="uk">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FBF - Fast Build Force</title>
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
</head>

<body>
    <h1>Fast Build Force</h1>
    <button id="theme-toggle">Темна тема</button>
    <button class="menu-button" onclick="window.location.href='projects.php'">Проекти</button>
    <button class="menu-button" onclick="window.location.href='clients.php'">Клієнти</button>
    <button class="menu-button" onclick="window.location.href='employees.php'">Працівники</button>
    <button class="menu-button" onclick="window.location.href='inventory.php'">Інвентар</button>
    <button class="menu-button" onclick="window.location.href='managers.php'">Менеджери</button>
    <button class="button"onclick="window.location.href='logout.php'" style="float:right;">Logout</button>
    <div class="version">Версія 0.9.5</div>
</body>
</html>