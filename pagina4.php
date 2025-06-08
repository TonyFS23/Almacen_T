<?php
session_start();
include("Modelo/conexion.php");

// Leer con paginación
$limit = 10; // Número de registros por página
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$offset = ($page - 1) * $limit;

$totalResult = $conexion->query("SELECT COUNT(*) AS total FROM actividades");
$totalRows = $totalResult->fetch_assoc()['total'];
$totalPages = ceil($totalRows / $limit);

$result = $conexion->query("SELECT * FROM actividades LIMIT $limit OFFSET $offset");

// Buscar
$searchResult = null;
if (isset($_POST['buscar'])) {
    $searchTarea = $_POST['searchTarea'];
    $searchAsignacion = $_POST['searchAsignacion'];

    $stmt = $conexion->prepare("SELECT * FROM actividades WHERE tarea LIKE ? AND asignacion LIKE ?");
    $searchTarea = "%$searchTarea%";
    $searchAsignacion = "%$searchAsignacion%";
    $stmt->bind_param("ss", $searchTarea, $searchAsignacion);
    $stmt->execute();
    $searchResult = $stmt->get_result();
    $stmt->close();
}

// Crear
if (isset($_POST['crear'])) {
    $tarea = $_POST['tarea'];
    $asignacion = $_POST['asignacion'];
    $fecha = $_POST['fecha'];
    $area = $_POST['area'];
    $estado = $_POST['estado'];

    $stmt = $conexion->prepare("INSERT INTO actividades (tarea, asignacion, fecha, area, estado) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $tarea, $asignacion, $fecha, $area, $estado);
    $stmt->execute();
    $stmt->close();
}

// Actualizar
if (isset($_POST['actualizar'])) {
    $id = $_POST['id'];
    $tarea = $_POST['tarea'];
    $asignacion = $_POST['asignacion'];
    $fecha = $_POST['fecha'];
    $area = $_POST['area'];
    $estado = $_POST['estado'];

    $stmt = $conexion->prepare("UPDATE actividades SET tarea = ?, asignacion = ?, fecha = ?, area = ?, estado = ? WHERE id = ?");
    $stmt->bind_param("sssssi", $tarea, $asignacion, $fecha, $area, $estado, $id);
    $stmt->execute();
    $stmt->close();
}

// Borrar
if (isset($_POST['borrar'])) {
    $id = $_POST['id'];

    $stmt = $conexion->prepare("DELETE FROM actividades WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
}

// Fetch areas from the database
$areasResult = $conexion->query("SELECT DISTINCT area FROM actividades");
$areas = [];
while ($row = $areasResult->fetch_assoc()) {
    $areas[] = $row['area'];
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Actividades</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/css/alertify.min.css"/>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/css/themes/default.min.css"/>
    <style>
        body {
            font-family: Georgia, sans-serif;
            background-image: url('fibra.avif');
            background-size: 200%; /* Amplía la imagen 4 veces */
            background-position: top left;
            background-repeat: no-repeat;
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
            color: #570909;
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
            background-color: rgb(151, 24, 24);
            border: none;
        }
        .btn-primary:hover {
            background-color: rgb(180, 45, 21);
        }
        .btn-warning {
            background-color: rgb(123, 18, 18);
            border: none;
        }
        .btn-warning:hover {
            background-color: rgb(231, 51, 51);
        }
        .btn-danger {
            background-color: #dc3545;
            border: none;
        }
        .btn-danger:hover {
            background-color: #c82333;
        }
        .pagination .page-link {
            color: rgb(11, 10, 10);
        }
        .pagination .page-item.active .page-link {
            background-color: #dc3545;
            border-color: #dc3545;
        }
        .pagination .page-link:hover {
            background-color: #c82333;
            border-color: #c82333;
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
            <h2 class="mt-5">Actividades</h2>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#crearModal">
                Crear Actividad
            </button>
            <button type="button" class="btn btn-primary" onclick="confirmNavigation('PagInicial.html')">
                Regresar
            </button>

            <!-- Modal Crear -->
            <div class="modal fade" id="crearModal" tabindex="-1" aria-labelledby="crearModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="crearModalLabel">Crear Actividad</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form method="POST" action="" onsubmit="return confirmAction('crear')">
                                <div class="mb-3">
                                    <label for="tarea" class="form-label">Tarea</label>
                                    <input type="text" class="form-control" id="tarea" name="tarea" required>
                                </div>
                                <div class="mb-3">
                                    <label for="asignacion" class="form-label">Asignación</label>
                                    <input type="text" class="form-control" id="asignacion" name="asignacion" required>
                                </div>
                                <div class="mb-3">
                                    <label for="fecha" class="form-label">Fecha</label>
                                    <input type="date" class="form-control" id="fecha" name="fecha" required>
                                </div>
                                <div class="mb-3">
                                    <label for="area" class="form-label">Área</label>
                                    <select class="form-control" id="area" name="area" required>
                                        <option value="" disabled selected>Seleccione un área</option>
                                        <?php foreach ($areas as $area): ?>
                                            <option value="<?php echo $area; ?>"><?php echo $area; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="estado" class="form-label">Estado</label>
                                    <select class="form-control" id="estado" name="estado" required>
                                        <option value="Pendiente">Pendiente</option>
                                        <option value="En proceso">En proceso</option>
                                        <option value="Completada">Completada</option>
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
                            <h5 class="modal-title" id="actualizarModalLabel">Actualizar Actividad</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form method="POST" action="" onsubmit="return confirmAction('actualizar')">
                                <input type="hidden" id="updateId" name="id">
                                <div class="mb-3">
                                    <label for="updateTarea" class="form-label">Tarea</label>
                                    <input type="text" class="form-control" id="updateTarea" name="tarea" required>
                                </div>
                                <div class="mb-3">
                                    <label for="updateAsignacion" class="form-label">Asignación</label>
                                    <input type="text" class="form-control" id="updateAsignacion" name="asignacion" required>
                                </div>
                                <div class="mb-3">
                                    <label for="updateFecha" class="form-label">Fecha</label>
                                    <input type="date" class="form-control" id="updateFecha" name="fecha" required>
                                </div>
                                <div class="mb-3">
                                    <label for="updateArea" class="form-label">Área</label>
                                    <input type="text" class="form-control" id="updateArea" name="area" required>
                                </div>
                                <div class="mb-3">
                                    <label for="updateEstado" class="form-label">Estado</label>
                                    <input type="text" class="form-control" id="updateEstado" name="estado" required>
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
                                        <th>Id</th>
                                        <th>Tarea</th>
                                        <th>Asignación</th>
                                        <th>Fecha</th>
                                        <th>Área</th>
                                        <th>Estado</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if ($searchResult && $searchResult->num_rows > 0): ?>
                                        <?php while ($row = $searchResult->fetch_assoc()): ?>
                                        <tr>
                                            <td><?php echo $row['Id']; ?></td>
                                            <td><?php echo $row['Tarea']; ?></td>
                                            <td><?php echo $row['Asignacion']; ?></td>
                                            <td><?php echo $row['Fecha']; ?></td>
                                            <td><?php echo $row['Area']; ?></td>
                                            <td><?php echo $row['Estado']; ?></td>
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
                        <input type="text" class="form-control" name="searchTarea" placeholder="Buscar por Tarea">
                    </div>
                    <div class="col-md-5">
                        <input type="text" class="form-control" name="searchAsignacion" placeholder="Buscar por Asignación">
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary" name="buscar">Buscar</button>
                    </div>
                </div>
            </form>

            <h2 class="mt-5">Lista de Actividades</h2>
            <table class="table table-dark">
                <thead>
                    <tr>
                        <th>Id</th>
                        <th>Tarea</th>
                        <th>Asignación</th>
                        <th>Fecha</th>
                        <th>Área</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['id']; ?></td>
                        <td><?php echo $row['Tarea']; ?></td>
                        <td><?php echo $row['Asignación']; ?></td>
                        <td><?php echo $row['fecha']; ?></td>
                        <td><?php echo $row['Area']; ?></td>
                        <td><?php echo $row['Estado']; ?></td>
                        <td>
                            <form method="POST" action="" style="display:inline-block;" onsubmit="return confirmAction('cambiar a Pendiente')">
                                <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                <input type="hidden" name="estado" value="Pendiente">
                                <button type="submit" class="btn btn-primary" name="cambiar_estado">Pendiente</button>
                            </form>
                            <form method="POST" action="" style="display:inline-block;" onsubmit="return confirmAction('cambiar a En proceso')">
                                <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                <input type="hidden" name="estado" value="En proceso">
                                <button type="submit" class="btn btn-warning" name="cambiar_estado">En proceso</button>
                            </form>
                            <form method="POST" action="" style="display:inline-block;" onsubmit="return confirmAction('cambiar a Completada')">
                                <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                <input type="hidden" name="estado" value="Completada">
                                <button type="submit" class="btn btn-success" name="cambiar_estado">Completada</button>
                            </form>
                            <form method="POST" action="" style="display:inline-block;" onsubmit="return confirmAction('borrar')">
                                <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
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
        function setUpdateData(id, tarea, asignacion, fecha, area, estado) {
            document.getElementById('updateId').value = id;
            document.getElementById('updateTarea').value = tarea;
            document.getElementById('updateAsignacion').value = asignacion;
            document.getElementById('updateFecha').value = fecha;
            document.getElementById('updateArea').value = area;
            document.getElementById('updateEstado').value = estado;
        }

        function confirmAction(action) {
            return alertify.confirm("¿Estás seguro de que deseas " + action + " esta actividad?",
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