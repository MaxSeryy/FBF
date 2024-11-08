<?php
require_once '../../config.php';
$error_message = '';

$id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
    $role = filter_input(INPUT_POST, 'role', FILTER_SANITIZE_STRING);
    $project_id = filter_input(INPUT_POST, 'project_id', FILTER_SANITIZE_NUMBER_INT);
    $inventory_ids = filter_input(INPUT_POST, 'inventory_ids', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);

    if (!preg_match("/^[a-zA-Zа-яА-ЯёЁіІїЇєЄ'.\-\s]+$/u", $name)) {
        $error_message = "Ім'я може містити лише літери.";
    } elseif (empty($name) || empty($role) || empty($project_id)) {
        $error_message = "Будь ласка, заповніть всі поля.";
    } else {
        if ($id) {
            $stmt = $conn->prepare("UPDATE employee SET name=?, role=?, project_id=? WHERE id=?");
            if ($stmt === false) {
                $error_message = "Помилка підготовки запиту: " . $conn->error;
            } else {
                $stmt->bind_param("ssii", $name, $role, $project_id, $id);

                if ($stmt->execute() === TRUE) {
                    $delete_stmt = $conn->prepare("DELETE FROM employee_inventory WHERE employee_id=?");
                    $delete_stmt->bind_param("i", $id);
                    $delete_stmt->execute();
                    $delete_stmt->close();

                    if (!empty($inventory_ids)) {
                        $inventory_stmt = $conn->prepare("INSERT INTO employee_inventory (employee_id, inventory_id) VALUES (?, ?)");
                        if ($inventory_stmt === false) {
                            $error_message = "Помилка підготовки запиту: " . $conn->error;
                        } else {
                            foreach ($inventory_ids as $inventory_id) {
                                $inventory_stmt->bind_param("ii", $id, $inventory_id);
                                $inventory_stmt->execute();
                            }

                            $inventory_stmt->close();
                        }
                    }

                    header('Location: ../../employees.php');
                    exit();
                } else {
                    $error_message = "Помилка: " . $stmt->error;
                }

                $stmt->close();
            }
        }
    }
}

$employee = ['name' => '', 'role' => '', 'project_id' => ''];
$selected_inventories = [];

if ($id) {
    $stmt = $conn->prepare("SELECT * FROM employee WHERE id=?");
    if ($stmt === false) {
        $error_message = "Помилка підготовки запиту: " . $conn->error;
    } else {
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $employee = $result->fetch_assoc();
        $stmt->close();

        $selected_inventories_query = "SELECT inventory_id FROM employee_inventory WHERE employee_id=?";
        $selected_inventories_stmt = $conn->prepare($selected_inventories_query);
        $selected_inventories_stmt->bind_param("i", $id);
        $selected_inventories_stmt->execute();
        $selected_inventories_result = $selected_inventories_stmt->get_result();
        while ($row = $selected_inventories_result->fetch_assoc()) {
            $selected_inventories[] = $row['inventory_id'];
        }
        $selected_inventories_stmt->close();
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
    <title>Редагувати працівника</title>
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
    <form method="post" action="edit_employee.php<?= $id ? '?id=' . htmlspecialchars($id) : '' ?>">
        Ім'я: <input type="text" name="name" value="<?= htmlspecialchars($employee['name']) ?>" required><br><br>
        Посада: <input type="text" name="role" value="<?= htmlspecialchars($employee['role']) ?>" required><br><br>
        Проект:
        <select name="project_id" required>
            <?php while ($project = $projects_result->fetch_assoc()) { ?>
                <option value="<?= htmlspecialchars($project['id']) ?>" <?php if ($employee['project_id'] == $project['id']) echo 'selected'; ?>>
                    <?= htmlspecialchars($project['name']) ?>
                </option>
            <?php } ?>
        </select><br><br>
        Інвентар (затисніть Ctrl для вибору декількох):
        <br>
        <select name="inventory_ids[]" multiple>
            <?php while ($inventory = $inventory_result->fetch_assoc()) { ?>
                <option value="<?= htmlspecialchars($inventory['id']) ?>" <?php if (in_array($inventory['id'], $selected_inventories)) echo 'selected'; ?>>
                    <?= htmlspecialchars($inventory['name']) ?>
                </option>
            <?php } ?>
        </select><br><br>
        <div class="center-text">
            <button type="submit" class="button">Оновити</button>
            <br>
            <button type="button" class="button" onclick="window.location.href='../../employees.php'">До списку працівників</button>
            <br>
            <?php if (!empty($error_message)): ?>
                <div id="error-message" class="error-message"><?= htmlspecialchars($error_message) ?></div>
            <?php endif; ?>
        </div>
    </form>
</body>

</html>