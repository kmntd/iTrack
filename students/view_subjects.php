<?php
include '../fn/dbcon.php';
include '../data/home.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home - Grade Monitoring System</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>

<div class="flex h-screen bg-[#0165DC]">
    <?php include '../component/aside.php'; ?>

    <!-- Main Content -->
    <div class="flex-1 flex flex-col">
        <?php include '../component/header.php'; ?>

        <main class="flex-1 p-6 flex flex-col md:flex-row space-x-4">
            <!-- Left Container -->
            <div class="flex w-full mb-4 md:mb-0 bg-white p-4 shadow-lg rounded-lg">
                <!-- Left Container (Larger) -->
                <div class="flex-1 w-3/4 p-4"> 
                <?php if ($subjects_result): // Ensure this condition is valid ?>
    <div class="flex flex-wrap -m-2">
        <?php
        $colors = ['#0165DC', '#4088E3'];
        $colorCount = count($colors);
        $index = 0; // To cycle through colors
        
        while ($subject = $subjects_result->fetch_assoc()): ?>
            <div class="w-1/2 p-2"> <!-- Change to half width -->
                <a href="subject_details.php?subject_code=<?php echo urlencode($subject['subject_code']); ?>&teacher_id=<?php echo urlencode($subject['teacher_id']); ?>" 
                   class="block p-4 rounded-lg shadow-lg min-h-[150px] flex flex-col justify-between transition-transform transform hover:scale-105 hover:shadow-xl" 
                   style="background-color: <?php echo $colors[$index % $colorCount]; ?>;"
                   onmouseover="updateBanner('<?php echo htmlspecialchars($subject['teacher_name']); ?>', '<?php echo htmlspecialchars($subject['subject_name']); ?>', '<?php echo htmlspecialchars($subject['subject_code']); ?>')"
                   onmouseout="clearBanner()">
                    <h2 class="text-xl text-white font-bold title-font mb-1 truncate"> <!-- Reduced margin -->
                        <?php echo htmlspecialchars($subject['subject_name']); ?>
                    </h2>
                    <div class="flex-grow"> <!-- Allow the div to take remaining space -->
                        <h3 class="tracking-widest text-gray-300 text-sm font-medium title-font mb-1">
                            Subject Code
                        </h3>
                        <p class="text-white text-lg mb-1"> <!-- Reduced margin -->
                            <?php echo htmlspecialchars($subject['subject_code']); ?>
                        </p>
                    </div>
                </a>
            </div>
            <?php $index++; // Increment the index to use the next color ?>
        <?php endwhile; ?>
    </div>
<?php else: ?>
    <p class="text-gray-600">You are not enrolled in any subjects yet.</p>
<?php endif; ?>


                </div>

                <!-- Right Container (Smaller) -->
                <div class="w-1/4 p-4 bg-blue-400"> 
                    <div id="teacher-banner" class="bg-white p-4 rounded-lg shadow-md">
                        <h2 class="text-lg font-bold mb-2">Teacher Info</h2>
                        <p id="teacher-name" class="text-gray-600 mb-1"></p>
                        <p id="subject-name" class="text-gray-600 mb-1"></p>
                        <p id="subject-code" class="text-gray-600 mb-1"></p>
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

    function updateBanner(name, subjectName, subjectCode) {
        document.getElementById('teacher-name').innerText = name || 'Unknown Teacher';
        document.getElementById('subject-name').innerText = subjectName || 'Unknown Subject';
        document.getElementById('subject-code').innerText = subjectCode || 'N/A';
    }

    function clearBanner() {
        document.getElementById('teacher-name').innerText = '';
        document.getElementById('subject-name').innerText = '';
        document.getElementById('subject-code').innerText = '';
    }
</script>

</body>
</html>
