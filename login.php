
<?php
//Make a config file for you database
include('dbconfig.php');
session_start();
//Protects against Cross-Site Request Forgery (CSRF)
	$csrfToken = bin2hex(random_bytes(32));
	$_SESSION['csrf_token'] = $csrfToken;
	$conn = pg_connect("host={$dbConfig['host']} port={$dbConfig['port']} dbname={$dbConfig['dbname']} user={$dbConfig['user']} password={$dbConfig['password']}");
	$authenticated = false;

	
function handleLogin($conn, $user, $pwd) {
	$query = 'SELECT id, password FROM users WHERE username = $1';
	$res = pg_query_params($conn, $query, array($user));
	$result = pg_fetch_object($res);

	if ($result) {
			$authenticated = password_verify($pwd, $result->password);
			if ($authenticated) {
					session_regenerate_id();
					$_SESSION['id'] = $result->id; 
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

function handleRegistration($conn, $reg_user, $reg_pwd) {
	// Hash the password using Bcrypt
	$salt = bin2hex(random_bytes(22)); // 22 bytes for a 32-character salt
	$hashed_password = password_hash($reg_pwd . $salt, PASSWORD_BCRYPT);
	// Insert the user into the database with the hashed password
	$query = 'INSERT INTO users (username, password, salt) VALUES ($1, $2, $3)';
	pg_query_params($conn, $query, array($reg_user, $hashed_password, $salt)); //You need to pass this into ALTER TABLE users ADD COLUMN salt VARCHAR(44);
}
	if(isset($_POST['verify']) && $_POST['verify'] =='Verify') {
		if($conn) {
			$user = $_POST['user'];
			$pwd = $_POST['pwd'];
			$user = pg_escape_string($user);
			$pwd = pg_escape_string($pwd);
			$authenticated = handleLogin($conn, $user, $pwd);
		}

		if(!$authenticated) {
			echo 'Not valid';	
		} else {
			$cart = [
				"Nötfärs 500g" => ["quantity" => 2, "image_url" => "https://res.cloudinary.com/coopsverige/images/e_sharpen,f_auto,fl_clip,fl_progressive,q_90,c_lpad,g_center,h_330,w_330/v1614683461/423522/N%C3%B6tf%C3%A4rs%2012%25.jpg"],
				"Potatis" => ["quantity" => 1, "image_url" => "https://www.hermelins.se/app/uploads/2018/10/hermelins-potatis.jpg"]
				// Add more items as needed
			];
			$_SESSION["cart"] = $cart;
			header('location:landing_page.php');
		}
	} 
	if(isset($_POST['register']) && $_POST['register'] == 'Register') {
		if($conn) {
			$reg_user = $_POST['reg_user'];
			$reg_pwd = $_POST['reg_pwd'];
			//SQL protection
			$reg_user = pg_escape_string($reg_user);
			$reg_pwd = pg_escape_string($reg_pwd);
			handleRegistration($conn, $reg_user, $reg_pwd);
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
			<input type="hidden" name="csrf_token" value="<?php echo bin2hex(random_bytes(32)); ?>" />
			<input type="submit" value="Verify" name="verify" />
		</form>
		<form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post">
			Username: <input type="text" name="reg_user" /><br/>
			Password: <input type="password" name="reg_pwd" /><br/>
			<input type="hidden" name="csrf_token" value="<?php echo bin2hex(random_bytes(32)); ?>" />
			<input type="submit" value="Register" name="register" />
		</form>
	</body>
</html>