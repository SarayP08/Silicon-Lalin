<?php
header("Access-Control-Allow-Credentials: true");
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: http://localhost:5173");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit;
}

session_start();

require_once __DIR__ . '/../config/conexion.php';

$id = intval($_GET["id_material"] ?? 0);

if ($id <= 0) {
    echo json_encode([
        "success" => false,
        "error" => "ID inválido"
    ]);
    exit;
}

$sql = "SELECT * FROM material WHERE id_material = ?";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "error" => true,
        "message" => $conn->error
    ]);
    exit;
}

$stmt->bind_param("i", $id);

$stmt->execute();

$result = $stmt->get_result();

$material = $result->fetch_assoc();

$stmt->close();

if (!$material) {

    echo json_encode([
        "success" => false,
        "error" => "Mercancia no encontrado"
    ]);

    exit;
}
$conn->close();
echo json_encode($material);
