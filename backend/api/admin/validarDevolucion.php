<?php
session_start();

header("Content-Type: application/json; charset=utf-8");
header("Access-Control-Allow-Origin: http://localhost:5173");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Credentials: true");

if ($_SERVER["REQUEST_METHOD"] === "OPTIONS") {
    http_response_code(200);
    exit;
}

if (($_SESSION["usuario_rol"] ?? "") !== "admin") {
    echo json_encode([
        "success" => false,
        "error" => "Solo un administrador puede validar devoluciones"
    ]);
    exit;
}

require_once __DIR__ . "/../config/conexion.php";

$idMovimiento = intval($_POST["id_movimiento"] ?? 0);
$idMaterial = intval($_POST["id_material"] ?? 0);
$idValidador = intval($_SESSION["usuario_id"] ?? 0);

$sqlMovimiento = "
    SELECT tipo_movimiento
    FROM movimiento_material
    WHERE id_movimiento = ?
      AND id_material = ?";

$stmtMovimiento = $conn->prepare($sqlMovimiento);
$stmtMovimiento->bind_param("ii", $idMovimiento, $idMaterial);
$stmtMovimiento->execute();
$movimiento = $stmtMovimiento->get_result()->fetch_assoc();

    if (!$movimiento || $movimiento["tipo_movimiento"] !== "devolucion") {
        echo json_encode([
            "success" => false,
            "error" => "No es una devolucion pendiente"
        ]);
        exit;
    }

$sqlUpdate = "
    UPDATE movimiento_material
    SET tipo_movimiento = 'devuelto'
    WHERE id_material = ?
    AND id_movimiento = ?";

$stmtUpdate = $conn->prepare($sqlUpdate);
$stmtUpdate->bind_param("ii",$idMaterial, $idMovimiento);
$stmtUpdate->execute();

$sql = "UPDATE material SET estado 
    = 'devuelto' WHERE id_material = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $idMaterial);
$stmt->execute();

$sqlValidador =
    "INSERT INTO devolucion (id_movimiento, id_validador, id_material, fecha_devolucion)
    VALUES (?, ?, ?, NOW())";
$stmtDev = $conn->prepare($sqlValidador);
$stmtDev->bind_param("iii", $idMovimiento, $idValidador, $idMaterial);
$stmtDev->execute();
$stmtDev->close();
$stmt->close();

echo json_encode([
    "success" => true,
    "message" => "Devolucion validada"
]);
