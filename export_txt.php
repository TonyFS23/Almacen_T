<?php
session_start();

// Generate a random 5-character alphanumeric code
$randomCode = substr(str_shuffle("ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789"), 0, 5);

// Retrieve order items from session
$orderItems = $_SESSION['order_items'] ?? [];
$totalPurchasePrice = array_sum(array_column($orderItems, 'total_price'));

// Create content for the TXT file
$content = "Resumen de la Compra\n";
$content .= "Código de Compra: $randomCode\n\n";
$content .= "Producto\tCantidad\tPrecio Unitario\tPrecio Total\n";

foreach ($orderItems as $item) {
    $content .= $item['product_name'] . "\t" . $item['quantity'] . "\t" . $item['unit_price'] . "\t" . $item['total_price'] . "\n";
}

$content .= "\nTotal de la Compra: $totalPurchasePrice\n";

// Set headers to download the file
header('Content-Type: text/plain');
header('Content-Disposition: attachment; filename="Resumen_Compra_' . $randomCode . '.txt"');

// Output the content
echo $content;
exit;
?>