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

// 2. Fetch All Announcements
// (Make sure getAllAnnouncements() is in your database.php file as discussed!)
$announcements = $db->getAllAnnouncements(); 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Announcements | SK 360°</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* Optional: small utility if you ever revert to hover dropdown */
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
            <a href="home.php" class="flex items-center gap-2 hover:bg-red-500 p-2 rounded-lg">
                <span class="bg-red-400 p-1 rounded">🏠</span><span>Home</span>
            </a>
            <a href="announcements.php" class="flex items-center gap-2 bg-red-500 p-2 rounded-lg">
                <span class="bg-yellow-400 text-red-600 p-1 rounded">📢</span>
                <span class="text-yellow-300 font-semibold">Announcements</span>
            </a>
            <a href="calendar.php" class="flex items-center gap-2 hover:bg-red-500 p-2 rounded-lg">
                <span class="bg-red-400 p-1 rounded">📅</span><span>Event Calendar</span>
            </a>
            <a href="rankings.php" class="flex items-center gap-2 hover:bg-red-500 p-2 rounded-lg">
                <span class="bg-red-400 p-1 rounded">🏆</span><span>Rankings</span>
            </a>
            <a href="leadership.php" class="flex items-center gap-2 hover:bg-red-500 p-2 rounded-lg">
                <span class="bg-red-400 p-1 rounded">👥</span><span>Leadership</span>
            </a>
        </nav>
    </div>

    <div class="flex-1 flex flex-col">

        <div class="bg-red-600 text-white px-6 py-3 flex justify-between items-center shadow relative">
            <div class="w-1/4"></div> 
            
            <div class="w-1/3">
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

        <div class="p-8 overflow-y-auto">
            <h1 class="text-3xl font-bold text-gray-800">Announcements</h1>
            <p class="text-gray-500 mb-8">Official communications and updates for SK federation</p>

            <div class="max-w-4xl space-y-6">
                <?php if (!empty($announcements)): ?>
                    <?php foreach ($announcements as $row): ?>
                        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 hover:shadow-md transition relative">
                            <div class="absolute top-4 right-6 flex gap-2">
                                <span class="px-3 py-1 rounded-full text-[10px] font-bold uppercase 
                                    <?php echo (isset($row['priority']) && $row['priority'] == 'high') ? 'bg-red-100 text-red-600' : 
                                               ((isset($row['priority']) && $row['priority'] == 'medium') ? 'bg-yellow-100 text-yellow-600' : 'bg-blue-100 text-blue-600'); ?>">
                                    <?php echo $row['priority'] ?? 'Low'; ?>
                                </span>
                                <span class="bg-gray-100 text-gray-500 px-3 py-1 rounded-full text-[10px] font-bold uppercase flex items-center gap-1">
                                    🌐 Public
                                </span>
                            </div>

                            <div class="flex items-start gap-4">
                                <div class="text-red-500 text-xl mt-1">📢</div>
                                <div class="flex-1">
                                    <h2 class="text-xl font-bold text-gray-800"><?php echo htmlspecialchars($row['title']); ?></h2>
                                    <p class="text-xs text-gray-400 font-medium mb-4">
                                        By <?php echo htmlspecialchars($row['author_name'] ?? 'SK Federation President'); ?> • <?php echo date('Y-m-d', strtotime($row['created_at'])); ?>
                                    </p>
                                    
                                    <p class="text-gray-600 text-sm leading-relaxed mb-4">
                                        <?php echo nl2br(htmlspecialchars($row['content'])); ?>
                                    </p>

                                    <div class="flex items-center text-gray-400 text-[10px] font-bold">
                                        <span class="flex items-center gap-1">👁️ <?php echo $row['views'] ?? '0'; ?> views</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="text-gray-400 italic">No announcements found.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
// Dropdown Logic (Copied directly from home.php)
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
</script>

</body>
</html>