<!DOCTYPE html>
<html lang="ru">
<head>
    <title>{{ title }}</title>
</head>
<body>
    <h1>{{ content }}</h1>

    <?php
        // Примеры PHP-кода
        for ($i = 1; $i <= 5; $i++) {
            echo "<p>Это абзац номер $i</p>";
        }

        $name = 'John';
        echo "<p>Привет, $name!</p>";
    ?>
</body>
</html>
