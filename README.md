# UsTwo

UsTwo es una aplicación web privada para Ángel y Alejandra. Su objetivo es
centralizar listas, experiencias, recuerdos e información compartida mediante
un dashboard desde el que cada lista abre su propia página.

## Estado del proyecto

El proyecto está en fase de definición de producto. La base tecnológica aprobada
es Laravel 13 + Inertia 3 + React 19 con JavaScript y JSX, CSS puro y CSS Modules,
con PHP 8.5 separado de XAMPP, Vite 8, Composer y npm con configuración mínima.
UsTwo ofrece un cuaderno privado de películas por espacio compartido. En
local, SQLite guarda las cuentas, espacios, invitaciones, películas y valoraciones. Las sesiones y la caché locales usan
archivos y las colas son síncronas. La verificación con PostgreSQL y el
despliegue en Render quedan para la fase posterior.

## Decisiones de producto confirmadas

- La aplicación es privada y está pensada inicialmente para dos usuarios.
- El dashboard es el punto central de navegación.
- Cada lista se abre en una página independiente, no en una ventana emergente.
- La presentación puede adaptarse al contenido de cada lista.
- Películas y videojuegos tendrán necesidades funcionales similares.
- Los lugares se visualizarán preferentemente en un mapa interactivo.
- La sincronización con listas personales de Google Maps no es obligatoria.
- El foco principal es registrar experiencias pasadas, salvo deseos y regalos.

Las listas iniciales, la privacidad por contenido, los campos, las
integraciones externas, el diseño visual y el resto de la arquitectura siguen
pendientes de decisión.

## Entorno PHP local en Windows

PHP 8.5.12 (distribución 8.5.12-1, x64 NTS) está instalado de forma independiente
en `%LOCALAPPDATA%\UsTwo\php\8.5`. Para usarlo desde PowerShell:

```powershell
$php85Dir = Join-Path $env:LOCALAPPDATA 'UsTwo/php/8.5'
$php85Exe = Join-Path $php85Dir 'php.exe'
$composerLocal = Join-Path $env:LOCALAPPDATA 'UsTwo/composer/2.11.1/composer.phar'
& $php85Exe -c "$php85Dir/php.ini" -v
& $php85Exe -c "$php85Dir/php.ini" $composerLocal --no-plugins --no-scripts --no-interaction -vvv --version
```

El `php.ini` de esta instalación habilita cURL, Fileinfo, Mbstring, OpenSSL y Zip.
Se verificaron las 14 extensiones previstas, cuatro funciones y `ZipArchive`.
Composer 2.11.1 está instalado en `%LOCALAPPDATA%\UsTwo\composer\2.11.1`.
La invocación anterior se verificó con PHP 8.5.12, código 0 y sin avisos en
ninguna de sus dos salidas. PHP conserva `error_reporting = E_ALL` y la
visualización de avisos activada; no se ocultó el aviso para obtener el resultado.
El Composer global 2.8.9 se conserva y puede seguir mostrando la deprecación
de `react/promise` al ejecutarlo con PHP 8.5; para UsTwo se usa el PHAR local.

XAMPP conserva PHP 8.2.12. `php` y `composer` sin las rutas anteriores siguen
resolviendo al entorno previo. En la instalación de Composer se compararon los
112 archivos protegidos de Composer global, PHP local y XAMPP/Apache, sin
cambios. El PATH persistente de usuario y sistema tampoco cambió.
Estas comprobaciones no prueban todas las aplicaciones de XAMPP. La instalación
del stack comprobó después las descargas de dependencias y el intérprete usado
por los subprocesos `@php`, conservando el entorno anterior.

## Instalación de la base local

Desde la raíz, con PHP 8.5, Composer 2.11.1 y Node compatible con
`^20.19.0 || >=22.12.0` (comprobado con Node 22.16.0 y npm 10.9.2):

```powershell
$php85Dir = Join-Path $env:LOCALAPPDATA 'UsTwo/php/8.5'
$php85Exe = Join-Path $php85Dir 'php.exe'
$composerLocal = Join-Path $env:LOCALAPPDATA 'UsTwo/composer/2.11.1/composer.phar'

& $php85Exe -c "$php85Dir/php.ini" $composerLocal install --no-plugins --no-scripts --no-interaction
npm.cmd ci --ignore-scripts

if (-not (Test-Path -LiteralPath '.env')) {
    Copy-Item -LiteralPath '.env.example' -Destination '.env'
    & $php85Exe -c "$php85Dir/php.ini" artisan key:generate --no-interaction
}
& $php85Exe -c "$php85Dir/php.ini" artisan package:discover --no-interaction
```

Usar los archivos de bloqueo para reproducir la instalación. No regenerar la
clave de un entorno existente ni ejecutar `composer update` o migraciones como
parte de la instalación habitual. El script de autoload solo limpia cachés de
autoload y descubre paquetes; el proyecto no tiene un script `setup` que cree BD.

`composer.json` permite versiones compatibles de Laravel e Inertia, mientras
`composer.lock` conserva las versiones exactas probadas. Las actualizaciones
requieren una tarea explícita y sus comprobaciones. Las demás dependencias
directas permanecen fijadas al parche indicado en sus manifiestos.

### Preparar y arrancar el MVP local

Después de copiar `.env.example`, completa estos valores en `.env` y conserva
el `APP_KEY` generado en el paso anterior:

```dotenv
APP_NAME=UsTwo
APP_ENV=local
APP_DEBUG=true
APP_URL=http://127.0.0.1:8000
APP_LOCALE=es
APP_FALLBACK_LOCALE=es
APP_MAINTENANCE_DRIVER=file
LOG_CHANNEL=stack
LOG_LEVEL=debug
DB_CONNECTION=sqlite
DB_URL=
SESSION_DRIVER=file
CACHE_STORE=file
QUEUE_CONNECTION=sync
FILESYSTEM_DISK=local
BROADCAST_CONNECTION=log
MAIL_MAILER=log
```

Deja `DB_URL` vacía para que Laravel use `database/database.sqlite`. Compila
los recursos con `npm.cmd run build`. El controlador SQLite se habilita en
los siguientes comandos solo para el proceso de UsTwo.

```powershell
if (-not (Test-Path -LiteralPath 'database/database.sqlite')) {
    New-Item -ItemType File -Path 'database/database.sqlite' | Out-Null
}
& $php85Exe -c "$php85Dir/php.ini" -d extension=pdo_sqlite artisan migrate
Push-Location public
try {
    & $php85Exe -c "$php85Dir/php.ini" -d extension=pdo_sqlite -S 127.0.0.1:8000 ../vendor/laravel/framework/src/Illuminate/Foundation/resources/server.php
} finally {
    Pop-Location
}
```

En El recibidor puedes iniciar sesión o crear una cuenta con un nombre de
usuario único. **Tus estanterías** muestra tus estanterías y permite crear
otra al final de la lista. Dentro de cada una, el propietario puede
renombrarla, invitar por nombre de usuario, retirar accesos o borrarla.
Los invitados pueden salir de ella. La persona invitada responde desde el
botón circular de la esquina inferior izquierda. El menú de tu `@usuario`
abre **Mi cuenta**, donde puedes cambiar tus credenciales y consultar tu rol
en cada estantería. El botón `+` permite colocar o mover Películas y
Series en huecos libres. Mover un cajón conserva su colección.

En una base local ya usada, copia `database/database.sqlite` antes de migrar.
La migración asigna las películas y valoraciones de las dos cuentas
originales a Nuestra estantería, propiedad de Ángel. Si detecta cuentas
heredadas diferentes, se detiene para que se compruebe la identidad de las
cuentas. La base SQLite se ignora en Git.

Para restablecer manualmente la contraseña de una cuenta, usa
`artisan ustwo:account <correo> <nombre>`. Si creas una cuenta con ese
comando, añade `--username=<nombre_de_usuario>`.

Para crear las dos cuentas de prueba con los correos de ejemplo, tras migrar
SQLite y antes de arrancar el servidor, ejecuta el seeder local. Solicita
las contraseñas, conserva las cuentas de ejemplo que ya existan y, si la
base está vacía, prepara el espacio inicial de Ángel y Alejandra.

```powershell
& $php85Exe -c "$php85Dir/php.ini" -d extension=pdo_sqlite artisan db:seed '--class=App\Database\Seeders\LocalUsersSeeder'
```

Para activar el catálogo, configura `TMDB_TOKEN` en `.env` con el token de
lectura de TMDB. Si falta el token o falla el catálogo, la pantalla ofrece
el alta manual.

La colección muestra portadas y sinopsis de TMDB cuando están disponibles.
Series usa el mismo catálogo y guarda una ficha por serie completa.
En ambos cajones se puede añadir una ficha a mano si falla la búsqueda.
El comando `php artisan ustwo:movie-metadata` completa estos datos en películas
ya vinculadas sin cambiar sus títulos, fechas ni valoraciones.

Si PHP en Windows devuelve el error cURL 60, configura `curl.cainfo` en
el `php.ini` independiente de UsTwo con un archivo PEM de certificados raíz
de confianza. Mantén habilitada la verificación TLS.

En otra terminal, desde la misma raíz:

```powershell
npm.cmd run dev
```

Abrir `http://127.0.0.1:8000` para iniciar sesión. Vite escucha en
`127.0.0.1:5173` y actualiza JSX/CSS durante el desarrollo.
Detener ambos comandos con `Ctrl+C` al terminar; no exponerlos a la red.

### Build y pruebas

Detener Vite antes de comprobar los assets de producción:

```powershell
npm.cmd run build
& $php85Exe -c "$php85Dir/php.ini" -d extension=pdo_sqlite vendor/bin/phpunit --testdox
& $php85Exe -c "$php85Dir/php.ini" $composerLocal validate --strict
& $php85Exe -c "$php85Dir/php.ini" $composerLocal check-platform-reqs
& $php85Exe -c "$php85Dir/php.ini" $composerLocal audit
npm.cmd ls --depth=0
npm.cmd audit
```

PHPUnit usa SQLite en memoria para verificar el MVP sin tocar el fichero de
datos local. Las pruebas de catálogo simulan las respuestas de TMDB. El build
genera los assets locales de producción.

Las carpetas de agrupación contienen solo subcarpetas. Cada carpeta final
contiene `Nombre.jsx` y, si la vista tiene estilos propios, `Nombre.module.css`. Por ejemplo, `Login`
agrupa `LoginAccessForm` y `AccessForm`. Cuando varios consumidores usan un
componente, este pasa al ámbito común más cercano. El punto de entrada
relaciona los nombres de página de Inertia con sus archivos JSX.
Declara las vistas con `export default function Nombre`,
desestructura los props en la firma y usa un `return` explícito.

Los módulos contienen CSS estándar y consumen los colores, tipografías y
demás valores compartidos con `var(...)`. Las variables, estilos base y temas
compartidos se definen en `resources/css/global.css`, importado solo por las
páginas principales. SSR está
desactivado en Laravel y Vite. `.env`, dependencias, sesiones, cachés, logs y
assets compilados quedan fuera de Git; los dos archivos de bloqueo se conservan.

## Despliegue en Render Free

El flujo de GitHub Actions verifica cada push a `dev` y `main`. Compila los
recursos, ejecuta PHPUnit con SQLite y con PostgreSQL temporal, y construye
la imagen de Render. Solo CI correcto en `main` habilita la publicación.
La configuración de PostgreSQL para pruebas se usa exclusivamente con una
base desechable `ustwo_ci` en el puerto local 55432.

### Configuración inicial

1. Crea un proyecto Supabase Free vacío en Europa si está disponible.
   Desactiva su Data API y exige SSL. Usa la URL del pooler de sesión con
   `sslmode=require`. Este modo exige cifrado TLS sin comprobar el nombre
   del servidor. Deja las URL de conexión sin parámetros de consulta para
   que el modo TLS lo determine la configuración de Laravel.
2. Separa las credenciales. El rol de migración posee el esquema de UsTwo
   y puede crear y modificar sus tablas. El rol de aplicación puede
   conectar y usar el esquema, leer y modificar los datos necesarios y
   usar las secuencias, sin permisos DDL. Configura esos permisos también
   para tablas y secuencias que cree el rol de migración. Comprueba que
   el rol de aplicación no puede crear tablas.
3. Configura el entorno `production` de GitHub Actions restringido a
   `main` si el plan del repositorio lo permite. Guarda
   `SUPABASE_MIGRATION_URL` y, cuando exista el servicio,
   `RENDER_API_KEY` como secretos. Si el plan gratuito del repositorio
   carece de entornos, usa secretos del repositorio y protege `main`
   frente a cambios no autorizados. Comprueba las cuotas y evita habilitar
   consumo de pago.
4. Para el primer arranque, activa la variable `RENDER_BOOTSTRAP=true`
   y mantén estable `main`. Promueve el commit aprobado. Espera a que CI
   pase y el trabajo de publicación confirme las migraciones de ese SHA.
   Hasta el alta del servicio, el trabajo indica «publicación pendiente».
5. Con el mismo SHA en `main`, crea un **Web Service Free** de Render
   enlazado al repositorio con Docker y **Auto-Deploy Off**. Usa `/up`
   para el health check. Configura antes del primer arranque
   `APP_ENV=production`, `APP_DEBUG=false`, `APP_URL` con la URL HTTPS,
   `APP_KEY` estable, `DB_CONNECTION=pgsql`, `DB_URL` del rol de
   aplicación, `DB_SSLMODE=require`, `SESSION_DRIVER=database`,
   `SESSION_SECURE_COOKIE=true`, `LOG_CHANNEL=stderr` y `TMDB_TOKEN`.
   Comprueba en Render que el primer despliegue corresponde al SHA de CI.
6. Registra `RENDER_SERVICE_ID` y `RENDER_PUBLIC_URL` como variables,
   guarda el secreto de la API de Render y desactiva `RENDER_BOOTSTRAP`.
   Reejecuta la publicación del mismo SHA. GitHub Actions aplica las
   migraciones pendientes y espera a que Render confirme el SHA y HTTPS.

Mantén las URL con contraseña y tokens fuera de Git y de los comandos
copiados en registros. En un equipo autorizado, configura temporalmente
`APP_ENV=production`, `DB_CONNECTION=pgsql`, `DB_URL` con el rol de
aplicación y `DB_SSLMODE=require`. Ejecuta
interactivamente este comando con PHP 8.5 y PDO PostgreSQL:

```text
php artisan db:seed --class="App\Database\Seeders\ProductionUsersSeeder" --force
```

Introduce los correos cuando se soliciten. Si defines `PASSWORD_ACCOUNT`
en el `.env` local, el seeder usa esa contraseña para ambas cuentas nuevas.
Si falta o está vacía, solicita cada contraseña de forma oculta.
Retira `PASSWORD_ACCOUNT` del `.env` después del alta.

Verifica las dos cuentas con los mismos correos al repetir el seeder.
Con la base vacía, el seeder también crea su espacio compartido inicial. Para
recuperar una contraseña, usa `ustwo:account` con el correo existente
desde el equipo autorizado. Retira la configuración temporal al terminar.

### Primera publicación de El recibidor

1. Prepara una copia cifrada y comprueba su restauración en PostgreSQL aislado.
   Registra los permisos actuales de `ustwo_app` sobre las dos tablas.
2. Durante la ventana de mantenimiento, retira esos permisos. Desde una
   conexión autorizada con capacidad para administrar el esquema, ejecuta:

   ```sql
   REVOKE SELECT, INSERT, UPDATE, DELETE, TRUNCATE
   ON TABLE public.movies, public.movie_ratings FROM ustwo_app;
   ```

   Comprueba con una conexión existente y otra nueva del rol de aplicación
   que ya no pueden leer ni escribir en esas tablas. Revisa también posibles
   permisos heredados. La migración espera a que terminen las operaciones en
   curso y falla si el rol de aplicación conserva acceso.
3. Promueve a `main` con el bloqueo vigente y espera al flujo de GitHub
   Actions. Antes de reponer los permisos originales, confirma en Render que
   el commit nuevo sirve tráfico y que la revisión anterior ya no lo recibe.
4. Comprueba el acceso de las dos cuentas y la conservación de sus datos.
   Si falla la publicación, conserva el bloqueo hasta validar una revisión
   que aplique el aislamiento. Para recuperar el esquema, restaura la copia
   previa en un entorno aislado y valida los datos antes de reabrir el servicio.
   Una vez creados varios espacios, la versión de recuperación también debe
   aislar sus colecciones.

### Comprobación y recuperación

- Comprueba en GitHub Actions y Render el estado del despliegue y su SHA.
  Prueba `/up`, el acceso de ambas cuentas, el rechazo anónimo, TMDB y un
  registro compartido que siga disponible tras redesplegar.
- Si falla una migración, revisa sus resultados desde una conexión
  autorizada antes de reintentar. Si falla el despliegue, revisa el estado
  de la revisión anterior. Un rollback de aplicación exige que esa
  revisión siga siendo compatible con el esquema actual.
- Render Free puede tardar en responder tras reposo. Si Supabase está
  pausado, restáuralo desde su panel y vuelve a comprobar `/up`.
- Antes de promover cambios de esquema sobre datos existentes, exporta
  el esquema y los datos de UsTwo con `pg_dump` usando TLS desde un
  equipo autorizado. Cifra la copia con una herramienta local y guárdala
  fuera del repositorio. Comprueba una restauración con `pg_restore`
  en PostgreSQL aislado antes de usar una copia para recuperar producción.
- Revisa periódicamente los planes y cuotas de Render, Supabase y GitHub
  Actions. Detén el proceso si continuar requiere recursos de pago.

## Proceso de trabajo

Los cambios relevantes siguen el proceso SDD descrito en
[`docs/proceso-sdd.md`](docs/proceso-sdd.md). Las decisiones técnicas aprobadas
se conservan en [`docs/decisiones-tecnicas.md`](docs/decisiones-tecnicas.md) y
los artefactos de cada cambio en `docs/specs/`.

Las skills locales `ui-ux-pro-max`, `frontend-design` e `impeccable` apoyan la
definición visual, la construcción de interfaces y su revisión dentro de ese
proceso. Su uso por fase y procedencia se describen en
[`docs/proceso-sdd.md`](docs/proceso-sdd.md#diseño-de-interfaces-dentro-del-flujo).
