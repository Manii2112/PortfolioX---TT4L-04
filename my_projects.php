<?php 
session_start(); 
include 'connect.php'; // Connect to the database 

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id']; // Get the logged-in user's ID 

// Fetch all projects created by the logged-in user
$sql = "SELECT id, name, description, link, image FROM projects WHERE user_id = ?";
if ($stmt = $conn->prepare($sql)) {
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Projects</title>
    <link rel="stylesheet" href="styles.css"> <!-- Link to your CSS -->
    <style>
        /* Add some basic styles */
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            padding: 20px;
        }
        header {
            text-align: center;
            margin-bottom: 20px;
        }
        .project {
            background-color: white;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .project img {
            max-width: 100px;
            margin-bottom: 10px;
        }
        .project a {
            text-decoration: none;
            color: #007bff;
            margin-right: 10px;
        }
        .project a:hover {
            color: #0056b3;
        }
    </style>
</head>
<body>
    <header>
        <h1>My Projects</h1>
        <a href="add_project.php">Add New Project</a>
    </header>

    <main>
        <?php if ($result->num_rows > 0): ?>
            <?php while ($project = $result->fetch_assoc()): ?>
                <div class="project">
                    <h2><?php echo htmlspecialchars($project['name']); ?></h2>
                    <img src="uploads/<?php echo htmlspecialchars($project['image']); ?>" alt="<?php echo htmlspecialchars($project['name']); ?>">
                    <p><?php echo htmlspecialchars($project['description']); ?></p>
                    <p><a href="<?php echo htmlspecialchars($project['link']); ?>" target="_blank">View Project</a></p>
                    <a href="edit_project.php?id=<?php echo $project['id']; ?>">Edit</a>
                    <a href="delete_project.php?id=<?php echo $project['id']; ?>" onclick="return confirm('Are you sure you want to delete this project?')">Delete</a>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>You haven't created any projects yet.</p>
        <?php endif; ?>
    </main>
</body>
</html>
