<?php

require_once("../auth/auth_check.php");
require_once("../config/database.php");

$loadDashboardCss = true;
$pageTitle = "Carbon Calculator";
$result = null;
$error = take_flash("error");
$success = take_flash("success");

$companies = $conn->query("SELECT id, company_name FROM companies ORDER BY company_name")->fetchAll();
$factors = $conn->query("SELECT * FROM emission_factors ORDER BY category, id")->fetchAll();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $company_id = (int) ($_POST["company_id"] ?? 0);
    $scope      = $_POST["scope"] ?? "Scope 1";
    $factor_id  = (int) ($_POST["factor_id"] ?? 0);
    $quantity   = (float) ($_POST["quantity"] ?? 0);
    $activity   = trim($_POST["activity"] ?? "");

    $allowedScopes = ["Scope 1", "Scope 2", "Scope 3"];
    if (!in_array($scope, $allowedScopes, true)) {
        $scope = "Scope 1";
    }

    $factorRow = null;
    if ($factor_id > 0) {
        $fs = $conn->prepare("SELECT * FROM emission_factors WHERE id = ?");
        $fs->execute([$factor_id]);
        $factorRow = $fs->fetch();
    }

    if ($company_id <= 0 || $quantity <= 0 || !$factorRow) {
        flash("error", "Please select a company, emission factor, and enter a valid quantity.");
        redirect("calculator/calculator.php");
    }

    $category = $factorRow["category"];
    $unit = $factorRow["unit"];
    $factor = (float) ($factorRow["emission_factor"] ?? 0);
    $total = $quantity * $factor;
    if ($activity === "") {
        $activity = trim(($factorRow["activity"] ?? "") !== "" ? $factorRow["activity"] : ($category . " usage"));
    }

    $stmt = $conn->prepare("
        INSERT INTO emission_records
        (company_id, scope, category, activity, quantity, unit, emission_factor, total_emission, reporting_date)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, CURDATE())
    ");

    if ($stmt->execute([$company_id, $scope, $category, $activity, $quantity, $unit, $factor, $total])) {
        log_activity($conn, "Emission calculated: " . number_format($total, 2) . " kg CO₂e");
        flash("success", "Saved successfully. Estimated emission: " . number_format($total, 2) . " kg CO₂e");
        redirect("calculator/calculator.php");
    }

    flash("error", "Could not save emission record.");
    redirect("calculator/calculator.php");
}

include("../includes/header.php");
?>

<div class="dashboard-wrapper">
    <?php include("../includes/sidebar.php"); ?>
    <div class="main-content">
        <?php include("../includes/navbar.php"); ?>
        <div class="container-fluid mt-4">

            <div class="mb-4">
                <h2 class="fw-bold">Carbon Calculator</h2>
                <p class="text-muted">Estimate and save CO₂e using standard emission factors</p>
            </div>

            <?php if ($error): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?>
            <?php if ($success): ?><div class="alert alert-success"><?= e($success) ?></div><?php endif; ?>

            <div class="row g-4">
                <div class="col-lg-8">
                    <div class="dashboard-card">
                        <form method="POST" class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Company</label>
                                <select name="company_id" class="form-select" required>
                                    <option value="">Select company</option>
                                    <?php foreach ($companies as $c): ?>
                                        <option value="<?= $c["id"] ?>"><?= e($c["company_name"]) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Scope</label>
                                <select name="scope" class="form-select">
                                    <option>Scope 1</option>
                                    <option>Scope 2</option>
                                    <option>Scope 3</option>
                                </select>
                            </div>
                            <div class="col-md-8">
                                <label class="form-label">Emission Factor</label>
                                <select name="factor_id" id="factorSelect" class="form-select" required>
                                    <option value="">Select factor</option>
                                    <?php foreach ($factors as $f): ?>
                                        <option value="<?= $f["id"] ?>"
                                            data-factor="<?= $f["emission_factor"] ?>"
                                            data-unit="<?= e($f["unit"]) ?>">
                                            <?= e($f["category"]) ?><?= !empty($f["activity"]) ? " / " . e($f["activity"]) : "" ?>
                                            — <?= $f["emission_factor"] ?> kg CO₂e / <?= e($f["unit"]) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Quantity</label>
                                <input type="number" step="0.01" min="0.01" name="quantity" id="qtyInput" class="form-control" required>
                                <small class="text-muted" id="unitHint"></small>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Activity description (optional)</label>
                                <input type="text" name="activity" class="form-control" placeholder="e.g. Fleet diesel, Office electricity">
                            </div>
                            <div class="col-12">
                                <div class="alert alert-light border" id="previewBox">
                                    Estimated result: <strong id="previewTotal">0.00</strong> kg CO₂e
                                </div>
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-calculator"></i> Calculate & Save
                                </button>
                                <a href="<?= APP_BASE ?>/modules/emissions/emissions.php" class="btn btn-outline-secondary">View Emissions</a>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="dashboard-card">
                        <h5>Factor Library</h5>
                        <p class="text-muted small">Based on common IPCC / DEFRA-style defaults for demo use.</p>
                        <ul class="list-unstyled mb-0">
                            <?php foreach (array_slice($factors, 0, 6) as $f): ?>
                            <li class="mb-2">
                                <strong><?= e($f["category"]) ?></strong><br>
                                <small class="text-muted"><?= $f["emission_factor"] ?> / <?= e($f["unit"]) ?> · <?= e($f["source"] ?? "") ?></small>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const select = document.getElementById("factorSelect");
    const qty = document.getElementById("qtyInput");
    const preview = document.getElementById("previewTotal");
    const unitHint = document.getElementById("unitHint");

    function updatePreview() {
        const opt = select.options[select.selectedIndex];
        const factor = parseFloat(opt?.dataset?.factor || 0);
        const unit = opt?.dataset?.unit || "";
        const quantity = parseFloat(qty.value || 0);
        unitHint.textContent = unit ? ("Unit: " + unit) : "";
        preview.textContent = (factor * quantity).toFixed(2);
    }

    select?.addEventListener("change", updatePreview);
    qty?.addEventListener("input", updatePreview);
});
</script>

<?php include("../includes/footer.php"); ?>
