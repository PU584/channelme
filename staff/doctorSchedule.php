

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <link rel="stylesheet" href="doctorSchedule.css">
    <style>
        .left-aligned {
            text-align: left;
            margin-left: 0;
        }
        .btn-purple {
            background-color: purple;
            color: white;
        }
        .btn-purple:hover {
            background-color: #d8b6ff;
        }
        .btn-success:hover {
            background-color: #a3e4a3;
        }
        .btn-warning:hover {
            background-color: #ffe699;
        }
        .image-preview-container {
            margin-top: 15px;
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }
        .image-preview {
            position: relative;
            width: 100px;
            height: 100px;
        }
        .image-preview img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 8px;
        }
        .remove-image-btn {
            position: absolute;
            top: 5px;
            right: 5px;
            background: red;
            color: white;
            border: none;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg shadow-sm p-3">
        <div class="container d-flex align-items-center">
            <div class="d-flex align-items-center">
                <a class="navbar-brand fw-bold" href="#">Staff Dashboard</a>
                <h1 style="font-size: 40px; margin-left: 0;font-family:'Franklin Gothic Medium', 'Arial Narrow', Arial, sans-serif;font-weight: 300;">
                    Staff Dashboard
                </h1>
            </div>
            <div class="ms-auto d-flex align-items-center">
                <button class="btn btn-primary me-2">Login</button>
                <button class="btn btn-danger me-3">Logout</button>
                <img id="staffProfileImage" src="staff/images/staff.avif" alt="Staff Profile" class="profile-img">
            </div>
        </div>
    </nav>
    
    <script>
        // Fetch the latest staff image dynamically
        fetch('../admin/getStaffImage.php')
            .then(response => response.json())
            .then(data => {
                document.getElementById('staffProfileImage').src = '../' + data.image;
            })
            .catch(error => console.error('Error loading image:', error));
    </script>
 <div class="d-flex gap-3 flex-wrap mb-4 button-container justify-content-start">
    <button class="btn btn-success" onclick="window.location.href='doctorSchedule.html'">
        <i class="bi bi-calendar-event"></i> Time Slots
    </button>
    <button class="btn btn-purple"onclick="window.location.href='staff.html'">
        <i class="bi bi-calendar"></i> view inquiries
    </button>
    <button class="btn btn-purple" onclick="window.location.href='AddRemoveDoctor.html'">
        <i class="bi bi-person-plus"></i> Add/Remove Doctors
    </button>
    <button class="btn btn-warning" onclick="window.location.href='staff.html'">
        <i class="bi bi-calendar-check"></i> View Appointments
    </button>
</div>
    
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Doctor Schedule Form</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-4">
    <div class="card shadow-lg">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0">Doctor Schedule Form</h4>
        </div>
        <div class="card-body">
            <form id="addDoctorForm" method="post" action="insertSchedule.php">
                <!-- Doctor ID and Specialization -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="doctorId" class="form-label">Doctor ID</label>
                        <input type="text" class="form-control" id="doctorId" name="doctorId" required>
                    </div>
                    <div class="col-md-6">
                        <label for="specialization" class="form-label">Specialization ID</label>
                        <input type="text" class="form-control" id="specialization" name="specializationID" required>
                    </div>
                </div>

                <!-- Time Slots for Each Day -->
                <div class="row">
                    <?php 
                    $days = ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday"];
                    foreach ($days as $day) { ?>
                        <div class="col-md-6 mb-3">
                            <label class="form-label"><?= $day ?> Morning Slot (7:00 AM - 9:00 AM)</label>
                            <div class="d-flex">
                                <input type="time" class="form-control" name="<?= strtolower($day) ?>MorningStart" required>
                                <span class="mx-2">to</span>
                                <input type="time" class="form-control" name="<?= strtolower($day) ?>MorningEnd" required>
                            </div>
                            <label class="form-label mt-2">Max Appointments</label>
                            <input type="number" class="form-control" name="<?= strtolower($day) ?>MorningMax" min="1" required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label"><?= $day ?> Evening Slot (5:00 PM - 7:00 PM)</label>
                            <div class="d-flex">
                                <input type="time" class="form-control" name="<?= strtolower($day) ?>EveningStart" required>
                                <span class="mx-2">to</span>
                                <input type="time" class="form-control" name="<?= strtolower($day) ?>EveningEnd" required>
                            </div>
                            <label class="form-label mt-2">Max Appointments</label>
                            <input type="number" class="form-control" name="<?= strtolower($day) ?>EveningMax" min="1" required>
                        </div>
                    <?php } ?>
                </div>
                
                <!-- Submit Button -->
                <div class="row">
                    <div class="col-12 mt-4">
                        <button type="submit" class="btn btn-success">Add Doctor</button>
                        <button type="button" class="btn btn-danger">Remove Doctor</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>




    <script src="doctorSchedule.js"></script>
</body>
</html>
