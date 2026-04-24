<?php
session_start();
require_once '../classes/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'sk_president') {
    header("Location: ../login.php");
    exit();
}

$db = new Database();
$user = $db->getUserById($_SESSION['user_id']);
$full_name = $user ? trim($user['first_name'] . ' ' . $user['last_name']) : 'User';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['event_title'])) {
    $start = $_POST['start_datetime'] . " 00:00:00";
    $end   = $_POST['end_datetime'] . " 23:59:59";

    $db->createEvent(
        $_POST['event_title'],
        $_POST['event_type'],
        $start,
        $end,
        $_POST['description'],
        $_SESSION['user_id']
    );

    header("Location: calendar.php");
    exit();
}

$events = $db->getEvents();
$upcomingEvents = $db->getUpcomingEvents(5);

$calendarEvents = array_map(fn($event) => [
    'id' => $event['event_id'],
    'title' => $event['title'],
    'start' => $event['start_datetime'],
    'end' => $event['end_datetime'],
    'className' => match($event['event_type']) {
        'meeting'  => 'bg-blue-700',
        'program'  => 'bg-green-600',
        'deadline' => 'bg-red-600',
        default    => 'bg-fuchsia-500'
    }
], $events);

$menuItems = [
    ['home.php', '🏠', 'Home'],
    ['dashboard.php', '📊', 'Dashboard'],
    ['consolidation.php', '📁', 'Consolidation'],
    ['module.php', '⚙️', 'Module Management'],
    ['announcements.php', '📢', 'Announcements'],
    ['calendar.php', '📅', 'Calendar'],
    ['chat.php', '💬', 'Chat'],
    ['meetings.php', '📞', 'Meetings'],
    ['rankings.php', '🏆', 'Rankings'],
    ['analytics.php', '📈', 'Analytics'],
    ['leadership.php', '👥', 'Leadership'],
    ['archive.php', '🗂️', 'Archive'],
    ['user_management.php', '👤', 'User Management'],
];

$currentPage = basename($_SERVER['PHP_SELF']);

$legendItems = [
    ['bg-blue-700', 'Meeting'],
    ['bg-green-600', 'Event'],
    ['bg-red-600', 'Deadline'],
    ['bg-fuchsia-500', 'Training']
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Calendar</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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

    <div class="w-64 bg-red-600 text-white flex flex-col p-3 overflow-y-auto border-r border-red-700">
        <div class="flex items-center gap-2 mb-3">
            <img src="https://cdn-icons-png.flaticon.com/512/3135/3135715.png" class="w-7 h-7" alt="logo">
            <h2 class="text-base font-bold">SK 360°</h2>
        </div>

        <div class="bg-red-500 rounded-lg p-2 flex items-center gap-2 mb-3 shadow text-xs">
            <div class="bg-yellow-400 text-red-600 p-1 rounded-full text-sm">👤</div>
            <div>
                <p class="font-semibold text-xs">SK President</p>
                <p class="text-xs opacity-80">Active Role</p>
            </div>
        </div>

        <nav class="space-y-1 text-xs">
            <?php foreach ($menuItems as [$link, $icon, $label]): ?>
                <?php $isActive = $currentPage === $link; ?>
                <a href="<?= $link ?>" class="flex items-center gap-2 p-2 rounded-lg <?= $isActive ? 'bg-red-500' : 'hover:bg-red-500 transition' ?>">
                    <span class="<?= $isActive ? 'bg-yellow-400 text-red-600' : 'bg-red-400' ?> p-1 rounded text-sm"><?= $icon ?></span>
                    <span class="<?= $isActive ? 'text-yellow-300 font-semibold' : '' ?> text-xs"><?= $label ?></span>
                </a>
            <?php endforeach; ?>
        </nav>
    </div>

    <div class="flex-1 flex flex-col">

        <div class="bg-red-600 text-white px-6 py-3 flex justify-between items-center shadow-md">
            <input type="text" placeholder="Search" class="px-4 py-2 rounded-full text-black w-1/3 focus:outline-none text-sm">

            <div class="flex items-center gap-3 relative">
                <div class="relative">
                    <button id="notifBtn" type="button" class="text-xl hover:bg-red-500 p-2 rounded-lg transition">🔔</button>
                    <div id="notifDropdown" class="hidden absolute right-0 mt-3 w-72 bg-white rounded-2xl shadow-xl border z-50 overflow-hidden">
                        <div class="px-4 py-3 font-semibold border-b text-gray-800">Notifications</div>
                        <div class="max-h-64 overflow-y-auto">
                            <div class="px-4 py-3 hover:bg-gray-100 text-sm text-gray-700 font-normal">No notifications yet</div>
                        </div>
                    </div>
                </div>

                <div class="relative">
                    <button id="userMenuBtn" type="button" class="flex items-center gap-2 hover:bg-red-500 px-3 py-2 rounded-lg transition">
                        <span class="font-semibold text-sm"><?= htmlspecialchars($full_name) ?></span>
                    </button>
                    <div id="userDropdown" class="hidden absolute right-0 mt-3 w-64 bg-white rounded-2xl shadow-xl border overflow-hidden z-50">
                        <div class="px-5 py-4 font-semibold text-gray-800 border-b">My Account</div>
                        <a href="profile.php" class="flex items-center gap-3 px-5 py-3 hover:bg-gray-100 transition text-sm text-gray-700">
                            <span>👤</span> Profile Settings
                        </a>
                        <a href="../auth/logout.php" class="flex items-center gap-3 px-5 py-3 text-red-500 hover:bg-gray-100 transition text-sm">
                            <span>↩️</span> Log Out
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <main class="flex-1 overflow-y-auto p-8 bg-gray-50">
            <div class="flex items-end justify-between mb-8">
                <div>
                    <h2 class="text-4xl font-bold text-gray-900 leading-tight">Event Calendar</h2>
                    <p class="text-gray-500 mt-2 text-lg">Schedule and coordinate SK events, meetings, and deadlines</p>
                </div>

                <button id="openEventModalBtn" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm font-semibold shadow-sm transition">
                    ＋ Add Event
                </button>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
                <div class="lg:col-span-3 bg-white rounded-2xl border border-gray-200 p-6 shadow-sm">
                    <div id="calendar"></div>
                </div>

                <div class="space-y-6">
                    <div class="bg-white p-5 rounded-2xl border border-gray-200 shadow-sm">
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

                    <div class="bg-white p-5 rounded-2xl border border-gray-200 shadow-sm">
                        <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-4">Upcoming</h3>
                        <div class="space-y-4">
                            <?php if (empty($upcomingEvents)): ?>
                                <p class="text-xs text-gray-400">No events found.</p>
                            <?php else: ?>
                                <?php foreach ($upcomingEvents as $event): ?>
                                    <div class="group border-l-4 border-red-500 pl-3 py-1 hover:bg-gray-50 transition cursor-pointer">
                                        <p class="text-[11px] font-black text-gray-800 uppercase leading-none"><?= htmlspecialchars($event['title']) ?></p>
                                        <p class="text-[9px] text-gray-500 mt-1"><?= date('M d, Y', strtotime($event['start_datetime'])) ?></p>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<div id="eventModal" class="fixed inset-0 bg-black/60 hidden items-center justify-center z-[100] px-4 backdrop-blur-sm">
    <div class="bg-white w-full max-w-lg rounded-[30px] shadow-2xl p-8 relative animate-in fade-in zoom-in duration-300">
        <button id="closeEventModalBtn" class="absolute top-6 right-6 text-gray-400 hover:text-red-600 text-2xl transition">&times;</button>
        
        <h2 class="text-2xl font-black text-gray-900 uppercase mb-1">New Event</h2>
        <p class="text-gray-500 text-xs mb-6">Fill in the details to create a new activity.</p>

        <form action="" method="POST" class="space-y-4">
            <div>
                <label class="block text-[10px] font-black text-gray-400 uppercase mb-1 ml-1 tracking-widest">Event Title</label>
                <input type="text" name="event_title" class="w-full px-4 py-3 rounded-xl border border-gray-100 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-red-400 outline-none transition text-sm" required>
            </div>

            <div>
                <label class="block text-[10px] font-black text-gray-400 uppercase mb-1 ml-1 tracking-widest">Type</label>
                <select name="event_type" class="w-full px-4 py-3 rounded-xl border border-gray-100 bg-gray-50 outline-none text-sm focus:bg-white transition">
                    <option value="meeting">Meeting</option>
                    <option value="program">Program</option>
                    <option value="deadline">Deadline</option>
                    <option value="other">Other</option>
                </select>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase mb-1 ml-1 tracking-widest">Start Date</label>
                    <input type="date" name="start_datetime" class="w-full px-4 py-3 rounded-xl border border-gray-100 bg-gray-50 outline-none text-sm focus:bg-white transition" required>
                </div>
                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase mb-1 ml-1 tracking-widest">End Date</label>
                    <input type="date" name="end_datetime" class="w-full px-4 py-3 rounded-xl border border-gray-100 bg-gray-50 outline-none text-sm focus:bg-white transition" required>
                </div>
            </div>

            <div>
                <label class="block text-[10px] font-black text-gray-400 uppercase mb-1 ml-1 tracking-widest">Description</label>
                <textarea name="description" rows="3" class="w-full px-4 py-3 rounded-xl border border-gray-100 bg-gray-50 outline-none text-sm focus:bg-white transition"></textarea>
            </div>

            <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white font-black py-4 rounded-2xl shadow-lg transition uppercase tracking-widest mt-4 text-xs">
                Publish to Calendar
            </button>
        </form>
    </div>
</div>

<script>
    const notifBtn = document.getElementById('notifBtn');
    const notifDropdown = document.getElementById('notifDropdown');
    const userMenuBtn = document.getElementById('userMenuBtn');
    const userDropdown = document.getElementById('userDropdown');
    const openEventModalBtn = document.getElementById('openEventModalBtn');
    const closeEventModalBtn = document.getElementById('closeEventModalBtn');
    const eventModal = document.getElementById('eventModal');

    notifBtn.onclick = (e) => { e.stopPropagation(); notifDropdown.classList.toggle('hidden'); userDropdown.classList.add('hidden'); };
    userMenuBtn.onclick = (e) => { e.stopPropagation(); userDropdown.classList.toggle('hidden'); notifDropdown.classList.add('hidden'); };
    
    document.onclick = (e) => {
        if (!notifBtn.contains(e.target) && !notifDropdown.contains(e.target)) notifDropdown.classList.add('hidden');
        if (!userMenuBtn.contains(e.target) && !userDropdown.contains(e.target)) userDropdown.classList.add('hidden');
    };

    openEventModalBtn.onclick = () => { eventModal.classList.remove('hidden'); eventModal.classList.add('flex'); };
    closeEventModalBtn.onclick = () => { eventModal.classList.add('hidden'); eventModal.classList.remove('flex'); };

    document.addEventListener('DOMContentLoaded', () => {
        const calendar = new FullCalendar.Calendar(document.getElementById('calendar'), {
            initialView: 'dayGridMonth',
            height: 'auto',
            headerToolbar: { left: 'prev,next today', center: 'title', right: '' },
            events: <?= json_encode($calendarEvents) ?>,
            eventClick: info => {
                Swal.fire({
                    title: 'Delete Event?',
                    text: `Are you sure you want to remove "${info.event.title}"?`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc2626',
                    confirmButtonText: 'Delete'
                }).then(result => {
                    if (result.isConfirmed) {
                        fetch('delete_slot.php', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                            body: 'id=' + info.event.id + '&type=event'
                        }).then(() => location.reload());
                    }
                });
            }
        });
        calendar.render();
    });
</script>

</body>
</html>