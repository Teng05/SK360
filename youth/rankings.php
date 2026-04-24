<?php
session_start();
require_once '../classes/database.php';

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

// Mock Data for Rankings
$topRankings = [
    ['name' => 'Barangay Balintawak', 'points' => 850, 'color' => 'border-yellow-400', 'icon' => '🏆', 'badges' => ['Perfect Attendance', 'Early Bird']],
    ['name' => 'Barangay Sabang', 'points' => 820, 'color' => 'border-gray-200', 'icon' => '🥈', 'badges' => ['Constant Performer', 'Quality Reports']],
    ['name' => 'Barangay Muntingpulo', 'points' => 795, 'color' => 'border-orange-400', 'icon' => '🥉', 'badges' => ['Rising Star', 'Team Player']]
];

$leaderboard = [
    ['rank' => 1, 'name' => 'Barangay Sta. Rita', 'points' => 810, 'on_time' => 100, 'completion' => 88, 'engagement' => 95, 'trend' => 'up'],
    ['rank' => 2, 'name' => 'Barangay 2', 'points' => 800, 'on_time' => 95, 'completion' => 92, 'engagement' => 80, 'trend' => 'up'],
    ['rank' => 3, 'name' => 'Barangay Inosluban', 'points' => 785, 'on_time' => 85, 'completion' => 84, 'engagement' => 88, 'trend' => 'down'],
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rankings | SK 360°</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 overflow-hidden">

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
            <a href="announcements.php" class="flex items-center gap-2 hover:bg-red-500 p-2 rounded-lg">
                <span class="bg-red-400 p-1 rounded">📢</span><span>Announcements</span>
            </a>
            <a href="calendar.php" class="flex items-center gap-2 hover:bg-red-500 p-2 rounded-lg">
                <span class="bg-red-400 p-1 rounded">📅</span><span>Event Calendar</span>
            </a>
            <a href="rankings.php" class="flex items-center gap-2 bg-red-500 p-2 rounded-lg">
                <span class="bg-yellow-400 text-red-600 p-1 rounded">🏆</span>
                <span class="text-yellow-300 font-semibold">Rankings</span>
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
                        <a href="profile.php" class="block px-4 py-3 text-gray-700 hover:bg-gray-50 text-xs flex items-center gap-2 transition font-medium">
                            <span>👤</span> View Profile
                        </a>
                        <a href="../auth/logout.php" class="block px-4 py-3 text-red-600 hover:bg-red-50 text-xs font-bold flex items-center gap-2 transition">
                            <span>🚪</span> Log Out
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="p-8 overflow-y-auto h-full bg-gray-50">
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-800 uppercase tracking-tight">Gamified Rankings</h1>
                <p class="text-gray-500">Encouraging timely submissions and active participation</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
                <?php foreach ($topRankings as $top): ?>
                <div class="bg-white p-6 rounded-2xl border-2 <?= $top['color'] ?> shadow-sm text-center">
                    <div class="text-3xl mb-2"><?= $top['icon'] ?></div>
                    <h3 class="text-xs font-black text-gray-800 uppercase mb-1"><?= $top['name'] ?></h3>
                    <p class="text-2xl font-black text-red-600 leading-none mb-3"><?= $top['points'] ?> <span class="text-[10px] text-gray-400 uppercase">pts</span></p>
                    <div class="flex justify-center gap-1 flex-wrap">
                        <?php foreach ($top['badges'] as $badge): ?>
                            <span class="bg-gray-100 text-[8px] font-black uppercase px-2 py-1 rounded text-gray-500 border border-gray-200"><?= $badge ?></span>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <div class="bg-white rounded-2xl border border-gray-100 p-6 mb-10 shadow-sm">
                <div class="mb-6">
                    <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Complete Leaderboard</h3>
                </div>

                <div class="space-y-6">
                    <?php foreach ($leaderboard as $row): ?>
                    <div class="bg-gray-50/50 rounded-2xl p-5 border border-gray-100 flex flex-col md:flex-row items-center gap-6 group hover:border-red-200 transition">
                        <div class="flex items-center gap-4 min-w-[200px]">
                            <div class="text-2xl"><?= $row['rank'] == 1 ? '🥇' : ($row['rank'] == 2 ? '🥈' : '🥉') ?></div>
                            <div class="text-left leading-tight">
                                <h4 class="text-xs font-black text-gray-800 uppercase"><?= $row['name'] ?></h4>
                                <p class="text-[10px] text-gray-400"><?= $row['points'] ?> points</p>
                            </div>
                        </div>

                        <div class="flex-1 grid grid-cols-1 md:grid-cols-3 gap-6 w-full">
                            <?php 
                            $metrics = [
                                ['label' => 'On-time Rate', 'val' => $row['on_time']],
                                ['label' => 'Completion', 'val' => $row['completion']],
                                ['label' => 'Engagement', 'val' => $row['engagement']]
                            ];
                            foreach ($metrics as $m): 
                            ?>
                            <div>
                                <div class="flex justify-between text-[8px] font-black uppercase text-gray-400 mb-1">
                                    <span><?= $m['label'] ?></span>
                                    <span><?= $m['val'] ?>%</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-1.5 overflow-hidden">
                                    <div class="bg-red-500 h-full" style="width: <?= $m['val'] ?>%"></div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>

                        <div class="flex items-center gap-2">
                             <span class="text-xs px-2 py-0.5 rounded bg-green-100 text-green-600 font-black text-[9px]">#<?= $row['rank'] ?></span>
                             <span><?= $row['trend'] == 'up' ? '📈' : '📉' ?></span>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pb-10">
                <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
                    <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-6">Points System</h3>
                    <div class="space-y-3">
                        <div class="flex justify-between items-center text-[11px] border-b border-gray-50 pb-2">
                            <span class="text-gray-600 font-medium">On-time Report Submission</span>
                            <span class="text-green-500 bg-green-50 px-2 py-0.5 rounded font-black">+50 pts</span>
                        </div>
                        <div class="flex justify-between items-center text-[11px] border-b border-gray-50 pb-2">
                            <span class="text-gray-600 font-medium">Meeting Attendance</span>
                            <span class="text-green-500 bg-green-50 px-2 py-0.5 rounded font-black">+30 pts</span>
                        </div>
                        <div class="flex justify-between items-center text-[11px] border-b border-gray-50 pb-2">
                            <span class="text-gray-600 font-medium">Late Submission</span>
                            <span class="text-red-500 bg-red-50 px-2 py-0.5 rounded font-black">-20 pts</span>
                        </div>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
                    <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-6">Achievement Badges</h3>
                    <div class="space-y-4">
                        <div class="flex items-center gap-4">
                            <div class="bg-gray-50 w-10 h-10 rounded-full flex items-center justify-center shadow-inner">🏆</div>
                            <div>
                                <h4 class="text-xs font-black text-gray-800 uppercase leading-none">Perfect Attendance</h4>
                                <p class="text-[10px] text-gray-400 mt-1">Attend all meetings in a quarter</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-4">
                            <div class="bg-gray-50 w-10 h-10 rounded-full flex items-center justify-center shadow-inner">⭐</div>
                            <div>
                                <h4 class="text-xs font-black text-gray-800 uppercase leading-none">Early Bird</h4>
                                <p class="text-[10px] text-gray-400 mt-1">Submit reports 3+ days before deadline</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Profile Dropdown Logic (Consistent with your code)
    const dropdownBtn = document.getElementById('profileDropdownBtn');
    const profileMenu = document.getElementById('profileMenu');

    dropdownBtn.addEventListener('click', (e) => {
        e.stopPropagation();
        profileMenu.classList.toggle('hidden');
    });

    window.addEventListener('click', (e) => {
        if (!profileMenu.contains(e.target) && !dropdownBtn.contains(e.target)) {
            profileMenu.classList.add('hidden');
        }
    });
</script>

</body>
</html>