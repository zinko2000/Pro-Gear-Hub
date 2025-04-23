<!DOCTYPE html>
<html lang="en">
<head>
    <title>Pro Gear Hub - Admin Panel</title>
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
            <li class="active">
                <a href="dashboard.php">
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

    <!-- CONTENT -->
    <section id="content">
        <!-- Navigation Bar -->
        <nav>
            <i class="bx bx-menu toggle-sidebar"></i>
            <div class="breadcrumb">
                <a href="#">Dashboard</a>
                <span>/</span>
                <a href="#" class="active">Overview</a>
            </div>
            
            <form action="#">
                <div class="form-input">
                    <input type="search" placeholder="Search orders, products...">
                    <button type="submit" class="search-btn"><i class='bx bx-search'></i></button>
                </div>
            </form>
            
<!--             <div class="notifications">
                <i class='bx bxs-bell'></i>
                <span class="num">3</span>
            </div> -->
        </nav>

        <!-- ===== DASHBOARD CONTENT ===== -->
        <div class="dashboard-grid">
            <!-- 1. Today's Sales Card -->
            <div class="card">
              <div class="card-header"><h3>Today's Sales</h3><i class='bx bxs-dollar-circle'></i></div>
              <div class="card-body">
                <h2>$0</h2>
                <span class="success">↑0% from yesterday</span>
              </div>
            </div>

            <!-- 2. New Orders Card -->
            <div class="card">
              <div class="card-header"><h3>New Orders</h3><i class='bx bxs-package'></i></div>
              <div class="card-body">
                <h2>0</h2>
                <span>0 awaiting fulfillment</span>
              </div>
            </div>

            <!-- 3. Products Card -->
            <div class="card">
              <div class="card-header"><h3>Products</h3><i class='bx bxs-widget'></i></div>
              <div class="card-body">
                <h2>0</h2>
                <span class="danger">↓0 low stock</span>
              </div>
            </div>

            <!-- 4. Recent Orders Table -->
            <div class="card table-card">
              <div class="card-header"><h3>Recent Orders</h3></div>
              <div class="card-body">
                <table><thead><tr><th>Order ID</th><th>Customer</th><th>Date</th><th>Status</th><th>Total</th></tr></thead>
                <tbody><!-- Filled by JavaScript --></tbody></table>
              </div>
            </div>
        </div>

            <!-- 5. Sales Chart -->
            <div class="card chart-card">
              <div class="card-header"><h3>Sales Analytics</h3></div>
              <div class="card-body">
                <canvas id="salesChart"></canvas>
              </div>
            </div>

        <!-- 6. Recent Products -->
            <div class="card products-card">
              <div class="card-header"><h3>Recent Products</h3></div>
              <div class="card-body">
                <div class="product-grid"><!-- Filled by JavaScript --></div>
              </div>
            </div>
    </section>

    <script src="script.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</body>
</html>