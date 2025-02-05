FROM php:8.0-apache

RUN docker-php-ext-install mysqli pdo pdo_mysql

RUN apt-get update && apt-get install -y libpng-dev libjpeg-dev libfreetype6-dev && \
    docker-php-ext-configure gd --with-freetype --with-jpeg && \
    docker-php-ext-install gd

COPY . /home/repos/agendamentoDeViagens

RUN sed -i 's|DocumentRoot /var/www/html|DocumentRoot /home/repos/agendamentoDeViagens|' /etc/apache2/sites-available/000-default.conf

RUN chown -R www-data:www-data /home/repos/agendamentoDeViagens && \
    chmod -R 755 /home/repos/agendamentoDeViagens

RUN echo "<Directory /home/repos/agendamentoDeViagens>\n\
    AllowOverride All\n\
    Require all granted\n\
    </Directory>" >> /etc/apache2/apache2.conf


EXPOSE 80
