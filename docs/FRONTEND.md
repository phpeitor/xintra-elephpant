# Reglas de Frontend

- No mezclar CSS ni JS dentro de los archivos PHP/HTML; usar siempre `assets/css/` y `assets/js/`.
- Mantener la UI consistente con el template actual y evitar cambios de estilo fuera del sistema visual existente.
- Preferir componentes reutilizables y lógica centralizada antes que duplicar comportamiento en varias vistas.
- Evitar scripts inline salvo que no exista alternativa razonable.
- Cuando se agregue interacción nueva, validar también el estado vacío, el error y la edición.
- Cuidar la accesibilidad básica: labels, estados deshabilitados, mensajes visibles y feedback claro.
- Si una pantalla usa datos dinámicos, asegurar que el refresco visual quede sincronizado con el estado interno.
- Mantener el código JS pequeño y enfocado por módulo o pantalla.
