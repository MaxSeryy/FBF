<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: actions/reglog/login.php');
    exit();
}

if (isset($_GET['action']) && $_GET['action'] == 'logout') {
    session_destroy();
    header('Location: actions/reglog/login.php');
    exit();
}
//idk how to use this
header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline'; style-src 'self' 'unsafe-inline'; img-src 'self';");
?>
<!DOCTYPE html>
<html lang="uk">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FBF - Fast Build Force</title>
    <link rel="stylesheet" href="styles.css">
    <script src="scripts/theme.js" defer></script>
</head>
    <body>
    <h1>Fast Build Force</h1>
    <button id="theme-toggle">Темна тема</button>

    <div class="user-info">
    <?php if (isset($_SESSION['user_id'])): ?>
        <?php
            $firstName = isset($_SESSION['first_name']) ? htmlspecialchars($_SESSION['first_name']) : '';
            $lastName = isset($_SESSION['last_name']) ? htmlspecialchars($_SESSION['last_name']) : '';
            $fullName = $firstName . ' ' . $lastName;

            if (mb_strlen($fullName) > 20) {
                $fullName = mb_substr($fullName, 0, 17) . '...';
            }
        ?>
        <span class="welcome-text"><?= $fullName ?></span>
        <button class="button logout-button" onclick="window.location.href='index.php?action=logout'">Вийти</button>
    <?php else: ?>
        <button class="button" onclick="window.location.href='actions/reglog/login.php'">Вхід</button>
        <button class="button" onclick="window.location.href='actions/reglog/register.php'">Реєстрація</button>
    <?php endif; ?>
</div>

    <button class="menu-button" onclick="window.location.href='projects.php'">Проекти</button>
    <button class="menu-button" onclick="window.location.href='clients.php'">Клієнти</button>
    <button class="menu-button" onclick="window.location.href='employees.php'">Працівники</button>
    <button class="menu-button" onclick="window.location.href='inventory.php'">Інвентар</button>
    <button class="menu-button" onclick="window.location.href='managers.php'">Менеджери</button>
    <div class="version">Версія 1.0.1</div>
</body>