<?php
session_start();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $item = $_POST['item'];
    $quantity = $_POST['quantity'];

    if (isset($_SESSION['cart'])) {
        $cart = $_SESSION['cart'];

        // Update the quantity in the cart
        if ($quantity > 0) {
            $cart[$item]['quantity'] = $quantity;
        } else {
            // Remove the item from the cart if quantity is 0
            //unset($cart[$item]);
        }

        // Update the cart in the session
        $_SESSION['cart'] = $cart;

        echo 'Cart updated successfully';
    } else {
        echo 'Error: Cart not found in session';
    }
} else {
    echo 'Error: Invalid request';
}
?>


<!DOCTYPE html>
<html>
<head>
    <title>Cart</title>
</head>
    <body>
    <?php
        session_start();

        $loggedIn = isset($_SESSION['id']) && !empty($_SESSION['id']);

        $cart = $_SESSION["cart"];

        if ($loggedIn) {
            if (!empty($cart)) {
                echo '<h1>Your Shopping Cart:</h1>';
                echo '<ul>';
                foreach ($cart as $item => $itemDetails) {
                    $quantity = $itemDetails['quantity'];
                    $image_url = $itemDetails['image_url'];
        
                    echo '<li id="cart-item-' . $item . '">
                            <img src="' . $image_url . '" alt="' . $item . '" style="width: 50px; height: 50px;">
                            ' . $item . ' - Quantity: <span id="quantity-' . $item . '">' . $quantity . '</span>
                            <button onclick="decreaseQuantity(\'' . $item . '\')">-</button>
                            <button onclick="increaseQuantity(\'' . $item . '\')">+</button>
                            <button onclick="removeItem(\'' . $item . '\')">Remove</button>
                        </li>';
                }
                echo '</ul>';
        
                echo '<button onclick="finishPurchase()">Checkout</button>';
        
                echo '<button onclick="backToShop()">Back to landing page</button>';
            } else {
                echo '<p>Your cart is empty.</p>';
            }
        } else {
            echo '<p>Access denied. Please sign in to view your cart.</p>';
        }

    ?>
    <script>
        function decreaseQuantity(item) {
            var quantityElement = document.getElementById('quantity-' + item);
            var quantity = parseInt(quantityElement.innerText);
            if (quantity > 1) {
                quantity--;
                quantityElement.innerText = quantity;
                updateCart(item, quantity);
            }
        }

        function increaseQuantity(item) {
            var quantityElement = document.getElementById('quantity-' + item);
            var quantity = parseInt(quantityElement.innerText);
            quantity++;
            quantityElement.innerText = quantity;
            updateCart(item, quantity);
        }

        function removeItem(item) {
            var cartItemElement = document.getElementById('cart-item-' + item);
            cartItemElement.style.display = 'none';

            updateCart(item, 0); // Set quantity to 0 for removal
        }

        function updateCart(item, quantity) {
            var xhr = new XMLHttpRequest();
            xhr.open('POST', 'cart.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onreadystatechange = function () {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    console.log(xhr.responseText);
                }
            };
            xhr.send('item=' + encodeURIComponent(item) + '&quantity=' + encodeURIComponent(quantity));
        }

        function finishPurchase() {
            alert('Thank you for your purchase!');
        }

        function backToShop() {
            window.location.href = 'landing_page.php';
        }
    </script>
    
    </body>
</html>

