<?php

require_once("../auth/auth_check.php");
require_once("../config/database.php");

$loadDashboardCss = true;
$pageTitle = "ESG Analysis";
$error = take_flash("error");
$success = take_flash("success");

$companies = $conn->query("SELECT id, company_name FROM companies ORDER BY company_name")->fetchAll();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $company_id = (int) ($_POST["company_id"] ?? 0);
    $eScore = (float) ($_POST["environment_score"] ?? 0);
    $sScore = (float) ($_POST["social_score"] ?? 0);
    $gScore = (float) ($_POST["governance_score"] ?? 0);
    $total = round(($eScore + $sScore + $gScore) / 3, 2);

    $rating = "C";
    if ($total >= 85) $rating = "A";
    elseif ($total >= 70) $rating = "B";
    elseif ($total >= 55) $rating = "C";
    else $rating = "D";

    if ($company_id <= 0) {
        flash("error", "Please select a company.");
        redirect("pages/esg.php");
    }

    $stmt = $conn->prepare("
        INSERT INTO esg_scores
        (company_id, environmental_score, social_score, governance_score, total_score, rating)
        VALUES (?, ?, ?, ?, ?, ?)
    ");
    if ($stmt->execute([$company_id, $eScore, $sScore, $gScore, $total, $rating])) {
        log_activity($conn, "ESG score saved for company #{$company_id}");
        flash("success", "ESG score saved successfully.");
    } else {
        flash("error", "Failed to save ESG score.");
    }
    redirect("pages/esg.php");
}

$scores = $conn->query("
    SELECT c.company_name, e.*
    FROM esg_scores e
    JOIN companies c ON c.id = e.company_id
    ORDER BY e.id DESC
")->fetchAll();

$avg = $conn->query("
    SELECT
        IFNULL(AVG(environmental_score),0) AS e_avg,
        IFNULL(AVG(social_score),0) AS s_avg,
        IFNULL(AVG(governance_score),0) AS g_avg,
        IFNULL(AVG(total_score),0) AS t_avg
    FROM esg_scores
")->fetch();

include("../includes/header.php");
?>

<div class="dashboard-wrapper">
    <?php include("../includes/sidebar.php"); ?>
    <div class="main-content">
        <?php include("../includes/navbar.php"); ?>
        <div class="container-fluid mt-4">

            <div class="mb-4">
                <h2 class="fw-bold">ESG Analysis</h2>
                <p class="text-muted">Environment, Social and Governance performance</p>
            </div>

            <?php if ($error): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?>
            <?php if ($success): ?><div class="alert alert-success"><?= e($success) ?></div><?php endif; ?>

            <div class="row g-4 mb-4">
                <div class="col-lg-3 col-md-6"><div class="dashboard-card text-center"><h6>Environment</h6><h2 class="counter"><?= number_format((float)$avg["e_avg"], 1) ?></h2></div></div>
                <div class="col-lg-3 col-md-6"><div class="dashboard-card text-center"><h6>Social</h6><h2 class="counter"><?= number_format((float)$avg["s_avg"], 1) ?></h2></div></div>
                <div class="col-lg-3 col-md-6"><div class="dashboard-card text-center"><h6>Governance</h6><h2 class="counter"><?= number_format((float)$avg["g_avg"], 1) ?></h2></div></div>
                <div class="col-lg-3 col-md-6"><div class="dashboard-card text-center"><h6>Overall</h6><h2 class="counter"><?= number_format((float)$avg["t_avg"], 1) ?></h2></div></div>
            </div>

            <div class="row g-4">
                <div class="col-lg-5">
                    <div class="dashboard-card">
                        <h4 class="mb-3">Add ESG Score</h4>
                        <form method="POST" class="row g-3">
                            <div class="col-12">
                                <label class="form-label">Company</label>
                                <select name="company_id" class="form-select" required>
                                    <option value="">Select company</option>
                                    <?php foreach ($companies as $c): ?>
                                        <option value="<?= $c["id"] ?>"><?= e($c["company_name"]) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Environment</label>
                                <input type="number" step="0.1" min="0" max="100" name="environment_score" class="form-control" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Social</label>
                                <input type="number" step="0.1" min="0" max="100" name="social_score" class="form-control" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Governance</label>
                                <input type="number" step="0.1" min="0" max="100" name="governance_score" class="form-control" required>
                            </div>
                            <div class="col-12">
                                <button class="btn btn-success" type="submit"><i class="fas fa-save"></i> Save Score</button>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="col-lg-7">
                    <div class="dashboard-card">
                        <h4 class="mb-3">Score History</h4>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Company</th>
                                        <th>E</th>
                                        <th>S</th>
                                        <th>G</th>
                                        <th>Total</th>
                                        <th>Rating</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php if (count($scores) > 0): ?>
                                    <?php foreach ($scores as $row): ?>
                                    <tr>
                                        <td><?= e($row["company_name"]) ?></td>
                                        <td><?= number_format((float)$row["environmental_score"], 1) ?></td>
                                        <td><?= number_format((float)$row["social_score"], 1) ?></td>
                                        <td><?= number_format((float)$row["governance_score"], 1) ?></td>
                                        <td><strong><?= number_format((float)$row["total_score"], 1) ?></strong></td>
                                        <td><span class="badge text-bg-success"><?= e($row["rating"] ?? "-") ?></span></td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr><td colspan="6" class="text-muted text-center py-4">No ESG scores yet. Add the first one.</td></tr>
                                <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<?php include("../includes/footer.php"); ?>
