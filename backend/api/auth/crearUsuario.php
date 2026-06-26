<?php
header("Content-Type: application/json; charset=utf-8");
header('Access-Control-Allow-Origin: http://localhost:5173');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Credentials: true');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit;
}

REQUIRE_ONCE __DIR__ . '/../config/conexion.php';

$rol = "usuario";
$nombre = $_POST['nombre'];
$apellidos = $_POST['apellidos'];
$email = $_POST['email'];
$passwordPlana = $_POST['password'];

$passwordHasheada = password_hash($passwordPlana, PASSWORD_DEFAULT);

$sql = "INSERT INTO usuario (nombre, apellidos, email, password, rol) 
        VALUES (?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);

    if (!$stmt) {
        echo json_encode([
            "success" => false,
            "error" => $conn->error
        ]);
        exit;
    }
$stmt->bind_param(
    "sssss",
    $nombre,
    $apellidos,
    $email,
    $passwordHasheada,
    $rol
);
    if ($stmt->execute()) {
        echo json_encode([
            "success" => true
        ]);
    } else {
        if ($conn->errno == 1062) {
            echo json_encode([
                "success" => false,
                "error" => "El email ya existe"
            ]);
    } else {
        echo json_encode([
            "success" => false,
            "error" => $stmt->error
        ]);
    }
}
    $stmt->close();
    $conn->close();