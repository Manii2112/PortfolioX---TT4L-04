<?php
// Include the database connection file
include 'connect.php';

// Get the project ID from the URL and sanitize it
$id = intval($_GET['id']);

// Fetch the image name associated with the project
$query = "SELECT image FROM projects WHERE id=$id";
$result = mysqli_query($conn, $query);

// Check if project exists
if (mysqli_num_rows($result) > 0) {
    $project = mysqli_fetch_assoc($result);
    $image = $project['image'];

    // Delete the project from the database
    $deleteQuery = "DELETE FROM projects WHERE id=$id";
    if (mysqli_query($conn, $deleteQuery)) {
        // Delete the image file from the server
        if ($image && file_exists("images/$image")) {
            unlink("images/$image");
        }
        $message = "<p class='success'>Project deleted successfully!</p>";
    } else {
        $message = "<p class='error'>Error: " . mysqli_error($conn) . "</p>";
    }
} else {
    $message = "<p class='error'>Project not found.</p>";
}

// Close the database connection
mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Project</title>
    <style>
        /* General Styles for PHP Actions */
        .action-message {
            background-color: #fff;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            max-width: 600px;
            margin: 30px auto;
            text-align: center;
            font-size: 16px;
        }

        .action-message p {
            margin: 0;
        }

        .action-message .success {
            color: #28a745;
            font-weight: bold;
        }

        .action-message .error {
            color: #dc3545;
            font-weight: bold;
        }

        .action-message a {
            display: inline-block;
            margin-top: 10px;
            padding: 10px 20px;
            background-color: #007bff;
            color: #fff;
            text-decoration: none;
            border-radius: 25px;
            font-size: 16px;
            transition: background-color 0.3s ease;
        }

        .action-message a:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="action-message">
        <?php echo $message; ?>
        <a href="index.php">Return to Project List</a>
    </div>
</body>
</html>
