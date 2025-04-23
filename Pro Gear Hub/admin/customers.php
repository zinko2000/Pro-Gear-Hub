<?php
require_once '../config.php';

// Handle search
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$where = '';
$params = [];

if (!empty($search)) {
    $where = "WHERE username LIKE :search OR email LIKE :search";
    $params[':search'] = "%$search%";
}

// Get total number of customers
$totalQuery = $pdo->prepare("SELECT COUNT(*) FROM users $where");
foreach ($params as $key => $value) {
    $totalQuery->bindValue($key, $value);
}
$totalQuery->execute();
$totalCustomers = $totalQuery->fetchColumn();

// Pagination
$perPage = 10;
$totalPages = ceil($totalCustomers / $perPage);
$page = isset($_GET['page']) ? max(1, min($totalPages, intval($_GET['page']))) : 1;
$offset = ($page - 1) * $perPage;

// Get customers with pagination
$query = $pdo->prepare("SELECT id, username, email, created_at, is_admin FROM users $where ORDER BY created_at DESC LIMIT :offset, :perPage");
foreach ($params as $key => $value) {
    $query->bindValue($key, $value);
}
$query->bindValue(':offset', $offset, PDO::PARAM_INT);
$query->bindValue(':perPage', $perPage, PDO::PARAM_INT);
$query->execute();
$customers = $query->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Pro Gear Hub - Customers</title>
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
            <li>
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
            <li class="active">
                <a href="customers.php">
                    <i class='bx bxs-group'></i>
                    <span class="text">Customers</span>
                </a>
            </li>
        </ul>

        <ul class="side-menu bottom">
            <li>
                <a href="../auth/logout.php" class="logout">
                    <i class='bx bxs-log-out-circle'></i>
                    <span class="text">Logout</span>
                </a>
            </li>
        </ul>
    </section>

    <!-- CONTENT -->
    <section id="content">
        <!-- Navigation Bar -->
        <nav>
            <i class="bx bx-menu toggle-sidebar"></i>
            <div class="breadcrumb">
                <a href="admin.php">Dashboard</a>
                <span>/</span>
                <a href="customers.php" class="active">Customers</a>
            </div>
            
            <form action="customers.php" method="get">
                <div class="form-input">
                    <input type="search" name="search" placeholder="Search customers..." value="<?= htmlspecialchars($search) ?>">
                    <button type="submit" class="search-btn"><i class='bx bx-search'></i></button>
                </div>
            </form>
        </nav>

        <!-- Main Content -->
        <div class="customers-management">
            <div class="card">
                <div class="card-header">
                    <h3>Customers (<?= $totalCustomers ?>)</h3>
                </div>
                <div class="card-body">
                    <?php if (!empty($search)): ?>
                        <div class="alert success">
                            <i class='bx bx-info-circle'></i>
                            Showing results for "<?= htmlspecialchars($search) ?>"
                        </div>
                    <?php endif; ?>
                    
                    <div class="table-responsive">
                        <table>
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Username</th>
                                    <th>Email</th>
                                    <th>Registered</th>
                                    <th>Type</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($customers as $customer): ?>
                                    <tr>
                                        <td><?= $customer['id'] ?></td>
                                        <td><?= htmlspecialchars($customer['username']) ?></td>
                                        <td><?= htmlspecialchars($customer['email']) ?></td>
                                        <td><?= date('M d, Y', strtotime($customer['created_at'])) ?></td>
                                        <td>
                                            <span class="status <?= $customer['is_admin'] ? 'admin' : 'customer' ?>">
                                                <?= $customer['is_admin'] ? 'Admin' : 'Customer' ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination -->
                    <?php if ($totalPages > 1): ?>
                        <div class="pagination">
                            <?php if ($page > 1): ?>
                                <a href="customers.php?page=<?= $page-1 ?>&search=<?= urlencode($search) ?>" class="btn">Previous</a>
                            <?php endif; ?>
                            
                            <span>Page <?= $page ?> of <?= $totalPages ?></span>
                            
                            <?php if ($page < $totalPages): ?>
                                <a href="customers.php?page=<?= $page+1 ?>&search=<?= urlencode($search) ?>" class="btn">Next</a>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>

    <script src="script.js"></script>
</body>
</html>