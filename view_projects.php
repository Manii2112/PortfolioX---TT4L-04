<?php 
session_start(); 
include 'connect.php'; // Connect to the database 

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Get the project ID from the URL
$project_id = $_GET['id'];

// Fetch the project details
$sql = "SELECT name, description, link, image FROM projects WHERE id = ? AND user_id = ?";
if ($stmt = $conn->prepare($sql)) {
    $stmt->bind_param("ii", $project_id, $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    $project = $result->fetch_assoc();
    
    if (!$project) {
        echo "Project not found or you don't have permission to view this project.";
        exit;
    }
} else {
    echo "Error preparing the SQL statement.";
    exit;
}
?>

<!DOCTYPE html> 
<html lang="en"> 
<head> 
    <meta charset="UTF-8"> 
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> 
    <title><?php echo htmlspecialchars($project['name']); ?></title> 
    <link rel="stylesheet" href="styles.css"> 
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            padding: 20px;
        }
        .project {
            background-color: white;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .project img {
            max-width: 100%;
            height: auto;
            margin-bottom: 15px;
        }
        .project h2 {
            margin-top: 0;
        }
        .project p {
            font-size: 16px;
            line-height: 1.6;
        }
        a.back {
            display: inline-block;
            margin-top: 20px;
            text-decoration: none;
            color: #007bff;
        }
        a.back:hover {
            color: #0056b3;
        }
    </style>
</head> 
<body> 
    <div class="project"> 
        <h2><?php echo htmlspecialchars($project['name']); ?></h2> 
        <img src="uploads/<?php echo htmlspecialchars($project['image']); ?>" alt="<?php echo htmlspecialchars($project['name']); ?>"> 
        <p><?php echo htmlspecialchars($project['description']); ?></p> 
        <p><a href="<?php echo htmlspecialchars($project['link']); ?>" target="_blank">View Live Project</a></p> 
    </div> 
    <a class="back" href="my_projects.php">Back to My Projects</a>
</body> 
</html>
