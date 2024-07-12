<?php

if (empty($_POST["user_name"])) {
    die("Username is required");
}

if (strlen($_POST["password"]) < 8) {
    die("Password must be at least 8 characters");
}

if (!preg_match("/[a-z]/i", $_POST["password"])) {
    die("Password must contain at least one letter");
}

if (!preg_match("/[0-9]/", $_POST["password"])) {
    die("Password must contain at least one number");
}

if ($_POST["password"] !== $_POST["password_confirmation"]) {
    die("Passwords must match");
}

// Sanitize user inputs
$userName = htmlspecialchars($_POST["user_name"]);
$password = htmlspecialchars($_POST["password"]);
$passwordConfirmation = htmlspecialchars($_POST["password_confirmation"]);

$password_hash = password_hash($password, PASSWORD_DEFAULT);

$mysqli = require __DIR__ . "/database.php";

$sql = "INSERT INTO users (user_name,  password_hash,user_elo)
        VALUES (?, ?, 1000)";

$stmt = $mysqli->stmt_init();

if (!$stmt->prepare($sql)) {
    die("SQL error: " . $mysqli->error);
}

$stmt->bind_param("ss", $userName, $password_hash);

if ($stmt->execute()) {

    header("Location: signup-success.html");
    exit;

} else {

    if ($mysqli->errno === 1062) {
        die("Username already taken");
    } else {
        die($mysqli->error . " " . $mysqli->errno);
    }
}
