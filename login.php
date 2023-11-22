<?php
session_start();
	//Enter own values for $dbHost etc.
  $dbHost="localhost";
  $dbPort="5432";
  $dbName="postgres";
  $dbUser="postgres";
  $dbPassword="lusql";
	$conn = pg_connect("host=$dbHost port=$dbPort dbname=$dbName user=$dbUser password=$dbPassword");
	

	$authenticated = false;
	if(isset($_POST['verify']) && $_POST['verify'] =='Verify') {

		if($conn) {
			$user = $_POST['user'];
			$pwd = $_POST['pwd'];

			$query = 'select * from verify($1, $2)';
			$res = pg_query_params($conn, $query, array($user, $pwd));
			$result = pg_fetch_object($res);
			if ($result) {
				$authenticated = $result->verify!=0;
				$_SESSION['id'] = $result->verify;
			}

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
			$query = 'insert into users (username, password) values ($1, crypt($2, gen_salt(\'bf\')))';
			pg_query_params($conn, $query, array($reg_user, $reg_pwd));
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
			<input type="submit" value="Verify" name="verify" />
		</form>
		<form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post">
			Username: <input type="text" name="reg_user" /><br/>
			Password: <input type="password" name="reg_pwd" /><br/>
			<input type="submit" value="Register" name="register" />
		</form>
	</body>
</html>