# UsTwo

UsTwo es una aplicación web privada para Ángel y Alejandra. Su objetivo es
centralizar listas, experiencias, recuerdos e información compartida mediante
un dashboard desde el que cada lista abre su propia página.

## Estado del proyecto

El proyecto está en fase de definición de producto. La base tecnológica aprobada
es Laravel 13 + Inertia 3 + React 19 con JavaScript y JSX, CSS puro y CSS Modules,
con PHP 8.5 separado de XAMPP, Vite 8, Composer y npm con configuración mínima.
La base local está instalada en la raíz, con una página técnica de comprobación.
No usa base de datos: sesiones y caché locales en archivos y colas síncronas.
El producto, su diseño, los datos, el acceso y la infraestructura de producción
siguen pendientes de definición. Esta base no está preparada para uso público.

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

### Desarrollo

Con las variables PHP anteriores preparadas en la terminal:

```powershell
& $php85Exe -c "$php85Dir/php.ini" artisan serve --host=127.0.0.1 --port=8000 --no-reload
```

En otra terminal, desde la misma raíz:

```powershell
npm.cmd run dev
```

Abrir `http://127.0.0.1:8000`. Vite escucha en `127.0.0.1:5173` y actualiza
JSX/CSS durante el desarrollo. La página muestra una prop de Laravel y un
contador local; es una prueba técnica, no el dashboard de producto.
Detener ambos comandos con `Ctrl+C` al terminar; no exponerlos a la red.

### Build y pruebas

Detener Vite antes de comprobar los assets de producción:

```powershell
npm.cmd run build
& $php85Exe -c "$php85Dir/php.ini" $composerLocal --no-plugins run-script test -- --testdox
& $php85Exe -c "$php85Dir/php.ini" $composerLocal validate --strict
& $php85Exe -c "$php85Dir/php.ini" $composerLocal check-platform-reqs
& $php85Exe -c "$php85Dir/php.ini" $composerLocal audit
npm.cmd ls --depth=0
npm.cmd audit
```

Las tres pruebas PHPUnit comprueban HTML inicial, respuesta Inertia y salud
local sin abrir conexiones a una BD. No se usa `artisan test` ni se han añadido
linters o herramientas de navegador adicionales. El build es una comprobación
local, no un despliegue. Los servidores iniciados para verificar la instalación
se han detenido al terminar las pruebas.

Los componentes propios usan `.jsx` y su `.module.css` correspondiente.
`resources/css/global.css` se importa solo desde páginas principales. SSR está
desactivado en Laravel y Vite. `.env`, dependencias, sesiones, cachés, logs y
assets compilados quedan fuera de Git; los dos archivos de bloqueo se conservan.

## Proceso de trabajo

Los cambios relevantes siguen el proceso SDD descrito en
[`docs/proceso-sdd.md`](docs/proceso-sdd.md). Las decisiones técnicas aprobadas
se conservan en [`docs/decisiones-tecnicas.md`](docs/decisiones-tecnicas.md) y
los artefactos de cada cambio en `docs/specs/`.

Las skills locales `ui-ux-pro-max`, `frontend-design` e `impeccable` apoyan la
definición visual, la construcción de interfaces y su revisión dentro de ese
proceso. Su uso por fase y procedencia se describen en
[`docs/proceso-sdd.md`](docs/proceso-sdd.md#diseño-de-interfaces-dentro-del-flujo).
