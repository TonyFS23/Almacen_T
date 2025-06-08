<?php
// Conexión a la base de datos
$conexion = new mysqli("localhost", "u882154034_tony", "M@ik4312", "u882154034_almacent");
if ($conexion->connect_error) {
    die("Conexión fallida: " . $conexion->connect_error);
}

// Obtener todos los nombres de productos para el menú desplegable
$productNames = $conexion->query("SELECT nombre FROM Mercancias");

// Obtener detalles del producto dinámicamente cuando se selecciona un producto
$productDetails = [];
if (isset($_POST['product_name'])) {
    $productName = $_POST['product_name'];
    $result = $conexion->query("SELECT nombre AS product_name, costo AS unit_price FROM Mercancias WHERE nombre = '$productName'");
    $productDetails = $result->fetch_assoc();
}

// Iniciar sesión para almacenar los artículos del pedido
session_start();
if (!isset($_SESSION['order_items'])) {
    $_SESSION['order_items'] = [];
}

// Manejar la adición de artículos al pedido y guardarlos en la base de datos
if (isset($_POST['add_to_order'])) {
    $productName = $_POST['product_name'];
    $quantity = (int)$_POST['quantity']; // Convertir a entero
    $unitPrice = (float)$_POST['unit_price']; // Convertir a flotante
    $totalPrice = $quantity * $unitPrice; // Calcular el precio total

    // Insertar los datos en la base de datos ventas_h
    $stmt = $conexion->prepare("INSERT INTO ventas_h (Producto, Cantidad, Precio_unitario, Precio_total) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("sidd", $productName, $quantity, $unitPrice, $totalPrice);
    $stmt->execute();
    $stmt->close();

    // Guardar en la sesión
    $_SESSION['order_items'][] = [
        'product_name' => $productName,
        'quantity' => $quantity,
        'unit_price' => $unitPrice,
        'total_price' => $totalPrice
    ];
}

// Manejar el reinicio de la sesión para una nueva venta
if (isset($_POST['clear_sale'])) {
    $_SESSION['order_items'] = [];
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// Recuperar los artículos del pedido desde la sesión
$orderItems = $_SESSION['order_items'];

// Calcular el precio total de la compra
$totalPurchasePrice = array_sum(array_column($orderItems, 'total_price'));

// Configuración de la paginación para la tabla de ventas
$itemsPerPage = 5; // Número de elementos por página
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $itemsPerPage;

// Obtener el total de registros en la tabla ventas_h
$totalItemsResult = $conexion->query("SELECT COUNT(*) AS total FROM ventas_h");
$totalItems = $totalItemsResult->fetch_assoc()['total'];
$totalPages = ceil($totalItems / $itemsPerPage);

// Obtener los datos de la tabla ventas_h con límite y desplazamiento
$result = $conexion->query("SELECT id, Producto, Cantidad, Precio_unitario, Precio_total FROM ventas_h LIMIT $itemsPerPage OFFSET $offset");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Venta</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body {
            font-family: Georgia, sans-serif;
            background-image: url('fibra.avif');
            background-size: 200%; /* Amplía la imagen 4 veces */
            background-position: top left;
            background-repeat: no-repeat;
            margin: 0;
            padding: 0;
            color: white;
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
        .pagination .page-link {
            background-color: black;
            color: white;
            border-color: white;
        }
        .pagination .page-link:hover {
            background-color: red;
            color: white;
        }
        .pagination .page-item.active .page-link {
            background-color: red;
            border-color: red;
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <img src="dragon blanco.png" alt="Logo">
        <h1>Almacen T</h1>
        <button onclick="confirmNavigation('pagina1.2.php')">👤 Usuarios</button>
        <button onclick="confirmNavigation('pagina2.2.php')">📦 Mercancías</button>
        <button onclick="confirmNavigation('pagina3.2.php')">📝 Pedidos</button>
        <button onclick="confirmNavigation('pagina4.2.php')">⚙️ Actividades</button>
        <button onclick="confirmNavigation('pagina5.2.php')">🚚 Proveedores</button>
        <button onclick="confirmNavigation('pagina6.2.php')">📤 Embarque</button>
        <button onclick="confirmNavigation('venta1.2.php')">🛒 Venta</button>
        <button onclick="confirmNavigation('loginT.html')">🚪 Salir</button>
    </div>
    <div class="content">
        <div class="container text-center">
            <img src="dragon blanco.png" alt="Descripción de la imagen" class="header-image">
            <h1>Venta</h1>

            <!-- Formulario de búsqueda de productos -->
            <form method="POST" class="mb-3">
                <div class="row">
                    <div class="col-md-4">
                        <label for="product_name" class="form-label">Nombre del Producto</label>
                        <select class="form-control" id="product_name" name="product_name" required>
                            <option value="" disabled selected>Seleccione un producto</option>
                            <?php while ($row = $productNames->fetch_assoc()): ?>
                                <option value="<?php echo $row['nombre']; ?>" <?php echo (isset($_POST['product_name']) && $_POST['product_name'] === $row['nombre']) ? 'selected' : ''; ?>>
                                    <?php echo $row['nombre']; ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="quantity" class="form-label">Cantidad</label>
                        <input type="number" class="form-control" id="quantity" name="quantity" min="1" required>
                    </div>
                    <div class="col-md-2">
                        <label for="unit_price" class="form-label">Precio Unitario</label>
                        <input type="text" class="form-control" id="unit_price" name="unit_price" value="<?php echo $productDetails['unit_price'] ?? ''; ?>" readonly>
                    </div>
                    <div class="col-md-2">
                        <label for="total_price" class="form-label">Precio Total</label>
                        <input type="text" class="form-control" id="total_price" value="" readonly>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-danger" name="add_to_order">Agregar</button>
                    </div>
                </div>
            </form>

            <!-- Tabla de Resumen de la Compra -->
            <h2>Resumen de la Compra</h2>
            <table class="table table-dark">
                <thead>
                <tr>
                    <th>Producto</th>
                    <th>Cantidad</th>
                    <th>Precio Unitario</th>
                    <th>Precio Total</th> 
                </tr>
                </thead>
                <tbody>
                <?php foreach ($orderItems as $item): ?>
                    <tr>
                        <td><?php echo $item['product_name']; ?></td>
                        <td><?php echo $item['quantity']; ?></td>
                        <td><?php echo $item['unit_price']; ?></td>
                        <td><?php echo $item['total_price']; ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
                <tfoot>
                <tr>
                    <th colspan="3">Total</th>
                    <th><?php echo $totalPurchasePrice; ?></th>
                </tr>
                </tfoot>
            </table>

            <!-- Botón para exportar a TXT -->
            <form method="POST" action="export_txt.php">
                <button type="submit" class="btn btn-danger">Exportar a TXT</button>
            </form>

            <!-- Botón para nueva venta -->
            <form method="POST" class="mt-3">
                <button type="submit" class="btn btn-danger" name="clear_sale">Nueva Venta</button>
            </form>
            <a href="http://localhost/AlmacenT/PagInicial.2.html" class="btn btn-danger mt-3">Regresar</a>

            <!-- Tabla de Resumen de Ventas -->
            <h2 class="mt-5">Resumen de Ventas</h2>
            <table class="table table-dark">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Producto</th>
                    <th>Cantidad</th>
                    <th>Precio Unitario</th>
                    <th>Precio Total</th>
                </tr>
                </thead>
                <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['id']; ?></td>
                        <td><?php echo $row['Producto']; ?></td>
                        <td><?php echo $row['Cantidad']; ?></td>
                        <td><?php echo $row['Precio_unitario']; ?></td>
                        <td><?php echo $row['Precio_total']; ?></td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>

            <!-- Paginación -->
            <nav aria-label="Page navigation example">
                <ul class="pagination justify-content-center">
                    <li class="page-item <?php if ($page <= 1) echo 'disabled'; ?>">
                        <a class="page-link" href="<?php if ($page > 1) echo "?page=" . ($page - 1); ?>">Anterior</a>
                    </li>
                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <li class="page-item <?php if ($page == $i) echo 'active'; ?>">
                            <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                        </li>
                    <?php endfor; ?>
                    <li class="page-item <?php if ($page >= $totalPages) echo 'disabled'; ?>">
                        <a class="page-link" href="<?php if ($page < $totalPages) echo "?page=" . ($page + 1); ?>">Siguiente</a>
                    </li>
                </ul>
            </nav>
        </div>
    </div>

    <script>
        function confirmNavigation(url) {
            if (confirm("¿Estás seguro de que deseas navegar a esta página?")) {
                window.location.href = url;
            }
        }

        // Actualizar el precio total dinámicamente
        const productNameSelect = document.getElementById('product_name');
        const unitPriceInput = document.getElementById('unit_price');
        const totalPriceInput = document.getElementById('total_price');
        const quantityInput = document.getElementById('quantity');

        productNameSelect.addEventListener('change', async () => {
            const productName = productNameSelect.value;
            if (productName) {
                const response = await fetch('fetch_price.php?product_name=' + encodeURIComponent(productName));
                const data = await response.json();
                unitPriceInput.value = data.unit_price || '';
                totalPriceInput.value = '';
                quantityInput.value = '';
            }
        });

        quantityInput.addEventListener('input', () => {
            const quantity = parseFloat(quantityInput.value) || 0;
            const unitPrice = parseFloat(unitPriceInput.value) || 0;
            totalPriceInput.value = (quantity * unitPrice).toFixed(2);
        });
    </script>
</body>
</html>