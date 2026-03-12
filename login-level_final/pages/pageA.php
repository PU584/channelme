<?php
$requiredRole = 'staff';
require '../middleware.php';
checkAccess('pageA');
echo "Welcome to Page A";
?>