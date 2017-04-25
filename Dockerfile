FROM php:7.1-fpm

RUN apt-get update
RUN apt-get install -y wget \
    build-essential \
    libtool \
    autoconf \
    uuid-dev \
    pkg-config \
    git \
    libsodium-dev \
    curl \
    libhiredis-dev \
    apt-transport-https \
    lsb-release \
    ca-certificates \
    zip \
    unzip
RUN wget -O /etc/apt/trusted.gpg.d/php.gpg \
    https://packages.sury.org/php/apt.gpg
RUN echo "deb https://packages.sury.org/php/ $(lsb_release -sc) main" | tee /etc/apt/sources.list.d/php.list
RUN apt update && apt install php7.1-zip -y

RUN curl -sS https://getcomposer.org/installer | php \
    && mv composer.phar /usr/local/bin/composer \
    && chmod a+x /usr/local/bin/composer

#Phpiredis
RUN git clone https://github.com/nrk/phpiredis.git
RUN cd phpiredis && phpize && ./configure --enable-phpiredis && make && make install

RUN docker-php-ext-install pcntl

WORKDIR /var/www/btc_ticker