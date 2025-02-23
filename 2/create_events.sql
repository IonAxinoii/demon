-- Создание базы данных (если она еще не существует)
CREATE DATABASE IF NOT EXISTS your_database;

-- Выбор базы данных для работы
USE your_database;

-- Создание таблицы для хранения событий
CREATE TABLE IF NOT EXISTS events (
    id INT AUTO_INCREMENT PRIMARY KEY,  -- Идентификатор события
    name VARCHAR(255) NOT NULL,          -- Имя события
    receiver INT NOT NULL,              -- Айди получателя
    text TEXT NOT NULL,                 -- Текст напоминания
    cron VARCHAR(255) NOT NULL          -- Cron-выражение
);
