---
spec: "005"
status: approved
---

# Plan técnico — 005

## Resumen

Implementar y verificar en local el cuaderno privado de películas vistas de
la especificación aprobada. Usar el stack instalado, SQLite en local y en
las pruebas, y el cliente HTTP de Laravel para consultar TMDB.

## Componentes afectados

- Laravel: rutas web, autenticación por sesión, validación, persistencia y
  consultas a TMDB desde el servidor.
- Inertia y React: acceso, panel, lista, detalle, formularios y créditos.
  Cada componente JSX usa su CSS Module. Las páginas principales importan
  `global.css` según la convención aprobada.
- PHPUnit: pruebas funcionales, de permisos, de persistencia y de catálogo.

## Contratos, datos e interfaces

- Guardar las dos cuentas, las películas y las aportaciones individuales.
  Crear migraciones para usuarios, registros y valoraciones. Mantener una
  aportación por película y cuenta, e identificador TMDB único cuando exista.
  El borrado definitivo elimina las aportaciones en la misma operación.
- Usar autenticación de sesión de Laravel con contraseñas almacenadas mediante
  hash. Documentar la creación previa y el restablecimiento manual con
  herramientas locales y sin credenciales versionadas. Proteger en servidor
  tanto las páginas como las acciones, incluido el buscador de TMDB.
- Validar título y fecha de visionado, año cuando corresponda y puntuaciones
  enteras de 0 a 100. Derivar la autoría de la sesión. Guardar solo los
  campos comunes modificados para conservar cambios concurrentes en otros
  campos. Revalidar en el servidor el identificador, los metadatos y la
  elegibilidad de la candidata al confirmar un vínculo. Rechazar un segundo
  vínculo para el mismo identificador o sobre una candidata ya vinculada.
  Una edición obsoleta no recrea una entrada borrada.
- Mantener el token TMDB en la configuración privada del servidor. Consultar
  películas por título y presentar título y año si consta. Distinguir error
  y ausencia de coincidencia, y ofrecer el alta manual en ambos casos.
  Resolver candidatos según CA-13 y CA-21 a CA-25. Vincular únicamente al
  confirmar la entrada elegida y conservar fecha y aportaciones.
- Mostrar en la lista solo título y ambas puntuaciones, del visionado más
  reciente al más antiguo. Mostrar el resto en el detalle. Incluir estado
  vacío, validación junto a los campos y recuperación ante fallo de guardado.
  Verificar navegación por teclado, foco visible y recorrido móvil sin
  desplazamiento horizontal.

## Fases de implementación

1. Preparar SQLite, migraciones y cuentas locales. Proteger las rutas.
2. Implementar panel, lista, detalle, alta manual, edición y borrado.
3. Integrar búsqueda TMDB, confirmaciones, alternativas y créditos.
4. Ejecutar pruebas y compilación, comprobar el recorrido en navegador,
   actualizar README y someter el resultado a auditoría final.

## Estrategia de pruebas y criterios

| Criterios | Verificación prevista |
|---|---|
| CA-01, CA-04, CA-08, CA-16 | Dos sesiones independientes, acceso directo sin permiso, inicio y cierre de sesión. |
| CA-02, CA-03, CA-05–CA-07 | Lista vacía, datos compartidos, nueva sesión y fallo de guardado con datos conservados. |
| CA-09–CA-13, CA-23 | Revisión de fecha, límites de puntuación, permisos individuales, validación y coincidencias manuales. |
| CA-14, CA-15 | Alta, edición y borrado por cada cuenta. Confirmar y cancelar, con verificación de las aportaciones. |
| CA-17, CA-26 | Dos ediciones desde el mismo estado en el mismo campo y en campos distintos. |
| CA-18–CA-22, CA-25 | Respuestas controladas de TMDB, duplicados, varios candidatos, confirmación, alta manual y créditos. Comprobación real local con token de desarrollo. |
| CA-24 y requisitos de interfaz | Recorrido en navegador, orden y contenido del resumen y detalle, teclado, foco, móvil y validación visible. |

## Documentación

- Actualizar README con preparación de SQLite, migraciones, arranque,
  creación y recuperación manual de las cuentas y configuración del token.
  Mantener el fichero SQLite, los secretos y los datos personales fuera de Git.

## Despliegue y compatibilidad

- El alcance termina con la verificación local. Render, GitHub Actions y
  la comprobación con PostgreSQL corresponden a la fase posterior.
- Usar migraciones y consultas de Laravel sin SQL específico de SQLite.
  La fase de despliegue deberá comprobar migraciones, restricciones, borrados
  y ediciones simultáneas con PostgreSQL.

## Riesgos y medidas

- Catálogo inaccesible o incompleto: alta manual y pruebas controladas de
  errores. Para la comprobación real se necesita un token local válido.
- Vinculación y borrado con solicitudes concurrentes: comprobar permisos y
  estado actual al guardar, usar transacciones cuando la operación afecte a
  varios datos y probar que una edición tardía no recrea la entrada.
