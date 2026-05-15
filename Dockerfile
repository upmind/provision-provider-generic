ARG PHP_VERSION=8.1
FROM php:${PHP_VERSION}-cli

RUN apt-get update \
	&& apt-get install -y --no-install-recommends git unzip libzip-dev \
	&& docker-php-ext-install zip \
	&& rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /workspace

CMD ["bash", "-lc", "while true; do sleep 1000; done"]
