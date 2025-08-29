<?php
include 'db.php';

// Fetch recent products (same query as reports.php)
$sql = "SELECT p.name AS product_name, c.name AS category_name, s.name AS supplier_name, p.stock
        FROM products p
        LEFT JOIN categories c ON p.category_id = c.category_id
        LEFT JOIN suppliers s ON p.supplier_id = s.supplier_id
        ORDER BY p.product_id DESC
        LIMIT 10";
$result = $conn->query($sql);

// Set headers to download file
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=recent_stock_movements.csv');

// Open output stream
$output = fopen('php://output', 'w');

// Write column headers
fputcsv($output, ['Product', 'Category', 'Supplier', 'Stock']);

// Write rows
while ($row = $result->fetch_assoc()) {
    fputcsv($output, [$row['product_name'], $row['category_name'], $row['supplier_name'], $row['stock']]);
}

fclose($output);
exit;
?>
