<?php
// Include the database connection file
include 'connect.php';

if (isset($_POST['submit'])) {
    // Retrieve form data
    $title = $_POST['title'];
    $description = $_POST['description'];

    // Handle image upload
    $image = $_FILES['image']['name'];
    $target = "images/" . basename($image);

    // Create 'images' directory if it doesn't exist
    if (!is_dir('images')) {
        mkdir('images', 0755, true);
    }

    // Move the uploaded file to the target directory
    if (move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
        echo "<p class='success-message'>Image uploaded successfully.</p>";
    } else {
        echo "<p class='error-message'>Failed to upload image.</p>";
    }

    // Save project details to the database
    $query = "INSERT INTO projects (title, description, image) VALUES ('$title', '$description', '$image')";
    if (mysqli_query($conn, $query)) {
        echo "<p class='success-message'>Project added successfully!</p>";
    } else {
        echo "<p class='error-message'>Error: " . mysqli_error($conn) . "</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Project</title>
    <link rel="stylesheet" href="css/style.css"> <!-- Link to your main CSS file -->
    <style>
        /* Inline CSS for add_project.php */
        body {
            background-color: #f4f4f4;
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            line-height: 1.6;
        }
        h1 {
            text-align: center;
            color: #007bff;
            margin-top: 20px;
        }
        form {
            background: #fff;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            max-width: 600px;
            margin: 30px auto;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        form label {
            display: block;
            margin: 12px 0 5px;
            font-weight: bold;
        }
        form input[type="text"], 
        form textarea, 
        form input[type="file"] {
            width: 100%;
            padding: 12px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 14px;
            transition: border-color 0.3s ease;
        }
        form input[type="text"]:focus, 
        form textarea:focus, 
        form input[type="file"]:focus {
            border-color: #007bff;
        }
        form button {
            background-color: #007bff;
            color: #fff;
            border: none;
            padding: 12px 25px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 16px;
            font-weight: bold;
            transition: background-color 0.3s ease, transform 0.3s ease;
        }
        form button:hover {
            background-color: #0056b3;
            transform: translateY(-2px);
        }
        .success-message {
            color: #28a745;
            font-weight: bold;
            margin: 10px 0;
            text-align: center;
        }
        .error-message {
            color: #f44336;
            font-weight: bold;
            margin: 10px 0;
            text-align: center;
        }
    </style>
</head>
<body>
    <h1>Add New Project</h1>
    <form action="add_project.php" method="POST" enctype="multipart/form-data">
        <label for="title">Project Title:</label>
        <input type="text" id="title" name="title" required>
        
        <label for="description">Description:</label>
        <textarea id="description" name="description" required></textarea>
        
        <label for="image">Image:</label>
        <input type="file" id="image" name="image" accept="image/*" required>
        
        <button type="submit" name="submit">Add Project</button>
    </form>
</body>
</html>
