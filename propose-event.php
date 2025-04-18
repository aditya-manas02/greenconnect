<?php
session_start();
require_once 'config/database.php';

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: auth.php");
    exit();
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Handle file upload first
    $banner_url = null;
    if (isset($_FILES['event_banner']) && $_FILES['event_banner']['error'] == 0) {
        $target_dir = "uploads/event_banners/";
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        $file_extension = strtolower(pathinfo($_FILES["event_banner"]["name"], PATHINFO_EXTENSION));
        $file_name = uniqid() . '.' . $file_extension;
        $target_file = $target_dir . $file_name;
        
        if (move_uploaded_file($_FILES["event_banner"]["tmp_name"], $target_file)) {
            $banner_url = $target_file;
        }
    }

    try {
        $stmt = $pdo->prepare("INSERT INTO events (
            organizer_id, title, contact_email, contact_phone, start_datetime, 
            end_datetime, address, city, district, state, tree_types, 
            tree_count, needs_volunteers, volunteer_count, description, 
            event_banner_url, funding_source, partner_organizations, 
            event_type, expected_impact, has_permission, recurrence_type
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

        $needs_volunteers = isset($_POST['needs_volunteers']) ? 1 : 0;
        $volunteer_count = $needs_volunteers ? $_POST['volunteer_count'] : null;
        $has_permission = isset($_POST['has_permission']) ? 1 : 0;

        $stmt->execute([
            $_SESSION['user_id'],
            $_POST['title'],
            $_POST['contact_email'],
            $_POST['contact_phone'],
            $_POST['start_datetime'],
            !empty($_POST['end_datetime']) ? $_POST['end_datetime'] : null,
            $_POST['address'],
            $_POST['city'],
            $_POST['district'],
            $_POST['state'],
            $_POST['tree_types'],
            $_POST['tree_count'],
            $needs_volunteers,
            $volunteer_count,
            $_POST['description'],
            $banner_url,
            $_POST['funding_source'],
            $_POST['partner_organizations'],
            $_POST['event_type'],
            $_POST['expected_impact'],
            $has_permission,
            $_POST['recurrence_type']
        ]);

        $success = "Event proposed successfully! It will be reviewed by our team.";
    } catch(PDOException $e) {
        $error = "Failed to propose event. Please try again.";
    }
}
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
            <a href="propose-event.php" class="text-green-700 border-b-2 border-green-700 pb-1 w-full sm:w-auto text-center">Propose Event</a>
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


    <main class="max-w-4xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
        <div class="bg-white rounded-lg shadow-lg p-6 md:p-8">
            <h1 class="text-2xl font-bold text-gray-900 mb-6">Propose a Tree Planting Event</h1>

            <?php if ($error): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">
                    <?php echo $success; ?>
                </div>
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data" class="space-y-6">
                <!-- Basic Information -->
                <div class="space-y-4">
                    <h2 class="text-lg font-semibold text-gray-900">Basic Information</h2>
                    
                    <div>
                        <label for="title" class="block text-sm font-medium text-gray-700">Event Title</label>
                        <input type="text" id="title" name="title" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500" placeholder="e.g., Miyawaki Forest Drive in Sector 45">
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="contact_email" class="block text-sm font-medium text-gray-700">Contact Email</label>
                            <input type="email" id="contact_email" name="contact_email" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
                        </div>
                        <div>
                            <label for="contact_phone" class="block text-sm font-medium text-gray-700">Contact Phone</label>
                            <input type="tel" id="contact_phone" name="contact_phone" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
                        </div>
                    </div>
                </div>

                <!-- Date and Time -->
                <div class="space-y-4">
                    <h2 class="text-lg font-semibold text-gray-900">Date and Time</h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="start_datetime" class="block text-sm font-medium text-gray-700">Start Date & Time</label>
                            <input type="datetime-local" id="start_datetime" name="start_datetime" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
                        </div>
                        <div>
                            <label for="end_datetime" class="block text-sm font-medium text-gray-700">End Date & Time (Optional)</label>
                            <input type="datetime-local" id="end_datetime" name="end_datetime" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
                        </div>
                    </div>
                </div>

                <!-- Location -->
                <div class="space-y-4">
                    <h2 class="text-lg font-semibold text-gray-900">Location</h2>
                    
                    <div>
                        <label for="address" class="block text-sm font-medium text-gray-700">Address</label>
                        <textarea id="address" name="address" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500" rows="2"></textarea>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label for="city" class="block text-sm font-medium text-gray-700">City</label>
                            <input type="text" id="city" name="city" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
                        </div>
                        <div>
                            <label for="district" class="block text-sm font-medium text-gray-700">District</label>
                            <input type="text" id="district" name="district" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
                        </div>
                        <div>
                            <label for="state" class="block text-sm font-medium text-gray-700">State</label>
                            <input type="text" id="state" name="state" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
                        </div>
                    </div>
                </div>

                <!-- Tree Information -->
                <div class="space-y-4">
                    <h2 class="text-lg font-semibold text-gray-900">Tree Information</h2>
                    
                    <div>
                        <label for="tree_types" class="block text-sm font-medium text-gray-700">Tree Types</label>
                        <textarea id="tree_types" name="tree_types" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500" rows="2" placeholder="List the types of trees to be planted"></textarea>
                    </div>

                    <div>
                        <label for="tree_count" class="block text-sm font-medium text-gray-700">Number of Trees</label>
                        <input type="number" id="tree_count" name="tree_count" required min="1" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
                    </div>
                </div>

                <!-- Volunteer Information -->
                <div class="space-y-4">
                    <h2 class="text-lg font-semibold text-gray-900">Volunteer Information</h2>
                    
                    <div class="flex items-center">
                        <input type="checkbox" id="needs_volunteers" name="needs_volunteers" class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300 rounded">
                        <label for="needs_volunteers" class="ml-2 block text-sm text-gray-700">Need Volunteers?</label>
                    </div>

                    <div id="volunteer_count_container" class="hidden">
                        <label for="volunteer_count" class="block text-sm font-medium text-gray-700">Number of Volunteers Needed</label>
                        <input type="number" id="volunteer_count" name="volunteer_count" min="1" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
                    </div>
                </div>

                <!-- Event Details -->
                <div class="space-y-4">
                    <h2 class="text-lg font-semibold text-gray-900">Event Details</h2>
                    
                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                        <textarea id="description" name="description" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500" rows="4" placeholder="Purpose, goals, and important instructions"></textarea>
                    </div>

                    <div>
                        <label for="event_banner" class="block text-sm font-medium text-gray-700">Event Banner</label>
                        <input type="file" id="event_banner" name="event_banner" accept="image/jpeg,image/png" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-medium file:bg-green-50 file:text-green-700 hover:file:bg-green-100">
                    </div>

                    <div>
                        <label for="funding_source" class="block text-sm font-medium text-gray-700">Tree Sourcing</label>
                        <select id="funding_source" name="funding_source" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
                            <option value="self-funded">Self-funded</option>
                            <option value="ngo-backed">NGO-backed</option>
                            <option value="govt-backed">Government-backed</option>
                            <option value="sponsored">Sponsored</option>
                        </select>
                    </div>

                    <div>
                        <label for="partner_organizations" class="block text-sm font-medium text-gray-700">Partner Organizations</label>
                        <textarea id="partner_organizations" name="partner_organizations" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500" rows="2" placeholder="List any co-organizers or sponsors"></textarea>
                    </div>

                    <div>
                        <label for="event_type" class="block text-sm font-medium text-gray-700">Event Type</label>
                        <select id="event_type" name="event_type" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
                            <option value="school-drive">School Drive</option>
                            <option value="community-planting">Community Planting</option>
                            <option value="csr">Corporate Social Responsibility (CSR)</option>
                            <option value="urban-forestry">Urban Forestry</option>
                            <option value="other">Other</option>
                        </select>
                    </div>

                    <div>
                        <label for="expected_impact" class="block text-sm font-medium text-gray-700">Expected Impact</label>
                        <textarea id="expected_impact" name="expected_impact" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500" rows="2" placeholder="e.g., Carbon sequestration of ~10 tons/year, Improving biodiversity"></textarea>
                    </div>

                    <div>
                        <label for="recurrence_type" class="block text-sm font-medium text-gray-700">Recurring Event?</label>
                        <select id="recurrence_type" name="recurrence_type" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
                            <option value="one-time">One-time</option>
                            <option value="weekly">Weekly</option>
                            <option value="monthly">Monthly</option>
                        </select>
                    </div>

                    <div class="flex items-center">
                        <input type="checkbox" id="has_permission" name="has_permission" required class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300 rounded">
                        <label for="has_permission" class="ml-2 block text-sm text-gray-700">I confirm necessary permissions have been obtained</label>
                    </div>
                </div>

                <div class="pt-5">
                    <button type="submit" class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-700 hover:bg-green-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                        Propose Event
                    </button>
                </div>
            </form>
        </div>
    </main>

    <script>
        // Handle volunteer checkbox
        const needsVolunteers = document.getElementById('needs_volunteers');
        const volunteerCountContainer = document.getElementById('volunteer_count_container');
        const volunteerCount = document.getElementById('volunteer_count');

        needsVolunteers.addEventListener('change', () => {
            volunteerCountContainer.classList.toggle('hidden', !needsVolunteers.checked);
            volunteerCount.required = needsVolunteers.checked;
        });
    </script>
</body>
</html> 