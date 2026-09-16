<?php

require_once("../../auth/auth_check.php");
require_once("../../config/database.php");

$loadDashboardCss = true;
$pageTitle = "Companies";

$totalCompanies = (int) $conn->query("SELECT COUNT(*) FROM companies")->fetchColumn();
$companies = $conn->query("SELECT * FROM companies ORDER BY id DESC")->fetchAll();
$success = take_flash("success");

include("../../includes/header.php");
?>

<div class="dashboard-wrapper">
    <?php include("../../includes/sidebar.php"); ?>
    <div class="main-content">
        <?php include("../../includes/navbar.php"); ?>
        <div class="container-fluid mt-4">

            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="fw-bold">Companies Management</h2>
                    <p class="text-muted mb-0">Manage all registered companies</p>
                </div>
                <a href="create.php" class="btn btn-success">
                    <i class="fas fa-plus"></i> Add Company
                </a>
            </div>

            <?php if ($success): ?><div class="alert alert-success"><?= e($success) ?></div><?php endif; ?>

            <div class="row mb-4">
                <div class="col-lg-3 col-md-6">
                    <div class="dashboard-card text-center">
                        <h6>Total Companies</h6>
                        <h2 class="counter"><?= $totalCompanies ?></h2>
                    </div>
                </div>
            </div>

            <div class="dashboard-card mb-4">
                <input type="text" id="companySearch" class="form-control" placeholder="Search company...">
            </div>

            <div class="dashboard-card">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Company</th>
                                <th>Industry</th>
                                <th>Country</th>
                                <th>Employees</th>
                                <th>Revenue</th>
                                <th>Created</th>
                                <th width="220">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="companyTable">
                        <?php if (count($companies) > 0): ?>
                            <?php foreach ($companies as $company): ?>
                            <tr>
                                <td><?= $company["id"] ?></td>
                                <td><strong><?= e($company["company_name"]) ?></strong></td>
                                <td><?= e($company["industry"] ?? "-") ?></td>
                                <td><?= e($company["country"] ?? "-") ?></td>
                                <td><?= number_format((int) $company["employees"]) ?></td>
                                <td>$<?= number_format((float) $company["annual_revenue"], 2) ?></td>
                                <td><?= e($company["created_at"]) ?></td>
                                <td>
                                    <a href="view.php?id=<?= $company["id"] ?>" class="btn btn-primary btn-sm"><i class="fas fa-eye"></i></a>
                                    <a href="edit.php?id=<?= $company["id"] ?>" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i></a>
                                    <form method="POST" action="delete.php" class="d-inline" onsubmit="return confirm('Delete this company?');">
                                        <input type="hidden" name="id" value="<?= $company["id"] ?>">
                                        <button type="submit" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" class="text-center p-5">
                                    <i class="fas fa-building fa-3x mb-3 text-success"></i>
                                    <h4>No Companies Found</h4>
                                    <p>Click Add Company to create your first company.</p>
                                </td>
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
