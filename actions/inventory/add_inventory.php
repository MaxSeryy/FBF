<?php
require_once '../../config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $rent_cost = $_POST['rent_cost'];

    if (empty($name) || empty($rent_cost)) {
        echo "Будь ласка, заповніть всі поля.";
        exit();
    }

    $stmt = $conn->prepare("INSERT INTO inventory (name, rent_cost) VALUES (?, ?)");
    $stmt->bind_param("ss", $name, $rent_cost);

    if ($stmt->execute() === TRUE) {
        header('Location: ../../inventory.php');
        exit();
    } else {
        echo "Помилка: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
    echo '<br><a href="../../inventory.php">Повернутися до інвентарю</a>';
    exit();
}
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
    <title>Додати інвентар</title>
</head>
<body>
    <h1>Додати новий інвентар</h1>
    <form method="post" action="add_inventory.php">
        Назва: <input type="text" name="name" required><br><br>
        Вартість оренди: <input type="number" name="rent_cost" required><br><br>
        <div style="text-align: center;">
            <button type="submit" class="button">Додати</button>
        </div>
    </form>
    <button class="button" onclick="window.location.href='../../inventory.php'">До списку інвентарю</button>
</body>
</html>