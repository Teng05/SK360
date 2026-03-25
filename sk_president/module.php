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
        <main class="flex-1 overflow-y-auto p-8 bg-[#f8fafc]">

            <!-- HEADER -->
            <div class="flex items-start justify-between mb-8">
                <div>
                    <h2 class="text-[38px] font-bold text-gray-900 leading-tight">
                        Submission Slot Management
                    </h2>
                    <p class="text-gray-500 mt-2 text-base">
                        Create and manage submission periods for Accomplishment Reports and Budget Documents
                    </p>
                </div>

                <button id="openModalBtn" class="bg-red-600 hover:bg-red-700 text-white px-5 py-3 rounded-lg text-sm font-semibold shadow-sm">
                       ＋ Create Submission Slot </button>
            </div>

                <!-- CREATE SUBMISSION SLOT MODAL -->
    <div id="submissionModal" class="fixed inset-0 bg-black/40 hidden items-center justify-center z-50 px-4">
        <div class="bg-white w-full max-w-3xl rounded-[24px] border-2 border-blue-500 shadow-2xl p-8 relative">

            <!-- CLOSE BUTTON -->
            <button id="closeModalBtn" 
                class="absolute top-4 right-5 text-gray-500 hover:text-red-600 text-2xl font-bold">
                &times;
            </button>

            <!-- MODAL HEADER -->
            <h2 class="text-4xl font-bold text-gray-900 mb-2">
                Create New Submission Slot
            </h2>
            <p class="text-gray-600 mb-8 text-base">
                Set up a new submission period for SK officials to submit reports
            </p>

            <!-- FORM -->
            <form action="" method="POST" class="space-y-6">

                <!-- SUBMISSION TYPE -->
                <div>
                    <label class="block text-lg font-semibold text-gray-900 mb-2">
                        Submission Type
                    </label>
                    <input type="text" name="submission_type"
                        class="w-full h-14 px-4 rounded-xl border border-red-300 bg-red-50 focus:outline-none focus:ring-2 focus:ring-red-400"
                        value="">
                </div>

                <!-- SUBMISSION TITLE -->
                <div>
                    <label class="block text-lg font-semibold text-gray-900 mb-2">
                        Submission Title
                    </label>
                    <input type="text" name="submission_title"
                        class="w-full h-14 px-4 rounded-xl border border-red-300 bg-red-50 focus:outline-none focus:ring-2 focus:ring-red-400"
                        value="">
                </div>

                <!-- DESCRIPTION -->
                <div>
                    <label class="block text-lg font-semibold text-gray-900 mb-2">
                        Description
                    </label>
                    <input type="text" name="description"
                        class="w-full h-14 px-4 rounded-xl border border-red-300 bg-red-50 focus:outline-none focus:ring-2 focus:ring-red-400"
                        value="">
                </div>

                <!-- DATES -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-lg font-semibold text-gray-900 mb-2">
                            Start Date
                        </label>
                        <input type="date" name="start_date"
                            class="w-full h-14 px-4 rounded-xl border border-red-300 bg-red-50 focus:outline-none focus:ring-2 focus:ring-red-400">
                    </div>

                    <div>
                        <label class="block text-lg font-semibold text-gray-900 mb-2">
                            End Date
                        </label>
                        <input type="date" name="end_date"
                            class="w-full h-14 px-4 rounded-xl border border-red-300 bg-red-50 focus:outline-none focus:ring-2 focus:ring-red-400">
                    </div>
                </div>

                <!-- SUBMIT BUTTON -->
                <button type="submit"
                    class="w-full bg-red-600 hover:bg-red-700 text-white text-2xl font-bold py-4 rounded-2xl transition">
                    Create Slot
                </button>

            </form>
        </div>
    </div>

    <script>
    const openModalBtn = document.getElementById('openModalBtn');
    const closeModalBtn = document.getElementById('closeModalBtn');
    const submissionModal = document.getElementById('submissionModal');

    openModalBtn.addEventListener('click', () => {
        submissionModal.classList.remove('hidden');
        submissionModal.classList.add('flex');
    });

    closeModalBtn.addEventListener('click', () => {
        submissionModal.classList.add('hidden');
        submissionModal.classList.remove('flex');
    });

    submissionModal.addEventListener('click', (e) => {
        if (e.target === submissionModal) {
            submissionModal.classList.add('hidden');
            submissionModal.classList.remove('flex');
        }
    });
</script>

            <!-- SUMMARY CARDS -->
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-5 mb-10">

                <!-- CARD 1 -->
                <div class="bg-white rounded-2xl border border-red-400 p-5">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-sm text-gray-500 mb-2">Total Slots</p>
                            <h3 class="text-4xl font-bold text-gray-900 leading-none">0</h3>
                        </div>
                        <div class="w-12 h-12 rounded-xl bg-red-50 flex items-center justify-center text-red-500 text-xl">
                            📋
                        </div>
                    </div>
                </div>

                <!-- CARD 2 -->
                <div class="bg-white rounded-2xl border border-green-400 p-5">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-sm text-gray-500 mb-2">Open Slots</p>
                            <h3 class="text-4xl font-bold text-gray-900 leading-none">0</h3>
                        </div>
                        <div class="w-12 h-12 rounded-xl bg-green-50 flex items-center justify-center text-green-500 text-xl">
                            🔓
                        </div>
                    </div>
                </div>

                <!-- CARD 3 -->
                <div class="bg-white rounded-2xl border border-blue-400 p-5">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Current</p>
                            <p class="text-sm text-gray-500 mb-2">Submissions</p>
                            <h3 class="text-4xl font-bold text-gray-900 leading-none">0/0</h3>
                        </div>
                        <div class="w-12 h-12 rounded-xl bg-blue-50 flex items-center justify-center text-blue-500 text-xl">
                            ☑️
                        </div>
                    </div>
                </div>

                <!-- CARD 4 -->
                <div class="bg-white rounded-2xl border border-yellow-400 p-5">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-sm text-gray-500 mb-2">All-Time Total</p>
                            <h3 class="text-4xl font-bold text-gray-900 leading-none">0</h3>
                        </div>
                        <div class="w-12 h-12 rounded-xl bg-yellow-50 flex items-center justify-center text-yellow-500 text-xl">
                            👥
                        </div>
                    </div>
                </div>

            </div>

        </main>
    </div>
</div>

</body>
</html>