<?php

namespace App\TaskScheduler;

use App\EventSender\EventSender;
use DateTime;

class TaskDaemon
{
    private $eventSender;
    private $dbConnection;
    private $pidFile = '/path/to/your/pidfile.pid';

    public function __construct(EventSender $eventSender, $dbConnection)
    {
        $this->eventSender = $eventSender;
        $this->dbConnection = $dbConnection;
    }

    // Метод для обработки сигналов
    public function handleSignals()
    {
        pcntl_signal(SIGTERM, [$this, 'handleStop']);
        pcntl_signal(SIGINT, [$this, 'handleStop']);
        pcntl_signal(SIGHUP, [$this, 'handleRestart']);
    }

    // Обработчик остановки процесса
    public function handleStop()
    {
        echo "Получен сигнал для завершения работы. Завершаем выполнение...\n";
        exit;
    }

    // Обработчик перезапуска демона
    public function handleRestart()
    {
        echo "Получен сигнал для перезапуска. Сохраняем текущее состояние...\n";
        $this->saveCurrentTime();
        exit;
    }

    // Метод для сохранения текущего времени
    public function saveCurrentTime()
    {
        // Сохраняем текущее время в файл (или базу данных)
        file_put_contents('/path/to/your/statefile.txt', json_encode([
            'minute' => date('i'),
            'hour' => date('H'),
            'day' => date('d'),
            'month' => date('m'),
            'weekday' => date('w'),
        ]));
    }

    // Метод для загрузки состояния
    public function loadSavedState()
    {
        if (file_exists('/path/to/your/statefile.txt')) {
            return json_decode(file_get_contents('/path/to/your/statefile.txt'), true);
        }

        return null;
    }

    // Основной цикл демона
    public function run()
    {
        // Проверяем, есть ли сохраненное состояние
        $savedState = $this->loadSavedState();
        if ($savedState) {
            echo "Загружено сохраненное состояние: " . json_encode($savedState) . "\n";
        }

        // Создаем процесс-демон
        $this->daemonize();

        // Обрабатываем сигналы
        $this->handleSignals();

        // Бесконечный цикл, который будет постоянно проверять задания
        while (true) {
            $this->checkAndSendMessages();
            sleep(60); // Спим 60 секунд (проверяем раз в минуту)
            pcntl_signal_dispatch(); // Обрабатываем сигналы
        }
    }

    // Метод для проверки и отправки сообщений
    private function checkAndSendMessages()
    {
        $now = new DateTime();
        $currentMinute = $now->format('i');
        $currentHour = $now->format('H');
        $currentDay = $now->format('d');
        $currentMonth = $now->format('m');
        $currentWeekDay = $now->format('w');

        $tasks = $this->getScheduledTasks();

        foreach ($tasks as $task) {
            $taskTime = new DateTime($task['scheduled_time']);
            if (
                $taskTime->format('i') == $currentMinute &&
                $taskTime->format('H') == $currentHour &&
                $taskTime->format('d') == $currentDay &&
                $taskTime->format('m') == $currentMonth &&
                $taskTime->format('w') == $currentWeekDay
            ) {
                $this->eventSender->sendMessage($task['receiver_id'], $task['message']);
            }
        }
    }

    // Метод для получения заданий из базы данных
    private function getScheduledTasks()
    {
        $query = "SELECT * FROM tasks WHERE status = 'pending'";
        $result = $this->dbConnection->query($query);
        return $result->fetchAll();
    }

    // Метод для демонстрации процесса
    private function daemonize()
    {
        // Пытаемся создать фоновый процесс
        $pid = pcntl_fork();
        if ($pid == -1) {
            exit("Не удалось создать процесс\n");
        }
        if ($pid) {
            exit; // Родительский процесс завершает выполнение
        }
        // Записываем PID процесса в файл
        file_put_contents($this->pidFile, getmypid());
    }
}

// Пример использования
$eventSender = new EventSender();
$dbConnection = new PDO('mysql:host=localhost;dbname=your_database', 'username', 'password');
$taskDaemon = new TaskDaemon($eventSender, $dbConnection);

// Запуск демона
$taskDaemon->run();
