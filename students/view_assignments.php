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
</style>
</head>
<body>

<div class="flex h-screen bg-[#0165DC]">
<?php
include '../component/aside.php';
?>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col">
        <?php
include '../component/header.php';
?>

            <main class="flex-1 p-6 flex flex-col md:flex-row space-x-4">
                <!-- Left Container -->
                <div class="flex-1 mb-4 md:mb-0 bg-white p-4 shadow-lg rounded-lg">
                 
        <div>
       <?php
       // Display the results as before
foreach ($teachers as $teacher) {
    $teacher_id = $teacher['teacher_id'];
    echo "<div class='mb-6 border p-4 rounded-lg shadow-md'>";
    echo "<h2 class='text-xl font-semibold mb-2'>{$teacher['name']}'s Assignments and Quizzes</h2>";

    // Assignments Section
    echo "<h3 class='font-medium text-lg'>Assignments</h3>";
    if (!empty($assignments_and_quizzes[$teacher_id]['assignments'])) {
        echo "<ul class='list-disc pl-5'>";
        foreach ($assignments_and_quizzes[$teacher_id]['assignments'] as $assignment) {
            $score = $assignment['score'] > 0 ? htmlspecialchars($assignment['score']) : 'Not Submitted';
            $due_date = date('h:i A', strtotime($assignment['due_date'])); // Format to 12-hour
            echo "<li class='mb-2'>
                    <strong class='text-blue-600'>" . htmlspecialchars($assignment['assignment_title']) . "</strong> 
                    - Due: " . $due_date . "
                    <p class='text-gray-700'>" . htmlspecialchars($assignment['description']) . "</p>
                    <p class='text-sm'>Score: " . $score . "</p>
                  </li>";
        }
        echo "</ul>";
    } else {
        echo "<p>No assignments found.</p>";
    }

    // Quizzes Section
    echo "<h3 class='font-medium text-lg'>Quizzes</h3>";
    if (!empty($assignments_and_quizzes[$teacher_id]['quizzes'])) {
        echo "<ul class='list-disc pl-5'>";
        foreach ($assignments_and_quizzes[$teacher_id]['quizzes'] as $quiz) {
            $score = $quiz['score'] > 0 ? htmlspecialchars($quiz['score']) : 'Not Submitted';
            $due_date = date('h:i A', strtotime($quiz['due_date'])); // Format to 12-hour
            echo "<li class='mb-2'>
                    <strong class='text-blue-600'>" . htmlspecialchars($quiz['title']) . "</strong> 
                    - Due: " . $due_date . "
                    <p class='text-gray-700'>" . htmlspecialchars($quiz['description']) . "</p>
                    <p class='text-sm'>Score: " . $score . "</p>
                  </li>";
        }
        echo "</ul>";
    } else {
        echo "<p>No quizzes found.</p>";
    }

    echo "</div>"; // Close teacher div
}
       ?>

        <!-- <h4 class="text-lg font-semibold mb-2">Your Total Grade: <?php echo number_format($percentage, 2) . '%'; ?></h4> -->
        </div>
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
