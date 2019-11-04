FROM php:rc-zts-alpine

WORKDIR /app

RUN wget -O phpunit https://phar.phpunit.de/phpunit-8.phar
RUN chmod +x phpunit
RUN mv phpunit /bin

COPY . .