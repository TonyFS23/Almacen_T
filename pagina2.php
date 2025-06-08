<?php
session_start();
include("Modelo/conexion.php");

// Crear
if (isset($_POST['crear'])) {
    $codigo = $_POST['codigo'];
    $nombre = $_POST['nombre'];
    $unidades = $_POST['unidades'];
    $costo = $_POST['costo'];

    $stmt = $conexion->prepare("INSERT INTO mercancias (codigo, nombre, unidades, costo) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssii", $codigo, $nombre, $unidades, $costo);
    $stmt->execute();
    $stmt->close();
}

// Leer con paginación
$limit = 10; // Número de registros por página
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$offset = ($page - 1) * $limit;

$totalResult = $conexion->query("SELECT COUNT(*) AS total FROM mercancias");
$totalRows = $totalResult->fetch_assoc()['total'];
$totalPages = ceil($totalRows / $limit);

$result = $conexion->query("SELECT * FROM mercancias LIMIT $limit OFFSET $offset");

// Buscar
$searchResult = null;
if (isset($_POST['buscar'])) {
    $searchCodigo = $_POST['searchCodigo'];
    $searchNombre = $_POST['searchNombre'];

    $stmt = $conexion->prepare("SELECT * FROM mercancias WHERE codigo LIKE ? AND nombre LIKE ?");
    $searchCodigo = "%$searchCodigo%";
    $searchNombre = "%$searchNombre%";
    $stmt->bind_param("ss", $searchCodigo, $searchNombre);
    $stmt->execute();
    $searchResult = $stmt->get_result();
    $stmt->close();
}

// Actualizar
if (isset($_POST['actualizar'])) {
    $codigo = $_POST['Codigo'];
    $nombre = $_POST['Nombre'];
    $unidades = $_POST['Unidades'];
    $costo = $_POST['Costo'];

    $stmt = $conexion->prepare("UPDATE mercancias SET codigo = ?, nombre = ?, unidades = ?, costo = ? WHERE codigo = ?");
    $stmt->bind_param("ssiii", $codigo, $nombre, $unidades, $costo, $codigo);
    $stmt->execute();
    $stmt->close();
}

// Borrar
if (isset($_POST['borrar'])) {
    $codigo = $_POST['Codigo'];

    $stmt = $conexion->prepare("DELETE FROM mercancias WHERE codigo = ?");
    $stmt->bind_param("s", $codigo);
    $stmt->execute();
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mercancías</title>
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
            <h2 class="mt-5">Mercancías</h2>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#crearModal">
                Crear Mercancía
            </button>
            <button type="button" class="btn btn-primary" onclick="confirmNavigation('PagInicial.html')">
                Regresar
            </button>

            <!-- Modal Crear -->
            <div class="modal fade" id="crearModal" tabindex="-1" aria-labelledby="crearModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="crearModalLabel">Crear Mercancía</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form method="POST" action="" onsubmit="return confirmAction('crear')">
                                <div class="mb-3">
                                    <label for="codigo" class="form-label">Código</label>
                                    <select class="form-control" id="codigo" name="codigo" onchange="autocompletarPorCodigo(); calcularTotal();" required>
                                        <option value="">Selecciona un código</option>
                                        <?php
                                        // Consulta para obtener códigos existentes
                                        $codigosResult = $conexion->query("SELECT DISTINCT codigo, nombre, costo FROM mercancias");
                                        $mercancias = [];
                                        while ($row = $codigosResult->fetch_assoc()) {
                                            $mercancias[] = $row; // Guardar datos en un array para JavaScript
                                            echo "<option value='" . $row['codigo'] . "'>" . $row['codigo'] . "</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="nombre" class="form-label">Nombre</label>
                                    <select class="form-control" id="nombre" name="nombre" onchange="autocompletarPorNombre(); calcularTotal();" required>
                                        <option value="">Selecciona un nombre</option>
                                        <?php
                                        // Consulta para obtener nombres existentes
                                        foreach ($mercancias as $mercancia) {
                                            echo "<option value='" . $mercancia['nombre'] . "'>" . $mercancia['nombre'] . "</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="costo" class="form-label">Costo</label>
                                    <input type="number" class="form-control" id="costo" name="costo" readonly required>
                                </div>
                                <div class="mb-3">
                                    <label for="unidades" class="form-label">Unidades</label>
                                    <input type="number" class="form-control" id="unidades" name="unidades" oninput="calcularTotal()" required>
                                </div>
                                <div class="mb-3">
                                    <label for="total" class="form-label">Total</label>
                                    <input type="text" class="form-control" id="total" name="total" readonly>
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
                            <h5 class="modal-title" id="actualizarModalLabel">Actualizar Mercancía</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form method="POST" action="" onsubmit="return confirmAction('actualizar')">
                                <input type="hidden" id="updateCodigo" name="Codigo">
                                <div class="mb-3">
                                    <label for="updateCodigo" class="form-label">Código</label>
                                    <input type="text" class="form-control" id="updateCodigo" name="Codigo" required>
                                </div>
                                <div class="mb-3">
                                    <label for="updateNombre" class="form-label">Nombre</label>
                                    <input type="text" class="form-control" id="updateNombre" name="Nombre" required>
                                </div>
                                <div class="mb-3">
                                    <label for="updateUnidades" class="form-label">Unidades</label>
                                    <input type="number" class="form-control" id="updateUnidades" name="Unidades" required>
                                </div>
                                <div class="mb-3">
                                    <label for="updateCosto" class="form-label">Costo</label>
                                    <input type="number" class="form-control" id="updateCosto" name="Costo" required>
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
                                        <th>Código</th>
                                        <th>Nombre</th>
                                        <th>Unidades</th>
                                        <th>Costo</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if ($searchResult && $searchResult->num_rows > 0): ?>
                                        <?php while ($row = $searchResult->fetch_assoc()): ?>
                                        <tr>
                                            <td><?php echo $row['Codigo']; ?></td>
                                            <td><?php echo $row['Nombre']; ?></td>
                                            <td><?php echo $row['Unidades']; ?></td>
                                            <td><?php echo $row['Costo']; ?></td>
                                        </tr>
                                        <?php endwhile; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="5">No se encontraron resultados</td>
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
                        <input type="text" class="form-control" name="searchCodigo" placeholder="Buscar por Código">
                    </div>
                    <div class="col-md-5">
                        <input type="text" class="form-control" name="searchNombre" placeholder="Buscar por Nombre">
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary" name="buscar">Buscar</button>
                    </div>
                </div>
            </form>

            <h2 class="mt-5">Lista de Mercancías</h2>
            <table class="table table-dark">
                <thead>
                    <tr>
                        <th>Código</th>
                        <th>Nombre</th>
                        <th>Unidades</th>
                        <th>Costo</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['Codigo']; ?></td>
                        <td><?php echo $row['Nombre']; ?></td>
                        <td><?php echo $row['Unidades']; ?></td>
                        <td><?php echo $row['Costo']; ?></td>
                        <td>
                            <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#actualizarModal" onclick="setUpdateData('<?php echo $row['Codigo']; ?>', '<?php echo $row['Codigo']; ?>', '<?php echo $row['Nombre']; ?>', '<?php echo $row['Unidades']; ?>', '<?php echo $row['Costo']; ?>')">Actualizar</button>
                            <form method="POST" action="" style="display:inline-block;" onsubmit="return confirmAction('borrar')">
                                <input type="hidden" name="Codigo" value="<?php echo $row['Codigo']; ?>">
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
        function setUpdateData(codigo, codigo, nombre, unidades, costo) {
            document.getElementById('updateCodigo').value = codigo;
            document.getElementById('updateCodigo').value = codigo;
            document.getElementById('updateNombre').value = nombre;
            document.getElementById('updateUnidades').value = unidades;
            document.getElementById('updateCosto').value = costo;
        }

        function confirmAction(action) {
            return alertify.confirm("¿Estás seguro de que deseas " + action + " esta mercancía?",
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
<script>
    // Convertir los datos de PHP a JSON para usarlos en JavaScript
    const mercancias = <?php echo json_encode($mercancias); ?>;

    function autocompletarPorCodigo() {
        const codigoSeleccionado = document.getElementById('codigo').value;
        const mercancia = mercancias.find(item => item.codigo === codigoSeleccionado);
        if (mercancia) {
            document.getElementById('nombre').value = mercancia.nombre;
            document.getElementById('costo').value = mercancia.costo;
        }
    }

    function autocompletarPorNombre() {
        const nombreSeleccionado = document.getElementById('nombre').value;
        const mercancia = mercancias.find(item => item.nombre === nombreSeleccionado);
        if (mercancia) {
            document.getElementById('codigo').value = mercancia.codigo;
            document.getElementById('costo').value = mercancia.costo;
        }
    }
    // Función para calcular el total (Unidades * Costo)
    function calcularTotal() {
        const unidades = parseFloat(document.getElementById('unidades').value) || 0;
        const costo = parseFloat(document.getElementById('costo').value) || 0;
        const total = unidades * costo;
        document.getElementById('total').value = total.toFixed(2); // Mostrar el total con 2 decimales
    }
</script>
</html>