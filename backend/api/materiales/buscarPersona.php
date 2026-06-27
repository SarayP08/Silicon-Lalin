<?php
header("Access-Control-Allow-Credentials: true");
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: http://localhost:5173");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit;
}

$nif = trim($_GET["nif"] ?? "");

require_once __DIR__ . "/../config/conexion.php";

$stmt = $conn->prepare("SELECT * FROM persona WHERE nif = ?");
$stmt->bind_param("s", $nif);
$stmt->execute();
$result = $stmt->get_result();

if ($fila = $result->fetch_assoc()) {
    echo json_encode([
        "existe" => true,
        "id_persona" => $fila["id_persona"],
        "persona" => [
            "nif" => $fila["nif"],
            "nombre_persona" => $fila["nombre"],
            "apellidos" => $fila["apellidos"],
            "correo" => $fila["correo"],
            "telefono" => $fila["telefono"]]
    ]);
} else {
    echo json_encode([
        "existe" => false
    ]);
}
$stmt->close();
$conn->close();