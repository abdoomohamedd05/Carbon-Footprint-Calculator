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
<?php if ($error): ?>
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <?= e($error) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>
<?php if ($success): ?>
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <?= e($success) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>
</div>

<div class="container-fluid">
    <div class="row min-vh-100">

        <div class="col-lg-6 d-none d-lg-flex flex-column justify-content-center px-5 left-side">
            <h1 class="display-4 fw-bold">Join Carbon Footprint AI</h1>
            <p class="mt-3">Create your account and start measuring your company's environmental impact.</p>

            <div class="feature mt-5"><i class="fas fa-check-circle"></i> Carbon Footprint Assessment</div>
            <div class="feature"><i class="fas fa-chart-pie"></i> ESG Performance Dashboard</div>
            <div class="feature"><i class="fas fa-robot"></i> AI Sustainability Advisor</div>
            <div class="feature"><i class="fas fa-file-pdf"></i> Professional Reports</div>
        </div>

        <div class="col-lg-6 d-flex justify-content-center align-items-center">
            <div class="login-box">
                <div class="text-center">
                    <div class="logo"><i class="fas fa-user-plus"></i></div>
                    <h2 class="mt-3">Create Account</h2>
                    <p class="subtitle">Register to continue</p>
                </div>

                <form action="register_process.php" method="POST" id="registerForm">
                    <div class="input-group mt-4">
                        <span class="input-group-text"><i class="fas fa-user"></i></span>
                        <input type="text" name="full_name" class="form-control" placeholder="Full Name" required>
                    </div>

                    <div class="input-group mt-3">
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

                    <div class="progress mt-2" style="height:8px;">
                        <div id="strengthBar" class="progress-bar" style="width:0%;"></div>
                    </div>
                    <small id="strengthText" class="text-muted"></small>

                    <div class="input-group mt-3">
                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                        <input type="password" id="confirmPassword" name="confirm_password" class="form-control" placeholder="Confirm Password" required>
                        <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('confirmPassword')">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                    <small id="matchMessage"></small>

                    <button class="btn btn-login w-100 mt-4" id="registerBtn">Create Account</button>

                    <div class="text-center mt-4">
                        Already have an account?
                        <a href="login.php">Login</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include("../includes/footer.php"); ?>
