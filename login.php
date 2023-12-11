<?php
//Make a config file for you database
if ($_SERVER['HTTPS'] !== 'on') {
  $redirect_url = "https://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
  header("Location: $redirect_url");
  exit();
}

session_start();
//Protects against Cross-Site Request Forgery (CSRF)
$dbHost = "localhost";
$dbPort = "5432";
$dbName = "EITF06-project";
$dbUser = "postgres";
$dbPassword = "password";

//Protects against Cross-Site Request Forgery (CSRF)
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
	$conn = pg_connect("host={$dbConfig['host']} port={$dbConfig['port']} dbname={$dbConfig['dbname']} user={$dbConfig['user']} password={$dbConfig['password']}");
	$authenticated = false;

	
function handleLogin($conn, $user, $pwd) {
	if (!handleBruteForce($conn, $user)) {
		return false;
	}

  $query = 'SELECT id, password, salt FROM users WHERE username = $1';
  $res = pg_query_params($conn, $query, array($user));
  $result = pg_fetch_object($res);

  if ($result) {
    $stored_password = $result->password;
    $salt = $result->salt;
    $authenticated = password_verify($pwd . $salt, $stored_password); //check with salt
    if ($authenticated) {
          $user_id = $result->id;

          $key_query = 'SELECT public_key, encrypted_private_key FROM user_keys WHERE user_id = $1';
          $key_result = pg_query_params($conn, $key_query, array($user_id));
          $key_row = pg_fetch_assoc($key_result);

          $public_key = $key_row['public_key'];
          $encrypted_private_key = $key_row['encrypted_private_key'];

          session_regenerate_id(true);
          $_SESSION['id'] = $user_id; 
          $_SESSION['username'] = $user;
          $_SESSION['public_key'] =  $public_key;
          $_SESSION['encrypted_private_key'] = $encrypted_private_key;
          echo 'Login successful!';
      }
      else {
        echo 'Invalid password!';
      }
  }     else {
    echo 'User not found!';
  }    
  return $authenticated;
}

function createWallet($conn, $reg_user, $reg_pwd, $url) {
  $ch = curl_init($url);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  $response = curl_exec($ch);

  if (curl_errno($ch)) {
      echo 'Curl error: ' . curl_error($ch);
  }
  curl_close($ch);
  $data = json_decode($response, true);

  $_private_key = $data['private_key'];
  $private_key = openssl_encrypt($_private_key, "AES-256-CBC", $reg_pwd, 0, '1234567891011121');
  $public_key = $data['public_key'];

  return array($public_key, $private_key);
}

function isEndpointAccessible($url) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5); // Set a timeout in seconds

    $response = curl_exec($ch);
    $httpStatusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    curl_close($ch);
    return $httpStatusCode === 200;
}

function isPasswordCommon($password) {
    $commonPasswords = file("2k-most-common.txt", FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    return in_array($password, $commonPasswords);
}

function handleBruteForce($conn, $user) {

    // Check if the user has exceeded the maximum number of attempts
    $maxAttempts = 5; // Adjust as needed
    $blockDuration = 300; // 5 minutes

    $query = 'SELECT login_attempts, last_attempt_time FROM users WHERE username = $1';
    $result = pg_query_params($conn, $query, array($user));
    $row = pg_fetch_assoc($result);

    if ($row) {
        $attempts = (int)$row['login_attempts'];
        $lastAttempt = strtotime($row['last_attempt_time']);
        $currentTime = time();

        if ($attempts >= $maxAttempts && ($currentTime - $lastAttempt) < $blockDuration) {
            echo "Too many login attempts. Please try again later.";
            return false;
        }
    }
}

function handleRegistration($conn, $reg_user, $reg_pwd, $reg_adress) {
  $uppercase = preg_match('@[A-Z]@', $reg_pwd);
  $lowercase = preg_match('@[a-z]@', $reg_pwd);
  $number    = preg_match('@[0-9]@', $reg_pwd);
  $specialChars = preg_match('@[^\w]@', $reg_pwd);
  if(!$uppercase || !$lowercase || !$number || !$specialChars || strlen($reg_pwd) < 8) {
      echo 'Password should be at least 8 characters in length and should include at least one upper case letter, one number, and one special character.';
      return;
  }
  $wallet_endpoint = 'http://127.0.0.1:5000/wallet';

  if (!isEndpointAccessible($wallet_endpoint)) {
    echo 'Blockchain server down, registration unsuccesfull';
    return;
  }
  
  if (isPasswordCommon($reg_pwd)) {
    echo "Cannot use $reg_pwd, choose another password";
    return;
  }
	$salt = bin2hex(random_bytes(22)); // 22 bytes for a 32-character salt
	$hashed_password = password_hash($reg_pwd . $salt, PASSWORD_BCRYPT);
	// Insert the user into the database with the hashed password
	$registration_query = 'INSERT INTO users (username, password, home_adress, salt) VALUES ($1, $2, $3, $4) RETURNING id';
	$result = pg_query_params($conn, $registration_query, array($reg_user, $hashed_password, $reg_adress, $salt));
	if ($result !== false) {
    $user_id = pg_fetch_result($result, 0, 0);
    list($public_key, $private_key) = createWallet($conn, $reg_user, $reg_pwd, $wallet_endpoint);
    $key_insert_query = 'INSERT INTO user_keys (user_id, public_key, encrypted_private_key) VALUES ($1, $2, $3)';
    $key_result = pg_query_params($conn, $key_insert_query, array($user_id, $public_key, $private_key));
    echo 'New user successfully registered!';
  } else {
      echo 'Error inserting user into the database: ' . pg_last_error($conn);
  }
}
	if(isset($_POST['verify']) && $_POST['verify'] =='Verify') {
		if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
			die("CSRF token mismatch");
	}
		if($conn) {
			//This is prot against XSS attacks
			$user = htmlspecialchars($_POST['user'], ENT_QUOTES, 'UTF-8');
			$pwd = htmlspecialchars($_POST['pwd'], ENT_QUOTES, 'UTF-8');

			$authenticated = handleLogin($conn, $user, $pwd);
		}

		if(!$authenticated) {
			echo 'Not valid';	
		} else {
			// $cart = [
			// 	"Nötfärs 500g" => ["quantity" => 2, "image_url" => "https://res.cloudinary.com/coopsverige/images/e_sharpen,f_auto,fl_clip,fl_progressive,q_90,c_lpad,g_center,h_330,w_330/v1614683461/423522/N%C3%B6tf%C3%A4rs%2012%25.jpg"],
			// 	"Potatis" => ["quantity" => 1, "image_url" => "https://www.hermelins.se/app/uploads/2018/10/hermelins-potatis.jpg"]
			// 	// Add more items as needed
			// ];
			$_SESSION["cart"] = [];
			header('location:landing_page.php');
		}
	} 
	if(isset($_POST['register']) && $_POST['register'] == 'Register') {
		if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
			die("CSRF token mismatch");
	}
		if($conn) {
			$reg_user = $_POST['reg_user'];
			$reg_pwd = $_POST['reg_pwd'];
      $reg_adress = $_POST['reg_adress'];
			//SQL protection
			$reg_user = pg_escape_string($reg_user);
			$reg_pwd = pg_escape_string($reg_pwd);
      $reg_adress = pg_escape_string($reg_adress);
			handleRegistration($conn, $reg_user, $reg_pwd, $reg_adress);
		}
	}
	
?>

<!DOCTYPE html>
<html>
	<head>
	</head>
	<body>
		<form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post">
			Username: <input type="text" name="user" /><br/>
			Password: <input type="password" name="pwd" /><br/>
			<input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>" />
			<input type="submit" value="Verify" name="verify" />
		</form>
		<form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post">
			Username: <input type="text" name="reg_user" /><br/>
			Password: <input type="password" name="reg_pwd" /><br/>
      Home adress: <input type="text" name="reg_adress" /><br/>
			<input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>" />
			<input type="submit" value="Register" name="register" />
		</form>
	</body>
</html>