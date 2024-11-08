<?php
require_once '../../config.php';
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
    $role = filter_input(INPUT_POST, 'role', FILTER_SANITIZE_STRING);
    $project_id = filter_input(INPUT_POST, 'project_id', FILTER_SANITIZE_NUMBER_INT);
    $inventory_ids = filter_input(INPUT_POST, 'inventory_ids', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);

    if (!preg_match("/^[a-zA-Zа-яА-ЯёЁіІїЇєЄ'.\-\s]+$/u", $name)) {
        $error_message = "Ім'я може містити лише літери.";
    } elseif (empty($inventory_ids)) {
        $error_message = "Помилка: Ви повинні вибрати хоча б один інвентар.";
    } else {
        $stmt = $conn->prepare("INSERT INTO employee (name, role, project_id) VALUES (?, ?, ?)");
        if ($stmt === false) {
            $error_message = "Помилка підготовки запиту: " . $conn->error;
        } else {
            $stmt->bind_param("ssi", $name, $role, $project_id);

            if ($stmt->execute() === TRUE) {
                $employee_id = $stmt->insert_id;

                $inventory_stmt = $conn->prepare("INSERT INTO employee_inventory (employee_id, inventory_id) VALUES (?, ?)");
                if ($inventory_stmt === false) {
                    $error_message = "Помилка підготовки запиту: " . $conn->error;
                } else {
                    foreach ($inventory_ids as $inventory_id) {
                        $inventory_stmt->bind_param("ii", $employee_id, $inventory_id);
                        $inventory_stmt->execute();
                    }

                    $inventory_stmt->close();
                    header('Location: ../../employees.php');
                    exit();
                }
            } else {
                $error_message = "Помилка: " . $stmt->error;
            }

            $stmt->close();
        }
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
    <link rel="stylesheet" href="../../styles.css">
    <script src="../../scripts/theme.js" defer></script>
    <script src="../../scripts/message.js" defer></script>
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
        <div class="center-text">
            <button type="submit" class="button">Додати</button>
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