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
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            background-color: #e0f7fa; /* Light ocean blue background */
            font-family: 'Arial', sans-serif;
        }
        .container {
            border: 2px dashed #4fc3f7; /* Light blue dashed border */
            border-radius: 10px;
            background-color: #ffffff;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            height: 90vh;
            overflow: hidden;
        }
        .dashed-button {
            border: 2px dashed #4fc3f7; /* Light blue border */
            font-weight: 600;
            padding: 0.5rem;
            border-radius: 0.375rem;
            transition: background-color 0.3s, color 0.3s;
            width: 100%;
            background-color: transparent;
            color: #0288d1; /* Ocean blue text */
        }
        .dashed-button:hover {
            background-color: #b3e5fc; /* Lighter blue on hover */
            color: #01579b; /* Darker blue text on hover */
        }
        .login-container, .register-container {
            background-color: #e0f7fa; /* Light ocean blue background */
            width: 100%;
            border-radius: 10px;
            padding: 1.5rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }
        .login-form, .register-form {
            width: 100%;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        .hidden {
            display: none;
        }
        /* New styles for sub-containers */
        .sub-container {
            background-color: #e0f7fa; /* Light ocean blue background */
            border: 2px dashed #4fc3f7; /* Light blue dashed border */
            border-radius: 10px;
        }
    </style>
</head>
<body>

<div class="flex flex-col w-full max-w-full p-4 space-y-2"> <!-- Adjusted space-y-4 to space-y-2 -->

        <!-- Header Section -->
        <div class="w-full mb-2"> <!-- Adjusted mb-4 to mb-2 -->
            <div class="p-4 bg-white border-2 border-dashed border-blue rounded-lg flex justify-between items-center">
                <h1 class="text-xl font-bold">Cebu Eastern College</h1>
            </div>
            <div class="p-4 bg-white/30 backdrop-blur-md rounded-lg lg:hidden"></div>
        </div>

        <div class="flex flex-col md:flex-row w-full space-y-2 md:space-y-0"> <!-- Adjusted space-y-4 to space-y-2 -->
            <!-- Sidebar -->
            <div class="flex flex-col w-full md:w-1/3 lg:w-1/5 space-y-2"> <!-- Adjusted space-y-4 to space-y-2 -->
                <div class="container flex-1 p-6 bg-gray-50 flex flex-col items-center justify-between">
                    <!-- Additional content can go here (Sidebar content) -->
                    <h2 class="text-lg font-bold">Sidebar</h2>
                    <p class="text-gray-700">Links, information, or other content can be added here.</p>
                </div>
            </div>

            <!-- Main Content -->
            <div class="container w-full md:w-2/3 lg:w-4/5 p-6 mt-0 ml-2"> <!-- Added ml-2 for margin-left -->
                <!-- Two Sub-Containers -->
                <div class="flex flex-col md:flex-row space-y-2 md:space-y-0 md:space-x-4 h-full"> <!-- Adjusted space-y-4 to space-y-2 -->
                    <div class="sub-container flex-1 p-4 flex justify-center items-center">
                        <!-- Optional Sub-Container Content -->
                        <p>Content Area 1</p>
                    </div>
                    <div class="sub-container flex-1 p-4">
                        <!-- Optional Sub-Container Content -->
                        <p>Content Area 2</p>
                    </div>
                </div>
            </div>
        </div>
    </div>


        





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

        <?php if ($subjects_count > 0): ?>
            <div>
                <h4 class="text-lg font-semibold mb-2">Enrolled Subjects:</h4>
                <?php while ($subject = $subjects_result->fetch_assoc()): ?>
                    <div class="subject-box" onclick="window.location.href='subject_details.php?subject_code=<?php echo urlencode($subject['subject_code']); ?>&teacher_id=<?php echo urlencode($subject['teacher_id']); ?>'">
                        <p><?php echo htmlspecialchars($subject['subject_code']); ?> - <?php echo htmlspecialchars($subject['subject_name']); ?></p>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <p class="text-gray-600">You are not enrolled in any subjects yet.</p>
        <?php endif; ?>

        <!-- Add the Chart.js visualization -->
        <div class="mt-6">
            <canvas id="finalGradesChart"></canvas>
        </div>
    </main>

    <script>
        const finalGradesData = <?php echo json_encode($final_grades_data); ?>;
        
        const labels = finalGradesData.map(item => item.subject);
        const data = finalGradesData.map(item => parseFloat(item.final_grade));

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

        const applyDarkMode = () => {
            if (localStorage.getItem('dark-mode') === 'true') {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        };

        // Initial check for dark mode
        applyDarkMode();

        document.getElementById('toggleDarkMode').addEventListener('click', () => {
            const isDarkMode = document.documentElement.classList.toggle('dark');
            localStorage.setItem('dark-mode', isDarkMode);
        });
    </script>
</body>
</html>
