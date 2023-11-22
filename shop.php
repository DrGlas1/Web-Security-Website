<!DOCTYPE html>
<html>
<head>
    <title>Shop</title>
</head>
<body>

<h1>Welcome to the shop!</h1>

<?php
    include 'inventory.php';

    echo '<table border="1">';
    //echo '<tr><th>Item</th><th>Price</th><th>Image</th></tr>';

    foreach ($inventory as $item => $details) {
        echo '<tr>';
        echo '<td>' . $item . '</td>';
        echo '<td>' . $details['price'] . ' kr</td>';
        echo '<td><img src="' . $details['image_url'] . '" alt="' . $item . '" height="100"></td>';
        echo '<td><button onclick="increaseQuantity(\'' . $item . '\')">Add to cart</button></td>';
    }

    if(isset($_POST['add_to_cart'])) {
        // Call the update_cart function and pass the item name
        if(isset($_POST['item'])) {
            $itemName = $_POST['item'];
            update_cart($itemName, 7);
        }
    }

    echo '</table>';
    echo '<br><a href="cart.php"><button>Go to Cart</button></a>';
?>
<script>
    function increaseQuantity(item) {
        console.log(item);
    }
</script>
</body>
</html>
