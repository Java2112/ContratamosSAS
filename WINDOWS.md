# Despliegue en Windows 11 — ContratamosSAS

El proyecto está dockerizado, así que en Windows 11 todo se reduce a instalar
**Docker Desktop** y levantar el stack. Los comandos son para **PowerShell**.

## 1. Requisitos previos (una sola vez)

**a) Activar virtualización**
- Reinicia y entra a la BIOS/UEFI → activa *Virtualization* (Intel VT-x / AMD-V).
- En Windows: *Activar o desactivar características de Windows* → marca
  **Plataforma de máquina virtual** y **Subsistema de Windows para Linux**.

**b) Instalar WSL2** (motor que usa Docker). En PowerShell **como administrador**:
```powershell
wsl --install
wsl --set-default-version 2
```
Reinicia el equipo.

**c) Instalar Docker Desktop**
- Descarga: https://www.docker.com/products/docker-desktop/
- Instálalo dejando marcado *"Use WSL 2 instead of Hyper-V"*.
- Ábrelo y espera a que diga **"Engine running"** (abajo a la izquierda en verde).

**d) Instalar Git**
```powershell
winget install --id Git.Git -e
```

## 2. Clonar el proyecto (rama `produccion`)

```powershell
cd C:\
mkdir proyectos; cd proyectos
git clone -b produccion https://github.com/Java2112/ContratamosSAS.git
cd ContratamosSAS
```

## 3. Crear el archivo de entorno

```powershell
Copy-Item .env.docker.example .env.docker
notepad .env.docker
```
Cambia al menos estas líneas (no dejes las contraseñas de ejemplo):
```
DB_PASSWORD=una_clave_segura
DB_ROOT_PASSWORD=otra_clave_segura
APP_URL=http://localhost:8080
```

## 4. Generar la APP_KEY

Laravel necesita una clave de cifrado. Genérala con Docker (sin instalar PHP):
```powershell
docker run --rm php:8.2-cli php -r "echo 'base64:'.base64_encode(random_bytes(32));"
```
Copia el valor completo (incluido `base64:`) y pégalo en `.env.docker`:
```
APP_KEY=base64:loQueTeDevolvioElComando=
```
> ⚠️ Debe quedar fija aquí para que los contenedores `app` y `queue` compartan la misma clave.

## 5. Construir y levantar

```powershell
docker compose up --build -d
```
La primera vez tarda varios minutos (descarga imágenes, compila assets de Vue/Vite
e instala dependencias). Se levantan 4 contenedores: `web`, `app`, `queue`, `db`.

Verifica el estado:
```powershell
docker compose ps
```

## 6. Cargar datos iniciales (usuario admin)

Las migraciones corren solas al arrancar. Para crear los roles y el usuario admin:
```powershell
docker compose exec app php artisan db:seed --force
```
Esto crea el usuario:
- **Usuario:** `admin@contratamos.com`
- **Contraseña:** `Admin123*`

## 7. Entrar a la aplicación

Abre el navegador en:
```
http://localhost:8080
```
Inicia sesión con las credenciales de arriba y **cambia la contraseña del admin**
tras el primer acceso.

## Comandos útiles

```powershell
docker compose ps              # estado de los contenedores
docker compose logs -f app     # ver logs de la app en vivo
docker compose down            # detener (conserva la base de datos)
docker compose up -d           # volver a arrancar
docker compose exec app php artisan tinker   # consola de Laravel
```

**Actualizar tras cambios en el repo:**
```powershell
git pull
docker compose up --build -d
```

**Borrar TODO incluida la base de datos (¡cuidado!):**
```powershell
docker compose down -v
```

## Notas

- **Docker Desktop debe estar abierto** para que los contenedores funcionen.
  En Ajustes → *Start Docker Desktop when you log in* para que arranque solo.
- **Cambiar el puerto:** si el 8080 está ocupado, edita `docker-compose.yml`
  (`"8080:80"` → `"9090:80"`) y reinicia.
- **Acceso desde otros equipos de la red:** abre el puerto 8080 en el Firewall de
  Windows y entra con `http://IP-del-PC:8080`.
- **Producción real (en internet):** pon HTTPS (proxy tipo Traefik/Nginx con
  certificado) delante del servicio `web`, y deja `APP_ENV=production` y
  `APP_DEBUG=false` en `.env.docker` (ya viene así por defecto).

Ver también [DOCKER.md](DOCKER.md) para los detalles de la arquitectura de contenedores.
