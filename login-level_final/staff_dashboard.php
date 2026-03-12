<?php
// staff_dashboard.php
$requiredRole = 'staff';
include 'middleware.php';
echo "Welcome, " . $_SESSION['name'] . "!";

echo "<h4><a href='logout.php'>Logout</a></h4>";

// Display links to accessible pages only if there are pages
if (!empty($_SESSION['pages'])) {
    echo "<h3>Accessible Pages:</h3>";
    foreach ($_SESSION['pages'] as $page) {
        echo "<a href='pages/$page.php'>$page</a><br>";
    }
}
?>
