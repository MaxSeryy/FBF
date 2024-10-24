<?php
require_once 'config.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    $query = "DELETE FROM manager WHERE id=$id";
    if ($conn->query($query) === TRUE) {
        header('Location: managers.php');
    } else {
        echo "Помилка: " . $conn->error;
    }
}
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
    <?php
    $query = "SELECT * FROM manager";
    $result = $conn->query($query);
    ?>
    <table border="1">
        <tr>
            <th>ID</th>
            <th>Ім'я</th>
            <th>Контактна інформація</th>
            <th>Дії</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()) { ?>
            <tr>
                <td><?= $row['id'] ?></td>
                <td><?= $row['name'] ?></td>
                <td><?= $row['contact_info'] ?></td>
                <td>
                    <a href="managers.php?id=<?= $row['id'] ?>" onclick="return confirm('Ви впевнені, що хочете видалити цього менеджера?')">Видалити</a> |
                    <a href="actions/manager/edit_manager.php?id=<?= $row['id'] ?>">Редагувати</a>
                </td>
            </tr>
        <?php } ?>
    </table>
    <?php $conn->close(); ?>
    <br>
    <button class="button" onclick="window.location.href='index.php'">Повернутися на головну</button>
    <button class="button" onclick="window.location.href='actions/manager/add_manager.php'">Додати нового менеджера</button>
</body>
</html>