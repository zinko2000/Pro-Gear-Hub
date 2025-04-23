document.addEventListener('DOMContentLoaded', function() {
    // helper function
    function ensureNumber(value) {
    if (typeof value === 'string') {
        return parseFloat(value);
    }
    return value;
}
    const cartItemsContainer = document.querySelector('.cart-items-container');
    const emptyCartMessage = document.querySelector('.empty-cart-message');
    const subtotalElement = document.querySelector('.subtotal');
    const taxElement = document.querySelector('.tax');
    const totalElement = document.querySelector('.total-amount');
    
    // Load cart items
    loadCartItems();
    
async function loadCartItems() {
    try {
        const response = await fetch('Api/get_cart_items.php');
        const result = await response.json();
        
        if (result.success) {
            renderCartItems(result.items || []);
            calculateTotals(result.items || []);
            
            // Update cart count
            if (result.cart_count !== undefined) {
                updateHeaderCartCount(result.cart_count);
            }
        }
    } catch (error) {
        console.error('Cart load failed:', error);
        showErrorToUser("Couldn't load cart. Please try again.");
    }
}

function updateHeaderCartCount(count) {
    const cartCountElements = document.querySelectorAll('.cart-count');
    cartCountElements.forEach(el => {
        el.textContent = count;
    });
}


function showErrorToUser(message) {
    // Create error display container if it doesn't exist
    let errorContainer = document.getElementById('error-container');
    if (!errorContainer) {
        errorContainer = document.createElement('div');
        errorContainer.id = 'error-container';
        errorContainer.style.position = 'fixed';
        errorContainer.style.bottom = '20px';
        errorContainer.style.right = '20px';
        errorContainer.style.zIndex = '10000';
        document.body.appendChild(errorContainer);
    }
    
    // Create error message element
    const errorElement = document.createElement('div');
    errorElement.className = 'error-message';
    errorElement.style.background = '#ff4444';
    errorElement.style.color = 'white';
    errorElement.style.padding = '15px';
    errorElement.style.margin = '10px';
    errorElement.style.borderRadius = '5px';
    errorElement.style.display = 'flex';
    errorElement.style.alignItems = 'center';
    errorElement.style.gap = '10px';
    errorElement.innerHTML = `
        <i class="fas fa-exclamation-circle"></i>
        <span>${message}</span>
    `;
    
    errorContainer.appendChild(errorElement);
    
    // Auto-remove after 5 seconds
    setTimeout(() => {
        errorElement.remove();
    }, 5000);
}
    
    function renderCartItems(items) {
    const cartItemsContainer = document.querySelector('.cart-items-container');
    const emptyCartMessage = document.querySelector('.empty-cart-message');
        cartItemsContainer.innerHTML = '';

        if (!items || items.length === 0) {
            emptyCartMessage.style.display = 'block';
            return;
        }

        // Hide empty message if we have items
        emptyCartMessage.style.display = 'none'
        
        items.forEach(item => {
            const price = ensureNumber(item.price); // Convert to number

            const itemElement = document.createElement('div');
            itemElement.className = 'cart-item';
            itemElement.dataset.id = item.id;
            
            itemElement.innerHTML = `
                <img src="${item.image}" alt="${item.name}" class="cart-item-img">
                <div class="cart-item-details">
                    <h3 class="cart-item-title">${item.name}</h3>
                    <div class="cart-item-price">$${price.toFixed(2)}</div>
                    <div class="quantity-control">
                        <button class="quantity-btn minus" data-id="${item.id}">-</button>
                        <input type="number" value="${item.quantity}" min="1" class="quantity-input" data-id="${item.id}">
                        <button class="quantity-btn plus" data-id="${item.id}">+</button>
                        <div class="loading-spinner" style="display: none;">
                            <i class="fas fa-spinner fa-spin"></i>
                        </div>
                    </div>
                    <button class="remove-item" data-id="${item.id}">
                        <i class="fas fa-trash"></i> Remove
                    </button>
                </div>
            `;
            
            cartItemsContainer.appendChild(itemElement);
        });
        
        // Add event listeners
        document.querySelectorAll('.quantity-btn').forEach(button => {
            button.addEventListener('click', handleQuantityChange);
        });
        
        document.querySelectorAll('.quantity-input').forEach(input => {
            input.addEventListener('change', handleManualQuantityChange);
        });
        
        document.querySelectorAll('.remove-item').forEach(button => {
            button.addEventListener('click', handleRemoveItem);
        });
    }
    
    function calculateTotals(items) {
        const subtotal = items.reduce((sum, item) => {
            const price = ensureNumber(item.price);
            return sum + (price * item.quantity);
        }, 0);
        const shipping = 5.99;
        const tax = subtotal * 0.1; // 10% tax
        
        subtotalElement.textContent = `$${subtotal.toFixed(2)}`;
        taxElement.textContent = `$${tax.toFixed(2)}`;
        totalElement.textContent = `$${(subtotal + shipping + tax).toFixed(2)}`;
    }
    
    async function handleQuantityChange(e) {
        const button = e.target;
        const productId = button.dataset.id;
        const input = document.querySelector(`.quantity-input[data-id="${productId}"]`);
        
        // Disable button during processing
        button.disabled = true;
        
        try {
            let quantity = parseInt(input.value);
            
            // Determine if it's + or - button
            if (button.classList.contains('minus')) {
                quantity = Math.max(1, quantity - 1); // Never go below 1
            } else {
                quantity += 1;
            }
            
            // Visual feedback
            input.value = quantity;
            
            // Add loading indicator
            const originalText = button.innerHTML;
            button.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
            
            // Update cart
            await updateCartItem(productId, quantity);
            
            // Restore button
            button.innerHTML = originalText;
        } catch (error) {
            console.error('Quantity change failed:', error);
            showErrorToUser("Failed to update quantity");
        } finally {
            button.disabled = false;
        }
    }
    
    async function handleManualQuantityChange(e) {
        const input = e.target;
        const productId = input.dataset.id;
        
        try {
            let quantity = parseInt(input.value);
            
            // Validate input
            if (isNaN(quantity)) {
                quantity = 1;
            }
            quantity = Math.max(1, quantity); // Never below 1
            
            // Show loading in the input
            input.classList.add('loading');
            
            // Update cart
            await updateCartItem(productId, quantity);
            
        } catch (error) {
            console.error('Manual quantity change failed:', error);
            showErrorToUser("Invalid quantity entered");
            // Reset to previous valid value
            const response = await fetch(`Api/get_cart_items.php`);
            const result = await response.json();
            const item = result.items.find(i => i.id == productId);
            input.value = item ? item.quantity : 1;
        } finally {
            input.classList.remove('loading');
        }
    }
    
    async function handleRemoveItem(e) {
        const button = e.target;
        button.disabled = true;
        button.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
        
        try {
            const productId = button.dataset.id;
            await removeFromCart(productId);
            
            // Remove item from DOM
            button.closest('.cart-item').remove();
            
            // Check if cart is now empty
            const remainingItems = document.querySelectorAll('.cart-item');
            if (remainingItems.length === 0) {
                document.querySelector('.empty-cart-message').style.display = 'block';
                calculateTotals([]);
                updateHeaderCartCount(0);
            }
        } catch (error) {
            console.error('Remove failed:', error);
            showErrorToUser("Failed to remove item");
        } finally {
            button.disabled = false;
            button.innerHTML = '<i class="fas fa-trash"></i> Remove';
        }
    }
    
    async function updateCartItem(productId, quantity) {
        try {
            const response = await fetch('Api/update_cart_item.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    product_id: productId,
                    quantity: quantity
                })
            });
            
            // Check for HTML errors first
            const responseText = await response.text();
            if (responseText.startsWith('<')) {
                throw new Error('Server returned HTML instead of JSON');
            }
            
            const result = JSON.parse(responseText);
            
            if (!result.success) {
                throw new Error(result.message || 'Failed to update cart');
            }
            
            // Update totals
            calculateTotals(result.items || []);
            
            // Update cart count in header
            const cartCountElements = document.querySelectorAll('.cart-count');
            cartCountElements.forEach(el => {
                el.textContent = result.cart_count || 0;
            });
            
            return true;
        } catch (error) {
            console.error('Error updating cart item:', error);
            showErrorToUser("Failed to update cart. Please try again.");
            return false;
        }
    }
    
    async function removeFromCart(productId) {
        try {
            const response = await fetch('Api/remove_from_cart.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ product_id: productId })
            });
            
            // Check for HTML errors first
            const responseText = await response.text();
            if (responseText.startsWith('<')) {
                throw new Error('Server returned HTML instead of JSON');
            }
            
            const result = JSON.parse(responseText);
            return result.success;
            
        } catch (error) {
            console.error('Error removing item:', error);
            return false;
        }
    }
});