<?php
// start or resume the session
session_start();

// connect to database
require "includes/connect.php";

// include site header
require "includes/header.php";

// variable to store login errors
$error = "";

// only run login if form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // get login and remove extra spaces
	$usernameOrEmail = trim($_POST['username_or_email'] ?? '');
    // get password from form
	$password = $_POST['password'] ?? '';
    
    // basic validation
	if ($usernameOrEmail === '' || $password === '') {
		$error = "Username/email and password are required.";
	} else {
        // look for matching username or email
		$sql = "SELECT id, username, email, password FROM users WHERE username = :login OR email = :login LIMIT 1";
		$stmt = $pdo->prepare($sql);
		$stmt->bindValue(':login', $usernameOrEmail);
		
        // run query
        $stmt->execute();

        // fetch matching user
		$user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // check if user exists and password matches hashed password
		if ($user && password_verify($password, $user['password'])) {
			// regenerate session id for security
            session_regenerate_id(true);

			// store login info in session
            $_SESSION['user_id'] = $user['id'];
			$_SESSION['username'] = $user['username'];
            
            // send user to players page after login
			header("Location: gallery.php");
			exit;
		} else {
			$error = "Invalid credentials. Please try again.";
		}
	}
}
?>
<!-- login form -->
<main class="container mt-4">
	<h2>Login</h2>

	<?php if ($error !== ""): ?>
		<div class="alert alert-danger">
			<?= htmlspecialchars($error); ?>
		</div>
	<?php endif; ?>

	<form method="post" class="mt-3">
		<label for="username_or_email" class="form-label">Username or email</label>
		<input
			type="text"
			id="username_or_email"
			name="username_or_email"
			class="form-control mb-3"
		>

		<label for="password" class="form-label">Password</label>
		<input
			type="password"
			id="password"
			name="password"
			class="form-control mb-4"
		>

		<button type="submit" class="btn btn-primary">Login</button>
	</form>
</main>

<?php require "includes/footer.php"; ?>