<?php
// Include the database connection
include 'dbcon.php';

// Start the session to get the current student's ID
session_start();
$student_id = $_SESSION['student_id'] ?? null;

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($student_id)) {
    // Fetch the form input
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $image_file = $_FILES['image_file'] ?? null;



    // Handle image upload
    $image_path = null; // Initialize variable for the image path
    if ($image_file && $image_file['error'] === UPLOAD_ERR_OK) {
        // Define the path to upload the image
        $target_dir = "../students/profile/";
        $target_file = $target_dir . basename($image_file["name"]);
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Allow only certain file formats (JPEG, PNG)
        $allowed_types = ['jpg', 'jpeg', 'png'];
        if (!in_array($imageFileType, $allowed_types)) {
            $errors[] = "Only JPG, JPEG, and PNG files are allowed!";
        }

        // Upload the image file if no errors
        if (empty($errors)) {
            if (move_uploaded_file($image_file["tmp_name"], $target_file)) {
                $image_path = $target_file; // Store the path if the upload was successful
            } else {
                $errors[] = "Failed to upload the image file.";
            }
        }
    }

    // Update the database only if there are no errors
    if (empty($errors)) {
        // Start building the SQL query
        $sql = "UPDATE students SET ";
        $params = [];
        $types = '';
        $update_fields = []; // Track if any fields are being updated

        // Check if the password should be updated
        if (!empty($new_password)) {
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $update_fields[] = "password = ?";
            $params[] = $hashed_password;
            $types .= 's';
        }

        // Check if an image was uploaded
        if ($image_path) {
            $update_fields[] = "image = ?";
            $params[] = $image_path;
            $types .= 's';
        }

        // Only proceed if there are fields to update
        if (!empty($update_fields)) {
            $sql .= implode(', ', $update_fields) . " WHERE id = ?";
            $params[] = $student_id;
            $types .= 'i';

            // Prepare the SQL statement
            if ($stmt = $con->prepare($sql)) {
                // Bind the parameters dynamically
                $stmt->bind_param($types, ...$params);

                // Execute the query
                if ($stmt->execute()) {
                    // On success, redirect to the settings page
                    header("Location: ../students/student_settings.php");
                    exit(); // Always exit after a header redirect to stop further script execution
                } else {
                    echo "Error updating record: " . $con->error;
                }

                // Close the statement
                $stmt->close();
            } else {
                echo "Error preparing the statement: " . $con->error;
            }
        } else {
            // If there are no fields to update, you might want to inform the user
            echo "No changes made.";
        }
    } else {
        // Display errors
        foreach ($errors as $error) {
            echo "<p>$error</p>";
        }
    }
}
?>
