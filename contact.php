<?php
session_start();
require_once 'config/database.php';

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $subject = trim($_POST['subject']);
    $message = trim($_POST['message']);

    // Basic validation
    if (empty($name) || empty($email) || empty($subject) || empty($message)) {
        $error = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please enter a valid email address.";
    } else {
        try {
            $stmt = $pdo->prepare("INSERT INTO contact_messages (name, email, subject, message) VALUES (?, ?, ?, ?)");
            $stmt->execute([$name, $email, $subject, $message]);
            $success = "Thank you for your message. We'll get back to you soon!";
            
            // Clear form data after successful submission
            $_POST = array();
        } catch(PDOException $e) {
            $error = "Sorry, there was an error sending your message. Please try again.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GreenConnect - Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/js/all.min.js"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');
        body {
            font-family: 'Poppins', sans-serif;
        }
        .card-hover:hover {
            transform: translateY(-5px);
            transition: transform 0.2s ease-in-out;
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
            <a href="contact.php" class="text-green-700 border-b-2 border-green-700 pb-1 w-full sm:w-auto text-center">Contact</a>
          </nav>
        </div>

        <!-- Auth Buttons -->
        <div class="flex items-center space-x-4 order-2 sm:order-3">
          <?php if(isset($_SESSION['user_id'])): ?>
            <a href="logout.php" class="bg-green-700 hover:bg-green-800 text-white px-4 py-2 rounded-md text-sm font-medium transition-all shadow-md">Logout</a>
          <?php else: ?>
            <a href="auth.php" class="text-sm font-medium text-gray-600 hover:text-green-700 transition-all hidden sm:inline-block">Login</a>
            <a href="auth.php" class="bg-green-700 hover:bg-green-800 text-white px-4 py-2 rounded-md text-sm font-medium transition-all shadow-md">Register</a>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </header>

    <main class="max-w-4xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
        <div class="bg-white rounded-lg shadow-lg p-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-8">Contact Us</h1>
            
            <?php if ($success): ?>
                <div class="mb-8 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
                    <?php echo $success; ?>
                </div>
            <?php endif; ?>

            <?php if ($error): ?>
                <div class="mb-8 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-12">
                <!-- Contact Form -->
                <div>
                    <form method="POST" class="space-y-6">
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
                            <input type="text" id="name" name="name" value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                        </div>

                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                            <input type="email" id="email" name="email" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                        </div>

                        <div>
                            <label for="subject" class="block text-sm font-medium text-gray-700">Subject</label>
                            <input type="text" id="subject" name="subject" value="<?php echo isset($_POST['subject']) ? htmlspecialchars($_POST['subject']) : ''; ?>" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                        </div>

                        <div>
                            <label for="message" class="block text-sm font-medium text-gray-700">Message</label>
                            <textarea id="message" name="message" rows="4" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500"><?php echo isset($_POST['message']) ? htmlspecialchars($_POST['message']) : ''; ?></textarea>
                        </div>

                        <button type="submit" class="w-full bg-green-700 hover:bg-green-800 text-white py-2 px-4 rounded-md font-medium transition-colors">
                            Send Message
                        </button>
                    </form>
                </div>

                <!-- Contact Information -->
                <div class="space-y-6">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-3">Get in Touch</h3>
                        <p class="text-gray-600">Have questions about GreenConnect? We're here to help! Reach out to us using the contact form or through any of our channels below.</p>
                    </div>

                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-3">Contact Information</h3>
                        <div class="space-y-4">
                            <p class="flex items-center text-gray-600">
                                <i class="fas fa-envelope w-6 text-green-700 mr-1"></i>
                                 shanewillplay2006@gmail.com
                            </p>
                            <p class="flex items-center text-gray-600 mr-1">
                                <i class="fas fa-phone w-6 text-green-700 "></i>
                                +91 91354 80157
                            </p>
                            <p class="flex items-center text-gray-600 ">
                                <i class="fas fa-map-marker-alt w-6 text-green-700 mr-1"></i>
                                Lovely Professional University, Punjab, 144411
                            </p>
                        </div>
                    </div>

                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-3">Follow Us</h3>
                        <div class="flex space-x-4">
                            <a href="" class="text-gray-600 hover:text-green-700">
                                <i class="fab fa-facebook fa-lg"></i>
                            </a>
                            <a href="#" class="text-gray-600 hover:text-green-700">
                                <i class="fab fa-twitter fa-lg"></i>
                            </a>
                            <a href="https://www.instagram.com/_aditya_manas_?igsh=MTJna3Z5bjZ2NHN3Mw==" class="text-gray-600 hover:text-green-700">
                                <i class="fab fa-instagram fa-lg"></i>
                            </a>
                            <a href="https://www.linkedin.com/in/aditya-manas-29731a290/" class="text-gray-600 hover:text-green-700">
                                <i class="fab fa-linkedin fa-lg"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</body>
</html> 