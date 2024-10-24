<?php
require_once '../../config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $contact_info = $_POST['contact_info'];

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
    $id = $_GET['id'];
    $query = "SELECT * FROM client WHERE id=$id";
    $result = $conn->query($query);
    $row = $result->fetch_assoc();
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
    <input type="hidden" name="id" value="<?php echo $id; ?>">
    Ім'я: <input type="text" name="name" value="<?php echo $row['name']; ?>" required><br><br>
    Контактна інформація: <input type="text" name="contact_info" value="<?php echo $row['contact_info']; ?>" required><br><br>
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