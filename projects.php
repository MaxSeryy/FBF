<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}

$isAdmin = ($_SESSION['username'] === 'admin');

$message = '';

if ($isAdmin && isset($_GET['delete_id'])) {
    $id = $_GET['delete_id'];
    $query = "DELETE FROM project WHERE id=$id";

    if ($conn->query($query) === TRUE) {
        header('Location: projects.php');
    } else {
        $message = "Помилка: " . $conn->error;
    }
}

$sort_column = $_GET['sort'] ?? 'id';
$sort_direction = $_GET['dir'] ?? 'ASC';
$sort_direction = strtoupper($sort_direction) === 'ASC' ? 'ASC' : 'DESC';

$query = "SELECT project.id, project.name, project.start, project.end, project.status, manager.name AS manager_name, client.name AS client_name
          FROM project
          JOIN manager ON project.manager_id = manager.id
          JOIN client ON project.client_id = client.id
          ORDER BY $sort_column $sort_direction";
$result = $conn->query($query);
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

            const message = document.getElementById('message');
            if (message) {
                setTimeout(() => {
                    message.style.display = 'none';
                }, 5000);
            }
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
                <td><?= $row['id'] ?></td>
                <td><?= $row['name'] ?></td>
                <td><?= $row['start'] ?></td>
                <td><?= $row['end'] ?></td>
                <td><?= $row['status'] ?></td>
                <td><?= $row['manager_name'] ?></td>
                <td><?= $row['client_name'] ?></td>
                <td>
                    <?php if ($isAdmin) { ?>
                        <a href="actions/project/edit_project.php?id=<?= $row['id'] ?>">Редагувати</a> | 
                        <a href="projects.php?delete_id=<?= $row['id'] ?>" onclick="return confirm('Ви впевнені, що хочете видалити цей проект?')">Видалити</a>
                    <?php } else { ?>
                        <span>Недоступно</span>
                    <?php } ?>
                </td>
            </tr>
        <?php } ?>
    </table>
    <?php $conn->close(); ?>
    <br>
    <button class="button" onclick="window.location.href='index.php'">Повернутися на головну</button>
    <?php if ($isAdmin) { ?>
        <button class="button" onclick="window.location.href='actions/project/add_project.php'">Додати новий проект</button>
    <?php } ?>
</body>
</html>