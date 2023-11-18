<!DOCTYPE html>
<html>
<head>
    <title>Cart</title>
</head>
    <body>
    <?php
        session_start();

        $loggedIn = isset($_SESSION['id']) && !empty($_SESSION['id']);

        $cart = [
            "Nötfärs 500g" => ["quantity" => 2, "image_url" => "https://res.cloudinary.com/coopsverige/images/e_sharpen,f_auto,fl_clip,fl_progressive,q_90,c_lpad,g_center,h_330,w_330/v1614683461/423522/N%C3%B6tf%C3%A4rs%2012%25.jpg"],
            "Potatis" => ["quantity" => 1, "image_url" => "https://d1ax460061ulao.cloudfront.net/500x500/5/3/533e3036b273cdca7d6357963fa2f6ae.jpg"]
            // Add more items as needed
        ];


        if ($loggedIn) {
            include 'cart_content.php';
        } else {
            echo '<p>Access denied. Please sign in to view your cart.</p>';
        }

    ?>
    </body>
</html>