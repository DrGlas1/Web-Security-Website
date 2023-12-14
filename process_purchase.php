<?php
  session_start();
  function send_transaction($signature, $public_key, $price) {
    $date = $_SESSION['date'];
    $url = 'http://127.0.0.1:5000/txion';
    $data = array(
        'from' => $public_key,
        'to' => 'RW+/MCUJQ+aOkccfnOYyriSqsjFfQPzzxRMUUZvC0XWfORXhCzALw9jALirucEhhSJZo3agbM69lLMU30kGCHw==',
        'amount' => $price,
        'signature' => $signature,
        'message' => $price . $date
    );

    $json_data = json_encode($data);
    $ch = curl_init($url);

    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);

    $response = curl_exec($ch);
    if (curl_errno($ch)) {
        echo 'Error: ' . curl_error($ch);
    }
    curl_close($ch);
    return $response;
  }

  function checkout($signature, $price) {
      $public_key = $_SESSION['public_key'];
      return send_transaction($signature, $public_key, $price);
  }

  if ($_SERVER["REQUEST_METHOD"] == "POST") {
      $signature = $_POST["signature"];
      $cart = $_SESSION["cart"];
      $amount = 0;
      foreach ($cart as $item => $itemDetails) {
        $price = ($item == "Potatis") ? 30 : 100;
        $amount += $itemDetails['quantity'] * $price;
      }

    $checkout_result = checkout($signature, $amount);
    if ((strpos($checkout_result, 'Transaction submission successful') !== false)) {
        echo "<h1>Receipt</h1>";
        echo "<p>Items purchased:</p>";
        echo "<ul>";
        foreach ($cart as $item => $itemDetails) {
            $subtotal = $itemDetails['quantity'] * $price;
            echo "<li>{$itemDetails['quantity']} x {$item} - {$subtotal} kr</li>";
            $itemDetails['quantity'] = 0;
        }
        echo "</ul>";
        echo "<p>Total amount: {$amount} kr</p>";
        echo "<a href='shop.php'><button>Back to Shop</button></a>";

        // Remove all our goods from cart
        $_SESSION['cart'] = [];
    } else {
        echo "<h1>Error</h1>";
        echo "<p>There was an error processing your order. Please try again later.</p>";
    }
  }
?>