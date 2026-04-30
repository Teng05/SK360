<?php
session_start();
require_once '../classes/database.php';

$db = new Database();

// 1. Check Login - Restrict to SK Chairman
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

// Variables for dynamic UI
$full_name = $user_data['first_name'] . ' ' . $user_data['last_name'];
$barangay_id = $user_data['barangay_id'];
$barangay_name = $db->getBarangayName($barangay_id);
$initials = strtoupper(substr($user_data['first_name'], 0, 1) . substr($user_data['last_name'], 0, 1));

// Data Fetching for Rankings
$leaderboard = $db->getBarangayRank();
$top3 = array_slice($leaderboard, 0, 3);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rankings | SK 360°</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700;800;900&display=swap');
        body { font-family: 'Inter', sans-serif; }
        .progress-red { background-color: #ef4444; }
    </style>
</head>
<body class="bg-gray-100">

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
                <span class="bg-red-400 p-1 rounded text-sm">🏠</span>
                <span>Home</span>
            </a>
            <a href="reports.php" class="flex items-center gap-2 hover:bg-red-500 p-2 rounded-lg">
                <span class="bg-red-400 p-1 rounded text-sm">📊</span>
                <span>Reports</span>
            </a>
            <a href="budget.php" class="flex items-center gap-2 hover:bg-red-500 p-2 rounded-lg">
                <span class="bg-red-400 p-1 rounded text-sm">💰</span>
                <span>Budget</span>
            </a>
            <a href="announcements.php" class="flex items-center gap-2 hover:bg-red-500 p-2 rounded-lg">
                <span class="bg-red-400 p-1 rounded text-sm">📢</span>
                <span>Announcements</span>
            </a>
            <a href="calendar.php" class="flex items-center gap-2 hover:bg-red-500 p-2 rounded-lg">
                <span class="bg-red-400 p-1 rounded text-sm">📅</span>
                <span>Calendar</span>
            </a>
            <a href="chat.php" class="flex items-center gap-2 hover:bg-red-500 p-2 rounded-lg">
                <span class="bg-red-400 p-1 rounded text-sm">💬</span>
                <span>Chat</span>
            </a>
            <a href="meetings.php" class="flex items-center gap-2 hover:bg-red-500 p-2 rounded-lg">
                <span class="bg-red-400 p-1 rounded text-sm">📞</span>
                <span>Meetings</span>
            </a>
            <a href="rankings.php" class="flex items-center gap-2 bg-red-500 p-2 rounded-lg">
                <span class="bg-yellow-400 text-red-600 p-1 rounded text-sm">🏆</span>
                <span class="text-yellow-300 font-semibold">Rankings</span>
            </a>
            <a href="leadership.php" class="flex items-center gap-2 hover:bg-red-500 p-2 rounded-lg">
                <span class="bg-red-400 p-1 rounded text-sm">👥</span>
                <span>Leadership</span>
            </a>
            <a href="archive.php" class="flex items-center gap-2 hover:bg-red-500 p-2 rounded-lg">
                <span class="bg-red-400 p-1 rounded text-sm">🗂️</span>
                <span>Archive</span>
            </a>
        </nav>
    </div>

    <div class="flex-1 flex flex-col overflow-hidden">
        <div class="bg-red-600 text-white px-6 py-3 flex justify-between items-center shadow">
            <input type="text" placeholder="Search..." class="px-4 py-2 rounded-full text-black w-1/3 focus:outline-none">

            <div class="flex items-center gap-3 relative">
                <div class="relative">
                    <button id="notifBtn" class="text-xl hover:bg-red-500 p-2 rounded-lg transition">🔔</button>
                    <div id="notifDropdown" class="hidden absolute right-0 mt-3 w-72 bg-white rounded-2xl shadow-xl border z-50 overflow-hidden">
                        <div class="px-4 py-3 font-semibold border-b text-gray-800">Notifications</div>
                        <div class="max-h-64 overflow-y-auto">
                            <div class="px-4 py-3 hover:bg-gray-100 text-sm text-gray-700">No notifications yet</div>
                        </div>
                    </div>
                </div>

                <div class="relative">
                    <button id="userMenuBtn" class="flex items-center gap-2 hover:bg-red-500 px-3 py-2 rounded-lg transition">
                        <div class="w-7 h-7 rounded-full bg-yellow-400 text-red-600 flex items-center justify-center text-[10px] font-black border border-white/50 overflow-hidden flex-shrink-0">
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
        </div>

        <main class="flex-1 overflow-y-auto p-10 space-y-12 bg-gray-50">
            <section>
                <h1 class="text-3xl font-black tracking-tight text-[#2c3e50]">Gamified Rankings</h1>
                <p class="text-gray-500 font-medium mt-1">Encouraging timely submissions and active participation</p>
            </section>

            <section class="grid grid-cols-1 md:grid-cols-3 gap-8 items-end px-4">
                <?php 
                $badges = ['🥇', '🥈', '🥉'];
                $colors = ['border-[#f1c40f]', 'border-[#bdc3c7]', 'border-[#e67e22]'];
                foreach ($top3 as $idx => $row): 
                ?>
                <div class="bg-white p-8 rounded-xl shadow-sm border-t-[6px] <?= $colors[$idx] ?> text-center">
                    <span class="text-3xl"><?= $badges[$idx] ?></span>
                    <h3 class="font-bold text-gray-700 mt-2 text-sm uppercase">Barangay <?= htmlspecialchars($row['barangay_name']) ?></h3>
                    <p class="text-3xl font-black text-[#d91e18] mt-1"><?= number_format($row['total_points']) ?> pts</p>
                    <div class="flex justify-center gap-1 mt-3">
                        <span class="bg-[#34495e] text-white text-[8px] px-2 py-1 rounded font-bold uppercase">Perfect Attendance</span>
                        <span class="bg-[#7f8c8d] text-white text-[8px] px-2 py-1 rounded font-bold uppercase">Early Bird</span>
                    </div>
                </div>
                <?php endforeach; ?>
            </section>

            <section class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
                <h2 class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-6">Complete Leaderboard <br><span class="capitalize font-medium text-gray-400 tracking-normal">Current standings and performance metrics</span></h2>
                
                <div class="space-y-6">
                    <?php foreach ($leaderboard as $index => $row): $rank = $index + 1; ?>
                    <div class="flex items-center gap-8 p-6 bg-white border border-gray-100 rounded-xl shadow-sm hover:shadow-md transition">
                        <div class="flex flex-col items-center gap-1">
                            <span class="text-2xl <?= $rank <= 3 ? 'text-[#f1c40f]' : 'text-gray-300' ?>">🏆</span>
                            <span class="text-[10px] font-black text-green-500">#<?= $rank ?> 🔺</span>
                        </div>
                        <div class="flex-1">
                            <div class="flex justify-between items-center mb-3">
                                <h4 class="font-black text-[#2c3e50] uppercase text-xs">Barangay <?= htmlspecialchars($row['barangay_name']) ?> <br><span class="text-[10px] font-medium text-gray-400 normal-case"><?= number_format($row['total_points']) ?> points</span></h4>
                                <span class="bg-green-100 text-green-700 text-[9px] px-2 py-0.5 rounded font-black">#<?= $rank ?></span>
                            </div>
                            <div class="grid grid-cols-3 gap-8">
                                <div>
                                    <p class="text-[9px] font-bold text-gray-400 uppercase mb-1">On-time Rate <span class="float-right text-gray-800"><?= $row['timely_submission_points'] ?>%</span></p>
                                    <div class="w-full bg-gray-100 h-1.5 rounded-full overflow-hidden">
                                        <div class="progress-red h-full" style="width: <?= $row['timely_submission_points'] ?>%"></div>
                                    </div>
                                </div>
                                <div>
                                    <p class="text-[9px] font-bold text-gray-400 uppercase mb-1">Completion <span class="float-right text-gray-800"><?= $row['completeness_points'] ?>%</span></p>
                                    <div class="w-full bg-gray-100 h-1.5 rounded-full overflow-hidden">
                                        <div class="progress-red h-full" style="width: <?= $row['completeness_points'] ?>%"></div>
                                    </div>
                                </div>
                                <div>
                                    <p class="text-[9px] font-bold text-gray-400 uppercase mb-1">Engagement <span class="float-right text-gray-800"><?= $row['participation_points'] ?>%</span></p>
                                    <div class="w-full bg-gray-100 h-1.5 rounded-full overflow-hidden">
                                        <div class="progress-red h-full" style="width: <?= $row['participation_points'] ?>%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </section>

                        <section class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <div class="bg-white p-8 rounded-2xl border border-gray-100 shadow-sm">
                    <h3 class="font-extrabold text-gray-800 text-sm">Points System</h3>
                    <p class="text-xs text-gray-400 mb-6">How points are earned and deducted</p>
                    <div class="space-y-4">
                        <div class="flex justify-between items-center text-xs">
                            <span class="text-gray-600 font-medium">On-time Report Submission</span>
                            <span class="bg-green-100 text-green-700 px-3 py-1 rounded-lg font-black text-[10px]">+50 points</span>
                        </div>
                        <div class="flex justify-between items-center text-xs">
                            <span class="text-gray-600 font-medium">Meeting Attendance</span>
                            <span class="bg-green-100 text-green-700 px-3 py-1 rounded-lg font-black text-[10px]">+30 points</span>
                        </div>
                        <div class="flex justify-between items-center text-xs">
                            <span class="text-gray-600 font-medium">Community Engagement</span>
                            <span class="bg-green-100 text-green-700 px-3 py-1 rounded-lg font-black text-[10px]">+25 points</span>
                        </div>
                        <div class="flex justify-between items-center text-xs">
                            <span class="text-gray-600 font-medium">Quality Documentation</span>
                            <span class="bg-green-100 text-green-700 px-3 py-1 rounded-lg font-black text-[10px]">+20 points</span>
                        </div>
                        <div class="flex justify-between items-center text-xs">
                            <span class="text-gray-600 font-medium">Event Participation</span>
                            <span class="bg-green-100 text-green-700 px-3 py-1 rounded-lg font-black text-[10px]">+15 points</span>
                        </div>
                        <div class="flex justify-between items-center text-xs border-t pt-4">
                            <span class="text-red-500 font-bold italic underline decoration-red-200">Late Submission</span>
                            <span class="bg-red-100 text-red-700 px-3 py-1 rounded-lg font-black text-[10px]">-25 points</span>
                        </div>
                        <div class="flex justify-between items-center text-xs">
                            <span class="text-red-500 font-bold italic underline decoration-red-200">Missed Meeting</span>
                            <span class="bg-red-100 text-red-700 px-3 py-1 rounded-lg font-black text-[10px]">-30 points</span>
                        </div>
                    </div>
                </div>

                <div class="bg-white p-8 rounded-2xl border border-gray-100 shadow-sm">
                    <h3 class="font-extrabold text-gray-800 text-sm">Achievement Badges</h3>
                    <p class="text-xs text-gray-400 mb-6">Unlock special recognitions</p>
                    <div class="space-y-3">
                        <div class="flex items-center gap-4 p-3 border border-gray-50 rounded-xl">
                            <span class="text-2xl">🏆</span>
                            <div class="flex-1">
                                <p class="text-xs font-black text-gray-800">Perfect Attendance</p>
                                <p class="text-[10px] text-gray-400">Attend all meetings in a quarter</p>
                            </div>
                            <span class="bg-[#fcf3cf] text-[#b7950b] text-[8px] font-black px-2 py-0.5 rounded uppercase">gold</span>
                        </div>
                        <div class="flex items-center gap-4 p-3 border border-gray-50 rounded-xl">
                            <span class="text-2xl">⭐</span>
                            <div class="flex-1">
                                <p class="text-xs font-black text-gray-800">Early Bird</p>
                                <p class="text-[10px] text-gray-400">Submit reports 3+ days before deadline</p>
                            </div>
                            <span class="bg-[#fcf3cf] text-[#b7950b] text-[8px] font-black px-2 py-0.5 rounded uppercase">gold</span>
                        </div>
                        <div class="flex items-center gap-4 p-3 border border-gray-50 rounded-xl">
                            <span class="text-2xl">👑</span>
                            <div class="flex-1">
                                <p class="text-xs font-black text-gray-800">Top Performer</p>
                                <p class="text-[10px] text-gray-400">Rank #1 for consecutive months</p>
                            </div>
                            <span class="bg-[#f5eef8] text-[#8e44ad] text-[8px] font-black px-2 py-0.5 rounded uppercase">platinum</span>
                        </div>
                        <div class="flex items-center gap-4 p-3 border border-gray-50 rounded-xl">
                            <span class="text-2xl">🎖️</span>
                            <div class="flex-1">
                                <p class="text-xs font-black text-gray-800">Rising Star</p>
                                <p class="text-[10px] text-gray-400">Improve ranking by 5+ positions</p>
                            </div>
                            <span class="bg-[#f4f6f7] text-[#7f8c8d] text-[8px] font-black px-2 py-0.5 rounded uppercase">silver</span>
                        </div>
                        <div class="flex items-center gap-4 p-3 border border-gray-50 rounded-xl">
                            <span class="text-2xl">🤝</span>
                            <div class="flex-1">
                                <p class="text-xs font-black text-gray-800">Team Player</p>
                                <p class="text-[10px] text-gray-400">High engagement in collaborative activities</p>
                            </div>
                            <span class="bg-[#fef5e7] text-[#d68910] text-[8px] font-black px-2 py-0.5 rounded uppercase">bronze</span>
                        </div>
                    </div>
                </div>
            </section>
        </main>
    </div>
</div>

<script>
    // Dropdown Logic (Copied from calendar.php)
    const notifBtn = document.getElementById('notifBtn');
    const notifDropdown = document.getElementById('notifDropdown');
    const userMenuBtn = document.getElementById('userMenuBtn');
    const userDropdown = document.getElementById('userDropdown');

    notifBtn.addEventListener('click', function(e) {
        e.stopPropagation();
        notifDropdown.classList.toggle('hidden');
        userDropdown.classList.add('hidden');
    });

    userMenuBtn.addEventListener('click', function(e) {
        e.stopPropagation();
        userDropdown.classList.toggle('hidden');
        notifDropdown.classList.add('hidden');
    });

    document.addEventListener('click', function(e) {
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