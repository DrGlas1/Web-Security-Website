<?php
session_start();
if (!isset($_SESSION['csrf_token'])) {
    die("CSRF token mismatch");
}

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
            unset($cart[$item]);
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
    $loggedIn = isset($_SESSION['id']) && !empty($_SESSION['id']);

    $cart = $_SESSION["cart"];

    if (!$loggedIn) {
      header('location:login.php');
    }

    if (!empty($cart)) : 
    ?>
    <h1>Your Shopping Cart:</h1>
    <ul>
        <?php foreach ($cart as $item => $itemDetails) : ?>
            <?php
            $quantity = $itemDetails['quantity'];
            $image_url = $itemDetails['image_url'];
            ?>
            <li id="cart-item-<?php echo $item; ?>">
                <img src="<?php echo $image_url; ?>" alt="<?php echo $item; ?>" style="width: 50px; height: 50px;">
                <?php echo $item; ?> - Quantity: <span id="quantity-<?php echo $item; ?>"><?php echo $quantity; ?></span>
                <button onclick="decreaseQuantity('<?php echo $item; ?>')">-</button>
                <button onclick="increaseQuantity('<?php echo $item; ?>')">+</button>
                <button onclick="removeItem('<?php echo $item; ?>')">Remove</button>
            </li>
        <?php endforeach; ?>
    </ul>

    <form method="post" action="process_purchase.php">
        <div>Create signature with</div>
        <div>curl -X POST -H "Content-Type: application/json" -d '{"private_key": "your_private_key_value", "amount": "your_total"}' http://127.0.0.1:5000/sign</div>
        <label for="password">Enter signature:</label>
        <input type="enter" name="password" id="password" required>
        <button type="submit" name="checkout">Checkout</button>
    </form>

    <button onclick="backToShop()">Back to shop</button>

<?php else : ?>
    <p>Your cart is empty.</p>
    <button onclick="backToShop()">Back to shop</button>
<?php endif; ?>
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

            updateCart(item, 0);
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

        function backToShop() {
            window.location.href = 'shop.php';
        }
    </script>
    
    </body>
</html>

