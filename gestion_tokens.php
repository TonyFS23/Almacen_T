<?php
// Conexión a la base de datos
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('DB_NAME', 'tokens_db');

$conexion = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);
if ($conexion->connect_error) {
    die("Error en la conexión: " . $conexion->connect_error);
}

// Insertar datos en la tabla si se envía el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $campo1 = $_POST['campo1'];
    $campo2 = $_POST['campo2'];
    $campo3 = $_POST['campo3'];

    $sql = "INSERT INTO tokens (campo1, campo2, campo3) VALUES (?, ?, ?)";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param('sss', $campo1, $campo2, $campo3);

    if ($stmt->execute()) {
        echo "<script>alert('Datos insertados correctamente');</script>";
    } else {
        echo "<script>alert('Error al insertar datos: " . $stmt->error . "');</script>";
    }

    $stmt->close();
}

// Obtener datos de la tabla
$sql = "SELECT * FROM tokens";
$resultado = $conexion->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Tokens</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
<div class="container text-center">
<img src="dragon blanco.png" alt="Descripción de la imagen" class="header-image">
    <h1 class="text-center">Gestión de Tokens</h1>

    <!-- Formulario para insertar datos -->
    <form method="POST" class="mb-4">
        <div class="mb-3">
            <label for="fecha_hora" class="form-label">Fecha y Hora</label>
            <input type="datetime-local" class="form-control" id="fecha_hora" name="fecha_hora" required>
        </div>
        <div class="mb-3">
            <label for="campo2" class="form-label">Token</label>
            <input type="text" class="form-control" id="campo2" name="campo2" required>
        </div>
        <div class="mb-3">
            <label for="campo3" class="form-label">Usuario</label>
            <input type="text" class="form-control" id="campo3" name="campo3" required>
        </div>
        <button type="submit" class="btn btn-primary">Agregar</button>
    </form>

    <!-- Tabla para mostrar datos -->
    <table class="table table-dark">
        <thead>
        <tr>
            <th>ID</th>
            <th>Fecha y hora</th>
            <th> Token</th>
            <th>Usuario</th>
        </tr>
        </thead>
        <tbody>
        <?php if ($resultado->num_rows > 0): ?>
            <?php while ($fila = $resultado->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $fila['id']; ?></td>
                    <td><?php echo $fila['fecha_hora']; ?></td>
                    <td><?php echo $fila['token']; ?></td>
                    <td><?php echo $fila['usuario']; ?></td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="4" class="text-center">No hay datos disponibles</td>
            </tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Almacen T</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/css/alertify.min.css"/>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/css/themes/default.min.css"/>
    <style>
        body {
            background-color: black;
            color: white;
        }
        .container {
            margin-top: 50px;
        }
        .header-image {
            display: block;
            margin: 0 auto;
            max-width: 100%;
            height: auto;
        }
        .modal-content {
            background-color: #333;
            color: white;
        }
        .form-control {
            background-color: #555;
            color: white;
            border: none;
        }
        .form-control:focus {
            background-color: #666;
            color: white;
            border: none;
            box-shadow: none;
        }
        .btn-primary {
            background-color:rgb(151, 24, 24);
            border: none;
        }
        .btn-primary:hover {
            background-color:rgb(180, 45, 21);
        }
        .btn-warning {
            background-color:rgb(123, 18, 18);
            border: none;
        }
        .btn-warning:hover {
            background-color:rgb(231, 51, 51);
        }
        .btn-danger {
            background-color: #dc3545;
            border: none;
        }
        .btn-danger:hover {
            background-color: #c82333;
        }
        .pagination .page-link {
            color:rgb(11, 10, 10); /* Color del texto del paginador */
        }
        .pagination .page-item.active .page-link {
            background-color: #dc3545; /* Color de fondo del paginador activo */
            border-color: #dc3545; /* Color del borde del paginador activo */
        }
        .pagination .page-link:hover {
            background-color: #c82333; /* Color de fondo del paginador al pasar el ratón por encima */
            border-color: #c82333; /* Color del borde del paginador al pasar el ratón por encima */
        }
    </style>
</head>
</body>
</html>

<?php
$conexion->close();
?>