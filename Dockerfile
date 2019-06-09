ARG PHP_VERSION
ARG WORDPRESS_VERSION
FROM wordpress:$WORDPRESS_VERSION-php$PHP_VERSION-apache

WORKDIR /usr/local

# Install Composer
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
RUN php -r "if (hash_file('sha384', 'composer-setup.php') === '48e3236262b34d30969dca3c37281b3b4bbe3221bda826ac6a9a62d6444cdb0dcd0615698a5cbe587c3f0fe57a54d8f5') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"
RUN php composer-setup.php
RUN php -r "unlink('composer-setup.php');"
RUN chmod +x composer.phar
RUN mv composer.phar /usr/local/bin/composer
RUN composer --version

# Install WP-CLI
RUN curl -O https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar
RUN chmod +x wp-cli.phar
RUN mv wp-cli.phar /usr/local/bin/wp
RUN wp --info

WORKDIR /var/www/html

COPY languages /var/www/html/wp-content/plugins/wp-gistpen/languages
COPY views /var/www/html/wp-content/plugins/wp-gistpen/views
COPY icon-128x128.png /var/www/html/wp-content/plugins/wp-gistpen/icon-128x128.png
COPY index.php /var/www/html/wp-content/plugins/wp-gistpen/index.php
COPY uninstall.php /var/www/html/wp-content/plugins/wp-gistpen/uninstall.php
COPY wp-gistpen.php /var/www/html/wp-content/plugins/wp-gistpen/wp-gistpen.php
COPY lib /var/www/html/wp-content/plugins/wp-gistpen/lib
COPY config /var/www/html/wp-content/plugins/wp-gistpen/config
COPY app /var/www/html/wp-content/plugins/wp-gistpen/app
COPY assets /var/www/html/wp-content/plugins/wp-gistpen/assets
