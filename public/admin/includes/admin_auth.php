<?php
// admin/includes/admin_auth.php
session_start();

if (empty($_SESSION['admin_id'])) {
    header("Location: ../login.php?auth=required");
    exit;
}
