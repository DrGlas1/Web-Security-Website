<?php
session_start();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $itemName = $_POST['item'];

    $cart = $_SESSION["cart"];

    // Update the quantity for the specified item
    if (array_key_exists($itemName, $cart)) {
        $cart[$itemName]['quantity']++;
        // Return the updated quantity as a response
        echo $cart[$itemName]['quantity'];
    }

    $_SESSION["cart"] = $cart;
    // You can store the updated cart in a session variable or database for future use
    // For now, the cart will revert to its original state upon page reload
}
?>


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

    echo '</table>';
    echo '<br><a href="cart.php"><button>Go to Cart</button></a>';
?>
<script>
    function increaseQuantity(itemName) {
        // Send an AJAX request to update the quantity in PHP
        var xhr = new XMLHttpRequest();
        xhr.onreadystatechange = function() {
            if (xhr.readyState === XMLHttpRequest.DONE) {
                if (xhr.status === 200) {
                    // Quantity updated successfully, update the UI if needed
                    // For instance, you might update the displayed quantity
                    // in the user interface
                    //updateUI(itemName, xhr.responseText);
                } else {
                    // Handle error response
                    console.error('Error:', xhr.status);
                }
            }
        };

        // Prepare the data to be sent to PHP
        var formData = new FormData();
        formData.append('item', itemName); // Identify the item to update in PHP

        // Send the request to the PHP script that handles quantity updates
        xhr.open('POST', 'shop.php', true);
        xhr.send(formData);
    }
</script>
</body>
</html>
