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
</head>
<body>
    <section class="">
      <!-- THE WHOLE CONTAINER -->
        <div class="w-full h-screen flex justify-center items-center bg-gray-200">
          <!-- A SUB CONTAINER INSIDE OF THE MAIN CONTAINER -->
          <div class="w-[95%] h-[90vh] flex justify-center items-center  bg-[url('../img/main.jpg')] bg-center  bg-cover rounded-2xl">
            <!-- A CONTAINER WITH BORDER -->
            <div class="w-[95%] h-[90%] rounded-2xl">
              <div class="w-full h-full rounded-2xl bg-black/70 ">
                <!-- THE NAVIGATION BAR -->
                <header class="text-gray-600 body-font">
                  <div class="container mx-auto flex flex-wrap p-5 flex-col lg:flex-row items-center">
                    <a class="flex title-font font-medium items-center text-gray-900">
                      <img src="../img/cec.png" class="h-10">
                      <span class="ml-3 text-xl text-white">CEC | ITrack </span>
                    </a>
                  </div>
                </header>
                <!-- END OF THE NAVIGATION BAR -->

                <!-- ANOTHER SECTION UBOS SA HEADER -->
                <section>
                  <!-- SECTION THAT HOLDS TWO SIDEBARD -->
                  <div class=" flex flex-wrap items-center">
                    <!-- SIDEBAR LEFT SECTION -->
                    <div class="lg:h-[70vh] h-[12vh] lg:w-1/2 w-full flex justify-center items-center">
                      <h1 class="px-12 lg:text-7xl text-center text-2xl lg:text-right font-medium lg:mt-[-100px] text-white">Track Your <span class="text-blue-400">Progress</span>, Shape Your <span class="text-blue-400">Future!</span></h1>
                    </div>
                    <!-- END OF SIDEBAR LEFT SECTION -->

                     <!-- SIDEBAR RIGHT SECTION -->
                     <div class=" lg:h-[70vh] h-[50vh] lg:w-1/2 w-full">
                      <form class="space-y-4 mx-10" action="../fn/student_login.php" method="post">
                        <h1 class="lg:text-3xl font-bold text-white">
                          Sign in to your account
                         </h1>
                        <div>
                            <label  class="block mb-4 text-sm font-medium text-white ">Your LRN</label>
                            <input type="text" name="lrn" placeholder="Enter LRN" id="email" class="bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-1" >
                        </div>
                        <div>
                            <label  class="block mb-1 text-sm font-medium text-white ">Password</label>
                            <input type="password" name="password" placeholder="Enter password" id="password"  class="bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-1">
                        </div>
                        <div class="flex items-center justify-between">
                            <div class="flex items-start">
                                <div class="flex items-center h-5">
                                  <input id="remember" aria-describedby="remember" type="checkbox" class="w-4 h-4 border border-gray-300 rounded bg-gray-50">
                                </div>
                                <div class="ml-3 text-sm">
                                  <label for="remember" class="text-white">Remember me</label>
                                </div>
                            </div>
                            <a href="#" class="text-sm font-medium text-white hover:underline ">Forgot password?</a>
                        </div>
                        <button class="w-full text-white bg-blue-950 hover:bg-blue-900 font-medium rounded-lg text-sm px-5 py-2.5 text-center"><a href="home.html">Login</a></button>
                        <p class="text-sm font-light text-white">
                            Don’t have an account? <a href="student_register.php" class="font-medium text-primary-600 hover:underline">Register here</a>
                        </p>
                    </form>
                     </div>
                     <!-- END OF SIDEBAR RIGHT SECTION -->
                    
                  <div>                 
                </section>

                
                
              </div>
          
            </div>
             <!-- END OF A CONTAINER WITH BORDER -->
          </div>
          <!-- END OF A SUB CONTAINER INSIDE OF THE MAIN CONTAINER -->
        </div>
        <!-- END OFTHE WHOLE CONTAINER -->
      </section>
</body>
</html>
