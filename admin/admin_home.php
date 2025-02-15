<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

// Include the database connection
include '../fn/dbcon.php';

// Query to get the total counts
$total_students_query = "SELECT COUNT(*) AS total_students FROM students";
$total_teachers_query = "SELECT COUNT(*) AS total_teachers FROM teachers";
$total_sections_query = "SELECT COUNT(*) AS total_sections FROM sections";
$total_subjects_query = "SELECT COUNT(*) AS total_subjects FROM subjects"; // Add this line


// Execute the queries
$total_students_result = $con->query($total_students_query);
$total_teachers_result = $con->query($total_teachers_query);
$total_sections_result = $con->query($total_sections_query);
// Execute the new query
$total_subjects_result = $con->query($total_subjects_query);




// Fetch the counts
$total_students = $total_students_result->fetch_assoc()['total_students'];
$total_teachers = $total_teachers_result->fetch_assoc()['total_teachers'];
$total_sections = $total_sections_result->fetch_assoc()['total_sections'];
// Fetch the count
$total_subjects = $total_subjects_result->fetch_assoc()['total_subjects']; // Add this line
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">

    <div class="flex h-screen">
        <!-- Sidebar -->
        <aside class="w-64 bg-white p-6">
    <h2 class="text-2xl font-bold text-gray-800">Admin Dashboard</h2>
    <nav class="mt-6">
        <ul>
            <!-- User Management -->
            <li class="mb-4">
                <button onclick="toggleDropdown('userManagement')" class="text-gray-700 hover:text-blue-500 w-full text-left">User Management</button>
                <ul id="userManagement" class="ml-4 mt-2 hidden">
                    <li class="mb-2"><a href="manage_students.php" class="text-gray-600 hover:text-blue-400">Add/Edit/Delete Students</a></li>
                    <li class="mb-2"><a href="manage_teachers.php" class="text-gray-600 hover:text-blue-400">Add/Edit/Delete Teachers</a></li>
                    <li class="mb-2"><a href="manage_admins.php" class="text-gray-600 hover:text-blue-400">Manage Admin Accounts</a></li>
                </ul>
            </li>

            <!-- Section Management -->
            <li class="mb-4">
                <button onclick="toggleDropdown('sectionManagement')" class="text-gray-700 hover:text-blue-500 w-full text-left">Section Management</button>
                <ul id="sectionManagement" class="ml-4 mt-2 hidden">
                    <li class="mb-2"><a href="manage_sections.php" class="text-gray-600 hover:text-blue-400">Create/Edit/Delete Sections</a></li>
                    <li class="mb-2"><a href="assign_teachers.php" class="text-gray-600 hover:text-blue-400">Assign Teachers to Sections</a></li>
                    <li class="mb-2"><a href="assign_students.php" class="text-gray-600 hover:text-blue-400">Assign Students to Sections</a></li>
                    <li class="mb-2"><a href="student_list.php" class="text-gray-600 hover:text-blue-400">View Sections</a></li>
                </ul>
            </li>

            <!-- Subject Management -->
            <li class="mb-4">
                <button onclick="toggleDropdown('subjectManagement')" class="text-gray-700 hover:text-blue-500 w-full text-left">Subject Management</button>
                <ul id="subjectManagement" class="ml-4 mt-2 hidden">
                    <li class="mb-2"><a href="manage_subjects.php" class="text-gray-600 hover:text-blue-400">Add/Edit/Delete Subjects</a></li>
                    <li class="mb-2"><a href="assign_subjects.php" class="text-gray-600 hover:text-blue-400">Assign Subjects to Teachers</a></li>
                </ul>
            </li>

            <!-- Grade and Progress Tracking -->
            <li class="mb-4">
                <button onclick="toggleDropdown('gradeTracking')" class="text-gray-700 hover:text-blue-500 w-full text-left">Grade and Progress Tracking</button>
                <ul id="gradeTracking" class="ml-4 mt-2 hidden">
                    <li class="mb-2"><a href="view_grades.php" class="text-gray-600 hover:text-blue-400">View Student Grades</a></li>
                    <li class="mb-2"><a href="generate_reports.php" class="text-gray-600 hover:text-blue-400">Generate Reports</a></li>
                </ul>
            </li>

            <!-- Communication -->
            <li class="mb-4">
                <button onclick="toggleDropdown('communication')" class="text-gray-700 hover:text-blue-500 w-full text-left">Communication</button>
                <ul id="communication" class="ml-4 mt-2 hidden">
                    <li class="mb-2"><a href="announcements.php" class="text-gray-600 hover:text-blue-400">Announcements</a></li>
                </ul>
            </li>

            <!-- Account Settings -->
            <li class="mb-4">
                <button onclick="toggleDropdown('settings')" class="text-gray-700 hover:text-blue-500 w-full text-left">Account Settings</button>
                <ul id="settings" class="ml-4 mt-2 hidden">
                    <li class="mb-2"><a href="admin_settings.php" class="text-gray-600 hover:text-blue-400">Admin Account Settings</a></li>
                </ul>
            </li>

            <li class="mb-4"><a href="../fn/admin_logout.php" class="text-gray-700 hover:text-blue-500">Logout</a></li>
        </ul>
    </nav>
</aside>

<script>
    let currentDropdown = null; // Variable to track the currently open dropdown

    function toggleDropdown(id) {
        const dropdown = document.getElementById(id);
        
        // If the dropdown is currently open, close it
        if (currentDropdown && currentDropdown !== dropdown) {
            currentDropdown.classList.add('hidden');
        }

        // Toggle the clicked dropdown
        dropdown.classList.toggle('hidden');

        // Update the currentDropdown variable
        currentDropdown = dropdown.classList.contains('hidden') ? null : dropdown;
    }
</script>


        <!-- Main Content -->
        <main class="flex-1 p-10">
    <h1 class="text-3xl font-semibold text-gray-800">Welcome, <?php echo $_SESSION['admin_username']; ?></h1>
    <p class="mt-2 text-gray-600">Manage your school operations efficiently.</p>

    <div class="mt-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <!-- Card 1 -->
        <div class="bg-white p-6 rounded-lg shadow">
            <h2 class="text-xl font-bold text-gray-800">Total Students</h2>
            <p class="text-3xl text-blue-600"><?php echo $total_students; ?></p>
        </div>
        <!-- Card 2 -->
        <div class="bg-white p-6 rounded-lg shadow">
            <h2 class="text-xl font-bold text-gray-800">Total Teachers</h2>
            <p class="text-3xl text-blue-600"><?php echo $total_teachers; ?></p>
        </div>
        <!-- Card 3 -->
        <div class="bg-white p-6 rounded-lg shadow">
            <h2 class="text-xl font-bold text-gray-800">Total Sections</h2>
            <p class="text-3xl text-blue-600"><?php echo $total_sections; ?></p>
        </div>
        <!-- Card for Total Subjects -->
<div class="bg-white p-6 rounded-lg shadow">
    <h2 class="text-xl font-bold text-gray-800">Total Subjects</h2>
    <p class="text-3xl text-blue-600"><?php echo $total_subjects; ?></p>
</div>

    </div>

    <!-- Recent Activity Section -->
    <div class="mt-8">
        <h2 class="text-2xl font-semibold text-gray-800">Recent Activity</h2>
        <ul class="mt-4 bg-white p-4 rounded-lg shadow">
            <!-- Example of activity items -->
            <li class="border-b py-2 text-gray-600">Added 5 new students.</li>
            <li class="border-b py-2 text-gray-600">Assigned teacher John to Math section A.</li>
            <li class="border-b py-2 text-gray-600">Updated section B information.</li>
            <li class="border-b py-2 text-gray-600">Created new subject: Science.</li>
        </ul>
    </div>

    <!-- Graphs and Charts Section -->
    <div class="mt-8">
        <h2 class="text-2xl font-semibold text-gray-800">Performance Overview</h2>
        <div class="mt-4 bg-white p-6 rounded-lg shadow">
            <!-- Placeholder for graphs/charts -->
            <p class="text-gray-600">[Graphs and charts will be displayed here]</p>
        </div>
    </div>

    <!-- Important Notices Section -->
    <div class="mt-8">
        <h2 class="text-2xl font-semibold text-gray-800">Important Notices</h2>
        <div class="mt-4 bg-white p-6 rounded-lg shadow">
            <p class="text-gray-600">No new notices at this time.</p>
        </div>
    </div>

    <div class="mt-6">
        <a href="../fn/admin_logout.php" class="bg-red-500 text-white py-2 px-4 rounded hover:bg-red-600">Logout</a>
    </div>
</main>

    </div>

</body>
</html>
