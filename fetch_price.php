<?php
// Conexión a la base de datos
$conexion = new mysqli("localhost", "root", "", "almacenT");
if ($conexion->connect_error) {
    die("Connection failed: " . $conexion->connect_error);
}

// Verificar si se envió el nombre del producto
if (isset($_GET['product_name'])) {
    $productName = $_GET['product_name'];

    // Consultar el precio unitario del producto
    $result = $conexion->query("SELECT costo AS unit_price FROM Mercancias WHERE nombre = '$productName'");
    if ($result && $row = $result->fetch_assoc()) {
        echo json_encode(['unit_price' => $row['unit_price']]);
    } else {
        echo json_encode(['unit_price' => null]);
    }
} else {
    echo json_encode(['unit_price' => null]);
}

$conexion->close();
?>