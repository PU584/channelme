<?php
session_start();
if (isset($_SESSION['latest_staff_image'])) {
    echo json_encode(['image' => $_SESSION['latest_staff_image']]);
} else {
    echo json_encode(['image' => 'staff/images/staff.avif']); // Default image if no staff added
}
?>
