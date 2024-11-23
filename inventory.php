<?php
require_once 'config.php';

header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline'; style-src 'self' 'nonce-123456'; img-src 'self';");

function sanitize_input($data) {
    return htmlspecialchars(trim($data));
}

if (isset($_GET['id'])) {
    $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
    if ($id) {
        $stmt = $conn->prepare("DELETE FROM inventory WHERE id = ?");
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            header('Location: inventory.php');
        } else {
            echo "Помилка: " . $conn->error;
        }
        $stmt->close();
    } else {
        echo "Невірний ID для видалення.";
    }
}

$sort_column = sanitize_input(filter_input(INPUT_GET, 'sort', FILTER_SANITIZE_STRING)) ?? 'id';
$sort_direction = sanitize_input(filter_input(INPUT_GET, 'dir', FILTER_SANITIZE_STRING)) ?? 'ASC';
$sort_direction = strtoupper($sort_direction) === 'ASC' ? 'ASC' : 'DESC';

$allowed_columns = ['id', 'name', 'rent_cost'];
$allowed_directions = ['ASC', 'DESC'];

if (!in_array($sort_column, $allowed_columns)) {
    $sort_column = 'id';
}

if (!in_array($sort_direction, $allowed_directions)) {
    $sort_direction = 'ASC';
}

$stmt = $conn->prepare("SELECT * FROM inventory ORDER BY $sort_column $sort_direction");
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <title>Доступний Інвентар</title>
    <link rel="stylesheet" href="styles.css" nonce="123456">
    <script src="scripts/theme.js" defer></script>
</head>
<body>
<button id="theme-toggle" nonce="123456">Темна тема</button>
    <h1>Доступний Інвентар</h1>
    <?php if (isset($deleteMessage)) { echo "<p>$deleteMessage</p>"; } ?>
    <table class="styled-table">
        <tr>
            <th><a href="?sort=id&dir=<?= $sort_direction === 'ASC' ? 'DESC' : 'ASC' ?>">ID</a></th>
            <th><a href="?sort=name&dir=<?= $sort_direction === 'ASC' ? 'DESC' : 'ASC' ?>">Назва</a></th>
            <th><a href="?sort=rent_cost&dir=<?= $sort_direction === 'ASC' ? 'DESC' : 'ASC' ?>">Вартість оренди</a></th>
            <th>Дії</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()) { ?>
            <tr>
                <td><?= htmlspecialchars($row['id']) ?></td>
                <td><?= htmlspecialchars($row['name']) ?></td>
                <td><?= htmlspecialchars($row['rent_cost']) ?> USD</td>
                <td>
                    <a href="actions/inventory/edit_inventory.php?id=<?= htmlspecialchars($row['id']) ?>">Редагувати</a> |
                    <a href="?id=<?= htmlspecialchars($row['id']) ?>" onclick="return confirm('Ви впевнені, що хочете видалити цей інвентар?')">Видалити</a>
                </td>
            </tr>
        <?php } ?>
    </table>
    <?php $conn->close(); ?>
    <br>
    <button class="button" onclick="window.location.href='index.php'">Повернутися на головну</button>
    <button class="button" onclick="window.location.href='actions/inventory/add_inventory.php'">Додати новий інвентар</button>
</body>
</html>