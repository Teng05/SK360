<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'sk_president') {
    header("Location: ../login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>SK 360 Dashboard</title>

<script src="https://cdn.tailwindcss.com"></script>

</head>

<body class="bg-gray-100">

<div class="flex h-screen">

   <!-- SIDEBAR -->
<div class="w-64 bg-red-600 text-white flex flex-col p-3 overflow-y-auto">

    <!-- LOGO -->
    <div class="flex items-center gap-2 mb-3">
        <img src="https://cdn-icons-png.flaticon.com/512/3135/3135715.png" 
             class="w-7 h-7" alt="logo">
        <h2 class="text-base font-bold">SK 360°</h2>
    </div>

    <!-- PROFILE CARD -->
    <div class="bg-red-500 rounded-lg p-2 flex items-center gap-2 mb-3 shadow text-xs">
        <div class="bg-yellow-400 text-red-600 p-1 rounded-full text-sm">
            👤
        </div>
        <div>
            <p class="font-semibold text-xs">SK President</p>
            <p class="text-xs opacity-80">Active Role</p>
        </div>
    </div>

    <!-- MENU -->
    <nav class="space-y-1 text-xs">

        
        <a href="home.php" class="flex items-center gap-2 bg-red-500 p-2 rounded-lg">
            <span class="bg-red-400 p-1 rounded text-sm ">🏠</span>
            <span class="text-yellow-300 font-semibold text-xs">Home</span>
        </a>
<!-- ACTIVE -->
        <a href="dashboard.php" class="flex items-center gap-2 hover:bg-red-500 p-2 rounded-lg transition">
            <span class="bg-yellow-400 text-red-600 p-1 rounded text-sm">📊</span>
            <span class="text-xs">Dashboard</span>
        </a>

        <a href="consolidation.php" class="flex items-center gap-2 hover:bg-red-500 p-2 rounded-lg">
            <span class="bg-red-400 p-1 rounded text-sm">📁</span>
            <span class="text-xs">Consolidation</span>
        </a>

        <a href="module.php" class="flex items-center gap-2 hover:bg-red-500 p-2 rounded-lg">
            <span class="bg-red-400 p-1 rounded text-sm">⚙️</span>
            <span class="text-xs">Module Management</span>
        </a>

        <a href="announcements.php" class="flex items-center gap-2 hover:bg-red-500 p-2 rounded-lg">
            <span class="bg-red-400 p-1 rounded text-sm">📢</span>
            <span class="text-xs">Announcements</span>
        </a>

        <a href="calendar.php" class="flex items-center gap-2 hover:bg-red-500 p-2 rounded-lg">
            <span class="bg-red-400 p-1 rounded text-sm">📅</span>
            <span class="text-xs">Calendar</span>
        </a>

        <a href="chat.php" class="flex items-center gap-2 hover:bg-red-500 p-2 rounded-lg">
            <span class="bg-red-400 p-1 rounded text-sm">💬</span>
            <span class="text-xs">Chat</span>
        </a>

        <a href="meetings.php" class="flex items-center gap-2 hover:bg-red-500 p-2 rounded-lg">
            <span class="bg-red-400 p-1 rounded text-sm">📞</span>
            <span class="text-xs">Meetings</span>
        </a>

        <a href="rankings.php" class="flex items-center gap-2 hover:bg-red-500 p-2 rounded-lg">
            <span class="bg-red-400 p-1 rounded text-sm">🏆</span>
            <span class="text-xs">Rankings</span>
        </a>

        <a href="analytics.php" class="flex items-center gap-2 hover:bg-red-500 p-2 rounded-lg">
            <span class="bg-red-400 p-1 rounded text-sm">📈</span>
            <span class="text-xs">Analytics</span>
        </a>

        <a href="leadership.php" class="flex items-center gap-2 hover:bg-red-500 p-2 rounded-lg">
            <span class="bg-red-400 p-1 rounded text-sm">👥</span>
            <span class="text-xs">Leadership</span>
        </a>

        <a href="archive.php" class="flex items-center gap-2 hover:bg-red-500 p-2 rounded-lg">
            <span class="bg-red-400 p-1 rounded text-sm">🗂️</span>
            <span class="text-xs">Archive</span>
        </a>

        <a href="user_management.php" class="flex items-center gap-2 hover:bg-red-500 p-2 rounded-lg">
            <span class="bg-red-400 p-1 rounded text-sm">👤</span>
            <span class="text-xs">User Management</span>
        </a>

    </nav>

</div>

       <!-- MAIN -->
    <div class="flex-1 flex flex-col">

        <!-- TOPBAR -->
        <div class="bg-red-600 text-white px-6 py-3 flex justify-between items-center shadow">
            <input type="text" placeholder="Search..." 
                class="px-4 py-2 rounded-full text-black w-1/3 focus:outline-none">

            <div class="flex items-center gap-3">
                <span class="text-lg">🔔</span>
                <!-- USER NAME (PHP READY) -->
                <span class="font-semibold">User</span>
            </div>
        </div>

        <!-- CONTENT -->
        <div class="flex-1 bg-gray-100 p-8 overflow-y-auto">
            
            <!-- WELCOME TEXT -->
            <h1 class="text-4xl font-bold text-gray-900 mb-2">
                Welcome back, SK President
            </h1>
            <p class="text-gray-600 text-lg mb-8">
                Here's an overview of SK activities and submissions as of 1/25/2026
            </p>

            <!-- CARDS -->
            <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-6">

                <!-- CARD 1 -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-5">
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <p class="text-sm text-gray-500">Total Users</p>
                            <h2 class="text-4xl font-bold text-gray-900 leading-none">0</h2>
                        </div>
                        <div class="bg-red-100 p-3 rounded-xl">
                            <span class="text-red-500 text-xl">👥</span>
                        </div>
                    </div>
                    <div class="text-sm text-gray-500 leading-5 mb-3">
                        <p>0 officials,</p>
                        <p>0 youth</p>
                    </div>
                    <p class="text-sm text-green-500">↗ +0 this month</p>
                </div>

                <!-- CARD 2 -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-5">
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <p class="text-sm text-gray-500">Lipa Youth</p>
                            <h2 class="text-4xl font-bold text-gray-900 leading-none">0</h2>
                        </div>
                        <div class="bg-yellow-100 p-3 rounded-xl">
                            <span class="text-yellow-500 text-xl">👤</span>
                        </div>
                    </div>
                    <div class="text-sm text-gray-500 leading-5 mb-3">
                        <p>0 active</p>
                        <p>members</p>
                    </div>
                    <p class="text-sm text-green-500">↗ +0 new signups</p>
                </div>

                <!-- CARD 3 -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-5">
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <p class="text-sm text-gray-500">SK Chairmen</p>
                            <h2 class="text-4xl font-bold text-gray-900 leading-none">0</h2>
                        </div>
                        <div class="bg-green-100 p-3 rounded-xl">
                            <span class="text-green-500 text-xl">🛡️</span>
                        </div>
                    </div>
                    <div class="text-sm text-gray-500 leading-5 mb-3">
                        <p>Across 0</p>
                        <p>barangays</p>
                    </div>
                    <p class="text-sm text-green-500">↗ 0% coverage</p>
                </div>

                <!-- CARD 4 -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-5">
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <p class="text-sm text-gray-500">SK Secretaries</p>
                            <h2 class="text-4xl font-bold text-gray-900 leading-none">0</h2>
                        </div>
                        <div class="bg-blue-100 p-3 rounded-xl">
                            <span class="text-blue-500 text-xl">📄</span>
                        </div>
                    </div>
                    <div class="text-sm text-gray-500 leading-5 mb-3">
                        <p>0</p>
                        <p>remaining</p>
                    </div>
                    <p class="text-sm text-green-500">↗ 0% staffed</p>
                </div>

            </div>
        </div>

    </div>

    </div>
</body>
</html>