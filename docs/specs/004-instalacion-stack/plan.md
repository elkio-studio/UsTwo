---
spec: "004"
status: approved
---

# Plan técnico — 004

## Resumen

Incorporar una base Laravel 13 + Inertia 3 + React 19 en la raíz existente,
usando Vite 8, JSX y CSS puro. La comprobación será una sola página técnica,
con un componente interactivo mínimo; no implementa el producto ni su diseño.
Sesiones/caché locales en archivos, colas síncronas y ninguna base de datos.

La especificación está aprobada con sus propuestas. Este plan recibió revisión
`PASS` y aprobación expresa del desarrollador el 2026-11-08. Sus tareas siguen
pendientes hasta la solicitud expresa de implementar. Se mantiene `main` por
autorización del desarrollador hasta completar el stack, sin commits.

## Componentes afectados

- Nuevos archivos de aplicación: `artisan`, `app/`, `bootstrap/`, `config/`,
  `public/`, `resources/`, `routes/`, estructura mínima de `storage/`, `tests/`,
  manifiestos, archivos de bloqueo, `vite.config.js`, `phpunit.xml` y `.env.example`.
- Existentes a adaptar: README, `.gitignore`, MEMORY y decisiones técnicas.
  Conservar AGENTS, `.agents`, `.codex`, `docs` y todo cambio previo ajeno.
- Archivos locales no versionados: `.env`, `vendor/`, `node_modules/`, cachés,
  sesiones, logs, `public/hot`, `public/build/` y evidencias temporales.
- El agente principal es el único escritor. Los revisores no reparan archivos.

## Contratos, datos e interfaces

### Versiones fijadas y procedencia

| Paquete | Versión exacta |
| --- | --- |
| Plantilla `laravel/laravel` | 13.11.0, referencia `06d016a364a37430eec9cbc52209adce3d7667ce` |
| `laravel/framework` | 13.40.0 |
| `inertiajs/inertia-laravel` | 3.7.0 |
| `phpunit/phpunit` | 12.5.43 |
| `mockery/mockery` | 1.6.15 |
| `react`, `react-dom` | 19.3.1 ambos |
| `@inertiajs/react`, `@inertiajs/vite` | 3.11.0 ambos |
| `vite` | 8.3.6 |
| `laravel-vite-plugin` | 3.2.1 |
| `@vitejs/plugin-react` | 6.2.1 |

La plantilla no queda como dependencia de la aplicación. Las versiones instaladas
se fijan a esos parches en los archivos de bloqueo; PHP declarado como `^8.5`.
Por ajuste aprobado expresamente durante la implementación, `composer.json`
usa `^13.40.0` para Laravel y `^3.7.0` para Inertia, sin cambiar las versiones
instaladas. Los demás paquetes directos mantienen restricciones exactas.
Dependencias transitivas resueltas en ambos locks, con referencias e integridad.
No adoptar versiones de desarrollo ni sustituir las fijadas silenciosamente.

El ajuste permite superar la validación estricta de Composer sin ignorar sus
avisos sobre restricciones exactas. `composer install` reproduce el lock;
actualizaciones posteriores requieren una tarea explícita y nueva verificación.

Metadatos consultados el 2026-11-08 en los registros oficiales de
[Packagist](https://packagist.org/) y [npm](https://www.npmjs.com/).
El adaptador Laravel 3 admite framework 13. Los plugins elegidos admiten Vite 8
y Node `^20.19.0 || >=22.12.0`; el Node observado es 22.16.0 con npm 10.9.2.
No se instalan peers opcionales como Echo, tipos React, React Compiler o Fontaine.

### Comprobación aislada de resolución ya realizada

En `%LOCALAPPDATA%\Temp\ustwo-stack-plan-gotypjqx` se prepararon manifiestos
temporales con estos paquetes, fuera del repositorio:

- Composer local con PHP 8.5.12: `update --dry-run --no-install --prefer-dist`
  con `--no-plugins --no-scripts --no-interaction`, código 0. Resolución de 102
  paquetes sin incompatibilidades ni avisos de vulnerabilidades de Composer.
- npm 10.9.2: `install --package-lock-only --ignore-scripts --no-audit --no-fund`
  contra el registro oficial, código 0. Lock temporal de 59 entradas, sin
  conflictos de peers/engines. Solo emitió un aviso informativo de nueva versión
  de npm; no se actualizó la herramienta.
- No se crearon `vendor` ni `node_modules` ni manifiestos de aplicación en el
  repositorio. La prueba no instaló paquetes ni validó todavía su ejecución.
- El único script de instalación identificado en ese lock es el de `fsevents`
  2.3.3, dependencia opcional de macOS, no necesaria en Windows. Se mantendrá
  `--ignore-scripts` en npm para esta instalación de Windows.
- Evidencia temporal: `resolution.json`, salidas de ambos comandos y lock npm.
  La resolución real se repetirá durante la implementación y quedará en los
  locks definitivos; el dry-run no fija las transitivas para siempre.

### Incorporación segura del esqueleto

Descargar el ZIP de la referencia fijada desde
`https://api.github.com/repos/laravel/laravel/zipball/06d016a364a37430eec9cbc52209adce3d7667ce`
a una carpeta temporal nueva, sin clonar ni ejecutar Git sobre el repositorio.
Registrar origen, referencia y SHA-256 obtenido como evidencia de descarga;
Packagist no publica un SHA esperado para este ZIP, por lo que ese hash es un
registro de integridad, no una autenticación independiente del origen HTTPS.
Validar rutas/enlaces del ZIP antes de extraer dentro de la carpeta temporal.

Preparar allí solo los archivos de aplicación necesarios y revisar scripts
antes de copiarlos a la raíz. No usar `create-project` en el repositorio ni
copiar `.git`, `.github`, README, configuración de agentes o Git del esqueleto.
No incorporar el modelo User, sus migraciones, factories/seeders ni la vista
welcome de ejemplo. Conservar estructura estándar vacía donde sea necesaria.
Integrar README y `.gitignore` sobre su contenido existente. Si una ruta de
aplicación ya tiene contenido ajeno, detener la colisión y no sobrescribirlo.

### Configuración local sin base de datos

- `.env.example` y `.env` local: `SESSION_DRIVER=file`, `CACHE_STORE=file`,
  `QUEUE_CONNECTION=sync`. `DB_CONNECTION=null`; en `config/database.php`,
  no conservar SQLite como valor por defecto: usar `env('DB_CONNECTION')`.
  Una conexión no solicitada debe fallar por falta de conexión seleccionada,
  en vez de conectarse a una base implícita.
- Mantener configuraciones convencionales de conexiones sin seleccionar
  ningún motor ni habilitar drivers PDO. No crear archivos SQLite ni ejecutar
  migraciones, seeds o `RefreshDatabase`.
- En PHPUnit, retirar la selección SQLite en memoria del esqueleto. Usar
  `DB_CONNECTION=null`; sesiones/caché pueden ser `array` en pruebas, colas
  `sync`. Ninguna prueba usa base de datos.
- Generar la clave de aplicación solo en `.env` ignorado, sin `--show` y sin
  mostrar ni guardar el secreto en evidencias. No volcar configuración completa.
- Mantener correo/broadcasting locales sin envíos ni servicios externos.

### Laravel, Inertia y React

- `app/Http/Middleware/HandleInertiaRequests.php`: middleware estándar de
  Inertia registrado en el grupo `web` de `bootstrap/app.php`.
- `resources/views/app.blade.php`: raíz HTML con viewport, idioma español,
  directiva React Refresh y entrada Vite `resources/js/app.jsx`, más los
  componentes Blade de head/app de Inertia. No cargar CSS global desde Blade.
- `routes/web.php`: ruta local `/` que devuelve `StackCheck` con una prop
  ficticia `message` que confirme el recorrido del backend al navegador.
  Mantener `/up` del esqueleto como comprobación técnica, no crear endpoints de producto.
- `resources/js/app.jsx`: inicialización de Inertia, resolución de páginas
  `.jsx` en `./pages`, montaje React gestionado por el plugin. Sin global.css.
- `resources/js/pages/StackCheck.jsx` y `StackCheck.module.css`: prop visible,
  importación de `resources/css/global.css` y componente técnico hijo.
- `resources/js/components/RuntimeCheck.jsx` y `RuntimeCheck.module.css`:
  contador local con botón nativo accesible. Una clase local con el mismo
  nombre que la de la página tendrá un estilo distinto para probar aislamiento.
- `global.css`: reglas globales mínimas de documento, fuente de sistema y
  foco visible. No fija identidad visual, descarga fuentes o añade animaciones.
- `vite.config.js`: plugins Laravel, React e Inertia, solo entrada JSX;
  `inertia({ ssr: false })`. En `config/inertia.php`, `ssr.enabled=false`.
  Así se desactiva SSR en cliente de desarrollo y en adaptador Laravel.
  Servidor/HMR en `127.0.0.1`; no activar exposición a la red.
- Sustituir los imports y paquetes Tailwind que trae la plantilla. No añadir
  TypeScript, bibliotecas UI o compiladores opcionales.

## Fases de implementación

1. **Precondiciones y conservación.** Comprobar versiones, TLS de las operaciones
   necesarias, rutas, permisos y estado de Git. Registrar inventarios/hashes
   protegidos y comprobar puertos. Crear evidencias temporales sin secretos.
2. **Base Laravel controlada.** Descargar y preparar el esqueleto como se indica
   arriba. Incorporar archivos y preparar manifiestos/configuración sin BD
   antes de ejecutar scripts. Adaptar `.gitignore` antes de crear `.env`.
3. **Dependencias e intérpretes.** Resolver e instalar con Composer local por
   PHP explícito, `--no-scripts --no-plugins`. Instalar npm con scripts
   desactivados y sin forzar peers. Conservar locks definitivos. Permitir solo
   el autoload/discovery oficial revisado y comandos `@php` que no migren ni
   invoquen Composer global; quitar los scripts `setup`/post-create y otros
   automatismos no aplicables del esqueleto. Generar clave y descubrir paquetes
   con el PHP correcto una vez configurado el entorno.
4. **Integración mínima.** Configurar middleware, Blade, React, Vite, CSS,
   página/componente técnicos y respuestas Inertia. No ampliar producto.
5. **Pruebas y ejecución local.** Crear pruebas PHPUnit relevantes, ejecutar
   controles de manifiestos/plataforma/dependencias y probar en navegador
   loopback tanto desarrollo como producción, incluyendo HMR e interacción.
6. **Documentación y cierre.** Restaurar cualquier marcador de prueba HMR,
   detener servidores propios, comprobar conservación, actualizar README y
   recopilar evidencias. Aplicar `ustwo-verify` y revisión `test_reviewer` de
   solo lectura antes de completar artefactos. Sin commit ni publicación.

Los comandos PHP usan `%LOCALAPPDATA%\UsTwo\php\8.5\php.exe` y su INI;
Composer usa el PHAR `%LOCALAPPDATA%\UsTwo\composer\2.11.1\composer.phar`.
El proceso de instalación puede anteponer PHP 8.5 al PATH del proceso para los
subprocesos, guardándolo/restaurándolo en `finally`; nunca modificar PATH
persistente ni lanzar `composer` global. Verificar con un script temporal de
Composer `@php` que `PHP_BINARY` sea el esperado, y registrar el runtime de
las pruebas. No añadir un lanzador o script permanente solo para esa prueba.

## Estrategia de pruebas y criterios

### Controles reales y derivados de los manifiestos previstos

- Composer local: `validate --strict`, `check-platform-reqs` y `audit` sobre
  la instalación real. Revisar código y salida, no desactivar comprobaciones
  para ocultar fallos o avisos de incompatibilidad.
- npm: `ls --depth=0`, `audit` y `run build`. Revisar vulnerabilidades y
  dependencias inesperadas; no ejecutar `audit fix` automáticamente. Un fallo
  de red del audit se informa como comprobación pendiente, no como resultado limpio.
- `composer.json` tendrá `test` con `@php vendor/bin/phpunit`; ejecutar ese
  script con Composer/PHP locales. No anunciar `artisan test`, lint, Pint,
  ESLint, Vitest o una cobertura que no se ha ejecutado.
- Confirmar que los comandos y locks usan exactamente las dependencias directas
  fijadas. Una incompatibilidad o cambio de parche vuelve a revisión, no se fuerza.
- Ejecutar `git diff --check` y comprobar `.gitignore` con rutas reales, sin
  mostrar `.env` o claves; asegurar que ambos locks no están ignorados.

### PHPUnit y ausencia de conexiones

Adaptar `tests/TestCase.php` y crear `tests/Feature/StackCheckTest.php`.
Cubrir HTML inicial, componente/props de Inertia y petición con `X-Inertia`.
Comprobar cabeceras, estado y contenido esperado; un bypass de Vite en una
prueba HTTP no sustituye las pruebas reales de assets en navegador.
Comprobar que `database.default` no selecciona un motor y que el gestor
mantiene `getConnections()` vacío tras las peticiones. No llamar a `connection`
para comprobarlo. Detectar si se creó cualquier archivo SQLite; no usar
traits de migraciones ni consultas de prueba. Confirmar configuración `file`
de sesiones/caché en desarrollo y `sync` para colas, sin worker o conexión.

### Navegador, desarrollo y producción

Usar las herramientas de navegador ya disponibles, sin instalar automatización
adicional. Registrar URL, tamaños, captura y resultados de consola/red.

1. Iniciar Laravel en `127.0.0.1:8000` y Vite en `127.0.0.1:5173` o puertos
   libres documentados. Conservar PID/handles de los procesos propios; si se
   usa `Start-Process`, hacerlo con `-WindowStyle Hidden` y `-PassThru`.
2. Abrir `/`, comprobar prop backend, contador con botón/teclado, foco visible
   y carga sin errores. Verificar estilos calculados de página y componente:
   mismo nombre local, nombres CSS generados distintos, sin colisión. Comprobar
   estilo global y una pantalla móvil y otra de escritorio.
3. Durante implementación, probar HMR con un cambio temporal controlado en
   texto JSX y CSS y comprobar la actualización observable. Restaurar el
   contenido final antes del build y guardar evidencias. La revisión de solo
   lectura reutiliza esa evidencia; no modifica archivos para repetir HMR.
4. Detener Vite, comprobar que no quede un `public/hot` propio obsoleto y
   ejecutar build. Cargar Laravel sin Vite: manifest, JS/CSS y página correctos,
   interacción y estilos aún funcionan, sin solicitudes fallidas ni intento de SSR.
5. Detener únicamente los procesos creados por la tarea, incluidos hijos
   identificados, sin matar procesos por nombre o puertos de otros trabajos.

| Criterio | Evidencia prevista |
|---|---|
| CA-01 | Árbol Laravel y manifiestos/locks, versiones exactas; comparación del contexto previo preservado. |
| CA-02 | Versiones/rutas de herramientas, PHP_BINARY en subproceso `@php`, runtime de PHPUnit y engines/peers sin conflictos. |
| CA-03 | PHPUnit HTML/Inertia; navegador con prop y contador operativo, sin errores de consola/red. |
| CA-04 | Inspección de imports; CSS Modules sin colisiones y global aplicado en desarrollo/producción. |
| CA-05 | Evidencia HMR, build correcto, Vite detenido y navegador cargando assets compilados. |
| CA-06 | Configuración sin motor seleccionado, pruebas sin conexiones/migraciones y sin archivos SQLite creados. |
| CA-07 | PHPUnit, validación/plataforma/auditorías y revisión de dependencias con comandos/resultados reales. |
| CA-08 | Hashes/PATH comparados, loopback, PIDs propios y servidores detenidos. |
| CA-09 | README reproducible, ignore efectivo de secretos/generados y locks versionables; estado documental veraz. |

## Documentación

Actualizar README con requisitos, instalación desde los locks, comandos de
PHP/Composer locales, npm, desarrollo/build/pruebas y límites sin BD ni producto.
Documentar los pasos revisados de autoload/discovery y los scripts disponibles.
Mantener los artefactos SDD y MEMORY conforme a resultados observados. No generar
documentación visual, de arquitectura o instrucciones duplicadas para esta base.

## Despliegue y compatibilidad

No se despliega. Build de producción es un control local y no aprueba
infraestructura, autenticación, seguridad de producción o uso con datos reales.
No modificar PHP, Composer global/local, Node/npm, Apache, certificados o PATH
persistente. Si un requisito externo falla, informar y volver a la decisión
correspondiente, conservando el avance independiente.

Inventario protegido antes/después:

- 112 archivos ya protegidos en 003: Composer global (PHAR y lanzador), PHP
  de XAMPP (EXE/INI), `.conf` bajo `C:\xampp\apache\conf` y todos los archivos
  del PHP independiente de UsTwo. Recalcular inventario; no asumir siempre 112.
- PHAR local de Composer 2.11.1 y ejecutables/entradas de Node y npm usados.
- AGENTS, `.agents`, `.codex` y artefactos SDD previos; README/MEMORY/decisiones
  se adaptan solo en el alcance autorizado. `.gitignore` conserva reglas previas.
- PATH persistentes de usuario/sistema comparados solo en memoria, sin imprimirlos.

No usar reset, stash, operaciones forzadas, cambios a main o limpieza automática
para reparar fallos. No borrar ni mover rutas existentes; una instalación parcial
se documenta y se continúa solo dentro del alcance aprobado.

## Riesgos y medidas

- La resolución aislada no sustituye descargar/ejecutar las dependencias reales.
  Repetir controles tras instalar y conservar los locks definitivos.
- Si un binario nativo no funciona con scripts npm desactivados, inspeccionar y
  comunicar el requisito concreto; no instalar compiladores o herramientas nuevas.
- No convertir la página técnica en identidad de producto ni publicar la app
  privada sin las futuras decisiones de acceso y privacidad.
- Cualquier prueba pendiente debe quedar visible; no completar la especificación
  si falta navegador, build real, ausencia de BD o conservación del entorno.

Fuentes: registros oficiales, referencia fijada de Laravel y documentación de
[instalación cliente](https://inertiajs.com/docs/v3/installation/client-side-setup),
[servidor](https://inertiajs.com/docs/v3/installation/server-side-setup) y
[desactivación de SSR](https://inertiajs.com/docs/v3/advanced/server-side-rendering#disabling-ssr)
de Inertia 3, consultados el 2026-11-08.
