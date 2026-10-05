<?php
require_once __DIR__ . '/../database/conexion.php';

class Permisos
{
    public const CATALOGO = [
        'inicio' => 'Inicio',
        'usuarios' => 'Usuarios',
        'asistencia' => 'Asistencia',
        'clientes' => 'Clientes',
        'categorias' => 'Categoría',
        'items' => 'Items',
        'tickets' => 'Tickets',
        'reporte' => 'Reporte',
        'compras' => 'Proveedores',
        'sucursales' => 'Sucursales',
    ];

    private PDO $conn;
    private ?array $permisosCache = null;
    private ?int $permisosCacheUsuario = null;

    public function __construct()
    {
        $this->conn = (new Conexion())->conectar();
    }

    public function permitidos(int $idUsuario): array
    {
        if ($this->permisosCache !== null && $this->permisosCacheUsuario === $idUsuario) return $this->permisosCache;
        $stmt = $this->conn->prepare('SELECT 1 FROM usuario_permisos_configurados WHERE id_usuario = :id LIMIT 1');
        $stmt->execute([':id' => $idUsuario]);
        if (!$stmt->fetchColumn()) {
            $this->permisosCacheUsuario = $idUsuario;
            return $this->permisosCache = array_keys(self::CATALOGO);
        }

        $stmt = $this->conn->prepare('SELECT permiso FROM usuario_permisos WHERE id_usuario = :id');
        $stmt->execute([':id' => $idUsuario]);
        $this->permisosCacheUsuario = $idUsuario;
        return $this->permisosCache = $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    public function tiene(int $idUsuario, string $permiso): bool
    {
        return in_array($permiso, $this->permitidos($idUsuario), true);
    }

    public function esAdminOCargoUno(int $idUsuario): bool
    {
        if ($idUsuario === 1) return true;

        $stmt = $this->conn->prepare('SELECT USUARIO, CARGO FROM personal WHERE IDPERSONAL = :id LIMIT 1');
        $stmt->execute([':id' => $idUsuario]);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
        return $usuario
            && (strcasecmp((string)$usuario['USUARIO'], 'admin') === 0 || (int)$usuario['CARGO'] === 1);
    }

    public function asignar(int $idUsuario, array $permisos): void
    {
        $permisos = array_values(array_intersect(array_keys(self::CATALOGO), $permisos));
        $this->conn->beginTransaction();
        try {
            $stmt = $this->conn->prepare('INSERT INTO usuario_permisos_configurados (id_usuario) VALUES (:id) ON DUPLICATE KEY UPDATE id_usuario = VALUES(id_usuario)');
            $stmt->execute([':id' => $idUsuario]);
            $stmt = $this->conn->prepare('DELETE FROM usuario_permisos WHERE id_usuario = :id');
            $stmt->execute([':id' => $idUsuario]);
            $stmt = $this->conn->prepare('INSERT INTO usuario_permisos (id_usuario, permiso) VALUES (:id, :permiso)');
            foreach ($permisos as $permiso) {
                $stmt->execute([':id' => $idUsuario, ':permiso' => $permiso]);
            }
            $this->conn->commit();
            $this->permisosCache = null;
            $this->permisosCacheUsuario = null;
        } catch (Throwable $e) {
            if ($this->conn->inTransaction()) $this->conn->rollBack();
            throw $e;
        }
    }

    public static function permisoRuta(string $ruta): ?string
    {
        $ruta = strtolower(basename($ruta));
        $rutas = [
            'home.php' => 'inicio',
            'usuarios.php' => 'usuarios', 'add_usuario.php' => 'usuarios', 'upd_usuario.php' => 'usuarios', 'horario.php' => 'usuarios',
            'asistencia.php' => 'asistencia',
            'clientes.php' => 'clientes', 'add_cliente.php' => 'clientes', 'upd_cliente.php' => 'clientes',
            'categorias.php' => 'categorias', 'add_categoria.php' => 'categorias', 'upd_categoria.php' => 'categorias',
            'items.php' => 'items', 'add_item.php' => 'items', 'upd_item.php' => 'items', 'stk_item.php' => 'items',
            'tickets.php' => 'tickets', 'add_ticket.php' => 'tickets', 'upd_ticket.php' => 'tickets',
            'reporte.php' => 'reporte', 'sucursal.php' => 'sucursales',
            'table_usuario.php' => 'usuarios', 'catalogos_usuario.php' => 'usuarios', 'asignar_sucursal_cargo.php' => 'usuarios',
            'permisos_usuario.php' => 'usuarios', 'get_usuario.php' => 'usuarios', 'delete_usuario.php' => 'usuarios',
            'asistencia.php' => 'asistencia',
            'table_cliente.php' => 'clientes', 'get_cliente.php' => 'clientes', 'add_cliente.php' => 'clientes', 'upd_cliente.php' => 'clientes',
            'table_categoria.php' => 'categorias', 'get_categoria.php' => 'categorias', 'get_categoria_hash.php' => 'categorias', 'add_categoria.php' => 'categorias', 'upd_categoria.php' => 'categorias', 'delete_categoria.php' => 'categorias',
            'table_item.php' => 'items', 'get_item.php' => 'items', 'add_item.php' => 'items', 'upd_item.php' => 'items', 'delete_item.php' => 'items', 'add_stock.php' => 'items', 'stk_item.php' => 'items',
            'table_ticket.php' => 'tickets', 'get_ticket.php' => 'tickets', 'add_ticket.php' => 'tickets', 'upd_ticket.php' => 'tickets', 'validar_promocode.php' => 'tickets', 'emitir_comprobante.php' => 'tickets', 'tkt_pdf.php' => 'tickets',
            'get_item_categoria.php' => 'items',
            'api.php' => 'clientes', 'api_ruc.php' => 'clientes',
            'table_sucursal.php' => 'sucursales', 'add_sucursal.php' => 'sucursales', 'upd_sucursal.php' => 'sucursales', 'estado_sucursal.php' => 'sucursales', 'aumentar_cuota_sucursal.php' => 'sucursales', 'historial_cuota_sucursal.php' => 'sucursales', 'configurar_nubefact_sucursal.php' => 'sucursales',
            'apx_contadores.php' => 'inicio', 'apx_diario.php' => 'reporte', 'apx_mensual.php' => 'reporte', 'apx_cliente.php' => 'reporte', 'apx_item.php' => 'reporte', 'apx_usuario.php' => 'reporte', 'apx_valida.php' => 'tickets', 'excel.php' => 'reporte',
        ];
        return $rutas[$ruta] ?? null;
    }
}
