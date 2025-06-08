<!-- filepath: /c:/xampp/htdocs/AlmacenT/Modelo/conexion.php -->
<?php
$conexion = new mysqli("localhost", "u882154034_tony", "M@ik4312", "u882154034_almacent");

// Verificar la conexión
if ($conexion->connect_error) {
    die("Conexión fallida: " . $conexion->connect_error);
}

$conexion->set_charset("utf8");
?>
