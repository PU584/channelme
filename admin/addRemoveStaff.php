<?php

session_start(); // Start the session

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    include '../connection.php';

    $staff_id = isset($_POST['staffId']) ? trim($_POST['staffId']) : null;
    $first_name = isset($_POST['firstName']) ? trim($_POST['firstName']) : null;
    $last_name = isset($_POST['lastName']) ? trim($_POST['lastName']) : null;
    $email = isset($_POST['email']) ? trim($_POST['email']) : null;
    $position = isset($_POST['position']) ? trim($_POST['position']) : null;
    $accessible_pages = isset($_POST['accessiblePages']) ? json_encode($_POST['accessiblePages']) : json_encode([]);

    if (!$staff_id || !$first_name || !$last_name || !$email || !$position) {
        die("<h4 style='color: red;'>Error: All fields are required!</h4>");
    }

    $connection = new Connection('localhost', 'root', '', 'channel_me_test');  
    $conn = $connection->getConnection();      

    // Check if email already exists
    $check_email_sql = "SELECT * FROM staff WHERE email = ?";
    $check_email_stmt = $conn->prepare($check_email_sql);
    $check_email_stmt->bind_param("s", $email);
    $check_email_stmt->execute();
    $check_email_result = $check_email_stmt->get_result();
    $email_exists = $check_email_result->num_rows > 0;
    $check_email_stmt->close();

    if ($email_exists) {
        die("<h4 style='color: red;'>Error: Email already exists!</h4>");
    }

    // Handle multiple image uploads
    $image_paths = [];
    if (!empty($_FILES['staffImages']['name'][0])) {
        $upload_dir = "../staff/images/"; // Save images inside the staff folder
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        foreach ($_FILES['staffImages']['tmp_name'] as $key => $tmp_name) {
            if ($_FILES['staffImages']['error'][$key] === 0) {
                $file_name = time() . "_" . basename($_FILES['staffImages']['name'][$key]);
                $target_path = $upload_dir . $file_name;
                if (move_uploaded_file($tmp_name, $target_path)) {
                    $image_paths[] = "staff/images/" . $file_name; // Relative path
                }
            }
        }
    }

    if (empty($image_paths)) {
        die("<h4 style='color: red;'>Error: No images were uploaded!</h4>");
    }

    $image_paths_str = json_encode($image_paths);
    
    // Store the latest image in a session
    $_SESSION['latest_staff_image'] = end($image_paths);

    $sql = "INSERT INTO staff (staff_id, first_name, last_name, email, position, profile_images, accessible_pages) 
            VALUES (?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("issssss", $staff_id, $first_name, $last_name, $email, $position, $image_paths_str, $accessible_pages);

    if ($stmt->execute()) {
        echo "<h4>Staff Member Added Successfully!</h4>";
    } else {
        echo "<h4 style='color: red;'>Error: " . $stmt->error . "</h4>";
    }

    $stmt->close();
    $conn->close();

}
?>
