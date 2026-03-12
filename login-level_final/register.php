<?php
// Step 2: User Registration (register.php)
session_start();
include 'db.php';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $role = $_POST['role'] ?: 'customer';
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Check if passwords match
    if ($password !== $confirm_password) {
        $_SESSION['error'] = "Passwords do not match!";
        header("Location: register.php");
        exit();
    }
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Check if email or phone already exists
    $db = Database::getInstance();  // Get the database connection
    $conn = $db->getConnection();   // Retrieve the connection

    // Prepare the query to check both email and phone
    $stmt = $conn->prepare("SELECT 
                            (SELECT COUNT(*) FROM users WHERE email = ?) AS email_count,
                            (SELECT COUNT(*) FROM users WHERE phone = ?) AS phone_count");
    $stmt->bind_param("ss", $email, $phone);
    $stmt->execute();
    $stmt->bind_result($email_count, $phone_count);
    $stmt->fetch();
    $stmt->close();

    // Set an error message if email already exists
    if ($email_count > 0) {
        $_SESSION['error'] = "Email is already registered!";
        header("Location: register.php");
        exit();
    }
    // Set an error message if phone already exists
    if ($phone_count > 0) {
        $_SESSION['error'] = "Phone number is already registered!";
        header("Location: register.php");
        exit();
    }

    // Generate user_id (e.g., D-001, S-002)
    $role_prefix = strtoupper(substr($role, 0, 1)); // Get first letter of role (C, S, D)
    
    // Get last user ID for the same role
    $stmt = $conn->prepare("SELECT user_id FROM users WHERE user_id LIKE ? ORDER BY user_id DESC LIMIT 1");
    $search_pattern = $role_prefix . "-%";
    $stmt->bind_param("s", $search_pattern);
    $stmt->execute();
    $stmt->bind_result($last_user_id);
    $stmt->fetch();
    $stmt->close();

    // Extract numeric part and increment it
    if ($last_user_id) {
        $last_number = (int)substr($last_user_id, 2);
        $new_number = $last_number + 1;
    } else {
        $new_number = 1;
    }

    $formatted_number = str_pad($new_number, 3, "0", STR_PAD_LEFT);
    $user_id = $role_prefix . "-" . $formatted_number;

    // Insert into `users` table
    $stmt = $conn->prepare("INSERT INTO users (user_id, first_name, last_name, role, email, phone, password) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssss", $user_id, $first_name, $last_name, $role, $email, $phone, $hashed_password);
    
    if ($stmt->execute()) {
        if ($role == 'customer') {
            //$gender = $_POST['gender'];
            $dob = $_POST['dob'];
            $stmt = $conn->prepare("INSERT INTO customer_details (user_id, dob) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $user_id, $dob);
            $stmt->execute();
        } elseif ($role == 'staff') {
            $accessible_pages = isset($_POST['accessible_pages']) ? implode(',', $_POST['accessible_pages']) : '';
            $stmt = $conn->prepare("INSERT INTO staff_details (user_id, accessible_pages) VALUES (?, ?)");
            $stmt->bind_param("ss", $user_id, $accessible_pages);
            $stmt->execute();
        } elseif ($role == 'doctor') {
            $work_from = $_POST['work_from'];
            $medical_type = $_POST['medical_type'];
            $description = $_POST['description'];
            $image_path = $_POST['image_path'];
            $stmt = $conn->prepare("INSERT INTO doctor_details (user_id, work_from, medical_type, description, image_path) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sssss", $user_id, $work_from, $medical_type, $description, $image_path);
            $stmt->execute();
        }
        $_SESSION['success'] = "Registration successful! Please log in.";
        header("Location: logout.php");
        exit();
    } else {
        $_SESSION['error'] = "Error registering user!";
        header("Location: register.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Registration</title>
    <style>
        body {
            font-family: sans-serif;
            margin: 20px;
        }
        form {
            max-width: 400px;
            margin: 20px auto;
            padding: 20px;
            border: 1px solid #ccc;
        }
        label {
            display: block;
            margin-bottom: 5px;
        }
        input, select, textarea {
            width: 100%;
            padding: 8px;
            margin-bottom: 10px;
            box-sizing: border-box;
        }
        button {
            background-color: #4CAF50;
            color: white;
            padding: 10px 15px;
            border: none;
            cursor: pointer;
        }
        .error, .success {
            color: white;
            padding: 10px;
            margin-bottom: 10px;
            text-align: center;
        }
        .error { background-color: red; }
        .success { background-color: green; }
    </style>
</head>
<body>

    <div id="register">
        <h2>Register</h2>

        <?php
        if (isset($_SESSION['error'])) {
            echo "<div class='error'>" . $_SESSION['error'] . "</div>";
            unset($_SESSION['error']);
        }
        if (isset($_SESSION['success'])) {
            echo "<div class='success'>" . $_SESSION['success'] . "</div>";
            unset($_SESSION['success']);
        }
        ?>

        <form action="register.php" method="post">
            <label for="first_name">First Name:</label>
            <input type="text" name="first_name" required>

            <label for="last_name">Last Name:</label>
            <input type="text" name="last_name" required>

            <label for="role">Role:</label>
            <select name="role">
                <option value="customer">Customer</option>
                <option value="staff">Staff</option>
                <option value="doctor">Doctor</option>
            </select>

            <label for="email">Email:</label>
            <input type="email" name="email" required>

            <label for="phone">Phone:</label>
            <input type="tel" name="phone" required>

            <label for="password">Password:</label>
            <input type="password" name="password" required>

            <label for="confirm_password">Confirm Password:</label>
            <input type="password" name="confirm_password" required>

            <div id="customerFields" style="display: none;">
                <label for="dob">Date of Birth:</label>
                <input type="date" name="dob">

                <label>Gender:</label>
                <input type="checkbox" name="gender" value="male"> Male<br>
                <input type="checkbox" name="gender" value="female"> Female<br>
                <input type="checkbox" name="gender" value="other"> Other<br>
            </div>

            <div id="staffFields" style="display: none;">
                <label>Select Pages:</label>
                <input type="checkbox" name="accessible_pages[]" value="page1"> Page 1<br>
                <input type="checkbox" name="accessible_pages[]" value="page2"> Page 2<br>
                <input type="checkbox" name="accessible_pages[]" value="pageA"> Page A<br>
                <input type="checkbox" name="accessible_pages[]" value="pageB"> Page B<br>
            </div>

            <div id="doctorFields" style="display: none;">
                <label for="work_from">Work From:</label>
                <input type="text" name="work_from" value="retired">
                
                <label for="medical_type">Medical Type:</label>
                <input type="text" name="medical_type">
                
                <label for="description">Description:</label>
                <textarea name="description"></textarea>
                
                <label for="image_path">Image Path:</label>
                <input type="text" name="image_path">
            </div>

            <button type="submit">Register</button>
        </form>

        <p>Already have an account? <a href="logout.php">Login</a></p>
    </div>

    <script>
        document.querySelector('select[name="role"]').addEventListener('change', function () {
            let role = this.value;
            document.getElementById('customerFields').style.display = (role === 'customer') ? 'block' : 'none';
            document.getElementById('staffFields').style.display = (role === 'staff') ? 'block' : 'none';
            document.getElementById('doctorFields').style.display = (role === 'doctor') ? 'block' : 'none';
        });
    </script>

</body>
</html>
