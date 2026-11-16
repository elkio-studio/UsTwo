---
id: "002"
title: "Preparación de PHP 8.5 separado de XAMPP"
status: completed
---

# 002 — Preparación de PHP 8.5 separado de XAMPP

## Problema y objetivo

El PHP disponible en la terminal es 8.2.12 de XAMPP y no satisface los requisitos
de Laravel 13. Preparar PHP 8.5 para UsTwo sin sustituir el entorno de XAMPP.
La especificación 001 solo define el stack y no autoriza esta instalación.

## Alcance incluido

- Instalación independiente de una distribución oficial estable de PHP 8.5
  para Windows compatible con la arquitectura del equipo.
- Comprobación de integridad del archivo descargado contra el SHA-256 oficial.
- Configuración propia de PHP y extensiones requeridas por Laravel 13 y Composer.
- Ejecutar Composer con el PHP nuevo, verificando qué intérprete usa realmente.
- Documentar en README el uso local y los controles realizados.

## Alcance excluido

- Sustituir archivos de XAMPP, modificar Apache, servicios o bases de datos.
- Cambiar el PATH persistente de usuario o sistema o instalar otros runtimes.
- Instalar Laravel, React, Inertia, Vite o dependencias de la aplicación.
- Implementar funcionalidades, autenticación, persistencia o despliegue.

## Actores

- Desarrollador: aprueba la especificación y el plan; solicita la ejecución.
- Agente principal: prepara y verifica exclusivamente el entorno aprobado.

## Flujo principal

1. Comprobar arquitectura, requisitos del runtime y ubicaciones existentes.
2. Descargar la distribución oficial y verificar su integridad antes de usarla.
3. Instalarla en una ubicación independiente y crear su configuración propia.
4. Comprobar versión, configuración y extensiones con el ejecutable nuevo.
5. Ejecutar el Composer disponible con PHP 8.5 y verificar su intérprete.
6. Confirmar que el PHP anterior y la configuración de XAMPP siguen intactos.

## Flujos alternativos y errores

- Si la integridad no coincide, no extraer ni ejecutar la descarga.
- Si el destino ya contiene archivos, inspeccionarlo y no sobrescribirlo.
- Si faltan bibliotecas de Windows u otros requisitos externos, informar y
  presentar la instalación necesaria para aprobación; no instalarlos por defecto.
- Si Composer o una extensión no funcionan, informar del error y no volver al
  PHP anterior como si la comprobación hubiera pasado.
- Si el procedimiento requiere un cambio excluido, detener esa parte y
  solicitar una decisión; conservar los resultados independientes.

## Reglas de negocio y casos límite

- Este cambio no altera las reglas del producto ni inicia la aplicación.
- No reutilizar DLL ni copiar íntegramente `php.ini` de XAMPP entre versiones.
- No incorporar binarios, configuración local o certificados al repositorio.
- No desactivar la verificación TLS para resolver problemas de certificados.

## Criterios de aceptación

- **CA-01:** El ejecutable independiente informa PHP 8.5.x y su ubicación y
  archivo de configuración no están dentro de XAMPP.
- **CA-02:** Se conserva evidencia de origen, versión exacta y coincidencia
  SHA-256 de la distribución instalada.
- **CA-03:** PHP arranca sin errores de carga y las extensiones necesarias de
  Laravel 13 y Composer están disponibles, con comprobaciones documentadas.
- **CA-04:** Composer se ejecuta correctamente usando el PHP 8.5 nuevo, sin
  modificar el lanzador global ni instalar dependencias del proyecto.
- **CA-05:** La versión y los hashes de los archivos de PHP, configuración de
  XAMPP y Apache inspeccionados antes y después no cambian; el PATH persistente
  de usuario y sistema tampoco cambia.
- **CA-06:** README permite repetir la invocación de PHP y Composer; el estado
  operativo refleja el resultado real y los archivos locales no se versionan.

## Decisiones aprobadas

- Laravel 13 y PHP 8.5 separado de XAMPP (DT-003).
- Composer como gestor de dependencias PHP (DT-002).

## Enfoque técnico aprobado con esta especificación

- Usar una distribución ZIP oficial para CLI, sin registrar servicios.
- Guardarla en `%LOCALAPPDATA%\UsTwo\php\8.5`, fuera del repositorio.
- Invocar explícitamente ese ejecutable para PHP y para el archivo de Composer
  disponible, evitando cambios persistentes del PATH.
- Fijar en el plan el parche estable 8.5.x, el archivo, su hash y las extensiones
  tras verificar las fuentes oficiales y la arquitectura del equipo.

## Decisiones pendientes

- Ninguna para este alcance. Especificación aprobada expresamente por el
  desarrollador el 2026-11-08, tras revisión `PASS`. La aprobación autoriza
  preparar el plan, no ejecutar la instalación.

## Fuentes

- [PHP para Windows](https://www.php.net/downloads.php?os=windows&version=8.5).
- [Instalación de PHP en Windows](https://www.php.net/manual/en/install.windows.php).
- [Requisitos de Laravel 13](https://laravel.com/framework/docs/13.x/deployment).
