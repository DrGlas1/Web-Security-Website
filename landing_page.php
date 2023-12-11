<?php
    session_start();
    //CRSF check
    if (!isset($_SESSION['csrf_token'])) {
        die("CSRF token mismatch");
    }
    if (isset($_SESSION['id'])) {
        $username = $_SESSION['username'];
        echo "This is the landing page, welcome user $username";
        echo '<br><a href="shop.php"><button>Start shopping</button></a>';
        echo '<br><a href="cart.php"><button>Go to Cart</button></a>';
        echo '<br><a href="logout.php"><button>Log Out</button></a>'; 
    } else {
        header('location:login.php');
    }
?>
