<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['student_id'])) {
    header("Location: student_login.php");
    exit();
}

// Include the database connection file
include '../fn/dbcon.php';

// Fetch the student's information based on the session variable
$student_id = $_SESSION['student_id'];
$stmt = $con->prepare("SELECT * FROM students WHERE id = ?");
$stmt->bind_param("i", $student_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $student = $result->fetch_assoc();
} else {
    echo "<script>alert('Student not found!'); window.location.href='login.php';</script>";
    exit();
}

// Fetch the student's section ID
$section_query = "SELECT section_id FROM students WHERE id = ?";
$section_stmt = $con->prepare($section_query);
$section_stmt->bind_param("i", $student_id);
$section_stmt->execute();
$section_result = $section_stmt->get_result();
$section = $section_result->fetch_assoc();

// Initialize subjects variable
$subjects_result = null;
$subjects_count = 0;

if (!empty($section['section_id'])) {
    // Fetch subjects the student is enrolled in based on their section
    $subjects_query = "
        SELECT s.subject_code, s.subject_name, ts.teacher_id 
        FROM subjects s 
        JOIN teacher_section ts ON s.id = ts.subject_id 
        WHERE ts.section_id = ? 
    ";
    $subjects_stmt = $con->prepare($subjects_query);
    $subjects_stmt->bind_param("i", $section['section_id']);
    $subjects_stmt->execute();
    $subjects_result = $subjects_stmt->get_result();
    $subjects_count = $subjects_result->num_rows;
}

// Fetch the student's grades from assignments
$grades_query = "
    SELECT 
        COALESCE(SUM(sub.score), 0) AS total_assignment_score,
        COALESCE(SUM(a.perfect_score), 0) AS total_assignment_possible_score
    FROM 
        submissions sub
    JOIN 
        assignments a ON sub.assignment_id = a.id
    WHERE 
        sub.student_id = ?";

// Fetch quiz grades
$quiz_query = "
    SELECT 
        COALESCE(SUM(qs.score), 0) AS total_quiz_score,
        COALESCE(SUM(q.perfect_score), 0) AS total_quiz_possible_score
    FROM 
        quiz_submissions qs
    JOIN 
        quizzes q ON qs.quiz_id = q.id
    WHERE 
        qs.student_id = ?";

// Fetch assignment grades
$grades_stmt = $con->prepare($grades_query);
$grades_stmt->bind_param("i", $student_id);
$grades_stmt->execute();
$grades_result = $grades_stmt->get_result();
$grades = $grades_result->fetch_assoc();

// Fetch quiz grades
$quiz_stmt = $con->prepare($quiz_query);
$quiz_stmt->bind_param("i", $student_id);
$quiz_stmt->execute();
$quiz_result = $quiz_stmt->get_result();
$quiz_data = $quiz_result->fetch_assoc();

// Calculate total scores
$total_assignment_score = $grades['total_assignment_score'];
$total_assignment_possible_score = $grades['total_assignment_possible_score'];

$total_quiz_score = $quiz_data['total_quiz_score'];
$total_quiz_possible_score = $quiz_data['total_quiz_possible_score'];

// Calculate overall scores
$total_score = $total_assignment_score + $total_quiz_score;
$total_possible_score = $total_assignment_possible_score + $total_quiz_possible_score;

// Calculate percentage
$percentage = 0;
if ($total_possible_score > 0) {
    $percentage = ($total_score / $total_possible_score) * 100;
}

// Fetch the final grades per subject
$final_grades_query = "
    SELECT 
        s.subject_code, 
        s.subject_name,
        COALESCE(SUM(sub.score), 0) AS total_assignment_score,
        COALESCE(SUM(a.perfect_score), 0) AS total_assignment_possible_score,
        COALESCE(SUM(qs.score), 0) AS total_quiz_score,
        COALESCE(SUM(q.perfect_score), 0) AS total_quiz_possible_score
    FROM 
        subjects s
    LEFT JOIN 
        assignments a ON s.id = a.subject_id
    LEFT JOIN 
        submissions sub ON a.id = sub.assignment_id AND sub.student_id = ?
    LEFT JOIN 
        quizzes q ON s.id = q.subject_id
    LEFT JOIN 
        quiz_submissions qs ON q.id = qs.quiz_id AND qs.student_id = ?
    WHERE 
        s.id IN (SELECT subject_id FROM teacher_section WHERE section_id = ?)
    GROUP BY 
        s.id";

$final_grades_stmt = $con->prepare($final_grades_query);
$final_grades_stmt->bind_param("iii", $student_id, $student_id, $section['section_id']);
$final_grades_stmt->execute();
$final_grades_result = $final_grades_stmt->get_result();

// Prepare data for Chart.js
$final_grades_data = [];
while ($row = $final_grades_result->fetch_assoc()) {
    $total_score = $row['total_assignment_score'] + $row['total_quiz_score'];
    $total_possible_score = $row['total_assignment_possible_score'] + $row['total_quiz_possible_score'];
    $final_percentage = ($total_possible_score > 0) ? ($total_score / $total_possible_score) * 100 : 0;

    $final_grades_data[] = [
        'subject' => $row['subject_code'] . ' - ' . $row['subject_name'],
        'final_grade' => number_format($final_percentage, 2)
    ];
}
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
        body {
            background-color: #f9fafb;
        }
        header {
            text-align: center; 
            padding: 20px;
            background-color: #4a5568;
            color: white;
        }
        main {
            padding: 20px;
            max-width: 800px;
            margin: 0 auto;
        }
        .subject-box {
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            padding: 20px;
            margin-bottom: 20px;
            cursor: pointer;
        }
        /* Dark mode styles */
        .dark {
            background-color: #2d3748; /* Dark background */
            color: white; /* Light text */
        }
        .dark .subject-box {
            background-color: #4a5568; /* Dark subject box */
        }
    </style>
</head>
<body>
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
