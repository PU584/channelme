<?php
$requiredRole = 'staff';
require '../middleware.php';
checkAccess('page2');
echo "Welcome to Page 2";
?>