<?php

require_once("../auth/auth_check.php");
require_once("../config/database.php");

$loadDashboardCss = true;
$pageTitle = "Report Details";
$id = (int) ($_GET["id"] ?? 0);

$stmt = $conn->prepare("
    SELECT r.*, c.company_name, c.industry, c.country, c.city, c.employees, c.annual_revenue
    FROM reports r
    LEFT JOIN companies c ON c.id = r.company_id
    WHERE r.id = ?
");
$stmt->execute([$id]);
$report = $stmt->fetch();

if (!$report) {
    redirect("reports/reports.php");
}

$emissions = [];
$esg = null;
if (!empty($report["company_id"])) {
    $q = $conn->prepare("SELECT * FROM emission_records WHERE company_id = ? ORDER BY id DESC LIMIT 10");
    $q->execute([$report["company_id"]]);
    $emissions = $q->fetchAll();

    $e = $conn->prepare("SELECT * FROM esg_scores WHERE company_id = ? ORDER BY id DESC LIMIT 1");
    $e->execute([$report["company_id"]]);
    $esg = $e->fetch();
}


$total = array_sum(array_map(fn($r) => (float) $r["total_emission"], $emissions));

$city=strtolower($report["city"]??"");
$industry=strtolower($report["industry"]??"");

$physicalRisk="Medium";
$physicalScore=60;
$physicalReason="Moderate exposure to climate-related physical risks.";

if(
    str_contains($city,"alex") ||
    str_contains($city,"port") ||
    str_contains($city,"damietta") ||
    str_contains($city,"دمياط") ||
    str_contains($city,"matrouh") ||
    str_contains($city,"red sea") ||
    str_contains($city,"north sinai") ||
    str_contains($city,"south sinai")
){
    $physicalRisk="High";
    $physicalScore=90;
    $physicalReason="High exposure to sea level rise, coastal flooding and extreme weather events.";
}
elseif(
    str_contains($city,"aswan") ||
    str_contains($city,"luxor") ||
    str_contains($city,"qena") ||
    str_contains($city,"sohag") ||
    str_contains($city,"assiut") ||
    str_contains($city,"minya")
){
    $physicalRisk="High";
    $physicalScore=90;
    $physicalReason="High exposure to extreme heat waves, drought and water scarcity.";
}
$transitionRisk="Low";$transitionScore=20;
if(str_contains($industry,"cement")||str_contains($industry,"iron")||str_contains($industry,"steel")){$transitionRisk="High";$transitionScore=90;}
elseif(str_contains($industry,"car")){$transitionRisk="Medium";$transitionScore=60;}
if($total>10000)$transitionScore=min(100,$transitionScore+10);
$overall=round(($physicalScore+$transitionScore)/2);
$overallRisk=$overall>=80?"High":($overall>=50?"Medium":"Low");
if ($overallRisk == "High") {

    $recommendation = "
    <h5 class='mb-3'><i class='fas fa-robot text-success'></i> AI Sustainability Advisor</h5>

    <strong>Key Findings</strong>
    <ul class='mt-2'>
        <li>Carbon emissions are significantly higher than the recommended benchmark.</li>
        <li>Physical climate risk is <strong>High</strong> due to the company's geographical location.</li>
        <li>Transition risk is <strong>High</strong> because the industry is carbon intensive.</li>
        <li>The current sustainability performance requires immediate improvement.</li>
    </ul>

    <strong>Recommended Actions</strong>
    <ul class='mt-2'>
        <li>Reduce Natural Gas consumption by at least <strong>15%</strong>.</li>
        <li>Increase energy efficiency across operations.</li>
        <li>Consider renewable energy sources such as solar power.</li>
        <li>Monitor carbon emissions monthly.</li>
        <li>Develop a Net Zero roadmap aligned with 2050 sustainability targets.</li>
    </ul>

    <div class='alert alert-danger mt-3 mb-0'>
        <strong>Priority:</strong> Immediate Action Required
    </div>";
}
elseif ($overallRisk == "Medium") {

    $recommendation = "
    <h5 class='mb-3'><i class='fas fa-robot text-success'></i> AI Sustainability Advisor</h5>

    <strong>Key Findings</strong>
    <ul class='mt-2'>
        <li>Current emissions are within an acceptable range but have room for improvement.</li>
        <li>Climate risks should be monitored regularly.</li>
        <li>ESG performance is stable.</li>
    </ul>

    <strong>Recommended Actions</strong>
    <ul class='mt-2'>
        <li>Improve operational efficiency.</li>
        <li>Continue monthly emissions monitoring.</li>
        <li>Increase renewable energy usage.</li>
        <li>Enhance ESG initiatives.</li>
    </ul>

    <div class='alert alert-warning mt-3 mb-0'>
        <strong>Priority:</strong> Continuous Improvement
    </div>";
}
else {

    $recommendation = "
    <h5 class='mb-3'><i class='fas fa-robot text-success'></i> AI Sustainability Advisor</h5>

    <strong>Key Findings</strong>
    <ul class='mt-2'>
        <li>Carbon emissions are well controlled.</li>
        <li>Climate risks are currently low.</li>
        <li>The organization demonstrates good sustainability performance.</li>
    </ul>

    <strong>Recommended Actions</strong>
    <ul class='mt-2'>
        <li>Maintain current sustainability practices.</li>
        <li>Continue periodic ESG assessments.</li>
        <li>Review energy efficiency annually.</li>
    </ul>

    <div class='alert alert-success mt-3 mb-0'>
        <strong>Priority:</strong> Maintain Current Performance
    </div>";
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
                    <h2 class="fw-bold"><?= e($report["report_name"]) ?></h2>
                    <p class="text-muted mb-0"><?= e($report["report_type"]) ?> · <?= e($report["company_name"] ?? "") ?></p>
                </div>
                <div class="d-flex gap-2">
                    <button class="btn btn-outline-secondary" onclick="window.print()"><i class="fas fa-print"></i> Print</button>
                    <a href="reports.php" class="btn btn-outline-secondary">Back</a>
                </div>
            </div>

            <div class="row g-4">
                <div class="col-lg-4">
                    <div class="dashboard-card">
                        <h5>Report Info</h5>
                        <table class="table mt-3">
                            <tr><th>Company</th><td><?= e($report["company_name"] ?? "-") ?></td></tr>
                            <tr><th>Industry</th><td><?= e($report["industry"] ?? "-") ?></td></tr>
                            <tr><th>Country</th><td><?= e($report["country"] ?? "-") ?></td></tr>
                            <tr><th>Type</th><td><?= e($report["report_type"] ?? "-") ?></td></tr>
                            <tr><th>Date</th><td><?= e($report["report_date"] ?? $report["created_at"]) ?></td></tr>
                        </table>
                    </div>
                    <?php if ($esg): ?>
                    <div class="dashboard-card mt-4">
                        <h5>Latest ESG</h5>
                        <p class="mb-1">E: <?= number_format((float)$esg["environmental_score"], 1) ?></p>
                        <p class="mb-1">S: <?= number_format((float)$esg["social_score"], 1) ?></p>
                        <p class="mb-1">G: <?= number_format((float)$esg["governance_score"], 1) ?></p>
                        <p class="mb-0"><strong>Total: <?= number_format((float)$esg["total_score"], 1) ?></strong></p>
                    </div>
                    <?php endif; ?>
<div class="dashboard-card mt-4"><h5>Climate Risk Assessment</h5><table class="table"><tr><th>Physical Risk</th><td><strong><?= $physicalRisk ?></strong><br><small class="text-muted"><?= $physicalReason ?></small></td></tr><tr><th>Transition Risk</th><td><?= $transitionRisk ?></td></tr><tr><th>Overall Risk</th><td><strong><?= $overallRisk ?></strong></td></tr></table></div>
                </div>
                <div class="col-lg-8">
                    <div class="dashboard-card">
                        <div class="d-flex justify-content-between">
                            <h5>Recent Emissions Snapshot</h5>
                            <strong><?= number_format($total, 2) ?> kg CO₂e</strong>
                        </div>
                        <div class="table-responsive mt-3">
                            <table class="table table-sm">
                                <thead>
                                    <tr><th>Scope</th><th>Activity</th><th>CO₂e</th><th>Date</th></tr>
                                </thead>
                                <tbody>
                                <?php if (count($emissions) > 0): ?>
                                    <?php foreach ($emissions as $row): ?>
                                    <tr>
                                        <td><?= e($row["scope"] ?? "-") ?></td>
                                        <td><?= e($row["activity"] ?? "-") ?></td>
                                        <td><?= number_format((float)$row["total_emission"], 2) ?></td>
                                        <td><?= e($row["reporting_date"] ?? $row["created_at"]) ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr><td colspan="4" class="text-muted">No linked emission data.</td></tr>
                                <?php endif; ?>
<div class="dashboard-card mt-4"><h5>Climate Risk Assessment</h5><table class="table"><tr><th>Physical Risk</th><td><strong><?= $physicalRisk ?></strong><br><small class="text-muted"><?= $physicalReason ?></small></td></tr><tr><th>Transition Risk</th><td><?= $transitionRisk ?></td></tr><tr><th>Overall Risk</th><td><strong><?= $overallRisk ?></strong></td></tr></table></div>
                                </tbody>
                            </table>
                        </div>
                    <div class="dashboard-card mt-4"><h5>AI Recommendation</h5><div class="alert alert-success"><?= $recommendation ?></div></div>
                </div>
            </div>

        </div>
    </div>
</div>

<?php include("../includes/footer.php"); ?>
