<?php
include '../fn/dbcon.php';
?>

<?php
include '../data/home.php';
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
            display: none;
        }
        .message-container {
            cursor: pointer;
        }
</style>
</head>
<body>

<div class="flex h-screen">
        <!-- Sidebar -->
        <aside id="sidebar" class="fixed inset-y-0 left-0 w-64 bg-blue-800 text-white p-4 transform -translate-x-full transition-transform duration-300 ease-in-out md:relative md:translate-x-0 z-10">
            <div class="flex items-center mb-4">
                <!-- Logo -->
                <img src="https://via.placeholder.com/40" alt="Logo" class="mr-2">
                <h1 class="text-xl font-bold">ITrack</h1>
            </div>

            <nav>
                <ul>
                    <li class="mb-4">
                        <a href="home.php" class="hover:text-blue-300">Dashboard</a>
                    </li>
                    <li class="mb-4">
                        <a href="view_subjects.php" class="hover:text-blue-300">Subject</a>
                    </li>
                    <li class="mb-4">
                        <a href="view_progress_report.php" class="hover:text-blue-300">Progress Report</a>
                    </li>
                    <li class="mb-4">
                    <a href="view_assignments.php?section_id=<?php echo urlencode($section['section_id']); ?>" class="text-blue-500 hover:underline">View Assignments</a>
                    </li>
                    <li class="mb-4">
                        <a href="view_notice.php" class="hover:text-blue-300">Notice</a>
                    </li>
                    <li>
                        <a href="student_settings.php" class="hover:text-blue-300">Settings</a>
                    </li>
                </ul>
            </nav>
        </aside>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col">
        <header class="bg-blue-600 text-white p-4 flex justify-between items-center">
    <!-- Search Bar -->
    <div class="flex flex-1 mx-4">
        <input type="text" placeholder="Search..." class="flex-1 p-2 rounded-lg border border-blue-300" aria-label="Search">
    </div>

    <!-- Profile Image and Name -->
    <div class="flex items-center">
        <img src="<?php echo htmlspecialchars($image_path); ?>" alt="Profile" class="rounded-full mr-2 w-10 h-10"> <!-- Use the fetched image -->
        <span class="hidden md:block"><?php echo htmlspecialchars($last_name); ?></span> 
    </div>

    <button id="burger" class="md:hidden p-2 focus:outline-none" aria-label="Toggle sidebar">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7" />
        </svg>
    </button>
</header>

            <main class="flex-1 p-6 flex flex-col md:flex-row space-x-4">
                <!-- Left Container -->
                <div class="flex-1 mb-4 md:mb-0 bg-white p-4 shadow-lg rounded-lg">
                 
        <div>
        <div class="container mx-auto flex flex-wrap">
        <!-- Left Container: Teacher Announcements -->
        <div class="w-full md:w-1/2 p-4">
            <h2 class="text-xl font-bold mb-4">Teacher Announcements</h2>
            <?php foreach ($teacher_announcements as $announcement): ?>
                <div class="bg-white p-4 mb-2 rounded shadow">
                    <div class="message-container" onclick="toggleMessage(this)">
                        <p class="font-semibold"><?php echo htmlspecialchars($announcement['teacher_name']); ?></p>
                        <p><?php echo htmlspecialchars(substr($announcement['message'], 0, 100)); ?>...</p>
                    </div>
                    <div class="hidden-message mt-2">
                        <p><?php echo htmlspecialchars($announcement['message']); ?></p>
                    </div>
                    <p class="text-gray-500 text-sm"><?php echo htmlspecialchars($announcement['created_at']); ?></p>
                </div>
            <?php endforeach; ?>
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

    <script>
        function toggleMessage(element) {
            const hiddenMessage = element.nextElementSibling;
            hiddenMessage.classList.toggle('hidden-message');
        }
    </script>

    <script>
        const burger = document.getElementById('burger');
        const sidebar = document.getElementById('sidebar');

        burger.addEventListener('click', () => {
            sidebar.classList.toggle('-translate-x-full');
        });
    </script>

    <header>
   
        <h1>Welcome to the Grade Monitoring System</h1>
        <h2>Name: <?php echo htmlspecialchars($student['first_name'] . ' ' . $student['last_name']); ?></h2>
        <h2>Your LRN: <?php echo htmlspecialchars($student['lrn']); ?></h2>
        <nav>
            <a href="../fn/student_logout.php" class="text-white hover:underline">Logout</a>
        </nav>
        <button id="toggleDarkMode" class="mt-4 bg-blue-500 text-white px-4 py-2 rounded">
            Toggle Dark Mode
        </button>
    </header>
    <main>
        <h3 class="text-xl font-semibold mb-4">Your Dashboard</h3>
        
        <!-- Display your overall percentage -->
        <div>
        
            <h4 class="text-lg font-semibold mb-2">Your Total Grade: <?php echo number_format($percentage, 2) . '%'; ?></h4>
        </div>

        

        <!-- Add the Chart.js visualization -->
        <div class="mt-6">
            <canvas id="finalGradesChart"></canvas>
        </div>
    </main>

    <script>
        const finalGradesData = <?php echo json_encode($final_grades_data); ?>;
        
        const labels = finalGradesData.map(item => item.subject);
        const data = finalGradesData.map(item => parseFloat(item.final_grade));

        // Function to calculate the final average
    function calculateFinalAverage(grades) {
        const total = grades.reduce((sum, grade) => sum + grade, 0);
        const average = total / grades.length;
        return average;
    }

     // Function to display each subject's grade
     function displayGrades(gradesData) {
        const gradesDisplay = document.getElementById('gradesDisplay');
        gradesDisplay.innerHTML = ''; // Clear previous content

        gradesData.forEach(item => {
            const gradeElement = document.createElement('div');
            gradeElement.textContent = `${item.subject}: ${item.final_grade}%`;
            gradesDisplay.appendChild(gradeElement);
        });
    }

    // Calculate final average
    const finalAverage = calculateFinalAverage(data);
    
    // Display the final average on the page
    const averageDisplay = document.getElementById('finalAverageDisplay');
    averageDisplay.textContent = `Final Average: ${finalAverage.toFixed(2)}%`;

    // Display individual grades
    displayGrades(finalGradesData);

        const ctx = document.getElementById('finalGradesChart').getContext('2d');
        const finalGradesChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Final Grades (%)',
                    data: data,
                    backgroundColor: 'rgba(75, 192, 192, 0.6)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Percentage'
                        }
                    }
                }
            }
        });
        
    </script>
</body>
</html>
