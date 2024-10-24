<?php
require_once '../../config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $role = $_POST['role'];
    $project_id = $_POST['project_id'];
    $inventory_ids = $_POST['inventory_ids'];

    $query = "INSERT INTO employee (name, role, project_id) VALUES ('$name', '$role', '$project_id')";
    if ($conn->query($query) === TRUE) {
        $employee_id = $conn->insert_id;

        if (!empty($inventory_ids)) {
            foreach ($inventory_ids as $inventory_id) {
                $conn->query("INSERT INTO employee_inventory (employee_id, inventory_id) VALUES ('$employee_id', '$inventory_id')");
            }
        }
        header('Location: ../../employees.php');
    } else {
        echo "Помилка: " . $query . "<br>" . $conn->error;
    }

    $conn->close();
    echo '<br><a href="../../employees.php">Повернутися до працівників</a>';
    exit();
}

$projects_query = "SELECT id, name FROM project";
$projects_result = $conn->query($projects_query);

$inventory_query = "SELECT id, name FROM inventory";
$inventory_result = $conn->query($inventory_query);
?>

<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="../../styles.css">
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
    <title>Додати працівника</title>
</head>
<body>
    <h1>Додати нового працівника</h1>
    <button id="theme-toggle">Темна тема</button>
    <form method="post" action="">
        Ім'я: <input type="text" name="name" required><br><br>
        Посада: <input type="text" name="role" required><br><br>
        Проект:
        <select name="project_id" required>
            <?php while ($project = $projects_result->fetch_assoc()) { ?>
                <option value="<?= $project['id'] ?>"><?= $project['name'] ?></option>
            <?php } ?>
        </select><br><br>
        Інвентар (затисніть Ctrl для вибору декількох):
        <br>
        <select name="inventory_ids[]" multiple>
            <?php while ($inventory = $inventory_result->fetch_assoc()) { ?>
                <option value="<?= $inventory['id'] ?>"><?= $inventory['name'] ?></option>
            <?php } ?>
        </select><br><br>
        <div style="text-align: center;">
            <button type="submit" class="button">Додати</button>
        </div>
    </form>
    <button class="button" onclick="window.location.href='../../employees.php'">До списку працівників</button>
</body>
</html>