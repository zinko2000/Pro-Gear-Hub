// Cart functionality
document.addEventListener('DOMContentLoaded', function() {
    // Initialize cart count
    updateCartCount();

    // Add to cart event delegation
    document.addEventListener('click', async function(e) {
        if (e.target.classList.contains('add-to-cart-btn') || 
            e.target.closest('.add-to-cart-btn')) {
            const button = e.target.classList.contains('add-to-cart-btn') 
                ? e.target 
                : e.target.closest('.add-to-cart-btn');
            const productId = button.dataset.productId;
            
            await addToCart(productId, button);
        }
    });
});

// Add to cart function
async function addToCart(productId, button) {
    // Visual feedback
    const originalHTML = button.innerHTML;
    button.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
    button.disabled = true;
    
    try {
        const response = await fetch('api/add_to_cart.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                product_id: productId,
                quantity: 1
            })
        });
        
        const result = await response.json();
        
        if (result.success) {
            button.innerHTML = '<i class="fas fa-check"></i> Added!';
            updateCartCount(result.cart_count);
            
            // Reset button after 2 seconds
            setTimeout(() => {
                button.innerHTML = originalHTML;
                button.disabled = false;
            }, 2000);
        } else {
            throw new Error(result.message || 'Failed to add to cart');
        }
    } catch (error) {
        console.error('Error:', error);
        button.innerHTML = '<i class="fas fa-times"></i> Error';
        setTimeout(() => {
            button.innerHTML = originalHTML;
            button.disabled = false;
        }, 2000);
        alert('Failed to add item: ' + error.message);
    }
}

// Update cart count
async function updateCartCount() {
    try {
        const response = await fetch('Api/get_cart_count.php');
        
        // First check if response is OK
        if (!response.ok) {
            const errorText = await response.text();
            console.error('API Error Response:', errorText);
            throw new Error(`API Error: ${response.status}`);
        }
        
        // Then try to parse as JSON
        const result = await response.json();
        
        if (result.success) {
            const cartCountElements = document.querySelectorAll('.cart-count');
            cartCountElements.forEach(el => {
                el.textContent = result.count;
            });
        } else {
            console.error('API error:', result.message);
        }
    } catch (error) {
        console.error('Error updating cart count:', error);
    }
}

