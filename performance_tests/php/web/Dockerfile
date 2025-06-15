FROM php:8.3-apache-bookworm

LABEL maintainer="Wolfgang Gassler <wolfgang@gassler.org>"

RUN apt-get update \
        && apt-get install -y default-mysql-client \
        && docker-php-ext-install mysqli pdo_mysql  \
        && rm -rf /var/lib/apt/lists/*

EXPOSE 80

RUN rm -fR /var/www/html/*

COPY init.sh /
RUN chmod u+x /init.sh
COPY src /var/www/html

CMD ["/init.sh"]
