ARG PHP_VERSION
ARG WORDPRESS_VERSION
FROM wordpress:$WORDPRESS_VERSION-php$PHP_VERSION-apache

WORKDIR /usr/local

# Install Composer
RUN curl --silent --show-error https://getcomposer.org/installer | php
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
