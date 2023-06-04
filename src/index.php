<?php
session_start();

// Redirect to dashboard if the user is already logged in
if(isset($_SESSION['user_id'])) {
  if (headers_sent()) {
      echo '<script>window.location.href = "homepage.php";</script>';
      exit();
  } else {
      header("Location: homepage.php");
      exit();
  }
}

// Process login form
if (isset($_POST['email']) && isset($_POST['password'])) {
    // Get form data
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // Validate form data
    if (empty($email) || empty($password)) {
        $error = "Please fill out all fields";
    } else {
        try {
            // Connect to database
            require 'assets/script/db_connect.php';
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Check if user exists and verify password
            $stmt = $conn->prepare("SELECT * FROM users WHERE email = :email");
            $stmt->bindParam(":email", $email);
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $password = $_POST['password'];
            echo "test";

            if ($user && password_verify($password, $user['password']) == TRUE) {
                // Login successful, set session variables and redirect to dashboard
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['user_type'] = $user['role'];
                header('Location: homepage.php');
                exit;
            } else {
                // Invalid credentials
                // $error = "Incorrect username or password";
                echo "Incorrect username or password";
            }
        } catch (PDOException $e) {
            $error = "Connection failed: " . $e->getMessage();
        }
        $conn = null;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login</title>
  <link rel="icon" type="image/png" href="./assets/img/logo.png">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css">
</head>
<body class="bg-gray-900 text-white">
  <nav class="bg-gray-800 p-4">
    <div class="container mx-auto flex items-center justify-between">
      <a class="text-xl font-bold" href="#">Login page</a>
      <ul class="flex">
        <li>
          <a class="text-white" href="./signup.php">Create account</a>
        </li>
      </ul>
    </div>
  </nav>
  <div class="container mx-auto mt-10">
    <div class="max-w-md mx-auto">
      <div class="bg-gray-800 p-8 rounded-lg shadow-lg">
        <h4 class="text-2xl font-bold mb-6">Login</h4>
        <?php if(isset($error)): ?>
          <div class="bg-red-500 text-white px-4 py-2 mb-4 rounded"><?= $error ?></div>
        <?php endif; ?>
        <form action="index.php" method="post">
          <div class="mb-4">
            <label for="email" class="text-white">E-mail</label>
            <input type="email" class="form-input mt-1 block w-full bg-gray-700 rounded" id="email" name="email" required>
          </div>
          <div class="mb-6">
            <label for="password" class="text-white">Password</label>
            <input type="password" class="form-input mt-1 block w-full bg-gray-700 rounded" id="confirm_password" name="password" required>
          </div>
          <div class="flex items-center justify-between">
            <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded">
              Log In
            </button>
            <button class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
              <a href="src/signup.php">Create Account</a>
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</body>
</html>
