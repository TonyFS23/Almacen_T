<?php
@session_start();
date_default_timezone_set('America/Mexico_City');
$usuario = "root";
$contrasena = "";
$consulta= new PDO('mysql:host=localhost;dbname=pi', $usuario, $contrasena);
;
?>
