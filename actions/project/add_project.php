<?php
require_once '../../config.php';

function sanitize_input($data) {
    return htmlspecialchars(trim($data));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize_input(filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING));
    $start = sanitize_input(filter_input(INPUT_POST, 'start', FILTER_SANITIZE_STRING));
    $end = sanitize_input(filter_input(INPUT_POST, 'end', FILTER_SANITIZE_STRING));
    $status = sanitize_input(filter_input(INPUT_POST, 'status', FILTER_SANITIZE_STRING));
    $manager_id = filter_input(INPUT_POST, 'manager_id', FILTER_SANITIZE_NUMBER_INT);
    $client_id = filter_input(INPUT_POST, 'client_id', FILTER_SANITIZE_NUMBER_INT);

    if (empty($name) || empty($start) || empty($end) || empty($status) || empty($manager_id) || empty($client_id)) {
        echo "Будь ласка, заповніть всі поля.";
        exit();
    }

    $stmt = $conn->prepare("INSERT INTO project (name, start, end, status, manager_id, client_id) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssii", $name, $start, $end, $status, $manager_id, $client_id);
    if ($stmt->execute()) {
        $stmt->close();
        $conn->close();
        header('Location: ../../projects.php');
        exit();
    } else {
        echo "Помилка: " . $stmt->error;
    }
    $stmt->close();
}

$managers_query = "SELECT id, name FROM manager";
$managers_result = $conn->query($managers_query);
$clients_query = "SELECT id, name FROM client";
$clients_result = $conn->query($clients_query);
$conn->close();
?>
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Додати новий проект</title>
    <link rel="stylesheet" href="../../styles.css">
    <script src="../../scripts/theme.js" defer></script>
</head>
<body>
    <h1>Додати новий проект</h1>
    <button id="theme-toggle">Темна тема</button>
    <form action="add_project.php" method="post">
        Назва проекту: <input type="text" name="name" required><br><br>
        Дата початку: <input type="date" name="start" required><br><br>
        Дата завершення: <input type="date" name="end" required><br><br>
        Статус:
        <select name="status" required>
            <option value="Building">Будується</option>
            <option value="Ended">Завершено</option>
            <option value="Planning">Планується</option>
        </select><br><br>
        Менеджер:
        <select name="manager_id" required>
            <?php while ($manager = $managers_result->fetch_assoc()) { ?>
                <option value="<?= htmlspecialchars($manager['id']) ?>"><?= htmlspecialchars($manager['name']) ?></option>
            <?php } ?>
        </select><br><br>
        Клієнт:
        <select name="client_id" required>
            <?php while ($client = $clients_result->fetch_assoc()) { ?>
                <option value="<?= htmlspecialchars($client['id']) ?>"><?= htmlspecialchars($client['name']) ?></option>
            <?php } ?>
        </select><br><br>
        <div class="center-text">
            <button type="submit" class="button">Додати</button>
            <br>
            <button type="button" class="button" onclick="window.location.href='../../projects.php'">До списку проектів</button>
        </div>
    </form>
</body>
</html>