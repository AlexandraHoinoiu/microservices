FROM ubuntu:latest

# Install apache, PHP, and supplimentary programs
RUN apt-get update && apt-get -y upgrade && DEBIAN_FRONTEND=noninteractive apt-get -y install \
    apache2 php7.4 libapache2-mod-php7.4 curl

# Enable apache mods.
RUN a2enmod php7.4
RUN a2enmod rewrite

RUN apt-get install -y vim

COPY ./docker/learner/service-vhost.conf /etc/apache2/sites-available/000-default.conf
COPY ./docker/learner/entrypoint.sh /usr/sbin/entrypoint.sh
RUN chmod +x /usr/sbin/entrypoint.sh

# Update the PHP.ini file, enable <? ?> tags and quieten logging.
RUN sed -i "s/short_open_tag = Off/short_open_tag = On/" /etc/php/7.4/apache2/php.ini
RUN sed -i "s/error_reporting = .*$/error_reporting = E_ERROR | E_WARNING | E_PARSE/" /etc/php/7.4/apache2/php.ini

# Manually set up the apache environment variables
ENV APACHE_RUN_USER www-data
ENV APACHE_RUN_GROUP www-data
ENV APACHE_LOG_DIR /var/log/apache2
ENV APACHE_LOCK_DIR /var/lock/apache2
ENV APACHE_PID_FILE /var/run/apache2.pid

# Expose apache.
EXPOSE 80
ENTRYPOINT ["/bin/bash", "/usr/sbin/entrypoint.sh"]


COPY ./learner/vendor /var/www/vendor
COPY ./learner/public /var/www/public
COPY ./learner/app /var/www/app
COPY ./learner/bootstrap /var/www/bootstrap
COPY ./learner/config /var/www/config
COPY ./learner/routes /var/www/routes

CMD /usr/sbin/apache2ctl -D FOREGROUND