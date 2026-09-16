<?php
$currentPage = basename($_SERVER["PHP_SELF"]);
$requestPath = $_SERVER["PHP_SELF"] ?? "";
$base = APP_BASE;
$userRole = $_SESSION["role"] ?? "Analyst";
?>

<aside class="sidebar" id="sidebar">

    <div class="sidebar-header">
        <a href="<?= $base ?>/pages/dashboard.php" class="sidebar-brand">
            <div class="logo-icon"><i class="fa-solid fa-leaf"></i></div>
            <div class="logo-text">
                <h4>Carbon AI</h4>
                <span>Sustainability Platform</span>
            </div>
        </a>
    </div>

    <ul class="sidebar-menu" id="sidebarMenu">
        <li class="<?= $currentPage === "dashboard.php" ? "active" : "" ?>">
            <a href="<?= $base ?>/pages/dashboard.php">
                <i class="fa-solid fa-chart-line"></i><span>Dashboard</span>
            </a>
        </li>
        <li class="<?= $currentPage === "calculator.php" ? "active" : "" ?>">
            <a href="<?= $base ?>/calculator/calculator.php">
                <i class="fa-solid fa-calculator"></i><span>Carbon Calculator</span>
            </a>
        </li>
        <li class="<?= $currentPage === "emissions.php" ? "active" : "" ?>">
            <a href="<?= $base ?>/modules/emissions/emissions.php">
                <i class="fa-solid fa-smog"></i><span>Emissions</span>
            </a>
        </li>
        <li class="<?= (strpos($requestPath, "companies") !== false) ? "active" : "" ?>">
            <a href="<?= $base ?>/modules/companies/index.php">
                <i class="fa-solid fa-building"></i><span>Companies</span>
            </a>
        </li>
        <li class="<?= $currentPage === "esg.php" ? "active" : "" ?>">
            <a href="<?= $base ?>/pages/esg.php">
                <i class="fa-solid fa-chart-pie"></i><span>ESG Analysis</span>
            </a>
        </li>
        <li class="<?= $currentPage === "advisor.php" ? "active" : "" ?>">
            <a href="<?= $base ?>/ai/advisor.php">
                <i class="fa-solid fa-robot"></i><span>AI Advisor</span>
            </a>
        </li>
        <li class="<?= strpos($requestPath, "reports") !== false ? "active" : "" ?>">
            <a href="<?= $base ?>/reports/reports.php">
                <i class="fa-solid fa-file-lines"></i><span>Reports</span>
            </a>
        </li>
        <li class="<?= $currentPage === "settings.php" ? "active" : "" ?>">
            <a href="<?= $base ?>/pages/settings.php">
                <i class="fa-solid fa-gear"></i><span>Settings</span>
            </a>
        </li>
    </ul>

    <div class="sidebar-footer" id="sidebarFooter">
        <div class="footer-drag-handle" id="footerDragHandle" title="اسحب لتغيير المكان">
            <i class="fa-solid fa-grip-lines"></i>
            <span>اسحب لنقل Logout</span>
            <button type="button" class="footer-reset-btn" id="footerResetBtn" title="إعادة للأسفل">
                <i class="fa-solid fa-arrow-down"></i>
            </button>
        </div>
        <div class="user-card">
            <div class="user-avatar">
                <?= e(strtoupper(substr($_SESSION["full_name"] ?? "U", 0, 1))) ?>
            </div>
            <div class="user-info">
                <h6><?= e($_SESSION["full_name"] ?? "User") ?></h6>
                <small><?= e($userRole) ?></small>
            </div>
        </div>
        <a href="<?= $base ?>/auth/logout.php" class="logout-btn">
            <i class="fa-solid fa-right-from-bracket"></i>
            <span>Logout</span>
        </a>
    </div>

</aside>
