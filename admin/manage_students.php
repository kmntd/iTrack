<?php
session_start();
include '../fn/dbcon.php'; // Include database connection

// Handle adding a student
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_student'])) {
    $lrn = $_POST['lrn'];
    $first_name = $_POST['first_name'];
    $middle_name = $_POST['middle_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Hashing password
    $dob = $_POST['dob'];
    $grade_level = $_POST['grade_level'];
    $section_id = $_POST['section_id'];

    $sql = "INSERT INTO students (lrn, first_name, middle_name, last_name, email, password, dob, grade_level, section_id) 
            VALUES ('$lrn', '$first_name', '$middle_name', '$last_name', '$email', '$password', '$dob', $grade_level, $section_id)";

    if ($con->query($sql) === TRUE) {
        $message = "Student added successfully!";
    } else {
        $message = "Error: " . $con->error;
    }
}

// Handle editing a student
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit_student'])) {
    $student_id = $_POST['student_id'];
    $lrn = $_POST['lrn'];
    $first_name = $_POST['first_name'];
    $middle_name = $_POST['middle_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];
    $dob = $_POST['dob'];
    $grade_level = $_POST['grade_level'];
    $section_id = $_POST['section_id'];

    $sql = "UPDATE students SET lrn='$lrn', first_name='$first_name', middle_name='$middle_name', last_name='$last_name', 
            email='$email', dob='$dob', grade_level=$grade_level, section_id=$section_id WHERE id=$student_id";

    if ($con->query($sql) === TRUE) {
        $message = "Student updated successfully!";
    } else {
        $message = "Error: " . $con->error;
    }

    if (isset($_POST['restore_student'])) {
        $student_id = intval($_POST['student_id']);
        $query = "UPDATE students SET deleted_at = NULL WHERE id = $student_id";
        if ($con->query($query) === TRUE) {
            echo "<script>alert('Student restored successfully.');</script>";
        } else {
            echo "<script>alert('Error restoring student: " . $con->error . "');</script>";
        }
    }
    
    // Retrieve active students (soft delete logic)
    $query = "SELECT * FROM students WHERE deleted_at IS NULL";
    $result = $con->query($query);
    
    // Optional: Retrieve deleted students
    $deleted_query = "SELECT * FROM students WHERE deleted_at IS NOT NULL";
    $deleted_result = $con->query($deleted_query);
}

// Soft delete student
if (isset($_POST['delete_student'])) {
    $student_id = intval($_POST['student_id']);
    $query = "UPDATE students SET deleted_at = NOW() WHERE id = $student_id";
    if ($con->query($query) === TRUE) {
        echo "<script>alert('Student deleted successfully.');</script>";
    } else {
        echo "<script>alert('Error deleting student: " . $con->error . "');</script>";
    }
}



// Fetch students for displaying, ensuring we only select those who are not soft deleted
$sql = "SELECT s.*, sec.section_name FROM students s LEFT JOIN sections sec ON s.section_id = sec.id WHERE s.deleted_at IS NULL";
$result = $con->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Students</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <div class="container mx-auto p-6">
        <h1 class="text-2xl font-bold mb-4">Manage Students</h1>
        
        <?php if (isset($message)): ?>
            <div class="bg-green-500 text-white p-2 mb-4 rounded">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <!-- Add Student Form -->
        <form method="POST" class="bg-white p-6 rounded shadow mb-6">
            <h2 class="text-xl font-semibold mb-4">Add Student</h2>
            <input type="text" name="lrn" placeholder="LRN" required class="border p-2 w-full mb-4">
            <input type="text" name="first_name" placeholder="First Name" required class="border p-2 w-full mb-4">
            <input type="text" name="middle_name" placeholder="Middle Name" class="border p-2 w-full mb-4">
            <input type="text" name="last_name" placeholder="Last Name" required class="border p-2 w-full mb-4">
            <input type="email" name="email" placeholder="Email" required class="border p-2 w-full mb-4">
            <input type="password" name="password" placeholder="Password" required class="border p-2 w-full mb-4">
            <input type="date" name="dob" required class="border p-2 w-full mb-4">
            <select name="grade_level" required class="border p-2 w-full mb-4">
                <option value="">Select Year Level</option>
                <option value="1">1</option>
                <option value="2">2</option>
                <option value="3">3</option>
                <option value="4">4</option>
            </select>
            <select name="section_id" required class="border p-2 w-full mb-4">
                <option value="">Select Section</option>
                <?php
                // Fetch sections for dropdown
                $sec_sql = "SELECT * FROM sections";
                $sec_result = $con->query($sec_sql);
                while ($section = $sec_result->fetch_assoc()) {
                    echo "<option value='{$section['id']}'>{$section['section_name']}</option>";
                }
                ?>
            </select>
            <button type="submit" name="add_student" class="bg-blue-500 text-white py-2 px-4 rounded hover:bg-blue-600">Add Student</button>
        </form>

        <!-- Existing Students List -->
        <h2 class="text-xl font-semibold mb-4">Existing Students</h2>
        <table class="min-w-full bg-white rounded shadow">
            <thead>
                <tr>
                    <th class="py-2 px-4 border">LRN</th>
                    <th class="py-2 px-4 border">Name</th>
                    <th class="py-2 px-4 border">Email</th>
                    <th class="py-2 px-4 border">Grade Level</th>
                    <th class="py-2 px-4 border">Section</th>
                    <th class="py-2 px-4 border">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while($student = $result->fetch_assoc()): ?>
                        <tr>
                            <td class="py-2 px-4 border-b"><?php echo $student['lrn']; ?></td>
                            <td class="py-2 px-4 border-b"><?php echo $student['first_name'] . ' ' . $student['last_name']; ?></td>
                            <td class="py-2 px-4 border-b"><?php echo $student['email']; ?></td>
                            <td class="py-2 px-4 border-b"><?php echo $student['grade_level']; ?></td>
                            <td class="py-2 px-4 border-b"><?php echo $student['section_name']; ?></td>
                            <td class="py-2 px-4 border-b">
                                <a href="edit_student.php?id=<?php echo $student['id']; ?>" class="text-blue-500 hover:text-blue-700">Edit</a>
                                <form method="post" action="../fn/soft_delete_student.php" style="display:inline;">
                                    <input type="hidden" name="student_id" value="<?php echo $student['id']; ?>">
                                    <button type="submit" class="text-red-500 hover:text-red-700 ml-4" onclick="return confirm('Are you sure you want to delete this student?');">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="py-2 px-4 text-center">No students found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
<?php
$con->close(); // Close database connection
?>
