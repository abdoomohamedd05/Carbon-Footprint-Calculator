<?php
require_once "config/session.php";

if (isset($_SESSION["user_id"])) {
    redirect("pages/dashboard.php");
}

redirect("auth/login.php");
