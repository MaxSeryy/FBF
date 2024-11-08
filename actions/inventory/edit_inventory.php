<?php
require_once '../../config.php';

$id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
    $rent_cost = filter_input(INPUT_POST, 'rent_cost', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);

    if (empty($name) || empty($rent_cost)) {
        echo "Будь ласка, заповніть всі поля.";
        exit();
    }

    $stmt = $conn->prepare("UPDATE inventory SET name=?, rent_cost=? WHERE id=?");
    if ($stmt === false) {
        echo "Помилка підготовки запиту: " . $conn->error;
        exit();
    }

    $stmt->bind_param("sdi", $name, $rent_cost, $id);

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

$stmt = $conn->prepare("SELECT * FROM inventory WHERE id=?");
if ($stmt === false) {
    echo "Помилка підготовки запиту: " . $conn->error;
    exit();
}

$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$inventory = $result->fetch_assoc();
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="../../styles.css">
    <script src="../../scripts/theme.js" defer></script>
    <script src="../../scripts/message.js" defer></script>
    <title>Редагувати інвентар</title>
</head>
<body>
<button id="theme-toggle">Темна тема</button>
    <h1>Редагувати інвентар</h1>
    <form method="post" action="edit_inventory.php?id=<?= htmlspecialchars($id) ?>">
        Назва: <input type="text" name="name" value="<?= htmlspecialchars($inventory['name']) ?>" required><br><br>
        Вартість оренди: <input type="number" step="0.01" name="rent_cost" value="<?= htmlspecialchars($inventory['rent_cost']) ?>" required><br><br>
        <div class="center-text">
            <button type="submit" class="button">Оновити</button>
            <br>
            <button type="button" class="button" onclick="window.location.href='../../inventory.php'">До списку інвентарю</button>
        </div>
    </form>
   
</body>
</html>