<?php
require_once 'config.php';

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

$sort_column = filter_input(INPUT_GET, 'sort', FILTER_SANITIZE_STRING) ?? 'id';
$sort_direction = filter_input(INPUT_GET, 'dir', FILTER_SANITIZE_STRING) ?? 'ASC';
$sort_direction = strtoupper($sort_direction) === 'ASC' ? 'ASC' : 'DESC';

$allowed_columns = ['id', 'name', 'contact_info'];
$allowed_directions = ['ASC', 'DESC'];

if (!in_array($sort_column, $allowed_columns)) {
    $sort_column = 'id';
}

if (!in_array($sort_direction, $allowed_directions)) {
    $sort_direction = 'ASC';
}

$query = "SELECT * FROM client ORDER BY $sort_column $sort_direction";
$result = $conn->query($query);
?>
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <title>Клієнти</title>
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
    <h1>Клієнти</h1>
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

<!-- <?php
require_once 'config.php';

if (isset($_GET['delete_id'])) {
    $id = $_GET['delete_id'];
    $query = "DELETE FROM client WHERE id=$id";

    if ($conn->query($query) === TRUE) {
        header('Location: clients.php');
    } else {
        $deleteMessage = "Помилка: " . $conn->error;
    }
}

$sort_column = $_GET['sort'] ?? 'id';
$sort_direction = $_GET['dir'] ?? 'ASC';
$sort_direction = strtoupper($sort_direction) === 'ASC' ? 'ASC' : 'DESC';

$query = "SELECT * FROM client ORDER BY $sort_column $sort_direction";
$result = $conn->query($query);
?>
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <title>Клієнти</title>
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
    <h1>Клієнти</h1>
    <?php if (isset($deleteMessage)) { echo "<p>$deleteMessage</p>"; } ?>
    <table border="1">
        <tr>
            <th><a href="?sort=id&dir=<?= $sort_direction === 'ASC' ? 'DESC' : 'ASC' ?>">ID</a></th>
            <th><a href="?sort=name&dir=<?= $sort_direction === 'ASC' ? 'DESC' : 'ASC' ?>">Ім'я</a></th>
            <th><a href="?sort=contact_info&dir=<?= $sort_direction === 'ASC' ? 'DESC' : 'ASC' ?>">Контактна інформація</a></th>
            <button id="theme-toggle">Темна тема</button>
            <th>Дії</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()) { ?>
            <tr>
                <td><?= $row['id'] ?></td>
                <td><?= $row['name'] ?></td>
                <td><?= $row['contact_info'] ?></td>
                <td>
                    <a href="actions/client/edit_client.php?id=<?= $row['id'] ?>">Редагувати</a> | 
                    <a href="?delete_id=<?= $row['id'] ?>" onclick="return confirm('Ви впевнені, що хочете видалити цього клієнта?')">Видалити</a>
                </td>
            </tr>
        <?php } ?>
    </table>
    <?php $conn->close(); ?>
    <br>
    <button class="button" onclick="window.location.href='index.php'">Повернутися на головну</button>
    <button class="button" onclick="window.location.href='actions/client/add_client.php'">Додати нового клієнта</button>
</body>
</html> -->