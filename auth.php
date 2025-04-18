<?php
session_start();
require_once 'config/database.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action'])) {
        if ($_POST['action'] == 'register') {
            $username = trim($_POST['username']);
            $email = trim($_POST['email']);
            $password = $_POST['password'];
            $confirm_password = $_POST['confirm_password'];
            $user_type = $_POST['user_type'];
            $org_name = null;
            $community_name = null;
            $location = null;

            if ($user_type === 'organization') {
                $org_name = trim($_POST['org_name']);
                $location = trim($_POST['location']);
            } elseif ($user_type === 'community_leader') {
                $community_name = trim($_POST['community_name']);
                $location = trim($_POST['location']);
            }

            if ($password !== $confirm_password) {
                $error = "Passwords do not match!";
            } else {
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                
                try {
                    $stmt = $pdo->prepare("INSERT INTO users (username, email, password, user_type, org_name, community_name, location) VALUES (?, ?, ?, ?, ?, ?, ?)");
                    $stmt->execute([$username, $email, $hashed_password, $user_type, $org_name, $community_name, $location]);
                    $success = "Registration successful! Please login.";
                } catch(PDOException $e) {
                    if ($e->getCode() == 23000) {
                        $error = "Username or email already exists!";
                    } else {
                        $error = "Registration failed. Please try again.";
                    }
                }
            }
        } elseif ($_POST['action'] == 'login') {
            $email = trim($_POST['email']);
            $password = $_POST['password'];

            try {
                $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
                $stmt->execute([$email]);
                $user = $stmt->fetch();

                if ($user && password_verify($password, $user['password'])) {
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['username'] = $user['username'];
                    header("Location: index.php");
                    exit();
                } else {
                    $error = "Invalid email or password!";
                }
            } catch(PDOException $e) {
                $error = "Login failed. Please try again.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GreenConnect - Authentication</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/js/all.min.js"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');
        .auth-container {
            background: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url('pc5.jpg');
            background-size: cover;
            background-position: center;
        }
        .form-container {
            backdrop-filter: blur(10px);
            background-color: rgba(255, 255, 255, 0.95);
        }
        body {
        font-family: 'Poppins', sans-serif;
        scroll-behavior: smooth;
        }
        .hero-gradient {
        background: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5));
        }
        .transition-all {
        transition: all 0.3s ease;
        }
        .card-hover:hover {
        transform: translateY(-5px);
        }
    </style>
</head>
<body class="bg-white text-gray-800 font-sans">

  <!-- Navbar -->
  <header class="bg-white shadow-md sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
      <div class="flex justify-between items-center">
        <!-- Logo -->
        <a href="index.html" class="text-2xl font-bold text-green-700 flex items-center order-1">
          <span class="text-green-700 mr-2"><i class="fas fa-leaf"></i></span>
          GreenConnect
        </a>
        
        <!-- Mobile Menu Button -->
        <button id="mobile-menu-button" class="sm:hidden focus:outline-none order-3">
          <svg class="w-6 h-6 text-green-700" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
          </svg>
        </button>
        
        <!-- Navigation Menu -->
        <div id="mobile-menu" class="hidden sm:flex flex-col sm:flex-row items-center justify-between w-full sm:w-auto order-4 sm:order-2 mt-4 sm:mt-0 absolute sm:relative top-20 sm:top-0 left-0 sm:left-auto bg-white sm:bg-transparent w-full sm:w-auto px-4 sm:px-0 pb-4 sm:pb-0 shadow-lg sm:shadow-none">
          <nav class="flex flex-col sm:flex-row items-center space-y-3 sm:space-y-0 sm:space-x-8 text-sm font-medium w-full sm:w-auto">
            <a href="index.php" class="text-gray-700 hover:text-green-700 hover:border-b-2 hover:border-green-700 pb-1 w-full sm:w-auto text-center">Home</a>
            <a href="dashboard.php" class="text-gray-700 hover:text-green-700 hover:border-b-2 hover:border-green-700 pb-1 w-full sm:w-auto text-center">Dashboard</a>
            <a href="events.php" class="text-gray-700 hover:text-green-700 hover:border-b-2 hover:border-green-700 pb-1 w-full sm:w-auto text-center">Events</a>
            <a href="propose-event.php" class="text-gray-700 hover:text-green-700 hover:border-b-2 hover:border-green-700 pb-1 w-full sm:w-auto text-center">Propose Event</a>
            <a href="contact.php" class="text-gray-700 hover:text-green-700 hover:border-b-2 hover:border-green-700 pb-1 w-full sm:w-auto text-center">Contact</a>
          </nav>
        </div>

        <!-- Auth Buttons -->
        <div class="flex items-center space-x-4 order-2 sm:order-3">
          <?php if(isset($_SESSION['user_id'])): ?>
            <a href="logout.php" class="text-sm font-medium text-gray-600 hover:text-green-700 transition-all">Logout</a>
          <?php else: ?>
            <a href="auth.php" class="text-sm font-medium text-gray-600 hover:text-green-700 transition-all hidden sm:inline-block">Login</a>
            <a href="auth.php" class="bg-green-700 hover:bg-green-800 text-white px-4 py-2 rounded-md text-sm font-medium transition-all shadow-md">Register</a>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </header>

    <div class="auth-container min-h-screen flex items-center justify-center p-4">
        <div class="form-container w-full max-w-md p-8 rounded-lg shadow-2xl">
            <div class="text-center mb-8">
                <a href="index.php" class="text-2xl font-bold text-green-700 flex items-center justify-center">
                    <span class="text-green-700 mr-2"><i class="fas fa-leaf"></i></span>
                    GreenConnect
                </a>
                <h2 class="mt-4 text-xl font-medium text-gray-900">Sign in to your account</h2>
            </div>

            <?php if ($error): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <span class="block sm:inline"><?php echo $error; ?></span>
                </div>
            <?php endif; ?>
            <?php if ($success): ?>
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <span class="block sm:inline"><?php echo $success; ?></span>
                </div>
            <?php endif; ?>

            <!-- Login Form -->
            <form id="loginForm" class="space-y-6" method="POST" action="">
                <input type="hidden" name="action" value="login">
                <div>
                    <label for="login-email" class="block text-sm font-medium text-gray-700">Username</label>
                    <input id="login-email" name="email" type="email" required class="mt-1 block w-full px-3 py-2 bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-green-500 focus:border-green-500" placeholder="admin@gmail.com">
                </div>
                <div>
                    <label for="login-password" class="block text-sm font-medium text-gray-700">Password</label>
                    <input id="login-password" name="password" type="password" required class="mt-1 block w-full px-3 py-2 bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-green-500 focus:border-green-500" placeholder="••••••••">
                </div>
                <div>
                    <button type="submit" class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-700 hover:bg-green-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                        Login
                    </button>
                </div>
            </form>

            <!-- Register Form -->
            <form id="registerForm" class="space-y-6 hidden" method="POST" action="">
                <input type="hidden" name="action" value="register">
                <div>
                    <label for="register-username" class="block text-sm font-medium text-gray-700">Username</label>
                    <input id="register-username" name="username" type="text" required class="mt-1 block w-full px-3 py-2 bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-green-500 focus:border-green-500" placeholder="Choose a username">
                </div>
                <div>
                    <label for="register-email" class="block text-sm font-medium text-gray-700">Email address</label>
                    <input id="register-email" name="email" type="email" required class="mt-1 block w-full px-3 py-2 bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-green-500 focus:border-green-500" placeholder="Enter your email">
                </div>
                <div>
                    <label for="user-type" class="block text-sm font-medium text-gray-700">Register as</label>
                    <select id="user-type" name="user_type" required class="mt-1 block w-full px-3 py-2 bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-green-500 focus:border-green-500">
                        <option value="user">Individual User</option>
                        <option value="organization">Organization</option>
                        <option value="community_leader">Community Leader</option>
                    </select>
                </div>

                <!-- Organization Fields -->
                <div id="org-fields" class="hidden space-y-6">
                    <div>
                        <label for="org-name" class="block text-sm font-medium text-gray-700">Organization Name</label>
                        <input id="org-name" name="org_name" type="text" class="mt-1 block w-full px-3 py-2 bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-green-500 focus:border-green-500" placeholder="Enter organization name">
                    </div>
                    <div>
                        <label for="org-location" class="block text-sm font-medium text-gray-700">Location</label>
                        <input id="org-location" name="location" type="text" class="mt-1 block w-full px-3 py-2 bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-green-500 focus:border-green-500" placeholder="Enter location">
                    </div>
                </div>

                <!-- Community Leader Fields -->
                <div id="community-fields" class="hidden space-y-6">
                    <div>
                        <label for="community-name" class="block text-sm font-medium text-gray-700">Community Name</label>
                        <input id="community-name" name="community_name" type="text" class="mt-1 block w-full px-3 py-2 bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-green-500 focus:border-green-500" placeholder="Enter community name">
                    </div>
                    <div>
                        <label for="community-location" class="block text-sm font-medium text-gray-700">Location</label>
                        <input id="community-location" name="location" type="text" class="mt-1 block w-full px-3 py-2 bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-green-500 focus:border-green-500" placeholder="Enter location">
                    </div>
                </div>

                <div>
                    <label for="register-password" class="block text-sm font-medium text-gray-700">Password</label>
                    <input id="register-password" name="password" type="password" required class="mt-1 block w-full px-3 py-2 bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-green-500 focus:border-green-500" placeholder="Create a password">
                </div>
                <div>
                    <label for="confirm-password" class="block text-sm font-medium text-gray-700">Confirm Password</label>
                    <input id="confirm-password" name="confirm_password" type="password" required class="mt-1 block w-full px-3 py-2 bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-green-500 focus:border-green-500" placeholder="Confirm your password">
                </div>
                <div>
                    <button type="submit" class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-700 hover:bg-green-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                        Create Account
                    </button>
                </div>
            </form>

            <!-- Toggle Link -->
            <div class="mt-4 text-center text-sm">
                <span id="toggleText" class="text-gray-600">New User?</span>
                <button type="button" id="toggleAuth" class="ml-1 text-green-700 hover:text-green-800 font-medium">
                    Register Here
                </button>
            </div>

            <div class="mt-6 text-center text-xs text-gray-500">
                © 2025 GreenConnect. All rights reserved.
            </div>
        </div>
    </div>

    <script>
        // Mobile menu toggle
        const menuButton = document.getElementById('mobile-menu-button');
        const mobileMenu = document.getElementById('mobile-menu');
        
        menuButton.addEventListener('click', () => {
            if (mobileMenu.classList.contains('hidden')) {
                mobileMenu.classList.remove('hidden');
            } else {
                mobileMenu.classList.add('hidden');
            }
        });

        // Handle responsive menu on window resize
        window.addEventListener('resize', () => {
            if (window.innerWidth >= 640) { // sm breakpoint
                mobileMenu.classList.remove('hidden');
            } else if (!menuButton.classList.contains('active')) {
                mobileMenu.classList.add('hidden');
            }
        });

        // Form toggle functionality
        const loginForm = document.getElementById('loginForm');
        const registerForm = document.getElementById('registerForm');
        const toggleAuth = document.getElementById('toggleAuth');
        const toggleText = document.getElementById('toggleText');
        const title = document.querySelector('h2');

        toggleAuth.addEventListener('click', () => {
            loginForm.classList.toggle('hidden');
            registerForm.classList.toggle('hidden');
            
            if (loginForm.classList.contains('hidden')) {
                toggleAuth.textContent = 'Login Here';
                toggleText.textContent = 'Already have an account?';
                title.textContent = 'Create a new account';
            } else {
                toggleAuth.textContent = 'Register Here';
                toggleText.textContent = 'New User?';
                title.textContent = 'Sign in to your account';
            }
        });

        // Handle user type selection
        const userType = document.getElementById('user-type');
        const orgFields = document.getElementById('org-fields');
        const communityFields = document.getElementById('community-fields');
        const orgName = document.getElementById('org-name');
        const communityName = document.getElementById('community-name');
        const orgLocation = document.getElementById('org-location');
        const communityLocation = document.getElementById('community-location');

        userType.addEventListener('change', () => {
            // Hide all fields first
            orgFields.classList.add('hidden');
            communityFields.classList.add('hidden');
            
            // Remove required attribute from all fields
            orgName.required = false;
            communityName.required = false;
            orgLocation.required = false;
            communityLocation.required = false;

            // Show relevant fields based on selection
            if (userType.value === 'organization') {
                orgFields.classList.remove('hidden');
                orgName.required = true;
                orgLocation.required = true;
            } else if (userType.value === 'community_leader') {
                communityFields.classList.remove('hidden');
                communityName.required = true;
                communityLocation.required = true;
            }
        });
    </script>
</body>
</html> 