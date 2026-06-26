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

$esAdmin = ($_SESSION["usuario_rol"] ?? "") === "admin";

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
exit;
}

require_once __DIR__ . '/../config/conexion.php';

$result = $conn->query("SELECT * FROM material");

$materiales = [];

while ($row = $result->fetch_assoc()) {
    $materiales[] = $row;
}

echo json_encode($materiales);