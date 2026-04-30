<?php
session_start();
require_once '../classes/database.php';

$db = new Database();
$user_data = $db->getUserById($_SESSION['user_id']);

if (!$user_data) {
    header("Location: ../login.php");
    exit();
}

// 2. Variables para sa UI
$full_name = $user_data['first_name'] . ' ' . $user_data['last_name'];
$barangay_id = $user_data['barangay_id'];
$barangay_name = $db->getBarangayName($barangay_id); // Siguraduhing may ganitong function sa Database class

// 3. Initials Fallback
$initials = strtoupper(substr($user_data['first_name'], 0, 1) . substr($user_data['last_name'], 0, 1));

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
$user_name = $user_data['first_name']; // For greeting

// 2. Fetch Events
$events = $db->getEvents(); 
$upcomingEvents = $db->getUpcomingEvents(5);

// 3. Format for FullCalendar
$calendarEvents = array_map(fn($event) => [
    'id'    => $event['event_id'],
    'title' => $event['title'],
    'start' => $event['start_datetime'],
    'end'   => $event['end_datetime'],
    'className' => match($event['event_type']) {
        'meeting'  => 'bg-blue-700',
        'program'  => 'bg-green-600',
        'deadline' => 'bg-red-600',
        default    => 'bg-fuchsia-500'
    }
], $events);

// Legend Items
$legendItems = [
    ['bg-blue-700', 'Official Meeting'],
    ['bg-green-600', 'City-wide Program'],
    ['bg-red-600', 'Report Deadline'],
    ['bg-fuchsia-500', 'Other Activities']
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SK Chairman Dashboard - Calendar</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js"></script>
    <style>
        .fc .fc-toolbar-title { font-size: 1.1rem; font-weight: 700; color: #1f2937; text-transform: uppercase; }
        .fc .fc-button { background: #ef4444 !important; border: none !important; color: #fff !important; font-size: 0.8rem !important; text-transform: uppercase; font-weight: bold; }
        .fc .fc-button:hover { background: #dc2626 !important; }
        .fc-event { border: none !important; padding: 3px 5px !important; border-radius: 4px !important; font-size: 10px !important; cursor: pointer; }
        .fc .fc-daygrid-day-number { color: #6b7280; font-size: 12px; text-decoration: none !important; }
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
            <a href="calendar.php" class="flex items-center gap-2 bg-red-500 p-2 rounded-lg">
                <span class="bg-yellow-400 text-red-600 p-1 rounded text-sm">📅</span>
                <span class="text-yellow-300 font-semibold">Calendar</span>
            </a>
            <a href="chat.php" class="flex items-center gap-2 hover:bg-red-500 p-2 rounded-lg">
                <span class="bg-red-400 p-1 rounded text-sm">💬</span>
                <span>Chat</span>
            </a>
            <a href="meetings.php" class="flex items-center gap-2 hover:bg-red-500 p-2 rounded-lg">
                <span class="bg-red-400 p-1 rounded text-sm">📞</span>
                <span>Meetings</span>
            </a>
            <a href="rankings.php" class="flex items-center gap-2 hover:bg-red-500 p-2 rounded-lg">
                <span class="bg-red-400 p-1 rounded text-sm">🏆</span>
                <span>Rankings</span>
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

    <div class="flex-1 flex flex-col">

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

        <div class="p-6 overflow-y-auto bg-gray-50 h-full">
            <div class="mb-6">
                <h1 class="text-2xl font-bold text-gray-800">Event Calendar</h1>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
                <div class="lg:col-span-3 bg-white p-6 rounded-xl shadow border">
                    <div id="calendar"></div>
                </div>

                <div class="space-y-6">
                    <div class="bg-white p-5 rounded-xl border shadow-sm">
                        <h3 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-4">Legend</h3>
                        <div class="space-y-3">
                            <?php foreach ($legendItems as [$color, $label]): ?>
                                <div class="flex items-center gap-3">
                                    <span class="w-3 h-3 rounded-full <?= $color ?>"></span>
                                    <span class="text-xs font-semibold text-gray-600"><?= $label ?></span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="bg-white p-5 rounded-xl border shadow-sm">
                        <h3 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-4">Upcoming</h3>
                        <div class="space-y-4">
                            <?php if (empty($upcomingEvents)): ?>
                                <p class="text-xs text-gray-400 italic">No events scheduled.</p>
                            <?php else: ?>
                                <?php foreach ($upcomingEvents as $e): ?>
                                    <div class="border-l-4 border-red-500 pl-3 py-1">
                                        <p class="text-xs font-bold text-gray-800"><?= htmlspecialchars($e['title']) ?></p>
                                        <p class="text-[10px] text-gray-500 mt-1"><?= date('M d, Y', strtotime($e['start_datetime'])) ?></p>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Calendar Initialization
    document.addEventListener('DOMContentLoaded', () => {
        const calendarEl = document.getElementById('calendar');
        const calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            height: 'auto',
            headerToolbar: { 
                left: 'prev,next today', 
                center: 'title', 
                right: '' 
            },
            events: <?= json_encode($calendarEvents) ?>,
            eventClick: info => {
                alert("Event: " + info.event.title + "\nDate: " + info.event.start.toDateString());
            }
        });
        calendar.render();
    });

    // Dropdown Logic (Copied from home.php)
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