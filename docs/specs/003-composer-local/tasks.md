---
spec: "003"
status: completed
---

# Tareas — 003

- [x] **T-01 — Confirmar destino y registrar la línea base**
  - Criterios: CA-01, CA-04.
  - Verificar PHP y destino; inventariar y calcular hashes de las rutas
    protegidas. Conservar PATH solo en memoria para su comparación.
  - Evidencia esperada: destino libre, intérprete disponible e inventario inicial.

- [x] **T-02 — Descargar e instalar el PHAR verificado**
  - Criterios: CA-01.
  - Descargar la versión exacta a carpeta temporal, comparar SHA-256 antes de
    ejecutar y copiar sin sobrescribir al destino; verificar de nuevo el hash.
  - Evidencia esperada: URL, versión, hashes iguales y ruta final independiente.

- [x] **T-03 — Comprobar versión y ausencia de avisos**
  - Criterios: CA-02, CA-03.
  - Consultar ajustes efectivos de PHP y ejecutar Composer con captura separada
    de ambas salidas; restaurar variables temporales del proceso al terminar.
  - Evidencia esperada: PHP 8.5.12 y Composer 2.11.1, código 0, avisos habilitados
    y ninguna deprecación, aviso ni error en stdout/stderr.

- [x] **T-04 — Confirmar conservación y documentar la invocación local**
  - Criterios: CA-04, CA-05.
  - Comparar inventarios, hashes y PATH; actualizar README y estado operativo
    sin modificar evidencias históricas de la instalación 002.
  - Evidencia esperada: archivos protegidos y PATH sin cambios; instrucciones
    reproducibles y ausencia de binarios o configuración local en Git.

- [x] **T-05 — Verificación SDD y revisión final**
  - Criterios: CA-01, CA-02, CA-03, CA-04, CA-05.
  - Aplicar `ustwo-verify`, ejecutar `git diff --check` y solicitar auditoría de
    evidencias a `test_reviewer` en modo de solo lectura.
  - Evidencia esperada: resultado por criterio y `PASS` sin bloqueos antes de
    completar tareas, especificación y MEMORY. Sin commit ni publicación.

## Evidencias de ejecución — 2026-11-08

- **CA-01 / T-01, T-02:** destino inicialmente inexistente; instalado el PHAR
  en `%LOCALAPPDATA%\UsTwo\composer\2.11.1\composer.phar`, fuera de Git y
  del Composer global. URL oficial fijada en el plan. SHA-256 antes de ejecutar
  y después de copiar, iguales al esperado:
  `6f1b891e1d4927d62740276c540fb490e47689e19e102c49e8da849794882f8c`.
- **CA-02 / T-03:** invocación del plan con `--no-plugins --no-scripts
  --no-interaction -vvv --version`: código 0. Stdout:
  `Composer version 2.11.1 2026-10-30 14:52:10`. Stderr identifica Composer
  2.11.1, PHP 8.5.12 y su ejecutable en la carpeta independiente de UsTwo.
- **CA-03 / T-03:** stdout y stderr capturados íntegramente por separado;
  ninguna deprecación, aviso ni error. Stderr contiene solo información normal
  de versión y la sugerencia de usar `diagnose`. Comprobador PHP temporal:
  `error_reporting = E_ALL = 30719`, `E_DEPRECATED = 8192`,
  `E_USER_DEPRECATED = 16384`, `display_errors = 1`,
  `display_startup_errors = 1`, `log_errors = 1`. INI cargado desde la
  instalación PHP independiente. No se modificó su configuración ni se usó
  `-d`, supresión de errores o filtrado de las salidas para hacer pasar la prueba.
- **CA-04 / T-01, T-04:** inventario y SHA-256 de 112 archivos protegidos
  comparados antes/después, iguales. Sin cambios en los PATH persistentes de
  usuario y sistema. Variables temporales PHPRC, PHP_INI_SCAN_DIR, COMPOSER_HOME
  y COMPOSER_CACHE_DIR restauradas; sus valores no se mostraron. El Composer
  global, PHP independiente y archivos de XAMPP/Apache permanecen intactos.
- **CA-05 / T-04, T-05:** README actualizado para recomendar el PHAR local y
  distinguirlo del global. Descarga, PHAR y comprobador fuera del repositorio.
  `git diff --check` correcto y auditoría final `PASS`; sin commits ni subidas.

Evidencias temporales:
`%LOCALAPPDATA%\Temp\ustwo-composer2111-zwlfqj2_\` contiene `integrity.json`,
`php-settings.stdout.txt`, `php-settings.stderr.txt`, `composer.stdout.txt`
y `composer.stderr.txt`. El informe conserva origen, hashes, inventario
protegido y resultados; no guarda valores PATH ni configuración privada.

Limitaciones: la comprobación valida la invocación de versión. No se ejecutaron
instalaciones de paquetes, pruebas TLS desde PHP ni procesos PHP secundarios.
Las evidencias históricas de 002 se conservan; el aviso del Composer global
anterior no se ha ocultado ni reparado modificando ese PHAR.

## Revisión final

`test_reviewer`: **PASS**, sin bloqueos para CA-01 a CA-05. Recalculó el hash
del PHAR instalado y los 112 archivos protegidos sin diferencias, y revisó las
dos salidas completas de Composer y los ajustes efectivos de PHP. Se completan
T-01 a T-05 y la especificación 003; el plan permanece aprobado. Se mantienen
las limitaciones de las pruebas de versión indicadas arriba. El cierre no
autoriza commits, publicación ni instalar el resto del stack.
