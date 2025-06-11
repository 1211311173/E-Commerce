<?php
require_once '../includes/session_helper.php';

// Restrict access to admin panel for non-admin users
if (!isAdmin()) {
    header("location:../index.php?AdminRestricted");
    exit;
}
?>