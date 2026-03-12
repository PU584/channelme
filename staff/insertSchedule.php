<?php
include '../connection.php';

$connection = new Connection('localhost', 'root', '', 'channel_me_test');
$conn = $connection->getConnection();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Debugging: Print all POST data (Remove after testing)
    // var_dump($_POST);

    // Retrieve basic details
    $doctorId = $_POST['doctorId'] ?? '';
    $specialization = $_POST['specializationID'] ?? '';

    if (empty($doctorId) || empty($specialization)) {
        die("Error: Doctor ID and Specialization ID are required.");
    }

    // Days of the week
    $days = ["monday", "tuesday", "wednesday", "thursday", "friday", "saturday", "sunday"];

    // Initialize arrays for dynamic SQL query
    $fields = ["doctor_id", "specialization_id"];
    $placeholders = ["?", "?"];
    $values = [$doctorId, $specialization];
    $types = "ss"; // String type for doctor_id and specialization_id

    foreach ($days as $day) {
        // Process Morning and Evening slots for each day
        foreach (["morning", "evening"] as $slot) {
            $start = $_POST["{$day}MorningStart"] ?? ''; // Get morning start time
            $end = $_POST["{$day}MorningEnd"] ?? ''; // Get morning end time
            $max = $_POST["{$day}MorningMax"] ?? ''; // Get max appointments for morning slot

            if ($slot === "evening") {
                $start = $_POST["{$day}EveningStart"] ?? ''; // Get evening start time
                $end = $_POST["{$day}EveningEnd"] ?? ''; // Get evening end time
                $max = $_POST["{$day}EveningMax"] ?? ''; // Get max appointments for evening slot
            }

            // Check if any value is empty and stop execution if true
            if ($start === '' || $end === '' || $max === '') {
                die("Error: Missing values for $day $slot slot. Please check your form inputs.");
            }

            // Add the day, slot, and max to the fields for the SQL query
            $fields[] = "{$day}_{$slot}_start";
            $fields[] = "{$day}_{$slot}_end";
            $fields[] = "{$day}_{$slot}_max";

            // Add placeholders for each slot
            $placeholders[] = "?, ?, ?";
            $values[] = $start;
            $values[] = $end;
            $values[] = $max;
            $types .= "sss"; // Three string types per slot
        }
    }

    // Prepare the SQL query dynamically
    $sql = "INSERT INTO doctor_schedule (" . implode(", ", $fields) . ") VALUES (" . implode(", ", $placeholders) . ")";

    // Prepare and execute the SQL statement
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param($types, ...$values);

        if ($stmt->execute()) {
            echo "Doctor schedule added successfully.";
        } else {
            echo "Error: " . $stmt->error;
        }
        $stmt->close();
    } else {
        echo "Error: " . $conn->error;
    }
}
?>
