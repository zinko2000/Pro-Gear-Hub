document.addEventListener('DOMContentLoaded', function() {
    const productsGrid = document.querySelector('.products-grid');
    const categoryLinks = document.querySelectorAll('.category-list a');
    const priceSlider = document.querySelector('.price-slider');
    const sortSelect = document.querySelector('.sort-options');
    const viewOptions = document.querySelectorAll('.view-option');
    
    // Fetch products from API
    async function fetchProducts(params = {}) {
    const queryString = new URLSearchParams(params).toString();
    try {
        const response = await fetch(`Api/products.php?${queryString}`);
        if (!response.ok) throw new Error('Network response was not ok');
        
        const result = await response.json();
        
        // Return the data array if successful, otherwise empty array
        return result.success ? result.data : [];
    } catch (error) {
        console.error('Error fetching products:', error);
        return []; // Ensure we always return an array
    }
}
    
    // Render products with complete details
    function renderProducts(products) {
        productsGrid.innerHTML = '';
        
        // Ensure products is always an array
        if (!Array.isArray(products)) {
        console.error('Received non-array products:', products);
        products = [];
        }
        if (products.length === 0) {
            productsGrid.innerHTML = '<p class="no-products">No products found matching your criteria.</p>';
            return;
        }
        
        products.forEach(products => {
            const productCard = document.createElement('div');
            productCard.className = 'product-card';
            productCard.innerHTML = `
                ${products.is_new ? '<span class="product-badge">New</span>' : ''}
                <img src="${products.image_path}" alt="${products.name}" loading="lazy">
                
                <div class="product-content">
                    <h3>${products.name}</h3>
                    ${products.description ? `<p class="product-description">${products.description}</p>` : ''}
                    
                    <div class="product-meta">
                        <div class="rating">
                            ${renderStars(products.rating)}
                            <span>(${products.review_count})</span>
                        </div>
                        <div class="price">
                            $${parseFloat(products.price).toFixed(2)}
                            ${products.original_price ? `<span class="original-price">$${parseFloat(products.original_price).toFixed(2)}</span>` : ''}
                        </div>
                    </div>
                    
                    <div class="product-actions">
                        <button class="add-to-cart-btn" data-product-id="${products.id}">
                            <i class="fas fa-shopping-cart"></i> Add to Cart
                        </button>
                    </div>
                </div>
            `;
            productsGrid.appendChild(productCard);
        });
    }
    
    // Helper function to render star ratings
    function renderStars(rating) {
        let stars = '';
        const fullStars = Math.floor(rating);
        const hasHalfStar = rating % 1 >= 0.5;
        
        for (let i = 1; i <= 5; i++) {
            if (i <= fullStars) {
                stars += '<i class="fas fa-star"></i>';
            } else if (i === fullStars + 1 && hasHalfStar) {
                stars += '<i class="fas fa-star-half-alt"></i>';
            } else {
                stars += '<i class="far fa-star"></i>';
            }
        }
        return stars;
    }
    
    // Filter products by category
    categoryLinks.forEach(link => {
        link.addEventListener('click', async function(e) {
            e.preventDefault();
            const category = this.dataset.category;
            
            // Update active state
            categoryLinks.forEach(l => l.classList.remove('active'));
            this.classList.add('active');
            
            const products = await fetchProducts({ category });
            renderProducts(products);
        });
    });
    
    // Filter by price
    priceSlider.addEventListener('input', async function() {
        const maxPrice = parseFloat(this.value);
        document.querySelector('.price-values span:last-child').textContent = `$${maxPrice}`;
        
        const products = await fetchProducts({ maxPrice });
        renderProducts(products);
    });
    
    // Sort products
    sortSelect.addEventListener('change', async function() {
        const sortValue = this.value;
        const products = await fetchProducts({ sort: sortValue });
        renderProducts(products);
    });
    
    // View options (grid/list)
    viewOptions.forEach(option => {
        option.addEventListener('click', function() {
            viewOptions.forEach(opt => opt.classList.remove('active'));
            this.classList.add('active');
            
            productsGrid.classList.toggle('list-view', this.dataset.view === 'list');
        });
    });
    
    // Initial load
    async function init() {
        const products = await fetchProducts();
        // console.log('API Response:', products); // Debug what you're receiving
        renderProducts(products);
    }
    
    init();
});