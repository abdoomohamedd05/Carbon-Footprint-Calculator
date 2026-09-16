<?php

require_once("../../auth/auth_check.php");
require_once("../../config/database.php");

$loadDashboardCss = true;
$pageTitle = "Company Details";
$id = (int) ($_GET["id"] ?? 0);

$stmt = $conn->prepare("SELECT * FROM companies WHERE id = ?");
$stmt->execute([$id]);
$company = $stmt->fetch();

if (!$company) {
    redirect("modules/companies/index.php");
}

$emissions = $conn->prepare("SELECT * FROM emission_records WHERE company_id = ? ORDER BY id DESC");
$emissions->execute([$id]);
$emissionRows = $emissions->fetchAll();

$totalCo2 = array_sum(array_map(fn($r) => (float) $r["total_emission"], $emissionRows));

$esg = $conn->prepare("SELECT * FROM esg_scores WHERE company_id = ? ORDER BY id DESC LIMIT 1");
$esg->execute([$id]);
$esgRow = $esg->fetch();

include("../../includes/header.php");
?>

<div class="dashboard-wrapper">
    <?php include("../../includes/sidebar.php"); ?>
    <div class="main-content">
        <?php include("../../includes/navbar.php"); ?>
        <div class="container-fluid mt-4">

            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="fw-bold"><?= e($company["company_name"]) ?></h2>
                    <p class="text-muted mb-0">Company profile and emission history</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="edit.php?id=<?= $id ?>" class="btn btn-warning">Edit</a>
                    <a href="index.php" class="btn btn-outline-secondary">Back</a>
                </div>
            </div>

            <div class="row g-4 mb-4">
                <div class="col-md-4">
                    <div class="dashboard-card text-center">
                        <h6>Total Emissions</h6>
                        <h3><?= number_format($totalCo2, 2) ?> kg</h3>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="dashboard-card text-center">
                        <h6>Records</h6>
                        <h3><?= count($emissionRows) ?></h3>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="dashboard-card text-center">
                        <h6>ESG Score</h6>
                        <h3><?= $esgRow ? number_format((float) $esgRow["total_score"], 1) : "—" ?></h3>
                    </div>
                </div>
            </div>

            <div class="row g-4">
                <div class="col-lg-5">
                    <div class="dashboard-card">
                        <h4>Profile</h4>
                        <table class="table mt-3">
                            <tr><th>Industry</th><td><?= e($company["industry"] ?? "-") ?></td></tr>
                            <tr><th>Country</th><td><?= e($company["country"] ?? "-") ?></td></tr>
                            <tr><th>City</th><td><?= e($company["city"] ?? "-") ?></td></tr>
                            <tr><th>Employees</th><td><?= number_format((int) $company["employees"]) ?></td></tr>
                            <tr><th>Revenue</th><td>$<?= number_format((float) $company["annual_revenue"], 2) ?></td></tr>
                            <tr><th>Created</th><td><?= e($company["created_at"]) ?></td></tr>
                        </table>
                    </div>
                </div>
                <div class="col-lg-7">
                    <div class="dashboard-card">
                        <h4>Emission Records</h4>
                        <?php if (count($emissionRows) > 0): ?>
                        <div class="table-responsive mt-3">
                            <table class="table table-sm">
                                <thead>
                                    <tr><th>Scope</th><th>Activity</th><th>CO₂e</th><th>Date</th></tr>
                                </thead>
                                <tbody>
                                <?php foreach ($emissionRows as $row): ?>
                                    <tr>
                                        <td><?= e($row["scope"] ?? "-") ?></td>
                                        <td><?= e($row["activity"] ?? "-") ?></td>
                                        <td><?= number_format((float) $row["total_emission"], 2) ?></td>
                                        <td><?= e($row["reporting_date"] ?? $row["created_at"]) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <?php else: ?>
                            <p class="text-muted mb-0 mt-3">No emission records yet.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<?php include("../../includes/footer.php"); ?>
