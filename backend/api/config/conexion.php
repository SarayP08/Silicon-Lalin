<?php
$host = "localhost";
$usuario = "root";
$pass = "";
$db = "material_deza";

$conn = new mysqli($host, $usuario, $pass, $db);

if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode([
        "ok" => false,
        "message" => "Error de conexión con la base de datos"
    ]);
    exit;
}
$conn->set_charset("utf8mb4");