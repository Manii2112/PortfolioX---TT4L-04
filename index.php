<?php
session_start(); // Start the session
include 'connect.php'; // Include database connection

// Initialize variables for feedback messages
$contact_feedback = '';
$login_feedback = '';
$signup_feedback = '';

// Handle Login Form Submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $sql = "SELECT id, username, profile_picture, password FROM users WHERE email = ?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->bind_result($user_id, $username, $profile_picture, $hashed_password);
        if ($stmt->fetch() && password_verify($password, $hashed_password)) {
            // Store user info in session
            $_SESSION['user_id'] = $user_id;
            $_SESSION['username'] = $username;
            $_SESSION['profile_picture'] = $profile_picture ? $profile_picture : 'default.png'; // Set default if profile_picture is empty
            $login_feedback = "<p class='success'>Login successful!</p>";
            header("Location: index.php");
            exit();
        } else {
            $login_feedback = "<p class='error'>Invalid email or password.</p>";
        }
        $stmt->close();
    }
}

// Handle Signup Form Submission with Password Confirmation
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['signup'])) {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Check if passwords match
    if ($password !== $confirm_password) {
        $signup_feedback = "<p class='error'>Passwords do not match.</p>";
    } else {
        // Hash the password and proceed with signup
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $sql = "INSERT INTO users (username, email, password) VALUES (?, ?, ?)";
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("sss", $username, $email, $hashed_password);
            if ($stmt->execute()) {
                $signup_feedback = "<p class='success'>Signup successful! You can now log in.</p>";
            } else {
                $signup_feedback = "<p class='error'>Signup failed: " . htmlspecialchars($stmt->error) . "</p>";
            }
            $stmt->close();
        }
    }
}

// Handle Contact Form Submission (requires login)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['contact'])) {
    if (isset($_SESSION['user_id'])) {
        $user_id = $_SESSION['user_id'];
        $message = htmlspecialchars($_POST['message'], ENT_QUOTES, 'UTF-8');

        if ($stmt = $conn->prepare("INSERT INTO contacts (user_id, message) VALUES (?, ?)")) {
            $stmt->bind_param("is", $user_id, $message);
            if ($stmt->execute()) {
                $contact_feedback = "<p class='success'>Message sent successfully!</p>";
            } else {
                $contact_feedback = "<p class='error'>Error: " . htmlspecialchars($stmt->error) . "</p>";
            }
            $stmt->close();
        }
    } else {
        $contact_feedback = "<p class='error'>You must be logged in to submit a message.</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MMU Students Portfolio</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* Form switcher for toggling between login and signup */
        .form-switcher {
            text-align: center;
            margin: 20px 0;
        }

        .form-switcher button {
            background-color: #007bff;
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            margin: 0 10px;
            transition: background-color 0.3s ease;
        }

        .form-switcher button:hover {
            background-color: #0056b3;
        }

        /* Profile image styling */
        nav {
            float: right;
        }

        .profile-pic {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
        }

        /* Feedback messages */
        .success {
            color: green;
            font-weight: bold;
        }

        .error {
            color: red;
            font-weight: bold;
        }

        /* Reduced footer height */
        footer {
            padding: 10px 0;
            background-color: #333;
            color: #fff;
            text-align: center;
            font-size: 14px;
        }
    </style>
    <script>
        function showForm(formName) {
            document.getElementById('login-form').style.display = 'none';
            document.getElementById('signup-form').style.display = 'none';
            document.getElementById(formName).style.display = 'block';
        }
    </script>
</head>
<body>
    <header>
        <h1>MMU Students Portfolio</h1>
        <?php if (isset($_SESSION['user_id'])): ?>
            <nav>
                <a href="profile.php" class="profile-button">
                    <?php if (!empty($_SESSION['profile_picture'])): ?>
                        <img src="uploads/<?php echo htmlspecialchars($_SESSION['profile_picture']); ?>" alt="Profile Picture" class="profile-pic">
                    <?php else: ?>
                        <i class="fas fa-user-circle" style="font-size: 40px;"></i>
                    <?php endif; ?>
                </a>
                <a href="logout.php" class="logout-button">Logout</a>
            </nav>
        <?php endif; ?>
    </header>
    
    <main>
        <!-- Login and Signup Forms -->
        <?php if (!isset($_SESSION['user_id'])): ?>
            <div class="form-switcher">
                <button onclick="showForm('login-form')">Login</button>
                <button onclick="showForm('signup-form')">Signup</button>
            </div>
            <div id="login-form" style="display: block;">
                <h2>Login</h2>
                <?php echo $login_feedback; ?>
                <form method="post" action="">
                    <label for="email">Email:</label>
                    <input type="email" name="email" required><br>
                    <label for="password">Password:</label>
                    <input type="password" name="password" required><br>
                    <input type="submit" name="login" value="Login">
                </form>
            </div>

            <div id="signup-form" style="display: none;">
                <h2>Signup</h2>
                <?php echo $signup_feedback; ?>
                <form method="post" action="">
                    <label for="username">Username:</label>
                    <input type="text" name="username" required><br>
                    <label for="email">Email:</label>
                    <input type="email" name="email" required><br>
                    <label for="password">Password:</label>
                    <input type="password" name="password" required><br>
                    <label for="confirm_password">Confirm Password:</label>
                    <input type="password" name="confirm_password" required><br>
                    <input type="submit" name="signup" value="Signup">
                </form>
            </div>
        <?php endif; ?>

        <!-- Contact Form Section -->
        <?php if (isset($_SESSION['user_id'])): ?>
            <div class="contact-form-container">
                <h2>Contact Us</h2>
                <?php echo $contact_feedback; ?>
                <form method="post" action="">
                    <label for="message">Your Message:</label>
                    <textarea id="message" name="message" required></textarea><br>
                    <input type="submit" name="contact" value="Send Message">
                </form>
            </div>
        <?php endif; ?>
    </main>

    <footer>
        <p>&copy; 2024 MMU Portfolio</p>
    </footer>

    <?php
    // Close the connection after all operations
    if (isset($conn)) {
        $conn->close();
    }
    ?>
</body>
</html>
