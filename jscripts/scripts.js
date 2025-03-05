const loginBtn = document.querySelector('.btnLogin-popup');
        const popup = document.getElementById('popupForm');
        const closeBtn = document.getElementById('closeBtn');
        const closeRegisterBtn = document.getElementById('closeRegisterBtn');
        const registerLink = document.querySelector('.register-link');
        const loginLink = document.querySelector('.login-link');

        loginBtn.addEventListener('click', () => {
            popup.style.display = 'flex';
            document.getElementById('loginForm').style.display = 'block';
        });

        closeBtn.addEventListener('click', () => {
            popup.style.display = 'none';
        });

        closeRegisterBtn.addEventListener('click', () => {
            popup.style.display = 'none';
        });

        registerLink.addEventListener('click', (e) => {
            e.preventDefault();
            document.getElementById('loginForm').style.display = 'none';
            document.getElementById('registerForm').style.display = 'block';
        });

        loginLink.addEventListener('click', (e) => {
            e.preventDefault();
            document.getElementById('registerForm').style.display = 'none';
            document.getElementById('loginForm').style.display = 'block';
        });

        let cartCount = 0;
        function openCartModal(button) {
            const modal = document.getElementById('cartModal');
            modal.style.display = 'block';
            // Store the product ID for later use
            const productId = button.getAttribute('data-product-id');
            modal.setAttribute('data-product-id', productId);
        }
        function closeCartModal() {
            const modal = document.getElementById('cartModal');
            modal.style.display = 'none';
        }
        function addToCart() {
            const modal = document.getElementById('cartModal');
            const quantity = document.getElementById('quantityInput').value;
            const productId = modal.getAttribute('data-product-id');
            // Send a request to add the product to the cart (you might want to use AJAX for this)
            // For example:
            fetch('cart/add_to_cart.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    productId: productId,
                    quantity: quantity
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    cartCount += parseInt(quantity);
                    document.getElementById('cartCount').innerText = cartCount;
                    closeCartModal();
                } else {
                    alert('Failed to add to cart');
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
        }
        // Optional: Function to show the login modal
        function showLoginModal() {
            alert("Login modal would show here.");
        }