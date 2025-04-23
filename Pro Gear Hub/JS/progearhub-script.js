document.addEventListener('DOMContentLoaded', function() {
    // Navigation functionality
    const hamburger = document.querySelector('.hamburger');
    const navLinks = document.querySelector('.nav-links');
    const searchContainer = document.querySelector('.search-container');
    const dropdownBtns = document.querySelectorAll('.dropdown-btn');
    const searchForm = document.querySelector('.search-form');

    // Toggle mobile menu
    hamburger.addEventListener('click', () => {
        navLinks.classList.toggle('active');
        hamburger.classList.toggle('active');
        
        // Toggle hamburger to X
        document.querySelectorAll('.hamburger .bar').forEach((bar, index) => {
            if (index === 0) {
                bar.style.transform = hamburger.classList.contains('active') ? 'rotate(-45deg) translate(-5px, 6px)' : '';
            } else if (index === 1) {
                bar.style.opacity = hamburger.classList.contains('active') ? '0' : '1';
            } else if (index === 2) {
                bar.style.transform = hamburger.classList.contains('active') ? 'rotate(45deg) translate(-5px, -6px)' : '';
            }
        });
    });

    // Toggle dropdowns on mobile
    dropdownBtns.forEach(btn => {
        btn.addEventListener('click', (e) => {
            if (window.innerWidth <= 768) {
                e.preventDefault();
                const dropdown = btn.parentElement;
                dropdown.classList.toggle('active');
            }
        });
    });

    // Close dropdown when clicking outside (for both mobile and desktop)
    document.addEventListener('click', (e) => {
        if (!e.target.matches('.dropdown-btn') && !e.target.closest('.dropdown')) {
            document.querySelectorAll('.dropdown').forEach(dropdown => {
                dropdown.classList.remove('active');
            });
        }
    });
    
});

        // Product details data
        const products = {
            1: {
                name: "NELEUS Dry Fit",
                price: "$29.89",
                originalPrice: "$59.99",
                description: "Ignore the Size Chart provided by amazon,check the size chart of the third picture to select the appropriate size.",
                image: "images/81FwxCuDZXL.png",
            },
            2: {
                name: "MOHUACHI Women Scrunch Butt Lifting Leggings",
                price: "$22.92",
                originalPrice: "$59.99",
                description: "HIGH WAISTED SEAMLESS LEGGINGS: High waist tummy control anti cellulite leggings can conceal the dimples or cellulite, hide muffin tops and streamline your shape, show your curves.",
                image: "images/71iB+pHo2lL.png",
            },
            3: {
                name: "Fitness Tank Tops",
                price: "$32.99",
                originalPrice: "$65.99",
                description: "Premium Viscose Blend - Shrink-resistant and more comfortable. And it's great for better drape and fit to highlight the muscles and perfectly show the results of your workout in the gym.",
                image: "images/AC_SX679.png",
            },
            4: {
                name: "Hanes Sport Xtemp",
                price: "$15.90",
                originalPrice: "$29.99",
                description: "Performance. Style. Comfort. Hanes Sport apparel builds on our comfort heritage with innovative technologies to give you the looks that fit your life, your sport, and your style.",
                image: "images/removebg-preview.png",
            }
        };

        // Create modal HTML
        const modalHTML = `
        <div class="product-modal" id="productModal">
            <div class="modal-content">
                <span class="close-modal">&times;</span>
                <div class="modal-body"></div>
            </div>
        </div>
        `;
        document.body.insertAdjacentHTML('beforeend', modalHTML);

        // View More button functionality
        document.querySelectorAll('.view-more-btn').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.stopPropagation(); // Prevent event bubbling
                const productId = this.getAttribute('data-product-id');
                const product = products[productId];
                
                document.querySelector('.modal-body').innerHTML = `
                    <div class="product-images">
                        <img src="${product.image}" alt="${product.name}">
                    </div>
                    <div class="product-info">
                        <h1 class="product-title">${product.name}</h1>
                        <div class="product-price">
                            ${product.price}
                            <span class="original-price">${product.originalPrice}</span>
                        </div>
                        <div class="product-rating"><div>
                        <p class="product-description">${product.description}</p>
                        <button class="add-to-cart">Add to Cart</button>
                        <a href="progearhub.html" class="back-to-home">← Back to Home</a>
                        <div class="logo">
                            <img src="images/Screenshot2025-03-13174412.png" alt="Company Logo">
                        </div>
                    </div>
                `;
                document.getElementById('productModal').style.display = "block";
            });
        });

        // Close modal
        document.querySelector('.close-modal').addEventListener('click', function() {
            document.getElementById('productModal').style.display = "none";
        });

        // Close when clicking outside modal
        window.addEventListener('click', (e) => {
            if (e.target === document.getElementById('productModal')) {
                document.getElementById('productModal').style.display = "none";
            }
        });


// Check login status and update UI accordingly
// function checkLoginStatus() {
//     fetch('../Api/check_login.php')  // Fixed path
//         .then(response => {
//             if (!response.ok) throw new Error('Network response was not ok');
//             return response.json();
//         })
//         .then(data => {
//             if (data.loggedIn) {
//                 const loginBtn = document.getElementById('login-btn');
//                 const registerBtn = document.getElementById('register-btn');
//                 const logoutBtn = document.getElementById('logout-btn');
                
//                 if (loginBtn) loginBtn.style.display = 'none';
//                 if (registerBtn) registerBtn.style.display = 'none';
//                 if (logoutBtn) logoutBtn.style.display = 'inline-block';
                
//                 //Show username
//                 if (data.username) {
//                     const userGreeting = document.getElementById('user-greeting');
//                     if (userGreeting) {
//                         userGreeting.textContent = `Hi, ${data.username}`;
//                         userGreeting.style.display = 'inline-block';
//                     }
//                 }
//             }
//         })
//         .catch(error => console.error('Error checking login status:', error));
// }

// // Run when DOM is fully loaded
// document.addEventListener('DOMContentLoaded', function() {
//     checkLoginStatus();
// });


// Check login status and update UI accordingly
// function checkLoginStatus() {
//     fetch('../Api/check_login.php')
//         .then(response => {
//             if (!response.ok) throw new Error('Network response was not ok');
//             return response.json();
//         })
//         .then(data => {
//             console.log('Login status response:', data); // Debug logging
//             if (data.loggedIn) {
//                 updateAuthUI(true, data.username);
//             } else {
//                 updateAuthUI(false);
//             }
//         })
//         .catch(error => {
//             console.error('Error checking login status:', error);
//             updateAuthUI(false); // Fallback to logged out state on error
//         });
// }

// Update authentication UI based on login state
// function updateAuthUI(isLoggedIn, username = null) {
//     const loginBtn = document.getElementById('login-btn');
//     const registerBtn = document.getElementById('register-btn');
//     const logoutBtn = document.getElementById('logout-btn');
//     const userGreeting = document.getElementById('user-greeting');

//     if (isLoggedIn) {
//         // User is logged in
//         if (loginBtn) loginBtn.style.display = 'none';
//         if (registerBtn) registerBtn.style.display = 'none';
//         if (logoutBtn) logoutBtn.style.display = 'inline-block';
//         if (userGreeting) {
//             userGreeting.textContent = username ? `Hi, ${username}` : 'Welcome!';
//             userGreeting.style.display = 'inline-block';
//         }
//     } else {
//         // User is not logged in
//         if (loginBtn) loginBtn.style.display = 'inline-block';
//         if (registerBtn) registerBtn.style.display = 'inline-block';
//         if (logoutBtn) logoutBtn.style.display = 'none';
//         if (userGreeting) {
//             userGreeting.style.display = 'none';
//         }
//     }
// }

// // Run when DOM is fully loaded
// document.addEventListener('DOMContentLoaded', function() {
//     checkLoginStatus();
    
//     // Also check login status periodically in case of tab changes
//     setInterval(checkLoginStatus, 30000); // Check every 30 seconds
// });