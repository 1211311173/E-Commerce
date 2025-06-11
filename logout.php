<?php
require_once 'includes/session_helper.php';
clearSession();
header("location:index.php?SuccessfullyLoggedout");
exit;
?>