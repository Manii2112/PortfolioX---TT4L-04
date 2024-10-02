<?php
session_start(); // Start the session
include 'connect.php'; // Include the database connection file

// Get the project ID from the URL and validate it
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id <= 0) {
    // If ID is not valid, redirect to index
    header("Location: index.php");
    exit();
}

// Fetch the current project details
$query = "SELECT * FROM projects WHERE id=$id";
$result = mysqli_query($conn, $query);

// Check if the project exists
if (mysqli_num_rows($result) == 0) {
    // If no project found, redirect to index
    header("Location: index.php");
    exit();
}

$project = mysqli_fetch_assoc($result);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Project</title>
    <link rel="stylesheet" href="css/style.css"> <!-- Link to your main CSS file -->
    <style>
        /* Inline CSS for edit_project.php */
        body {
            background-color: #f4f4f4;
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            line-height: 1.6;
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
        form input[type="file"], 
        form select {
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
        form input[type="file"]:focus, 
        form select:focus {
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
        .current-image {
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <h1 style="text-align: center;">Edit Project</h1>
    <form action="edit_project.php?id=<?php echo $id; ?>" method="POST" enctype="multipart/form-data">
        <!-- Project Title -->
        <label for="title">Project Title:</label>
        <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($project['title']); ?>" required>
        
        <!-- Project Description -->
        <label for="description">Description:</label>
        <textarea id="description" name="description" required><?php echo htmlspecialchars($project['description']); ?></textarea>
        
        <!-- Project Image -->
        <label for="image">Image:</label>
        <input type="file" id="image" name="image" accept="image/*">
        <div class="current-image">
            <img src="images/<?php echo htmlspecialchars($project['image']); ?>" width="100" alt="Current Image">
        </div>
        
        <!-- Custom Text -->
        <label for="custom_text">Custom Words or Text:</label>
        <textarea id="custom_text" name="custom_text"><?php echo htmlspecialchars($project['custom_text']); ?></textarea>

        <!-- Template Selection -->
        <label for="template">Select Template:</label>
        <select id="template" name="template">
            <option value="default" <?php if ($project['template'] == 'default') echo 'selected'; ?>>Default</option>
            <option value="template1" <?php if ($project['template'] == 'template1') echo 'selected'; ?>>Template 1</option>
            <option value="template2" <?php if ($project['template'] == 'template2') echo 'selected'; ?>>Template 2</option>
            <option value="template3" <?php if ($project['template'] == 'template3') echo 'selected'; ?>>Template 3</option>
        </select>

        <!-- Submit Button -->
        <button type="submit" name="submit">Update Project</button>
    </form>

    <?php
    // Handle form submission
    if (isset($_POST['submit'])) {
        $title = $_POST['title'];
        $description = $_POST['description'];
        $custom_text = $_POST['custom_text'];
        $template = $_POST['template'];

        // Handle image upload
        $image = $_FILES['image']['name'];
        if ($image) {
            $target = "images/" . basename($image);
            move_uploaded_file($_FILES['image']['tmp_name'], $target);
        } else {
            $image = $project['image']; // Keep old image if none is uploaded
        }

        // Update project details in the database, including template and custom text
        $query = "UPDATE projects SET title='$title', description='$description', image='$image', custom_text='$custom_text', template='$template' WHERE id=$id";
        if (mysqli_query($conn, $query)) {
            // Redirect to the index page after successful update
            header("Location: index.php"); // Redirect to the index page
            exit(); // Ensure the script stops after the redirect
        } else {
            echo "<p class='error-message'>Error: " . mysqli_error($conn) . "</p>";
        }
    }
    ?>
</body>
</html>
