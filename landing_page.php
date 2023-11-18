<?php
    session_start();
    if (isset($_SESSION['id'])) {
        $id = $_SESSION['id'];
        echo "This is the landing page, welcome user $id";
        echo '<br><a href="cart.php"><button>Go to Cart</button></a>';
    } else {
        echo "This is the landing page, but no user is authenticated.";
    }
?>
