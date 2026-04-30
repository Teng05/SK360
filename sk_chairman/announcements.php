<?php
session_start();
require_once '../classes/database.php';

$db = new Database();

// 1. Check Login - Chairman Only
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'sk_chairman') {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$user_data = $db->getUserById($user_id);

if (!$user_data) {
    header("Location: ../login.php?error=usernotfound");
    exit();
}

// 2. Variables for UI Consistency (Matching Calendar/Home)
$full_name = $user_data['first_name'] . ' ' . $user_data['last_name'];
$barangay_id = $user_data['barangay_id'];
$barangay_name = $db->getBarangayName($barangay_id);
$initials = strtoupper(substr($user_data['first_name'], 0, 1) . substr($user_data['last_name'], 0, 1));

// 3. Fetch Announcements (Using the parameterized function we discussed)
$announcements = $db->getAnnouncements('sk_chairman'); 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Announcements | SK 360°</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 overflow-hidden">

<div class="flex h-screen">

    <div class="w-64 bg-red-600 text-white flex flex-col p-3 overflow-y-auto">
        <div class="flex items-center gap-2 mb-3">
            <img src="https://cdn-icons-png.flaticon.com/512/3135/3135715.png" class="w-7 h-7" alt="logo">
            <h2 class="text-base font-bold">SK 360°</h2>
        </div>

        <div class="bg-red-500 rounded-lg p-2 flex items-center gap-2 mb-3 shadow text-xs">
            <div class="bg-yellow-400 text-red-600 h-9 w-9 rounded-full flex items-center justify-center font-bold border-2 border-red-400 overflow-hidden shadow-inner flex-shrink-0">
                <?php if (!empty($user_data['profile_pic'])): ?>
                    <img src="../uploads/<?= htmlspecialchars($user_data['profile_pic']) ?>" class="w-full h-full object-cover">
                <?php else: ?>
                    <span class="text-xs"><?= $initials ?></span>
                <?php endif; ?>
            </div>

            <div class="overflow-hidden">
                <p class="font-semibold text-[11px] truncate"><?= htmlspecialchars($full_name) ?></p>
                <p class="text-[9px] opacity-90 uppercase font-black tracking-tighter truncate">
                    SK Chairman - <?= htmlspecialchars($barangay_name) ?>
                </p>
            </div>
        </div>

        <nav class="space-y-1 text-xs">
            <a href="home.php" class="flex items-center gap-2 hover:bg-red-500 p-2 rounded-lg">
                <span class="bg-red-400 p-1 rounded text-sm">🏠</span><span>Home</span>
            </a>
            <a href="reports.php" class="flex items-center gap-2 hover:bg-red-500 p-2 rounded-lg">
                <span class="bg-red-400 p-1 rounded text-sm">📊</span><span>Reports</span>
            </a>
            <a href="budget.php" class="flex items-center gap-2 hover:bg-red-500 p-2 rounded-lg">
                <span class="bg-red-400 p-1 rounded text-sm">💰</span><span>Budget</span>
            </a>
            <a href="announcements.php" class="flex items-center gap-2 bg-red-500 p-2 rounded-lg">
                <span class="bg-yellow-400 text-red-600 p-1 rounded text-sm">📢</span>
                <span class="text-yellow-300 font-semibold">Announcements</span>
            </a>
            <a href="calendar.php" class="flex items-center gap-2 hover:bg-red-500 p-2 rounded-lg">
                <span class="bg-red-400 p-1 rounded text-sm">📅</span><span>Calendar</span>
            </a>
            <a href="chat.php" class="flex items-center gap-2 hover:bg-red-500 p-2 rounded-lg">
                <span class="bg-red-400 p-1 rounded text-sm">💬</span><span>Chat</span>
            </a>
            <a href="meetings.php" class="flex items-center gap-2 hover:bg-red-500 p-2 rounded-lg">
                <span class="bg-red-400 p-1 rounded text-sm">📞</span><span>Meetings</span>
            </a>
            <a href="rankings.php" class="flex items-center gap-2 hover:bg-red-500 p-2 rounded-lg">
                <span class="bg-red-400 p-1 rounded text-sm">🏆</span><span>Rankings</span>
            </a>
            <a href="leadership.php" class="flex items-center gap-2 hover:bg-red-500 p-2 rounded-lg">
                <span class="bg-red-400 p-1 rounded text-sm">👥</span><span>Leadership</span>
            </a>
            <a href="archive.php" class="flex items-center gap-2 hover:bg-red-500 p-2 rounded-lg">
                <span class="bg-red-400 p-1 rounded text-sm">🗂️</span><span>Archive</span>
            </a>
        </nav>
    </div>

    <div class="flex-1 flex flex-col overflow-hidden">
        
        <header class="bg-red-600 text-white px-6 py-3 flex justify-between items-center shadow">
            <input type="text" placeholder="Search announcements..." class="px-4 py-2 rounded-full text-black w-1/3 focus:outline-none text-sm">

            <div class="flex items-center gap-3 relative">
                <div class="relative">
                    <button id="notifBtn" class="text-xl hover:bg-red-500 p-2 rounded-lg transition">🔔</button>
                    <div id="notifDropdown" class="hidden absolute right-0 mt-3 w-72 bg-white rounded-2xl shadow-xl border z-50 overflow-hidden">
                        <div class="px-4 py-3 font-semibold border-b text-gray-800">Notifications</div>
                        <div class="max-h-64 overflow-y-auto">
                            <div class="px-4 py-3 hover:bg-gray-100 text-sm text-gray-700">No new announcements today</div>
                        </div>
                    </div>
                </div>

                <div class="relative">
                    <button id="userMenuBtn" class="flex items-center gap-2 hover:bg-red-500 px-3 py-2 rounded-lg transition">
                        <div class="w-7 h-7 rounded-full bg-yellow-400 text-red-600 flex items-center justify-center text-[10px] font-black border border-white/50 overflow-hidden">
                            <?php if (!empty($user_data['profile_pic'])): ?>
                                <img src="../uploads/<?= htmlspecialchars($user_data['profile_pic']) ?>" class="w-full h-full object-cover">
                            <?php else: ?>
                                <?= $initials ?>
                            <?php endif; ?>
                        </div>
                        <span class="font-semibold text-sm"><?= htmlspecialchars($full_name) ?></span>
                        <span class="text-[10px]">▼</span>
                    </button>
                    <div id="userDropdown" class="hidden absolute right-0 mt-3 w-64 bg-white rounded-2xl shadow-xl border overflow-hidden z-50">
                        <div class="px-5 py-4 font-semibold text-gray-800 border-b">My Account</div>
                        <a href="profile.php" class="flex items-center gap-3 px-5 py-3 hover:bg-gray-100 transition">
                            <span>👤</span> <span class="text-gray-700">Profile Settings</span>
                        </a>
                        <a href="../auth/logout.php" class="flex items-center gap-3 px-5 py-3 text-red-500 hover:bg-gray-100 transition">
                            <span>↩️</span> <span>Log Out</span>
                        </a>
                    </div>
                </div>
            </div>
        </header>

        <main class="p-8 overflow-y-auto h-full bg-gray-50">
            <div class="max-w-4xl mx-auto">
                <div class="mb-8">
                    <h1 class="text-3xl font-black text-gray-800 uppercase tracking-tighter">Announcements</h1>
                    <p class="text-gray-500 font-medium italic">Official updates from the SK Federation President.</p>
                </div>

                <div class="space-y-6">
                    <?php if (!empty($announcements)): foreach ($announcements as $row): ?>
                        <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100 hover:shadow-md transition-all relative group">
                            
                            <div class="absolute top-6 right-6 flex gap-2">
                                <?php if(($row['visibility'] ?? 'public') == 'internal'): ?>
                                    <span class="bg-gray-800 text-white text-[9px] font-black px-3 py-1 rounded-full uppercase">
                                        🔒 Internal
                                    </span>
                                <?php else: ?>
                                    <span class="bg-green-100 text-green-600 text-[9px] font-black px-3 py-1 rounded-full uppercase">
                                        🌐 Public
                                    </span>
                                <?php endif; ?>
                                
                                <?php 
                                    // Kunin ang priority, default is 'low' kung walang laman
                                    $priority = strtolower($row['priority'] ?? 'low'); 
                                ?>
                                <span class="px-3 py-1 rounded-full text-[9px] font-black uppercase 
                                    <?= $priority == 'high' ? 'bg-red-100 text-red-600' : ($priority == 'medium' ? 'bg-yellow-100 text-yellow-600' : 'bg-blue-100 text-blue-600') ?>">
                                    <?= htmlspecialchars($priority) ?>
                                </span>
                            </div>

                            <div class="flex items-start gap-5">
                                <div class="w-12 h-12 bg-red-50 rounded-2xl flex items-center justify-center text-xl shadow-inner">📢</div>
                                <div class="flex-1">
                                    <h2 class="text-xl font-black text-gray-800 uppercase leading-tight mb-1">
                                        <?= htmlspecialchars($row['title']); ?>
                                    </h2>
                                    <div class="flex items-center gap-2 text-[10px] text-gray-400 font-bold uppercase mb-4">
                                        <span class="text-red-500">Fed President</span>
                                        <span>•</span>
                                        <span><?= date('M d, Y', strtotime($row['created_at'])); ?></span>
                                    </div>

                                    <div class="text-gray-600 text-sm leading-relaxed mb-4">
                                        <?= nl2br(htmlspecialchars($row['content'])); ?>
                                    </div>

                                    <div class="flex items-center gap-4 text-[10px] font-black text-gray-400 uppercase pt-4 border-t border-gray-50">
                                        <span>👁️ <?= $row['views'] ?? 0 ?> Views</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; else: ?>
                        <div class="text-center py-20 bg-white rounded-3xl border-2 border-dashed border-gray-200">
                            <p class="text-gray-400 font-black uppercase text-[10px]">No announcements posted yet.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>
</div>

<script>
    // Synced Dropdown Logic
    const notifBtn = document.getElementById('notifBtn');
    const notifDropdown = document.getElementById('notifDropdown');
    const userMenuBtn = document.getElementById('userMenuBtn');
    const userDropdown = document.getElementById('userDropdown');

    notifBtn.addEventListener('click', (e) => {
        e.stopPropagation();
        notifDropdown.classList.toggle('hidden');
        userDropdown.classList.add('hidden');
    });

    userMenuBtn.addEventListener('click', (e) => {
        e.stopPropagation();
        userDropdown.classList.toggle('hidden');
        notifDropdown.classList.add('hidden');
    });

    document.addEventListener('click', (e) => {
        if (!notifBtn.contains(e.target) && !notifDropdown.contains(e.target)) {
            notifDropdown.classList.add('hidden');
        }
        if (!userMenuBtn.contains(e.target) && !userDropdown.contains(e.target)) {
            userDropdown.classList.add('hidden');
        }
    });
</script>

</body>
</html>