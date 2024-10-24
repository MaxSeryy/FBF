<?php
session_start();
require_once 'config.php';

$message = '';

if (isset($_GET['delete_id'])) {
    $id = $_GET['delete_id'];
    $query = "DELETE FROM project WHERE id=$id";

    if ($conn->query($query) === TRUE) {
        $message = "Проект успішно видалений!";
    } else {
        $message = "Помилка: " . $conn->error;
    }
}
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
    <?php
    $query = "SELECT project.id, project.name, project.start, project.end, project.status, manager.name AS manager_name, client.name AS client_name
              FROM project
              JOIN manager ON project.manager_id = manager.id
              JOIN client ON project.client_id = client.id";
    $result = $conn->query($query);
    ?>
    <table border="1">
        <tr>
            <th>ID</th>
            <th>Назва проекту</th>
            <th>Дата початку</th>
            <th>Дата завершення</th>
            <th>Статус</th>
            <th>Менеджер</th>
            <th>Клієнт</th>
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
                    <a href="actions/project/edit_project.php?id=<?= $row['id'] ?>">Редагувати</a> | 
                    <a href="projects.php?delete_id=<?= $row['id'] ?>" onclick="return confirm('Ви впевнені, що хочете видалити цей проект?')">Видалити</a>
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