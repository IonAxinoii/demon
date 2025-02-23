FROM php:8.0-apache

# Установка расширений для работы с MySQL
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd pdo pdo_mysql

# Копирование файлов проекта
COPY . /var/www/html/

# Обновление конфигурации Apache для работы с PHP
RUN a2enmod rewrite

# Открытие порта 80 для доступа к приложению
EXPOSE 80
