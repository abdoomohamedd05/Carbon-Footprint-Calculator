<?php

require_once("../config/session.php");

if (isset($_SESSION["user_id"])) {
    redirect("pages/dashboard.php");
}

include("../includes/header.php");

$error = take_flash("error");
$success = take_flash("success");
?>

<div class="container mt-3">
<?php if ($success): ?>
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <?= e($success) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<?php if ($error): ?>
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <?= e($error) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>
</div>

<div class="container-fluid">
    <div class="row min-vh-100">

        <div class="col-lg-6 d-none d-lg-flex flex-column justify-content-center px-5 left-side">
            <h1 class="display-4 fw-bold">Carbon Footprint AI</h1>
            <p class="mt-3">Sustainability Management Platform</p>

            <div class="feature mt-5"><i class="fas fa-chart-line"></i> Real-Time Carbon Analytics</div>
            <div class="feature"><i class="fas fa-leaf"></i> AI Sustainability Advisor</div>
            <div class="feature"><i class="fas fa-file-pdf"></i> Professional ESG Reports</div>
            <div class="feature"><i class="fas fa-globe"></i> Scope 1 · Scope 2 · Scope 3</div>
        </div>

        <div class="col-lg-6 d-flex justify-content-center align-items-center">
            <div class="login-box">
                <div class="text-center">
                    <div class="logo"><i class="fas fa-leaf"></i></div>
                    <h2 class="mt-3">Welcome Back</h2>
                    <p class="subtitle">Login to continue</p>
                </div>

                <form action="login_process.php" method="POST" id="loginForm">
                    <div class="input-group mt-4">
                        <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                        <input type="email" name="email" class="form-control" placeholder="Email Address" required>
                    </div>

                    <div class="input-group mt-3">
                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                        <input type="password" id="password" name="password" class="form-control" placeholder="Password" required>
                        <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('password')">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>

                    <div class="d-flex justify-content-between mt-3 auth-meta">
                        <label><input type="checkbox" name="remember"> Remember Me</label>
                        <span class="text-muted small">Contact admin for password reset</span>
                    </div>

                    <button class="btn btn-login w-100 mt-4" id="loginBtn">Login</button>

                    <div class="text-center mt-4">
                        Don't have an account?
                        <a href="register.php">Register</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include("../includes/footer.php"); ?>
