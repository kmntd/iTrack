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
    
</head>
<body class="">

<div class="flex h-screen bg-[#0165DC]">
<?php
include '../component/aside.php';
?>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col">
        <?php
include '../component/header.php';
?>

<!-- Custom Alert -->
<div id="customAlert" class="hidden fixed top-0 left-1/2 transform -translate-x-1/2 mt-4 bg-red-600 text-white px-4 py-2 rounded-lg shadow-lg z-50" role="alert">
    <span id="alertMessage">This is an alert message!</span>
    <button onclick="closeAlert()" class="ml-4 text-white focus:outline-none">
        &times;
    </button>
</div>




            <main class="flex-1 p-6 flex flex-col md:flex-row space-x-4">
                <div class="flex-1 mb-4 md:mb-0 bg-white p-4 shadow-lg rounded-lg">
                     <!-- First Banner -->
                     <div class="bg-blue-500 text-white p-4 rounded-lg mb-4">
                    <h3 class="text-xl font-bold">Welcome <?php echo htmlspecialchars($last_name . ' ' . $first_name . ' ' . $middle_name); ?></h3>
                        <p><?php echo htmlspecialchars($lrn); ?></p>
                        <p>Welcome to your dashboard!</p>
                    </div>
                     <!-- Second Banner -->
                     <div class="bg-white text-white p-8 rounded-lg mb-4">
                    <div class="mt-6">
                        <canvas id="finalGradesChart"></canvas>
                    </div>    
                    </div>
                </div>

                <!-- Right Container -->
                <div class="flex-initial w-full md:w-2/5 bg-white p-4 shadow-lg rounded-lg">
                    <!-- First Banner -->
                    <div class="bg-blue-500 text-white p-4 rounded-lg mb-4">
                    <?php if (!empty($admin_announcements)): ?>
    <h3 class='text-xl font-semibold mb-4'>Admin Announcements</h3>
    <ul>
        <?php foreach ($admin_announcements as $announcement): ?>
            <li>
                <strong><?php echo htmlspecialchars($announcement['created_at']); ?>:</strong>
                <p><?php echo htmlspecialchars($announcement['message']); ?></p>
            </li>
        <?php endforeach; ?>
    </ul>
<?php else: ?>
    <p>No announcements available for admins.</p>
<?php endif; ?>
                    </div>
                    
                    <div class="bg-blue-600 text-white p-8 rounded-lg mb-4">
                        <!-- Display Teacher Announcements -->
<?php if (!empty($announcements)): ?>
    <h3 class='text-xl font-semibold mb-4'>Teacher Announcements</h3>
    <ul>
        <?php foreach ($announcements as $announcement): ?>
            <li class="max-w-full break-words">
                <strong><?php echo htmlspecialchars($announcement['created_at']); ?>:</strong>
                <p class="break-words"><?php echo htmlspecialchars($announcement['message']); ?></p>
                <p class="text-gray-100"><em>Posted by: <?php echo htmlspecialchars($announcement['teacher_name']); ?></em></p>
            </li>
        <?php endforeach; ?>
    </ul>
<?php else: ?>
    <p>No announcements available for your section.</p>
<?php endif; ?>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script>
        const burger = document.getElementById('burger');
        const sidebar = document.getElementById('sidebar');

        burger.addEventListener('click', () => {
            sidebar.classList.toggle('-translate-x-full');
        });
    </script>

   
            <a href="../fn/student_logout.php" class=" hover:underline">Logout</a>
        </nav>
       
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

<script>
    function searchSubject() {
        const input = document.getElementById('subjectSearch').value.trim();
        const teacherId = 1; // Replace with the actual teacher ID if needed

        // Check if input is not empty before redirecting
        if (input.length > 0) {
            // AJAX call to check if the subject code exists in the database
            fetch(`check_subject.php?subject_code=${encodeURIComponent(input)}`)
                .then(response => response.json())
                .then(data => {
                    if (data.exists) {
                        // Redirect if the subject code exists
                        window.location.href = `http://localhost/gms/students/subject_details.php?subject_code=${encodeURIComponent(input)}&teacher_id=${teacherId}`;
                    } else {
                        // Show the custom alert if the subject code does not exist
                        showAlert(`The subject code "${input}" does not exist. Please try again.`);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showAlert("An error occurred while checking the subject code.");
                });
        } else {
            // Alert if input is empty
            showAlert("Please enter a search query.");
        }
    }

    function showAlert(message) {
        const alertBox = document.getElementById('customAlert');
        const alertMessage = document.getElementById('alertMessage');
        alertMessage.textContent = message; // Set the alert message
        alertBox.classList.remove('hidden'); // Show the alert box

        // Automatically hide the alert after 5 seconds
        setTimeout(() => {
            closeAlert();
        }, 5000);
    }

    function closeAlert() {
        const alertBox = document.getElementById('customAlert');
        alertBox.classList.add('hidden'); // Hide the alert box
    }

    // Add event listener for "Enter" key
    document.getElementById('subjectSearch').addEventListener('keypress', function(event) {
        if (event.key === 'Enter') {
            searchSubject(); // Call search function when "Enter" is pressed
        }
    });
</script>

</body>
</html>
