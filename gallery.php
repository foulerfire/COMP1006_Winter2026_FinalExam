<?php

// site header authentication and database connection
require "includes/auth.php";
require "includes/connect.php";
require "includes/header.php";

$userId = $_SESSION['user_id'];

// sql query to retrieve all images from database for user that's logged in
$sql = "SELECT * FROM images WHERE user_id = :user_id ORDER BY image_id DESC";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
$stmt->execute();

// fetch rows from the result as array
$images = $stmt->fetchAll();
?>

<main class="container mt-4">
	<h2>My Gallery</h2>

	<!-- link to upload form -->
	<a href="upload.php" class="btn btn-primary mb-4">Upload New Image</a>

	<!-- if no images exist display a message -->
	<?php if (empty($images)): ?>
		<p>No images have been uploaded yet.</p>
	<?php else: ?>

		<div class="row">
			<?php
			// loop through each image returned from the database and output for each one
			?>
			<?php foreach ($images as $image): ?>
				<div class="col-md-4 mb-4">
					<div class="card h-100">
						<!-- output uploaded image -->
						<img
							src="<?= htmlspecialchars($image['image_path']); ?>"
							class="card-img-top"
							alt="<?= htmlspecialchars($image['title']); ?>"
						>

						<div class="card-body">
							<h5 class="card-title"><?= htmlspecialchars($image['title']); ?></h5>

							<!-- delete button sends image id to delete.php -->
							<a
								href="delete.php?id=<?= urlencode($image['image_id']); ?>"
								class="btn btn-danger btn-sm"
								onclick="return confirm('Are you sure you want to delete this image?');"
							>
								Delete
							</a>
						</div>
					</div>
				</div>
			<?php endforeach; ?>
		</div>

	<?php endif; ?>
</main>

<?php require "includes/footer.php"; ?>