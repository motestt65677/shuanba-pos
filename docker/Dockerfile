FROM ubuntu:16.04

# ENV VAR
ENV HOME=/root WORKDIR=/var/www/html/PHP
ENV TERM xterm

# Switch to /root
WORKDIR $HOME

# Setting time zone
ADD ./docker/Timezone_Taipei /etc/localtime

# Update and install package
RUN apt-get update -y\
    && apt-get install -y software-properties-common \
    && LC_ALL=C.UTF-8 add-apt-repository ppa:ondrej/php\
    && apt update -y \
    && apt install -y apache2 git curl nano \
        php7.4 libapache2-mod-php7.4 php7.4-mbstring \
        php7.4-xmlrpc php7.4-soap php7.4-gd php7.4-xml \
        php7.4-cli php7.4-zip php7.4-bcmath php7.4-json php7.4-mysql
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"\
    && php -r "if (hash_file('sha384', 'composer-setup.php') === '756890a4488ce9024fc62c56153228907f1545c228516cbf63f885e036d37e9a59d27d63f46af1d4d07ee0f76181c7d3') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"\
    && php composer-setup.php --install-dir=/usr/local/bin --filename=composer\
    && php -r "unlink('composer-setup.php');"

RUN curl -sL https://deb.nodesource.com/setup_14.x -o nodesource_setup.sh \
    && bash nodesource_setup.sh \
    && apt-get install -y nodejs build-essential libssl-dev

# Setup temphawk web service
COPY . $WORKDIR
WORKDIR $WORKDIR

RUN composer install

# Setup apache rewrite mod
RUN cp $WORKDIR/docker/PHP.conf /etc/apache2/sites-available/PHP.conf \
    && a2dissite 000-default.conf \
    && a2ensite PHP.conf \
    && a2enmod rewrite \
    && service apache2 restart
RUN chown -R www-data:www-data /var/www/html/PHP/ \
    && chmod -R 755 /var/www/html/PHP/

RUN cp $WORKDIR/docker/docker-entrypoint.sh /entrypoint.sh
ENTRYPOINT ["/entrypoint.sh"]

# Export port
EXPOSE 80 443


