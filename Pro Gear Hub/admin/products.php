<?php

require_once '../config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit;
}

// Fetch user data to check admin status
$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT is_admin FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

if (!$user || !$user['is_admin']) {
    header('Location: ../auth/login.php');
    exit;
}

// Handle product actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle delete first
    if (isset($_POST['delete'])) {
        $product_id = $_POST['product_id'] ?? 0;
        if ($product_id) {
            $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
            $stmt->execute([$product_id]);
            
            // Optional: Delete the associated image file
            $stmt = $pdo->prepare("SELECT image_path FROM products WHERE id = ?");
            $stmt->execute([$product_id]);
            $product = $stmt->fetch();
            
            if ($product && $product['image_path'] && file_exists('../images/' . $product['image_path'])) {
                unlink('../images/' . $product['image_path']);
            }
            
            $_SESSION['message'] = "Product deleted successfully";
            header("Location: products.php");
            exit;
        }
    } 
    // Handle add product
    elseif (isset($_POST['add_product'])) {
        // Validate required fields
        $errors = [];
        $name = trim($_POST['name'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $price = $_POST['price'] ?? 0;
        
        if (empty($name)) {
            $errors[] = "Product name is required";
        }
        if (empty($description)) {
            $errors[] = "Description is required";
        }
        if (!is_numeric($price) || $price <= 0) {
            $errors[] = "Valid price is required";
        }
        
        if (empty($errors)) {
            $original_price = $_POST['original_price'] ?? null;
            $category = $_POST['category'] ?? null;
            $is_new = isset($_POST['is_new']) ? 1 : 0;
            
            // Handle image upload
            $image_path = null;
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $upload_dir = '../images/';
                $file_name = basename($_FILES['image']['name']);
                $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
                $valid_extensions = ['jpg', 'jpeg', 'png', 'gif'];
                
                if (in_array($file_ext, $valid_extensions)) {
                    $unique_name = uniqid() . '.' . $file_ext;
                    $file_path = $upload_dir . $unique_name;
                    
                    if (move_uploaded_file($_FILES['image']['tmp_name'], $file_path)) {
                        $image_path = $unique_name;
                    }
                }
            }
            
            try {
                $stmt = $pdo->prepare("INSERT INTO products (name, description, price, original_price, category, is_new, image_path) 
                                      VALUES (?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([
                    $name,
                    $description,
                    $price,
                    $original_price,
                    $category,
                    $is_new,
                    $image_path
                ]);
                
                $_SESSION['message'] = "Product added successfully";
                header("Location: products.php");
                exit;
            } catch (PDOException $e) {
                $errors[] = "Database error: " . $e->getMessage();
            }
        }
        
        // Store errors in session to display
        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['form_data'] = $_POST;
        }
    }
}

// Display errors if they exist
$errors = $_SESSION['errors'] ?? [];
$form_data = $_SESSION['form_data'] ?? [];
unset($_SESSION['errors']);
unset($_SESSION['form_data']);

// Fetch all products from database
try {
    $stmt = $pdo->query("SELECT * FROM products ORDER BY created_at DESC");
    $products = $stmt->fetchAll();
} catch (PDOException $e) {
    $errors[] = "Failed to load products: " . $e->getMessage();
    $products = [];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Pro Gear Hub - Product Management</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <!-- CSS -->
    <link rel="stylesheet" type="text/css" href="style.css">
    <!-- Boxicons CSS -->
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>
<body>
    <!-- SIDEBAR -->
    <section id="sidebar">
        <a class="brand">
            <i class='bx bxs-cog'></i>
            <span class="text">Pro Gear Hub</span>
        </a>
        
        <!-- E-Commerce Focused Menu -->
        <ul class="side-menu top">
            <li>
                <a href="admin.php">
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
 <!--            <li>
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

    <!-- CONTENT -->
    <?php if (!empty($errors)): ?>
        <div class="alert alert-error">
            <?php foreach ($errors as $error): ?>
                <p><?= htmlspecialchars($error) ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
    
    <section id="content">
        <!-- Navigation Bar -->
        <nav>
            <i class="bx bx-menu toggle-sidebar"></i>
            <div class="breadcrumb">
                <a href="admin.php">Dashboard</a>
                <span>/</span>
                <a href="products.php" class="active">Products</a>
            </div>
            
            <form action="#">
                <div class="form-input">
                    <input type="search" placeholder="Search products..." id="productSearch">
                    <button type="submit" class="search-btn"><i class='bx bx-search'></i></button>
                </div>
            </form>
        </nav>

        <!-- Main Content -->
        <div class="product-management">
            <!-- Add Product Form -->
            <div class="card add-product-card">
                <div class="card-header">
                    <h3>Add New Product</h3>
                </div>
                <div class="card-body">
                    <form method="POST" enctype="multipart/form-data" class="product-form">
                        <div class="form-group">
                            <label for="name">Product Name</label>
                            <input type="text" id="name" name="name" value="<?= htmlspecialchars($form_data['name'] ?? '') ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="description">Description</label>
                            <textarea id="description" name="description" rows="3" required><?= htmlspecialchars($form_data['description'] ?? '') ?></textarea>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="price">Price ($)</label>
                                <input type="number" id="price" name="price" step="0.01" min="0" 
                                       value="<?= htmlspecialchars($form_data['price'] ?? '') ?>" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="original_price">Original Price ($)</label>
                                <input type="number" id="original_price" name="original_price" step="0.01" min="0">
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="category">Category</label>
                                <input type="text" id="category" name="category">
                            </div>
                            
                            <div class="form-group checkbox-group">
                                <input type="checkbox" id="is_new" name="is_new">
                                <label for="is_new">Mark as New</label>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="image">Product Image</label>
                            <input type="file" id="image" name="image" accept="image/*">
                        </div>
                        
                        <button type="submit" name="add_product" class="btn">Add Product</button>
                    </form>
                </div>
            </div>

            <!-- Products List -->
            <div class="card products-list-card">
                <div class="card-header">
                    <h3>Product List</h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table>
                            <thead>
                                <tr>
                                    <th>Image</th>
                                    <th>Name</th>
                                    <th>Price</th>
                                    <th>Category</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($products)): ?>
                                    <tr>
                                        <td colspan="6" style="text-align: center;">No products found</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($products as $product): ?>
                                    <tr>
                                        <td>
    <?php
    // Set default image path
    $default_image = '../images/placeholder-product.jpg';
    
    // Get the correct image path
    $image_path = !empty($product['image_path']) 
        ? str_replace('images/', '../images/', $product['image_path'])
        : $default_image;
    
    // Verify the image file exists
    $final_image = file_exists($image_path) ? $image_path : $default_image;
    ?>
    
    <img src="<?= htmlspecialchars($final_image) ?>" 
         alt="<?= htmlspecialchars($product['name'] ?? 'Product image') ?>" 
         width="50"
         style="height:50px; object-fit:cover;">
</td>
                                        <td><?= htmlspecialchars($product['NAME']) ?></td>
                                        <td>$<?= number_format($product['price'], 2) ?></td>
                                        <td><?= htmlspecialchars($product['category'] ?? 'N/A') ?></td>
                                        <td>
                                            <span class="status <?= $product['is_new'] ? 'new' : 'regular' ?>">
                                                <?= $product['is_new'] ? 'New' : 'Regular' ?>
                                            </span>
                                        </td>
                                        <td class="actions">
                                            <a href="edit_product.php?id=<?= $product['id'] ?>" class="btn edit-btn">Edit</a>
                                            <form method="POST" style="display: inline;">
                                                <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                                                <button type="submit" name="delete" class="btn delete-btn" onclick="return confirm('Are you sure you want to delete this product?')">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script src="script.js"></script>
    <script>
        // Product search functionality
        document.getElementById('productSearch').addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const rows = document.querySelectorAll('.products-list-card tbody tr');
            
            rows.forEach(row => {
                const productName = row.querySelector('td:nth-child(2)').textContent.toLowerCase();
                if (productName.includes(searchTerm)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    </script>
</body>
</html>