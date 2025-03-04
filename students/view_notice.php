<?php
include '../fn/dbcon.php';
include '../data/home.php';

// Check if the user is logged in
if (!isset($_SESSION['student_id'])) {
    header("Location: student_login.php");
    exit();
}

// Fetch the student's information
$student_id = $_SESSION['student_id'];
$stmt = $con->prepare("SELECT first_name, middle_name, last_name, lrn FROM students WHERE id = ?");
$stmt->bind_param("i", $student_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $student = $result->fetch_assoc();
} else {
    echo "<script>alert('Student not found!'); window.location.href='student_login.php';</script>";
    exit();
}

// Fetch the student's section ID
$section_query = "SELECT section_id FROM students WHERE id = ?";
$section_stmt = $con->prepare($section_query);
$section_stmt->bind_param("i", $student_id);
$section_stmt->execute();
$section_result = $section_stmt->get_result();
$section = $section_result->fetch_assoc();

if (!$section) {
    echo "<script>alert('Section not found!'); window.location.href='student_login.php';</script>";
    exit();
}

$section_id = $section['section_id'];

// Fetch teacher announcements
$teacher_announcements = [];
$teacher_announcements_query = "SELECT ta.title, ta.message, ta.created_at, 
                                CONCAT(t.first_name, ' ', t.last_name) AS teacher_name
                                FROM teacher_announcements ta 
                                JOIN teachers t ON ta.teacher_id = t.id
                                WHERE ta.section_id = ? 
                                ORDER BY ta.created_at DESC";
$teacher_announcements_stmt = $con->prepare($teacher_announcements_query);
$teacher_announcements_stmt->bind_param("i", $section_id);
$teacher_announcements_stmt->execute();
$teacher_announcements_result = $teacher_announcements_stmt->get_result();

while ($row = $teacher_announcements_result->fetch_assoc()) {
    $row['created_at'] = date('m/d/Y h:i A', strtotime($row['created_at']));
    $teacher_announcements[] = $row;
}

// Fetch admin announcements
$admin_announcements = [];
$announcements_query = "SELECT message, created_at FROM admin_announcements ORDER BY created_at DESC";
$announcements_stmt = $con->prepare($announcements_query);
$announcements_stmt->execute();
$announcements_result = $announcements_stmt->get_result();

while ($row = $announcements_result->fetch_assoc()) {
    $row['created_at'] = date('m/d/Y h:i A', strtotime($row['created_at']));
    $admin_announcements[] = $row;
}

// Other data fetching...
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home - Grade Monitoring System</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .parent-container {
            width: 600px; /* Set a fixed width or use max-width for responsiveness */
            margin: 0 auto; /* Center the container */
        }

        #finalAverageDisplay {
            font-weight: bold;
            margin-top: 20px;
            font-size: 1.5em;
            color: #4CAF50; /* Green color */
        }
        #gradesDisplay {
            margin-top: 20px;
            font-size: 1.2em;
            color: #333;
        }
        .grade-item {
            padding: 5px 0;
            border-bottom: 1px solid #ddd; /* Light gray border */
        }
        .grade-item:last-child {
            border-bottom: none; /* Remove border for the last item */
        }
        
        .hidden-message {
            display: none; /* Keep this to show/hide full messages */
            width: 100%; /* Ensure full width within the container */
            max-height: 200px; /* Optional: Limit height for larger messages */
            overflow: auto; /* Enable scrolling if content exceeds max height */
        }

        .message-container {
            cursor: pointer;
            overflow: hidden; /* Prevent overflow */
            text-overflow: ellipsis; /* Add ellipsis */
            white-space: nowrap; /* Prevent text from wrapping */
            width: 100%; /* Make sure it takes the full width */
        }

        .line-clamp {
            display: -webkit-box; 
            -webkit-box-orient: vertical; 
            -webkit-line-clamp: 2; /* Change this number for the desired visible lines */
            overflow: hidden;
        }
        /* New styles for fixed height and scrolling */
        .parent-container {
            max-height: 650px; /* Set your desired height */
            overflow-y: auto; /* Enable vertical scrolling */
        }
    </style>
</head>
<body>
<div class="flex h-screen bg-[#0165DC]">
    <?php include '../component/aside.php'; ?>
    
    <!-- Main Content -->
    <div class="flex-1 flex flex-col">
        <?php include '../component/header.php'; ?>

        <main class="flex-1 p-6 flex flex-col md:flex-row space-x-4">
            <div class="flex-1 mb-4 md:mb-0 bg-white p-4 shadow-lg rounded-lg parent-container">
                <div class="container mx-auto flex flex-wrap">
                    <!-- Left Container: Teacher Announcements -->
                    <div class="w-full md:w-1/2 p-4">
                        <h2 class="text-2xl font-bold text-center mb-4 text-blue-600">Teacher Announcements</h2>

                        <?php if (empty($teacher_announcements)): ?>
                            <p class="text-gray-500 text-center">No announcements available for your section.</p>
                        <?php else: ?>
                            <?php foreach ($teacher_announcements as $index => $announcement): ?>
                                <div class="bg-white p-4 mb-4 rounded-lg shadow-lg transition-transform transform hover:scale-105">
                                    <div class="message-container" onclick="toggleMessage(<?php echo $index; ?>)">
                                        <div class="flex items-center mb-2">
                                            <div>
                                                <p class="font-semibold text-blue-600"><?php echo htmlspecialchars($announcement['teacher_name'] ?? 'Unknown Teacher'); ?></p>
                                                <p class="text-lg font-bold text-gray-800">
                                                    <?php echo isset($announcement['title']) && !empty($announcement['title']) ? htmlspecialchars($announcement['title']) : 'No Title'; ?>
                                                </p>
                                                <p class="text-gray-600 line-clamp-2"><?php echo htmlspecialchars(substr($announcement['message'], 0, 100)); ?>...</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="hidden-message mt-2" id="message-<?php echo $index; ?>">
                                        <p class="whitespace-normal break-words"><?php echo htmlspecialchars($announcement['message']); ?></p>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>

                    <!-- Right Container: Admin Announcements -->
                    <div class="w-full md:w-1/2 p-4">
                        <h2 class="text-xl font-bold mb-4">Admin Announcements</h2>
                        <?php foreach ($admin_announcements as $announcement): ?>
                            <div class="bg-white p-4 mb-2 rounded shadow">
                                <p><?php echo htmlspecialchars($announcement['message']); ?></p>
                                <p class="text-gray-500 text-sm"><?php echo htmlspecialchars($announcement['created_at']); ?></p>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </main>

        <script>
            function toggleMessage(index) {
                console.log(`Toggling message for index: ${index}`);
                const messageContainer = document.getElementById(`message-${index}`);
                if (messageContainer) {
                    messageContainer.classList.toggle('hidden-message');
                } else {
                    console.error(`No element found with ID: message-${index}`);
                }
            }

            const burger = document.getElementById('burger');
            const sidebar = document.getElementById('sidebar');

            if (burger) {
                burger.addEventListener('click', () => {
                    sidebar.classList.toggle('-translate-x-full');
                });
            }
        </script>
    </div>
</div>
</body>
</html>
