<?php
session_start();
require_once '../classes/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'sk_president') {
    header("Location: ../login.php");
    exit();
}

$db = new Database();
$user = $db->getUserById($_SESSION['user_id']);

$full_name = 'User';
if ($user) {
    $full_name = trim($user['first_name'] . ' ' . $user['last_name']);
}

// HANDLE CREATE SLOT
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submission_title'])) {

    $db->createSlot(
        $_POST['submission_type'],
        $_POST['submission_title'],
        $_POST['description'],
        $_POST['submission_role'],
        $_POST['start_date'],
        $_POST['end_date']
    );

    header("Location: module.php");
    exit();
}

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
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>SK 360 Dashboard</title>
<script src="https://cdn.tailwindcss.com"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
            <?php foreach ($menuItems as [$link, $icon, $label]): ?>
                <?php $isActive = $currentPage === $link; ?>
                <a href="<?= $link ?>" class="flex items-center gap-2 p-2 rounded-lg <?= $isActive ? 'bg-red-500' : 'hover:bg-red-500 transition' ?>">
                    <span class="<?= $isActive ? 'bg-yellow-400 text-red-600' : 'bg-red-400' ?> p-1 rounded text-sm"><?= $icon ?></span>
                    <span class="<?= $isActive ? 'text-yellow-300 font-semibold' : '' ?> text-xs"><?= $label ?></span>
                </a>
            <?php endforeach; ?>
        </nav>

</div>

    <!-- MAIN -->
    <div class="flex-1 flex flex-col">

        <!-- TOPBAR -->
        <div class="bg-red-600 text-white px-6 py-3 flex justify-between items-center shadow">
            <input
                type="text"
                placeholder="Search..."
                class="px-4 py-2 rounded-full text-black w-1/3 focus:outline-none"
            >

            <div class="flex items-center gap-3 relative">

                <!-- NOTIFICATION -->
                <div class="relative">
                    <button id="notifBtn" type="button" class="text-xl hover:bg-red-500 p-2 rounded-lg transition">
                        🔔
                    </button>

                    <div id="notifDropdown" class="hidden absolute right-0 mt-3 w-72 bg-white rounded-2xl shadow-xl border z-50 overflow-hidden">
                        <div class="px-4 py-3 font-semibold border-b text-gray-800">
                            Notifications
                        </div>

                        <div class="max-h-64 overflow-y-auto">
                            <div class="px-4 py-3 hover:bg-gray-100 text-sm text-gray-700">
                                No notifications yet
                            </div>
                        </div>
                    </div>
                </div>

                <!-- USER MENU -->
                <div class="relative">
                    <button id="userMenuBtn" type="button" class="flex items-center gap-2 hover:bg-red-500 px-3 py-2 rounded-lg transition">
                        <span class="font-semibold"><?= htmlspecialchars($full_name) ?></span>
                    </button>

                    <div id="userDropdown" class="hidden absolute right-0 mt-3 w-64 bg-white rounded-2xl shadow-xl border overflow-hidden z-50">
                        <div class="px-5 py-4 font-semibold text-gray-800 border-b">
                            My Account
                        </div>

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

           <form id="slotForm" action="" method="POST" class="space-y-6">

    <div>
        <label class="block text-lg font-semibold text-gray-900 mb-2">
            Submission Type
        </label>
        <input type="text" id="submissionType" name="submission_type"
            class="w-full h-14 px-4 rounded-xl border border-red-300 bg-red-50 focus:outline-none focus:ring-2 focus:ring-red-400"
            value="">
    </div>

    <div>
        <label class="block text-lg font-semibold text-gray-900 mb-2">
            Submission Title
        </label>
        <input type="text" id="submissionTitle" name="submission_title"
            class="w-full h-14 px-4 rounded-xl border border-red-300 bg-red-50 focus:outline-none focus:ring-2 focus:ring-red-400"
            value="">
    </div>

    <div>
        <label class="block text-lg font-semibold text-gray-900 mb-2">
            Description
        </label>
        <input type="text" id="submissionDescription" name="description"
            class="w-full h-14 px-4 rounded-xl border border-red-300 bg-red-50 focus:outline-none focus:ring-2 focus:ring-red-400"
            value="">
    </div>

    <div>
        <label class="block text-lg font-semibold text-gray-900 mb-2">
            Who Can Submit
        </label>
        <select id="submissionRole" name="submission_role"
            class="w-full h-14 px-4 rounded-xl border border-red-300 bg-red-50 focus:outline-none focus:ring-2 focus:ring-red-400">
            <option value="SK Chairman">SK Chairman</option>
            <option value="SK Secretary">SK Secretary</option>
            <option value="Both">SK Chairman & SK Secretary</option>
        </select>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
        <div>
            <label class="block text-lg font-semibold text-gray-900 mb-2">
                Start Date
            </label>
            <input type="date" id="startDate" name="start_date"
                class="w-full h-14 px-4 rounded-xl border border-red-300 bg-red-50 focus:outline-none focus:ring-2 focus:ring-red-400">
        </div>

        <div>
            <label class="block text-lg font-semibold text-gray-900 mb-2">
                End Date
            </label>
            <input type="date" id="endDate" name="end_date"
                class="w-full h-14 px-4 rounded-xl border border-red-300 bg-red-50 focus:outline-none focus:ring-2 focus:ring-red-400">
        </div>
    </div>

    <button type="submit"
        class="w-full bg-red-600 hover:bg-red-700 text-white text-2xl font-bold py-4 rounded-2xl transition">
        Create Slot
    </button>

</form>
        </div>
    </div>



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
            <!-- OPEN SUBMISSION SLOTS -->
<?php $slots = $db->getSlots(); ?>

<div id="slotContainer" class="grid grid-cols-1 xl:grid-cols-2 gap-5">

<?php if (empty($slots)): ?>
    <div id="emptySlotState" class="bg-white rounded-2xl border border-dashed border-gray-300 p-8 text-center text-gray-400">
        No submission slots yet.
    </div>
<?php else: ?>

<?php foreach ($slots as $slot): ?>
<div class="bg-white rounded-2xl border border-green-400 p-6 min-h-[280px]">

    <div class="flex justify-between items-start mb-5">

        <div>
            <p class="text-xs text-gray-400"><?= $slot['submission_type'] ?></p>
            <h4 class="text-lg font-medium"><?= $slot['title'] ?></h4>
            <p class="text-sm text-gray-400"><?= $slot['description'] ?></p>
        </div>

        <!-- DELETE BUTTON -->
        <button onclick="deleteSlot(<?= $slot['slot_id'] ?>)" 
            class="text-red-500 text-xl hover:text-red-700">
            ✕
        </button>

    </div>

    <div class="text-sm text-gray-500 mb-4">
        📅 <?= $slot['start_date'] ?> - <?= $slot['end_date'] ?>
    </div>

    <span class="text-xs bg-gray-100 px-2 py-1 rounded">
        <?= $slot['role'] ?>
    </span>

    <div class="mt-4 text-green-600 text-xs">🔓 Open</div>

</div>
<?php endforeach; ?>

<?php endif; ?>

</div>

        </main>
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

    function deleteSlot(id) {
        Swal.fire({
            title: 'Delete Slot?',
            text: "This will be permanently removed.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            confirmButtonText: 'Delete'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch('delete_slot.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: 'id=' + id
                })
                .then(res => res.text())
                .then(() => {
                    location.reload();
                });
            }
        });
    }
</script>
</body>
</html>