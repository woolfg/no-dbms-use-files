FROM php:7.2-apache-stretch

LABEL maintainer="Wolfgang Gassler <wolfgang@gassler.org>"

RUN apt-get update \
        && apt-get install -y mariadb-client \
        && docker-php-ext-install mysqli pdo_mysql  \
        && rm -rf /var/lib/apt/lists/*

EXPOSE 80

RUN rm -fR /var/www/html/*

COPY init.sh /
COPY src /var/www/html

CMD ["/init.sh"]
