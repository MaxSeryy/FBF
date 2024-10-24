<?php
require_once 'config.php';

if (isset($_GET['delete_id'])) {
    $id = $_GET['delete_id'];
    $query = "DELETE FROM client WHERE id=$id";

    if ($conn->query($query) === TRUE) {
        $deleteMessage = "Клієнт успішно видалений!";
    } else {
        $deleteMessage = "Помилка: " . $conn->error;
    }
}
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
    <?php
    $query = "SELECT * FROM client";
    $result = $conn->query($query);
    ?>
    <table border="1">
        <tr>
            <th>ID</th>
            <th>Ім'я</th>
            <th>Контактна інформація</th>
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
</html>
