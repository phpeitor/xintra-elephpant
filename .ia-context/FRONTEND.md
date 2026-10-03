# Reglas de Frontend

- Proyecto: Xintra Elephpant, vistas PHP con template Xintra, Tailwind, JavaScript y componentes/libs locales.
- No incluir bloques `<style>`, bloques `<script>` de código ni atributos de estilo/eventos inline en archivos PHP/HTML; usar `assets/css/` y `assets/js/`.
- Mantener la UI consistente con el template actual y evitar cambios de estilo fuera del sistema visual existente.
- Preferir componentes reutilizables y lógica centralizada antes que duplicar comportamiento en varias vistas.
- Pasar valores dinámicos del servidor con atributos `data-*` y leerlos desde JavaScript externo; escapar la salida HTML.
- Cuando se agregue interacción nueva, validar también el estado vacío, el error y la edición.
- Cuidar la accesibilidad básica: labels, estados deshabilitados, mensajes visibles y feedback claro.
- Si una pantalla usa datos dinámicos, asegurar que el refresco visual quede sincronizado con el estado interno.
- Mantener el código JS pequeño y enfocado por módulo o pantalla.
- Cargar los assets necesarios en la vista y evitar cambios globales de estilos para corregir una pantalla concreta.
