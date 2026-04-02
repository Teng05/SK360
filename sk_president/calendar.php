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
    'end' => $event['end_datetime']
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

$typeColors = [
    'meeting' => 'bg-blue-700',
    'program' => 'bg-green-600',
    'deadline' => 'bg-red-600',
    'other' => 'bg-fuchsia-500'
];

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
        .fc .fc-toolbar-title {
            font-size: 1rem;
            font-weight: 600;
            color: #374151;
        }
        .fc .fc-button {
            background: #f3f4f6 !important;
            border: none !important;
            color: #6b7280 !important;
            box-shadow: none !important;
            padding: 0.35rem 0.65rem !important;
        }
        .fc .fc-button:hover {
            background: #e5e7eb !important;
        }
        .fc .fc-daygrid-day {
            background: #fff;
        }
        .fc .fc-daygrid-day-frame {
            min-height: 90px;
        }
        .fc-theme-standard td,
        .fc-theme-standard th,
        .fc-theme-standard .fc-scrollgrid {
            border-color: #e5e7eb;
        }
        .fc .fc-col-header-cell-cushion,
        .fc .fc-daygrid-day-number {
            color: #6b7280;
            font-size: 12px;
            text-decoration: none !important;
        }
        .fc-event {
            border: none !important;
            padding: 2px 6px !important;
            border-radius: 6px !important;
            font-size: 11px !important;
        }
    </style>
</head>
<body class="bg-[#f1f5f9] overflow-hidden">

<div class="flex h-screen">

    <div class="w-64 bg-red-600 text-white flex flex-col p-3 overflow-y-auto">
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
        <div class="bg-red-600 text-white px-6 py-3 flex justify-between items-center shadow">
            <input type="text" placeholder="Search" class="px-4 py-2 rounded-full text-black w-1/3 focus:outline-none">

            <div class="flex items-center gap-3 relative">
                <div class="relative">
                    <button id="notifBtn" type="button" class="text-xl hover:bg-red-500 p-2 rounded-lg transition">🔔</button>
                    <div id="notifDropdown" class="hidden absolute right-0 mt-3 w-72 bg-white rounded-2xl shadow-xl border z-50 overflow-hidden">
                        <div class="px-4 py-3 font-semibold border-b text-gray-800">Notifications</div>
                        <div class="max-h-64 overflow-y-auto">
                            <div class="px-4 py-3 hover:bg-gray-100 text-sm text-gray-700">No notifications yet</div>
                        </div>
                    </div>
                </div>

                <div class="relative">
                    <button id="userMenuBtn" type="button" class="flex items-center gap-2 hover:bg-red-500 px-3 py-2 rounded-lg transition">
                        <span class="font-semibold"><?= htmlspecialchars($full_name) ?></span>
                    </button>

                    <div id="userDropdown" class="hidden absolute right-0 mt-3 w-64 bg-white rounded-2xl shadow-xl border overflow-hidden z-50">
                        <div class="px-5 py-4 font-semibold text-gray-800 border-b">My Account</div>
                        <a href="profile.php" class="flex items-center gap-3 px-5 py-3 hover:bg-gray-100 transition">
                            <span>👤</span>
                            <span class="text-gray-700">Profile Settings</span>
                        </a>
                        <a href="../auth/logout.php" class="flex items-center gap-3 px-5 py-3 text-red-500 hover:bg-gray-100 transition">
                            <span>↩️</span>
                            <span>Log Out</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <main class="flex-1 overflow-y-auto p-8 bg-[#f8fafc]">
            <div class="flex items-start justify-between mb-6">
                <div>
                    <h2 class="text-[38px] font-bold text-gray-900 leading-tight">Event Calendar</h2>
                    <p class="text-gray-500 mt-2 text-base">Schedule and coordinate SK events, meetings, and deadlines</p>
                </div>

                <button id="openEventModalBtn" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm font-semibold shadow-sm">
                    ＋ Add Event
                </button>
            </div>

            <div class="bg-white rounded-xl border border-gray-200 px-4 py-3 mb-5 flex flex-wrap gap-6 text-sm text-gray-700">
                <?php foreach ($legendItems as [$color, $label]): ?>
                    <div class="flex items-center gap-2">
                        <span class="w-4 h-4 rounded <?= $color ?> inline-block"></span>
                        <span><?= $label ?></span>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="bg-white rounded-2xl border border-gray-200 p-4 mb-5">
                <div id="calendar"></div>
            </div>

            <div class="space-y-3">
                <?php if (empty($upcomingEvents)): ?>
                    <div class="text-center text-gray-400 py-6">No upcoming events yet.</div>
                <?php else: ?>
                    <?php foreach ($upcomingEvents as $event): ?>
                        <div class="flex items-center justify-between border border-gray-200 rounded-xl px-4 py-3">
                            <div class="flex items-start gap-3">
                                <span class="w-2.5 h-2.5 mt-2 rounded-full <?= $typeColors[$event['event_type']] ?? 'bg-gray-500' ?>"></span>
                                <div>
                                    <p class="text-sm font-medium text-gray-800"><?= htmlspecialchars($event['title']) ?></p>
                                    <p class="text-xs text-gray-400"><?= htmlspecialchars($event['start_datetime']) ?></p>
                                </div>
                            </div>
                            <span class="text-xs text-gray-500"><?= htmlspecialchars($event['event_type']) ?></span>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <div id="eventModal" class="fixed inset-0 bg-black/40 hidden items-center justify-center z-50 px-4">
                <div class="bg-white w-full max-w-2xl rounded-[24px] shadow-2xl p-8 relative">
                    <button id="closeEventModalBtn" class="absolute top-4 right-5 text-gray-500 hover:text-red-600 text-2xl font-bold">
                        &times;
                    </button>

                    <h2 class="text-3xl font-bold text-gray-900 mb-2">Add Event</h2>
                    <p class="text-gray-600 mb-6 text-base">Create a calendar event for the SK calendar</p>

                    <form action="" method="POST" class="space-y-5">
                        <div>
                            <label class="block text-sm font-semibold text-gray-900 mb-2">Event Title</label>
                            <input type="text" name="event_title" class="w-full h-12 px-4 rounded-xl border border-gray-300 bg-white focus:outline-none focus:ring-2 focus:ring-red-400" required>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-900 mb-2">Event Type</label>
                            <select name="event_type" class="w-full h-12 px-4 rounded-xl border border-gray-300 bg-white focus:outline-none focus:ring-2 focus:ring-red-400">
                                <option value="meeting">Meeting</option>
                                <option value="deadline">Deadline</option>
                                <option value="program">Program</option>
                                <option value="other">Other</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-900 mb-2">Description</label>
                            <textarea name="description" rows="3" class="w-full px-4 py-3 rounded-xl border border-gray-300 bg-white focus:outline-none focus:ring-2 focus:ring-red-400"></textarea>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div>
                                <label class="block text-sm font-semibold text-gray-900 mb-2">Start Date</label>
                                <input type="date" name="start_datetime" class="w-full h-12 px-4 rounded-xl border border-gray-300 bg-white focus:outline-none focus:ring-2 focus:ring-red-400" required>
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-900 mb-2">End Date</label>
                                <input type="date" name="end_datetime" class="w-full h-12 px-4 rounded-xl border border-gray-300 bg-white focus:outline-none focus:ring-2 focus:ring-red-400">
                            </div>
                        </div>

                        <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white text-lg font-bold py-3 rounded-2xl transition">
                            Save Event
                        </button>
                    </form>
                </div>
            </div>
        </main>
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

    const toggleMenu = (btn, menu, other) => {
        btn.addEventListener('click', e => {
            e.stopPropagation();
            menu.classList.toggle('hidden');
            other.classList.add('hidden');
        });
    };

    toggleMenu(notifBtn, notifDropdown, userDropdown);
    toggleMenu(userMenuBtn, userDropdown, notifDropdown);

    document.addEventListener('click', e => {
        if (!notifBtn.contains(e.target) && !notifDropdown.contains(e.target)) notifDropdown.classList.add('hidden');
        if (!userMenuBtn.contains(e.target) && !userDropdown.contains(e.target)) userDropdown.classList.add('hidden');
    });

    const showEventModal = () => {
        eventModal.classList.remove('hidden');
        eventModal.classList.add('flex');
    };

    const hideEventModal = () => {
        eventModal.classList.add('hidden');
        eventModal.classList.remove('flex');
    };

    openEventModalBtn.addEventListener('click', showEventModal);
    closeEventModalBtn.addEventListener('click', hideEventModal);

    eventModal.addEventListener('click', e => {
        if (e.target === eventModal) hideEventModal();
    });

    const deleteItem = (id, type, title, text) => {
        Swal.fire({
            title,
            text,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            confirmButtonText: 'Delete'
        }).then(result => {
            if (result.isConfirmed) {
                fetch('delete_slot.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: 'id=' + id + '&type=' + type
                })
                .then(res => res.text())
                .then(() => location.reload());
            }
        });
    };

    document.addEventListener('DOMContentLoaded', () => {
        const calendar = new FullCalendar.Calendar(document.getElementById('calendar'), {
            initialView: 'dayGridMonth',
            height: 'auto',
            headerToolbar: {
                left: 'title',
                center: '',
                right: 'prev,next'
            },
            events: <?= json_encode($calendarEvents) ?>,
            eventClick: info => deleteItem(info.event.id, 'event', 'Delete Event?', info.event.title)
        });

        calendar.render();
    });

    function deleteSlot(id) {
        deleteItem(id, 'slot', 'Delete Slot?', 'This will be permanently removed.');
    }
</script>

</body>
</html>