<?php
	function getPDOConnection() {
		$host = "localhost";
		$dbname = "rest_bdd_despaux_couturier";
		$username = "user";
		$password = "pass";
		try {
			$conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
			// set the PDO error mode to exception
			$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			return $conn;
		}
		catch(PDOException $e) {
			echo "Connection failed: " . $e->getMessage();
		}
	}
?>
