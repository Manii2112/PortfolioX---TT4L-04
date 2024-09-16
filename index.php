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

    $sql = "SELECT id, password FROM users WHERE email = ?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->bind_result($user_id, $hashed_password);
        if ($stmt->fetch() && password_verify($password, $hashed_password)) {
            $_SESSION['user_id'] = $user_id;
            $login_feedback = "<p class='success'>Login successful!</p>";
        } else {
            $login_feedback = "<p class='error'>Invalid email or password.</p>";
        }
        $stmt->close();
    }
}

// Handle Signup Form Submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['signup'])) {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $sql = "INSERT INTO users (username, email, password) VALUES (?, ?, ?)";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("sss", $username, $email, $password);
        if ($stmt->execute()) {
            $signup_feedback = "<p class='success'>Signup successful! You can now log in.</p>";
        } else {
            $signup_feedback = "<p class='error'>Signup failed: " . htmlspecialchars($stmt->error) . "</p>";
        }
        $stmt->close();
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

// Query the database for student portfolios
$portfolio_result = null;
if (isset($conn)) {
    $sql = "SELECT name, course, year, project_link FROM students";
    $portfolio_result = $conn->query($sql);
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
        /* Additional CSS for form styling */
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

        .form-switcher button:active {
            background-color: #004494;
        }

        .form-switcher button:focus {
            outline: none;
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
                <a href="logout.php" class="logout-button">Logout</a>
            </nav>
        <?php endif; ?>
    </header>
    
    <main>
        <!-- Portfolio Section -->
        <div class="portfolio">
            <h2>Student Portfolios</h2>
            <?php
            if ($portfolio_result && $portfolio_result->num_rows > 0) {
                while ($row = $portfolio_result->fetch_assoc()) {
                    echo "<div class='portfolio-item'>";
                    echo "<h2>" . htmlspecialchars($row["name"]) . "</h2>";
                    echo "<p>Course: " . htmlspecialchars($row["course"]) . "</p>";
                    echo "<p>Year: " . htmlspecialchars($row["year"]) . "</p>";
                    echo "<a href='" . htmlspecialchars($row["project_link"]) . "' target='_blank'>View Project</a>";
                    echo "</div>";
                }
            } else {
                echo "<p>No portfolios found.</p>";
            }
            ?>
        </div>

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
