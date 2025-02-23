<?php

// Подключаемся к базе данных
$dsn = 'mysql:host=localhost;dbname=your_database';
$username = 'your_username';
$password = 'your_password';
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];
try {
    $pdo = new PDO($dsn, $username, $password, $options);
} catch (PDOException $e) {
    echo 'Connection failed: ' . $e->getMessage();
    exit;
}

// Парсим аргументы командной строки
$options = getopt('', ['name:', 'receiver:', 'text:', 'cron:']);
if (!isset($options['name'], $options['receiver'], $options['text'], $options['cron'])) {
    echo "Все параметры должны быть указаны: --name, --receiver, --text, --cron\n";
    exit(1);
}

// Извлекаем параметры
$name = $options['name'];
$receiver = $options['receiver'];
$text = $options['text'];
$cron = $options['cron'];

// Подготовка SQL запроса для вставки данных
$sql = "INSERT INTO events (name, receiver, text, cron) VALUES (:name, :receiver, :text, :cron)";
$stmt = $pdo->prepare($sql);

// Привязываем параметры и выполняем запрос
$stmt->execute([
    ':name' => $name,
    ':receiver' => $receiver,
    ':text' => $text,
    ':cron' => $cron,
]);

echo "Событие успешно сохранено в базе данных.\n";
