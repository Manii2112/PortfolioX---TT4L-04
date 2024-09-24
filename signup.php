<?php
session_start(); // Ensure session handling is active
include 'connect.php'; // Include your database connection code

$feedback = ''; // To store feedback messages

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Trim input data to remove extra spaces
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Server-side validation
    if (strlen($username) < 3) {
        $feedback = "<p class='error'>Username must be at least 3 characters long!</p>";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $feedback = "<p class='error'>Invalid email format!</p>";
    } elseif ($password !== $confirm_password) {
        $feedback = "<p class='error'>Passwords do not match!</p>";
    } else {
        // Hash the password
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);

        // Insert into database
        $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $username, $email, $hashed_password);

        if ($stmt->execute()) {
            // Redirect to login page after successful registration
            $_SESSION['success'] = "Registration successful! Please log in.";
            header("Location: login.php");
            exit(); // Ensure no further code is executed after redirection
        } else {
            $feedback = "<p class='error'>Error: " . htmlspecialchars($stmt->error) . "</p>";
        }

        $stmt->close();
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* Styles for the signup form */
        .success {
            color: green;
            font-weight: bold;
        }
        .error {
            color: red;
            font-weight: bold;
        }
        .form-container {
            width: 90%;
            max-width: 400px;
            margin: 30px auto;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 10px;
            background-color: #f9f9f9;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }
        form {
            display: flex;
            flex-direction: column;
        }
        label, input {
            margin-bottom: 10px;
            font-size: 14px;
        }
        input[type="text"], input[type="email"], input[type="password"] {
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 5px;
            width: 100%;
            font-size: 14px;
        }
        input[type="submit"] {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 10px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }
        input[type="submit"]:hover {
            background-color: #45a049;
        }
        @media (max-width: 600px) {
            .form-container {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <header>
        <h1>MMU Students Portfolio</h1>
    </header>
    
    <main>
        <div class="form-container">
            <!-- Display any feedback messages -->
            <?php echo $feedback; ?>

            <form method="post" action="signup.php">
                <h2>Sign Up</h2>

                <label for="username"><i class="fas fa-user"></i> Username:</label>
                <input type="text" id="username" name="username" required minlength="3">

                <label for="email"><i class="fas fa-envelope"></i> Email:</label>
                <input type="email" id="email" name="email" required>

                <label for="password"><i class="fas fa-lock"></i> Password:</label>
                <input type="password" id="password" name="password" required>

                <label for="confirm_password"><i class="fas fa-lock"></i> Confirm Password:</label>
                <input type="password" id="confirm_password" name="confirm_password" required>
                
                <input type="submit" value="Sign Up">
            </form>
        </div>
    </main>

    <footer>
        <p>&copy; 2024 MMU Portfolio</p>
    </footer>
</body>
</html>
