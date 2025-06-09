# Usando imagem oficial do PHP 8.3 com extensões do PostgreSQL
FROM php:8.3-fpm

# Instala extensões e utilitários necessários
RUN apt-get update && apt-get install -y \
    libpq-dev \
    zip \
    unzip \
    git \
    && docker-php-ext-install pdo pdo_pgsql

# Instala o Xdebug (opcional para debug)
RUN pecl install xdebug && docker-php-ext-enable xdebug

# Instala Composer globalmente
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Define o diretório de trabalho
WORKDIR /var/www/html

COPY composer.json ./
RUN composer install
# Copia os arquivos do projeto para dentro do container
COPY . .

# Instala PHPUnit e Guzzle com o Composer global
RUN composer require --dev phpunit/phpunit \
    && composer require --dev guzzlehttp/guzzle

# Expor a porta do PHP
EXPOSE 9000
