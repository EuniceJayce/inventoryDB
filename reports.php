<?php
session_start();
if(!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
?>

<?php include 'db.php'; ?>

<?php
// --- Fetch Counts ---
$totalProducts = $conn->query("SELECT COUNT(*) AS total FROM products")->fetch_assoc()['total'];
$lowStock = $conn->query("SELECT COUNT(*) AS total FROM products WHERE stock <= 5")->fetch_assoc()['total'];
$totalSuppliers = $conn->query("SELECT COUNT(*) AS total FROM suppliers")->fetch_assoc()['total'];
$totalCategories = $conn->query("SELECT COUNT(*) AS total FROM categories")->fetch_assoc()['total'];

// --- Fetch Recent Products (instead of stock_movements) ---
$movements = $conn->query("
  SELECT p.name AS product_name, c.name AS category_name, s.name AS supplier_name, p.stock
  FROM products p
  LEFT JOIN categories c ON p.category_id = c.category_id
  LEFT JOIN suppliers s ON p.supplier_id = s.supplier_id
  ORDER BY p.product_id DESC
  LIMIT 10
");



// --- Fetch Stock per Category for Chart ---
$stockPerCategory = $conn->query("
  SELECT c.name AS category_name, SUM(p.stock) AS total_stock
  FROM categories c
  LEFT JOIN products p ON c.category_id = p.category_id
  GROUP BY c.name
");



$categories = [];
$stocks = [];
while ($row = $stockPerCategory->fetch_assoc()) {
    $categories[] = $row['category_name'];
    $stocks[] = $row['total_stock'] ?? 0;
}
?>





<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Reports - Inventory Management</title>
  <!-- Bootstrap -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <style>
    body {
      background-color: #f8f9fa;
    }
    .sidebar {
      height: 100vh;
      background: #0d6efd;
      color: white;
    }
    .sidebar a {
      color: white;
      text-decoration: none;
      display: block;
      padding: 12px;
      border-radius: 8px;
      margin: 4px 0;
    }
    .sidebar a:hover {
      background: #0b5ed7;
    }
    .content {
      padding: 20px;
    }
    .card {
      border-radius: 12px;
      box-shadow: 0 4px 8px rgba(0,0,0,0.05);
    }
    canvas {
      max-height: 300px;
    }
  </style>
</head>
<body>
  <div class="container-fluid">
    <div class="row">
      <!-- Sidebar -->
      <div class="col-md-2 sidebar p-3">
        <h4 class="mb-4"><i class="fa fa-box"></i> Inventory</h4>
        <a href="index.php"><i class="fa fa-home"></i> Dashboard</a>
        <a href="products.php"><i class="fa fa-cubes"></i> Products</a>
        <a href="categories.php"><i class="fa fa-tags"></i> Categories</a>
        <a href="suppliers.php"><i class="fa fa-truck"></i> Suppliers</a>
        <a href="purchase_orders.php"><i class="fa fa-file-invoice"></i> Purchase Orders</a>
        <a href="reports.php"><i class="fa fa-file"></i> Reports</a>
        <a href="users.php"><i class="fa fa-users"></i> Users</a>
      </div>

      <!-- Main Content -->
      <div class="col-md-10 content">
        <h2 class="mb-4">Reports</h2>

        
        <!-- Summary Cards -->
        <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card p-3 text-center bg-primary text-white">
            <h5>Total Products</h5>
            <h2><?= $totalProducts ?></h2>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card p-3 text-center bg-danger text-white">
            <h5>Low Stock</h5>
            <h2><?= $lowStock ?></h2>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card p-3 text-center bg-success text-white">
            <h5>Total Suppliers</h5>
            <h2><?= $totalSuppliers ?></h2>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card p-3 text-center bg-warning text-dark">
            <h5>Categories</h5>
            <h2><?= $totalCategories ?></h2>
            </div>
        </div>
        </div>


        <!-- Stock Movements Table -->
        <!-- Stock Movements Table -->
        <div class="card mb-4">
        <div class="card-header d-flex justify-content-between">
            <h5>Recent Stock Movements</h5>
            <button class="btn btn-sm btn-secondary"><i class="fa fa-download"></i> Export</button>
        </div>
        <div class="card-body">
            <table class="table table-hover">
            <thead class="table-light">
                <tr>
                <th>Date</th>
                <th>Product</th>
                <th>Type</th>
                <th>Quantity</th>
                <th>Remarks</th>
                </tr>
            </thead>
            <tbody>
            <?php while($row = $movements->fetch_assoc()): ?>
                <tr>
                <td><?= $row['product_name'] ?></td>
                <td><?= $row['category_name'] ?></td>
                <td><?= $row['supplier_name'] ?></td>
                <td><?= $row['stock'] ?></td>
                <td>-</td> <!-- Placeholder since you don't have remarks -->
                </tr>
            <?php endwhile; ?>
            </tbody>


            </table>
        </div>
        </div>


        <!-- Chart (Placeholder) -->
        <div class="card">
          <div class="card-header">
            <h5>Stock Overview</h5>
          </div>
          <div class="card-body">
            <canvas id="stockChart"></canvas>
          </div>
        </div>

      </div>
    </div>
  </div>

  <!-- Bootstrap & Chart.js -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
  const ctx = document.getElementById('stockChart');
  new Chart(ctx, {
    type: 'bar',
    data: {
      labels: <?= json_encode($categories) ?>,
      datasets: [{
        label: 'Stock Quantity',
        data: <?= json_encode($stocks) ?>,
        backgroundColor: ['#0d6efd', '#198754', '#ffc107', '#dc3545', '#6f42c1']
      }]
    }
  });
</script>

</body>
</html>
