<?php
session_start();
require_once 'config.php';

$message = '';

if (isset($_GET['delete_id'])) {
    $id = filter_input(INPUT_GET, 'delete_id', FILTER_SANITIZE_NUMBER_INT);
    if ($id) {
        $stmt = $conn->prepare("DELETE FROM project WHERE id=?");
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            header('Location: projects.php');
        } else {
            $message = "Помилка: " . $stmt->error;
        }
        $stmt->close();
    } else {
        $message = "Невірний ID для видалення.";
    }
}

$sort_column = filter_input(INPUT_GET, 'sort', FILTER_SANITIZE_STRING) ?? 'id';
$sort_direction = filter_input(INPUT_GET, 'dir', FILTER_SANITIZE_STRING) ?? 'ASC';
$sort_direction = strtoupper($sort_direction) === 'ASC' ? 'ASC' : 'DESC';

$allowed_columns = ['id', 'name', 'start', 'end', 'status', 'manager_name', 'client_name'];
$allowed_directions = ['ASC', 'DESC'];

if (!in_array($sort_column, $allowed_columns)) {
    $sort_column = 'id';
}

if (!in_array($sort_direction, $allowed_directions)) {
    $sort_direction = 'ASC';
}

$stmt = $conn->prepare("SELECT project.id, project.name, project.start, project.end, project.status, manager.name AS manager_name, client.name AS client_name
                        FROM project
                        JOIN manager ON project.manager_id = manager.id
                        JOIN client ON project.client_id = client.id
                        ORDER BY $sort_column $sort_direction");
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <title>Проекти</title>
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
    <h1>Проекти</h1>
    <button id="theme-toggle">Темна тема</button>
    <?php if ($message) { echo "<p id='message'>$message</p>"; } ?>
    <table border="1">
        <tr>
            <th><a href="?sort=id&dir=<?= $sort_direction === 'ASC' ? 'DESC' : 'ASC' ?>">ID</a></th>
            <th><a href="?sort=name&dir=<?= $sort_direction === 'ASC' ? 'DESC' : 'ASC' ?>">Назва проекту</a></th>
            <th><a href="?sort=start&dir=<?= $sort_direction === 'ASC' ? 'DESC' : 'ASC' ?>">Дата початку</a></th>
            <th><a href="?sort=end&dir=<?= $sort_direction === 'ASC' ? 'DESC' : 'ASC' ?>">Дата завершення</a></th>
            <th><a href="?sort=status&dir=<?= $sort_direction === 'ASC' ? 'DESC' : 'ASC' ?>">Статус</a></th>
            <th><a href="?sort=manager_name&dir=<?= $sort_direction === 'ASC' ? 'DESC' : 'ASC' ?>">Менеджер</a></th>
            <th><a href="?sort=client_name&dir=<?= $sort_direction === 'ASC' ? 'DESC' : 'ASC' ?>">Клієнт</a></th>
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
                    <a href="actions/project/edit_project.php?id=<?= htmlspecialchars($row['id']) ?>">Редагувати</a> |
                    <a href="?delete_id=<?= htmlspecialchars($row['id']) ?>" onclick="return confirm('Ви впевнені, що хочете видалити цей проект?')">Видалити</a>
                </td>
            </tr>
        <?php } ?>
    </table>
    <?php $conn->close(); ?>
    <br>
    <button class="button" onclick="window.location.href='index.php'">Повернутися на головну</button>
    <button class="button" onclick="window.location.href='actions/project/add_project.php'">Додати новий проект</button>
</body>
</html>