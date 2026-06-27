<?php
session_start();
header("Content-Type: application/json; charset=utf-8");
header('Access-Control-Allow-Origin: http://localhost:5173');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Credentials: true');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit;
}

require_once __DIR__ . '/../config/conexion.php';

$idMaterial = (int)($_GET["id_material"] ?? 0);

$sql = "SELECT 
        mm.id_movimiento,
        mm.id_persona,
        mm.id_origen,
        mm.id_destino,
        mm.fecha_movimiento AS fecha_movimiento,
        mm.tipo_movimiento AS tipo_movimiento, 
        
        u.tipo AS tipo_origen,
        u.CP AS cp_origen,
        u.provincia AS provincia_origen,
        u.direccion AS direccion_origen, 
        
        d.tipo AS tipo_destino,
        d.CP AS cp_destino,
        d.provincia AS provincia_destino,
        d.direccion AS direccion_destino, 
        
        p.nombre AS nombre,
        p.apellidos AS apellidos,
        p.nif AS nif,
        p.correo AS correo,
        p.telefono AS telefono, 

        val.email AS email_validador,
        dev.fecha_devolucion AS fecha_devolucion_validada

        FROM movimiento_material mm 

        LEFT JOIN persona p 
            ON mm.id_persona = p.id_persona

        LEFT JOIN material m 
            ON mm.id_material = m.id_material

        LEFT JOIN ubicacion u 
            ON mm.id_origen = u.id_ubicacion

        LEFT JOIN ubicacion d 
            ON mm.id_destino = d.id_ubicacion
        
        LEFT JOIN devolucion dev
        ON dev.id_movimiento = mm.id_movimiento

        LEFT JOIN usuario val
        ON dev.id_validador = val.id_usuario

        WHERE mm.id_material = ?

        ORDER BY mm.fecha_movimiento DESC
        ";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $idMaterial);
$stmt->execute();
$result = $stmt->get_result();
$movimientos = [];
while ($fila = $result->fetch_assoc()) {
    $movimientos[] = $fila;
}
echo json_encode($movimientos);
