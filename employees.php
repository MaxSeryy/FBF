<?php
require_once 'config.php';

if (isset($_GET['delete_id'])) {
    $id = $_GET['delete_id'];
    $query = "DELETE FROM employee WHERE id=$id";
    if ($conn->query($query) === TRUE) {
        $conn->query("DELETE FROM employee_inventory WHERE employee_id=$id");
    } else {
        $message = "Помилка: " . $conn->error;
    }
}
?>
<!DOCTYPE html>
<html lang="uk">

<head>
    <meta charset="UTF-8">
    <title>Працівники</title>
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
    <h1>Працівники</h1>
    <button id="theme-toggle">Темна тема</button>
    <?php if (isset($message)) { echo "<p>$message</p>"; } ?>
    <?php
    $query = "SELECT employee.id, employee.name, employee.role, project.name AS project_name,
    GROUP_CONCAT(DISTINCT inventory.name SEPARATOR ', ') AS inventories
    FROM employee
    JOIN project ON employee.project_id = project.id
    LEFT JOIN employee_inventory ON employee.id = employee_inventory.employee_id
    LEFT JOIN inventory ON employee_inventory.inventory_id = inventory.id
    GROUP BY employee.id";
    $result = $conn->query($query);
    ?>
    <table border="1">
        <tr>
            <th>ID</th>
            <th>Ім'я</th>
            <th>Посада</th>
            <th>Проект</th>
            <th>Інвентар</th>
            <th>Дії</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()) { ?>
            <tr>
                <td><?= $row['id'] ?></td>
                <td><?= $row['name'] ?></td>
                <td><?= $row['role'] ?></td>
                <td><?= $row['project_name'] ?></td>
                <td><?= $row['inventories'] ? $row['inventories'] : 'Немає інвентарю' ?></td>
                <td>
                    <a href="actions/employee/edit_employee.php?id=<?= $row['id'] ?>">Редагувати</a> |
                    <a href="?delete_id=<?= $row['id'] ?>" onclick="return confirm('Ви впевнені, що хочете видалити цього працівника?')">Видалити</a>
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