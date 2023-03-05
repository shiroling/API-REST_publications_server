<?php
	function getPDOConnection() {
		$host = "153.92.220.151";
		$dbname = "u161682765_LeSport";
		$username = "u161682765_couturiLeclerc";
		$password = "~2Gqv?Kg[9lB";
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
