<?php

require_once("../../auth/auth_check.php");
require_once("../../config/database.php");

$loadDashboardCss = true;
$pageTitle = "Emissions";

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["delete_id"])) {
    $deleteId = (int) $_POST["delete_id"];
    $stmt = $conn->prepare("DELETE FROM emission_records WHERE id = ?");
    if ($stmt->execute([$deleteId])) {
        log_activity($conn, "Emission record #{$deleteId} deleted");
        flash("success", "Emission record deleted.");
    }
    redirect("modules/emissions/emissions.php");
}

$success = take_flash("success");

$records = $conn->query("
    SELECT e.*, c.company_name
    FROM emission_records e
    LEFT JOIN companies c ON c.id = e.company_id
    ORDER BY e.id DESC
")->fetchAll();

$byScope = $conn->query("
    SELECT scope, IFNULL(SUM(total_emission),0) AS total
    FROM emission_records
    GROUP BY scope
")->fetchAll();

include("../../includes/header.php");
?>

<div class="dashboard-wrapper">
    <?php include("../../includes/sidebar.php"); ?>
    <div class="main-content">
        <?php include("../../includes/navbar.php"); ?>
        <div class="container-fluid mt-4">

            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="fw-bold">Emissions</h2>
                    <p class="text-muted mb-0">All recorded carbon emission activities</p>
                </div>
                <a href="<?= APP_BASE ?>/calculator/calculator.php" class="btn btn-success">
                    <i class="fas fa-plus"></i> New Calculation
                </a>
            </div>

            <?php if ($success): ?><div class="alert alert-success"><?= e($success) ?></div><?php endif; ?>

            <div class="row g-4 mb-4">
                <?php foreach ($byScope as $scope): ?>
                <div class="col-md-4">
                    <div class="dashboard-card text-center">
                        <h6><?= e($scope["scope"] ?? "Unscoped") ?></h6>
                        <h3><?= number_format((float) $scope["total"], 2) ?> <small class="fs-6 text-muted">kg</small></h3>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <div class="dashboard-card mb-4">
                <input type="text" id="emissionSearch" class="form-control" placeholder="Search emissions...">
            </div>

            <div class="dashboard-card">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Company</th>
                                <th>Scope</th>
                                <th>Category</th>
                                <th>Activity</th>
                                <th>Quantity</th>
                                <th>CO₂e</th>
                                <th>Date</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody id="emissionTable">
                        <?php if (count($records) > 0): ?>
                            <?php foreach ($records as $row): ?>
                            <tr>
                                <td><?= $row["id"] ?></td>
                                <td><?= e($row["company_name"] ?? "-") ?></td>
                                <td><span class="badge badge-scope"><?= e($row["scope"] ?? "-") ?></span></td>
                                <td><?= e($row["category"] ?? "-") ?></td>
                                <td><?= e($row["activity"] ?? "-") ?></td>
                                <td><?= number_format((float) $row["quantity"], 2) ?> <?= e($row["unit"] ?? "") ?></td>
                                <td><strong><?= number_format((float) $row["total_emission"], 2) ?></strong></td>
                                <td><?= e($row["reporting_date"] ?? $row["created_at"]) ?></td>
                                <td>
                                    <form method="POST" onsubmit="return confirm('Delete this record?');">
                                        <input type="hidden" name="delete_id" value="<?= $row["id"] ?>">
                                        <button class="btn btn-sm btn-outline-danger" type="submit">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="9" class="text-center text-muted py-5">No emission records yet.</td>
                            </tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</div>

<?php include("../../includes/footer.php"); ?>
