<?php

require_once("../auth/auth_check.php");
require_once("../config/database.php");

$loadDashboardCss = true;
$pageTitle = "New Report";
$error = "";

$companies = $conn->query("SELECT id, company_name FROM companies ORDER BY company_name")->fetchAll();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $company_id = (int) ($_POST["company_id"] ?? 0);
    $report_name = trim($_POST["report_name"] ?? "");
    $report_type = trim($_POST["report_type"] ?? "ESG");
    $report_date = $_POST["report_date"] ?? date("Y-m-d");

    if ($company_id <= 0 || $report_name === "") {
        $error = "Company and report name are required.";
    } else {
        $stmt = $conn->prepare("
            INSERT INTO reports (company_id, report_name, report_type, report_date)
            VALUES (?, ?, ?, ?)
        ");
        if ($stmt->execute([$company_id, $report_name, $report_type, $report_date])) {
            log_activity($conn, "Report created: {$report_name}");
            flash("success", "Report created successfully.");
            redirect("reports/reports.php");
        }
        $error = "Failed to create report.";
    }
}

include("../includes/header.php");
?>

<div class="dashboard-wrapper">
    <?php include("../includes/sidebar.php"); ?>
    <div class="main-content">
        <?php include("../includes/navbar.php"); ?>
        <div class="container-fluid mt-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="fw-bold">New Report</h2>
                    <p class="text-muted">Create an ESG or emissions report entry</p>
                </div>
                <a href="reports.php" class="btn btn-outline-secondary">Back</a>
            </div>

            <?php if ($error): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?>

            <div class="dashboard-card" style="max-width:720px;">
                <form method="POST" class="row g-3">
                    <div class="col-md-12">
                        <label class="form-label">Company</label>
                        <select name="company_id" class="form-select" required>
                            <option value="">Select company</option>
                            <?php foreach ($companies as $c): ?>
                                <option value="<?= $c["id"] ?>"><?= e($c["company_name"]) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-8">
                        <label class="form-label">Report Name</label>
                        <input type="text" name="report_name" class="form-control" required placeholder="Q2 ESG Report 2026">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Type</label>
                        <select name="report_type" class="form-select">
                            <option>ESG</option>
                            <option>Emissions</option>
                            <option>Summary</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Report Date</label>
                        <input type="date" name="report_date" class="form-control" value="<?= date("Y-m-d") ?>">
                    </div>
                    <div class="col-12">
                        <button class="btn btn-success" type="submit"><i class="fas fa-save"></i> Create Report</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include("../includes/footer.php"); ?>
