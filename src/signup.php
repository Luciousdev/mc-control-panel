<?php
// Initialize session
session_start();
// Enable error reporting
error_reporting(E_ALL);

// Include database connection
require_once "assets/script/db_connect.php";

// Define variables and initialize with empty values
$email = $password = $confirm_password = "";
$email_err = $password_err = $confirm_password_err = "";

// Process form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Validate full name
    if (empty(trim($_POST["email"]))) {
        $email_err = "Please enter your full name.";
    } else {
        $email = trim($_POST["email"]);
    }

    // Validate password
    if (empty(trim($_POST["password"]))) {
        $password_err = "Please enter a password.";
    } elseif (strlen(trim($_POST["password"])) < 8) {
        $password_err = "Password must have at least 8 characters.";
    } else {
        $password = trim($_POST["password"]);
    }

    // Validate confirm password
    if (empty(trim($_POST["confirm_password"]))) {
        $confirm_password_err = "Please confirm password.";
    } else {
        $confirm_password = trim($_POST["confirm_password"]);
        if (empty($password_err) && ($password != $confirm_password)) {
            $confirm_password_err = "Password did not match.";
        }
    }
    $username = $_POST["username"];

    // Check if email already exists
    if (empty($email_err) && empty($password_err) && empty($confirm_password_err)) {
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = :email");
        $stmt->bindParam(":email", $email, PDO::PARAM_STR);

        if ($stmt->execute() && $stmt->rowCount() > 0) {
            $email_err = "This email already exists. Please choose a different one.";
        } else {
            $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (:username, :email, :password)");
            $stmt->bindParam("username", $username, PDO::PARAM_STR);
            $stmt->bindParam(":email", $email, PDO::PARAM_STR);
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt->bindParam(":password", $hashed_password, PDO::PARAM_STR);

            if ($stmt->execute()) {
                header("location: index.php");
                exit();
            } else {
                echo "Oops! Something went wrong. Please try again later.";
            }
        }
    }

    // Close connection
    unset($conn);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign up form</title>
    <link rel="icon" type="image/png" href="./assets/img/logo.png">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css">
</head>
<body class="bg-gray-900 text-white">
<nav class="bg-gray-800 p-4">
    <div class="container mx-auto flex items-center justify-between">
        <a class="text-xl font-bold" href="#">Sign up</a>
        <ul class="flex">
            <li>
                <a class="text-white" href="index.php">Log in</a>
            </li>
        </ul>
    </div>
</nav>
<div class="container mx-auto mt-5">
    <div class="max-w-md mx-auto">
        <div class="bg-gray-800 p-8 rounded-lg shadow-lg">
            <h3 class="text-2xl font-bold mb-6">Sign up form</h3>
            <form action="signup.php" method="post">
            <div class="mb-4">
                    <label for="username" class="text-white">Username</label>
                    <input type="text" class="form-input mt-1 block w-full bg-gray-700 rounded" id="username" name="username" required>
                </div>
                <div class="mb-4">
                    <label for="first_name" class="text-white">E-mail</label>
                    <input type="text" class="form-input mt-1 block w-full bg-gray-700 rounded" id="first_name" name="email" required>
                </div>
                <div class="mb-4">
                    <label for="password" class="text-white">Password:</label>
                    <input type="password" class="form-input mt-1 block w-full bg-gray-700 rounded" id="password" name="password" required>
                </div>
                <div class="mb-6">
                    <label for="confirm_password" class="text-white">Confirm password:</label>
                    <input type="password" class="form-input mt-1 block w-full bg-gray-700 rounded" id="confirm_password" name="confirm_password" required>
                </div>
                <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded">
                    Signup
                </button>
            </form>
        </div>
    </div>
</div>
</body>
</html>

