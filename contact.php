<?php
session_start(); // Ensure session handling is active
include 'connect.php'; // Include your database connection code

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $message = $_POST['message'];

    $stmt = $conn->prepare("INSERT INTO contacts (user_id, message) VALUES (?, ?)");
    $stmt->bind_param("is", $user_id, $message);

    if ($stmt->execute()) {
        $feedback = "<p class='success'>Message sent successfully!</p>";
    } else {
        $feedback = "<p class='error'>Error: " . $stmt->error . "</p>";
    }

    $stmt->close();
    $conn->close();
} else {
    $feedback = "<p class='error'>You must be logged in to submit a message.</p>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        .form-container {
            width: 400px;
            margin: auto;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 10px;
            background-color: #f9f9f9;
        }
        textarea {
            width: 100%;
            height: 100px;
            margin-bottom: 10px;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        input[type="submit"] {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 10px;
            border-radius: 5px;
            cursor: pointer;
        }
        input[type="submit"]:hover {
            background-color: #45a049;
        }
        .success {
            color: green;
            font-weight: bold;
        }
        .error {
            color: red;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <header>
        <h1>MMU Students Portfolio</h1>
    </header>
    
    <main>
        <div class="form-container">
            <form method="post" action="contact.php">
                <h2>Contact Us</h2>
                <?php echo $feedback; // Display feedback message ?>
                <label for="message"><i class="fas fa-comment"></i> Message:</label>
                <textarea id="message" name="message" required></textarea>
                <input type="submit" value="Send Message">
            </form>
        </div>
    </main>

    <footer>
        <p>&copy; 2024 MMU Portfolio</p>
    </footer>
</body>
</html>

