<?php
session_start();
session_unset();
session_destroy();
// Redirect to unified login/registration page (index.php)
header("Location: ../index.php");
exit();
?>
