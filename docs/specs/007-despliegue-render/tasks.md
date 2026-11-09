---
spec: "007"
status: in_progress
---

# Tareas — 007

- [ ] **T-01 — Preparar la imagen y el entorno de ejecución**
  - Criterios: CA-03, CA-08, CA-09.
  - Evidencia esperada: imagen PHP 8.5 y Vite construida desde locks, servicio en el puerto esperado, raíz pública, salud y exclusión de secretos.
- [ ] **T-02 — Preparar PostgreSQL, sesiones y aprovisionamiento**
  - Criterios: CA-03, CA-04, CA-06, CA-08, CA-10.
  - Evidencia esperada: migraciones, sesiones y dos cuentas idempotentes en PostgreSQL aislado, permisos de conexión probados y seeder local separado.
- [ ] **T-03 — Configurar CI para dev y main**
  - Criterios: CA-01, CA-06, CA-08, CA-09.
  - Evidencia esperada: build y pruebas SQLite y PostgreSQL con configuraciones independientes y sin acceso a producción.
- [ ] **T-04 — Configurar la publicación por SHA**
  - Criterios: CA-01, CA-02, CA-06, CA-07, CA-08.
  - Evidencia esperada: condiciones de rama y CI, migración previa, comprobación de SHA, espera de estado final y pruebas de fallo sin publicar.
- [ ] **T-05 — Aprovisionar los servicios gratuitos y publicar la primera revisión**
  - Criterios: CA-02, CA-03, CA-06, CA-08, CA-09, CA-10.
  - Evidencia esperada: Supabase limpio y protegido, planes gratuitos, migración de la revisión comprobada antes del alta, y mismo SHA activo en Render.
- [ ] **T-06 — Verificar el MVP desplegado y documentar recuperación**
  - Criterios: CA-01–CA-10.
  - Evidencia esperada: HTTPS, seeder ejecutado dos veces en Supabase con las mismas cuentas y credenciales conservadas, TMDB real, persistencia tras redespliegue, recuperación documentada y restauración en base aislada, con auditoría final.

## Evidencia local

- Vite compiló los recursos. PHPUnit en PostgreSQL 17.9 temporal pasó
  12 pruebas y 209 aserciones, incluidas las sesiones en base de datos
  y la repetición del seeder. SQLite pasó 11 pruebas con 195 aserciones
  y omitió el caso exclusivo de PostgreSQL.
- Se cifró un volcado temporal, se descifró y se restauró en otra base
  aislada, donde se recuperó la película de prueba.
- El script de publicación pasó nueve escenarios simulados. Se comprobaron
  rama incorrecta, ID ausente, SHA obsoleto, arranque inicial, migración
  fallida, despliegue fallido, timeout, SHA incorrecto y publicación correcta.
- Pendiente observar la imagen y su arranque en GitHub Actions, los planes
  y el despliegue en Render/Supabase y la prueba real de producción. Docker
  no está disponible en el entorno local. Las cuatro etiquetas de imagen
  se comprobaron en el registro público.
