<?php
// Start session
session_start();

// Check if the user is already logged in, if so, redirect to home.php
if (isset($_SESSION['student_id'])) {
    header("Location: ../home.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cebu Eastern College - Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="icon" href="../img/cec.png" type="image/x-icon" />

    <style>
        .hidden {
            display: none;
        }
        /* Ensuring equal height for both forms */
        .form-container {
            min-height: 500px; /* Set minimum height to match forms */
        }
    </style>
</head>
<body class="bg-blue-400 flex items-center justify-center min-h-screen">

<!-- Main Container -->
<div class="bg-white flex flex-col md:flex-row rounded-lg shadow-lg w-full max-w-4xl">

    <!-- Right Side: Brand Section (Visible on top in mobile view) -->
    <div class="bg-blue-600 w-full p-8 flex items-center justify-center rounded-t-lg md:rounded-l-lg md:rounded-tr-none md:w-1/2 md:order-2">
        <div class="text-center">
            <img src="../img/cec.png" alt="Brand Logo" class="mb-4 w-24 mx-auto">
            <h1 class="text-4xl text-white font-bold">CEC - ITrack</h1>
            <p class="text-gray-100 mt-2">Track Your Progress, Shape Your Future!</p>
        </div>
    </div>

    <!-- Left Side: Form Section -->
    <div class="w-full p-8 form-container flex flex-col justify-center md:w-1/2 md:order-1">

        <!-- Form Toggle Buttons -->
        <div class="flex justify-around gap-4 mb-6">
            <button id="loginBtn" class="w-full py-2 font-semibold rounded-lg text-white bg-blue-700 hover:bg-blue-800 focus:outline-none">Login</button>
            <button id="registerBtn" class="w-full py-2 font-semibold rounded-lg text-white bg-blue-700 hover:bg-blue-800 focus:outline-none">Register</button>
        </div>

        <!-- Forms Container -->
        <div class="relative flex items-center h-full">
            <!-- Login Form -->
            <form id="loginForm" class="space-y-4" action="../fn/student_login.php" method="post">
                <?php
                if (isset($_SESSION['error_message'])) {
                    echo '<span class="text-red-500">' . $_SESSION['error_message'] . '</span>';
                    unset($_SESSION['error_message']); // Clear the message after displaying it
                }
                ?>
                <input name="lrn" placeholder="LRN" type="text" required class="w-full p-3 rounded-lg border border-gray-600 bg-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-600">
                <input type="password" name="password" placeholder="Password" required class="w-full p-3 rounded-lg border border-gray-600 bg-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-600">
                <button type="submit" class="w-full py-3 font-semibold text-white rounded-lg bg-blue-600 hover:bg-blue-700 focus:ring-4 focus:ring-blue-600">Login</button>
            </form>

            <!-- Register Form -->
            <form id="registerForm" class="register-form hidden space-y-4" action="../fn/student_register.php" method="POST">
                <div class="grid grid-cols-2 gap-4 mb-4">
                    <input 
                        type="text" 
                        id="last_name" 
                        name="last_name"
                        placeholder="Last Name" 
                        class="border border-gray-600 bg-white rounded p-2 focus:outline-none focus:ring-2 focus:ring-blue-600" 
                        required 
                    />
                    <input 
                        type="text" 
                        id="first_name" 
                        name="first_name"
                        placeholder="First Name" 
                        class="border border-gray-600 bg-white rounded p-2 focus:outline-none focus:ring-2 focus:ring-blue-600" 
                        required 
                    />
                </div>
                <div class="grid grid-cols-2 gap-4 mb-4">
                    <input 
                        type="text" 
                        id="middle_name" 
                        name="middle_name"
                        placeholder="Middle Name (optional)" 
                        class="border border-gray-600 bg-white rounded p-2 focus:outline-none focus:ring-2 focus:ring-blue-600" 
                    />
                    <input 
                        type="text" 
                        id="lrn" 
                        name="lrn"
                        placeholder="LRN" 
                        class="border border-gray-600 bg-white rounded p-2 focus:outline-none focus:ring-2 focus:ring-blue-600" 
                        required 
                    />
                </div>
                <div class="grid grid-cols-2 gap-4 mb-4">
                    <input 
                        type="date" 
                        id="dob" 
                        name="dob"
                        class="border border-gray-600 bg-white rounded p-2 focus:outline-none focus:ring-2 focus:ring-blue-600" 
                        required 
                    />
                    <input 
                        type="email" 
                        id="email" 
                        name="email"
                        placeholder="Email" 
                        class="border border-gray-600 bg-white rounded p-2 focus:outline-none focus:ring-2 focus:ring-blue-600" 
                        required 
                    />
                </div>
                <div class="grid grid-cols-2 gap-4 mb-4">
                    <input 
                        type="password" 
                        id="password" 
                        name="password" 
                        placeholder="Password" 
                        class="border border-gray-600 bg-white rounded p-2 focus:outline-none focus:ring-2 focus:ring-blue-600" 
                        required 
                    />
                    <input 
                        type="password" 
                        id="repeat_password" 
                        name="repeat_password" 
                        placeholder="Repeat Password" 
                        class="border border-gray-600 bg-white rounded p-2 focus:outline-none focus:ring-2 focus:ring-blue-600" 
                        required 
                    />
                </div>
                <div>
                    <button type="submit" class="w-full py-3 font-semibold text-white rounded-lg bg-blue-600 hover:bg-blue-700 focus:ring-4 focus:ring-blue-600">Register</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    const loginForm = document.getElementById('loginForm');
    const registerForm = document.getElementById('registerForm');
    const loginBtn = document.getElementById('loginBtn');
    const registerBtn = document.getElementById('registerBtn');

    loginBtn.addEventListener('click', () => {
        loginForm.classList.remove('hidden');
        registerForm.classList.add('hidden');

        loginBtn.classList.add('bg-blue-800');
        registerBtn.classList.remove('bg-blue-800');
    });

    registerBtn.addEventListener('click', () => {
        registerForm.classList.remove('hidden');
        loginForm.classList.add('hidden');

        registerBtn.classList.add('bg-blue-800');
        loginBtn.classList.remove('bg-blue-800');
    });
</script>

</body>
</html>
