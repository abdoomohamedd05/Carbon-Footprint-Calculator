<?php

require_once("../../auth/auth_check.php");
require_once("../../config/database.php");

$loadDashboardCss = true;
$pageTitle = "Edit Company";
$id = (int) ($_GET["id"] ?? 0);
$error = "";

$stmt = $conn->prepare("SELECT * FROM companies WHERE id = ?");
$stmt->execute([$id]);
$company = $stmt->fetch();

if (!$company) {
    redirect("modules/companies/index.php");
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $company_name   = trim($_POST["company_name"] ?? "");
    $industry       = trim($_POST["industry"] ?? "");
    $country        = trim($_POST["country"] ?? "");
    $city           = trim($_POST["city"] ?? "");
    $employees      = (int) ($_POST["employees"] ?? 0);
    $annual_revenue = (float) ($_POST["annual_revenue"] ?? 0);

    if ($company_name === "") {
        $error = "Company name is required.";
    } else {
        $update = $conn->prepare("
            UPDATE companies
            SET company_name = ?, industry = ?, country = ?, city = ?, employees = ?, annual_revenue = ?
            WHERE id = ?
        ");
        if ($update->execute([$company_name, $industry, $country, $city, $employees, $annual_revenue, $id])) {
            log_activity($conn, "Company updated: {$company_name}");
            flash("success", "Company updated successfully.");
            redirect("modules/companies/view.php?id={$id}");
        }
        $error = "Failed to update company.";
    }
    $company = array_merge($company, $_POST);
}

include("../../includes/header.php");
?>

<div class="dashboard-wrapper">
    <?php include("../../includes/sidebar.php"); ?>
    <div class="main-content">
        <?php include("../../includes/navbar.php"); ?>
        <div class="container-fluid mt-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="fw-bold">Edit Company</h2>
                    <p class="text-muted">Update company information</p>
                </div>
                <a href="view.php?id=<?= $id ?>" class="btn btn-outline-secondary">Back</a>
            </div>

            <?php if ($error): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?>

            <div class="dashboard-card">
                <form method="POST" class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Company Name</label>
                        <input type="text" name="company_name" class="form-control" value="<?= e($company["company_name"]) ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Industry</label>
                        <input type="text" name="industry" class="form-control" value="<?= e($company["industry"] ?? "") ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Country</label>
                        <input type="text" name="country" class="form-control" value="<?= e($company["country"] ?? "") ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">City</label>
                        <input type="text" name="city" class="form-control" value="<?= e($company["city"] ?? "") ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Employees</label>
                        <input type="number" name="employees" class="form-control" min="0" value="<?= (int) $company["employees"] ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Annual Revenue ($)</label>
                        <input type="number" step="0.01" name="annual_revenue" class="form-control" min="0" value="<?= (float) $company["annual_revenue"] ?>">
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn btn-success"><i class="fas fa-save"></i> Update Company</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include("../../includes/footer.php"); ?>
