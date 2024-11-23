<?php
require_once '../../config.php';

function sanitize_input($data) {
    return htmlspecialchars(trim($data));
}

function validate_name($name) {
    return preg_match("/^[a-zA-Zа-яА-ЯёЁіІїЇєЄ'.\-\s]+$/u", $name);
}

$id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = sanitize_input(filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING));
    $contact_info = sanitize_input(filter_input(INPUT_POST, 'contact_info', FILTER_SANITIZE_STRING));

    if (!validate_name($name)) {
        $error_message = "Ім'я може містити лише літери.";
    } else {
        $stmt = $conn->prepare("UPDATE manager SET name=?, contact_info=? WHERE id=?");
        if ($stmt === false) {
            $error_message = "Помилка підготовки запиту: " . $conn->error;
        } else {
            $stmt->bind_param("ssi", $name, $contact_info, $id);
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
}

$stmt = $conn->prepare("SELECT * FROM manager WHERE id=?");
if ($stmt === false) {
    echo "Помилка підготовки запиту: " . $conn->error;
    exit();
}

$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$manager = $result->fetch_assoc();
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="../../styles.css">
    <script src="../../scripts/theme.js" defer></script>
    <title>Редагувати менеджера</title>
</head>
<body>
<button id="theme-toggle">Темна тема</button>
    <h1>Редагувати менеджера</h1>
    <form method="post" action="edit_manager.php?id=<?= htmlspecialchars($id) ?>">
        Ім'я: <input type="text" name="name" value="<?= htmlspecialchars($manager['name']) ?>" required><br><br>
        Контактна інформація: <input type="text" name="contact_info" value="<?= htmlspecialchars($manager['contact_info']) ?>" required><br><br>
        <div class="center-text">
            <button type="submit" class="button">Оновити</button>
            <br>
            <button type="button" class="button" onclick="window.location.href='../../managers.php'">До списку менеджерів</button>
            <?php if (!empty($error_message)): ?>
                <div id="error-message" class="error-message"><?= htmlspecialchars($error_message) ?></div>
            <?php endif; ?>
        </div>
    </form>
</body>
</html>