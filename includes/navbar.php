<?php
$base = APP_BASE;
$pageTitle = $pageTitle ?? page_title_from_request();
$userName = $_SESSION["full_name"] ?? "User";
$userRole = $_SESSION["role"] ?? "Analyst";

$notifCount = 0;
$notifications = [];
if (isset($conn) && $conn instanceof PDO) {
    try {
        $notifications = $conn->query("
            SELECT activity, created_at
            FROM activity_logs
            ORDER BY id DESC
            LIMIT 5
        ")->fetchAll();
        $notifCount = count($notifications);
    } catch (Throwable $e) {
        $notifications = [];
    }
}
?>

<nav class="top-navbar">
    <div class="navbar-left">
        <button class="menu-toggle" id="menuToggle" type="button" aria-label="Toggle menu">
            <i class="fa-solid fa-bars"></i>
        </button>
        <div class="page-title">
            <h3><?= e($pageTitle) ?></h3>
            <p>Carbon Footprint Management System</p>
        </div>
    </div>

    <div class="navbar-center">
        <div class="search-box">
            <i class="fa-solid fa-search"></i>
            <input type="text" id="globalSearch" placeholder="Search companies, reports or emissions...">
        </div>
    </div>

    <div class="navbar-right">
        <button class="nav-btn" id="darkModeBtn" type="button" title="Toggle dark mode">
            <i class="fa-solid fa-moon"></i>
        </button>

        <div class="dropdown notification">
            <button class="nav-btn dropdown-toggle" data-bs-toggle="dropdown" type="button" aria-expanded="false">
                <i class="fa-solid fa-bell"></i>
                <?php if ($notifCount > 0): ?>
                <span class="notification-count"><?= $notifCount ?></span>
                <?php endif; ?>
            </button>
            <ul class="dropdown-menu dropdown-menu-end shadow notification-menu">
                <li class="dropdown-header">Recent activity</li>
                <?php if ($notifCount > 0): ?>
                    <?php foreach ($notifications as $n): ?>
                    <li>
                        <span class="dropdown-item-text small">
                            <?= e(strlen($n["activity"]) > 60 ? substr($n["activity"], 0, 57) . "..." : $n["activity"]) ?>
                            <br><span class="text-muted"><?= e($n["created_at"]) ?></span>
                        </span>
                    </li>
                    <?php endforeach; ?>
                <?php else: ?>
                    <li><span class="dropdown-item-text text-muted">No notifications</span></li>
                <?php endif; ?>
            </ul>
        </div>

        <div class="dropdown">
            <button class="profile-btn dropdown-toggle" data-bs-toggle="dropdown" type="button">
                <div class="profile-avatar"><?= e(strtoupper(substr($userName, 0, 1))) ?></div>
                <div class="profile-info">
                    <h6><?= e($userName) ?></h6>
                    <span><?= e($userRole) ?></span>
                </div>
            </button>
            <ul class="dropdown-menu dropdown-menu-end shadow">
                <li>
                    <a class="dropdown-item" href="<?= $base ?>/pages/settings.php">
                        <i class="fa-solid fa-user"></i> My Profile
                    </a>
                </li>
                <li>
                    <a class="dropdown-item" href="<?= $base ?>/pages/settings.php">
                        <i class="fa-solid fa-gear"></i> Settings
                    </a>
                </li>
                <li><hr class="dropdown-divider"></li>
                <li>
                    <a class="dropdown-item text-danger" href="<?= $base ?>/auth/logout.php">
                        <i class="fa-solid fa-right-from-bracket"></i> Logout
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>
