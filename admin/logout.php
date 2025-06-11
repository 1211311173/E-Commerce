<?php
require_once '../includes/session_helper.php';
clearSession();
header("Location:login.php");
exit;
?>