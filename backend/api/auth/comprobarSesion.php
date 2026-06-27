<?php
session_start();

header("Content-Type: application/json");
header('Access-Control-Allow-Origin: http://localhost:5173');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Credentials: true');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit;
}

if (isset($_SESSION['usuario_id'])) {
    echo json_encode([
        "logueado" => true,
        "usuario" => [
            "id" => $_SESSION['usuario_id'],
            "nombre" => $_SESSION['usuario_nombre'],
            "email" => $_SESSION['usuario_email'],
            "rol" => $_SESSION['usuario_rol']
        ]
    ]);

} else {
    echo json_encode([
        "logueado" => false
    ]);
}