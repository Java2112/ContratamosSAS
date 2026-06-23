#!/usr/bin/env bash
set -e

cd /var/www/html

# Asegura que exista el .env
if [ ! -f .env ]; then
    if [ -f .env.docker.example ]; then
        cp .env.docker.example .env
        echo "[entrypoint] .env creado desde .env.docker.example"
    else
        cp .env.example .env
        echo "[entrypoint] .env creado desde .env.example"
    fi
fi

# APP_KEY: lo ideal es definirla en .env.docker (compartida por app y queue).
# Si llega vacía por el entorno, la generamos y EXPORTAMOS para este proceso
# (config:cache y php-fpm la heredan). Nota: en multi-contenedor cada uno
# generaría una distinta, por eso conviene fijarla en .env.docker.
if [ -z "${APP_KEY}" ]; then
    export APP_KEY="$(php artisan key:generate --show)"
    echo "[entrypoint] ATENCIÓN: APP_KEY generada al vuelo. Fíjala en .env.docker"
    echo "[entrypoint] para que 'app' y 'queue' compartan la misma clave."
fi

# Permisos (por si el volumen montado los reinició)
chown -R www-data:www-data storage bootstrap/cache || true
chmod -R 775 storage bootstrap/cache || true

# Espera a la base de datos (driver mysql/mariadb)
if grep -q "^DB_CONNECTION=mysql" .env; then
    echo "[entrypoint] Esperando a la base de datos en ${DB_HOST:-db}:${DB_PORT:-3306}..."
    until php -r "exit(@fsockopen(getenv('DB_HOST') ?: 'db', (int)(getenv('DB_PORT') ?: 3306)) ? 0 : 1);" 2>/dev/null; do
        sleep 2
    done
    echo "[entrypoint] Base de datos disponible."
fi

# Migraciones: solo en el contenedor principal (SKIP_INIT distinto de "true").
# Evita la carrera entre 'app' y 'queue' migrando a la vez.
if [ "${SKIP_INIT}" != "true" ]; then
    php artisan storage:link || true
    php artisan migrate --force
fi

# Cachés de producción (por contenedor; bootstrap/cache no es volumen compartido)
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "[entrypoint] Listo. Arrancando: $*"
exec "$@"
