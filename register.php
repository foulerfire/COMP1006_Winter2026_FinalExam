<?php

// include database connection
require "includes/connect.php";

// include site header
require "includes/header.php";

// array to store validation errors
$errors = [];
//variable to stroe success message
$success = "";

//check if form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    //santitze input
	$username = trim(filter_input(INPUT_POST, 'username', FILTER_SANITIZE_SPECIAL_CHARS));
	$email = trim(filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL));

	//passwords not sanitized in case special characters are used
    $password = $_POST['password'] ?? '';
	$confirm_password = $_POST['confirm_password'] ?? '';

    //validate username
	if ($username === '') {
		$errors[] = "Username is required.";
	}
    //validate email
	if ($email === '') {
		$errors[] = "Email is required.";
	} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
		$errors[] = "Please enter a valid email address.";
	}
    //validate password
	if ($password === '') {
		$errors[] = "Password is required.";
	}

	if ($confirm_password === '') {
		$errors[] = "Please confirm your password.";
	}

	if ($password !== $confirm_password) {
		$errors[] = "Passwords do not match.";
	}
    
    // only check database if no validation errors
	if (empty($errors)) {
		$sql = "SELECT id FROM users WHERE username = :username OR email = :email";
		$stmt = $pdo->prepare($sql);
		$stmt->bindValue(':username', $username);
		$stmt->bindValue(':email', $email);
		$stmt->execute();

		if ($stmt->fetch()) {
			$errors[] = "That username or email is already in use.";
		}
	}
    
    // insert new user if no errors
	if (empty($errors)) {
		$hashed_password = password_hash($password, PASSWORD_DEFAULT);

		$sql = "INSERT INTO users (username, email, password) VALUES (:username, :email, :password)";
		$stmt = $pdo->prepare($sql);
		$stmt->bindValue(':username', $username);
		$stmt->bindValue(':email', $email);
		$stmt->bindValue(':password', $hashed_password);
		$stmt->execute();

		$success = "Account created successfully. You can now log in.";
	}
}
?>

<main class="container mt-4">
	<h2>Register</h2>

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

	<form method="post" class="mt-3">
		<label for="username" class="form-label">Username</label>
		<input
			type="text"
			id="username"
			name="username"
			class="form-control mb-3"
			value="<?= htmlspecialchars($username ?? ''); ?>"
		>

		<label for="email" class="form-label">Email</label>
		<input
			type="email"
			id="email"
			name="email"
			class="form-control mb-3"
			value="<?= htmlspecialchars($email ?? ''); ?>"
		>

		<label for="password" class="form-label">Password</label>
		<input
			type="password"
			id="password"
			name="password"
			class="form-control mb-3"
		>

		<label for="confirm_password" class="form-label">Confirm Password</label>
		<input
			type="password"
			id="confirm_password"
			name="confirm_password"
			class="form-control mb-4"
		>

		<button type="submit" class="btn btn-primary">Create Account</button>
		<a href="login.php" class="btn btn-secondary">Login Instead</a>
	</form>
</main>

<?php require "includes/footer.php"; ?>