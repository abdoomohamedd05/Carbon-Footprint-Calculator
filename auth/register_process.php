<?php

require_once("../config/database.php");
require_once("../config/session.php");

if ($_SERVER["REQUEST_METHOD"] != "POST") {
    redirect("auth/register.php");
}

$full_name = trim($_POST["full_name"] ?? "");
$email     = trim($_POST["email"] ?? "");
$password  = $_POST["password"] ?? "";
$confirm   = $_POST["confirm_password"] ?? "";

if ($full_name === "" || $email === "" || $password === "" || $confirm === "") {
    flash("error", "Please fill all fields.");
    redirect("auth/register.php");
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    flash("error", "Invalid email address.");
    redirect("auth/register.php");
}

if (strlen($password) < 8) {
    flash("error", "Password must be at least 8 characters.");
    redirect("auth/register.php");
}

if ($password !== $confirm) {
    flash("error", "Passwords do not match.");
    redirect("auth/register.php");
}

$stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
$stmt->execute([$email]);

if ($stmt->fetch()) {
    flash("error", "Email already exists.");
    redirect("auth/register.php");
}

$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

$stmt = $conn->prepare("INSERT INTO users (full_name, email, password) VALUES (?, ?, ?)");

if ($stmt->execute([$full_name, $email, $hashedPassword])) {
    $userId = $conn->lastInsertId();
    $log = $conn->prepare("INSERT INTO activity_logs (user_id, activity) VALUES (?, ?)");
    $log->execute([$userId, "New user registered: {$full_name}"]);

    flash("success", "Account created successfully. Please login.");
    redirect("auth/login.php");
}

flash("error", "Something went wrong.");
redirect("auth/register.php");
