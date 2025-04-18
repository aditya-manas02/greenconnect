<?php
session_start();
require_once 'config/database.php';

// Fetch unique values for filters
$filterStmt = $pdo->query("
    SELECT DISTINCT 
        event_type,
        state,
        district,
        funding_source,
        recurrence_type,
        status
    FROM events
    ORDER BY state, district, event_type
");
$filters = $filterStmt->fetchAll(PDO::FETCH_ASSOC);

// Process filters
$where_conditions = [];
$params = [];

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (!empty($_GET['event_type'])) {
        $where_conditions[] = "e.event_type = ?";
        $params[] = $_GET['event_type'];
    }
    if (!empty($_GET['state'])) {
        $where_conditions[] = "e.state = ?";
        $params[] = $_GET['state'];
    }
    if (!empty($_GET['district'])) {
        $where_conditions[] = "e.district = ?";
        $params[] = $_GET['district'];
    }
    if (!empty($_GET['funding_source'])) {
        $where_conditions[] = "e.funding_source = ?";
        $params[] = $_GET['funding_source'];
    }
    if (!empty($_GET['recurrence_type'])) {
        $where_conditions[] = "e.recurrence_type = ?";
        $params[] = $_GET['recurrence_type'];
    }
    if (!empty($_GET['status'])) {
        $where_conditions[] = "e.status = ?";
        $params[] = $_GET['status'];
    }
    if (!empty($_GET['tree_types'])) {
        $where_conditions[] = "e.tree_types LIKE ?";
        $params[] = "%" . $_GET['tree_types'] . "%";
    }
}

// Build the query with filters
$query = "
    SELECT e.*, u.username, u.user_type, u.org_name, u.community_name 
    FROM events e 
    JOIN users u ON e.organizer_id = u.id 
";

if (!empty($where_conditions)) {
    $query .= " WHERE " . implode(" AND ", $where_conditions);
}

$query .= " ORDER BY e.start_datetime ASC";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$events = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get unique values for each filter
$states = array_unique(array_column($filters, 'state'));
$districts = array_unique(array_column($filters, 'district'));
$event_types = array_unique(array_column($filters, 'event_type'));
$funding_sources = array_unique(array_column($filters, 'funding_source'));
$recurrence_types = array_unique(array_column($filters, 'recurrence_type'));
$statuses = array_unique(array_column($filters, 'status'));
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GreenConnect - Events</title>
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
        .hero-gradient {
        background: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5));
        }
        .transition-all {
        transition: all 0.3s ease;
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
            <a href="events.php" class="text-green-700 border-b-2 border-green-700 pb-1 w-full sm:w-auto text-center">Events</a>
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

    <main class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
        <!-- Filter Section -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <h2 class="text-xl font-semibold mb-4">Filter Events</h2>
            <form method="GET" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Event Type</label>
                    <select name="event_type" class="w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                        <option value="">All Types</option>
                        <?php foreach ($event_types as $type): ?>
                            <option value="<?php echo htmlspecialchars($type); ?>" <?php echo isset($_GET['event_type']) && $_GET['event_type'] === $type ? 'selected' : ''; ?>>
                                <?php echo ucwords(str_replace('-', ' ', $type)); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">State</label>
                    <select name="state" class="w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                        <option value="">All States</option>
                        <?php foreach ($states as $state): ?>
                            <option value="<?php echo htmlspecialchars($state); ?>" <?php echo isset($_GET['state']) && $_GET['state'] === $state ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($state); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">District</label>
                    <select name="district" class="w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                        <option value="">All Districts</option>
                        <?php foreach ($districts as $district): ?>
                            <option value="<?php echo htmlspecialchars($district); ?>" <?php echo isset($_GET['district']) && $_GET['district'] === $district ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($district); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Funding Source</label>
                    <select name="funding_source" class="w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                        <option value="">All Sources</option>
                        <?php foreach ($funding_sources as $source): ?>
                            <option value="<?php echo htmlspecialchars($source); ?>" <?php echo isset($_GET['funding_source']) && $_GET['funding_source'] === $source ? 'selected' : ''; ?>>
                                <?php echo ucwords(str_replace('-', ' ', $source)); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Recurrence</label>
                    <select name="recurrence_type" class="w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                        <option value="">All Recurrence Types</option>
                        <?php foreach ($recurrence_types as $type): ?>
                            <option value="<?php echo htmlspecialchars($type); ?>" <?php echo isset($_GET['recurrence_type']) && $_GET['recurrence_type'] === $type ? 'selected' : ''; ?>>
                                <?php echo ucwords(str_replace('-', ' ', $type)); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select name="status" class="w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                        <option value="">All Statuses</option>
                        <?php foreach ($statuses as $status): ?>
                            <option value="<?php echo htmlspecialchars($status); ?>" <?php echo isset($_GET['status']) && $_GET['status'] === $status ? 'selected' : ''; ?>>
                                <?php echo ucwords($status); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="lg:col-span-3 flex justify-end space-x-4">
                    <a href="events.php" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-md text-sm font-medium transition-all shadow-md">Clear Filters</a>
                    <button type="submit" class="bg-green-700 hover:bg-green-800 text-white px-4 py-2 rounded-md text-sm font-medium transition-all shadow-md">Apply Filters</button>
                </div>
            </form>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <?php foreach ($events as $event): ?>
                <div class="bg-white rounded-lg shadow-lg overflow-hidden card-hover">
                    <a href="event-detail.php?id=<?php echo $event['id']; ?>" class="block">
                        <!-- Event Image -->
                        <div class="relative h-48 bg-gray-200">
                            <?php if ($event['event_banner_url']): ?>
                                <img src="<?php echo htmlspecialchars($event['event_banner_url']); ?>" 
                                     alt="<?php echo htmlspecialchars($event['title']); ?>" 
                                     class="w-full h-full object-cover">
                            <?php else: ?>
                                <div class="w-full h-full flex items-center justify-center bg-green-50">
                                    <i class="fas fa-tree text-4xl text-green-700"></i>
                                </div>
                            <?php endif; ?>
                            
                            <!-- Organizer Type Tag -->
                            <?php
                            $tagClass = '';
                            switch ($event['user_type']) {
                                case 'organization':
                                    $tagClass = 'bg-green-100 text-green-800';
                                    $tagText = 'Organization';
                                    break;
                                case 'community_leader':
                                    $tagClass = 'bg-red-100 text-red-800';
                                    $tagText = 'Community Leader';
                                    break;
                                default:
                                    $tagClass = 'bg-blue-100 text-blue-800';
                                    $tagText = 'Individual';
                            }
                            ?>
                            <span class="absolute top-4 right-4 px-3 py-1 rounded-full text-xs font-semibold <?php echo $tagClass; ?>">
                                <?php echo $tagText; ?>
                            </span>
                        </div>

                        <div class="p-6">
                            <h3 class="text-xl font-semibold text-gray-900 mb-2"><?php echo htmlspecialchars($event['title']); ?></h3>
                            
                            <!-- Organizer Info -->
                            <p class="text-sm text-gray-600 mb-4">
                                <i class="fas fa-user mr-2"></i>
                                <?php
                                if ($event['user_type'] === 'organization') {
                                    echo htmlspecialchars($event['org_name']);
                                } elseif ($event['user_type'] === 'community_leader') {
                                    echo htmlspecialchars($event['community_name']);
                                } else {
                                    echo htmlspecialchars($event['username']);
                                }
                                ?>
                            </p>

                            <!-- Date and Location -->
                            <div class="space-y-2 mb-4">
                                <p class="text-sm text-gray-600 flex items-center">
                                    <i class="far fa-calendar mr-2"></i>
                                    <?php echo date('F j, Y, g:i a', strtotime($event['start_datetime'])); ?>
                                </p>
                                <p class="text-sm text-gray-600 flex items-center">
                                    <i class="fas fa-map-marker-alt mr-2"></i>
                                    <?php echo htmlspecialchars($event['city'] . ', ' . $event['state']); ?>
                                </p>
                            </div>

                            <!-- Tree Info -->
                            <div class="flex justify-between items-center text-sm text-gray-600 mb-4">
                                <span>
                                    <i class="fas fa-tree mr-1"></i>
                                    <?php echo number_format($event['tree_count']); ?> Trees
                                </span>
                                <?php if ($event['needs_volunteers']): ?>
                                    <span>
                                        <i class="fas fa-users mr-1"></i>
                                        <?php echo number_format($event['volunteer_count']); ?> Volunteers Needed
                                    </span>
                                <?php endif; ?>
                            </div>

                            <!-- Join Button -->
                            <button class="w-full bg-green-700 hover:bg-green-800 text-white py-2 px-4 rounded-md text-sm font-medium transition-colors">
                                Join Event
                            </button>
                        </div>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    </main>
</body>
</html> 
