<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prekių Krepšelis</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .navbar .cart-icon {
            font-size: 1.5em;
            cursor: pointer;
        }
        .cart-item img {
            max-width: 80px;
            height: auto;
        }
        .cart-total {
            font-size: 1.2em;
            font-weight: bold;
        }
        .checkout-btn {
            background-color: green;
            color: white;
            border: none;
        }
        .checkout-btn:hover {
            background-color: darkgreen;
        }
        .quantity-input {
            width: 50px;
            text-align: center;
        }
        .error-message {
            color: red;
            font-size: 0.9em;
        }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-dark bg-success">
        <span class="navbar-brand text-white">Žvejybos Parduotuvė</span>
        <a href="pagrindinis.php" class="btn btn-light mr-3">Pagrindinis</a>
        <div class="ml-auto d-flex align-items-center">
            <!-- Profile Icon with Logout -->
            <a href="redaguoti_paskyra.html" class="profile-icon">&#128100;</a>
            <a href="atsijungti.html" class="btn btn-danger ml-2">Atsijungti</a>
        </div>
    </nav>

    <div class="container mt-4">
        <h2>Prekių Krepšelis</h2>

        <!-- If cart is not empty -->
        <div id="cart-content">
            <!-- Dynamically inserted cart items go here -->
        </div>

        <div id="empty-cart-message" class="d-none">
            <p>Jūsų krepšelis tuščias.</p>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', () => {
                let cart = JSON.parse(sessionStorage.getItem('cart')) || [];

                const cartContentDiv = document.getElementById('cart-content');
                const emptyCartMessage = document.getElementById('empty-cart-message');

                if (cart.length > 0) {
                    emptyCartMessage.classList.add('d-none'); // Hide empty cart message

                    cart.forEach(item => {
                        const cartItemDiv = document.createElement('div');
                        cartItemDiv.classList.add('cart-item', 'd-flex', 'justify-content-between', 'align-items-center', 'border-bottom', 'py-3');
                        
                        cartItemDiv.innerHTML = `
                            <div class="d-flex align-items-center">
                                <div>
                                    <a href="preke.php?id_Preke=${item.id}" class="card-title">${item.name}</a>
                                    <p class="mb-1">Kiekis: 
                                        <input type="number" class="quantity-input" data-id="${item.id}" value="${item.quantity}" min="1">
                                    </p>
                                    <p class="error-message d-none" id="error-${item.id}">Pasirinkta per daug prekių!</p>
                                </div>
                            </div>
                            <span>Kaina: €${(item.price * item.quantity).toFixed(2)}</span>
                            <button class="btn btn-danger btn-sm remove-item" data-id="${item.id}">Pašalinti</button>`;
                        
                        cartContentDiv.appendChild(cartItemDiv);
                    });

                    // Update total price
                    function updateTotal() {
                        const total = cart.reduce((sum, item) => sum + item.price * item.quantity, 0);
                        const totalDiv = document.createElement('div');
                        totalDiv.classList.add('d-flex', 'justify-content-between', 'align-items-center', 'mt-4');
                        totalDiv.innerHTML = `
                            <span class="cart-total">Bendra suma: €${total.toFixed(2)}</span>
                            <a href="uzsakymas.php" class="btn checkout-btn">Užsakyti</a>
                        `;
                        const existingTotal = document.querySelector('.cart-total');
                        if (existingTotal) {
                            existingTotal.parentElement.remove();
                        }
                        cartContentDiv.appendChild(totalDiv);
                    }

                    updateTotal();

                    // Handle quantity changes
                    document.querySelectorAll('.quantity-input').forEach(input => {
                        input.addEventListener('input', function() {
                            const productId = this.getAttribute('data-id');
                            const newQuantity = parseInt(this.value);

                            // Find the product in the cart
                            const product = cart.find(item => item.id === productId);

                            // Assuming item.stock is the available quantity
                            const availableQuantity = product.stock; 

                            if (newQuantity > availableQuantity) {
                                // Show error message if quantity exceeds available stock
                                document.getElementById(`error-${productId}`).classList.remove('d-none');
                                this.value = availableQuantity; // Revert to max available quantity
                            } else {
                                // Hide error message if the quantity is valid
                                document.getElementById(`error-${productId}`).classList.add('d-none');
                            }

                            if (product) {
                                product.quantity = newQuantity > 0 ? newQuantity : 1; // Ensure quantity is positive
                            }

                            // Save the updated cart in sessionStorage
                            sessionStorage.setItem('cart', JSON.stringify(cart));

                            // Update the price and total
                            this.closest('.cart-item').querySelector('span').textContent = `Kaina: €${(product.price * product.quantity).toFixed(2)}`;
                            updateTotal();
                        });
                    });

                    // Handle remove item action
                    document.querySelectorAll('.remove-item').forEach(button => {
                        button.addEventListener('click', function() {
                            const productId = this.getAttribute('data-id');
                            // Remove product from the cart array
                            cart = cart.filter(item => item.id !== productId);

                            // Save updated cart to sessionStorage
                            sessionStorage.setItem('cart', JSON.stringify(cart));

                            // Remove the cart item from the DOM
                            this.closest('.cart-item').remove();

                            // Check if the cart is empty and show the empty message
                            if (cart.length === 0) {
                                emptyCartMessage.classList.remove('d-none');
                                cartContentDiv.innerHTML = ''; // Clear the cart content
                            }
                            // Update the total price
                            updateTotal();
                        });
                    });
                } else {
                    // Show empty cart message if cart is empty
                    emptyCartMessage.classList.remove('d-none');
                }
            });
        </script>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
