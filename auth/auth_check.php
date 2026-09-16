<?php

require_once __DIR__ . "/../config/session.php";

if (!isset($_SESSION["user_id"])) {
    redirect("auth/login.php");
}
