<?php
session_start();
require_once '../../config.php';

// Sanitize the 'id'
$id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sanitize and validate input data
    $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
    $start = filter_input(INPUT_POST, 'start', FILTER_SANITIZE_STRING);
    $end = filter_input(INPUT_POST, 'end', FILTER_SANITIZE_STRING);
    $status = filter_input(INPUT_POST, 'status', FILTER_SANITIZE_STRING);
    $manager_id = filter_input(INPUT_POST, 'manager_id', FILTER_SANITIZE_NUMBER_INT);
    $client_id = filter_input(INPUT_POST, 'client_id', FILTER_SANITIZE_NUMBER_INT);

    // Check if any field is empty
    if (empty($name) || empty($start) || empty($end) || empty($status) || empty($manager_id) || empty($client_id)) {
        echo "Будь ласка, заповніть всі поля.";
        exit();
    }

    // Prepare the SQL statement to prevent SQL injection
    $stmt = $conn->prepare("UPDATE project SET name=?, start=?, end=?, status=?, manager_id=?, client_id=? WHERE id=?");
    if ($stmt === false) {
        echo "Помилка підготовки запиту: " . $conn->error;
        exit();
    }

    // Bind parameters to the prepared statement
    $stmt->bind_param("ssssiis", $name, $start, $end, $status, $manager_id, $client_id, $id);

    // Execute the statement and check for errors
    if ($stmt->execute() === TRUE) {
        header('Location: ../../projects.php');
        exit();
    } else {
        echo "Помилка виконання запиту: " . $stmt->error;
    }

    $stmt->close();
}

// Prepare the SQL statement to prevent SQL injection
$query = "SELECT * FROM project WHERE id=?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$project = $result->fetch_assoc();
$stmt->close();

$managers_query = "SELECT id, name FROM manager";
$managers_result = $conn->query($managers_query);
$clients_query = "SELECT id, name FROM client";
$clients_result = $conn->query($clients_query);
?>
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Редагувати проект</title>
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
        });
    </script>
</head>
<body>
    <h1>Редагувати проект</h1>
    <button id="theme-toggle">Темна тема</button>
    <form action="edit_project.php?id=<?= htmlspecialchars($id) ?>" method="post">
        Назва проекту: <input type="text" name="name" value="<?= htmlspecialchars($project['name']) ?>" required><br><br>
        Дата початку: <input type="date" name="start" value="<?= htmlspecialchars($project['start']) ?>" required><br><br>
        Дата завершення: <input type="date" name="end" value="<?= htmlspecialchars($project['end']) ?>" required><br><br>
        Статус:
        <select name="status" required>
            <option value="Building" <?= $project['status'] === 'Building' ? 'selected' : '' ?>>Будується</option>
            <option value="Ended" <?= $project['status'] === 'Ended' ? 'selected' : '' ?>>Завершено</option>
            <option value="Planning" <?= $project['status'] === 'Planning' ? 'selected' : '' ?>>Планується</option>
        </select><br><br>
        Менеджер:
        <select name="manager_id" required>
            <?php while ($manager = $managers_result->fetch_assoc()) { ?>
                <option value="<?= htmlspecialchars($manager['id']) ?>" <?= $project['manager_id'] == $manager['id'] ? 'selected' : '' ?>><?= htmlspecialchars($manager['name']) ?></option>
            <?php } ?>
        </select><br><br>
        Клієнт:
        <select name="client_id" required>
            <?php while ($client = $clients_result->fetch_assoc()) { ?>
                <option value="<?= htmlspecialchars($client['id']) ?>" <?= $project['client_id'] == $client['id'] ? 'selected' : '' ?>><?= htmlspecialchars($client['name']) ?></option>
            <?php } ?>
        </select><br><br>
        <div style="text-align: center;">
            <button type="submit" class="button">Зберегти зміни</button>
        </div>
    </form>
    <button class="button" onclick="window.location.href='../../projects.php'">До списку проектів</button>
</body>
</html>
<?php
$conn->close();
?>


<!-- <?php
session_start();
require_once '../../config.php';

$id = $_GET['id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $start = $_POST['start'];
    $end = $_POST['end'];
    $status = $_POST['status'];
    $manager_id = $_POST['manager_id'];
    $client_id = $_POST['client_id'];

    if (empty($name) || empty($start) || empty($end) || empty($status) || empty($manager_id) || empty($client_id)) {
        echo "Будь ласка, заповніть всі поля.";
        exit();
    }

    $stmt = $conn->prepare("UPDATE project SET name=?, start=?, end=?, status=?, manager_id=?, client_id=? WHERE id=?");
    if ($stmt === false) {
        echo "Помилка підготовки запиту: " . $conn->error;
        exit();
    }

    $stmt->bind_param("ssssiis", $name, $start, $end, $status, $manager_id, $client_id, $id);

    if ($stmt->execute() === TRUE) {
        header('Location: ../../projects.php');
        exit();
    } else {
        echo "Помилка виконання запиту: " . $stmt->error;
    }

    $stmt->close();
}

$query = "SELECT * FROM project WHERE id=?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$project = $result->fetch_assoc();
$stmt->close();

$managers_query = "SELECT id, name FROM manager";
$managers_result = $conn->query($managers_query);

$clients_query = "SELECT id, name FROM client";
$clients_result = $conn->query($clients_query);
?>

<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
        });
    </script>
    <title>Редагувати проект</title>
</head>
<body>
<h1>Редагувати проект</h1>
<button id="theme-toggle">Темна тема</button>
<form action="edit_project.php?id=<?= htmlspecialchars($id) ?>" method="post">
    <input type="hidden" name="id" value="<?= htmlspecialchars($project['id']) ?>">
    Назва проекту: <input type="text" name="name" value="<?= htmlspecialchars($project['name']) ?>" required><br><br>
    Дата початку: <input type="date" name="start" value="<?= htmlspecialchars($project['start']) ?>" required><br><br>
    Дата завершення: <input type="date" name="end" value="<?= htmlspecialchars($project['end']) ?>" required><br><br>
    Статус: 
    <select name="status" required>
        <option value="Building" <?= $project['status'] == 'Building' ? 'selected' : '' ?>>Будується</option>
        <option value="Ended" <?= $project['status'] == 'Ended' ? 'selected' : '' ?>>Завершено</option>
        <option value="Planning" <?= $project['status'] == 'Planning' ? 'selected' : '' ?>>Планується</option>
    </select><br><br>
    Менеджер:
    <select name="manager_id" required>
        <?php while ($manager = $managers_result->fetch_assoc()) { ?>
            <option value="<?= htmlspecialchars($manager['id']) ?>" <?= $project['manager_id'] == $manager['id'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($manager['name']) ?>
            </option>
        <?php } ?>
    </select><br><br>
    Клієнт:
    <select name="client_id" required>
        <?php while ($client = $clients_result->fetch_assoc()) { ?>
            <option value="<?= htmlspecialchars($client['id']) ?>" <?= $project['client_id'] == $client['id'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($client['name']) ?>
            </option>
        <?php } ?>
    </select><br><br>
    <div style="text-align: center;">
        <button type="submit" class="button">Оновити</button>
    </div>
</form>

<br>
<button class="button" onclick="window.location.href='../../projects.php'">До списку проектів</button>
</body>
</html>

<?php
$conn->close();
?> -->