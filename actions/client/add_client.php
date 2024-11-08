<?php
require_once '../../config.php';
$error_message = '';
$name = '';
$contact_info = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
    $contact_info = filter_input(INPUT_POST, 'contact_info', FILTER_SANITIZE_STRING);

    if (!preg_match("/^[a-zA-Zа-яА-ЯёЁіІїЇєЄ'.\-\s]+$/u", $name)) {
        $error_message = "Ім'я може містити лише літери.";
    } else {
        $query = "INSERT INTO client (name, contact_info) VALUES (?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ss", $name, $contact_info);
        if ($stmt->execute()) {
            $stmt->close();
            $conn->close();
            header('Location: ../../clients.php');
            exit();
        } else {
            $error_message = "Помилка: " . $stmt->error;
        }
        $stmt->close();
    }
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="uk">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Додати клієнта</title>
    <link rel="stylesheet" href="../../styles.css">
    <script src="../../scripts/theme.js" defer></script>
    <script src="../../scripts/message.js" defer></script>
</head>
<body>
    <h1>Додати клієнта</h1>
    <button id="theme-toggle">Темна тема</button>
    <form action="" method="post">
        Ім'я: <input type="text" name="name" value="<?= htmlspecialchars($name) ?>" required><br><br>
        Контактна інформація: <input type="text" name="contact_info" value="<?= htmlspecialchars($contact_info) ?>" required><br><br>
        <div class="center-text">
            <button type="submit" class="button">Додати</button> <br>
            <button type="button" class="button" onclick="window.location.href='../../clients.php'">До списку клієнтів</button>
            <?php if (!empty($error_message)): ?>
                <div id="error-message" class="error-message"><?= htmlspecialchars($error_message) ?></div>
            <?php endif; ?>
        </div>
    </form>
</body>
</html>