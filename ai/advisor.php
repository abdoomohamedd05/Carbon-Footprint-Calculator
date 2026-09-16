<?php

require_once("../auth/auth_check.php");
require_once("../config/database.php");

$loadDashboardCss = true;
$pageTitle = "AI Advisor";
$success = take_flash("success");

$companies = $conn->query("SELECT id, company_name FROM companies ORDER BY company_name")->fetchAll();

function generate_recommendations(PDO $conn, int $companyId): int
{
    $stmt = $conn->prepare("
        SELECT scope, category, SUM(total_emission) AS total
        FROM emission_records
        WHERE company_id = ?
        GROUP BY scope, category
        ORDER BY total DESC
    ");
    $stmt->execute([$companyId]);
    $rows = $stmt->fetchAll();

    $created = 0;
    $insert = $conn->prepare("
        INSERT INTO ai_recommendations (company_id, recommendation, expected_reduction, estimated_saving, priority)
        VALUES (?, ?, ?, ?, ?)
    ");

    if (count($rows) === 0) {
        $insert->execute([
            $companyId,
            "No emission data found. Start with the Carbon Calculator to capture Scope 1–3 activities.",
            0,
            0,
            "High"
        ]);
        return 1;
    }

    foreach ($rows as $i => $row) {
        $scope = $row["scope"] ?? "emissions";
        $category = $row["category"] ?? "activity";
        $total = number_format((float) $row["total"], 2);
        $reduction = round(((float) $row["total"]) * 0.1, 2);

        if ($i === 0) {
            $insert->execute([
                $companyId,
                "Highest impact detected in {$scope} / {$category} ({$total} kg CO₂e). Prioritize reduction projects in this area first.",
                $reduction,
                round($reduction * 25, 2),
                "High"
            ]);
            $created++;
        } elseif (stripos((string) $category, "Electric") !== false || ($scope === "Scope 2")) {
            $insert->execute([
                $companyId,
                "Electricity-related emissions are material. Consider renewable tariffs, efficiency upgrades, and LED / HVAC optimization.",
                $reduction,
                round($reduction * 20, 2),
                "Medium"
            ]);
            $created++;
        } elseif ($scope === "Scope 1") {
            $insert->execute([
                $companyId,
                "Direct combustion / fleet emissions are present. Evaluate hybrid/EV fleets, route optimization, and cleaner fuels.",
                $reduction,
                round($reduction * 30, 2),
                "High"
            ]);
            $created++;
        } elseif ($scope === "Scope 3") {
            $insert->execute([
                $companyId,
                "Value-chain (Scope 3) emissions appear. Engage suppliers on reporting and preferred low-carbon purchasing criteria.",
                $reduction,
                round($reduction * 15, 2),
                "Medium"
            ]);
            $created++;
        }

        if ($created >= 3) {
            break;
        }
    }

    if ($created < 2) {
        $insert->execute([
            $companyId,
            "Set an internal reduction target (e.g. −10% year over year) and track progress monthly in the dashboard.",
            10,
            5000,
            "Low"
        ]);
        $created++;
    }

    return $created;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $companyId = (int) ($_POST["company_id"] ?? 0);
    if ($companyId > 0) {
        $count = generate_recommendations($conn, $companyId);
        log_activity($conn, "AI advisor generated {$count} recommendations");
        flash("success", "Generated {$count} recommendations.");
    }
    redirect("ai/advisor.php");
}

$recommendations = $conn->query("
    SELECT a.*, c.company_name
    FROM ai_recommendations a
    LEFT JOIN companies c ON c.id = a.company_id
    ORDER BY FIELD(a.priority,'High','Medium','Low'), a.id DESC
")->fetchAll();

include("../includes/header.php");
?>

<div class="dashboard-wrapper">
    <?php include("../includes/sidebar.php"); ?>
    <div class="main-content">
        <?php include("../includes/navbar.php"); ?>
        <div class="container-fluid mt-4">

            <div class="mb-4 d-flex justify-content-between align-items-start flex-wrap gap-3">
                <div>
                    <h2 class="fw-bold">AI Sustainability Advisor</h2>
                    <p class="text-muted mb-0">Rule-based recommendations driven by your emission profile</p>
                </div>
            </div>

            <?php if ($success): ?><div class="alert alert-success"><?= e($success) ?></div><?php endif; ?>

            <div class="dashboard-card mb-4">
                <form method="POST" class="row g-3 align-items-end">
                    <div class="col-md-8">
                        <label class="form-label">Generate recommendations for</label>
                        <select name="company_id" class="form-select" required>
                            <option value="">Select company</option>
                            <?php foreach ($companies as $c): ?>
                                <option value="<?= $c["id"] ?>"><?= e($c["company_name"]) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <button class="btn btn-success w-100" type="submit">
                            <i class="fas fa-robot"></i> Generate Advice
                        </button>
                    </div>
                </form>
            </div>

            <div class="row g-4">
                <?php if (count($recommendations) > 0): ?>
                    <?php foreach ($recommendations as $item): ?>
                    <div class="col-lg-4">
                        <div class="dashboard-card h-100 priority-<?= strtolower($item["priority"]) ?>">
                            <span class="badge bg-<?= $item["priority"] === "High" ? "danger" : ($item["priority"] === "Low" ? "secondary" : "warning") ?>">
                                <?= e($item["priority"]) ?>
                            </span>
                            <h5 class="mt-3"><?= e($item["company_name"] ?? "General") ?></h5>
                            <p class="mb-0"><?= e($item["recommendation"]) ?></p>
                            <?php if (!empty($item["expected_reduction"])): ?>
                            <small class="text-muted d-block mt-2">
                                Expected reduction: <?= number_format((float)$item["expected_reduction"], 1) ?>%
                                <?php if (!empty($item["estimated_saving"])): ?>
                                · Est. saving: $<?= number_format((float)$item["estimated_saving"], 0) ?>
                                <?php endif; ?>
                            </small>
                            <?php endif; ?>
                            <small class="text-muted d-block mt-2"><?= e($item["created_at"]) ?></small>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-12">
                        <div class="dashboard-card text-center text-muted py-5">
                            No recommendations yet. Select a company and generate advice.
                        </div>
                    </div>
                <?php endif; ?>
            </div>

        </div>
    </div>
</div>

<?php include("../includes/footer.php"); ?>
