<?php
require_once '../../config.php';
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
    $contact_info = filter_input(INPUT_POST, 'contact_info', FILTER_SANITIZE_STRING);

    if (!preg_match("/^[a-zA-Zа-яА-ЯёЁіІїЇєЄ'.\-\s]+$/u", $name)) {
        $error_message = "Ім'я може містити лише літери.";
    } else {
        $stmt = $conn->prepare("INSERT INTO manager (name, contact_info) VALUES (?, ?)");
        if ($stmt === false) {
            $error_message = "Помилка підготовки запиту: " . $conn->error;
        } else {
            $stmt->bind_param("ss", $name, $contact_info);
            if ($stmt->execute() === TRUE) {
                $stmt->close();
                $conn->close();
                header('Location: ../../managers.php');
                exit();
            } else {
                $error_message = "Помилка виконання запиту: " . $stmt->error;
            }
            $stmt->close();
        }
    }
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="../../styles.css">
    <script src="../../scripts/theme.js" defer></script>
    <script src="../../scripts/message.js" defer></script>
    <title>Додати менеджера</title>
</head>
<body>
<button id="theme-toggle">Темна тема</button>
    <h1>Додати нового менеджера</h1>
    <form method="post" action="add_manager.php">
        Ім'я: <input type="text" name="name" required><br><br>
        Контактна інформація: <input type="text" name="contact_info" required><br><br>
        <div class="center-text">
            <button type="submit" class="button">Додати</button>
            <br>
            <button type="button" class="button" onclick="window.location.href='../../managers.php'">До списку менеджерів</button>
            <?php if (!empty($error_message)): ?>
                <div id="error-message" class="error-message"><?= htmlspecialchars($error_message) ?></div>
            <?php endif; ?>
        </div>
    </form>
</body>
</html>