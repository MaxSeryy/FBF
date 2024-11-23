<?php
require_once '../../config.php';

function sanitize_input($data) {
    return htmlspecialchars(trim($data));
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = sanitize_input(filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING));
    $rent_cost = filter_input(INPUT_POST, 'rent_cost', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);

    if (empty($name) || empty($rent_cost)) {
        echo "Будь ласка, заповніть всі поля.";
        exit();
    }

    $stmt = $conn->prepare("INSERT INTO inventory (name, rent_cost) VALUES (?, ?)");
    if ($stmt === false) {
        echo "Помилка підготовки запиту: " . $conn->error;
        exit();
    }

    $stmt->bind_param("sd", $name, $rent_cost);

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
    <script src="../../scripts/theme.js" defer></script>
    <script src="../../scripts/message.js" defer></script>
    <title>Додати інвентар</title>
</head>
<body>
<button id="theme-toggle">Темна тема</button>
    <h1>Додати новий інвентар</h1>
    <form method="post" action="add_inventory.php">
        Назва: <input type="text" name="name" required><br><br>
        Вартість оренди: <input type="number" step="0.01" name="rent_cost" required><br><br>
        <div class="center-text">
            <button type="submit" class="button">Додати</button>
            <br>
            <button type="button" class="button" onclick="window.location.href='../../inventory.php'">До списку інвентарю</button>
        </div>
    </form>
</body>
</html>