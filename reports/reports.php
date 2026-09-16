<?php

require_once("../auth/auth_check.php");
require_once("../config/database.php");

$loadDashboardCss = true;
$pageTitle = "Reports";
$success = take_flash("success");

$reports = $conn->query("
    SELECT r.*, c.company_name
    FROM reports r
    LEFT JOIN companies c ON c.id = r.company_id
    ORDER BY r.id DESC
")->fetchAll();

include("../includes/header.php");
?>

<div class="dashboard-wrapper">
    <?php include("../includes/sidebar.php"); ?>
    <div class="main-content">
        <?php include("../includes/navbar.php"); ?>
        <div class="container-fluid mt-4">

            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="fw-bold">Reports</h2>
                    <p class="text-muted mb-0">ESG and emission reports</p>
                </div>
                <a href="create_report.php" class="btn btn-success">
                    <i class="fas fa-plus"></i> New Report
                </a>
            </div>

            <?php if ($success): ?><div class="alert alert-success"><?= e($success) ?></div><?php endif; ?>

            <div class="dashboard-card">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Report</th>
                                <th>Company</th>
                                <th>Type</th>
                                <th>Date</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php if (count($reports) > 0): ?>
                            <?php foreach ($reports as $report): ?>
                            <tr>
                                <td><?= $report["id"] ?></td>
                                <td><?= e($report["report_name"] ?? "-") ?></td>
                                <td><?= e($report["company_name"] ?? "-") ?></td>
                                <td><span class="badge text-bg-light"><?= e($report["report_type"] ?? "-") ?></span></td>
                                <td><?= e($report["report_date"] ?? $report["created_at"]) ?></td>
                                <td>
                                    <a class="btn btn-sm btn-outline-primary" href="view_report.php?id=<?= $report["id"] ?>">
                                        <i class="fas fa-eye"></i> View
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">No reports found.</td>
                            </tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</div>

<?php include("../includes/footer.php"); ?>
