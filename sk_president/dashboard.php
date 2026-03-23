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

        <!-- ACTIVE -->
        <a href="#" class="flex items-center gap-2 bg-red-500 p-2 rounded-lg">
            <span class="bg-yellow-400 text-red-600 p-1 rounded text-sm">🏠</span>
            <span class="text-yellow-300 font-semibold text-xs">Home</span>
        </a>

        <a href="#" class="flex items-center gap-2 hover:bg-red-500 p-2 rounded-lg transition">
            <span class="bg-red-400 p-1 rounded text-sm">📊</span>
            <span class="text-xs">Dashboard</span>
        </a>

        <a href="#" class="flex items-center gap-2 hover:bg-red-500 p-2 rounded-lg">
            <span class="bg-red-400 p-1 rounded text-sm">📁</span>
            <span class="text-xs">Consolidation</span>
        </a>

        <a href="#" class="flex items-center gap-2 hover:bg-red-500 p-2 rounded-lg">
            <span class="bg-red-400 p-1 rounded text-sm">⚙️</span>
            <span class="text-xs">Module Management</span>
        </a>

        <a href="#" class="flex items-center gap-2 hover:bg-red-500 p-2 rounded-lg">
            <span class="bg-red-400 p-1 rounded text-sm">📢</span>
            <span class="text-xs">Announcements</span>
        </a>

        <a href="#" class="flex items-center gap-2 hover:bg-red-500 p-2 rounded-lg">
            <span class="bg-red-400 p-1 rounded text-sm">📅</span>
            <span class="text-xs">Calendar</span>
        </a>

        <a href="#" class="flex items-center gap-2 hover:bg-red-500 p-2 rounded-lg">
            <span class="bg-red-400 p-1 rounded text-sm">💬</span>
            <span class="text-xs">Chat</span>
        </a>

        <a href="#" class="flex items-center gap-2 hover:bg-red-500 p-2 rounded-lg">
            <span class="bg-red-400 p-1 rounded text-sm">📞</span>
            <span class="text-xs">Meetings</span>
        </a>

        <a href="#" class="flex items-center gap-2 hover:bg-red-500 p-2 rounded-lg">
            <span class="bg-red-400 p-1 rounded text-sm">🏆</span>
            <span class="text-xs">Rankings</span>
        </a>

        <a href="#" class="flex items-center gap-2 hover:bg-red-500 p-2 rounded-lg">
            <span class="bg-red-400 p-1 rounded text-sm">📈</span>
            <span class="text-xs">Analytics</span>
        </a>

        <a href="#" class="flex items-center gap-2 hover:bg-red-500 p-2 rounded-lg">
            <span class="bg-red-400 p-1 rounded text-sm">👥</span>
            <span class="text-xs">Leadership</span>
        </a>

        <a href="#" class="flex items-center gap-2 hover:bg-red-500 p-2 rounded-lg">
            <span class="bg-red-400 p-1 rounded text-sm">🗂️</span>
            <span class="text-xs">Archive</span>
        </a>

        <a href="#" class="flex items-center gap-2 hover:bg-red-500 p-2 rounded-lg">
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
        <div class="p-6 overflow-y-auto">

            <!-- GREETING -->
            <h1 class="text-2xl font-bold mb-4">
                Good morning, <span>User</span>!
            </h1>

            <!-- CARDS -->
            <div class="grid grid-cols-4 gap-4 mb-6">

                <div class="bg-red-500 text-white p-5 rounded-xl shadow">
                    <h2 class="text-2xl font-bold">0</h2>
                    <p class="text-sm">Reports Submitted</p>
                </div>

                <div class="bg-blue-500 text-white p-5 rounded-xl shadow">
                    <h2 class="text-2xl font-bold">0%</h2>
                    <p class="text-sm">Community Engagement</p>
                </div>

                <div class="bg-yellow-500 text-white p-5 rounded-xl shadow">
                    <h2 class="text-2xl font-bold">0</h2>
                    <p class="text-sm">Pending Reviews</p>
                </div>

                <div class="bg-green-500 text-white p-5 rounded-xl shadow">
                    <h2 class="text-2xl font-bold">0</h2>
                    <p class="text-sm">Upcoming Events</p>
                </div>

            </div>

            <!-- QUICK ACTIONS -->
            <div class="bg-white p-5 rounded-xl shadow mb-6">
                <h2 class="font-semibold mb-3">Quick Actions</h2>
                <div class="flex gap-3 flex-wrap">
                    <button class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg">
                        Create Announcement
                    </button>
                    <button class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-lg">
                        Manage Events
                    </button>
                    <button class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg">
                        Schedule Meeting
                    </button>
                </div>
            </div>

            <!-- ACTIVITY FEED -->
            <div class="bg-white p-6 rounded-xl shadow">

                <div class="flex justify-between items-center mb-4">
                    <h2 class="font-semibold">Activity Feed</h2>
                    <span class="text-xs text-gray-400">Live</span>
                </div>

                <!-- POST BOX -->
                <div class="border rounded-lg p-4 mb-4">

                    <textarea 
                        class="w-full border rounded p-3 resize-none focus:outline-none focus:ring-2 focus:ring-red-400"
                        rows="3"
                        placeholder="What's on your mind?"
                    ></textarea>

                    <div class="flex justify-end mt-3">
                        <button class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg">
                            Post
                        </button>
                    </div>

                </div>

                <!-- EMPTY STATE -->
                <div class="text-center text-gray-400 py-10">
                    No posts yet. Start sharing updates 🚀
                </div>

            </div>

        </div>

    </div>

</div>

</body>
</html>