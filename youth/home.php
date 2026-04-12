<?php
session_start();
require_once('../classes/database.php');

$db = new Database();

// 1. Check Login
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'youth') {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$user_data = $db->getUserById($user_id);

if (!$user_data) {
    header("Location: ../login.php?error=usernotfound");
    exit();
}

$user_name = $user_data['first_name'] . ' ' . $user_data['last_name'];
$barangay_id = $user_data['barangay_id'] ?? 0;

// 4. Fetch Dynamic Dashboard Data
$events_joined = 0; 
$participation_rate = 0; 
$latest_ann = $db->getLatestAnnouncement();
$upcoming_events = $db->getUpcomingEvents(3);
$brgy_rank = $db->getBarangayRank($barangay_id);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Youth Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* Small utility for the dropdown visibility */
        .group:hover .group-hover\:block { display: block; }
    </style>
</head>

<body class="bg-gray-100">

<div class="flex h-screen">

    <div class="w-64 bg-red-600 text-white flex flex-col p-3">
        <div class="flex items-center gap-2 mb-3">
            <img src="https://cdn-icons-png.flaticon.com/512/3135/3135715.png" class="w-7 h-7">
            <h2 class="text-base font-bold text-white">SK 360°</h2>
        </div>

        <div class="bg-red-500 rounded-lg p-2 flex items-center gap-2 mb-3 shadow text-xs">
            <div class="bg-yellow-400 text-red-600 p-1 rounded-full font-bold">👤</div>
            <div>
                <p class="font-semibold"><?php echo htmlspecialchars($user_name); ?></p>
                <p class="opacity-80 text-[10px]">Youth Member</p>
            </div>
        </div>

        <nav class="space-y-1 text-xs">
            <a href="home.php" class="flex items-center gap-2 bg-red-500 p-2 rounded-lg">
                <span class="bg-yellow-400 text-red-600 p-1 rounded">🏠</span>
                <span class="text-yellow-300 font-semibold">Home</span>
            </a>
            <a href="announcements.php" class="flex items-center gap-2 hover:bg-red-500 p-2 rounded-lg">
                <span class="bg-red-400 p-1 rounded">📢</span><span>Announcements</span>
            </a>
            <a href="calendar.php" class="flex items-center gap-2 hover:bg-red-500 p-2 rounded-lg">
                <span class="bg-red-400 p-1 rounded">📅</span><span>Event Calendar</span>
            </a>
            <a href="rankings.php" class="flex items-center gap-2 hover:bg-red-500 p-2 rounded-lg">
                <span class="bg-red-400 p-1 rounded">🏆</span><span>Rankings</span>
            </a>
        </nav>
    </div>

    <div class="flex-1 flex flex-col">

        <div class="bg-red-600 text-white px-6 py-3 flex justify-between items-center shadow relative">
            <div class="w-1/4"></div> <div class="w-1/3">
                <input type="text" placeholder="Search..." class="w-full px-4 py-2 rounded-full text-black focus:outline-none text-sm">
            </div>

            <div class="w-1/4 flex justify-end items-center gap-5 text-sm">
                <button class="hover:opacity-80">🔔</button>
                
                <div class="relative">
                    <button id="profileDropdownBtn" class="flex items-center gap-2 font-semibold focus:outline-none hover:opacity-80 transition">
                        <span><?php echo htmlspecialchars($user_name); ?></span>
                        <span class="text-[10px]">▼</span>
                    </button>

                    <div id="profileMenu" class="absolute right-0 mt-3 w-48 bg-white rounded-xl shadow-2xl py-2 z-[9999] hidden border border-gray-100">
                        <div class="px-4 py-3 border-b border-gray-50">
                            <p class="text-[10px] text-gray-400 uppercase font-black tracking-widest">Account Settings</p>
                        </div>
                        <a href="profile.php" class="block px-4 py-3 text-gray-700 hover:bg-gray-50 text-xs flex items-center gap-2 transition">
                            <span>👤</span> View Profile
                        </a>
                        <a href="../auth/logout.php" class="block px-4 py-3 text-red-600 hover:bg-red-50 text-xs font-bold flex items-center gap-2 transition">
                            <span>🚪</span> Log Out
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="p-6 overflow-y-auto">
            <h1 class="text-2xl font-bold mb-4 uppercase tracking-tight text-gray-800">LIPA YOUTH DASHBOARD</h1>

        <div class="grid grid-cols-3 gap-4 mb-6">
            <div class="bg-blue-500 text-white p-5 rounded-xl shadow border-b-4 border-blue-700 text-center">
                <h2 class="text-2xl font-bold"><?php echo $participation_rate; ?>%</h2>
                <p class="text-[10px] uppercase font-semibold opacity-90">Participation Rate</p>
            </div>
            <div class="bg-yellow-500 text-white p-5 rounded-xl shadow border-b-4 border-yellow-700 text-center">
                <h2 class="text-2xl font-bold"><?php echo $events_joined; ?></h2>
                <p class="text-[10px] uppercase font-semibold opacity-90">Events Joined</p>
            </div>
            <div class="bg-green-500 text-white p-5 rounded-xl shadow border-b-4 border-green-700 text-center">
                <h2 class="text-2xl font-bold">#<?php echo $brgy_rank; ?></h2>
                <p class="text-[10px] uppercase font-semibold opacity-90">Your Barangay's Rank</p>
            </div>
        </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                
                <div class="lg:col-span-2 space-y-4">
                    <div class="flex justify-between items-center">
                        <h2 class="font-bold text-gray-700 uppercase text-xs tracking-wider">Activity Feed</h2>
                        <div class="flex gap-1 bg-gray-200 p-1 rounded-lg text-[9px] font-bold">
                            <button onclick="filterFeed('all')" id="tab-all" class="feed-tab bg-white px-3 py-1.5 rounded shadow text-red-600">ALL</button>
                            <button onclick="filterFeed('announcement')" id="tab-announcement" class="feed-tab px-3 py-1.5 rounded text-gray-500 hover:bg-gray-300">ANNOUNCEMENTS</button>
                            <button onclick="filterFeed('event')" id="tab-event" class="feed-tab px-3 py-1.5 rounded text-gray-500 hover:bg-gray-300">EVENTS</button>
                        </div>
                    </div>
                    
                    <div id="activity-container" class="space-y-4">
                        <div class="feed-item" data-category="announcement">
                            <?php if ($latest_ann): ?>
                            <div class="bg-white rounded-xl shadow overflow-hidden border">
                                <div class="h-40 bg-gray-200 overflow-hidden">
                                    <img src="../assets/uploads/<?php echo $latest_ann['image']; ?>" class="w-full h-full object-cover">
                                </div>
                                <div class="p-4">
                                    <span class="bg-blue-100 text-blue-600 text-[8px] font-bold px-2 py-0.5 rounded uppercase">Latest Announcement</span>
                                    <h3 class="font-bold text-lg mt-1"><?php echo htmlspecialchars($latest_ann['title']); ?></h3>
                                    <p class="text-gray-500 text-xs mt-1 leading-relaxed">
                                        <?php echo substr(htmlspecialchars($latest_ann['content']), 0, 150); ?>...
                                    </p>
                                    <a href="view_announcement.php?id=<?php echo $latest_ann['id']; ?>" class="mt-3 inline-block text-red-600 text-[10px] font-bold uppercase hover:underline">Read Full Article →</a>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>

                        <?php foreach ($upcoming_events as $event): ?>
                        <div class="feed-item" data-category="event">
                            <div class="bg-white p-4 rounded-xl shadow border flex justify-between items-center">
                                <div class="flex items-center gap-3">
                                    <div class="bg-red-100 text-red-600 p-2 rounded text-center font-bold text-xs min-w-[42px]">
                                        <?php echo date('d', strtotime($event['start_datetime'])); ?><br>
                                        <span class="text-[8px] uppercase"><?php echo date('M', strtotime($event['start_datetime'])); ?></span>
                                    </div>
                                    <div>
                                        <p class="text-[11px] font-bold text-gray-800"><?php echo htmlspecialchars($event['title']); ?></p>
                                        <p class="text-[9px] text-gray-400">📍 <?php echo htmlspecialchars($event['location'] ?? 'No Location'); ?></p>
                                    </div>
                                </div>
                                <a href="calendar.php" class="text-red-600 font-bold text-[9px] border border-red-600 px-3 py-1 rounded hover:bg-red-600 hover:text-white transition">JOIN</a>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="space-y-6">
                    <div class="bg-white p-4 rounded-xl shadow border">
                        <h2 class="font-bold text-gray-700 uppercase text-[10px] mb-3 tracking-widest border-b pb-2">Quick Actions</h2>
                        <div class="grid grid-cols-3 gap-2">
                            <a href="calendar.php" class="flex flex-col items-center justify-center p-3 bg-blue-600 rounded-lg hover:bg-blue-700 transition aspect-square">
                                <span class="text-white text-xl">📅</span>
                                <span class="text-[8px] font-bold mt-1 text-white text-center">JOIN EVENT</span>
                            </a>
                            <a href="rankings.php" class="flex flex-col items-center justify-center p-3 bg-green-600 rounded-lg hover:bg-green-700 transition aspect-square">
                                <span class="text-white text-xl">🏆</span>
                                <span class="text-[8px] font-bold mt-1 text-white text-center">RANKINGS</span>
                            </a>
                            <a href="profile.php" class="flex flex-col items-center justify-center p-3 bg-indigo-600 rounded-lg hover:bg-indigo-700 transition aspect-square">
                                <span class="text-white text-xl">👤</span>
                                <span class="text-[8px] font-bold mt-1 text-white text-center">PROFILE</span>
                            </a>
                        </div>
                    </div>

                    <div class="bg-white p-4 rounded-xl shadow border">
                        <h2 class="font-bold text-gray-700 uppercase text-[10px] mb-3 tracking-widest border-b pb-2">Calendar Preview</h2>
                        <p class="text-[10px] text-gray-400 italic">Check the calendar for more dates.</p>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<script>

    // Dropdown Logic
const dropdownBtn = document.getElementById('profileDropdownBtn');
const profileMenu = document.getElementById('profileMenu');

dropdownBtn.addEventListener('click', (e) => {
    e.stopPropagation(); // Prevents the click from immediately reaching the window listener below
    profileMenu.classList.toggle('hidden');
});

// Click outside to close
window.addEventListener('click', (e) => {
    if (!profileMenu.contains(e.target) && !dropdownBtn.contains(e.target)) {
        profileMenu.classList.add('hidden');
    }
});

function filterFeed(category) {
    const items = document.querySelectorAll('.feed-item');
    const tabs = document.querySelectorAll('.feed-tab');

    // Reset Tabs
    tabs.forEach(tab => {
        tab.classList.remove('bg-white', 'shadow', 'text-red-600');
        tab.classList.add('text-gray-500', 'hover:bg-gray-300');
    });

    // Set Active Tab
    const activeTab = document.getElementById('tab-' + category);
    activeTab.classList.add('bg-white', 'shadow', 'text-red-600');
    activeTab.classList.remove('text-gray-500', 'hover:bg-gray-300');

    // Filter Items
    items.forEach(item => {
        if (category === 'all' || item.getAttribute('data-category') === category) {
            item.style.display = 'block';
        } else {
            item.style.display = 'none';
        }
    });
}
</script>

</body>
</html>