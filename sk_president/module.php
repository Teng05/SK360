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

<body class="bg-[#f1f5f9] overflow-hidden">

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

    <a href="home.php" class="flex items-center gap-2 hover:bg-red-500 p-2 rounded-lg transition">
        <span class="bg-red-400 p-1 rounded text-sm">🏠</span>
        <span class="text-xs">Home</span>
    </a>

    <a href="dashboard.php" class="flex items-center gap-2 hover:bg-red-500 p-2 rounded-lg transition">
        <span class="bg-red-400 p-1 rounded text-sm">📊</span>
        <span class="text-xs">Dashboard</span>
    </a>

    <a href="consolidation.php" class="flex items-center gap-2 hover:bg-red-500 p-2 rounded-lg">
        <span class="bg-red-400 p-1 rounded text-sm">📁</span>
        <span class="text-xs">Consolidation</span>
    </a>

    <!-- ACTIVE -->
    <a href="module.php" class="flex items-center gap-2 bg-red-500 p-2 rounded-lg">
        <span class="bg-yellow-400 text-red-600 p-1 rounded text-sm">⚙️</span>
        <span class="text-yellow-300 font-semibold text-xs">Module Management</span>
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
    <div class="flex-1 flex flex-col min-w-0">

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
        <main class="flex-1 overflow-y-auto p-8">

            <!-- GREETING -->
            <div class="mb-6">
                <h2 class="text-[32px] font-bold text-gray-900 leading-tight">Good morning, SK President!</h2>
                <p class="text-gray-500 mt-1">Here's what’s happening in SK 360° today.</p>
            </div>

            <!-- CARDS -->
            <div class="grid grid-cols-4 gap-5 mb-6">

                <div class="bg-[#ef4444] text-white p-5 rounded-2xl shadow-sm">
                    <div class="flex justify-between items-start">
                        <div>
                            <h3 class="text-3xl font-bold">0</h3>
                            <p class="text-sm mt-1">Reports Submitted</p>
                        </div>
                        <span class="text-xl opacity-80">📄</span>
                    </div>
                </div>

                <div class="bg-[#3b82f6] text-white p-5 rounded-2xl shadow-sm">
                    <div class="flex justify-between items-start">
                        <div>
                            <h3 class="text-3xl font-bold">0%</h3>
                            <p class="text-sm mt-1">Community Engagement</p>
                        </div>
                        <span class="text-xl opacity-80">📈</span>
                    </div>
                </div>

                <div class="bg-[#f59e0b] text-white p-5 rounded-2xl shadow-sm">
                    <div class="flex justify-between items-start">
                        <div>
                            <h3 class="text-3xl font-bold">0</h3>
                            <p class="text-sm mt-1">Pending Reviews</p>
                        </div>
                        <span class="text-xl opacity-80">🕒</span>
                    </div>
                </div>

                <div class="bg-[#22c55e] text-white p-5 rounded-2xl shadow-sm">
                    <div class="flex justify-between items-start">
                        <div>
                            <h3 class="text-3xl font-bold">0</h3>
                            <p class="text-sm mt-1">Upcoming Events</p>
                        </div>
                        <span class="text-xl opacity-80">📅</span>
                    </div>
                </div>
            </div>

            <!-- QUICK ACTIONS -->
            <div class="bg-white rounded-2xl p-5 shadow-sm mb-6">
                <h3 class="font-semibold text-gray-800 mb-1">Quick Actions</h3>
                <p class="text-sm text-gray-400 mb-4">Frequently used features</p>

                <div class="flex flex-wrap gap-3">
                    <button class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg text-sm font-medium">
                        Create Announcement
                    </button>
                    <button class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-lg text-sm font-medium">
                        Manage Events
                    </button>
                    <button class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-medium">
                        Schedule Meeting
                    </button>
                    <button class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium">
                        Open Chat
                    </button>
                    <button class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium">
                        View Analytics
                    </button>
                </div>
            </div>

            <!-- ACTIVITY FEED -->
            <div class="bg-white rounded-2xl p-6 shadow-sm">
                <div class="flex justify-between items-center mb-4">
                    <div>
                        <h3 class="font-semibold text-gray-800">Activity Feed</h3>
                        <p class="text-sm text-gray-400">Latest updates and announcements</p>
                    </div>
                    <span class="text-xs px-2 py-1 rounded-full bg-red-50 text-red-500">● Live</span>
                </div>

                <!-- POST BOX -->
                <div class="border border-gray-200 rounded-xl p-4 mb-4">
                    <textarea 
                        rows="3"
                        placeholder="What's on your mind?"
                        class="w-full border border-gray-200 rounded-xl p-3 resize-none focus:outline-none focus:ring-2 focus:ring-red-400"
                    ></textarea>

                    <div class="flex justify-end mt-3">
                        <button class="bg-red-500 hover:bg-red-600 text-white px-5 py-2 rounded-lg text-sm font-medium">
                            Post
                        </button>
                    </div>
                </div>

                <!-- EMPTY STATE -->
                <div class="text-center text-gray-400 py-12">
                    No posts yet. Start sharing updates 🚀
                </div>
            </div>

        </main>
    </div>
</div>

</body>
</html>