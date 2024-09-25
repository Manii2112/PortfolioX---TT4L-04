<?php
// Include the database connection file
include 'connect.php';

// Fetch all projects from the database
$query = "SELECT * FROM projects";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Project List</title>
    <style>
        /* Global Styles */
        body { 
            font-family: Arial, sans-serif; 
            background-color: #f4f4f4; 
            color: #333; 
            margin: 0; 
            padding: 0; 
            line-height: 1.6; 
        }

        header { 
            background-color: #333; 
            color: #fff; 
            padding: 15px 0; 
            text-align: center; 
            font-size: 24px; 
        }

        nav a {
            color: #fff;
            text-decoration: none;
            font-size: 18px;
            background-color: #007bff;
            padding: 10px 20px;
            border-radius: 25px;
            transition: background-color 0.3s ease;
        }

        nav a:hover {
            background-color: #0056b3;
        }

        main {
            padding: 20px;
        }

        .project {
            background-color: #fff;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .project h2 {
            margin: 0 0 10px;
            font-size: 20px;
            color: #007bff;
        }

        .project p {
            font-size: 16px;
        }

        .project img {
            border-radius: 6px;
            max-width: 100%;
            height: auto;
            display: block;
            margin-bottom: 10px;
        }

        .project a {
            text-decoration: none;
            color: #007bff;
            font-weight: bold;
            margin-right: 10px;
            transition: color 0.3s ease;
        }

        .project a:hover {
            color: #0056b3;
        }

        footer { 
            background-color: #333; 
            color: #fff; 
            text-align: center; 
            padding: 15px 0; 
            position: fixed; 
            width: 100%; 
            bottom: 0; 
            font-size: 14px; 
        }
    </style>
</head>
<body>
    <header>
        <h1>Project List</h1>
        <nav>
            <a href="add_project.php">Add New Project</a> <!-- Link to Add Project -->
        </nav>
    </header>

    <main>
        <?php
        if (mysqli_num_rows($result) > 0) {
            // Loop through and display each project
            while ($project = mysqli_fetch_assoc($result)) {
                echo "<div class='project'>";
                echo "<h2>" . htmlspecialchars($project['title']) . "</h2>";
                echo "<p>" . htmlspecialchars($project['description']) . "</p>";
                echo "<img src='images/" . htmlspecialchars($project['image']) . "' alt='" . htmlspecialchars($project['title']) . "'>"; 
                echo "<a href='edit_project.php?id=" . $project['id'] . "'>Edit</a> | ";
                echo "<a href='delete_project.php?id=" . $project['id'] . "' onclick='return confirm(\"Are you sure you want to delete this project?\")'>Delete</a>";
                echo "</div>";
            }
        } else {
            echo "<p>No projects found.</p>";
        }
        ?>
    </main>

    <footer>
        <p>&copy; 2024 Your Company Name. All rights reserved.</p>
    </footer>
</body>
</html>
