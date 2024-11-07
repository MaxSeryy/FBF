<?php
require_once '../../config.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
    $start = filter_input(INPUT_POST, 'start', FILTER_SANITIZE_STRING);
    $end = filter_input(INPUT_POST, 'end', FILTER_SANITIZE_STRING);
    $status = filter_input(INPUT_POST, 'status', FILTER_SANITIZE_STRING);
    $manager_id = filter_input(INPUT_POST, 'manager_id', FILTER_SANITIZE_NUMBER_INT);
    $client_id = filter_input(INPUT_POST, 'client_id', FILTER_SANITIZE_NUMBER_INT);

    if (empty($name) || empty($start) || empty($end) || empty($status) || empty($manager_id) || empty($client_id)) {
        echo "Будь ласка, заповніть всі поля.";
        exit();
    }

    $stmt = $conn->prepare("INSERT INTO project (name, start, end, status, manager_id, client_id) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssii", $name, $start, $end, $status, $manager_id, $client_id);
    if ($stmt->execute()) {
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
?>
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Додати новий проект</title>
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
        <div style="text-align: center;">
            <button type="submit" class="button">Додати</button>
        </div>
    </form>
    <button class="button" onclick="window.location.href='../../projects.php'">До списку проектів</button>
</body>
</html>
<?php
$conn->close();
?>


<!-- <?php
require_once '../../config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $start = $_POST['start'];
    $end = $_POST['end'];
    $status = $_POST['status'];
    $manager_id = $_POST['manager_id'];
    $client_id = $_POST['client_id'];
    $insert_query = "INSERT INTO project (name, start, end, status, manager_id, client_id) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($insert_query);
    $stmt->bind_param("ssssii", $name, $start, $end, $status, $manager_id, $client_id);
    if ($stmt->execute()) {
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
?>

<!DOCTYPE html>
<html lang="uk">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Додати новий проект</title>
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
                <option value="<?php echo $manager['id']; ?>"><?php echo $manager['name']; ?></option>
            <?php } ?>
        </select><br><br>
        Клієнт:
        <select name="client_id" required>
            <?php while ($client = $clients_result->fetch_assoc()) { ?>
                <option value="<?php echo $client['id']; ?>"><?php echo $client['name']; ?></option>
            <?php } ?>
        </select><br> <br>

        <div style="text-align: center;">
            <button type="submit" class="button">Додати</button>
        </div>
    </form>
    <button class="button" onclick="window.location.href='../../projects.php'">До списку проектів</button>
    <br>
</body>

</html>

<?php
$conn->close();
?> -->