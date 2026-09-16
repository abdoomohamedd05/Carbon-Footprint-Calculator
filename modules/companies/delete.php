<?php

require_once("../../auth/auth_check.php");
require_once("../../config/database.php");

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    redirect("modules/companies/index.php");
}

$id = (int) ($_POST["id"] ?? 0);

$stmt = $conn->prepare("SELECT company_name FROM companies WHERE id = ?");
$stmt->execute([$id]);
$company = $stmt->fetch();

if ($company) {
    $delete = $conn->prepare("DELETE FROM companies WHERE id = ?");
    $delete->execute([$id]);
    log_activity($conn, "Company deleted: {$company["company_name"]}");
    flash("success", "Company deleted successfully.");
}

redirect("modules/companies/index.php");
