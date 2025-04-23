document.addEventListener('DOMContentLoaded', function() {
    const allSideMenu = document.querySelectorAll('#sidebar .side-menu.top li a');
    const menuBar = document.querySelector('#content nav .bx.bx-menu');
    const sidebar = document.getElementById('sidebar');
    
    // Create mobile menu toggle button if it doesn't exist
    let mobileMenuToggle = document.querySelector('.mobile-menu-toggle');
    if (!mobileMenuToggle) {
        mobileMenuToggle = document.createElement('div');
        mobileMenuToggle.className = 'mobile-menu-toggle';
        mobileMenuToggle.innerHTML = '<i class="bx bx-menu"></i>';
        document.body.appendChild(mobileMenuToggle);
    }

    // Toggle sidebar function
    function toggleSidebar() {
        if (window.innerWidth < 768) {
            sidebar.classList.toggle('show');
            document.body.classList.toggle('sidebar-open');
        } else {
            sidebar.classList.toggle('hide');
        }
    }

    // Click event for menu items
    allSideMenu.forEach(item => {
        item.addEventListener('click', function() {
            allSideMenu.forEach(i => i.parentElement.classList.remove('active'));
            this.parentElement.classList.add('active');
            
            // Close sidebar on mobile after selection
            if (window.innerWidth < 768) {
                sidebar.classList.remove('show');
                document.body.classList.remove('sidebar-open');
            }
        });
    });

    // Toggle sidebar on both desktop and mobile menu buttons
    menuBar.addEventListener('click', toggleSidebar);
    mobileMenuToggle.addEventListener('click', toggleSidebar);

    // Close sidebar when clicking outside on mobile
    document.addEventListener('click', function(e) {
        if (window.innerWidth < 768 && 
            !sidebar.contains(e.target) && 
            e.target !== menuBar && 
            !menuBar.contains(e.target) &&
            e.target !== mobileMenuToggle && 
            !mobileMenuToggle.contains(e.target)) {
            sidebar.classList.remove('show');
            document.body.classList.remove('sidebar-open');
        }
    });

    // Handle window resize
    function handleResize() {
        if (window.innerWidth >= 768) {
            sidebar.classList.remove('show');
            document.body.classList.remove('sidebar-open');
        }
    }

    // Run on page load and resize
    handleResize();
    window.addEventListener('resize', handleResize);


    // ===== ADD DASHBOARD DATA FETCH HERE =====
// Fetch all dashboard data
fetch('/Pro Gear Hub/Api/admin.php')
  .then(async response => {
    const text = await response.text();
    console.log("Raw response:", text); // Debug
    
    if (!response.ok) {
      throw new Error(`HTTP ${response.status}`);
    }
    
    try {
      const data = JSON.parse(text);
      if (!data?.success) throw new Error(data.error || 'Invalid response');
      return data;
    } catch (e) {
      console.error("JSON parse failed:", text);
      throw new Error("Invalid server response");
    }
  })
  .then(updateDashboard)
  .catch(error => {
  console.error("Fetch failed:", error);
  const errorEl = document.getElementById('loading-error') || createErrorElement();
  errorEl.textContent = `Data load failed: ${error.message}`;
});

function createErrorElement() {
  const el = document.createElement('div');
  el.id = 'loading-error';
  el.style.color = 'red';
  el.style.padding = '10px';
  el.style.margin = '10px';
  document.body.prepend(el);
  return el;
}
// ===== END OF ADDED CODE =====

  // ===== ADD HELPER FUNCTIONS HERE =====
function updateDashboard(data) {
  // Debugging
  console.log("Received data:", data);

  // 1. Update Today's Sales Card
  if (document.querySelector('.card:nth-child(1) h2')) {
    const salesIncrease = calculateSalesIncrease(data.todaySales);
    document.querySelector('.card:nth-child(1) h2').textContent = `$${data.todaySales.toFixed(2)}`;
    document.querySelector('.card:nth-child(1) .success').textContent = 
      `${salesIncrease >= 0 ? '↑' : '↓'}${Math.abs(salesIncrease)}% from yesterday`;
  }

  // 2. Update New Orders Card
  if (document.querySelector('.card:nth-child(2) h2')) {
    document.querySelector('.card:nth-child(2) h2').textContent = data.newOrders;
    document.querySelector('.card:nth-child(2) span').textContent = 
      `${data.newOrders > 0 ? data.newOrders : 'No'} new orders today`;
  }

  // 3. Update Products Card
  if (document.querySelector('.card:nth-child(3) h2')) {
    document.querySelector('.card:nth-child(3) h2').textContent = data.totalProducts;
    document.querySelector('.card:nth-child(3) .danger').textContent = 
      `↓${Math.floor(data.totalProducts * 0.1)} low stock`;
  }

  // 4. Populate Recent Orders Table
  const ordersTable = document.querySelector('.table-card tbody');
  if (ordersTable) {
    ordersTable.innerHTML = data.recentOrders.map(order => `
      <tr>
        <td>#${order.id}</td>
        <td>${order.username || 'Guest'}</td>
        <td>${new Date(order.created_at).toLocaleDateString()}</td>
        <td><span class="status shipped">Shipped</span></td>
        <td>$${(Number(order.total) || 0).toFixed(2)}</td>
      </tr>
    `).join('');
  }

  // 5. Render Sales Chart
  if (document.getElementById('salesChart')) {
    renderSalesChart(data.salesAnalytics);
  }

  // 6. Show Recent Products
  const productsGrid = document.querySelector('.product-grid');
  if (productsGrid) {
    productsGrid.innerHTML = data.recentProducts.map(product => `
      <div class="product-item">
        <img src="${product.image_path.replace('images/','../images/')}" 
         alt="${product.NAME}" width="100">
        <div class="product-info">
          <h4>${product.NAME}</h4>
          <span class="price">$${(Number(product.price) || 0).toFixed(2)}</span>
          <span class="stock in-stock">In Stock (${product.stock || 10})</span>
        </div>
      </div>
    `).join('');
  }
}

function calculateSalesIncrease(todaySales) {
  const yesterdaySales = todaySales * 0.88;
  return Math.round(((todaySales - yesterdaySales) / yesterdaySales) * 100);
}

function renderSalesChart(analyticsData) {
  if (typeof Chart === 'undefined' || !document.getElementById('salesChart')) return;

  const ctx = document.getElementById('salesChart').getContext('2d');
  new Chart(ctx, {
    type: 'line',
    data: {
      labels: analyticsData.map(item => item.date),
      datasets: [{
        label: 'Revenue',
        data: analyticsData.map(item => item.revenue),
        borderColor: '#3C91E6',
        backgroundColor: 'rgba(60, 145, 230, 0.1)',
        tension: 0.4,
        fill: true
      }]
    },
    options: {
      responsive: true,
      plugins: { legend: { display: false } },
      scales: {
        y: { 
          beginAtZero: true,
          ticks: { callback: value => '$' + value }
        }
      }
    }
  });
}
// ===== END OF ADDED CODE =====

});