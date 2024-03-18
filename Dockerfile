FROM php:8.2-fpm
ENV NODE_MAJOR=14

# Copy composer.lock and composer.json
COPY composer.json /var/www/

# Set working directory
WORKDIR /var/www

# Install dependencies
RUN apt-get update && apt-get install -y \
    build-essential \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    locales \
    zip \
    jpegoptim optipng pngquant gifsicle \
    vim \
    unzip \
    git \
    curl \
    libmemcached-dev \
    libzip-dev \
    libonig-dev \
    libxml2-dev \
    librdkafka-dev \
    supervisor \
    libpq-dev \
    openssh-server \
    sqlite3 \
    cron \
    python3

# Install Nodejs
RUN curl -sL https://deb.nodesource.com/setup_12.x | bash - \
    && DEBIAN_FRONTEND=noninteractive apt-get install nodejs -yq
   # && npm i -g --engine-strict --force npm \
   # && curl -o- -L https://yarnpkg.com/install.sh | bash \
   # && npm cache clean --force

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd mysqli
#RUN docker-php-ext-configure gd --with-gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/ --with-png-dir=/usr/include/
RUN docker-php-ext-install gd

# Install composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer --version=1.10.26
#--version=1.0.0-alpha8

# Add user for laravel application
RUN groupadd -g 1000 www
RUN useradd -u 1000 -ms /bin/bash -g www www

# Copy existing application directory contents
COPY . /var/www

RUN chmod -R 777 /var/www

# Copy existing application directory permissions
COPY --chown=www:www . /var/www

# Change current user to www
USER www

#Install dependecies
#RUN COMPOSER_MEMORY_LIMIT=-1 composer install

#Composer dumpautoload
#RUN COMPOSER_MEMORY_LIMIT=-1 composer dumpautoload
# RUN composer install --optimize-autoloader --no-interaction  --no-progress

# COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Expose port 9000 and start php-fpm server
EXPOSE 9000
CMD ["php-fpm"]