<?php
  session_start();
  function get_signature($private_key) {
    $post_data = array('private_key' => $private_key);

    $ch = curl_init('http://127.0.0.1:5000/sign');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($post_data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
    curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1); 
    $response = curl_exec($ch);
    if (curl_errno($ch)) {
        echo 'Curl error: ' . curl_error($ch);
    }
    curl_close($ch);
    $response_data = json_decode($response, true);
    return $response_data;
}

  function send_transaction($private_key, $public_key, $price) {
    $res = get_signature($private_key);
    $signature = $res['signature'];
    $message = $res['message'];
    $url = 'http://127.0.0.1:5000/txion';
    $data = array(
        'from' => $public_key,
        'to' => 'RW+/MCUJQ+aOkccfnOYyriSqsjFfQPzzxRMUUZvC0XWfORXhCzALw9jALirucEhhSJZo3agbM69lLMU30kGCHw==',
        'amount' => $price,
        'signature' => $signature,
        'message' => $message
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

  function checkout($entered_password, $price) {
      $public_key = $_SESSION['public_key'];
      $encrypted_private_key = $_SESSION['encrypted_private_key'];
      $private_key = openssl_decrypt($encrypted_private_key, "AES-256-CBC", $entered_password, 0, '1234567891011121');
      return send_transaction($private_key, $public_key, $price);
  }

  if ($_SERVER["REQUEST_METHOD"] == "POST") {
      $entered_password = $_POST["password"];
      $cart = $_SESSION["cart"];
      $amount = 0;
      foreach ($cart as $item => $itemDetails) {
        $price = ($item == "Potatis") ? 30 : 100;
        $amount += $itemDetails['quantity'] * $price;
    }

    echo "$amount" . '<br>';

    $checkout_result = checkout($entered_password, $amount);

    if ($checkout_result !== false) {
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