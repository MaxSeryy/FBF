<?php
require_once '../../config.php';
$id = isset($_GET['id']) ? $_GET['id'] : null;
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $role = $_POST['role'];
    $project_id = $_POST['project_id'];
    $inventory_ids = isset($_POST['inventory_ids']) ? $_POST['inventory_ids'] : [];
    if ($id) {
        $query = "UPDATE employee SET name='$name', role='$role', project_id='$project_id' WHERE id=$id";
        if ($conn->query($query) === TRUE) {
            $conn->query("DELETE FROM employee_inventory WHERE employee_id=$id");

            if (!empty($inventory_ids)) {
                foreach ($inventory_ids as $inventory_id) {
                    $conn->query("INSERT INTO employee_inventory (employee_id, inventory_id) VALUES ('$id', '$inventory_id')");
                }
            }
            header('Location: ../../employees.php');
        } else {
            echo "Помилка: " . $conn->error;
        }
    }
    $conn->close();
    echo '<br><a href="../../employees.php">Повернутися до працівників</a>';
    exit();
}
$employee = ['name' => '', 'role' => '', 'project_id' => ''];
$selected_inventories = [];

if ($id) {
    $query = "SELECT * FROM employee WHERE id=$id";
    $result = $conn->query($query);
    $employee = $result->fetch_assoc();

    $selected_inventories_query = "SELECT inventory_id FROM employee_inventory WHERE employee_id=$id";
    $selected_inventories_result = $conn->query($selected_inventories_query);
    while ($row = $selected_inventories_result->fetch_assoc()) {
        $selected_inventories[] = $row['inventory_id'];
    }
}

$projects_query = "SELECT id, name FROM project";
$projects_result = $conn->query($projects_query);

$inventory_query = "SELECT id, name FROM inventory";
$inventory_result = $conn->query($inventory_query);

$conn->close();
?>

<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <title> Редагувати працівника </title>
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
</head>
<body>
    <h1><?= $id ? 'Редагувати працівника' : 'Додати працівника' ?></h1>
    <button id="theme-toggle">Темна тема</button>
    <form method="post" action="edit_employee.php<?= $id ? '?id=' . $id : '' ?>">
        Ім'я: <input type="text" name="name" value="<?= $employee['name'] ?>" required><br><br>
        Посада: <input type="text" name="role" value="<?= $employee['role'] ?>" required><br><br>
        Проект:
        <select name="project_id" required>
            <?php while ($project = $projects_result->fetch_assoc()) { ?>
                <option value="<?= $project['id'] ?>" <?php if ($employee['project_id'] == $project['id']) echo 'selected'; ?>>
                    <?= $project['name'] ?>
                </option>
            <?php } ?>
        </select><br><br>
        Інвентар(затисніть Ctrl для вибору декількох):
        <br>
        <select name="inventory_ids[]" multiple>
            <?php while ($inventory = $inventory_result->fetch_assoc()) { ?>
                <option value="<?= $inventory['id'] ?>" <?php if (in_array($inventory['id'], $selected_inventories)) echo 'selected'; ?>>
                    <?= $inventory['name'] ?>
                </option>
            <?php } ?>
        </select><br><br>
        <div style="text-align: center;">
            <button type="submit" class="button">Оновити</button>
        </div>
    </form>
    <button class="button" onclick="window.location.href='../../employees.php'">До списку працівників</button>
</body>
</html>