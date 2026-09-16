<?php

require_once("../auth/auth_check.php");
require_once("../config/dashboard_data.php");

$loadDashboardCss = true;
$pageTitle = "Dashboard";
include("../includes/header.php");
?>

<div class="dashboard-wrapper">
    <?php include("../includes/sidebar.php"); ?>
    <div class="main-content">
        <?php include("../includes/navbar.php"); ?>
        <div class="container-fluid mt-4">

            <div class="row mb-4">
                <div class="col-lg-12">
                    <div class="welcome-card">
                        <div>
                            <h2>Welcome, <?= e($_SESSION["full_name"]) ?></h2>
                            <p>Track emissions, ESG performance, and sustainability actions in one place.</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-4">
                <div class="col-lg-3 col-md-6">
                    <div class="dashboard-card">
                        <h6>Companies</h6>
                        <h2 class="counter"><?= $totalCompanies ?></h2>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="dashboard-card">
                        <h6>Users</h6>
                        <h2 class="counter"><?= $totalUsers ?></h2>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="dashboard-card">
                        <h6>Total Carbon (kg)</h6>
                        <h2 class="counter"><?= number_format($totalEmission, 2) ?></h2>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="dashboard-card">
                        <h6>Reports</h6>
                        <h2 class="counter"><?= $totalReports ?></h2>
                    </div>
                </div>
            </div>

            <div class="row mt-4 g-4">
                <div class="col-lg-8">
                    <div class="dashboard-card">
                        <h4>Carbon Emissions Trend</h4>
                        <div class="chart-box mt-3">
                            <canvas id="carbonChart"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="dashboard-card">
                        <h4>ESG Overview</h4>
                        <div class="chart-box mt-3">
                            <canvas id="esgChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mt-4 g-4">
                <div class="col-lg-6">
                    <div class="dashboard-card">
                        <h4>Top Emitting Companies</h4>
                        <table class="table table-hover mt-3">
                            <thead>
                                <tr>
                                    <th>Company</th>
                                    <th>Total CO₂e</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php if (count($topCompanies) > 0): ?>
                                <?php foreach ($topCompanies as $row): ?>
                                <tr>
                                    <td><?= e($row["company_name"]) ?></td>
                                    <td><?= number_format((float) $row["total"], 2) ?> kg</td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr><td colspan="2" class="text-muted">No emission data yet.</td></tr>
                            <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="dashboard-card">
                        <h4>Latest Activities</h4>
                        <table class="table table-hover mt-3">
                            <thead>
                                <tr>
                                    <th>Activity</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($latestActivities as $row): ?>
                                <tr>
                                    <td><?= e($row["activity"]) ?></td>
                                    <td><?= e($row["created_at"]) ?></td>
                                </tr>
                            <?php endforeach; ?>
                            <?php if (count($latestActivities) === 0): ?>
                                <tr><td colspan="2" class="text-muted">No activity yet.</td></tr>
                            <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="row mt-4 mb-4">
                <div class="col-12">
                    <div class="dashboard-card">
                        <h4 class="mb-3">Quick Actions</h4>
                        <div class="d-flex flex-wrap gap-2">
                            <a href="<?= APP_BASE ?>/calculator/calculator.php" class="btn btn-success">
                                <i class="fas fa-calculator"></i> Calculate Emissions
                            </a>
                            <a href="<?= APP_BASE ?>/modules/companies/create.php" class="btn btn-outline-success">
                                <i class="fas fa-building"></i> Add Company
                            </a>
                            <a href="<?= APP_BASE ?>/reports/create_report.php" class="btn btn-outline-primary">
                                <i class="fas fa-file"></i> New Report
                            </a>
                            <a href="<?= APP_BASE ?>/ai/advisor.php" class="btn btn-outline-secondary">
                                <i class="fas fa-robot"></i> AI Advisor
                            </a>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<?php include("../includes/footer.php"); ?>
