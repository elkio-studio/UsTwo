---
spec: "027"
status: completed
---

# Tareas — Proteger aportaciones personales

- [x] **T-01 — Revisar asociaciones y ampliar pruebas de protección**
  - Criterios: CA-01, CA-02, CA-03, CA-04, CA-05.
  - Revisar restricciones y cobertura; completar pruebas existentes para
    dos autores, tipos de ficha, cinco categorías, aislamiento y manipulación.
  - Evidencia: asociaciones verificadas y matriz cubierta sin migraciones
    ni cambios de reglas de borrado.

- [x] **T-02 — Conservar la valoración ante fallos de guardado**
  - Criterios: CA-02, CA-04, CA-05.
  - Añadir manejo de QueryException al guardado de valoración de
    CollectionController, reutilizando el patrón de Lecturas.
  - Evidencia: error save y datos escritos retenidos; aportación anterior
    y aportaciones ajenas intactas ante fallo simulado.

- [x] **T-03 — Verificar independencia respecto a los metadatos**
  - Criterios: CA-01, CA-02, CA-03, CA-04.
  - Probar cambios compartidos y vinculación TMDB con dos autores y
    campos personales manipulados. Revisar selección de valoración propia
    en detalles y componentes compartidos.
  - Evidencia: solo se actualiza la aportación propia desde su flujo;
    metadatos y vinculaciones conservan autores, notas y puntuaciones.

- [x] **T-04 — Ejecutar controles y comprobaciones de interfaz**
  - Criterios: CA-01, CA-02, CA-03, CA-04, CA-05, CA-06.
  - PHPUnit con SQLite, build, diff y comprobaciones manuales autorizadas
    de móvil, teclado, errores y retornos a listados. Auditoría final.
  - Evidencia: comandos y resultados, atribución de pruebas de Ángel y
    pendientes explícitos si falta una comprobación.

- [x] **T-05 — Registrar el cierre y esfuerzo**
  - Criterios: CA-01, CA-02, CA-03, CA-04, CA-05, CA-06.
  - Tras la revisión final, actualizar estados, evidencias, horario y
    esfuerzo para GitHub Projects y pendientes operativos en MEMORY.md.
  - Evidencia: cierre trazable sin commits ni publicación no autorizados.
