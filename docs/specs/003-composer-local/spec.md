---
id: "003"
title: "Composer 2.11.1 independiente para UsTwo"
status: completed
---

# 003 — Composer 2.11.1 independiente para UsTwo

## Problema y objetivo

La instalación de PHP 8.5 (cambio 002, completado) comprobó Composer global
2.8.9 con código de salida 0, pero detectó un aviso de obsolescencia en su
biblioteca interna `react/promise`. Instalar Composer 2.11.1 por separado para
UsTwo y verificar que su ejecución con PHP 8.5 no emite ese aviso ni otros.

La instalación anterior excluía actualizar Composer. Este cambio conserva su
evidencia histórica y define el nuevo alcance solicitado por el desarrollador.

## Alcance incluido

- Composer 2.11.1 obtenido como PHAR de su distribución oficial, comprobando
  SHA-256 antes de ejecutarlo.
- Instalación independiente dentro de la carpeta local de herramientas de
  UsTwo, fuera del repositorio y del directorio de Composer global.
- Invocación con `%LOCALAPPDATA%\UsTwo\php\8.5\php.exe` y su `php.ini`.
- Verificación de versión, intérprete, salida y código de terminación sin
  suprimir avisos, deprecaciones ni errores.
- Actualizar las instrucciones existentes en README y el estado operativo.

## Alcance excluido

- Modificar el PHAR o lanzador globales de Composer 2.8.9, XAMPP o Apache.
- Cambiar PHP, su configuración, certificados o PATH persistente.
- Instalar Laravel o dependencias de la aplicación; ejecutar `install`,
  `update`, `self-update`, plugins o scripts de Composer.
- Introducir lanzadores globales, servicios, herramientas o binarios en Git.
- Probar acceso a repositorios de paquetes o procesos secundarios de PHP;
  esas comprobaciones corresponden a la instalación posterior del stack.

## Actores

- Desarrollador: confirma el alcance y aprueba especificación y plan.
- Agente principal: instala y verifica con el único permiso de escritura.
- Revisores SDD: revisan artefactos y evidencias en modo de solo lectura.

## Flujo principal

1. Registrar hashes e inventario protegido y comprobar la disponibilidad del
   PHP 8.5 independiente y que el destino elegido está libre.
2. Descargar el PHAR 2.11.1 oficial a una carpeta temporal independiente y
   comprobar su SHA-256 antes de ejecutarlo o instalarlo en su destino.
3. Instalarlo sin sobrescribir archivos existentes.
4. Ejecutarlo con PHP 8.5, plugins y scripts desactivados, conservando salida
   estándar, salida de error y código de terminación. Verificar versión e
   intérprete reales y ausencia de avisos de obsolescencia.
5. Comparar los archivos y variables protegidos y documentar la invocación
   local que se usará para UsTwo en adelante.

## Flujos alternativos y errores

- Si la descarga no está disponible o el hash no coincide, detener la
  instalación sin cambiar de versión ni ejecutar el archivo.
- Si el destino existe, inspeccionarlo sin sobrescribirlo ni borrarlo.
- Si la versión no coincide, falla la ejecución o aparece cualquier aviso,
  registrar el resultado y no considerar cumplido el objetivo; no ocultarlo.
- Si se necesita un cambio excluido, solicitar la decisión correspondiente
  antes de actuar. No modificar el Composer global como reparación.

## Reglas de negocio y casos límite

- No modifica reglas del producto ni inicia la aplicación.
- El Composer global puede seguir mostrando el aviso si se ejecuta con PHP
  8.5: la corrección consiste en usar el nuevo PHAR independiente para UsTwo.
- Mantener TLS y los ajustes de errores del PHP existente. Si se aíslan
  variables de configuración en el proceso de prueba, restaurarlas al terminar.
- Evidencias con rutas, versiones, hashes y resultados, sin contenido de
  configuración privada ni valores de variables sensibles.

## Criterios de aceptación

- **CA-01:** El nuevo PHAR está fuera del repositorio y de la instalación
  global, y se conservan origen oficial y SHA-256 esperado y obtenido iguales.
- **CA-02:** La invocación explícita devuelve Composer 2.11.1 y PHP 8.5.12 del
  ejecutable independiente, con código 0.
- **CA-03:** Con los ajustes de errores existentes, no aparecen el aviso de
  `react/promise`, otros avisos de obsolescencia ni errores en ninguna de las
  dos salidas de la comprobación. No se filtran ni suprimen para hacerla pasar.
- **CA-04:** Los hashes e inventario protegido del Composer global, PHP nuevo
  y archivos de XAMPP/Apache inspeccionados no cambian; tampoco cambian los
  PATH persistentes. El plan enumerará las rutas exactas que se compararán.
- **CA-05:** README describe la nueva invocación y las limitaciones; MEMORY
  refleja el resultado real. Git contiene solo los cambios documentales de
  esta tarea, sin binarios, dependencias ni configuración local.

## Decisiones aprobadas

- Composer 2.11.1 por separado para UsTwo y comprobación del aviso (DT-004).
- PHP 8.5 independiente ya instalado, sin sustituir el entorno de XAMPP.
- Continuar en `main` hasta terminar de instalar el stack, según autorización
  del desarrollador. Esta autorización no permite commits ni subidas.

## Decisiones pendientes

- Ninguna para este alcance. El plan fijará rutas y comandos concretos de
  instalación y comprobación. Especificación aprobada expresamente por el
  desarrollador el 2026-11-08, tras revisión `PASS`; permite preparar el plan.

## Fuentes

- [Descarga oficial de Composer 2.11.1 y su SHA-256](https://getcomposer.org/download/).
