---
spec: "007"
status: approved
---

# Plan técnico — 007

## Resumen

Preparar el MVP existente para Render Free con PHP 8.5 en Docker y
PostgreSQL de Supabase Free. GitHub Actions verifica cada push a `dev`
y `main`; solo una revisión comprobada de `main` pasa a producción.

## Componentes afectados

- Dockerfile y exclusiones de contexto para construir desde los archivos
  de bloqueo. Imagen Linux PHP 8.5 con Apache, raíz pública en `public/`,
  extensión PDO PostgreSQL y certificados de confianza. Compilar assets
  Vite en una etapa Node compatible y copiar solo los artefactos necesarios.
- Configuración Laravel para PostgreSQL y sesiones, migración de sesiones,
  comprobación de disponibilidad y seeder de producción independiente.
- Flujo de GitHub Actions, configuración de Render y Supabase, e
  instrucciones operativas en README y decisiones técnicas.

## Contratos, datos e interfaces

- Mantener SQLite para desarrollo y pruebas locales. Añadir configuración
  PHPUnit explícita para PostgreSQL aislado en CI: el XML actual fuerza
  SQLite en memoria. Ejecutar la misma suite contra ambos motores con
  bases desechables, sin secretos de producción. Verificar las migraciones
  y la comparación de valores entre motores, incluido orden, tipos, claves
  únicas y cascadas.
- Usar el pooler de sesión IPv4 de Supabase por TLS con `sslmode=require`
  en GitHub Actions y Render. Supabase exige SSL. El cliente no requiere
  el archivo de CA ni verifica el nombre del servidor. Separar el rol de
  ejecución con permisos de datos del rol que aplica migraciones. Probar
  los permisos de ambos y configurar los permisos de tablas nuevas.
  Deshabilitar la Data API del proyecto de UsTwo y comprobar que las
  peticiones anónimas tampoco acceden a las tablas.
- Render conserva `APP_KEY`, `DB_URL` y `TMDB_TOKEN` como secretos de
  ejecución, con `APP_ENV=production`, depuración desactivada, cookies
  seguras y sesiones en PostgreSQL. Configurar HTTPS tras el proxy y usar
  el puerto de Render. La comprobación de salud verifica disponibilidad
  de PostgreSQL sin devolver configuración, errores internos ni datos.
- El seeder de producción se invoca manualmente por TLS desde un equipo
  autorizado. Recibe las credenciales de forma privada, crea solo las
  dos cuentas previstas y conserva cuentas y hashes al repetirse.
  Mantener separado el seeder de SQLite local y la recuperación manual.
- GitHub Actions compila Vite y ejecuta las pruebas tras cada push a
  `dev` y `main`. El trabajo de publicación usa permisos mínimos,
  secretos separados de CI y una cola serial sin cancelar una migración
  activa. Verifica que el SHA de la ejecución sigue siendo la punta de
  `main` antes de tocar producción y vuelve a comprobarlo antes de
  pedir el despliegue.
- Migrar con el código del SHA comprobado. Si falla, detener antes de
  contactar a Render. Enviar `commitId` a Render con autodespliegue
  desactivado y consultar el ID de despliegue hasta estado final. Verificar
  SHA activo y HTTPS. Un timeout o SHA distinto deja el trabajo en error.
  [API de despliegue de Render](https://api-docs.render.com/reference/create-deploy).

## Fases de implementación

1. Preparar imagen, configuración, migración de sesiones y CI. Verificar
   la imagen local y CI en `dev` antes de promover cambios.
2. Preparar el seeder de producción y los pasos de migración y
   publicación. Probar el seeder y los fallos de publicación con servicios
   aislados o respuestas controladas.
3. Con autorización operativa, crear Supabase Free vacío, configurar roles,
   TLS y Data API y preparar secretos. Promover el primer SHA a `main` y
   mantener esa rama estable hasta completar el arranque. Esperar CI
   correcto. El trabajo protegido de ese mismo SHA aplica las migraciones
   incluso si todavía falta el ID de Render y registra «publicación
   pendiente de alta», sin declarar éxito de despliegue. Esta excepción
   requiere activar explícitamente el modo de arranque inicial. Fuera
   de ese modo, la falta del ID de Render detiene el trabajo antes de
   conectar con Supabase.
4. Crear el servicio Render Free enlazado a ese `main` verificado, con
   autodespliegue desactivado desde el alta y los secretos preparados.
   Render lanza una primera compilación al crear el servicio. Comprobar
   su SHA y registrar el ID para reejecutar la publicación del mismo
   commit desde GitHub Actions. Cualquier cambio de la punta invalida
   el arranque y exige repetir CI sobre la revisión que se vaya a publicar.
   Desactivar el modo de arranque al registrar el ID.
   [Creación de un servicio web](https://render.com/docs/web-services).
5. Aprovisionar las cuentas, verificar HTTPS, privacidad y TMDB, guardar
   una película de prueba y comprobar persistencia en un redespliegue.
   Registrar los resultados antes de cerrar la especificación.

## Estrategia de pruebas y criterios

| Criterio | Verificación prevista |
|---|---|
| CA-01 | Ejecuciones en `dev` y `main` para sus SHAs, Vite y PHPUnit en dos motores aislados. |
| CA-02 | `dev` y un CI fallido no publican. En `main`, cotejar el SHA comprobado con el despliegue inicial y el SHA activo registrado por Actions. |
| CA-03 | HTTPS, salud con y sin PostgreSQL y acceso anónimo rechazado en lectura y escritura. |
| CA-04 | Ejecutar dos veces el seeder en PostgreSQL aislado y comprobar credenciales conservadas. Verificar ambas sesiones y autoría en producción. |
| CA-05 | Buscar en TMDB real, guardar y comprobar el registro y aportaciones con ambas cuentas tras redesplegar. |
| CA-06 | Migraciones desde una base limpia en PostgreSQL, unicidad, autoría, cascadas, edición concurrente y bloqueo del deploy si falla la migración. |
| CA-07 | Simular fallo y timeout. Documentar revisión activa, reactivación y recuperación. Probar una restauración cifrada en base aislada. |
| CA-08 | Revisar contexto, imagen, logs y secretos por entorno. Confirmar que el seeder local no corre en producción. |
| CA-09 | Comprobar planes gratuitos y cuotas en los paneles antes de publicar. Detenerse si el uso previsto exige pago. |
| CA-10 | Verificar Data API desactivada y rechazo de acceso anónimo a tablas de UsTwo. |

## Documentación

- Actualizar README con ejecución local, CI, arranque inicial, seeder,
  recuperación de cuentas y verificación de producción. Registrar en
  decisiones técnicas la configuración efectivamente aprobada.
- Incluir procedimiento de exportación cifrada fuera de Git antes de
  promover cambios de esquema y restauración controlada.

## Despliegue y compatibilidad

- Render Free y Supabase Free en regiones europeas disponibles. Usar la
  URL HTTPS de Render. Verificar cuotas de GitHub Actions con runners Linux
  estándar y evitar recursos facturables. Documentar la revisión periódica
  de cuotas y límites durante la operación.
- Mantener el esquema compatible con la versión activa durante una
  publicación. El rollback de aplicación conserva el esquema y los datos.
  Una restauración de datos requiere una operación explícita.

## Riesgos y medidas

- Arranque inicial de Render al crear el servicio: exigir CI y migraciones
  del SHA previsto antes de crear el servicio y comprobarlo después.
- Render en reposo o Supabase pausado: registrar señales, reactivación y
  tiempos de espera. No utilizar sondeos permanentes para impedir la pausa.
- Las comprobaciones externas dependen de cuentas, permisos y secretos
  facilitados por el desarrollador. Dejar constancia si falta alguno.
