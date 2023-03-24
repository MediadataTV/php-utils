FROM php:7.4.33-cli

RUN apt-get update && apt-get install -y \
		libzip-dev \
        zip \
        libicu-dev \
    && docker-php-ext-configure intl \
    && docker-php-ext-install -j$(nproc) zip intl

COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

WORKDIR /opt/app

COPY . /opt/app

CMD [ "./run-test" ]
