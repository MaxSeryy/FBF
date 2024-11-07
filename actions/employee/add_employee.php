<?php
require_once '../../config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
    $role = filter_input(INPUT_POST, 'role', FILTER_SANITIZE_STRING);
    $project_id = filter_input(INPUT_POST, 'project_id', FILTER_SANITIZE_NUMBER_INT);
    $inventory_ids = filter_input(INPUT_POST, 'inventory_ids', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);

    if (empty($inventory_ids)) {
        echo "Помилка: Ви повинні вибрати хоча б один інвентар.";
    } else {
        $stmt = $conn->prepare("INSERT INTO employee (name, role, project_id) VALUES (?, ?, ?)");
        if ($stmt === false) {
            echo "Помилка підготовки запиту: " . $conn->error;
            exit();
        }

        $stmt->bind_param("ssi", $name, $role, $project_id);

        if ($stmt->execute() === TRUE) {
            $employee_id = $stmt->insert_id;

            $inventory_stmt = $conn->prepare("INSERT INTO employee_inventory (employee_id, inventory_id) VALUES (?, ?)");
            if ($inventory_stmt === false) {
                echo "Помилка підготовки запиту: " . $conn->error;
                exit();
            }

            foreach ($inventory_ids as $inventory_id) {
                $inventory_stmt->bind_param("ii", $employee_id, $inventory_id);
                $inventory_stmt->execute();
            }

            $inventory_stmt->close();
            header('Location: ../../employees.php');
        } else {
            echo "Помилка: " . $stmt->error;
        }

        $stmt->close();
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
                <option value="<?= htmlspecialchars($project['id']) ?>"><?= htmlspecialchars($project['name']) ?></option>
            <?php } ?>
        </select><br><br>
        Інвентар (затисніть Ctrl для вибору декількох):
        <br>
        <select name="inventory_ids[]" multiple required>
            <?php while ($inventory = $inventory_result->fetch_assoc()) { ?>
                <option value="<?= htmlspecialchars($inventory['id']) ?>"><?= htmlspecialchars($inventory['name']) ?></option>
            <?php } ?>
        </select><br><br>
        <div style="text-align: center;">
            <button type="submit" class="button">Додати</button>
        </div>
    </form>
    <button class="button" onclick="window.location.href='../../employees.php'">До списку працівників</button>
</body>
</html>