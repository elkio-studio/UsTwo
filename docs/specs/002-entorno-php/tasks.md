---
spec: "002"
status: completed
---

# Tareas — 002

- [x] **T-01 — Confirmar precondiciones y registrar la línea base**
  - Criterios: CA-01, CA-04, CA-05.
  - Comprobar arquitectura, destino, Composer y bibliotecas del sistema;
    registrar hashes e inventario protegido y comparar PATH sin mostrarlo.
  - Evidencia esperada: rutas, versiones, hashes iniciales y destino disponible.

- [x] **T-02 — Descargar y verificar la distribución fijada**
  - Criterios: CA-02.
  - Descargar el ZIP exacto; verificar SHA-256 y que las entradas no escapan
    del destino. No extraer si alguna comprobación falla.
  - Evidencia esperada: URL, nombre, revisión y hash obtenido igual al esperado.

- [x] **T-03 — Instalar y configurar PHP independiente**
  - Criterios: CA-01, CA-03, CA-05.
  - Extraer en el destino aprobado, comprobar arranque sin INI y crear la
    configuración propia con extensiones del mismo paquete.
  - Evidencia esperada: versión, PHP_BINARY, INI y arranque sin errores.

- [x] **T-04 — Verificar módulos, funciones y Composer con PHP 8.5**
  - Criterios: CA-03, CA-04.
  - Ejecutar los controles del plan con aislamiento temporal de INI y
    restauración del entorno del proceso. No instalar dependencias.
  - Evidencia esperada: módulos y funciones presentes, Composer sin errores,
    versión PHP correcta y códigos de salida.

- [x] **T-05 — Comprobar conservación de XAMPP y documentar uso local**
  - Criterios: CA-05, CA-06.
  - Comparar archivos protegidos y PATH, ejecutar PHP anterior y actualizar
    README con las rutas efectivas y los límites de lo comprobado.
  - Evidencia esperada: hashes/inventario sin cambios, igualdad de PATH y guía
    reproducible. Ningún binario ni configuración local en Git.

- [x] **T-06 — Verificación SDD y revisión final de evidencias**
  - Criterios: CA-01, CA-02, CA-03, CA-04, CA-05, CA-06.
  - Aplicar `ustwo-verify` y auditoría `test_reviewer` de solo lectura; registrar
    comandos, resultados y limitaciones por criterio. Ejecutar `git diff --check`.
  - Evidencia esperada: `PASS` con todos los criterios cubiertos antes de marcar
    esta tarea y la especificación completas y actualizar MEMORY. No commit.

## Evidencias de ejecución — 2026-11-08

- **CA-01 / T-01, T-03:** Windows x64 y destino inicialmente inexistente.
  `php.exe -n -v` y `php.exe -c <ini> -v` devolvieron código 0, PHP 8.5.12 NTS.
  `PHP_BINARY` y `php_ini_loaded_file()` identifican respectivamente `php.exe`
  y `php.ini` bajo `%LOCALAPPDATA%\UsTwo\php\8.5`, fuera de XAMPP.
- **CA-02 / T-02:** descargado el ZIP exacto de la URL fijada en el plan.
  SHA-256 obtenido, igual al esperado:
  `2a36a2edcb7a5b9252f638fd5e4cf84d7f6e066092654220dc0b63e5cf4e9723`.
  Se validaron 84 entradas contra escapes de ruta y enlaces antes de extraer.
- **CA-03 / T-03, T-04:** configuración creada desde `php.ini-development`
  de la misma distribución. `--ini`, `-v` y `-m` correctos, sin errores de carga.
  Comprobación PHP de las 14 extensiones del plan, cuatro funciones y clase
  `ZipArchive`: código 0. Se aisló la configuración adicional y se restauraron
  las variables del proceso. Un primer intento con `-r` falló por el paso de
  comillas de la terminal; se repitió con un archivo PHP temporal, que pasó.
- **CA-04 / T-04:** ejecutado el PHAR global existente con el nuevo PHP y
  `--no-plugins --no-scripts -vvv --version`: código 0, Composer 2.8.9 informa
  PHP 8.5.12 y su ejecutable independiente. Emite `Deprecated: Case statements
  followed by a semicolon (;) are deprecated, use a colon (:) instead` en
  `vendor/react/promise/src/functions.php:300`. No se ocultó el aviso ni se
  actualizó Composer. El revisor considera el aviso un riesgo no bloqueante
  para esta comprobación, que satisface CA-04 sin acreditar otras operaciones.
- **CA-05 / T-01, T-05:** 33 archivos protegidos comparados por ruta y SHA-256
  antes/después, sin diferencias. PATH persistentes de usuario y máquina iguales.
  Los hashes se volvieron a comprobar tras probar Composer, sin cambios.
  `C:\xampp\php\php.exe -v` sigue devolviendo PHP 8.2.12, código 0.
- **CA-06 / T-05, T-06:** README contiene las invocaciones locales y la advertencia
  de Composer. Instalación, ZIP y comprobador PHP fuera del repositorio.
  `git diff --check` pasó. La revisión final de evidencias emitió `PASS`;
  no se han hecho commits.

Evidencia temporal de integridad (inventario de hashes, sin contenido de INI ni
valores PATH):
`%LOCALAPPDATA%\Temp\ustwo-php85-9055b626b04444b5b09a8f7761077e81\integrity.json`.

Limitaciones: no se han probado descargas de paquetes, confianza TLS ni procesos
PHP secundarios de Composer. No hay aplicación que construir o probar todavía.

## Revisión final

`test_reviewer`: **PASS**, sin bloqueos para CA-01 a CA-06. Recalculó el hash
del ZIP, comprobó los 33 archivos protegidos y repitió el comprobador PHP y
la invocación de Composer. Mantiene como riesgos no bloqueantes el aviso de
obsolescencia y las operaciones de Composer todavía no probadas. Con esta
evidencia se completan T-01 a T-06 y la especificación 002; el plan permanece
aprobado. Este cierre no autoriza commits ni publicación.
