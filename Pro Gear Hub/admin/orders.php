<?php
require_once '../config.php';

// Get orders from database
$orders = [];
try {
    $stmt = $pdo->query("
        SELECT o.id, o.user_id, u.username, o.total_amount, o.status, o.created_at 
        FROM orders o
        LEFT JOIN users u ON o.user_id = u.id
        ORDER BY o.created_at DESC
    ");
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Failed to fetch orders: " . $e->getMessage();
}

// Handle status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $orderId = $_POST['order_id'];
    $newStatus = $_POST['new_status'];
    
    try {
        $stmt = $pdo->prepare("UPDATE orders SET status = ? WHERE id = ?");
        $stmt->execute([$newStatus, $orderId]);
        $success = "Order status updated successfully!";
        
        // Refresh orders after update
        $stmt = $pdo->query("
            SELECT o.id, o.user_id, u.username, o.total_amount, o.status, o.created_at 
            FROM orders o
            LEFT JOIN users u ON o.user_id = u.id
            ORDER BY o.created_at DESC
        ");
        $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $error = "Failed to update order status: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Pro Gear Hub - Orders</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <link rel="stylesheet" type="text/css" href="style.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>
<body>
    <!-- SIDEBAR -->
    <section id="sidebar">
        <a class="brand">
            <i class='bx bxs-cog'></i>
            <span class="text">Pro Gear Hub</span>
        </a>
        
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
            <li class="active">
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
            <li>
                <a href="settings.php">
                    <i class='bx bxs-cog'></i>
                    <span class="text">Settings</span>
                </a>
            </li>
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
        <nav>
            <i class="bx bx-menu toggle-sidebar"></i>
            <div class="breadcrumb">
                <a href="admin.php">Dashboard</a>
                <span>/</span>
                <a href="orders.php" class="active">Orders</a>
            </div>
            
            <form action="#">
                <div class="form-input">
                    <input type="search" placeholder="Search orders..." id="orderSearch">
                    <button type="submit" class="search-btn"><i class='bx bx-search'></i></button>
                </div>
            </form>
        </nav>

        <div class="orders-management">
            <!-- Status Filter -->
            <div class="status-filter">
                <button class="btn filter-btn active" data-status="all">All</button>
                <button class="btn filter-btn" data-status="pending">Pending</button>
                <button class="btn filter-btn" data-status="processing">Processing</button>
                <button class="btn filter-btn" data-status="shipped">Shipped</button>
                <button class="btn filter-btn" data-status="delivered">Delivered</button>
                <button class="btn filter-btn" data-status="cancelled">Cancelled</button>
            </div>

            <!-- Messages -->
            <?php if (isset($success)): ?>
                <div class="alert success">
                    <i class='bx bxs-check-circle'></i>
                    <?php echo $success; ?>
                </div>
            <?php endif; ?>
            
            <?php if (isset($error)): ?>
                <div class="alert error">
                    <i class='bx bxs-error-circle'></i>
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <!-- Orders Table -->
            <div class="card">
                <div class="card-header">
                    <h3>Order Management</h3>
                    <div class="table-actions">
                        <button class="btn export-btn">
                            <i class='bx bx-export'></i> Export
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="ordersTable">
                            <thead>
                                <tr>
                                    <th>Order ID</th>
                                    <th>Customer</th>
                                    <th>Date</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($orders as $order): ?>
                                    <tr data-status="<?php echo strtolower($order['status']); ?>">
                                        <td>#<?php echo $order['id']; ?></td>
                                        <td><?php echo $order['username'] ?? 'Guest'; ?></td>
                                        <td><?php echo date('M d, Y', strtotime($order['created_at'])); ?></td>
                                        <td>$<?php echo number_format($order['total_amount'], 2); ?></td>
                                        <td>
                                            <span class="status <?php echo strtolower($order['status']); ?>">
                                                <?php echo ucfirst($order['status']); ?>
                                            </span>
                                        </td>
                                        <td class="actions">
                                            <button class="btn view-btn" data-order-id="<?php echo $order['id']; ?>">
                                                <i class='bx bx-show'></i> View
                                            </button>
                                            <div class="status-dropdown">
                                                <select class="status-select" data-order-id="<?php echo $order['id']; ?>">
                                                    <option value="pending" <?php echo $order['status'] == 'pending' ? 'selected' : ''; ?>>Pending</option>
                                                    <option value="processing" <?php echo $order['status'] == 'processing' ? 'selected' : ''; ?>>Processing</option>
                                                    <option value="shipped" <?php echo $order['status'] == 'shipped' ? 'selected' : ''; ?>>Shipped</option>
                                                    <option value="delivered" <?php echo $order['status'] == 'delivered' ? 'selected' : ''; ?>>Delivered</option>
                                                    <option value="cancelled" <?php echo $order['status'] == 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                                                </select>
                                                <button class="btn update-btn" data-order-id="<?php echo $order['id']; ?>">
                                                    <i class='bx bx-check'></i> Update
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Order Details Modal -->
    <div class="modal" id="orderModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Order Details</h3>
                <button class="close-modal">&times;</button>
            </div>
            <div class="modal-body" id="orderDetails">
                <!-- Filled by JavaScript -->
            </div>
        </div>
    </div>

    <script src="script.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Filter orders by status
        const filterBtns = document.querySelectorAll('.filter-btn');
        filterBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                filterBtns.forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                
                const status = this.dataset.status;
                const rows = document.querySelectorAll('#ordersTable tbody tr');
                
                rows.forEach(row => {
                    if (status === 'all' || row.dataset.status === status) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            });
        });
        
        // Search functionality
        const orderSearch = document.getElementById('orderSearch');
        orderSearch.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const rows = document.querySelectorAll('#ordersTable tbody tr');
            
            rows.forEach(row => {
                const rowText = row.textContent.toLowerCase();
                if (rowText.includes(searchTerm)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
        
        // Update status
        document.querySelectorAll('.update-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const orderId = this.dataset.orderId;
                const select = this.closest('.status-dropdown').querySelector('.status-select');
                const newStatus = select.value;
                
                // Send AJAX request to update status
                fetch('../Api/admin.php?action=update_order_status', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `order_id=${orderId}&new_status=${newStatus}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Update UI
                        const row = this.closest('tr');
                        const statusCell = row.querySelector('.status');
                        statusCell.textContent = newStatus.charAt(0).toUpperCase() + newStatus.slice(1);
                        statusCell.className = 'status ' + newStatus;
                        row.dataset.status = newStatus;
                        
                        // Show success message
                        alert('Status updated successfully!');
                    } else {
                        alert('Error: ' + (data.error || 'Failed to update status'));
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while updating status');
                });
            });
        });
        
        // View order details
        document.querySelectorAll('.view-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const orderId = this.dataset.orderId;
                
                // Fetch order details
                fetch(`../Api/admin.php?action=get_order_details&order_id=${orderId}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Populate modal
                            const modal = document.getElementById('orderModal');
                            const details = document.getElementById('orderDetails');
                            
                            // Format order items
                            let itemsHtml = '';
                            data.order_items.forEach(item => {
                                itemsHtml += `
                                    <div class="order-item">
                                        <img src="../${item.image_path}" alt="${item.name}" width="60">
                                        <div class="item-info">
                                            <h4>${item.name}</h4>
                                            <p>$${item.price.toFixed(2)} x ${item.quantity}</p>
                                            <p>Subtotal: $${(item.price * item.quantity).toFixed(2)}</p>
                                        </div>
                                    </div>
                                `;
                            });
                            
                            details.innerHTML = `
                                <div class="order-summary">
                                    <p><strong>Order ID:</strong> #${data.order.id}</p>
                                    <p><strong>Customer:</strong> ${data.order.username || 'Guest'}</p>
                                    <p><strong>Date:</strong> ${new Date(data.order.created_at).toLocaleString()}</p>
                                    <p><strong>Status:</strong> <span class="status ${data.order.status.toLowerCase()}">${data.order.status}</span></p>
                                    <p><strong>Total Amount:</strong> $${data.order.total_amount.toFixed(2)}</p>
                                </div>
                                <div class="order-items">
                                    <h4>Order Items</h4>
                                    ${itemsHtml}
                                </div>
                            `;
                            
                            // Show modal
                            modal.style.display = 'block';
                        } else {
                            alert('Error: ' + (data.error || 'Failed to load order details'));
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('An error occurred while loading order details');
                    });
            });
        });
        
        // Close modal
        document.querySelector('.close-modal').addEventListener('click', function() {
            document.getElementById('orderModal').style.display = 'none';
        });
        
        // Close modal when clicking outside
        window.addEventListener('click', function(event) {
            const modal = document.getElementById('orderModal');
            if (event.target === modal) {
                modal.style.display = 'none';
            }
        });
    });
    </script>
</body>
</html>