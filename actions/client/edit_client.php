<?php
require_once '../../config.php';
$error_message = '';
$name = '';
$contact_info = '';

function sanitize_input($data) {
    return htmlspecialchars(trim($data));
}

function validate_name($name) {
    return preg_match("/^[a-zA-Zа-яА-ЯёЁіІїЇєЄ'.\-\s]+$/u", $name);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);
    $name = sanitize_input(filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING));
    $contact_info = sanitize_input(filter_input(INPUT_POST, 'contact_info', FILTER_SANITIZE_STRING));

    if (!validate_name($name)) {
        $error_message = "Ім'я може містити лише літери, пробіли, крапки та тире.";
    } else {
        $update_query = "UPDATE client SET name=?, contact_info=? WHERE id=?";
        $stmt = $conn->prepare($update_query);
        $stmt->bind_param("ssi", $name, $contact_info, $id);

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
} else {
    $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
    $query = "SELECT * FROM client WHERE id=?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $name = $row['name'];
    $contact_info = $row['contact_info'];
    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Редагувати клієнта</title>
    <link rel="stylesheet" href="../../styles.css">
    <script src="../../scripts/theme.js" defer></script>
    <script src="../../scripts/message.js" defer></script>
</head>
<body>
    <h1>Редагувати клієнта</h1>
    <button id="theme-toggle">Темна тема</button>
    <form action="" method="post">
        <input type="hidden" name="id" value="<?= htmlspecialchars($id) ?>">
        Ім'я: <input type="text" name="name" value="<?= htmlspecialchars($name) ?>" required><br><br>
        Контактна інформація: <input type="text" name="contact_info" value="<?= htmlspecialchars($contact_info) ?>" required><br><br>
        <div class="center-text">
            <button type="submit" class="button">Оновити</button> <br>
            <button type="button" class="button" onclick="window.location.href='../../clients.php'">До списку клієнтів</button>
            <?php if (!empty($error_message)): ?>
                <div id="error-message" class="error-message"><?= htmlspecialchars($error_message) ?></div>
            <?php endif; ?>
        </div>
    </form>
</body>
</html>