---
id: "007"
title: "Despliegue de UsTwo en Render"
status: approved
---

# 007 — Despliegue de UsTwo en Render

## Problema y objetivo

Publicar el MVP privado de UsTwo con acceso por HTTPS para Ángel y Alejandra,
datos persistentes en PostgreSQL de Supabase y un proceso verificable de integración y
despliegue en GitHub Actions. La experiencia de Películas y la identidad visual
siguen las especificaciones 005 y 006.

## Alcance incluido

- Aplicación web Laravel, Inertia y React en Render Free con PostgreSQL de
  Supabase Free en el
  entorno desplegado. SQLite sigue disponible para el desarrollo local.
- Construcción con las dependencias bloqueadas del proyecto y verificación
  automática de la aplicación antes de publicar.
- Base de producción vacía al inicio, aprovisionamiento de dos cuentas
  nuevas con credenciales propias, verificación de TMDB y
  comprobación de persistencia después de un redespliegue.
- Diagnóstico de fallos, comprobación de salud y procedimiento de recuperación.

## Alcance excluido

- Nuevas colecciones, registro público o cambios de comportamiento del MVP.
- Trasladar cuentas, películas, notas o puntuaciones del SQLite local.
- Alta de servicios, cambios de plan o despliegue durante esta especificación.
- Un dominio propio o un entorno adicional, salvo aprobación posterior.

## Actores

- Ángel y Alejandra usan la aplicación con sus cuentas individuales.
- El desarrollador autoriza la publicación y administra las cuentas y secretos.
- GitHub Actions verifica el código y Render aloja la aplicación.

## Flujo principal

1. Cada push a `dev` o a `main`, incluidos los que resultan de integrar una
   rama, activa CI en GitHub Actions para ese commit. CI ejecuta
   las pruebas con SQLite y comprueba migraciones y reglas de persistencia
   con una base PostgreSQL aislada. También compila los recursos del frontend.
2. Si CI pasa en `main`, se inicia automáticamente la publicación de ese
   commit. GitHub Actions solicita a Render el despliegue
   por SHA y comprueba que esa revisión está activa.
3. El trabajo de publicación ejecuta las migraciones de producción
   automáticamente, tras CI y antes de solicitar el despliegue.
   Render publica la revisión si pasa las comprobaciones de salud.
4. Para el primer despliegue, tras migrar el esquema, se ejecuta desde un
   equipo autorizado un seeder de producción contra Supabase por TLS. Render
   lee las dos cuentas creadas. Se verifica por HTTPS el acceso, la búsqueda
   real en TMDB y el guardado de una película.
5. Un redespliegue conserva los registros guardados y sus aportaciones.

## Flujos alternativos y errores

- Una comprobación de CI fallida impide publicar el commit.
- Si falla una migración de producción, GitHub Actions termina con error
  antes de solicitar el despliegue.
- Un despliegue fallido o que exceda el plazo se informa como fallo. Si ya hay
  una revisión operativa, se comprueba su disponibilidad y se documenta
  cómo recuperar una versión compatible con el esquema de datos.
- Un fallo de TMDB mantiene disponible el alta manual según la especificación
  005. Una incidencia de base de datos no se comunica como despliegue correcto.
- La recuperación de acceso de las dos cuentas sigue siendo manual.

## Reglas de infraestructura

- Un servicio web Docker con PHP 8.5 en Render Free. Laravel
  conecta a PostgreSQL en Supabase Free por TLS mediante el pooler compartido
  en modo sesión, con IPv4. La API de datos de Supabase no expone las tablas
  privadas de UsTwo. El plan concretará la imagen y el servidor web.
  [Docker en Render](https://render.com/docs/docker) · [Conexión PostgreSQL de Supabase](https://supabase.com/docs/guides/database/connecting-to-postgres).
- Desactivar el despliegue automático independiente de Render para
  que GitHub Actions controle el commit publicado. Serializar las publicaciones
  y rechazar revisiones obsoletas. [API de despliegues de Render](https://api-docs.render.com/reference/create-deploy).
- La creación inicial del servicio también respeta la verificación del
  commit de `main`. El plan concretará cómo coordinar el primer despliegue.
- Ejecutar las migraciones desde GitHub Actions mediante un
  acceso de migración de producción limitado al trabajo de publicación.
  La revisión anterior debe seguir funcionando durante el cambio de esquema.
  Render Free carece de comandos previos al despliegue y acceso por consola.
  [Fases del despliegue](https://render.com/docs/deploys) · [Render Free](https://render.com/docs/free).
- Producción usa secretos propios para la aplicación, la conexión y TMDB.
  GitHub conserva solo las credenciales necesarias para desplegar y migrar. Las pruebas
  de cambios no requieren acceso a secretos de producción. Los valores no se
  guardan en Git, imágenes ni logs.
- La aplicación se sirve con depuración desactivada y cookies de sesión
  seguras. PostgreSQL guarda los datos y las sesiones para conservarlos
  entre reinicios.
- El seeder de producción aprovisiona únicamente las dos cuentas con
  credenciales propias, introducidas de forma privada. Reejecutarlo conserva
  las cuentas y credenciales existentes. Queda fuera de CI/CD y se mantiene
  separado del seeder local.
- Render Free entra en reposo por inactividad y puede tardar en responder
  al reactivarse. Supabase Free puede pausar proyectos con poca actividad.
  Alojar ambos servicios en Europa cuando esté disponible en los planes
  gratuitos. Documentar la reactivación y realizar una exportación manual
  cifrada fuera de Git antes de promover a `main` cambios de esquema sobre
  una base que ya contiene datos.
  La especificación exige evitar recursos de pago y verificar los límites de
  uso de los tres servicios. [Render Free](https://render.com/docs/free) ·
  [Pausa en Supabase Free](https://supabase.com/docs/guides/platform/free-project-pausing) ·
  [Copias de seguridad de Supabase](https://supabase.com/docs/guides/platform/backups).

## Criterios de aceptación

- **CA-01**. Un push a `dev` y otro a `main` generan sendas ejecuciones de CI
  para sus commits. CI compila el frontend y pasa las pruebas con SQLite y
  PostgreSQL aislados. Un fallo impide el despliegue.
- **CA-02**. Un CI correcto en `main` inicia automáticamente la publicación
  del mismo commit. CI en `dev` y CI fallido en `main` no publican.
  El resultado registra el SHA activo y el estado final de Render.
- **CA-03**. La aplicación responde por HTTPS y pasa la comprobación de
  salud. Una sesión anónima no puede leer ni modificar Películas.
- **CA-04**. Las dos cuentas de producción pueden entrar por separado.
  Ambas ven la lista compartida, y cada una solo puede editar su valoración.
  Una segunda ejecución del seeder conserva las dos cuentas y sus credenciales.
- **CA-05**. Se busca una película en TMDB, se guarda en PostgreSQL y se
  comprueba que ambas cuentas conservan acceso al registro tras redesplegar.
- **CA-06**. Antes de publicar, CI aplica las migraciones en PostgreSQL
  aislado y comprueba unicidad, autoría, borrado de aportaciones y
  ediciones concurrentes. En `main`, un fallo de migración de producción
  impide solicitar el despliegue a Render.
- **CA-07**. Un fallo de publicación queda visible en GitHub Actions.
  La documentación describe cómo verificar la revisión activa y recuperar
  una versión compatible, cómo reactivar el servicio y cómo exportar y
  restaurar una copia cifrada fuera de Git.
- **CA-08**. El diff y la configuración de despliegue muestran que los
  secretos y las credenciales de las cuentas permanecen fuera del repositorio
  y de los artefactos y registros de ejecución. El seeder local no se
  ejecuta en producción.
- **CA-09**. Render y Supabase muestran los recursos en sus planes
  gratuitos y GitHub Actions funciona dentro de la cuota gratuita aplicable.
- **CA-10**. Una petición anónima a la API de datos de Supabase no puede
  leer ni modificar cuentas, películas, notas ni puntuaciones.

## Decisiones aprobadas

- Render Free para alojamiento, Supabase Free para PostgreSQL y GitHub
  Actions dentro del plan gratuito para CI/CD. SQLite se mantiene en local
  (DT-006 y confirmación del desarrollador sobre proveedores y coste).
- Las ramas de trabajo se integran en `dev`. `main` se reserva para despliegues.
  Cada push a `dev` o `main` activa CI, por confirmación del desarrollador.
- Si CI pasa en `main`, el mismo commit se publica automáticamente en Render.
- La publicación conserva el alcance funcional del MVP de Películas.
- Supabase empieza con una base limpia y dos cuentas nuevas de producción.
- Docker con PHP 8.5, conexión TLS por el pooler de Supabase y sesiones en
  PostgreSQL. Migraciones automáticas desde GitHub Actions con credencial
  separada antes de publicar. Cuentas iniciales mediante seeder de producción
  ejecutado desde un equipo autorizado.
- Región europea cuando los planes gratuitos la permitan, reactivación
  documentada y exportaciones manuales cifradas antes de cambios de esquema.
- El desarrollador aprobó la especificación 007 el 2026-11-09.
- El desarrollador aprobó usar `sslmode=require` en las conexiones a
  Supabase desde GitHub Actions, Render y el equipo autorizado, sin
  depender de un archivo de CA. Se mantiene SSL obligatorio en Supabase.

## Decisiones pendientes

- Ninguna para la revisión de la especificación. El plan concretará la
  imagen, la región disponible y el procedimiento de aprovisionamiento.
