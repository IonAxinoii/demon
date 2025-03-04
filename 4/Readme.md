**Демон для отправки уведомлений**
*Этот проект предназначен для запуска демона, который отправляет уведомления на основе запланированных задач. Скрипт постоянно проверяет, какие задачи нужно отправить в заданное время, и управляется с помощью Supervisor.*

**Описание**
*Скрипт работает как демон, проверяя каждую минуту задачи, которые нужно отправить.*
*Обрабатываются сигналы SIGTERM, SIGINT, и SIGHUP:*
*При получении сигнала SIGTERM или SIGINT демон завершает свою работу.
*При получении сигнала SIGHUP сохраняется текущее состояние (время),чтобы при перезапуске демон продолжил работать с того места, где он остановился.*
*Демон управляется и контролируется через Supervisor.*

**Требования**
1.PHP 7.4 или выше
2.База данных MySQL (или другая) с таблицей для хранения задач
3.Supervisor (для управления демоном)
4.Установка Supervisor
*Если у вас еще не установлен Supervisor, установите его следующими командами:* sudo apt-get update ; sudo apt-get install supervisor

**Установка**
*Клонируйте или скачайте репозиторий:*

Если у вас еще нет проекта, клонируйте его или скачайте файлы.

Установите зависимости (если используете Composer):

В корневой директории проекта выполните: *composer install*

**Запуск демона:**
sudo supervisorctl start task_daemon
