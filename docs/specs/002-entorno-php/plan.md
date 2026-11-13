---
spec: "002"
status: approved
---

# Plan técnico — 002

## Resumen

Instalar PHP 8.5.12-1 x64 NTS desde el ZIP oficial en
`%LOCALAPPDATA%\UsTwo\php\8.5`. Crear un `php.ini` independiente y ejecutar
Composer con ese intérprete por ruta explícita. No instalar la aplicación ni
modificar XAMPP, Apache, lanzadores globales o el PATH persistente.

La especificación 002 está aprobada. Este plan recibió revisión `PASS` y
aprobación expresa del desarrollador el 2026-11-08. Las tareas permanecen
pendientes hasta que se solicite expresamente su implementación.

## Componentes afectados

- Fuera del repositorio: carpeta local de PHP indicada, incluidos sus binarios,
  extensiones y configuración. Descarga y evidencias temporales en una carpeta
  única bajo el directorio temporal del usuario, sin sobrescribir archivos.
- Solo lectura: `C:\ProgramData\ComposerSetup\bin\composer.phar` y
  `composer.bat`, instalación de XAMPP y variables persistentes del entorno.
- Repositorio: sección de uso local en README, estado en MEMORY y evidencias
  de verificación en las tareas. Sin nuevos scripts, dependencias ni binarios
  versionados para este alcance.

## Contratos, datos e interfaces

### Distribución fijada

- Versión de distribución: `8.5.12-1`; PHP puede informar `8.5.12` en CLI,
  porque el sufijo identifica la revisión de la distribución de Windows.
- Variante: VS17 x64 Non Thread Safe, destinada a CLI, no al módulo de Apache.
- Archivo: `php-8.5.12-1-nts-Win32-vs17-x64.zip`.
- URL: `https://downloads.php.net/~windows/releases/archives/php-8.5.12-1-nts-Win32-vs17-x64.zip`.
- SHA-256 esperado:
  `2a36a2edcb7a5b9252f638fd5e4cf84d7f6e066092654220dc0b63e5cf4e9723`.
- Fuente y hash comprobados el 2026-11-08 en la
  [página oficial de PHP](https://www.php.net/downloads.php?os=windows&version=8.5).
  Si archivo, disponibilidad o hash cambian, no sustituir por otra descarga:
  informar y actualizar el plan para revisión antes de ejecutar otra variante.

### Precondiciones observadas y por repetir

- Windows x64; el destino no existía en la inspección de planificación.
- PHP actual: `C:\xampp\php\php.exe`, versión 8.2.12.
- Composer: 2.8.9, con PHAR existente en la ruta indicada.
- `vcruntime140.dll`, `vcruntime140_1.dll` y `msvcp140.dll` presentes en
  `C:\Windows\System32`, versión 14.51.36247.0. No se deduce compatibilidad
  solo de su presencia: la ejecución real de PHP sin errores será el control.
- La instalación requiere acceso de red y escritura fuera del repositorio.
  Tramitar los permisos de ejecución que correspondan cuando esté autorizada;
  no confundir la aprobación del plan con la solicitud de implementar.

### Configuración y extensiones

- Crear `php.ini` desde `php.ini-development` del ZIP nuevo, sin copiar la
  configuración de XAMPP. Mantener valores del paquete salvo los ajustes
  necesarios de esta sección.
- Fijar `extension_dir` a la carpeta `ext` absoluta de esta instalación.
- Habilitar las extensiones DLL `curl`, `fileinfo`, `mbstring`, `openssl` y
  `zip` del mismo ZIP. Zip permite a Composer extraer archivos sin incorporar
  otro programa. No habilitar drivers de bases de datos por defecto.
- Verificar también las extensiones integradas requeridas por Laravel:
  `ctype`, `dom`, `filter`, `hash`, `pcre`, `PDO`, `session`, `tokenizer` y `xml`.
  Si falta alguna, no intentar sustituirla con una DLL de otra distribución.
- Mantener la comprobación TLS. Si se necesita un almacén de certificados
  adicional y no hay una configuración de confianza válida disponible, informar
  del bloqueo y proponer su procedencia antes de añadirlo.
- Las comprobaciones usarán `-c` con el `php.ini` absoluto. Aislar temporalmente
  `PHPRC` y `PHP_INI_SCAN_DIR` en el proceso de comprobación y restaurarlos en
  `finally`, para evitar cargar configuración ajena. No mostrar sus valores.

## Fases de implementación

1. **Inspección y línea base.** Repetir precondiciones y comprobar que las rutas
   resueltas están dentro de los destinos previstos. Si el destino ya contiene
   archivos, no sobrescribir: inspeccionar e informar. Registrar versión y
   SHA-256 de los archivos protegidos enumerados abajo, y conservar en memoria
   los valores PATH de usuario y máquina para compararlos, sin imprimirlos.
2. **Descarga e integridad.** Descargar por HTTPS a la carpeta temporal única.
   Calcular `Get-FileHash -Algorithm SHA256` y compararlo con el valor fijado.
   Comprobar que todas las entradas del ZIP se resuelven dentro del destino y
   no contienen enlaces o rutas que escapen. Extraer solo tras ambas validaciones.
3. **Instalación y configuración.** Crear el destino vacío, extraer y ejecutar
   primero PHP sin configuración (`-n -v`). Si falla por dependencias del sistema,
   detenerse sin instalar otros runtimes. Crear después `php.ini` y habilitar las
   extensiones indicadas. No modificar PATH, registro, servicios o XAMPP.
4. **Verificación técnica.** Comprobar versión, `PHP_BINARY`, archivo INI cargado,
   módulos y funciones necesarias; ejecutar el PHAR existente con PHP 8.5. Capturar
   resultado y código de salida. No ejecutar `install`, `update` o scripts de
   Composer ni actualizar Composer como reparación automática.
5. **Conservación y documentación.** Repetir hashes y versión del PHP anterior,
   comparar los PATH persistentes y actualizar README con invocaciones reales.
   Ejecutar la verificación SDD; registrar resultados por criterio y limitaciones.

Archivos protegidos para comparar antes y después (solo rutas y hashes,
sin mostrar contenido de configuración):

- `C:\xampp\php\php.exe`.
- `C:\xampp\php\php.ini`.
- `C:\xampp\apache\conf\httpd.conf`.
- `C:\xampp\apache\conf\extra\httpd-xampp.conf`.
- Todos los demás `.conf` bajo `C:\xampp\apache\conf`, inventariando además
  rutas para detectar adiciones o eliminaciones.
- `C:\ProgramData\ComposerSetup\bin\composer.bat` y `composer.phar`.

Si un archivo protegido cambia concurrentemente, informar; no restaurarlo ni
atribuir su cambio automáticamente a esta tarea. La comparación no demuestra
el funcionamiento de todas las aplicaciones de XAMPP; demuestra conservación
de las rutas y variables inspeccionadas y ejecución del PHP anterior.

## Estrategia de pruebas y criterios

Comandos previstos en PowerShell, tras resolver `$php85Dir` a la carpeta
aprobada y `$php85Exe` a su `php.exe`. Son comprobaciones a ejecutar durante la
implementación, no resultados ya obtenidos:

```powershell
& $php85Exe -n -v
& $php85Exe -c "$php85Dir/php.ini" --ini
& $php85Exe -c "$php85Dir/php.ini" -v
& $php85Exe -c "$php85Dir/php.ini" -m
& $php85Exe -c "$php85Dir/php.ini" -r 'echo PHP_BINARY, PHP_EOL, php_ini_loaded_file(), PHP_EOL;'
& $php85Exe -c "$php85Dir/php.ini" 'C:/ProgramData/ComposerSetup/bin/composer.phar' --no-plugins --no-scripts -vvv --version
& 'C:/xampp/php/php.exe' -v
git diff --check
```

Comprobar cada código de salida antes de continuar. Además de `-m`, verificar
`extension_loaded` para cada módulo enumerado y funciones `curl_init`,
`mb_strlen`, `openssl_encrypt`, `finfo_open` y clase `ZipArchive`. Fallar ante
avisos de inicio o extensiones ausentes. Conservar salida de Composer que
identifique PHP 8.5 y la versión del PHAR; contrastarla con `PHP_BINARY`.

| Criterio | Verificación prevista |
|---|---|
| CA-01 | Versión PHP 8.5.12, PHP_BINARY e INI dentro del destino independiente. |
| CA-02 | URL, variante, versión, SHA-256 esperado y obtenido; coincidencia antes de extraer. |
| CA-03 | Arranque sin errores, módulos requeridos y funciones disponibles. Sin pruebas de la app, todavía inexistente. |
| CA-04 | PHAR existente ejecutado con PHP nuevo, código 0 y versión de PHP identificada; hashes del lanzador/PHAR conservados. |
| CA-05 | Hashes e inventario de rutas protegidas iguales, PHP anterior ejecutable con misma versión y PATH persistentes idénticos. |
| CA-06 | README con rutas reproducibles, MEMORY veraz y revisión de Git sin archivos de instalación. |

## Documentación

- README: agregar a la documentación existente la invocación de PHP y Composer
  por rutas explícitas. Aclarar que `php` y `composer` sin esas rutas siguen
  usando el entorno previo y que Laravel aún no está instalado.
- Tareas: registrar versión y hash reales y los resultados de los controles.
  No adjuntar archivos INI, contenido del entorno ni datos de otros proyectos.
- MEMORY: actualizar tras observar la instalación y la verificación reales;
  no anticipar completitud. DT-003 conserva la decisión aprobada.

## Despliegue y compatibilidad

- Solo entorno local del usuario, sin despliegue ni cambios al proyecto de app.
- No se instala el runtime de Visual C++ si la ejecución falla; esa decisión
  requeriría aprobación por estar fuera de esta especificación.
- No se prueba una instalación de Laravel para comprobar este entorno: hacerlo
  introduciría dependencias y alcance no aprobados.
- No borrar automáticamente el destino ni mover carpetas como reparación.
  Si queda una instalación parcial, registrar su ruta y estado para decidir
  cómo continuar. No tocar instalaciones previas ni XAMPP.

## Riesgos y medidas

- Composer 2.8.9 puede generar avisos o resultar incompatible con PHP 8.5:
  registrarlos y bloquear CA-04 si no funciona correctamente; proponer por
  separado una actualización, sin modificar el PHAR global.
- Los comandos de este plan no ejecutan procesos PHP secundarios de Composer.
  Esa selección del intérprete deberá comprobarse al instalar la app.
- La confianza TLS y las descargas de paquetes de la aplicación no se validan
  con `--version`; quedan pendientes para la instalación de dependencias.
- Un antivirus o permisos locales pueden impedir extraer o ejecutar el ZIP.
  Informar del resultado concreto; no desactivar protecciones del sistema.

Fuentes técnicas: [descarga y requisitos de PHP Windows](https://www.php.net/downloads.php?os=windows&version=8.5)
y [extensiones requeridas por Laravel 13](https://laravel.com/framework/docs/13.x/deployment).
