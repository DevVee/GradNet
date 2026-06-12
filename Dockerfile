# ─── Stage 1: Build frontend assets ──────────────────────────────────────────
FROM node:20-alpine AS assets
WORKDIR /app
COPY package*.json ./
RUN npm ci
COPY . .
RUN npm run build

# ─── Stage 2: PHP production runtime ─────────────────────────────────────────
FROM php:8.2-fpm-alpine

# ── System packages ───────────────────────────────────────────────────────────
RUN apk add --no-cache \
        nginx \
        supervisor \
        libpq-dev \
        libzip-dev \
        libpng-dev \
        libjpeg-turbo-dev \
        freetype-dev \
        libxml2-dev \
        oniguruma-dev \
        icu-dev \
        zip \
        unzip \
        curl \
        gettext \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j"$(nproc)" \
          pdo pdo_pgsql pgsql zip opcache mbstring gd dom xml intl bcmath exif \
    && rm -rf /var/cache/apk/*

# ── OPcache tuning ────────────────────────────────────────────────────────────
RUN { \
        echo 'opcache.enable=1'; \
        echo 'opcache.memory_consumption=128'; \
        echo 'opcache.interned_strings_buffer=8'; \
        echo 'opcache.max_accelerated_files=10000'; \
        echo 'opcache.revalidate_freq=0'; \
        echo 'opcache.validate_timestamps=0'; \
        echo 'opcache.save_comments=1'; \
    } > /usr/local/etc/php/conf.d/opcache.ini

# ── PHP upload limits (profile pics, post media, message attachments) ─────────
RUN { \
        echo 'upload_max_filesize=20M'; \
        echo 'post_max_size=25M'; \
        echo 'memory_limit=256M'; \
        echo 'max_execution_time=60'; \
    } > /usr/local/etc/php/conf.d/uploads.ini

# ── Composer ──────────────────────────────────────────────────────────────────
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# ── PHP dependencies (layer-cached until composer.lock changes) ───────────────
# Retries up to 3 times to handle transient Packagist 504 errors.
COPY composer.json composer.lock ./
RUN for i in 1 2 3; do \
        composer install \
            --no-dev \
            --no-scripts \
            --no-autoloader \
            --no-interaction \
            --no-progress \
        && break; \
        echo "==> composer attempt $i/3 failed, retrying in 15s..." && sleep 15; \
    done

# ── Application source ────────────────────────────────────────────────────────
COPY . .

# ── Copy compiled Vite assets from the node stage ────────────────────────────
COPY --from=assets /app/public/build ./public/build

# ── Finalise autoloader ───────────────────────────────────────────────────────
RUN composer dump-autoload --no-dev --optimize \
    && php artisan package:discover --ansi

# ── Runtime Docker config ─────────────────────────────────────────────────────
# NOTE: Database migrations are NOT run at build time because the database is
# an external Supabase PostgreSQL service — not reachable during docker build.
# Migrations run at container startup in docker/start.sh instead.
COPY docker/nginx.conf.template /etc/nginx/nginx.conf.template
COPY docker/supervisord.conf    /etc/supervisord.conf
COPY docker/start.sh            /start.sh
RUN chmod +x /start.sh

# ── Permissions ───────────────────────────────────────────────────────────────
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 storage bootstrap/cache

EXPOSE 8080

CMD ["/start.sh"]
