<?php
session_start();
include 'connect.php';

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php"); // Redirect to index if not authorized
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch the user's current profile data
$query = "SELECT full_name, profile_picture FROM users WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Handle profile update
if (isset($_POST['update_profile'])) {
    $full_name = $_POST['full_name'];
    
    // Handle profile picture upload
    if ($_FILES['profile_picture']['name']) {
        $profile_picture = basename($_FILES['profile_picture']['name']);
        $target = "uploads/" . $profile_picture;
        move_uploaded_file($_FILES['profile_picture']['tmp_name'], $target);
    } else {
        $profile_picture = $user['profile_picture']; // Keep current picture if none is uploaded
    }
    
    // Update user profile in the database
    $update_query = "UPDATE users SET full_name = ?, profile_picture = ? WHERE id = ?";
    $stmt = $conn->prepare($update_query);
    $stmt->bind_param("ssi", $full_name, $profile_picture, $user_id);
    
    if ($stmt->execute()) {
        $_SESSION['profile_update_success'] = "Profile updated successfully!";
        header("Location: index.php"); // Redirect to the index page after updating
        exit();
    } else {
        echo "Error updating profile: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .profile-container {
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 20px;
            width: 300px;
            text-align: center;
        }

        .profile-container h1 {
            font-size: 24px;
            margin-bottom: 20px;
        }

        .profile-container img {
            border-radius: 50%;
            width: 100px;
            height: 100px;
            object-fit: cover;
            display: block;
            margin: 10px auto;
        }

        .profile-container input[type="text"],
        .profile-container input[type="file"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .profile-container button {
            background-color: #007bff;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            width: 100%;
        }

        .profile-container button:hover {
            background-color: #0056b3;
        }

        .success {
            color: green;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>

    <div class="profile-container">
        <h1>My Profile</h1>

        <?php if (isset($_SESSION['profile_update_success'])): ?>
            <p class="success"><?php echo $_SESSION['profile_update_success']; unset($_SESSION['profile_update_success']); ?></p>
        <?php endif; ?>

        <form action="profile.php" method="POST" enctype="multipart/form-data">
            <label for="full_name">Full Name:</label>
            <input type="text" id="full_name" name="full_name" value="<?php echo htmlspecialchars($user['full_name']); ?>" required>
            
            <label for="profile_picture">Profile Picture:</label>
            <input type="file" id="profile_picture" name="profile_picture" accept="image/*">
            
            <?php if ($user['profile_picture']): ?>
                <img src="uploads/<?php echo htmlspecialchars($user['profile_picture']); ?>" alt="Profile Picture" id="profilePicturePreview">
            <?php endif; ?>
            
            <button type="submit" name="update_profile">Update Profile</button>
        </form>
    </div>

    <script>
        // Add a live preview for the profile picture
        const profilePicInput = document.getElementById('profile_picture');
        const profilePicPreview = document.getElementById('profilePicturePreview');

        profilePicInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    profilePicPreview.src = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        });
    </script>

</body>
</html>
