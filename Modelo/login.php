<!-- filepath: /c:/xampp/htdocs/AlmacenT/login.php -->
<?php
session_start();
include("Modelo/conexion.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usuario = $_POST['username'];
    $contraseña = $_POST['password'];

    // Preparar y ejecutar la consulta
    $stmt = $conexion->prepare("SELECT * FROM login WHERE usuario = ? AND contraseña = ?");
    $stmt->bind_param("ss", $usuario, $contraseña);
    $stmt->execute();
    $result = $stmt->get_result();
 
    if ($result->num_rows > 0) {
        // Usuario y contraseña correctos
        $_SESSION['usuario'] = $usuario;
        header("Location: Paginicial.html");
    } else {
        // Usuario o contraseña incorrectos
        echo "<script>alert('Usuario o contraseña incorrectos'); window.location.href='loginT.html';</script>";
    }

    $stmt->close();
    $conexion->close();
}
?>