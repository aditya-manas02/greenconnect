<?php
session_start();
require_once 'config/database.php';

// Get event ID from URL
$event_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$event_id) {
    header('Location: events.php');
    exit();
}

// Fetch event details with organizer information
$stmt = $pdo->prepare("
    SELECT e.*, u.username, u.user_type, u.org_name, u.community_name 
    FROM events e 
    JOIN users u ON e.organizer_id = u.id 
    WHERE e.id = ?
");
$stmt->execute([$event_id]);
$event = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$event) {
    header('Location: events.php');
    exit();
}

$error = '';
$success = '';
$isOrganizer = false;
$hasJoined = false;

if (isset($_SESSION['user_id'])) {
    // Check if user is the organizer
    $isOrganizer = ($_SESSION['user_id'] == $event['organizer_id']);
    
    // Check if user has already joined
    $stmt = $pdo->prepare("SELECT id FROM event_participants WHERE event_id = ? AND user_id = ?");
    $stmt->execute([$event_id, $_SESSION['user_id']]);
    $hasJoined = $stmt->fetch() !== false;

    // Handle join event submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$isOrganizer && !$hasJoined) {
        try {
            $stmt = $pdo->prepare("INSERT INTO event_participants (event_id, user_id) VALUES (?, ?)");
            $stmt->execute([$event_id, $_SESSION['user_id']]);
            $hasJoined = true;
            $success = "You have successfully joined this event!";
        } catch(PDOException $e) {
            if ($e->getCode() == 23000) { // Duplicate entry error
                $error = "You have already joined this event.";
            } else {
                $error = "Failed to join the event. Please try again.";
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
    <title>GreenConnect - <?php echo htmlspecialchars($event['title']); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/js/all.min.js"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');
        body {
            font-family: 'Poppins', sans-serif;
        }
    </style>
</head>
<body class="bg-gray-50">
    <main class="max-w-4xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
        <!-- Back Button -->
        <a href="events.php" class="inline-flex items-center text-green-700 hover:text-green-800 mb-8">
            <i class="fas fa-arrow-left mr-2"></i>
            Back to Events
        </a>

        <?php if ($error): ?>
            <div class="mb-8 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="mb-8 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
                <?php echo $success; ?>
            </div>
        <?php endif; ?>

        <div class="bg-white rounded-lg shadow-lg overflow-hidden">
            <!-- Event Banner -->
            <div class="relative h-64 bg-gray-200">
                <?php if ($event['event_banner_url']): ?>
                    <img src="<?php echo htmlspecialchars($event['event_banner_url']); ?>" 
                         alt="<?php echo htmlspecialchars($event['title']); ?>" 
                         class="w-full h-full object-cover">
                <?php else: ?>
                    <div class="w-full h-full flex items-center justify-center bg-green-50">
                        <i class="fas fa-tree text-6xl text-green-700"></i>
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
                <span class="absolute top-4 right-4 px-4 py-2 rounded-full text-sm font-semibold <?php echo $tagClass; ?>">
                    <?php echo $tagText; ?>
                </span>
            </div>

            <div class="p-8">
                <!-- Event Title and Organizer -->
                <div class="mb-8">
                    <h1 class="text-3xl font-bold text-gray-900 mb-4"><?php echo htmlspecialchars($event['title']); ?></h1>
                    <p class="text-lg text-gray-600">
                        <i class="fas fa-user mr-2"></i>
                        Organized by 
                        <span class="font-semibold">
                            <?php
                            if ($event['user_type'] === 'organization') {
                                echo htmlspecialchars($event['org_name']);
                            } elseif ($event['user_type'] === 'community_leader') {
                                echo htmlspecialchars($event['community_name']);
                            } else {
                                echo htmlspecialchars($event['username']);
                            }
                            ?>
                        </span>
                    </p>
                </div>

                <!-- Event Details Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
                    <!-- Date and Time -->
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900 mb-4">Date and Time</h2>
                        <div class="space-y-2">
                            <p class="flex items-center text-gray-600">
                                <i class="far fa-calendar mr-2 w-5"></i>
                                Start: <?php echo date('F j, Y', strtotime($event['start_datetime'])); ?>
                            </p>
                            <p class="flex items-center text-gray-600">
                                <i class="far fa-clock mr-2 w-5"></i>
                                Time: <?php echo date('g:i a', strtotime($event['start_datetime'])); ?>
                            </p>
                            <?php if ($event['end_datetime']): ?>
                                <p class="flex items-center text-gray-600">
                                    <i class="far fa-calendar-check mr-2 w-5"></i>
                                    End: <?php echo date('F j, Y, g:i a', strtotime($event['end_datetime'])); ?>
                                </p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Location -->
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900 mb-4">Location</h2>
                        <div class="space-y-2">
                            <p class="flex items-start text-gray-600">
                                <i class="fas fa-map-marker-alt mr-2 mt-1 w-5"></i>
                                <span>
                                    <?php echo htmlspecialchars($event['address']); ?><br>
                                    <?php echo htmlspecialchars($event['city'] . ', ' . $event['district']); ?><br>
                                    <?php echo htmlspecialchars($event['state']); ?>
                                </span>
                            </p>
                        </div>
                    </div>

                    <!-- Tree Information -->
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900 mb-4">Tree Information</h2>
                        <div class="space-y-2">
                            <p class="flex items-center text-gray-600">
                                <i class="fas fa-tree mr-2 w-5"></i>
                                <?php echo number_format($event['tree_count']); ?> Trees to be Planted
                            </p>
                            <p class="flex items-start text-gray-600">
                                <i class="fas fa-leaf mr-2 mt-1 w-5"></i>
                                <span><?php echo nl2br(htmlspecialchars($event['tree_types'])); ?></span>
                            </p>
                        </div>
                    </div>

                    <!-- Volunteer Information -->
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900 mb-4">Volunteer Information</h2>
                        <div class="space-y-2">
                            <?php if ($event['needs_volunteers']): ?>
                                <p class="flex items-center text-gray-600">
                                    <i class="fas fa-users mr-2 w-5"></i>
                                    <?php echo number_format($event['volunteer_count']); ?> Volunteers Needed
                                </p>
                            <?php else: ?>
                                <p class="flex items-center text-gray-600">
                                    <i class="fas fa-info-circle mr-2 w-5"></i>
                                    No volunteers needed for this event
                                </p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Description -->
                <div class="mb-8">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Description</h2>
                    <p class="text-gray-600 whitespace-pre-line"><?php echo htmlspecialchars($event['description']); ?></p>
                </div>

                <!-- Additional Details -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
                    <!-- Event Type and Impact -->
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900 mb-4">Event Details</h2>
                        <div class="space-y-2">
                            <p class="flex items-center text-gray-600">
                                <i class="fas fa-tag mr-2 w-5"></i>
                                Event Type: <?php echo htmlspecialchars(ucfirst(str_replace('-', ' ', $event['event_type']))); ?>
                            </p>
                            <p class="flex items-start text-gray-600">
                                <i class="fas fa-chart-line mr-2 mt-1 w-5"></i>
                                <span>
                                    Expected Impact:<br>
                                    <?php echo nl2br(htmlspecialchars($event['expected_impact'])); ?>
                                </span>
                            </p>
                        </div>
                    </div>

                    <!-- Funding and Partners -->
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900 mb-4">Support Information</h2>
                        <div class="space-y-2">
                            <p class="flex items-center text-gray-600">
                                <i class="fas fa-hand-holding-usd mr-2 w-5"></i>
                                Funding: <?php echo htmlspecialchars(ucfirst(str_replace('-', ' ', $event['funding_source']))); ?>
                            </p>
                            <?php if ($event['partner_organizations']): ?>
                                <p class="flex items-start text-gray-600">
                                    <i class="fas fa-handshake mr-2 mt-1 w-5"></i>
                                    <span>
                                        Partner Organizations:<br>
                                        <?php echo nl2br(htmlspecialchars($event['partner_organizations'])); ?>
                                    </span>
                                </p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Contact Information -->
                <div class="mb-8">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Contact Information</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <p class="flex items-center text-gray-600">
                            <i class="fas fa-envelope mr-2 w-5"></i>
                            <?php echo htmlspecialchars($event['contact_email']); ?>
                        </p>
                        <?php if ($event['contact_phone']): ?>
                            <p class="flex items-center text-gray-600">
                                <i class="fas fa-phone mr-2 w-5"></i>
                                <?php echo htmlspecialchars($event['contact_phone']); ?>
                            </p>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Join Event Section -->
                <div class="mt-8">
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <?php if ($isOrganizer): ?>
                            <p class="text-green-700 font-bold text-center text-lg">
                                You cannot join your own event
                            </p>
                        <?php elseif ($hasJoined): ?>
                            <div class="bg-green-100 text-green-700 px-4 py-3 rounded-md text-center font-medium">
                                You have joined this event
                            </div>
                        <?php else: ?>
                            <form method="POST">
                                <button type="submit" class="w-full bg-green-700 hover:bg-green-800 text-white py-3 px-6 rounded-md text-lg font-medium transition-colors">
                                    Join This Event
                                </button>
                            </form>
                        <?php endif; ?>
                    <?php else: ?>
                        <a href="auth.php" class="block w-full bg-green-700 hover:bg-green-800 text-white py-3 px-6 rounded-md text-lg font-medium text-center transition-colors">
                            Login to Join Event
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </main>
</body>
</html> 