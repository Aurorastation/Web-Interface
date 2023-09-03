FROM webdevops/php-nginx:8.0-alpine

# Install Laravel framework system requirements (https://laravel.com/docs/8.x/deployment#optimizing-configuration-loading)
RUN apk add oniguruma-dev postgresql-dev libxml2-dev
RUN docker-php-ext-install \
    bcmath \
    ctype \
    fileinfo \
    mbstring \
    pdo_mysql \
    tokenizer \
    xml

# Increase nginx client max_body_size and re-cache config on startup
RUN echo "client_max_body_size 100M;" > /opt/docker/etc/nginx/conf.d/11-client-body-size.conf
RUN echo "cd /app && php artisan config:cache" > /opt/docker/provision/entrypoint.d/99-config-cache.sh && chmod a+x /opt/docker/provision/entrypoint.d/99-config-cache.sh

# RUN docker-php-ext-install json

# Copy Composer binary from the Composer official Docker image
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

ENV WEB_DOCUMENT_ROOT /app/public
ENV APP_ENV production
WORKDIR /app
COPY --chown=application:application . .

USER application

RUN composer install --no-interaction --optimize-autoloader --no-dev
# Optimizing Configuration loading
RUN php artisan config:cache
# Optimizing Route loading
RUN php artisan route:cache
# Optimizing View loading
RUN php artisan view:cache

#RUN chown -R application:application .