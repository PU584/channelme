<?php
session_start();
include 'db.php';

// If user is already logged in, redirect to their dashboard
if (isset($_SESSION['user_id'])) {
    redirectToDashboard($_SESSION['role']);
}

// If "Remember Me" cookie exists, log in the user automatically
if (isset($_COOKIE['user_id']) && isset($_COOKIE['role'])) {
    $_SESSION['user_id'] = $_COOKIE['user_id'];
    $_SESSION['role'] = $_COOKIE['role'];
    $_SESSION['name'] = $_COOKIE['name'];
    redirectToDashboard($_COOKIE['role']);
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $identifier = $_POST['identifier']; // Email or phone
    $password = $_POST['password'];
    $remember = isset($_POST['remember']); // Checkbox for "Remember Me"

    // Get the database connection
    $db = Database::getInstance();
    $conn = $db->getConnection();
    $stmt = $conn->prepare("SELECT user_id, first_name, last_name, role, password FROM users WHERE email = ? OR phone = ?");
    $stmt->bind_param("ss", $identifier, $identifier);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();

    if ($result && password_verify($password, $result['password'])) {
        $_SESSION['user_id'] = $result['user_id'];
        $_SESSION['role'] = $result['role'];
        $_SESSION['name'] = $result['first_name'] . ' ' . $result['last_name'];

        // If "Remember Me" is checked, store login details in cookies
        if ($remember) {
            setcookie("user_id", $result['user_id'], time() + 86400, "/"); // 1-day expiry
            setcookie("role", $result['role'], time() + 86400, "/");
            setcookie("name", $_SESSION['name'], time() + 86400, "/");
        }
        redirectToDashboard($result['role']);
    } else {
        $error_message = "Invalid login credentials. Please try again.";
    }
}

// Function to redirect users based on their role
function redirectToDashboard($role) {
    $db = Database::getInstance();
    $conn = $db->getConnection();
    if ($role == 'staff') {
        $stmt = $conn->prepare("SELECT accessible_pages FROM staff_details WHERE user_id = ?");
        $stmt->bind_param("s", $_SESSION['user_id']);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        $_SESSION['pages'] = explode(',', $result['accessible_pages']);
        header("Location: staff_dashboard.php");
    } elseif ($role == 'doctor') {
        header("Location: doctor_dashboard.php");
    } else {
        header("Location: ../client and landing/client/client.php");
    }
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | eChanneling</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }
        body {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    display: flex;
    flex-direction: column;
    align-items: center;
    min-height: 100vh;
    padding-top: 0; /* Adjust to remove space at top */
}

        nav {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1rem 2rem;
    background: white;
    color: rgb(19, 1, 1);
    position: sticky;
    z-index: 1000;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    transition: top 0.3s ease;
    width: 100%;
    margin: 0; /* Ensure no margin is causing space at the top */
    padding-top: 0; /* Ensure there's no padding pushing the nav down */
    position: fixed; /* Use fixed to ensure it's at the top */
    top: 0;
    left: 0;
    right: 0;
    z-index: 1000;
}


nav.hide {
    top: -100px;
}

nav .logo {
    height: 80px;
}

nav ul {
    list-style: none;
    display: flex;
    flex-grow: 1;
    justify-content: center;
}

nav ul li {
    margin-left: 30px;
}

nav ul li a {
    text-decoration: none;
    color: rgb(19, 1, 1);
    font-weight: 500;
    transition: all 0.3s ease;
    padding: 10px 14px;
    border-radius: 5px;
}

nav ul li a:hover {
    color: #3498db;
    background-color: rgba(255, 255, 255, 0.1);
    border-radius: 8px;
    padding: 12px 18px;
    border: 2px solid #3498db;
}
        /* Set up a flex container inside the premium-header */
        /* Set up a flex container inside the premium-header */
/* Set up a flex container inside the premium-header */
/* Set up a flex container inside the premium-header */
.premium-header {
    position: relative; /* Ensure positioning context for the logo */
    display: flex;
    justify-content: space-between; /* Distributes space between elements */
    align-items: center; /* Keeps the text centered vertically */
    background: #2563eb;
    color: white;
    padding: 2rem;
    text-align: left;
    width: 100%;
    max-width: 1000px;
    margin: 20px auto;
    border-radius: 8px;
    margin-top: 100px; /* Adjust this value to bring the header down from the nav bar */
}



/* Wrapper for the logo and text */
.premium-header .premium-text {
    display: flex;
    flex-direction: column; /* Stack the text vertically */
    justify-content: center; /* Centers the text vertically */
}

/* Logo positioning */
.premium-header .logo {
    position: absolute; /* Take it out of the flow */
    top: 0; /* Align the logo to the top */
    margin-left: 100px; /* Align the logo to the left */
    max-width: 200px; /* Adjusted size */
    margin-top: 80px; /* Ensure logo stays at the top */
}


        /* Premium header text styles */
        .premium-header h1 {
            font-size: 1.8rem;
            margin-bottom: 0.5rem;
        }

        .premium-header p {
            color: #e0e7ff;
            font-size: 1rem;
            max-width: 600px;
        }

        /* Login container inside premium-header */
        .login-container {
            background: #ffffff;
            padding: 2.5rem;
            border-radius: 15px;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12);
            width: 100%;
            max-width: 440px;
            margin-left: 20px;
        }

        /* Modify existing body style */
      

        .signin-header {
            margin-bottom: 1.5rem;
        }

        .signin-header h2 {
            color: #2d3748;
            font-size: 1.8rem;
            margin-bottom: 0.5rem;
        }

        .signin-header p {
            color: #718096;
            font-size: 0.95rem;
        }

        .input-group {
            margin-bottom: 1.2rem;
        }

        .input-group label {
            display: block;
            color: #4a5568;
            font-size: 0.9rem;
            margin-bottom: 0.5rem;
            font-weight: 500;
        }

        .input-group input {
            width: 100%;
            padding: 0.9rem;
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            font-size: 0.95rem;
            transition: border-color 0.3s ease;
        }

        .input-group input:focus {
            border-color: #4c51bf;
            outline: none;
        }

        .options-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin: 1.5rem 0;
            font-size: 0.9rem;
        }

        .remember-me {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: #4a5568;
        }

        .forgot-password a {
            color: #4c51bf;
            text-decoration: none;
            font-weight: 500;
        }

        .login-btn {
            width: 100%;
            background: #4c51bf;
            color: white;
            padding: 1rem;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.3s ease;
        }

        .login-btn:hover {
            background: #434190;
        }

        .divider {
            text-align: center;
            color: #718096;
            margin: 1.5rem 0;
            position: relative;
        }

        .divider::before,
        .divider::after {
            content: "";
            flex: 1;
            border-bottom: 1px solid #e2e8f0;
            position: absolute;
            top: 50%;
            width: 45%;
        }

        .divider::before {
            left: 0;
        }

        .divider::after {
            right: 0;
        }

        .alternative-options {
            display: flex;
            justify-content: center;
            gap: 1rem;
            margin-top: 1.5rem;
        }

        .alternative-options a {
            color: #4c51bf;
            text-decoration: none;
            font-weight: 500;
            font-size: 0.9rem;
        }

        .error-message {
            color: #e53e3e;
            font-size: 0.9rem;
            margin-bottom: 1rem;
            text-align: center;
        }
    </style>
</head>
<body>

<nav>

    <img src="logo.png" alt="Logo" class="logo">
        <ul>
            <li><a href="../client and landing/landing/landing.html">Home</a></li>
            <li><a href="/client and landing/landing/landing.html#container">About us</a></li>
            <li><a href="/client and landing/client/client.html">Client</a></li>
            <li><a href="/client and landing/landing/landing.html#service_container">Services</a></li>
            <li><a href="/client and landing/landing/landing.html#contact-section">Contact us</a></li>
        </ul>


      
    </nav>


    <div class="premium-header">
        <img src="logo.png" alt="Logo" class="logo">
        <div class="premium-text">
            <h1>Welcome to <span style="color: black;">Muthuneth Channel Center</span></h1>
            <p>Become a premium member and enjoy<br/> a 15% on your ECL service<br/> fee.</p>
        </div>
        <div class="login-container">
            <div class="signin-header">
                <h2>Sign in</h2>
                <p>Please enter your user name and password to login or login with your mobile number</p>
            </div>

            <?php if (isset($error_message)) { ?>
                <div class="error-message"><?php echo $error_message; ?></div>
            <?php } ?>

            <form action="login.php" method="post">
                <div class="input-group">
                    <label for="identifier">Phone-No / Email</label>
                    <input type="text" id="identifier" name="identifier" required>
                </div>

                <div class="input-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                </div>

                <div class="options-row">
                    <label class="remember-me">
                        <input type="checkbox" name="remember">
                        Keep me signed in
                    </label>
                    <span class="forgot-password">
                        <a href="#">Forgot password?</a>
                    </span>
                </div>

                <button type="submit" class="login-btn">Sign In</button>

                <div class="divider">Or</div>

                <div class="alternative-options">
                    
                <a href="register.php">Don't have an account? Sign up</a>

                </div>
            </form>
        </div>
    </div>
</body>
</html>
