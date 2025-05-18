<?php
session_start();
require_once "includes/connect.inc.php";

$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $firstName = trim($_POST["firstName"]);
    $lastName = trim($_POST["lastName"]);
    $email = trim($_POST["email"]);
    $phone = trim($_POST["phone"]);
    $password = $_POST["password"];
    $confirmPassword = $_POST["confirmPassword"];
    $address = trim($_POST["address"]);

    // Validate inputs
    if (empty($firstName) || empty($lastName) || empty($email) || empty($password) || empty($confirmPassword) || empty($address)) {
        $error = "All fields are required except phone number.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format.";
    } elseif ($password !== $confirmPassword) {
        $error = "Passwords do not match.";
    } elseif (strlen($password) < 8) {
        $error = "Password must be at least 8 characters long.";
    } else {
        // Check if email already exists
        $stmt = $conn->prepare("SELECT CustomerID FROM Customer WHERE Email = ?");
        $stmt->execute([$email]);
        
        if ($stmt->rowCount() > 0) {
            $error = "Email already registered.";
        } else {
            // Hash password
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            
            // Insert new customer
            try {
                $stmt = $conn->prepare("INSERT INTO Customer (FirstName, LastName, Email, Phone, Password, Address) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->execute([$firstName, $lastName, $email, $phone, $hashedPassword, $address]);
                                
                $firstName = $lastName = $email = $phone = $address = '';
                $_SESSION['signup_success'] = true;
                header("Location: index.php");
                
            } catch (PDOException $e) {
                $error = "Database error: " . $e->getMessage();
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Signup</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
     <style>
       :root {
    --main-color: #6A89A7;       /* Your main color */
    --main-dark: #4A6A8A;        /* Darker shade */
    --main-light: #F5F8FA;       /* Light background */
    --text-color: #2C3E50;       /* Dark text */
    --error-color: #E74C3C;      /* Error red */
    --success-color: #27AE60;    /* Success green */
}

body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background-color: var(--main-light);
    margin: 0;
    padding: 0;
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 100vh;
    color: var(--text-color);
}

.signup-container {
    background-color: white;
    padding: 30px;
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    width: 100%;
    max-width: 500px;
    border-top: 4px solid var(--main-color);
}

h1 {
    text-align: center;
    color: var(--main-color);
    margin-bottom: 25px;
    font-weight: 600;
}

.form-group {
    margin-bottom: 20px;
}

label {
    display: block;
    margin-bottom: 8px;
    font-weight: 500;
    color: var(--text-color);
}

input[type="text"],
input[type="email"],
input[type="tel"],
input[type="password"],
textarea {
    width: 100%;
    padding: 12px 40px 12px 15px;
    border: 1px solid #D1D8E0;
    border-radius: 6px;
    font-size: 16px;
    box-sizing: border-box;
    transition: all 0.2s;
}

input:focus, textarea:focus {
    border-color: var(--main-color);
    outline: none;
    box-shadow: 0 0 0 3px rgba(106, 137, 167, 0.2);
}

.btn {
    background-color: var(--main-color);
    color: white;
    border: none;
    padding: 14px 20px;
    border-radius: 6px;
    cursor: pointer;
    font-size: 16px;
    font-weight: 500;
    width: 100%;
    transition: background-color 0.2s;
}

.btn:hover {
    background-color: var(--main-dark);
}

.message {
    padding: 12px;
    margin-bottom: 20px;
    border-radius: 6px;
    text-align: center;
    font-weight: 500;
}

.error {
    background-color: #FDECEA;
    color: var(--error-color);
    border-left: 4px solid var(--error-color);
}

.success {
    background-color: #E8F5E9;
    color: var(--success-color);
    border-left: 4px solid var(--success-color);
}

.login-link {
    text-align: center;
    margin-top: 20px;
    color: #7F8C8D;
}

.login-link a {
    color: var(--main-color);
    text-decoration: none;
    font-weight: 500;
}

.login-link a:hover {
    text-decoration: underline;
}

.password-container {
    position: relative;
}

.password-toggle {
    position: absolute;
    right: 12px;
    top: 38px; /* Adjusted to perfectly align with input */
    transform: translateY(0);
    cursor: pointer;
    color: #95A5A6;
    background: none;
    border: none;
    padding: 8px;
    z-index: 2;
    height: 20px;
    width: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.password-toggle:hover {
    color: var(--main-color);
}

.password-field {
    padding-right: 40px !important;
}
    </style>
</head>
<body>
    <div class="signup-container">
        <h1>Create Your Account</h1>
        
        <?php if ($error): ?>
            <div class="message error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        
        <form action="signup.php" method="post">
            <div class="form-group">
                <label for="firstName">First Name</label>
                <input type="text" id="firstName" name="firstName" value="<?php echo htmlspecialchars($firstName ?? ''); ?>" required>
            </div>
            
            <div class="form-group">
                <label for="lastName">Last Name</label>
                <input type="text" id="lastName" name="lastName" value="<?php echo htmlspecialchars($lastName ?? ''); ?>" required>
            </div>
            
            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email ?? ''); ?>" required>
            </div>
            
            <div class="form-group">
                <label for="phone">Phone Number (Optional)</label>
                <input type="tel" id="phone" name="phone" value="<?php echo htmlspecialchars($phone ?? ''); ?>">
            </div>
            
            <div class="form-group password-container">
                <label for="password">Password (min 8 characters)</label>
                <input type="password" id="password" name="password" class="password-field" required>
                <button type="button" class="password-toggle" id="togglePassword">
                    <i class="fas fa-eye"></i>
                </button>
            </div>
            
            <div class="form-group password-container">
                <label for="confirmPassword">Confirm Password</label>
                <input type="password" id="confirmPassword" name="confirmPassword" class="password-field" required>
                <button type="button" class="password-toggle" id="toggleConfirmPassword">
                    <i class="fas fa-eye"></i>
                </button>
            </div>
            
            <div class="form-group">
                <label for="address">Address</label>
                <textarea id="address" name="address" rows="3" required><?php echo htmlspecialchars($address ?? ''); ?></textarea>
            </div>
            
            <button type="submit" class="btn">Sign Up</button>
            
            <div class="login-link">
                Already have an account? <a href="index.php">Log in</a>
            </div>
        </form>
    </div>

    <script>
        // Toggle password visibility
        document.addEventListener('DOMContentLoaded', function() {
            const togglePassword = document.querySelector('#togglePassword');
            const password = document.querySelector('#password');
            const toggleIcon = togglePassword.querySelector('i');
            
            const toggleConfirmPassword = document.querySelector('#toggleConfirmPassword');
            const confirmPassword = document.querySelector('#confirmPassword');
            const toggleConfirmIcon = toggleConfirmPassword.querySelector('i');
            
            togglePassword.addEventListener('click', function() {
                const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
                password.setAttribute('type', type);
                toggleIcon.classList.toggle('fa-eye');
                toggleIcon.classList.toggle('fa-eye-slash');
            });
            
            toggleConfirmPassword.addEventListener('click', function() {
                const type = confirmPassword.getAttribute('type') === 'password' ? 'text' : 'password';
                confirmPassword.setAttribute('type', type);
                toggleConfirmIcon.classList.toggle('fa-eye');
                toggleConfirmIcon.classList.toggle('fa-eye-slash');
            });
        });
    </script>
</body>
</html>