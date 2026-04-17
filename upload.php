<?php
// upload image form processing
require "includes/auth.php";
require "includes/connect.php";
require "includes/header.php";

// arrays to store errors and success message
$errors = [];
$success = "";

// script only runs when form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

	// sanitize user input
	$title = trim(filter_input(INPUT_POST, 'title', FILTER_SANITIZE_SPECIAL_CHARS));

	// validate user input on server
	if ($title === '') {
		$errors[] = "Image title is required.";
	}

	// make sure a file was selected
	if (empty($_FILES['gallery_image']['name'])) {
		$errors[] = "Please choose an image file.";
	}

	// allowed image types
	$allowedTypes = ['image/jpeg', 'image/png'];

	// check file upload only if no errors so far
	if (empty($errors)) {
		$file = $_FILES['gallery_image'];

		// make sure file uploaded properly and is image
		if ($file['error'] !== 0) {
			$errors[] = "There was a problem uploading the file.";
		} elseif (!in_array($file['type'], $allowedTypes)) {
			$errors[] = "Only JPG, PNG, and GIF images are allowed.";
		}
	}

	// only upload and insert if no errors
	if (empty($errors)) {

		// get file name and set upload path
		$fileName = time() . "_" . basename($file['name']);
		$uploadPath = __DIR__ . "/uploads/" . $fileName;

		// move file from temp folder to uploads folder
		if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
			$imagePath = "uploads/" . $fileName;

			// insert image data into table
			$sql = "INSERT INTO images (title, image_path, user_id) VALUES (:title, :image_path, :user_id)";
			$stmt = $pdo->prepare($sql);

			// bind image info
			$stmt->bindValue(':title', $title);
			$stmt->bindValue(':image_path', $imagePath);
			$stmt->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
			$stmt->execute();

			$success = "Image uploaded successfully.";
		} else {
			$errors[] = "The image could not be moved to the uploads folder.";
		}
	}
}
?>

<main class="container mt-4">
	<h2>Upload Image</h2>

	<?php if (!empty($errors)): ?>
		<div class="alert alert-danger">
			<ul class="mb-0">
				<?php foreach ($errors as $error): ?>
					<li><?= htmlspecialchars($error); ?></li>
				<?php endforeach; ?>
			</ul>
		</div>
	<?php endif; ?>

	<?php if ($success !== ""): ?>
		<div class="alert alert-success">
			<?= htmlspecialchars($success); ?>
		</div>
	<?php endif; ?>

	<!-- form input for image title and file -->
	<form method="post" enctype="multipart/form-data" class="mt-3">
		<label for="title" class="form-label">Image Title</label>
		<input
			type="text"
			id="title"
			name="title"
			class="form-control mb-3"
			value="<?= htmlspecialchars($title ?? ''); ?>"
		>

		<label for="gallery_image" class="form-label">Choose Image</label>
		<input
			type="file"
			id="gallery_image"
			name="gallery_image"
			class="form-control mb-4"
		>

		<button type="submit" class="btn btn-primary">Upload Image</button>
		<a href="gallery.php" class="btn btn-secondary">Back to Gallery</a>
	</form>
</main>

<?php require "includes/footer.php"; ?>