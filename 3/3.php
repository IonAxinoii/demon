<?php
// Подключение автозагрузчика и нужных классов
require_once 'vendor/autoload.php'; // Если используется Composer

namespace App\TaskScheduler;

use App\EventSender\EventSender;
use DateTime;

class TaskChecker
{
    private $eventSender;
    private $dbConnection;

    public function __construct(EventSender $eventSender, $dbConnection)
    {
        $this->eventSender = $eventSender;
        $this->dbConnection = $dbConnection;
    }

    public function checkAndSendMessages()
    {
        // Получаем текущие дату и время
        $now = new DateTime();
        $currentMinute = $now->format('i');
        $currentHour = $now->format('H');
        $currentDay = $now->format('d');
        $currentMonth = $now->format('m');
        $currentWeekDay = $now->format('w'); // 0 - воскресенье, 1 - понедельник и т.д.

        // Извлекаем все задания из базы данных
        $tasks = $this->getScheduledTasks();

        // Проверяем, нужно ли отправлять сообщение для каждого задания
        foreach ($tasks as $task) {
            $taskTime = new DateTime($task['scheduled_time']);
            if (
                $taskTime->format('i') == $currentMinute &&
                $taskTime->format('H') == $currentHour &&
                $taskTime->format('d') == $currentDay &&
                $taskTime->format('m') == $currentMonth &&
                $taskTime->format('w') == $currentWeekDay
            ) {
                // Если время совпало, отправляем сообщение
                $this->eventSender->sendMessage($task['receiver_id'], $task['message']);
            }
        }
    }

    // Метод для получения заданий из базы данных
    private function getScheduledTasks()
    {
        // Здесь должен быть запрос к базе данных для получения всех заданий
        // Пример:
        $query = "SELECT * FROM tasks WHERE status = 'pending'";
        $result = $this->dbConnection->query($query);
        
        // Преобразуем результаты в массив
        return $result->fetchAll();
    }
}

// Пример использования
$eventSender = new EventSender();
$dbConnection = new PDO('mysql:host=localhost;dbname=your_database', 'username', 'password');
$taskChecker = new TaskChecker($eventSender, $dbConnection);

// Проверяем каждую минуту (например, с помощью crontab)
$taskChecker->checkAndSendMessages();
?>
