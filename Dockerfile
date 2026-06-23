# syntax=docker/dockerfile:1

############################
# Stage 1: dependencias PHP (Composer)
############################
FROM composer:2 AS vendor

WORKDIR /app

COPY composer.json composer.lock ./

# Instala dependencias de producción sin ejecutar scripts (no hay app aún)
RUN composer install \
    --no-dev \
    --no-scripts \
    --no-interaction \
    --prefer-dist \
    --optimize-autoloader


############################
# Stage 2: build de assets (Vite + Vue + SSR)
############################
FROM node:22-alpine AS assets

WORKDIR /app

COPY package.json package-lock.json ./
# El repo declara Vite 7 con @vitejs/plugin-vue 5 (peer dep ^5||^6),
# por lo que la instalación requiere --legacy-peer-deps.
RUN npm ci --legacy-peer-deps

# Necesitamos el código fuente y la config de Vite/Tailwind para compilar
COPY vite.config.js postcss.config.js tailwind.config.js jsconfig.json ./
COPY resources ./resources

# app.js importa Ziggy desde vendor/ (paquete Composer), así que necesitamos
# las dependencias PHP disponibles durante el build de Vite.
COPY --from=vendor /app/vendor ./vendor

# Genera public/build (cliente) y bootstrap/ssr (SSR)
RUN npm run build


############################
# Stage 3: imagen final (PHP-FPM)
############################
FROM php:8.2-fpm-alpine AS app

# Dependencias de sistema y extensiones PHP requeridas por Laravel + paquetes
RUN apk add --no-cache \
        bash \
        git \
        icu-dev \
        libzip-dev \
        zip \
        unzip \
        libpng-dev \
        libjpeg-turbo-dev \
        freetype-dev \
        oniguruma-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j"$(nproc)" \
        pdo_mysql \
        bcmath \
        intl \
        zip \
        gd \
        mbstring \
        exif \
        pcntl

WORKDIR /var/www/html

# Composer binario (útil para mantenimiento dentro del contenedor)
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Código de la aplicación
COPY . .

# Dependencias y assets ya compilados desde los stages previos
COPY --from=vendor /app/vendor ./vendor
COPY --from=assets /app/public/build ./public/build
COPY --from=assets /app/bootstrap/ssr ./bootstrap/ssr

# Regenera el autoloader optimizado y descubre paquetes (ya con el código completo)
RUN composer dump-autoload --no-dev --optimize \
    && php artisan package:discover --ansi || true

# Config de PHP y entrypoint
COPY docker/php/php.ini /usr/local/etc/php/conf.d/zz-app.ini
COPY docker/php/entrypoint.sh /usr/local/bin/entrypoint
RUN chmod +x /usr/local/bin/entrypoint

# Permisos para storage y cache
RUN chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

EXPOSE 9000

ENTRYPOINT ["entrypoint"]
CMD ["php-fpm"]


############################
# Stage 4: imagen Nginx (sirve los estáticos de public/)
############################
FROM nginx:1.27-alpine AS web

COPY docker/nginx/default.conf /etc/nginx/conf.d/default.conf

# public/ del repo + assets compilados, en la misma ruta que ve PHP-FPM
COPY public /var/www/html/public
COPY --from=assets /app/public/build /var/www/html/public/build

EXPOSE 80
