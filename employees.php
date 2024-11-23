<?php
require_once 'config.php';

header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline'; style-src 'self' 'unsafe-inline'; img-src 'self';");

function sanitize_input($data) {
    return htmlspecialchars(trim($data));
}

if (isset($_GET['delete_id'])) {
    $id = filter_input(INPUT_GET, 'delete_id', FILTER_SANITIZE_NUMBER_INT);
    if ($id) {
        $query = "DELETE FROM employee WHERE id=$id";
        if ($conn->query($query) === TRUE) {
            $conn->query("DELETE FROM employee_inventory WHERE employee_id=$id");
        } else {
            $message = "Помилка: " . $conn->error;
        }
    } else {
        $message = "Невірний ID для видалення.";
    }
}

$sort_column = sanitize_input(filter_input(INPUT_GET, 'sort', FILTER_SANITIZE_STRING)) ?? 'id';
$sort_direction = sanitize_input(filter_input(INPUT_GET, 'dir', FILTER_SANITIZE_STRING)) ?? 'ASC';
$sort_direction = strtoupper($sort_direction) === 'ASC' ? 'ASC' : 'DESC';

$allowed_columns = ['id', 'name', 'role', 'project_name', 'inventories'];
$allowed_directions = ['ASC', 'DESC'];

if (!in_array($sort_column, $allowed_columns)) {
    $sort_column = 'id';
}

if (!in_array($sort_direction, $allowed_directions)) {
    $sort_direction = 'ASC';
}

$query = "SELECT employee.id, employee.name, employee.role, project.name AS project_name,
          GROUP_CONCAT(DISTINCT inventory.name SEPARATOR ', ') AS inventories
          FROM employee
          JOIN project ON employee.project_id = project.id
          LEFT JOIN employee_inventory ON employee.id = employee_inventory.employee_id
          LEFT JOIN inventory ON employee_inventory.inventory_id = inventory.id
          GROUP BY employee.id
          ORDER BY $sort_column $sort_direction";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <title>Працівники</title>
    <link rel="stylesheet" href="styles.css">
    <script src="scripts/theme.js" defer></script>
</head>
<body>
    <h1>Працівники</h1>
    <button id="theme-toggle">Темна тема</button>

    <?php if (isset($message)) { echo "<p>$message</p>"; } ?>

    <table class="styled-table">
        <tr>
            <th><a href="?sort=id&dir=<?= $sort_direction === 'ASC' ? 'DESC' : 'ASC' ?>">ID</a></th>
            <th><a href="?sort=name&dir=<?= $sort_direction === 'ASC' ? 'DESC' : 'ASC' ?>">Ім'я</a></th>
            <th><a href="?sort=role&dir=<?= $sort_direction === 'ASC' ? 'DESC' : 'ASC' ?>">Посада</a></th>
            <th><a href="?sort=project_name&dir=<?= $sort_direction === 'ASC' ? 'DESC' : 'ASC' ?>">Проект</a></th>
            <th><a href="?sort=inventories&dir=<?= $sort_direction === 'ASC' ? 'DESC' : 'ASC' ?>">Інвентар</a></th>
            <th>Дії</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()) { ?>
            <tr>
                <td><?= htmlspecialchars($row['id']) ?></td>
                <td><?= htmlspecialchars($row['name']) ?></td>
                <td><?= htmlspecialchars($row['role']) ?></td>
                <td><?= htmlspecialchars($row['project_name']) ?></td>
                <td><?= htmlspecialchars($row['inventories'] ? $row['inventories'] : 'Немає інвентарю') ?></td>
                <td>
                    <a href="actions/employee/edit_employee.php?id=<?= htmlspecialchars($row['id']) ?>">Редагувати</a> |
                    <a href="?delete_id=<?= htmlspecialchars($row['id']) ?>" onclick="return confirm('Ви впевнені, що хочете видалити цього працівника?')">Видалити</a>
                </td>
            </tr>
        <?php } ?>
    </table>
    <?php $conn->close(); ?>
    <br>
    <button class="button" onclick="window.location.href='index.php'">Повернутися на головну</button>
    <button class="button" onclick="window.location.href='actions/employee/add_employee.php'">Додати нового працівника</button>
</body>
</html>