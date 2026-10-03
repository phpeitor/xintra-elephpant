# Reglas de Backend

- Proyecto: Xintra Elephpant, sistema PHP/MySQL de usuarios, clientes, catálogo, stock, tickets, horarios, asistencia y reportes por sucursal.
- Usar controladores en `controller/` y modelos en `model/`.
- Validar siempre `REQUEST_METHOD` y los datos de entrada antes de procesar.
- Responder en JSON cuando el endpoint sea consumido por JavaScript.
- Mantener las consultas SQL con prepared statements para evitar inyección.
- Cargar configuración desde `.env` mediante `config/bootstrap.php`.
- Preservar transacciones cuando una operación escriba varias tablas.
- No crear sesión ni registrar acciones si la autenticación o el estado del usuario no son válidos.
- Obtener la sucursal desde la sesión autenticada y limitar en backend todas las lecturas y escrituras a esa sucursal.
- Validar que las claves/hash recibidos y todas sus relaciones pertenezcan a la sucursal activa.
- Normalizar la salida de los endpoints para que el frontend no dependa de formatos cambiantes.
- Manejar errores con mensajes claros y códigos HTTP correctos.
- Consultar `DATABASE_STANDARDS.md` antes de modificar tablas, relaciones o consultas de alto volumen.
