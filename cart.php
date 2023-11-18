<!DOCTYPE html>
<html>
<head>
    <title>Cart</title>
</head>
<body>
<?php
session_start();
// Assuming $sessionId is your global variable
$loggedIn = isset($_SESSION['id']) && !empty($_SESSION['id']);

// Assuming $cart is an array containing the items in the cart
$cart = [
    "Nötfärs 500g" => ["quantity" => 2, "image_url" => "https://res.cloudinary.com/coopsverige/images/e_sharpen,f_auto,fl_clip,fl_progressive,q_90,c_lpad,g_center,h_330,w_330/v1614683461/423522/N%C3%B6tf%C3%A4rs%2012%25.jpg"],
    "Potatis" => ["quantity" => 1, "image_url" => "https://d1ax460061ulao.cloudfront.net/500x500/5/3/533e3036b273cdca7d6357963fa2f6ae.jpg"]
    // Add more items as needed
];


if ($loggedIn) {
    if (!empty($cart)) {
        // Display the items in the cart
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

        // Add a "Finish Purchase" button
        echo '<button onclick="finishPurchase()">Finish Purchase</button>';

        // Add a "Back to Shop" button
        echo '<button onclick="backToShop()">Back to Shop</button>';
    } else {
        // Display "empty cart" message
        echo '<p>Your cart is empty.</p>';
    }
} else {
    // Display "access denied" message
    echo '<p>Access denied. Please sign in to view your cart.</p>';
}
?>

<script>
    function decreaseQuantity(item) {
        // Implement logic to decrease the quantity of the item
        var quantityElement = document.getElementById('quantity-' + item);
        var quantity = parseInt(quantityElement.innerText);
        if (quantity > 1) {
            quantity--;
            quantityElement.innerText = quantity;
        }
    }

    function increaseQuantity(item) {
        // Implement logic to increase the quantity of the item
        var quantityElement = document.getElementById('quantity-' + item);
        var quantity = parseInt(quantityElement.innerText);
        quantity++;
        quantityElement.innerText = quantity;
    }

    function removeItem(item) {
        // Implement logic to visually remove the item from the cart
        var cartItemElement = document.getElementById('cart-item-' + item);
        cartItemElement.style.display = 'none';

        // If you want to update the cart on the server, you may need to send a request to your backend.
        // Example: sendRemoveItemRequest(item);
    }

    function finishPurchase() {
        // Implement logic to handle finishing the purchase
        alert('Thank you for your purchase!');
        // If needed, redirect the user to a checkout page or handle payment processing.
    }

    function backToShop() {
        // Implement logic to redirect the user back to the shop page
        // Replace 'shop.php' with the actual URL of your shop page
        window.location.href = 'landing_page.php';
    }
</script>

</body>
</html>