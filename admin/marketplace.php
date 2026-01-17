<?php
session_start();
require_once '../config/config.php';
require_once '../config/database.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

$db = new Database();

$stmt = $db->query("SELECT * FROM users WHERE id = ?");
$admin = $db->fetch($stmt, [$_SESSION['admin_id']]);

if (isset($_GET['delete_product'])) {
    $product_id = intval($_GET['delete_product']);
    $stmt = $db->query("DELETE FROM products WHERE id = ?");
    $db->execute($stmt, [$product_id]);
    header('Location: marketplace.php');
    exit();
}

if (isset($_GET['toggle_product'])) {
    $product_id = intval($_GET['toggle_product']);
    $stmt = $db->query("UPDATE products SET status = CASE WHEN status = 'available' THEN 'unavailable' ELSE 'available' END WHERE id = ?");
    $db->execute($stmt, [$product_id]);
    header('Location: marketplace.php');
    exit();
}

$stmt = $db->query("SELECT p.*, s.company_name, s.state, s.district, s.contact_number 
                    FROM products p 
                    JOIN suppliers s ON p.supplier_id = s.id 
                    ORDER BY p.created_at DESC");
$products = $db->fetchAll($stmt);

$stmt = $db->query("SELECT COUNT(*) as count FROM suppliers");
$total_suppliers = $db->fetch($stmt);

$stmt = $db->query("SELECT COUNT(*) as count FROM products WHERE status = 'available'");
$available_products = $db->fetch($stmt);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Marketplace - AGRIVISION Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="dashboard-container">
        <aside class="sidebar">
            <div class="sidebar-header">
                <h3><i class="fas fa-shield-alt"></i> AGRIVISION Admin</h3>
            </div>
            <nav class="sidebar-menu">
                <a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
                <a href="farmers.php"><i class="fas fa-users"></i> Farmers</a>
                <a href="fields.php"><i class="fas fa-map"></i> Fields & Crops</a>
                <a href="greenhouses.php"><i class="fas fa-warehouse"></i> Greenhouses</a>
                <a href="schemes.php"><i class="fas fa-hand-holding-usd"></i> Schemes</a>
                <a href="marketplace.php" class="active"><i class="fas fa-store"></i> Marketplace</a>
                <a href="appointments.php"><i class="fas fa-calendar"></i> Appointments</a>
                <a href="ai-queries.php"><i class="fas fa-robot"></i> AI Queries</a>
                <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </nav>
        </aside>
        
        <main class="main-content">
            <div class="top-bar">
                <div class="user-info">
                    <div class="user-avatar" style="background: #1b5e20;">
                        <?php echo strtoupper(substr($admin['name'], 0, 1)); ?>
                    </div>
                    <div>
                        <h4><?php echo htmlspecialchars($admin['name']); ?></h4>
                        <small><?php echo ucfirst($admin['role']); ?></small>
                    </div>
                </div>
                <button class="theme-toggle" title="Toggle Theme">
                    <i class="fas fa-moon"></i>
                </button>
            </div>
            
            <div class="grid-3" style="margin-bottom: 30px;">
                <div class="stat-card">
                    <i class="fas fa-store"></i>
                    <h4><?php echo count($products); ?></h4>
                    <p>Total Products</p>
                </div>
                <div class="stat-card">
                    <i class="fas fa-check-circle" style="color: #4caf50;"></i>
                    <h4><?php echo $available_products['count']; ?></h4>
                    <p>Available</p>
                </div>
                <div class="stat-card">
                    <i class="fas fa-building"></i>
                    <h4><?php echo $total_suppliers['count']; ?></h4>
                    <p>Suppliers</p>
                </div>
            </div>
            
            <div class="card">
                <div class="card-header">
                    <h3><i class="fas fa-box"></i> All Products</h3>
                </div>
                
                <?php if (empty($products)): ?>
                    <p style="text-align: center; color: var(--text-light); padding: 40px;">
                        <i class="fas fa-box fa-3x" style="display: block; margin-bottom: 15px;"></i>
                        No products found in marketplace.
                    </p>
                <?php else: ?>
                    <div class="grid-3">
                        <?php foreach ($products as $product): ?>
                            <div class="card" style="margin-bottom: 0;">
                                <div style="text-align: center; padding: 20px; background: var(--bg-light); border-radius: var(--radius); margin-bottom: 15px;">
                                    <i class="fas fa-box fa-3x" style="color: var(--primary-color);"></i>
                                </div>
                                <h4 style="color: var(--primary-color); margin-bottom: 5px;">
                                    <?php echo htmlspecialchars($product['product_name']); ?>
                                </h4>
                                <p style="font-size: 0.85rem; color: var(--text-light); margin-bottom: 10px;">
                                    <?php echo htmlspecialchars($product['category']); ?>
                                </p>
                                <p style="font-size: 0.9rem; margin-bottom: 10px;">
                                    <?php echo htmlspecialchars(substr($product['description'], 0, 80)); ?>...
                                </p>
                                <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 15px;">
                                    <h4 style="color: var(--secondary-color); margin: 0;">₹<?php echo number_format($product['price'], 2); ?></h4>
                                    <small style="color: var(--text-light);">Stock: <?php echo $product['stock_quantity']; ?></small>
                                </div>
                                <p style="font-size: 0.85rem; color: var(--text-light); margin-top: 10px;">
                                    <i class="fas fa-building"></i> <?php echo htmlspecialchars($product['company_name']); ?>
                                </p>
                                <p style="font-size: 0.85rem; color: var(--text-light);">
                                    <i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($product['district'] . ', ' . $product['state']); ?>
                                </p>
                                <p style="font-size: 0.85rem; color: var(--text-light);">
                                    <i class="fas fa-phone"></i> <?php echo htmlspecialchars($product['contact_number']); ?>
                                </p>
                                <div style="margin-top: 15px; display: flex; gap: 10px;">
                                    <span class="btn btn-sm" style="
                                        <?php 
                                        echo $product['status'] == 'available' ? 'background: #4caf50; color: white;' : 'background: #9e9e9e; color: white;';
                                        ?>">
                                        <?php echo ucfirst($product['status']); ?>
                                    </span>
                                    <a href="marketplace.php?toggle_product=<?php echo $product['id']; ?>" 
                                       class="btn btn-sm" style="color: #ff9800; border-color: #ff9800;">
                                        <i class="fas fa-toggle-on"></i>
                                    </a>
                                    <a href="marketplace.php?delete_product=<?php echo $product['id']; ?>" 
                                       class="btn btn-sm" style="color: #f44336; border-color: #f44336;"
                                       onclick="return confirm('Are you sure you want to delete this product?')">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </main>
    </div>
    
    <script src="../assets/js/main.js"></script>
</body>
</html>
