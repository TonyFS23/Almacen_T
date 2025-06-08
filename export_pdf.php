<?php
if (!file_exists('vendor/autoload.php')) {
    die("Error: No se encontró el archivo 'vendor/autoload.php'. Asegúrate de instalar las dependencias con Composer ejecutando 'composer require dompdf/dompdf' en la carpeta del proyecto.");
}

require 'vendor/autoload.php';
use Dompdf\Dompdf;

session_start();

// Generate a random 5-character alphanumeric code
$randomCode = substr(str_shuffle("ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789"), 0, 5);

// Retrieve order items from session
$orderItems = $_SESSION['order_items'] ?? [];
$totalPurchasePrice = array_sum(array_column($orderItems, 'total_price'));

// Create HTML content for the PDF
$html = '<h1>Resumen de la Compra</h1>';
$html .= '<p>Código de Compra: ' . $randomCode . '</p>';
$html .= '<table border="1" style="width:100%; border-collapse: collapse; text-align: left;">';
$html .= '<thead><tr><th>Producto</th><th>Cantidad</th><th>Precio Unitario</th><th>Precio Total</th></tr></thead><tbody>';

foreach ($orderItems as $item) {
    $html .= '<tr>';
    $html .= '<td>' . $item['product_name'] . '</td>';
    $html .= '<td>' . $item['quantity'] . '</td>';
    $html .= '<td>' . $item['unit_price'] . '</td>';
    $html .= '<td>' . $item['total_price'] . '</td>';
    $html .= '</tr>';
}

$html .= '</tbody>';
$html .= '<tfoot><tr><th colspan="3">Total</th><th>' . $totalPurchasePrice . '</th></tr></tfoot>';
$html .= '</table>';

// Initialize Dompdf
$dompdf = new Dompdf();
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();

// Output the generated PDF
$dompdf->stream('Resumen_Compra_' . $randomCode . '.pdf', ['Attachment' => true]);

exit;
?>