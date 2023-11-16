<?php
	session_start();
	if (isset($_SESSION['id'])) {
		$id = $_SESSION['id'];
		echo "This is the landing page, welcome user $id";
	} else {
		echo "This is the landing page, but no user is authenticated.";
	}
?>
