<script src="https://cdn.tailwindcss.com"></script>

<aside id="sidebar" class="fixed inset-y-0 left-0 w-64 bg-[#0165DC] text-[#F5F4F5] p-4 transform -translate-x-full transition-transform duration-300 ease-in-out md:relative md:translate-x-0 z-10">
            <div class="flex items-center mb-4">
                <!-- Logo -->
                <img src="../img/cec.png" alt="Logo" class="ml-4 mr-2 h-14">
                <h1 class="text-xl font-bold">ITrack</h1>
            </div>

            <nav class="mt-10 ml-6">
                <ul>
                    <li class="mb-4">
                        <a href="home.php" class="hover:text-blue-300 active:text-blue-300">Dashboard</a>
                    </li>
                    <li class="mb-4">
                        <a href="view_subjects.php" class="hover:text-blue-300">Subject</a>
                    </li>
                    <li class="mb-4">
                        <a href="view_progress_report.php" class="hover:text-blue-300">Progress Report</a>
                    </li>
                    <li class="mb-4">
                    <a href="view_assignments.php?section_id=<?php echo urlencode($section['section_id']); ?>" class="hover:text-blue-300">View Assignments</a>
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