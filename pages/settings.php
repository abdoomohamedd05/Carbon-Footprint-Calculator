<?php

require_once("../auth/auth_check.php");
require_once("../config/database.php");

$loadDashboardCss = true;
$pageTitle = "Settings";
$error = take_flash("error");
$success = take_flash("success");

$userId = (int) $_SESSION["user_id"];
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch();

if (!$user) {
    redirect("auth/logout.php");
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $action = $_POST["action"] ?? "profile";

    if ($action === "profile") {
        $fullName = trim($_POST["full_name"] ?? "");
        $email = trim($_POST["email"] ?? "");

        if ($fullName === "" || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            flash("error", "Please provide a valid name and email.");
            redirect("pages/settings.php");
        }

        $check = $conn->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
        $check->execute([$email, $userId]);
        if ($check->fetch()) {
            flash("error", "Email is already used by another account.");
            redirect("pages/settings.php");
        }

        $update = $conn->prepare("UPDATE users SET full_name = ?, email = ? WHERE id = ?");
        $update->execute([$fullName, $email, $userId]);
        $_SESSION["full_name"] = $fullName;
        $_SESSION["email"] = $email;
        log_activity($conn, "Profile updated");
        flash("success", "Profile updated successfully.");
        redirect("pages/settings.php");
    }

    if ($action === "password") {
        $current = $_POST["current_password"] ?? "";
        $new = $_POST["new_password"] ?? "";
        $confirm = $_POST["confirm_password"] ?? "";

        if (!password_verify($current, $user["password"])) {
            flash("error", "Current password is incorrect.");
            redirect("pages/settings.php");
        }
        if (strlen($new) < 8) {
            flash("error", "New password must be at least 8 characters.");
            redirect("pages/settings.php");
        }
        if ($new !== $confirm) {
            flash("error", "New passwords do not match.");
            redirect("pages/settings.php");
        }

        $hash = password_hash($new, PASSWORD_DEFAULT);
        $update = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
        $update->execute([$hash, $userId]);
        log_activity($conn, "Password changed");
        flash("success", "Password changed successfully.");
        redirect("pages/settings.php");
    }
}

include("../includes/header.php");
?>

<div class="dashboard-wrapper">
    <?php include("../includes/sidebar.php"); ?>
    <div class="main-content">
        <?php include("../includes/navbar.php"); ?>
        <div class="container-fluid mt-4">

            <div class="mb-4">
                <h2 class="fw-bold">Settings</h2>
                <p class="text-muted">Manage your profile and password</p>
            </div>

            <?php if ($error): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?>
            <?php if ($success): ?><div class="alert alert-success"><?= e($success) ?></div><?php endif; ?>

            <div class="row g-4">
                <div class="col-lg-6">
                    <div class="dashboard-card">
                        <h5>Profile</h5>
                        <form method="POST" class="row g-3 mt-1">
                            <input type="hidden" name="action" value="profile">
                            <div class="col-12">
                                <label class="form-label">Full Name</label>
                                <input type="text" name="full_name" class="form-control" value="<?= e($user["full_name"]) ?>" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" class="form-control" value="<?= e($user["email"]) ?>" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Role</label>
                                <input type="text" class="form-control" value="<?= e($user["role"]) ?>" disabled>
                            </div>
                            <div class="col-12">
                                <button class="btn btn-success" type="submit">Save Profile</button>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="dashboard-card">
                        <h5>Change Password</h5>
                        <form method="POST" class="row g-3 mt-1">
                            <input type="hidden" name="action" value="password">
                            <div class="col-12">
                                <label class="form-label">Current Password</label>
                                <input type="password" name="current_password" class="form-control" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label">New Password</label>
                                <input type="password" name="new_password" class="form-control" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Confirm New Password</label>
                                <input type="password" name="confirm_password" class="form-control" required>
                            </div>
                            <div class="col-12">
                                <button class="btn btn-outline-success" type="submit">Update Password</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<?php include("../includes/footer.php"); ?>
