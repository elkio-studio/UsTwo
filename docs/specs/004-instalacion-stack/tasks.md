---
spec: "004"
status: completed
---

# Tareas — 004

- [x] **T-01 — Registrar el entorno y proteger el contenido existente**
  - Criterios: CA-01, CA-02, CA-08.
  - Comprobar herramientas, inventarios/hashes, PATH y puertos; preparar las
    evidencias temporales sin secretos y detectar colisiones de archivos.
  - Evidencia esperada: versiones/rutas y línea base completa, sin mutaciones Git.

- [x] **T-02 — Incorporar el esqueleto Laravel y configurar la base sin BD**
  - Criterios: CA-01, CA-06, CA-09.
  - Descargar referencia fijada, validar ZIP, preparar selección mínima e
    integrar preservando contexto. Adaptar `.gitignore`, manifiestos y entorno
    sin SQLite, modelos, migraciones o automatismos fuera del alcance.
  - Evidencia esperada: estructura/versión de plantilla, archivos protegidos,
    `.env` ignorado y configuración de sesiones/caché/colas correcta.

- [x] **T-03 — Instalar las dependencias y comprobar intérpretes**
  - Criterios: CA-01, CA-02, CA-07.
  - Instalar versiones fijadas con scripts/plugins controlados y generar ambos
    locks. Ejecutar autoload/discovery revisado y clave sin mostrarla. Comprobar
    el PHP de subprocesos Composer y restaurar cualquier entorno temporal.
  - Evidencia esperada: locks coherentes, plataforma y peers compatibles,
    PHP_BINARY correcto y ninguna instalación o migración ajena.

- [x] **T-04 — Conectar Laravel, Inertia, React y los estilos mínimos**
  - Criterios: CA-03, CA-04, CA-05, CA-06.
  - Preparar middleware/Blade/ruta, entrada JSX, página y contador con sus módulos,
    global solo en página y Vite. Desactivar SSR en ambas capas.
  - Evidencia esperada: prop backend visible, contador local y archivos propios
    sin TypeScript, Tailwind o dependencias no aprobadas.

- [x] **T-05 — Pruebas backend y controles de dependencias**
  - Criterios: CA-01, CA-02, CA-03, CA-06, CA-07.
  - Preparar/ejecutar PHPUnit sin BD; verificar HTML/JSON de Inertia, conexiones
    vacías y configuración. Validar manifiestos, plataforma y auditorías reales.
  - Evidencia esperada: comandos/códigos/resultados y ninguna dependencia o
    vulnerabilidad sin revisar; no instalar herramientas adicionales de pruebas.

- [x] **T-06 — Comprobar navegador, HMR y assets de producción**
  - Criterios: CA-03, CA-04, CA-05, CA-08.
  - Servir solo en loopback; probar props, contador, teclado, CSS aislado y global
    en móvil/escritorio. Comprobar HMR durante implementación y restaurar contenido.
    Detener Vite, compilar y verificar assets y comportamiento sin su servidor.
  - Evidencia esperada: capturas, estilos calculados, consola/red, build y prueba
    real sin Vite. Servidores propios identificados y detenidos al terminar.

- [x] **T-07 — Documentar y comprobar conservación y archivos ignorados**
  - Criterios: CA-01, CA-08, CA-09.
  - Comparar inventarios/hashes/PATH y contexto protegido, verificar ignore y
    documentar instalación, ejecución y límites en README; MEMORY veraz.
  - Evidencia esperada: entorno previo conservado, locks versionables y ningún
    secreto, binario o resultado generado en el diff versionable.

- [x] **T-08 — Verificación SDD final por criterio**
  - Criterios: CA-01, CA-02, CA-03, CA-04, CA-05, CA-06, CA-07, CA-08, CA-09.
  - Aplicar `ustwo-verify`, ejecutar `git diff --check` y delegar auditoría de
    solo lectura en `test_reviewer`, sin reparar durante la verificación.
  - Evidencia esperada: `PASS` con todos los criterios cubiertos antes de completar
    tareas/especificación y actualizar MEMORY. Sin commit, push ni despliegue.

## Evidencias de implementación — 2026-11-08

- **CA-01 / T-01 a T-03:** esqueleto 13.11.0 de la referencia y URL fijadas;
  ZIP validado contra escapes/enlaces, SHA-256 registrado:
  `544ee1a789b7f6beff50acd7b331511e61b85e1c24ec89a6386b58508e5df961`.
  Aplicación en la raíz, sin incorporar User, migraciones o starter kit.
  Versiones directas instaladas iguales a la tabla del plan, en ambos locks.
- **Ajuste aprobado:** Composer avisó de las restricciones exactas de Laravel
  e Inertia en `validate --strict`. El desarrollador aprobó `^13.40.0` y
  `^3.7.0` en el manifiesto. Se sincronizaron metadatos con `update --lock
  --no-install --no-scripts --no-plugins`, comparando las 102 versiones del lock
  antes/después: ninguna cambió. La validación estricta final devuelve 0.
- **CA-02 / T-03, T-05:** PHP 8.5.12 y Composer 2.11.1 independientes; Node
  22.16.0 y npm 10.9.2. Script temporal `@php` devuelve PHP_BINARY e INI de
  `%LOCALAPPDATA%\UsTwo\php\8.5`, código 0. PHPUnit informa PHP 8.5.12.
  Descargas reales de dependencias completadas con verificación TLS activa;
  ningún gestor o requisito de plataforma se forzó.
- **CA-03 / T-04 a T-06:** tres pruebas PHPUnit, 25 aserciones, código 0,
  incluidas HTML inicial, JSON Inertia y `/up`. Petición HTTP real inicial 200
  e Inertia 200 con componente `StackCheck` y prop ficticia esperada. Tras el
  build, la prueba usa la versión de assets recibida en la página inicial:
  sin esa cabecera Inertia devuelve correctamente 409 por versión distinta.
  Navegador: prop visible, contador de 5 a 6 por clic y de 6 a 7 por Enter;
  sin errores o avisos en consola.
- **CA-04 / T-04, T-06:** página y componente importan módulos separados;
  solo la página importa `global.css`. Clase local `status` genera
  `_status_91p65_11` para página y `_status_48cn9_9` para componente; estilos
  calculados azul `rgb(29, 78, 216)` y verde `rgb(22, 101, 52)`, sin colisión.
  Fuente global `system-ui, sans-serif` y foco de teclado sólido de 2px.
  Los mismos estilos se mantienen con los assets compilados.
- **CA-05 / T-06:** HMR comprobado cambiando temporalmente encabezado JSX y
  color CSS: actualización visible sin recarga y contador conservado en 7.
  Archivos restaurados exactamente por comparación de hashes. Build final
  correcto (585 módulos). Vite detenido; navegador usa assets `/build/` y el
  contador funciona de 0 a 1. Tres assets del manifest solicitados por HTTP,
  todos 200. Sin peticiones fallidas observadas ni SSR activo.
- **CA-06 / T-02, T-05:** configuración local efectiva: sesiones `file`, caché
  `file`, colas `sync`, `database.default=null`, cero conexiones y cero drivers
  PDO habilitados. SSR desactivado. Pruebas sin BD, sin archivos SQLite creados,
  sin autenticación o funcionalidades de producto.
- **CA-07 / T-05:** `composer validate --strict`, `check-platform-reqs`, `audit`,
  PHPUnit, `npm ls --depth=0`, `npm audit` y `npm run build` finalizaron con 0.
  Auditorías: sin avisos de vulnerabilidades de Composer y 0 vulnerabilidades
  npm. No se ejecutaron linters ni herramientas de prueba adicionales.
- **CA-08 / T-01, T-06, T-07:** línea base de 285 archivos protegidos. Sin
  modificaciones o ausencias salvo el ajuste del plan 004 autorizado por el
  desarrollador. Inventarios de `.agents` (153 archivos), `.codex` (4) y PHP
  independiente (79) idénticos. PATH persistentes iguales. Servidores solo en
  loopback: Laravel 11448 (hijos 23780, 19420, 22824) y Vite 11028, detenidos.
  Comprobación final de sockets: 8000 y 5173 no aceptan conexiones y están libres.
- **CA-09 / T-07:** README actualizado con instalación desde locks, desarrollo,
  build y pruebas. `git check-ignore` confirma `.env`, dependencias, assets y
  sesiones ignorados; ambos locks y `.env.example` no están ignorados.
  Revisión de archivos nuevos versionables sin `.env`, `vendor` o `node_modules`.
  Ningún secreto se mostró o incorporó. `git diff --check` correcto.

## Ubicación de evidencias

- Comandos, código de salida, stdout/stderr y conservación:
  `%LOCALAPPDATA%\Temp\ustwo-stack-install-p33ltf_v\`. Incluye `baseline.json`,
  `conservation.json`, `lock-versions-preserved.json`, `production-http.json`,
  `local-environment.json`, controles Composer/npm, PHPUnit y limpieza de servidores.
- Capturas y observaciones de navegador:
  `C:\Users\Angel J Ragel\.codex\visualizations\2026\11\08\01a1bcd1-777d-7ce0-90c1-34fde43462e8\`.
  Archivos `browser-desktop.json`, `browser-mobile.json`, `browser-hmr.json`,
  `browser-production.json` y capturas `development-*.jpg`, `production-*.jpg`.
- Escritorio observado: 1101 × 831. Móvil emulado: 390 × 844, sin desbordamiento;
  botón de 47.59px de alto. No se afirma prueba en un dispositivo físico ni
  certificación de accesibilidad. Se revisaron semántica, foco y contraste de
  la página técnica, sin aprobar una identidad visual para el producto.

## Auditoría final — 2026-11-09

Resultado **PASS** de `test_reviewer` para CA-01 a CA-09. La auditoría de solo
lectura contrastó los manifiestos, los locks, el código y los registros de
instalación, HTTP, navegador y conservación del entorno.

En el cierre se repitieron `git diff --check`, `composer validate --strict`,
`composer check-platform-reqs`, `npm ls --depth=0` y PHPUnit con PHP 8.5.12.
Todos pasaron. PHPUnit confirmó 3 pruebas y 25 aserciones. La auditoría revisó
también los resultados registrados del build, HMR y las auditorías de paquetes.
