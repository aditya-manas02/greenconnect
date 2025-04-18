<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>GreenConnect - Home</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/js/all.min.js"></script>
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');
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
        <a href="index.php" class="flex items-center">
          <span class="text-green-700 mr-2"><i class="fas fa-leaf"></i></span>
          <span class="text-xl font-bold text-green-700">GreenConnect</span>
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
            <a href="index.php" class="text-green-700 border-b-2 border-green-700 pb-1 w-full sm:w-auto text-center">Home</a>
            <a href="dashboard.php" class="text-gray-700 hover:text-green-700 hover:border-b-2 hover:border-green-700 pb-1 w-full sm:w-auto text-center">Dashboard</a>
            <a href="events.php" class="text-gray-700 hover:text-green-700 hover:border-b-2 hover:border-green-700 pb-1 w-full sm:w-auto text-center">Events</a>
            <a href="propose-event.php" class="text-gray-700 hover:text-green-700 hover:border-b-2 hover:border-green-700 pb-1 w-full sm:w-auto text-center">Propose Event</a>
            <a href="contact.php" class="text-gray-700 hover:text-green-700 hover:border-b-2 hover:border-green-700 pb-1 w-full sm:w-auto text-center">Contact</a>
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

  <!-- Hero Section -->
  <section class="relative">
    <img src="pc2.jpg" alt="Nature" class="w-full h-[500px] object-cover" />
    <div class="absolute inset-0 hero-gradient flex flex-col justify-center items-center text-white px-6 text-center">
      <h1 class="text-3xl sm:text-4xl md:text-5xl font-bold mb-4 drop-shadow-lg">Reimagine Tree Plantation</h1>
      <p class="text-base sm:text-lg max-w-xl drop-shadow-md">Empowering communities and leaders to restore nature and promote sustainability through shared tree-planting efforts.</p>
      <br>
      <button onclick="handleGetInvolved()" class="bg-transparent border-2 border-white text-white font-semibold px-6 py-3 rounded-md hover:bg-white hover:text-green-800 transition-all">Get Involved</button>
    </div>
  </section>

  <!-- About Section -->
  <section class="py-16 sm:py-20 bg-gray-100 px-4 sm:px-6 lg:px-20">
    <div class="text-center mb-12">
      <span class="inline-block bg-green-100 text-green-800 px-3 py-1 rounded-full text-sm font-medium mb-2">Our Mission</span>
      <h2 class="text-2xl sm:text-3xl font-bold text-green-800">Why GreenConnect?</h2>
      <div class="h-1 w-20 bg-green-700 mx-auto mt-2 mb-4 rounded-full"></div>
      <p class="mt-4 max-w-2xl mx-auto text-gray-600">We connect citizens, environmental organizations, and leaders to organize tree plantation events and build a sustainable future.</p>
    </div>
    <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6 sm:gap-8">
      <div class="bg-white shadow-md rounded-lg p-6 text-center transition-all card-hover">
        <div class="mb-4 text-green-700 text-4xl flex justify-center">
          <i class="fas fa-seedling"></i>
        </div>
        <h3 class="text-xl font-semibold mb-2">Find Events</h3>
        <p class="text-gray-600">Explore tree-planting drives near you filtered by location, date, and tree type.</p>
        <a href="events.php" class="inline-block mt-4 text-green-700 hover:text-green-800 font-medium">Explore →</a>
      </div>
      <div class="bg-white shadow-md rounded-lg p-6 text-center transition-all card-hover">
        <div class="mb-4 text-green-700 text-4xl flex justify-center">
          <i class="far fa-calendar-alt"></i>
        </div>
        <h3 class="text-xl font-semibold mb-2">Propose an Event</h3>
        <p class="text-gray-600">Organize local events and invite others to join your green mission.</p>
        <a href="propose-event.php" class="inline-block mt-4 text-green-700 hover:text-green-800 font-medium">Propose →</a>
      </div>
      <div class="bg-white shadow-md rounded-lg p-6 text-center transition-all card-hover">
        <div class="mb-4 text-green-700 text-4xl flex justify-center">
          <i class="fas fa-hands-helping"></i>
        </div>
        <h3 class="text-xl font-semibold mb-2">Get in Touch</h3>
        <p class="text-gray-600">Have questions or suggestions? Reach out to our team for support and collaboration opportunities.</p>
        <a href="contact.php" class="inline-block mt-4 text-green-700 hover:text-green-800 font-medium">Contact →</a>
      </div>
    </div>
  </section>

  <!-- Impact Stats -->
  <section class="py-16 px-4 sm:px-6 lg:px-20">
    <div class="text-center mb-12">
      <h2 class="text-2xl sm:text-3xl font-bold text-green-800">Our Impact</h2>
      <div class="h-1 w-20 bg-green-700 mx-auto mt-2 mb-4 rounded-full"></div>
      <p class="mt-4 max-w-2xl mx-auto text-gray-600">Together we're making a measurable difference in our communities.</p>
    </div>
    <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
      <div class="text-center">
        <div class="text-4xl font-bold text-green-700 mb-2">1,200+</div>
        <p class="text-gray-600">Trees Planted</p>
      </div>
      <div class="text-center">
        <div class="text-4xl font-bold text-green-700 mb-2">45</div>
        <p class="text-gray-600">Events Organized</p>
      </div>
      <div class="text-center">
        <div class="text-4xl font-bold text-green-700 mb-2">260</div>
        <p class="text-gray-600">Active Members</p>
      </div>
      <div class="text-center">
        <div class="text-4xl font-bold text-green-700 mb-2">22</div>
        <p class="text-gray-600">Cities Covered</p>
      </div>
    </div>
  </section>

  <!-- Call to Action -->
  <section class="bg-green-700 text-white py-12 sm:py-16 px-4 text-center">
    <div class="max-w-4xl mx-auto">
      <h2 class="text-2xl sm:text-3xl font-bold mb-4">Ready to make a difference?</h2>
      <p class="text-base sm:text-lg mb-6 max-w-2xl mx-auto">Sign up now to join or organize tree plantation events in your community.</p>
      <div class="flex flex-col sm:flex-row justify-center gap-4">
        <button onclick="handleJoinClick()" class="bg-white text-green-800 font-semibold px-6 py-3 rounded-md hover:bg-gray-100 transition-all shadow-md">Join GreenConnect</button>
        <a href="events.php" class="bg-transparent border-2 border-white text-white font-semibold px-6 py-3 rounded-md hover:bg-white hover:text-green-800 transition-all">Browse Events</a>
      </div>
    </div>
  </section>

  <!-- Footer -->
  <footer class="bg-gray-100 py-10">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
        <div>
          <a href="index.html" class="text-xl font-bold text-green-700 flex items-center">
            <span class="text-green-700 mr-2"><i class="fas fa-leaf"></i></span>
            GreenConnect
          </a>
          <p class="mt-2 text-sm text-gray-600">Empowering communities to restore nature and promote sustainability.</p>
        </div>
        <div>
          <h3 class="text-sm font-semibold text-gray-800 uppercase tracking-wider">Navigation</h3>
          <ul class="mt-4 space-y-2">
            <li><a href="index.html" class="text-sm text-gray-600 hover:text-green-700">Home</a></li>
            <li><a href="dashboard.html" class="text-sm text-gray-600 hover:text-green-700">Dashboard</a></li>
            <li><a href="events.html" class="text-sm text-gray-600 hover:text-green-700">Events</a></li>
            <li><a href="event-detail.html" class="text-sm text-gray-600 hover:text-green-700">Event Detail</a></li>
          </ul>
        </div>
        <div>
          <h3 class="text-sm font-semibold text-gray-800 uppercase tracking-wider">Resources</h3>
          <ul class="mt-4 space-y-2">
            <li><a href="#" class="text-sm text-gray-600 hover:text-green-700">Blog</a></li>
            <li><a href="#" class="text-sm text-gray-600 hover:text-green-700">Guides</a></li>
            <li><a href="#" class="text-sm text-gray-600 hover:text-green-700">FAQ</a></li>
            <li><a href="#" class="text-sm text-gray-600 hover:text-green-700">Contact</a></li>
          </ul>
        </div>
        <div>
          <h3 class="text-sm font-semibold text-gray-800 uppercase tracking-wider">Connect</h3>
          <div class="mt-4 flex space-x-4">
            <a href="#" class="text-gray-600 hover:text-green-700">
              <i class="fab fa-facebook-f"></i>
            </a>
            <a href="#" class="text-gray-600 hover:text-green-700">
              <i class="fab fa-twitter"></i>
            </a>
            <a href="#" class="text-gray-600 hover:text-green-700">
              <i class="fab fa-instagram"></i>
            </a>
            <a href="#" class="text-gray-600 hover:text-green-700">
              <i class="fab fa-linkedin-in"></i>
            </a>
          </div>
          <div class="mt-4">
            <h4 class="text-sm font-medium text-gray-800">Subscribe to our newsletter</h4>
            <div class="mt-2 flex">
              <input type="email" placeholder="Your email" class="px-3 py-2 border border-gray-300 rounded-l-md w-full focus:outline-none focus:ring-1 focus:ring-green-700">
              <button class="bg-green-700 hover:bg-green-800 text-white px-4 py-2 rounded-r-md">
                <i class="fas fa-paper-plane"></i>
              </button>
            </div>
          </div>
        </div>
      </div>
      <div class="border-t border-gray-200 mt-8 pt-6 text-center text-sm text-gray-600">
        &copy; 2025 GreenConnect. All rights reserved.
      </div>
    </div>
  </footer>

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

    // Handle Join GreenConnect button click
    function handleJoinClick() {
      <?php if(isset($_SESSION['user_id'])): ?>
        alert('You are already logged in to GreenConnect!');
      <?php else: ?>
        window.location.href = 'auth.php';
      <?php endif; ?>
    }

    // Handle Get Involved button click
    function handleGetInvolved() {
      <?php if(isset($_SESSION['user_id'])): ?>
        alert('Welcome back! You are already part of GreenConnect. Check out our latest events!');
      <?php else: ?>
        window.location.href = 'auth.php';
      <?php endif; ?>
    }
  </script>
</body>
</html>