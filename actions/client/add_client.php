<?php
require_once '../../config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $contact_info = $_POST['contact_info'];

    $query = "INSERT INTO client (name, contact_info) VALUES ('$name', '$contact_info')";

    if ($conn->query($query) === TRUE) {
        header('Location: ../../clients.php');
    } else {
        echo "Помилка: " . $query . "<br>" . $conn->error;
    }
    $conn->close();
    echo '<br><a href="../../clients.php">Повернутися до списку клієнтів</a>';
    exit();
} else {
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
            <button type="submit" class="button">Додати</button>
        </div>
    </form>

    <br>
    <button class="button" onclick="window.location.href='../../clients.php'">До списку клієнтів</button>
    </body>
    </html>

    <?php
}
?>