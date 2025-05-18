<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Arial', sans-serif;
        }
        
        body {
            background-color: #f5f7fa;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        
        main {
            width: 100%;
            max-width: 500px;
            padding: 30px;
        }
        
        .container {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(106, 137, 167, 0.2);
            padding: 40px;
            text-align: center;
        }
        
        h1 {
            color: #6A89A7;
            margin-bottom: 30px;
            font-size: 28px;
        }
        
        form {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }
        
        label {
            display: block;
            text-align: left;
            color: #555;
            margin-bottom: 8px;
            font-weight: 500;
        }
        
        input {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e0e0e0;
            border-radius: 6px;
            font-size: 16px;
            transition: all 0.3s ease;
        }
        
        input:focus {
            border-color: #6A89A7;
            outline: none;
            box-shadow: 0 0 0 3px rgba(106, 137, 167, 0.2);
        }
        
        button {
            background-color: #6A89A7;
            color: white;
            border: none;
            padding: 14px;
            border-radius: 6px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 10px;
        }
        
        button:hover {
            background-color: #4A6B8A;
            transform: translateY(-2px);
        }
        
        .password-container {
            position: relative;
        }
        
        .password-container i {
            position: absolute;
            right: 15px;
            top: 42px;
            color: #999;
            cursor: pointer;
        }
        
        @media (max-width: 600px) {
            .container {
                padding: 30px 20px;
            }
            
            h1 {
                font-size: 24px;
            }
        }
        .error-message {
            color: #e74c3c;
            background-color: #fdecea;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
            border-left: 4px solid #e74c3c;
            display: none; /* Hidden by default */
        }
    </style>
</head>
<body>
    <main>
        <div class="container">
            <h1>Login to Your Account</h1>
            <?php if (isset($_GET['error'])): ?>
                <div class="error-message" id="errorMessage">
                    <?php
                    switch($_GET['error']) {
                        case 'emptyfields':
                            echo 'Please fill in all fields';
                            break;
                        case 'wrongcredentials':
                            echo 'Invalid email or password';
                            break;
                        case 'sqlerror':
                            echo 'Database error occurred';
                            break;
                        default:
                            echo 'Login error occurred';
                    }
                    ?>
                </div>
                
                <script>
                    // Show error message and fade out after 5 seconds
                    document.getElementById('errorMessage').style.display = 'block';
                    setTimeout(function() {
                        document.getElementById('errorMessage').style.opacity = '0';
                        setTimeout(function() {
                            document.getElementById('errorMessage').style.display = 'none';
                        }, 500);
                    }, 5000);
                </script>
            <?php endif; ?>
            <form action="includes/login.inc.php" method="post">
                <div>
                    <label for="em">Email Address</label>
                    <input type="email" name="username" id="em" placeholder="Enter your email">
                </div>
                
                <div class="password-container">
                    <label for="pass">Password</label>
                    <input type="password" name="pwd" id="pass" placeholder="Enter your password">
                    <i class="fas fa-eye" id="togglePassword"></i>
                </div>
                
                <button type="submit" name="add">Login</button>
                
                <div style="margin-top: 20px; color: #666; font-size: 14px;">
                    Don't have an account? <a href="signup.php" style="color: #6A89A7; text-decoration: none;">Sign up</a>
                </div>
            </form>
        </div>
    </main>

    <script>
        const togglePassword = document.querySelector('#togglePassword');
        const password = document.querySelector('#pass');
        
        togglePassword.addEventListener('click', function() {
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);
            this.classList.toggle('fa-eye-slash');
        });
    </script>
</body>
</html>