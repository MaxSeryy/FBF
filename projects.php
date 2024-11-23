<?php
session_start();
require_once 'config.php';

header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline'; style-src 'self' 'unsafe-inline'; img-src 'self';");

$message = '';

if (isset($_GET['delete_id'])) {
    $id = filter_input(INPUT_GET, 'delete_id', FILTER_SANITIZE_NUMBER_INT);
    if ($id) {
        $stmt = $conn->prepare("DELETE FROM project WHERE id=?");
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            header('Location: projects.php');
            exit();
        } else {
            $message = "Помилка: " . $stmt->error;
        }
        $stmt->close();
    } else {
        $message = "Невірний ID для видалення.";
    }
}

$allowed_columns = ['id', 'name', 'start', 'end', 'status', 'manager_name', 'client_name'];
$allowed_directions = ['ASC', 'DESC'];

$sort_column = 'id';
$sort_direction = 'ASC';

if (isset($_GET['sort']) && in_array($_GET['sort'], $allowed_columns)) {
    $sort_column = $_GET['sort'];
}

if (isset($_GET['dir']) && in_array(strtoupper($_GET['dir']), $allowed_directions)) {
    $sort_direction = strtoupper($_GET['dir']);
}

$order_by = "ORDER BY " . $sort_column . " " . $sort_direction;

$query = "SELECT project.id, project.name, project.start, project.end, project.status, manager.name AS manager_name, client.name AS client_name
          FROM project
          JOIN manager ON project.manager_id = manager.id
          JOIN client ON project.client_id = client.id
          $order_by";

$stmt = $conn->prepare($query);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <title>Проекти</title>
    <link rel="stylesheet" href="styles.css">
    <script src="scripts/theme.js" defer></script>
</head>
<body>
    <h1>Проекти</h1>
    <button id="theme-toggle">Темна тема</button>
    <?php if ($message) { echo "<p id='message'>" . htmlspecialchars($message) . "</p>"; } ?>
    <table class="styled-table">
        <tr>
            <th><a href="?sort=id&dir=<?= $sort_direction === 'ASC' ? 'DESC' : 'ASC' ?>">ID</a></th>
            <th><a href="?sort=name&dir=<?= $sort_direction === 'ASC' ? 'DESC' : 'ASC' ?>">Назва проекту</a></th>
            <th><a href="?sort=start&dir=<?= $sort_direction === 'ASC' ? 'DESC' : 'ASC' ?>">Дата початку</a></th>
            <th><a href="?sort=end&dir=<?= $sort_direction === 'ASC' ? 'DESC' : 'ASC' ?>">Дата завершення</а></th>
            <th><a href="?sort=status&dir=<?= $sort_direction === 'ASC' ? 'DESC' : 'ASC' ?>">Статус</а></th>
            <th><a href="?sort=manager_name&dir=<?= $sort_direction === 'ASC' ? 'DESC' : 'ASC' ?>">Менеджер</а></th>
            <th><a href="?sort=client_name&dir=<?= $sort_direction === 'ASC' ? 'DESC' : 'ASC' ?>">Клієнт</а></th>
            <th>Дії</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()) { ?>
            <tr>
                <td><?= htmlspecialchars($row['id']) ?></td>
                <td><?= htmlspecialchars($row['name']) ?></td>
                <td><?= htmlspecialchars($row['start']) ?></td>
                <td><?= htmlspecialchars($row['end']) ?></td>
                <td><?= htmlspecialchars($row['status']) ?></td>
                <td><?= htmlspecialchars($row['manager_name']) ?></td>
                <td><?= htmlspecialchars($row['client_name']) ?></td>
                <td>
                    <a href="actions/project/edit_project.php?id=<?= htmlspecialchars($row['id']) ?>">Редагувати</а> |
                    <a href="?delete_id=<?= htmlspecialchars($row['id']) ?>" onclick="return confirm('Ви впевнені, що хочете видалити цей проект?')">Видалити</а>
                </td>
            </tr>
        <?php } ?>
    </table>
    <?php $stmt->close(); ?>
    <?php $conn->close(); ?>
    <br>
    <button class="button" onclick="window.location.href='index.php'">Повернутися на головну</button>
    <button class="button" onclick="window.location.href='actions/project/add_project.php'">Додати новий проект</button>
</body>
</html>