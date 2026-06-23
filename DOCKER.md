# Despliegue con Docker — ContratamosSAS

Esta rama (`docker`) añade soporte de contenedores para la aplicación Laravel 12
(Inertia + Vue 3 + Vite + Tailwind) usando **Nginx + PHP-FPM** y **MariaDB**.

## Servicios

| Servicio | Imagen | Rol | Puerto |
|---|---|---|---|
| `web`   | `contratamos-web` (Nginx)        | Sirve `public/` y proxya PHP a `app:9000` | `8080:80` |
| `app`   | `contratamos-app` (PHP 8.2-FPM)  | Ejecuta Laravel (PHP-FPM) | interno `9000` |
| `queue` | `contratamos-app`                | Worker de colas (`queue:work`, driver `database`) | — |
| `db`    | `mariadb:11`                     | Base de datos | interno `3306` |

Volúmenes persistentes: `db-data` (datos MariaDB) y `storage-data` (`storage/` de Laravel).

## Arquitectura de la imagen

`Dockerfile` multi-stage:

1. **assets** (`node:22`) — `npm ci` + `npm run build` → genera `public/build` y `bootstrap/ssr`.
2. **vendor** (`composer:2`) — instala dependencias PHP de producción.
3. **app** (`php:8.2-fpm-alpine`) — código + vendor + assets compilados; extensiones
   `pdo_mysql, bcmath, intl, zip, gd, mbstring, exif, pcntl` + OPcache.
4. **web** (`nginx:1.27-alpine`) — hornea `public/` (misma ruta que ve PHP-FPM).

## Puesta en marcha

```bash
# 1. Copia el ejemplo de entorno y ajústalo
cp .env.docker.example .env.docker

# 2. (Recomendado) genera una APP_KEY y pégala en .env.docker
#    Así app y queue comparten la misma clave (sesiones/cifrado).
docker run --rm php:8.2-cli php -r "echo 'base64:'.base64_encode(random_bytes(32)).PHP_EOL;"
#    -> copia el valor en APP_KEY= dentro de .env.docker

# 3. Construye y levanta
docker compose up --build -d

# 4. La app queda en:
#    http://localhost:8080
```

El `entrypoint` del contenedor `app` se encarga automáticamente de:
- crear `.env` si falta (desde `.env.docker.example`),
- esperar a que MariaDB esté lista,
- `php artisan storage:link`,
- `php artisan migrate --force`,
- cachear config/rutas/vistas (`config:cache`, `route:cache`, `view:cache`).

## Comandos útiles

```bash
# Logs
docker compose logs -f app
docker compose logs -f web

# Consola artisan dentro del contenedor
docker compose exec app php artisan tinker
docker compose exec app php artisan migrate:status

# Seeders (si aplica)
docker compose exec app php artisan db:seed --force

# Reconstruir tras cambios de código/assets
docker compose up --build -d

# Parar (conservando datos)
docker compose down

# Parar y BORRAR datos (¡cuidado!)
docker compose down -v
```

## Notas

- **Base de datos**: por defecto MariaDB (`DB_CONNECTION=mysql`). Credenciales y nombre
  se configuran en `.env.docker`.
- **APP_KEY**: defínela en `.env.docker`. Si se deja vacía, cada contenedor generaría una
  distinta y se romperían sesiones/datos cifrados.
- **SSR de Inertia**: los assets SSR se compilan (`bootstrap/ssr`), pero no se levanta un
  servidor Node de SSR; Inertia funciona con render del lado cliente. Si más adelante se
  requiere SSR real, se puede añadir un servicio Node con `php artisan inertia:start-ssr`.
- **Producción**: ajusta `APP_ENV=production`, `APP_DEBUG=false` y usa un proxy/HTTPS
  (Nginx/Traefik) delante del servicio `web` si se expone a internet.
