<?php
$requiredRole = 'staff';
require '../middleware.php';
checkAccess('pageB');
echo "Welcome to Page B";
?>