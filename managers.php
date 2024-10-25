<?php
require_once 'config.php';

session_start();
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}

$isAdmin = ($_SESSION['username'] === 'admin');

if ($isAdmin && isset($_GET['id'])) {
    $id = $_GET['id'];

    $query = "DELETE FROM manager WHERE id=$id";
    if ($conn->query($query) === TRUE) {
        header('Location: managers.php');
    } else {
        echo "Помилка: " . $conn->error;
    }
}

$sort_column = $_GET['sort'] ?? 'id';
$sort_direction = $_GET['dir'] ?? 'ASC';
$sort_direction = strtoupper($sort_direction) === 'ASC' ? 'ASC' : 'DESC';

$query = "SELECT * FROM manager ORDER BY $sort_column $sort_direction";
$result = $conn->query($query);
?>
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <title>Менеджери</title>
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
    <h1>Менеджери</h1>
    <button id="theme-toggle">Темна тема</button>
    <?php if (isset($deleteMessage)) { echo "<p>$deleteMessage</p>"; } ?>
    <table border="1">
        <tr>
            <th><a href="?sort=id&dir=<?= $sort_direction === 'ASC' ? 'DESC' : 'ASC' ?>">ID</a></th>
            <th><a href="?sort=name&dir=<?= $sort_direction === 'ASC' ? 'DESC' : 'ASC' ?>">Ім'я</a></th>
            <th><a href="?sort=contact_info&dir=<?= $sort_direction === 'ASC' ? 'DESC' : 'ASC' ?>">Контактна інформація</a></th>
            <th>Дії</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()) { ?>
            <tr>
                <td><?= $row['id'] ?></td>
                <td><?= $row['name'] ?></td>
                <td><?= $row['contact_info'] ?></td>
                <td>
                    <?php if ($isAdmin) { ?>
                        <a href="managers.php?id=<?= $row['id'] ?>&sort=<?= $sort_column ?>&dir=<?= $sort_direction ?>" onclick="return confirm('Ви впевнені, що хочете видалити цього менеджера?')">Видалити</a> |
                        <a href="actions/manager/edit_manager.php?id=<?= $row['id'] ?>&sort=<?= $sort_column ?>&dir=<?= $sort_direction ?>">Редагувати</a>
                    <?php } else { ?>
                        <span>Недоступно</span>
                    <?php } ?>
                </td>
            </tr>
        <?php } ?>
    </table>
    <?php $conn->close(); ?>
    <br>
    <button class="button" onclick="window.location.href='index.php?sort=<?= $sort_column ?>&dir=<?= $sort_direction ?>'">Повернутися на головну</button>
    <?php if ($isAdmin) { ?>
        <button class="button" onclick="window.location.href='actions/manager/add_manager.php?sort=<?= $sort_column ?>&dir=<?= $sort_direction ?>'">Додати нового менеджера</button>
    <?php } ?>
</body>
</html>