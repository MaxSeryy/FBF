<?php
require_once 'config.php';

header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline'; style-src 'self' 'unsafe-inline' 'nonce-123456'; img-src 'self';");

function sanitize_input($data) {
    return htmlspecialchars(trim($data));
}

if (isset($_GET['delete_id'])) {
    $id = filter_input(INPUT_GET, 'delete_id', FILTER_SANITIZE_NUMBER_INT);
    if ($id) {
        $query = "DELETE FROM client WHERE id=$id";
        if ($conn->query($query) === TRUE) {
            header('Location: clients.php');
        } else {
            $deleteMessage = "Помилка: " . $conn->error;
        }
    } else {
        $deleteMessage = "Невірний ID для видалення.";
    }
}

$sort_column = sanitize_input(filter_input(INPUT_GET, 'sort', FILTER_SANITIZE_STRING)) ?? 'id';
$sort_direction = sanitize_input(filter_input(INPUT_GET, 'dir', FILTER_SANITIZE_STRING)) ?? 'ASC';
$sort_direction = strtoupper($sort_direction) === 'ASC' ? 'ASC' : 'DESC';
// check
$allowed_columns = ['id', 'name', 'contact_info'];
$allowed_directions = ['ASC', 'DESC'];

if (!in_array($sort_column, $allowed_columns)) {
    $sort_column = 'id';
}

if (!in_array($sort_direction, $allowed_directions)) {
    $sort_direction = 'ASC';
}
//use prepared commands
$query = "SELECT * FROM client ORDER BY $sort_column $sort_direction";
$result = $conn->query($query);
?>
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <title>Клієнти</title>
    <link rel="stylesheet" href="styles.css" nonce="123456">
    <script src="scripts/theme.js" defer></script>
</head>
<body>
    <button id="theme-toggle">Темна тема</button>
    <h1>Клієнти</h1>
    <?php if (isset($deleteMessage)) { echo "<p>$deleteMessage</p>"; } ?>
    <table class="styled-table">
        <tr>
            <th><a href="?sort=id&dir=<?= $sort_direction === 'ASC' ? 'DESC' : 'ASC' ?>">ID</a></th>
            <th><a href="?sort=name&dir=<?= $sort_direction === 'ASC' ? 'DESC' : 'ASC' ?>">Ім'я</a></th>
            <th><a href="?sort=contact_info&dir=<?= $sort_direction === 'ASC' ? 'DESC' : 'ASC' ?>">Контактна інформація</a></th>
            <th>Дії</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()) { ?>
            <tr>
                <td><?= htmlspecialchars($row['id']) ?></td>
                <td><?= htmlspecialchars($row['name']) ?></td>
                <td><?= htmlspecialchars($row['contact_info']) ?></td>
                <td>
                    <a href="actions/client/edit_client.php?id=<?= htmlspecialchars($row['id']) ?>">Редагувати</a> |
                    <a href="?delete_id=<?= htmlspecialchars($row['id']) ?>" onclick="return confirm('Ви впевнені, що хочете видалити цього клієнта?')">Видалити</a>
                </td>
            </tr>
        <?php } ?>
    </table>
    <?php $conn->close(); ?>
    <br>
    <button class="button" onclick="window.location.href='index.php'">Повернутися на головну</button>
    <button class="button" onclick="window.location.href='actions/client/add_client.php'">Додати нового клієнта</button>
</body>
</html>