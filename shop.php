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
      echo '<td><form method="post" action="cart.php">
                <input type="hidden" name="item" value="' . $item . '">
                <input type="submit" name="add_to_cart" value="Add to Cart">
              </form></td>';
      echo '</tr>';
  }

  echo '</table>';
  echo '<br><a href="cart.php"><button>Go to Cart</button></a>';
?>
</body>
</html>
