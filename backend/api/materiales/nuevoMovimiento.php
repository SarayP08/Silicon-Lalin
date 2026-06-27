<?php
session_start();
header("Access-Control-Allow-Credentials: true");
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: http://localhost:5173");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit;
}

require_once __DIR__ . '/../config/conexion.php';

$id = intval($_POST["id_material"] ?? "");

if ($id <= 0) {
    echo json_encode([
        "success" => false,
        "error" => "ID inválido"
    ]);
    exit;
}
$tipoDestino = $_POST["tipoDestino"] ?? "";

$tipo = $_POST["tipo"] ?? "";
$CP = $_POST["CP"] ?? "";
$provincia = $_POST["provincia"] ?? "";
$direccion = $_POST["direccion"] ?? "";
$tipoMovimiento = trim($_POST["tipoMovimiento"] ?? "");
if ($tipoMovimiento === "") {
    echo json_encode([
        "success" => false,
        "error" => "Tipo de movimiento obligatorio"
    ]);
    exit;
}
$idUsuario = $_SESSION["usuario_id"] ?? null;
if (!$idUsuario) {
    http_response_code(401);
    echo json_encode([
        "success" => false,
        "error" => "Usuario no autenticado o sesión expirada"
    ]);
    exit;
}
$idUsuario = (int)$idUsuario;

$persona = trim($_POST["id_persona"] ?? "");
$ubicacion = trim($_POST["id_ubicacion"] ?? "");

$persona = $persona === "" ? null : (int)$persona;
$ubicacion = $ubicacion === "" ? null : (int)$ubicacion;

$sqlActual = "SELECT id_persona, id_ubicacion FROM material WHERE id_material = ?";
$stmtActual = $conn->prepare($sqlActual);
$stmtActual->bind_param("i", $id);
$stmtActual->execute();

$resultActual = $stmtActual->get_result();
$materialActual = $resultActual->fetch_assoc();
if (!$materialActual) {
    echo json_encode([
        "success" => false,
        "error" => $conn->error
    ]);
    exit;
}
$personaOrigen = $materialActual["id_persona"];
$ubicacionOrigen = $materialActual["id_ubicacion"];
$stmtActual->close();

if ($tipoDestino === "persona" && $persona === null && !empty($_POST["nif"])) {
    $nombre = trim($_POST["nombre"] ?? ($_POST["nombre_persona"] ?? ""));
    $apellidos = trim($_POST["apellidos"] ?? "");
    $nif = trim($_POST["nif"] ?? "");
    $correo = trim($_POST["correo"] ?? "");
    $telefono = trim($_POST["telefono"] ?? "");
    if ($nombre === "" || $apellidos === "") {
        echo json_encode([
            "success" => false,
            "error" => "Nombre y apellidos obligatorios"
        ]);
        exit;
    }
    $sqlPersona = "INSERT INTO persona (nif, nombre, apellidos, correo, telefono) 
                VALUES (?, ?, ?, ?, ?)";

    $stmtPersona = $conn->prepare($sqlPersona);

    if (!$stmtPersona) {
        echo json_encode([
            "success" => false,
            "error" => $conn->error
        ]);
        exit;
    }

    $stmtPersona->bind_param("sssss", $nif, $nombre, $apellidos, $correo, $telefono);

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

if ($tipoDestino === "ubicacion" && $ubicacion === null && !empty($_POST["tipo"])) {
    $tipo = trim($_POST["tipo"] ?? "");
    $CP = trim($_POST["CP"] ?? "");
    $provincia = trim($_POST["provincia"] ?? "");
    $direccion = trim($_POST["direccion"] ?? "");

    $sqlUbicacion = "INSERT INTO ubicacion (tipo, CP, provincia, direccion) 
                    VALUES (?, ?, ?, ?)";

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

    if ($tipoDestino === "persona") {
        $sqlUpdateMaterial = "UPDATE material
                              SET id_persona = ?, 
                              id_ubicacion = NULL, 
                              estado = ?
                              where id_material = ?";

        $stmtUpdate = $conn->prepare($sqlUpdateMaterial);

        if (!$stmtUpdate) {
            echo json_encode([
                "success" => false,
                "error" => $conn->error
            ]);
            exit;
        }

        $stmtUpdate->bind_param("isi", $persona, $tipoMovimiento, $id);

        if (!$stmtUpdate->execute()) {
            echo json_encode([
                "success" => false,
                "error" => $stmtUpdate->error
            ]);
            exit;
        }
        $stmtUpdate->close();
    }

    if ($tipoDestino === "ubicacion") {
        $sqlUpdateMaterial = "UPDATE material
                              SET id_persona = NULL, 
                              id_ubicacion = ?, 
                              estado = ?
                              where id_material = ?";
        $stmtUpdate = $conn->prepare($sqlUpdateMaterial);

        if(!$stmtUpdate) {
            echo json_encode([
                "success" => false,
                "error" => $conn->error
            ]);
            exit;
        }

        $stmtUpdate->bind_param("isi", $ubicacion, $tipoMovimiento, $id);

        if(!$stmtUpdate->execute()) {
            echo json_encode([
                "success" => false,
                "error" => $stmtUpdate->error
            ]);
            exit;
        }
        $stmtUpdate->close();
    }

$sqlMovimiento = "
    INSERT INTO movimiento_material 
    (id_material, id_persona,  id_usuario, id_origen, id_destino, tipo_movimiento, fecha_movimiento)
    VALUES (?, ?, ?, ?, ?, ?, NOW())";

$stmtMovimiento = $conn->prepare($sqlMovimiento);

if (!$stmtMovimiento) {
    echo json_encode([
        "success" => false,
        "error" => $conn->error
    ]);
    exit;
}

if ($tipoDestino === "persona") {
    $idPersonaMovimiento = $persona;
    $idOrigen = $ubicacionOrigen;
    $idDestino = null;
} else {
    $idPersonaMovimiento = $personaOrigen;
    $idOrigen =  $ubicacionOrigen;
    $idDestino = $ubicacion;
}

$stmtMovimiento->bind_param("iiiiis", $id, $idPersonaMovimiento, $idUsuario, $idOrigen, $idDestino, $tipoMovimiento);
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
    "id_material" => $id
]);
$conn->close();