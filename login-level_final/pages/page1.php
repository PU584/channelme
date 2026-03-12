<?php
$requiredRole = 'staff';
require '../middleware.php';
checkAccess('page1');
echo "Welcome to Page 1";
?>