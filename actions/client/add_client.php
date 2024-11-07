<?php
require_once '../../config.php';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
    $contact_info = filter_input(INPUT_POST, 'contact_info', FILTER_SANITIZE_STRING);

    if (!preg_match("/^[a-zA-Zа-яА-ЯёЁіІїЇєЄ'-]+$/u", $name)) {
        $error_message = "Ім'я може містити лише літери.";
    } else {
        $query = "INSERT INTO client (name, contact_info) VALUES (?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ss", $name, $contact_info);
        if ($stmt->execute()) {
            header('Location: ../../clients.php');
            exit();
        } else {
            $error_message = "Помилка: " . $stmt->error;
        }
        $stmt->close();
        $conn->close();
    }
}
?>
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Додати клієнта</title>
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
<h1>Додати клієнта</h1>
<button id="theme-toggle">Темна тема</button>
<form action="" method="post">
    Ім'я: <input type="text" name="name" required><br><br>
    Контактна інформація: <input type="text" name="contact_info" required><br><br>
    <div style="text-align: center;">
    <?php if (isset($error_message)): ?>
    <div id="error-message" style="color: red; text-align: center;"><?= htmlspecialchars($error_message) ?></div>
<?php endif; ?>
        <button type="submit" class="button">Додати</button>
    </div>
</form>
<br>
<button class="button" onclick="window.location.href='../../clients.php'">До списку клієнтів</button>
</body>
</html>