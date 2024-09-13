FROM php:8.2-apache

ENV WORKDIR=/var/www/html
ENV APACHE_DOCUMENT_ROOT=$WORKDIR/public

WORKDIR $WORKDIR
COPY . /var/www/html

#apache configration
RUN echo "ServerName lifesycle-api" >> /etc/apache2/apache2.conf
RUN a2enmod rewrite && a2enmod ssl && a2enmod socache_shmcb
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf
RUN chown -R www-data:www-data /var/www

RUN apt-get update && sed -i -E 's/(CipherString\s*=\s*DEFAULT@SECLEVEL=)2/\11/' /etc/ssl/openssl.cnf && \
      apt-get install -y autoconf pkg-config \
                                                curl \
                                                libpq-dev \
                                                libzip-dev \
                                                nano \
                                                unzip \
                                                zip 

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*
# Install the PHP pgsql
RUN docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql && docker-php-ext-install pdo pdo_pgsql
#composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
COPY --from=composer /usr/bin/composer /usr/local/bin/composer
RUN composer install 
EXPOSE 80