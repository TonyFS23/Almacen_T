<?php
session_start();
include("Modelo/conexion.php");

// Crear
if (isset($_POST['crear'])) {
    $nombre = $_POST['nombre'];
    $apellidos = $_POST['apellidos'];
    $numero = $_POST['numero'];
    $area = $_POST['area'];
    $puesto = $_POST['puesto'];

    $stmt = $conexion->prepare("INSERT INTO usuarios (nombre, apellidos, numero, area, puesto) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $nombre, $apellidos, $numero, $area, $puesto);
    $stmt->execute();
    $stmt->close();
}

// Leer con paginación
$limit = 10; // Número de registros por página
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$offset = ($page - 1) * $limit;

$totalResult = $conexion->query("SELECT COUNT(*) AS total FROM usuarios");
$totalRows = $totalResult->fetch_assoc()['total'];
$totalPages = ceil($totalRows / $limit);

$result = $conexion->query("SELECT * FROM usuarios LIMIT $limit OFFSET $offset");

// Buscar
$searchResult = null;
if (isset($_POST['buscar'])) {
    $searchNombre = $_POST['searchNombre'];
    $searchApellidos = $_POST['searchApellidos'];

    $stmt = $conexion->prepare("SELECT * FROM usuarios WHERE nombre LIKE ? AND apellidos LIKE ?");
    $searchNombre = "%$searchNombre%";
    $searchApellidos = "%$searchApellidos%";
    $stmt->bind_param("ss", $searchNombre, $searchApellidos);
    $stmt->execute();
    $searchResult = $stmt->get_result();
    $stmt->close();
}

// Actualizar
if (isset($_POST['actualizar'])) {
    $id = $_POST['Id'];
    $nombre = $_POST['Nombre'];
    $apellidos = $_POST['Apellidos'];
    $numero = $_POST['Numero'];
    $area = $_POST['Area'];
    $puesto = $_POST['Puesto'];

    $stmt = $conexion->prepare("UPDATE usuarios SET nombre = ?, apellidos = ?, numero = ?, area = ?, puesto = ? WHERE id = ?");
    $stmt->bind_param("sssssi", $nombre, $apellidos, $numero, $area, $puesto, $id);
    $stmt->execute();
    $stmt->close();
}

// Borrar
if (isset($_POST['borrar'])) {
    $id = $_POST['Id'];

    $stmt = $conexion->prepare("DELETE FROM usuarios WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Usuarios</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/css/alertify.min.css"/>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/css/themes/default.min.css"/>
    <style>
        body {
            font-family: Georgia, sans-serif;
            background-image: url('fibra.avif');
            background-size: 200%; /* Amplía la imagen 4 veces */
            background-position: top left;
            background-repeat: no-repeat; /* Evita que la imagen se repita */
            margin: 0;
            padding: 0;
            color: rgb(94, 19, 19);
        }
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: 250px;
            height: 100%;
            background-color: rgba(87, 9, 9, 0.9);
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.5);
            padding: 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .sidebar img {
            max-width: 150px;
            height: auto;
            margin-bottom: 20px;
        }
        .sidebar h1 {
            color: #ffffff;
            font-size: 24px;
            margin-bottom: 30px;
            text-align: center;
        }
        .sidebar button {
            background-color: #570909;
            color: white;
            border: none;
            padding: 15px 20px;
            text-align: center;
            text-decoration: none;
            display: block;
            font-size: 18px;
            margin: 10px 0;
            cursor: pointer;
            border-radius: 10px;
            width: 100%;
            transition: background-color 0.3s, color 0.3s;
        }
        .sidebar button:hover {
            background-color: white;
            color:rgb(101, 78, 78);
        }
        .content {
            margin-left: 270px;
            padding: 20px;
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
        .support-button {
            position: fixed; /* Fijar el botón en la esquina inferior derecha */
            bottom: 20px;
            right: 20px;
            display: flex;
            align-items: center;
            justify-content: flex-start;
            height: 60px; /* Altura del botón */
            font-size: 1.2em; /* Tamaño del texto */
            padding: 0 20px;
            color: #000000;
            cursor: pointer;
            transition: background 0.3s, transform 0.3s, box-shadow 0.3s;
            background-color: #3c2546; /* Fondo gris oscuro */
            border: none;
            width: 60px; /* Ancho inicial */
            overflow: hidden;
            white-space: nowrap;
            border-radius: 30px; /* Bordes redondeados */
            box-shadow: 0 0 10px rgba(255, 255, 255, 0.2); /* Sombra suave */
        }
        .support-button span {
            margin-right: 10px;
            flex-shrink: 0;
        }
        .support-button p {
            opacity: 0;
            margin: 0;
            transition: opacity 0.3s;
            display: none;
            flex-grow: 1;
        }
        .support-button:hover {
            background: #ff6600; /* Cambiar a naranja al pasar el puntero */
            color: #000000; /* Cambiar texto a negro */
            width: 300px; /* Aumentar el ancho */
            transform: scale(1.1); /* Ampliar ligeramente */
            box-shadow: 0 0 20px rgba(255, 102, 0, 0.8); /* Sombra más intensa */
        }
        .support-button:hover p {
            opacity: 1;
            display: inline; /* Mostrar texto al pasar el puntero */
        }
    </style>
</head>
<body> 
    <div class="sidebar">
        <img src="dragon blanco.png" alt="Logo">
        <h1>Almacen T</h1>
        <button onclick="confirmNavigation('pagina1.php')">👤 Usuarios</button>
        <button onclick="confirmNavigation('pagina2.php')">📦 Mercancías</button>
        <button onclick="confirmNavigation('pagina3.php')">📝 Pedidos</button>
        <button onclick="confirmNavigation('pagina4.php')">⚙️ Actividades</button>
        <button onclick="confirmNavigation('pagina5.php')">🚚 Proveedores</button>
        <button onclick="confirmNavigation('pagina6.php')">📤 Embarque</button>
        <button onclick="confirmNavigation('pagina7.php')">💰 Costos</button>
        <button onclick="confirmNavigation('venta1.php')">🛒 Venta</button>
        <button onclick="confirmNavigation('loginT.html')">🚪 Salir</button>
    </div>
    
    <div class="content">
        <div class="container text-center">
            <img src="dragon blanco.png" alt="Descripción de la imagen" class="header-image">
            <h2 class="mt-5">Usuarios</h2>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#crearModal">
                Crear Usuario
            </button>

            <!-- Modal Crear -->
            <div class="modal fade" id="crearModal" tabindex="-1" aria-labelledby="crearModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="crearModalLabel">Crear Usuario</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form method="POST" action="" onsubmit="return confirmAction('crear')">
                                <div class="mb-3">
                                    <label for="nombre" class="form-label">Nombre</label>
                                    <input type="text" class="form-control" id="nombre" name="nombre" required>
                                </div>
                                <div class="mb-3">
                                    <label for="apellidos" class="form-label">Apellidos</label>
                                    <input type="text" class="form-control" id="apellidos" name="apellidos" required>
                                </div>
                                <div class="mb-3">
                                    <label for="numero" class="form-label">Número</label>
                                    <input type="text" class="form-control" id="numero" name="numero" required>
                                </div>
                                <div class="mb-3">
                                    <label for="area" class="form-label">Área</label>
                                    <select class="form-control" id="area" name="area" required>
                                        <option value="Produccion">Producción</option>
                                        <option value="Seguridad">Seguridad</option>
                                        <option value="Calidad">Calidad</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="puesto" class="form-label">Puesto</label>
                                    <select class="form-control" id="puesto" name="puesto" required>
                                    <option value="Usuario">Usuario</option>    
                                    <option value="Admin">Admin</option>
                                    </select>
                                </div>
                                <button type="submit" class="btn btn-primary" name="crear">Crear</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal Actualizar -->
            <div class="modal fade" id="actualizarModal" tabindex="-1" aria-labelledby="actualizarModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="actualizarModalLabel">Actualizar Usuario</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form method="POST" action="" onsubmit="return confirmAction('actualizar')">
                                <input type="hidden" id="updateId" name="Id">
                                <div class="mb-3">
                                    <label for="updateNombre" class="form-label">Nombre</label>
                                    <input type="text" class="form-control" id="updateNombre" name="Nombre" required>
                                </div>
                                <div class="mb-3">
                                    <label for="updateApellidos" class="form-label">Apellidos</label>
                                    <input type="text" class="form-control" id="updateApellidos" name="Apellidos" required>
                                </div>
                                <div class="mb-3">
                                    <label for="updateNumero" class="form-label">Número</label>
                                    <input type="text" class="form-control" id="updateNumero" name="Numero" required>
                                </div>
                                <div class="mb-3">
                                    <label for="updateArea" class="form-label">Área</label>
                                    <select class="form-control" id="updateArea" name="Area" required>
                                        <option value="Produccion">Producción</option>
                                        <option value="Seguridad">Seguridad</option>
                                        <option value="Calidad">Calidad</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="updatePuesto" class="form-label">Puesto</label>
                                    <select class="form-control" id="updatePuesto" name="Puesto" required>
                                        <option value="Admin">Admin</option>
                                        <option value="Usuario">Usuario</option>
                                    </select>
                                </div>
                                <button type="submit" class="btn btn-warning" name="actualizar">Actualizar</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal Buscar -->
            <div class="modal fade" id="buscarModal" tabindex="-1" aria-labelledby="buscarModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="buscarModalLabel">Resultados de la Búsqueda</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <table class="table table-dark">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Nombre</th>
                                        <th>Apellidos</th>
                                        <th>Número</th>
                                        <th>Área</th>
                                        <th>Puesto</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if ($searchResult && $searchResult->num_rows > 0): ?>
                                        <?php while ($row = $searchResult->fetch_assoc()): ?>
                                        <tr>
                                            <td><?php echo $row['Id']; ?></td>
                                            <td><?php echo $row['Nombre']; ?></td>
                                            <td><?php echo $row['Apellidos']; ?></td>
                                            <td><?php echo $row['Numero']; ?></td>
                                            <td><?php echo $row['Area']; ?></td>
                                            <td><?php echo $row['Puesto']; ?></td>
                                        </tr>
                                        <?php endwhile; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="6">No se encontraron resultados</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Buscador -->
            <form method="POST" action="" class="mt-5" onsubmit="return confirmAction('buscar')">
                <div class="row">
                    <div class="col-md-5">
                        <input type="text" class="form-control" name="searchNombre" placeholder="Buscar por Nombre">
                    </div>
                    <div class="col-md-5">
                        <input type="text" class="form-control" name="searchApellidos" placeholder="Buscar por Apellidos">
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary" name="buscar">Buscar</button>
                    </div>
                </div>
            </form>

            <h2 class="mt-5">Lista de Usuarios</h2>
            <table class="table table-dark">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Apellidos</th>
                        <th>Número</th>
                        <th>Área</th>
                        <th>Puesto</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['Id']; ?></td>
                        <td><?php echo $row['Nombre']; ?></td>
                        <td><?php echo $row['Apellidos']; ?></td>
                        <td><?php echo $row['Numero']; ?></td>
                        <td><?php echo $row['Area']; ?></td>
                        <td><?php echo $row['Puesto']; ?></td>
                        <td>
                            <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#actualizarModal" onclick="setUpdateData(<?php echo $row['Id']; ?>, '<?php echo $row['Nombre']; ?>', '<?php echo $row['Apellidos']; ?>', '<?php echo $row['Numero']; ?>', '<?php echo $row['Area']; ?>', '<?php echo $row['Puesto']; ?>')">Actualizar</button>
                            <form method="POST" action="" style="display:inline-block;" onsubmit="return confirmAction('borrar')">
                                <input type="hidden" name="Id" value="<?php echo $row['Id']; ?>">
                                <button type="submit" class="btn btn-danger" name="borrar">Borrar</button>
                            </form>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>

        <!-- Paginación -->
        <nav aria-label="Page navigation example">
        <ul class="pagination justify-content-center">
            <li class="page-item <?php if($page <= 1){ echo 'disabled'; } ?>">
                <a class="page-link" href="<?php if($page > 1){ echo "?page=".($page - 1); } ?>">Anterior</a>
            </li>
            <?php if($page > 3): ?>
                <li class="page-item">
                    <a class="page-link" href="?page=1">1</a>
                </li>
                <li class="page-item disabled">
                    <span class="page-link">...</span>
                </li>
            <?php endif; ?>
            <?php for($i = max(1, $page - 2); $i <= min($totalPages, $page + 2); $i++): ?>
                <li class="page-item <?php if($page == $i){ echo 'active'; } ?>">
                    <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                </li>
            <?php endfor; ?>
            <?php if($page < $totalPages - 2): ?>
                <li class="page-item disabled">
                    <span class="page-link">...</span>
                </li>
                <li class="page-item <?php if($page == $totalPages){ echo 'active'; } ?>">
                    <a class="page-link" href="?page=<?php echo $totalPages; ?>"><?php echo $totalPages; ?></a>
                </li>
            <?php endif; ?>
            <li class="page-item <?php if($page >= $totalPages){ echo 'disabled'; } ?>">
                <a class="page-link" href="<?php if($page < $totalPages){ echo "?page=".($page + 1); } ?>">Siguiente</a>
            </li>
        </ul>
    </nav>
    </div>
    </div>

    <button class="support-button" onclick="showSupportInfo()">
        <span>ℹ️</span>
        <p>Soporte</p>
    </button>

    <script src="https://cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/alertify.min.js"></script>
    <script>
        function setUpdateData(id, nombre, apellidos, numero, area, puesto) {
            document.getElementById('updateId').value = id;
            document.getElementById('updateNombre').value = nombre;
            document.getElementById('updateApellidos').value = apellidos;
            document.getElementById('updateNumero').value = numero;
            document.getElementById('updateArea').value = area;
            document.getElementById('updatePuesto').value = puesto;
        }

        function confirmAction(action) {
            return alertify.confirm("¿Estás seguro de que deseas " + action + " este usuario?",
            function(){
                alertify.success('Acción confirmada');
                return true;
            },
            function(){
                alertify.error('Acción cancelada');
                return false;
            }).set('labels', {ok:'Sí', cancel:'No'}).set('defaultFocus', 'cancel');
        }

        function confirmNavigation(url) {
            alertify.confirm("¿Estás seguro de que deseas navegar a esta página?",
            function(){
                alertify.success('Navegando...');
                window.location.href = url;
            },
            function(){
                alertify.error('Cancelado');
            }).set('labels', {ok:'Sí', cancel:'No'}).set('defaultFocus', 'cancel');
        }

        function showSupportInfo() {
            alertify.alert()
                .setting({
                    title: "Información de Soporte",
                    message: "<div style='color: red; background-color: black; padding: 20px; border-radius: 10px; text-shadow: 0 0 10px white;'>"
                            + "Diseñador: Juan Antonio Cardona Ramirez<br>"
                            + "Número telefónico: 3481016907<br>"
                            + "Correo de contacto: Juan_antony-c@hotmail.com"
                            + "</div>",
                    onok: function () { alertify.success('Cerrado'); }
                }).show();
        }

        <?php if ($searchResult && $searchResult->num_rows > 0): ?>
        var buscarModal = new bootstrap.Modal(document.getElementById('buscarModal'));
        buscarModal.show();
        <?php endif; ?>
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>