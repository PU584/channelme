<?php
include '../connection.php';

$connection = new Connection('localhost', 'root', '', 'channel_me_test');
$conn = $connection->getConnection();

// Get all doctors with their specialization
$sql = "
    SELECT d.doctor_id, d.doctor_name, d.images, s.specialization 
    FROM adddoctors d
    JOIN specialization s ON d.specialization_id = s.specialization_id
";
$result = $conn->query($sql);

// Get unique specializations for dropdown
$specializations = $conn->query("SELECT DISTINCT specialization FROM specialization ORDER BY specialization");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Doctors List</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        .doctor-card {
            border-radius: 10px;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
            padding: 15px;
            background-color: #fff;
            transition: all 0.3s ease-in-out;
            text-align: center;
            margin-bottom: 20px;
        }
        .doctor-card:hover {
            transform: scale(1.03);
            box-shadow: 0px 6px 12px rgba(0, 0, 0, 0.15);
        }
        .doctor-img {
            width: 100%;
            height: 250px;
            object-fit: contain;
            border-radius: 10px;
            background-color: #f8f9fa;
            padding: 10px;
        }
        .channel-btn {
            margin-top: 10px;
            display: inline-block;
            background-color: #007bff;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            text-decoration: none;
            transition: background-color 0.3s;
        }
        .channel-btn:hover {
            background-color: #0056b3;
        }
        .search-container {
            margin: 30px 0;
        }
        .col-md-4 {
            transition: all 0.3s ease-in-out;
        }
    </style>
</head>
<body class="bg-light">
    <div class="container py-5">
        <!-- Search Filter -->
        <div class="search-container">
            <div class="row justify-content-center">
                <div class="col-md-6">
                    <div class="input-group">
                        <select class="form-select" id="specializationFilter">
                            <option value="">All Specializations</option>
                            <?php while($spec = $specializations->fetch_assoc()): ?>
                                <option value="<?= $spec['specialization'] ?>">
                                    <?= $spec['specialization'] ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                        <button class="btn btn-primary" type="button" onclick="filterDoctors()">
                            Search
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <h2 class="text-center mb-4">Doctors List</h2>
        <div class="row" id="doctorContainer">
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="col-md-4 mb-4">
                    <div class="doctor-card p-3">
                        <?php 
                        $images = explode(',', $row['images']); 
                        $firstImage = !empty($images[0]) ? $images[0] : 'default.png'; 
                        ?>
                        <img src="<?= $firstImage ?>" class="doctor-img" alt="Doctor Image">
                        <h5 class="mt-3">Dr. <?= $row['doctor_name'] ?></h5>
                        <p><strong>Specialization:</strong> <?= $row['specialization'] ?></p>
                       
                        <a href="appointment.php?doctor_id=<?= $row['doctor_id'] ?>" class="channel-btn">
                            Channel Now
                        </a>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </div>

    <script>
    function filterDoctors() {
        const specialization = document.getElementById('specializationFilter').value.toLowerCase();
        const doctorCards = document.querySelectorAll('.col-md-4');
        
        doctorCards.forEach(card => {
            const cardSpecialization = card.querySelector('p:nth-of-type(1)').textContent
                .split(':')[1]
                .trim()
                .toLowerCase();
            
            if (specialization === '' || cardSpecialization === specialization) {
                card.style.display = 'block';
            } else {
                card.style.display = 'none';
            }
        });
    }
    </script>

</body>
</html>

<?php
$conn->close();
?>
