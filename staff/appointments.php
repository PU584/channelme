<?php
include '../connection.php';

// Check if doctor_id exists
if(!isset($_GET['doctor_id']) || empty($_GET['doctor_id'])) {
    header("Location: displayDoctors.php");
    exit();
}

$doctor_id = $_GET['doctor_id'];


// Create database connection
$connection = new Connection('localhost', 'root', '', 'channel_me_test');
$conn = $connection->getConnection();

// Fetch doctor details
// Fetch doctor details (correct binding type)
$stmt = $conn->prepare("SELECT * FROM adddoctors WHERE doctor_id = ?");
$stmt->bind_param("s", $doctor_id); // Changed "i" to "s"
$stmt->execute();
$result = $stmt->get_result();
if($result->num_rows === 0) {
    header("Location: doctors.php");
    exit();
}

$doctor = $result->fetch_assoc();

// Handle form submission
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Add your appointment booking logic here
    // Process $_POST data and insert into appointments table
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Appointment | <?php echo $doctor['doctor_name']; ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: #f8f9fa;
        }
        
        .doctor-header {
            background: white;
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            padding: 2rem;
            margin: 2rem auto;
            max-width: 800px;
        }
        
        .doctor-img {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid #007bff;
            margin-bottom: 1rem;
        }
        
        .appointment-form {
            background: white;
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            padding: 2rem;
            margin: 2rem auto;
            max-width: 800px;
        }
        
        .form-control:focus {
            border-color: #007bff;
            box-shadow: 0 0 0 0.2rem rgba(0,123,255,.25);
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Doctor Information Section -->
        <div class="doctor-header text-center">
            <?php 
            $images = explode(',', $doctor['images']);
            $firstImage = !empty($images[0]) ? $images[0] : 'default.png';
            ?>
            <img src="<?php echo $firstImage; ?>" class="doctor-img" alt="Doctor Image">
            <h2>Dr. <?php echo $doctor['doctor_name']; ?></h2>
            <div class="badge bg-primary mb-2">
                <?php echo $doctor['specialization']; ?>
            </div>
           
        </div>

        <!-- Appointment Form -->
        <div class="appointment-form">
            <h3 class="mb-4">Book Your Appointment</h3>
            <form method="POST">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Full Name</label>
                        <input type="text" class="form-control" name="patient_name" required>
                    </div>
                    
                    <div class="col-md-6">
                        <label class="form-label">Contact Number</label>
                        <input type="tel" class="form-control" name="patient_phone" required>
                    </div>
                    
                    <div class="col-md-6">
                        <label class="form-label">Appointment Date</label>
                        <input type="date" class="form-control" name="appointment_date" required>
                    </div>
                    
                    <div class="col-md-6">
                        <label class="form-label">Preferred Time</label>
                        <select class="form-select" name="appointment_time" required>
                            <option value="09:00">09:00 AM</option>
                            <option value="10:00">10:00 AM</option>
                            <option value="11:00">11:00 AM</option>
                            <option value="14:00">02:00 PM</option>
                            <option value="15:00">03:00 PM</option>
                        </select>
                    </div>
                    
                    <div class="col-12">
                        <label class="form-label">Additional Notes</label>
                        <textarea class="form-control" rows="3" name="notes"></textarea>
                    </div>
                    
                    <div class="col-12">
                        <input type="hidden" name="doctor_id" value="<?php echo $doctor_id; ?>">
                        <button type="submit" class="btn btn-primary">Confirm Appointment</button>
                        <a href="doctors.php" class="btn btn-secondary">Cancel</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</body>
</html>