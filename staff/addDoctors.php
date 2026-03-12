<?php 
if ($_SERVER["REQUEST_METHOD"] == "POST") {     
    include '../connection.php'; // Ensure this file correctly initializes $conn      

    // Retrieve form data     
    $doctor_id = $_POST['doctorId'];     
    $doctor_name = $_POST['doctorName'];     
    $specialization_id = $_POST['specialization_id']; // Ensure this is specialization_id, matching your form's input name

    $connection = new Connection('localhost', 'root', '', 'channel_me_test');  
    $conn = $connection->getConnection();      

    // Check if doctor name already exists
    $check_name_sql = "SELECT * FROM adddoctors WHERE doctor_name = ?";
    $check_name_stmt = $conn->prepare($check_name_sql);
    $check_name_stmt->bind_param("s", $doctor_name);
    $check_name_stmt->execute();
    $check_name_result = $check_name_stmt->get_result();

    $name_exists = $check_name_result->num_rows > 0; // Flag to track name existence
    $check_name_stmt->close();

    // Handle multiple image uploads     
    $image_paths = [];     
    $duplicate_image_found = false; // Flag to track duplicate images

    if (!empty($_FILES['doctorImages']['name'][0])) {         
        $upload_dir = "uploads/doctors/";          

        // Create the directory if it doesn't exist         
        if (!is_dir($upload_dir)) {             
            mkdir($upload_dir, 0777, true);         
        }          

        foreach ($_FILES['doctorImages']['tmp_name'] as $key => $tmp_name) {             

            $file_name = time() . "_" . basename($_FILES['doctorImages']['name'][$key]);             
            $target_path = $upload_dir . $file_name;              

            // Check if the image already exists in the database
            $image_check_sql = "SELECT * FROM adddoctors WHERE FIND_IN_SET(?, images) > 0";
            $image_check_stmt = $conn->prepare($image_check_sql);
            $image_check_stmt->bind_param("s", $target_path);
            $image_check_stmt->execute();
            $image_check_result = $image_check_stmt->get_result();

            if ($image_check_result->num_rows > 0) {
                $duplicate_image_found = true;
                break; // Stop processing further if a duplicate is found
            }

            if (move_uploaded_file($tmp_name, $target_path)) {                 
                $image_paths[] = $target_path;             
            }         
        }     
    }      

    // Display appropriate error messages
    if ($name_exists && $duplicate_image_found) {
        echo "<h4 style='color: red;'>Error: Doctor with the same name and image already exists!</h4>";
    } elseif ($name_exists) {
        echo "<h4 style='color: red;'>Error: Doctor with the same name already exists!</h4>";
    } elseif ($duplicate_image_found) {
        echo "<h4 style='color: red;'>Error: Doctor with the same image already exists!</h4>";
    } else {
        // Convert image paths array to a comma-separated string     
        $image_paths_str = implode(",", $image_paths);      

        // Insert data into database only if images are uploaded
        if (!empty($image_paths)) {
            $sql = "INSERT INTO adddoctors (doctor_id, doctor_name, specialization_id, images) VALUES (?, ?, ?, ?)";     
            $stmt = $conn->prepare($sql);     
            $stmt->bind_param("ssss", $doctor_id, $doctor_name, $specialization_id, $image_paths_str);      

            if ($stmt->execute()) {         
                echo "<h4>Doctor Added Successfully!</h4>";         
                echo "<p><strong>ID:</strong> $doctor_id</p>";         
                echo "<p><strong>Name:</strong> $doctor_name</p>";         
                echo "<p><strong>Specialization ID:</strong> $specialization_id</p>"; 

                // Display uploaded images         
                if (!empty($image_paths)) {             
                    echo "<h5>Uploaded Images:</h5>";             
                    foreach ($image_paths as $image) {                 
                        echo "<img src='$image' width='150' height='150' style='margin: 5px; border-radius: 10px;'>";             
                    }         
                }     
            } else {         
                echo "Error: " . $stmt->error;     
            }      

            $stmt->close();
        } else {
            echo "<h4 style='color: red;'>Error: No images were uploaded!</h4>";
        }
    }

    $conn->close(); 
} 
?>
