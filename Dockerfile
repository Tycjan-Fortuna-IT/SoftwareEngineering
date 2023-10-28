FROM ubuntu:latest

# Install apache, PHP, and supplimentary programs. openssh-server, curl, and lynx-cur are for debugging the container.

RUN apt-get update

RUN apt install software-properties-common apt-transport-https -y

RUN add-apt-repository ppa:ondrej/php -y

RUN apt-get update && apt-get -y upgrade && DEBIAN_FRONTEND=noninteractive apt-get -y install \
    apache2 \
    php8.1 \
    php8.1-pgsql \
    libapache2-mod-php8.1 \
    curl \
    php8.1-curl \
    nano \
    php8.1-xml \
    php8.1-dom \
    php8.1-zip \
    php8.1-bcmath \
    php8.1-tokenizer \
    php8.1-mbstring \
    php8.1-pdo \
    php8.1-gd \
    php8.1-cli \
    php8.1-redis \
    git \
    cron

# Enable apache mods.

RUN a2enmod php8.1

RUN a2enmod rewrite

RUN a2enmod ssl

RUN a2ensite default-ssl

# Update the PHP.ini file, enable <? ?> tags and quieten logging.

RUN sed -i "s/short_open_tag = Off/short_open_tag = On/" /etc/php/8.1/apache2/php.ini

RUN sed -i "s/error_reporting = .*$/error_reporting = E_ERROR | E_WARNING | E_PARSE/" /etc/php/8.1/apache2/php.ini

# Manually set up the apache environment variables

ENV APACHE_RUN_USER www-data

ENV APACHE_RUN_GROUP www-data

ENV APACHE_LOG_DIR /var/log/apache2

ENV APACHE_LOCK_DIR /var/lock/apache2

ENV APACHE_PID_FILE /var/run/apache2.pid

# Expose ports for apache server

EXPOSE 443

EXPOSE 80

# Update the default apache site with the config we created

ADD apache-config.conf /etc/apache2/sites-enabled/000-default.conf

# From now on, we need to work in our website directory

WORKDIR /var/www/html

RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"

RUN php -r "if (hash_file('sha384', 'composer-setup.php') === 'e21205b207c3ff031906575712edab6f13eb0b361f2085f1f1237b7126d785e826a450292b6cfd1d64d92e6563bbde02') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"

RUN php composer-setup.php

RUN php -r "unlink('composer-setup.php');"

RUN mv composer.phar /usr/local/bin/composer

# Add only composer files to run composer as a separate step from following ones

ADD composer.json composer.lock /var/www/html/

RUN composer install --no-scripts --no-autoloader

# Copy all project content (excluding exceptions defined in .dockerignore)

ADD . /var/www/html

RUN chown -R www-data:www-data storage/

# Autoloader and composer scripts

RUN composer dump-autoload --optimize

# Run apache

# CMD php artisan migrate && /usr/sbin/apache2ctl -D FOREGROUND
CMD /usr/sbin/apache2ctl -D FOREGROUND