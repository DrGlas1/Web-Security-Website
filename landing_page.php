<?php
    session_start();
    if (isset($_SESSION['id'])) {
        $id = $_SESSION['id'];
        echo "This is the landing page, welcome user $id";
        echo '<br><a href="shop.php"><button>Start shopping</button></a>';
        echo '<br><a href="cart.php"><button>Go to Cart</button></a>';
        echo '<br><a href="logout.php"><button>Log Out</button></a>'; 
    } else {
        echo "This is the landing page, but no user is authenticated.";
    }
?>
