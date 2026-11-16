---
spec: "003"
status: approved
---

# Plan técnico — 003

## Resumen

Descargar el PHAR oficial de Composer 2.11.1, verificar su integridad e instalarlo
en `%LOCALAPPDATA%\UsTwo\composer\2.11.1\composer.phar`. Ejecutarlo con PHP
8.5.12 independiente y comprobar ambas salidas para confirmar que desaparece
el aviso sin ocultarlo. Conservar Composer global, PHP, XAMPP y PATH.

La especificación 003 está aprobada. Este plan recibió revisión `PASS` y
aprobación expresa del desarrollador el 2026-11-08. Las tareas permanecen
pendientes hasta que se solicite expresamente su implementación. No instalar
la aplicación ni sus dependencias.

## Componentes afectados

- Nuevo PHAR local en la ruta indicada, fuera del repositorio.
- Carpeta temporal única bajo el directorio temporal del usuario: descarga,
  comprobador PHP, configuración temporal vacía y evidencias de ejecución.
- Solo lectura: PHP independiente, Composer global y archivos de XAMPP/Apache.
- Documentación existente: README, MEMORY y estado de DT-004; evidencias en
  `tasks.md`. No crear scripts o lanzadores persistentes para este alcance.

## Contratos, datos e interfaces

### Artefacto fijado

- Versión: Composer `2.11.1`.
- URL: `https://getcomposer.org/download/2.11.1/composer.phar`.
- SHA-256 esperado:
  `6f1b891e1d4927d62740276c540fb490e47689e19e102c49e8da849794882f8c`.
- Fuente de versión y hash, consultada el 2026-11-08:
  [descarga oficial](https://getcomposer.org/download/).
- Si el recurso no está disponible o no coincide el hash, no ejecutarlo ni
  sustituirlo por otra versión. Informar del resultado para revisar el plan.

### Precondiciones

- PHP 8.5.12 y `php.ini` en `%LOCALAPPDATA%\UsTwo\php\8.5`.
- Composer global 2.8.9 en `C:\ProgramData\ComposerSetup\bin`, conservado.
- Destino local inexistente en la inspección de planificación; repetirla antes
  de instalar. No sobrescribir un destino preexistente ni repararlo borrándolo.
- Acceso HTTPS y escritura en la carpeta local del usuario. Los permisos de
  ejecución correspondientes se tramitan al ejecutar la instalación autorizada.

### Archivos y variables protegidos

Registrar inventario de rutas y SHA-256 antes y después para:

- `C:\ProgramData\ComposerSetup\bin\composer.bat` y `composer.phar`.
- `C:\xampp\php\php.exe` y `php.ini`.
- Todos los archivos `.conf` bajo `C:\xampp\apache\conf`, incluidos
  `httpd.conf` y `extra\httpd-xampp.conf`.
- Todos los archivos de la instalación `%LOCALAPPDATA%\UsTwo\php\8.5`,
  incluido su `php.ini`; comparar también el inventario para detectar altas
  o bajas. No seguir enlaces que pudieran escapar de esas rutas.

Conservar los valores PATH de usuario y sistema solo en memoria y compararlos
sin mostrarlos. No modificar variables persistentes. Una diferencia concurrente
se informa y se investiga; no se restauran archivos ajenos automáticamente.

## Fases de implementación

1. **Inspección y línea base.** Confirmar el destino resuelto dentro de
   `%LOCALAPPDATA%\UsTwo\composer`, PHP y Composer global. Registrar el
   inventario protegido y crear una carpeta temporal única de evidencias.
2. **Descarga verificada.** Descargar por HTTPS el PHAR fijado a esa carpeta
   temporal y calcular `Get-FileHash -Algorithm SHA256`. Compararlo antes de
   ejecutarlo. Crear el destino vacío y copiar el archivo sin sobrescritura.
   Recalcular el hash del PHAR instalado.
3. **Comprobación de PHP y Composer.** Usar el ejecutable PHP 8.5 por ruta
   explícita y `-c` con el INI existente. Consultar versión, PHP_BINARY, INI y
   ajustes efectivos de errores con un archivo PHP temporal, evitando los
   problemas de comillas encontrados en 002. Ejecutar el PHAR nuevo con
   `--no-plugins --no-scripts --no-interaction -vvv --version`.
4. **Conservación y documentación.** Comparar hashes, inventarios y PATH;
   actualizar las instrucciones para usar el PHAR local. Mantener las evidencias
   históricas de 002 y explicar que el Composer global conserva su versión.
5. **Verificación SDD.** Relacionar los cinco criterios con evidencia real,
   ejecutar `git diff --check` y solicitar revisión de solo lectura. Completar
   artefactos solo tras `PASS`, sin commit ni publicación.

## Estrategia de pruebas y criterios

Las pruebas son previstas, no ejecutadas en esta fase. Capturar stdout y stderr
por separado y el código de salida; conservar el contenido completo para la
revisión. La presencia de información normal de `-vvv` en stderr no constituye
por sí sola un fallo: inspeccionar ambas salidas sin borrar líneas ni filtrar
mensajes para obtener un resultado favorable.

Durante las pruebas, aislar `PHPRC`, `PHP_INI_SCAN_DIR`, `COMPOSER_HOME` y
`COMPOSER_CACHE_DIR` solo en el proceso: sin PHPRC heredado, directorio de INI
adicional vacío y carpetas temporales nuevas para home/cache de Composer.
Guardar y restaurar sus valores originales mediante `try/finally`, sin
mostrarlos. Esto evita cargar configuración privada ajena; no modifica los
ajustes de errores de `php.ini` ni las variables persistentes.

El comprobador temporal PHP debe informar únicamente:

- `PHP_VERSION`, `PHP_BINARY` y `php_ini_loaded_file()`.
- `error_reporting()`, `E_ALL` y valores efectivos `display_errors`,
  `display_startup_errors` y `log_errors`.

Confirmar PHP 8.5.12, ejecutable e INI esperados y que `E_DEPRECATED` y
`E_USER_DEPRECATED` están incluidos en `error_reporting`. La configuración
actual debe seguir mostrando avisos; si no lo hace, informar y no modificarla
ni aceptar ausencia de texto como prueba de éxito. No usar `-d` ni cambios de
INI para ocultar errores; tampoco usar `@` para suprimirlos.

Invocación prevista en PowerShell, con las variables resueltas a las rutas
aprobadas y la captura de ambas salidas implementada durante la ejecución:

```powershell
$php85Dir = Join-Path $env:LOCALAPPDATA 'UsTwo/php/8.5'
$php85Exe = Join-Path $php85Dir 'php.exe'
$composerLocal = Join-Path $env:LOCALAPPDATA 'UsTwo/composer/2.11.1/composer.phar'
& $php85Exe -c "$php85Dir/php.ini" $composerLocal --no-plugins --no-scripts --no-interaction -vvv --version
```

Exigir código 0, Composer 2.11.1 y PHP 8.5.12 con su ruta real identificada.
Inspeccionar ambas salidas para descartar `Deprecated`, `Warning`, `Notice`,
errores de inicio, `Parse error` y `Fatal error`, incluido el aviso previo de
`react/promise`. Cualquier aviso bloquea CA-03, aunque el código sea 0.

| Criterio | Verificación prevista |
|---|---|
| CA-01 | URL y hash esperado/obtenido iguales antes de ejecutar y después de copiar; PHAR en destino independiente. |
| CA-02 | Salidas completas de la invocación: Composer 2.11.1, PHP 8.5.12 y ruta correcta; código 0. |
| CA-03 | Ajustes efectivos conservados y avisos habilitados; ambas salidas sin avisos ni errores. |
| CA-04 | Comparación SHA-256 e inventarios protegidos iguales, PATH persistentes sin cambios y variables temporales restauradas. |
| CA-05 | README actualizado, MEMORY veraz, diff revisado sin archivos locales y `git diff --check` correcto. |

## Documentación

- README: sustituir la invocación recomendada de Composer global por el PHAR
  local. Documentar versiones comprobadas y resultado real del aviso.
- Tareas: origen, hashes, ruta, versiones, ajustes de errores relevantes,
  resultado de stdout/stderr y controles de conservación, con referencia a
  evidencias temporales. No guardar configuración privada ni valores PATH.
- MEMORY y DT-004: actualizar solo tras observar los resultados reales.
  Mantener pendiente el resto del stack.

## Despliegue y compatibilidad

- Instalación local del usuario, sin despliegue ni configuración del sistema.
- Composer global y sus lanzadores siguen funcionando como antes; los comandos
  sin ruta explícita seguirán seleccionando el entorno previo.
- No cambiar certificados, PHP, PATH, plugins o dependencias como reparación.
  Si fueran necesarios, informar y volver a la decisión correspondiente.
- En caso de instalación parcial, documentar destino y estado; no borrar ni
  mover automáticamente archivos o carpetas.

## Riesgos y medidas

- La ausencia del aviso acredita únicamente la invocación comprobada. No
  acredita descargas de paquetes, validación TLS desde PHP ni subprocesos.
- La descarga se hace desde la terminal por HTTPS y se verifica contra el hash
  fijado; no ejecutar instaladores remotos ni depender de un canal `latest`.
- `-vvv` puede escribir en stderr incluso sin fallos. Evaluar contenido y
  código conjuntamente y no declarar éxito por mirar solo stdout.
