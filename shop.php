<?php
include 'inventory.php';
session_start();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $itemName = $_POST['item'];

    $cart = $_SESSION["cart"];

    if (array_key_exists($itemName, $inventory)) {
        if (array_key_exists($itemName, $cart)) {
            $cart[$itemName]['quantity']++;
            echo $cart[$itemName]['quantity'];
        } else { 
            $cart[$itemName] = ["quantity" => 1, "image_url" => $inventory[$itemName]['image_url']];
            echo $cart[$itemName]['quantity'];
        }
    }

    $_SESSION["cart"] = $cart;
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
        echo '<td><button onclick="addToCart(\'' . $item . '\')">Add to cart</button></td>';
    }

    echo '</table>';
    echo '<br><a href="cart.php"><button>Go to Cart</button></a>';
?>
<script>
    function addToCart(itemName) {
        var xhr = new XMLHttpRequest();
        xhr.onreadystatechange = function() {
            if (xhr.readyState === XMLHttpRequest.DONE) {
                if (xhr.status === 200) {
                    // Success idk
                } else {
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
