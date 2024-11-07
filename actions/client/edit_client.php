<?php
require_once '../../config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);
    $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
    $contact_info = filter_input(INPUT_POST, 'contact_info', FILTER_SANITIZE_STRING);

    $update_query = "UPDATE client SET name=?, contact_info=? WHERE id=?";
    $stmt = $conn->prepare($update_query);
    $stmt->bind_param("ssi", $name, $contact_info, $id);

    if ($stmt->execute()) {
        header('Location: ../../clients.php');
        exit();
    } else {
        echo "Помилка: " . $stmt->error;
    }

    $stmt->close();
} else {
    $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
    $query = "SELECT * FROM client WHERE id=?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Редагувати клієнта</title>
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
<h1>Редагувати клієнта</h1>
<button id="theme-toggle">Темна тема</button>
<form action="" method="post">
    <input type="hidden" name="id" value="<?= htmlspecialchars($id) ?>">
    Ім'я: <input type="text" name="name" value="<?= htmlspecialchars($row['name']) ?>" required><br><br>
    Контактна інформація: <input type="text" name="contact_info" value="<?= htmlspecialchars($row['contact_info']) ?>" required><br><br>
    <div style="text-align: center;">
    <button type="submit" class="button">Оновити</button>
</div>
</form>

<br>
<button class="button" onclick="window.location.href='../../clients.php'">До списку клієнтів</button>
</body>
</html>

<?php
$conn->close();
?>