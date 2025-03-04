<script src="https://cdn.tailwindcss.com"></script>


<header class="bg-[#0165DC] text-white p-4 flex justify-between items-center">
        <!-- Search Bar -->
        <div class="flex flex-1 mx-4">
            <input
                type="text"
                id="subjectSearch"
                placeholder="Search by subject code..."
                class="w-1/3 p-2 rounded-lg border border-blue-300"
                aria-label="Search"
            />
            <button onclick="searchSubject()" class="ml-2 bg-blue-500 text-white px-4 py-2 rounded-lg">Search</button>
        </div>
    
        <!-- Profile Image and Name -->
        <div class="flex items-center">
        <img data-popover-target="popover-default" src="<?php echo htmlspecialchars($image_path); ?>" alt="Profile" class="rounded-full mr-2 w-10 h-10">
            <span  class="hidden md:block font-bold"><?php echo htmlspecialchars($last_name); ?></span>
            <div data-popover id="popover-default" role="tooltip" class="absolute z-10 invisible inline-block w-50 text-sm text-gray-500 transition-opacity duration-300 bg-white border border-gray-200 rounded-lg shadow-sm opacity-0 dark:text-gray-400 dark:border-gray-600 dark:bg-gray-800">
                <div class="px-3 py-2 bg-gray-100 border-b border-gray-200 rounded-t-lg dark:border-gray-600 dark:bg-gray-700">
                    <a href="../fn/student_logout.php" class="font-semibold text-gray-900 dark:text-white">Logout</a>
                </div>
                <div data-popper-arrow></div>
            </div>
        </div>
    
        <button id="burger" class="md:hidden p-2 focus:outline-none" aria-label="Toggle sidebar">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7" />
            </svg>
        </button>
    </header>

    <script src="https://cdn.jsdelivr.net/npm/flowbite@2.5.2/dist/flowbite.min.js"></script>