<?php

header("Access-Control-Allow-Credentials: true");
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: http://localhost:5173");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER["REQUEST_METHOD"] === "OPTIONS") {
    http_response_code(200);
    exit;
}

require_once __DIR__ . "/../config/conexion.php";

$codigo = trim($_POST["codigo"] ?? "");
$nombreMaterial = trim($_POST["nombre"] ?? "");
$descripcion = trim($_POST["descripcion"] ?? "");
$categoria = trim($_POST["categoria"] ?? "");
$persona = trim($_POST["id_persona"] ?? "");
$ubicacion = trim($_POST["id_ubicacion"] ?? "");

$persona = $persona === "" ? null : (int)$persona;
$ubicacion = $ubicacion === "" ? null : (int)$ubicacion;

    //AÑADIR PERSONA
    if ($persona === null && !empty($_POST["nif"])) {
        $nif = trim($_POST["nif"] ?? "");
        $nombrePersona = trim($_POST["nombre_persona"] ?? "");
        $apellidos = trim($_POST["apellidos"] ?? "");
        $email = trim($_POST["correo"] ?? "");
        $telefono = trim($_POST["telefono"] ?? "");

        $sqlPersona = "INSERT INTO persona (nif, nombre, apellidos, correo, telefono) VALUES (?, ?, ?, ?, ?)";
        $stmtPersona = $conn->prepare($sqlPersona);

        if (!$stmtPersona) {
            echo json_encode([
                "success" => false,
                "error" => $conn->error
            ]);
            exit;
        }
        $stmtPersona->bind_param("sssss", $nif, $nombrePersona, $apellidos, $email, $telefono);
        if (!$stmtPersona->execute()) {
            echo json_encode([
                "success" => false,
                "error" => $stmtPersona->error
            ]);
            exit;
        }
        $persona = $conn->insert_id;
        $stmtPersona->close();
    }

    // AÑADIR UBICACIÓN
    if ($ubicacion === null && !empty($_POST["tipo"])) {
        $tipo = trim($_POST["tipo"] ?? "");
        $CP = trim($_POST["CP"] ?? "");
        $provincia = trim($_POST["provincia"] ?? "");
        $direccion = trim($_POST["direccion"] ?? "");

        $sqlUbicacion = "INSERT INTO ubicacion (tipo, CP, provincia, direccion) VALUES (?, ?, ?, ?)";
        $stmtUbicacion = $conn->prepare($sqlUbicacion);
        if (!$stmtUbicacion) {
            echo json_encode([
                "success" => false,
                "error" => $conn->error
            ]);
            exit;
        }
        $stmtUbicacion->bind_param("ssss", $tipo, $CP, $provincia, $direccion);
        if (!$stmtUbicacion->execute()) {
            echo json_encode([
                "success" => false,
                "error" => $stmtUbicacion->error
            ]);
            exit;
        }
        $ubicacion = $conn->insert_id;
        $stmtUbicacion->close();
    }

$sql = "
INSERT INTO material
(
    codigo,
    nombre,
    descripcion,
    categoria, 
    id_persona, 
    id_ubicacion
)
VALUES (?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);

if (!$stmt) {

    echo json_encode([
        "success" => false,
        "error" => $conn->error
    ]);
    exit;
}

$stmt->bind_param(
    "ssssii",
    $codigo,
    $nombreMaterial,
    $descripcion,
    $categoria,
    $persona,
    $ubicacion
);

if (!$stmt->execute()) {
    echo json_encode([
        "success" => false,
        "error" => $stmt->error
    ]);
    exit;
}
$idMaterial= $conn->insert_id;
$stmt->close();

$sqlMovimiento = "
    INSERT INTO movimiento_material 
    (id_material, id_persona, id_origen, id_destino,  fecha_movimiento)
    VALUES (?, ?, ?, ?, NOW())";

$stmtMovimiento = $conn->prepare($sqlMovimiento);

    if (!$stmtMovimiento) {
        echo json_encode([
            "success" => false,
            "error" => $conn->error
        ]);
        exit;
    }
if ($persona !== null) {
    $idPersonaMovimiento = $persona;
    $idOrigen = null;
    $idDestino = null;
} else {
    $idPersonaMovimiento = null;
    $idOrigen = $ubicacion;
    $idDestino = null;
}

$stmtMovimiento->bind_param("iiii", $idMaterial, $idPersonaMovimiento, $idOrigen, $idDestino);
    if (!$stmtMovimiento->execute()) {
        echo json_encode([
            "success" => false,
            "error" => $stmtMovimiento->error
        ]);
        exit;
    }
$stmtMovimiento->close();

echo json_encode([
    "success" => true,
    "id" => $idMaterial
]);
$conn->close();