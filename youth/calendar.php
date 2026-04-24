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

$legendItems = [
    ['bg-blue-700', 'Meeting'],
    ['bg-green-600', 'Event/Program'],
    ['bg-red-600', 'Deadline'],
    ['bg-fuchsia-500', 'Other Activities']
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Calendar | SK 360°</title>
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
            <a href="calendar.php" class="flex items-center gap-2 bg-red-500 p-2 rounded-lg">
                <span class="bg-yellow-400 text-red-600 p-1 rounded">📅</span>
                <span class="text-yellow-300 font-semibold">Event Calendar</span>
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

        <div class="p-8 overflow-y-auto h-full bg-gray-50">
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-800 uppercase">Event Calendar</h1>
                <p class="text-gray-500">Official schedule of activities and programs</p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
                <div class="lg:col-span-3 bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                    <div id="calendar"></div>
                </div>

                <div class="space-y-6">
                    <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm">
                        <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-4">Legend</h3>
                        <div class="space-y-3">
                            <?php foreach ($legendItems as [$color, $label]): ?>
                                <div class="flex items-center gap-3">
                                    <span class="w-3 h-3 rounded-full <?= $color ?>"></span>
                                    <span class="text-xs font-bold text-gray-600"><?= $label ?></span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm">
                        <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-4">Upcoming Agendas</h3>
                        <div class="space-y-4">
                            <?php if (empty($upcomingEvents)): ?>
                                <p class="text-xs text-gray-400 italic">No scheduled events.</p>
                            <?php else: ?>
                                <?php foreach ($upcomingEvents as $e): ?>
                                    <div class="border-l-4 border-red-500 pl-3">
                                        <p class="text-[11px] font-black text-gray-800 uppercase leading-none"><?= htmlspecialchars($e['title']) ?></p>
                                        <p class="text-[9px] text-gray-500 mt-1"><?= date('M d, Y', strtotime($e['start_datetime'])) ?></p>
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
    // Profile Dropdown Logic
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

    // FullCalendar Init
    document.addEventListener('DOMContentLoaded', () => {
        const calendarEl = document.getElementById('calendar');
        const calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            height: 'auto',
            headerToolbar: { left: 'prev,next today', center: 'title', right: '' },
            events: <?= json_encode($calendarEvents) ?>,
            eventClick: info => {
                alert("Event: " + info.event.title);
            }
        });
        calendar.render();
    });
</script>

</body>
</html>