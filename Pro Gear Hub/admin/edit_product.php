<?php
require_once '../config.php';

$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT is_admin FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

if (!$user || !$user['is_admin']) {
    header('Location: ../auth/login.php');
    exit;
}

// Get product ID from URL
$product_id = $_GET['id'] ?? 0;

// Fetch product data
$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$product_id]);
$product = $stmt->fetch();

if (!$product) {
    header('Location: products.php');
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $original_price = $_POST['original_price'];
    $category = $_POST['category'];
    $is_new = isset($_POST['is_new']) ? 1 : 0;
    
    // Handle image upload if new image is provided
    $image_path = $product['image_path'];
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = '../images/';
        $file_name = basename($_FILES['image']['name']);
        $file_path = $upload_dir . $file_name;
        
        if (move_uploaded_file($_FILES['image']['tmp_name'], $file_path)) {
            // Delete old image if it exists
            if ($image_path && file_exists($upload_dir . $image_path)) {
                unlink($upload_dir . $image_path);
            }
            $image_path = $file_name;
        }
    }
    
    $stmt = $pdo->prepare("UPDATE products SET name = ?, description = ?, price = ?, original_price = ?, category = ?, is_new = ?, image_path = ? WHERE id = ?");
    $stmt->execute([$name, $description, $price, $original_price, $category, $is_new, $image_path, $product_id]);
    
    header('Location: products.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Pro Gear Hub - Edit Product</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <link rel="stylesheet" type="text/css" href="style.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>
<body>
    <section id="sidebar">
        <a class="brand">
            <i class='bx bxs-cog'></i>
            <span class="text">Pro Gear Hub</span>
        </a>
        
        <ul class="side-menu top">
            <li>
                <a href="dashboard.php">
                    <i class='bx bxs-dashboard'></i>
                    <span class="text">Dashboard</span>
                </a>
            </li>
            <li class="active">
                <a href="products.php">
                    <i class='bx bxs-shopping-bags'></i>
                    <span class="text">Products</span>
                </a>
            </li>
            <li>
                <a href="orders.php">
                    <i class='bx bxs-receipt'></i>
                    <span class="text">Orders</span>
                </a>
            </li>
            <li>
                <a href="customers.php">
                    <i class='bx bxs-group'></i>
                    <span class="text">Customers</span>
                </a>
            </li>
        </ul>

        <ul class="side-menu bottom">
<!--             <li>
                <a href="settings.php">
                    <i class='bx bxs-cog'></i>
                    <span class="text">Settings</span>
                </a>
            </li> -->
            <li>
                <a href="../auth/logout.php" class="logout">
                    <i class='bx bxs-log-out-circle'></i>
                    <span class="text">Logout</span>
                </a>
            </li>
        </ul>
    </section>

    <section id="content">
        <nav>
            <i class="bx bx-menu toggle-sidebar"></i>
            <div class="breadcrumb">
                <a href="admin.php">Dashboard</a>
                <span>/</span>
                <a href="products.php">Products</a>
                <span>/</span>
                <a href="#" class="active">Edit Product</a>
            </div>
        </nav>

        <div class="product-management">
            <div class="card">
                <div class="card-header">
                    <h3>Edit Product</h3>
                </div>
                <div class="card-body">
                    <form method="POST" enctype="multipart/form-data" class="product-form">
                        <div class="form-group">
                            <label for="name">Product Name</label>
                            <input type="text" id="name" name="name" value="<?= htmlspecialchars($product['NAME']) ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="description">Description</label>
                            <textarea id="description" name="description" rows="3" required><?= htmlspecialchars($product['description']) ?></textarea>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="price">Price ($)</label>
                                <input type="number" id="price" name="price" step="0.01" min="0" value="<?= htmlspecialchars($product['price']) ?>" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="original_price">Original Price ($)</label>
                                <input type="number" id="original_price" name="original_price" step="0.01" min="0" value="<?= htmlspecialchars($product['original_price']) ?>">
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="category">Category</label>
                                <input type="text" id="category" name="category" value="<?= htmlspecialchars($product['category']) ?>">
                            </div>
                            
                            <div class="form-group checkbox-group">
                                <input type="checkbox" id="is_new" name="is_new" <?= $product['is_new'] ? 'checked' : '' ?>>
                                <label for="is_new">Mark as New</label>
                            </div>
                        </div>
                        
                        <div class="form-group">
    <label for="image">Product Image</label>
    <?php 
    // Set the correct image path
    $image_path = '';
    if (!empty($product['image_path'])) {
        // Remove any leading/trailing slashes
        $image_path = trim($product['image_path'], '/');
        // Ensure the path points to the correct location
        $image_path = '../images/' . ltrim($image_path, 'images/');
        
        // Verify the image exists
        if (!file_exists($image_path)) {
            $image_path = '../images/placeholder-product.jpg';
        }
    } else {
        $image_path = '../images/placeholder-product.jpg';
    }
    ?>
    
    <?php if ($product['image_path']): ?>
        <div class="current-image">
            <img src="<?= htmlspecialchars($image_path) ?>" 
                 alt="Current Product Image" 
                 width="100"
                 onerror="this.src='../images/placeholder-product.jpg'">
            <span>Current Image</span>
        </div>
    <?php endif; ?>
    <input type="file" id="image" name="image" accept="image/*">
</div>
                        
                        <div class="form-actions">
                            <button type="submit" class="btn">Update Product</button>
                            <a href="products.php" class="btn cancel-btn">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <script src="script.js"></script>
</body>
</html>