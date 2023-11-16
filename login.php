<?php
	session_start();
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
