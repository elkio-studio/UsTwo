---
spec: "025"
status: completed
---

# Tareas — Política de edición de metadatos externos

- [x] **T-01 — Contrastar alcance e inventario aprobados**
  - Criterios: CA-01, CA-02, CA-03.
  - Revisar specs 022 y 024 y separar política futura de comportamiento actual.
  - Evidencia esperada: matriz de criterios revisada sin cambios de código.

- [x] **T-02 — Incorporar la decisión en la documentación técnica**
  - Criterios: CA-01, CA-02, CA-03, CA-04.
  - Actualizar `docs/decisiones-tecnicas.md` con la política aprobada,
    sus límites y enlace al inventario de la spec 025.
  - Evidencia esperada: una entrada concisa, sin duplicar el inventario
    ni presentar como implementadas las reglas futuras.

- [x] **T-03 — Verificar reglas, casos y enlaces**
  - Criterios: CA-01, CA-02, CA-03, CA-04.
  - Comprobar los casos del plan, enlaces y diff exclusivamente documental;
    realizar la revisión final del proyecto.
  - Evidencia esperada: resultados registrados, sin modificar bases de
    datos, código, configuración, migraciones ni pruebas.

- [x] **T-04 — Registrar el cierre documental**
  - Criterios: CA-04.
  - Tras verificación y solicitud de cierre, anotar aprobación, evidencias,
    horario y esfuerzo para GitHub Projects; señalar publicación pendiente.
  - Evidencia esperada: spec y tareas actualizadas, sin commit ni publicación
    hasta recibir autorización específica.
