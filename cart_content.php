<h1>Your Shopping Cart:</h1>
<ul>
    <?php
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
    ?>
</ul>

<button onclick="finishPurchase()">Checkout</button>

<button onclick="backToShop()">Back to Shop</button>
