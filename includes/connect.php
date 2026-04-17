<?php
$dsn = "mysql:host=172.31.22.43;dbname=Matt200165243;charset=utf8mb4";
$username = "Matt200165243";
$password = "kPMW8lgdOZ";

try {
	$pdo = new PDO($dsn, $username, $password);
	$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
	die("Database connection failed: " . $e->getMessage());
}
?>