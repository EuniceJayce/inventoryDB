<?php
session_start();
if(!isset($_SESSION['user_id'])) {
    header("Location: inventory/login.php");
    exit;
}
include 'db.php';

// Fetch counts
$total_products = $conn->query("SELECT COUNT(*) as count FROM products")->fetch_assoc()['count'];
$total_categories = $conn->query("SELECT COUNT(*) as count FROM categories")->fetch_assoc()['count'];
$total_suppliers = $conn->query("SELECT COUNT(*) as count FROM suppliers")->fetch_assoc()['count'];

// Low stock (products with stock < 10)
$low_stock = $conn->query("SELECT COUNT(*) as count FROM products WHERE stock < 10")->fetch_assoc()['count'];

// Recent Products (last 5 added)
$recent_products = $conn->query("
    SELECT p.product_id, p.name, p.stock, p.price, c.category_id, s.supplier_id
    FROM products p
    LEFT JOIN categories c ON p.category_id = c.category_id
    LEFT JOIN suppliers s ON p.supplier_id = s.supplier_id
    ORDER BY p.product_id ASC
    
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Inventory Management System</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <style>
    body { background-color: #f8f9fa; }
    .sidebar { height: 100vh; background: #0d6efd; color: white; }
    .sidebar a { color: white; text-decoration: none; display: block; padding: 12px; border-radius: 8px; margin: 4px 0; }
    .sidebar a:hover { background: #0b5ed7; }
    .content { padding: 20px; }
    .card { border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.05); }
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
        <a href="logout.php" class="text-danger"><i class="fa fa-sign-out-alt"></i> Logout</a>
      </div>

      <!-- Main Content -->
      <div class="col-md-10 content">
        <h2 class="mb-4">Dashboard</h2>
        
        <div class="row g-3">
          <div class="col-md-3">
            <div class="card p-3 text-center bg-primary text-white">
              <h5>Total Products</h5>
              <h2><?php echo $total_products; ?></h2>
            </div>
          </div>
          <div class="col-md-3">
            <div class="card p-3 text-center bg-success text-white">
              <h5>Categories</h5>
              <h2><?php echo $total_categories; ?></h2>
            </div>
          </div>
          <div class="col-md-3">
            <div class="card p-3 text-center bg-warning text-dark">
              <h5>Suppliers</h5>
              <h2><?php echo $total_suppliers; ?></h2>
            </div>
          </div>
          <div class="col-md-3">
            <div class="card p-3 text-center bg-danger text-white">
              <h5>Low Stock</h5>
              <h2><?php echo $low_stock; ?></h2>
            </div>
          </div>
        </div>

        <!-- Recent Products Table -->
        <div class="card mt-4">
          <div class="card-header d-flex justify-content-between">
            <h5>Recent Products</h5>
          
          </div>
          <div class="card-body">
            <table class="table table-hover">
              <thead class="table-light">
                <tr>
                  <th>#</th>
                  <th>Product Name</th>
                  <th>Category</th>
                  <th>Stock</th>
                  <th>Price</th>
                  <th>Supplier</th>
              
                </tr>
              </thead>
              <tbody>
                <?php if($recent_products->num_rows > 0): ?>
                  <?php while($row = $recent_products->fetch_assoc()): ?>
                    <tr>
                      <td><?php echo $row['product_id']; ?></td>
                      <td><?php echo $row['name']; ?></td>
                      <td><?php echo $row['category_id'] ?? 'N/A'; ?></td>
                      <td><?php echo $row['stock']; ?></td>
                      <td><?php echo '$' . number_format($row['price'], 2); ?></td>
                      <td><?php echo $row['supplier_id'] ?? 'N/A'; ?></td>
                      
                    </tr>
                  <?php endwhile; ?>
                <?php else: ?>
                  <tr><td colspan="7" class="text-center">No products found</td></tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>

      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
