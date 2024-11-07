<?php
require_once '../../config.php';

// Sanitize the 'id' parameter to prevent SQL injection
$id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sanitize and validate input data
    $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
    $contact_info = filter_input(INPUT_POST, 'contact_info', FILTER_SANITIZE_STRING);

    if (empty($name) || empty($contact_info)) {
        echo "Будь ласка, заповніть всі поля.";
        exit();
    }

    // Prepare the SQL statement to prevent SQL injection
    $stmt = $conn->prepare("UPDATE manager SET name=?, contact_info=? WHERE id=?");
    if ($stmt === false) {
        echo "Помилка підготовки запиту: " . $conn->error;
        exit();
    }

    // Bind parameters to the prepared statement
    $stmt->bind_param("ssi", $name, $contact_info, $id);

    // Execute the statement and check for errors
    if ($stmt->execute() === TRUE) {
        header('Location: ../../managers.php');
        exit();
    } else {
        echo "Помилка виконання запиту: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
    echo '<br><a href="../../managers.php">Повернутися до менеджерів</a>';
    exit();
}

// Prepare the SQL statement to prevent SQL injection
$stmt = $conn->prepare("SELECT * FROM manager WHERE id=?");
if ($stmt === false) {
    echo "Помилка підготовки запиту: " . $conn->error;
    exit();
}

$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$manager = $result->fetch_assoc();
$stmt->close();
$conn->close();
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
        });
    </script>
    <title>Редагувати менеджера</title>
</head>
<body>
    <h1>Редагувати менеджера</h1>
    <form method="post" action="edit_manager.php?id=<?= htmlspecialchars($id) ?>">
        Ім'я: <input type="text" name="name" value="<?= htmlspecialchars($manager['name']) ?>" required><br><br>
        Контактна інформація: <input type="text" name="contact_info" value="<?= htmlspecialchars($manager['contact_info']) ?>" required><br><br>
        <div style="text-align: center;">
            <button type="submit" class="button">Оновити</button>
        </div>
    </form>
    <button class="button" onclick="window.location.href='../../managers.php'">До списку менеджерів</button>
</body>
</html>