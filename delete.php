<?php

// database connection and user authentication
require "includes/auth.php";
require "includes/connect.php";

// make sure an id was passed in the url
if (!isset($_GET['id'])) {
	die("No image ID provided.");
}

$imageId = $_GET['id'];

// get logged in user id from session
$userId = $_SESSION['user_id'];

// get file path first so uploaded file can be removed too
$sql = "SELECT image_path FROM images WHERE image_id = :image_id AND user_id = :user_id";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':image_id', $imageId, PDO::PARAM_INT);
$stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
$stmt->execute();

$image = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$image) {
	die("Image not found.");
}

// sql query to delete image only if it belongs to user
$sql = "DELETE FROM images WHERE image_id = :image_id AND user_id = :user_id";

// prepare sql statement
$stmt = $pdo->prepare($sql);

// bind image id and user id to query
$stmt->bindValue(':image_id', $imageId, PDO::PARAM_INT);
$stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);

// execute delete
$stmt->execute();

// delete physical file if it exists in uploads folder
$filePath = __DIR__ . "/" . $image['image_path'];
if (file_exists($filePath)) {
	unlink($filePath);
}

// redirect back to gallery list
header("Location: gallery.php");
exit;