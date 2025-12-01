FROM php:8.3-fpm

# Diretório de trabalho
WORKDIR /var/www/

# Atualiza e instala pacotes do sistema + bibliotecas de desenvolvimento para GD
RUN apt-get update && apt-get install -y \
    supervisor \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libwebp-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    libpq-dev \
    libzip-dev \
    cron \
    iputils-ping \
    telnet \
    net-tools \
    iproute2 \
    && mkdir -p /var/log/supervisor \
    && rm -rf /var/lib/apt/lists/*

# Extensões do PHP (Postgres, MySQL, Zip)
RUN docker-php-ext-install pgsql pdo pdo_pgsql pdo_mysql zip

# GD (com freetype, jpeg e webp)
RUN docker-php-ext-configure gd \
    --with-freetype=/usr/include/ \
    --with-jpeg=/usr/include/ \
    --with-webp=/usr/include/ \
    && docker-php-ext-install -j$(nproc) gd

# Redis via PECL
RUN pecl install redis && docker-php-ext-enable redis

# Copia o código da aplicação para dentro do container
ADD . /var/www

# Copia configuração do Supervisor
COPY docker_config_files/supervisor/supervisord.conf /etc/supervisor/supervisord.conf

# Permissões para storage e cache
RUN chmod -R 777 /var/www/storage /var/www/bootstrap/cache && \
    chown -R www-data:www-data /var/www

# Cria diretório de logs de queue/schedule se ainda não existir
RUN mkdir -p /var/www/storage/logs && \
    touch /var/www/storage/logs/queue.log && \
    touch /var/www/storage/logs/schedule.log

# Porta padrão do PHP-FPM
EXPOSE 9000

# Comando padrão para o serviço "app"
CMD ["php-fpm"]