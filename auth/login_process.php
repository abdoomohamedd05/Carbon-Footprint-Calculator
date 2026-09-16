<?php

require_once("../config/database.php");
require_once("../config/session.php");

if ($_SERVER["REQUEST_METHOD"] != "POST") {
    redirect("auth/login.php");
}

$email = trim($_POST["email"] ?? "");
$password = $_POST["password"] ?? "";

if ($email === "" || $password === "") {
    flash("error", "Please enter email and password.");
    redirect("auth/login.php");
}

$stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
$stmt->execute([$email]);
$user = $stmt->fetch();

if ($user && password_verify($password, $user["password"])) {
    session_regenerate_id(true);

    $_SESSION["user_id"] = $user["id"];
    $_SESSION["full_name"] = $user["full_name"];
    $_SESSION["email"] = $user["email"];
    $_SESSION["role"] = $user["role"];

    log_activity($conn, "User logged in");
    redirect("pages/dashboard.php");
}

flash("error", $user ? "Incorrect password." : "Email not found.");
redirect("auth/login.php");
