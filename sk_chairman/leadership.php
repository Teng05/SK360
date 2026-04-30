<?php
session_start();
require_once '../classes/database.php';

$db = new Database();

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

// Variables para sa UI
$full_name = $user_data['first_name'] . ' ' . $user_data['last_name'];
$barangay_id = $user_data['barangay_id'];
$barangay_name = $db->getBarangayName($barangay_id);
$initials = strtoupper(substr($user_data['first_name'], 0, 1) . substr($user_data['last_name'], 0, 1));

// --- CRUD LOGIC START ---

// HANDLE ADD MEMBER (to sk_council table)
if (isset($_POST['add_member'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $position = $_POST['position']; // Check mo kung nakukuha ito
    $term = $_POST['term'];

    // HANDLE ADD MEMBER
    if (isset($_POST['add_member'])) {
        if ($db->addCouncilMember($barangay_id, $name, $email, $phone, $position, $term)) {
            $_SESSION['status'] = "added"; 
            header("Location: leadership.php"); 
            exit();
        }
    }
}

// HANDLE DELETE MEMBER
if (isset($_GET['delete_id'])) {
    if ($db->deleteCouncilMember($_GET['delete_id'], $barangay_id)) {
        $_SESSION['status'] = "deleted"; 
        header("Location: leadership.php"); 
        exit();
    }
}


// --- CRUD LOGIC END ---

// Fetch combined list (Users + sk_council)
$councilMembers = $db->getCouncilByBarangay($barangay_id);

$executives = array_filter($councilMembers, function($m) {
    $pos = strtolower($m['position']);
    return str_contains($pos, 'chairman') || str_contains($pos, 'secretary') || str_contains($pos, 'treasurer');
});

$kagawads = array_filter($councilMembers, function($m) {
    $pos = strtolower($m['position']);
    return str_contains($pos, 'councilor') || str_contains($pos, 'kagawad');
});
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Leadership | SK 360°</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-gray-100 overflow-hidden">

<div id="addModal" class="hidden fixed inset-0 bg-black/50 z-[100] flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl w-full max-w-md overflow-hidden shadow-2xl">
        <div class="bg-red-600 p-6 text-white">
            <h2 class="text-xl font-black uppercase tracking-tighter">Add Council Member</h2>
            <p class="text-[10px] opacity-80 uppercase font-bold">This will add a non-login member to sk_council.</p>
        </div>
        <form method="POST" class="p-6 space-y-4">
            <div class="space-y-1">
                <label class="text-[10px] font-black text-gray-400 uppercase">Full Name</label>
                <input type="text" name="name" required class="w-full border-b-2 border-gray-100 focus:border-red-500 outline-none py-1 text-sm font-bold">
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div class="space-y-1">
                    <label class="text-[10px] font-black text-gray-400 uppercase">Email</label>
                    <input type="email" name="email" class="w-full border-b-2 border-gray-100 focus:border-red-500 outline-none py-1 text-sm font-bold">
                </div>
                <div class="space-y-1">
                    <label class="text-[10px] font-black text-gray-400 uppercase">Phone</label>
                    <input type="text" name="phone" class="w-full border-b-2 border-gray-100 focus:border-red-500 outline-none py-1 text-sm font-bold">
                </div>
            </div>
            <div class="space-y-1">
                <label class="text-[10px] font-black text-gray-400 uppercase">Position</label>
                    <select name="position" class="w-full border-b-2 border-gray-100 focus:border-red-500 outline-none py-1 text-sm font-bold bg-white" required>
                        <option value="SK Councilor" selected>SK Councilor (Kagawad)</option>
                        <option value="SK Treasurer">SK Treasurer</option>
                    </select>
            </div>
            <div class="space-y-1">
                <label class="text-[10px] font-black text-gray-400 uppercase">Term Period</label>
                <input type="text" name="term" value="2023-2026" class="w-full border-b-2 border-gray-100 focus:border-red-500 outline-none py-1 text-sm font-bold">
            </div>
            <div class="flex gap-3 pt-4">
                <button type="button" onclick="toggleModal()" class="flex-1 py-3 text-xs font-black uppercase text-gray-400">Cancel</button>
                <button type="submit" name="add_member" class="flex-1 bg-red-600 py-3 rounded-xl text-xs font-black uppercase text-white shadow-lg">Save Member</button>
            </div>
        </form>
    </div>
</div>

<div class="flex h-screen">
    <div class="w-64 bg-red-600 text-white flex flex-col p-3 overflow-y-auto">
        <div class="flex items-center gap-2 mb-3">
            <img src="https://cdn-icons-png.flaticon.com/512/3135/3135715.png" class="w-7 h-7" alt="logo">
            <h2 class="text-base font-bold">SK 360°</h2>
        </div>

        <div class="bg-red-500 rounded-lg p-2 flex items-center gap-2 mb-3 shadow text-xs">
            <div class="bg-yellow-400 text-red-600 h-9 w-9 rounded-full flex items-center justify-center font-bold border-2 border-red-400 overflow-hidden flex-shrink-0">
                <?php if (!empty($user_data['profile_pic'])): ?>
                    <img src="../uploads/<?= htmlspecialchars($user_data['profile_pic']) ?>" class="w-full h-full object-cover">
                <?php else: ?>
                    <span class="text-xs"><?= $initials ?></span>
                <?php endif; ?>
            </div>
            <div class="overflow-hidden">
                <p class="font-semibold text-[11px] truncate"><?= htmlspecialchars($full_name) ?></p>
                <p class="text-[9px] opacity-90 uppercase font-black tracking-tighter truncate">SK Chairman - <?= htmlspecialchars($barangay_name) ?></p>
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
            <a href="calendar.php" class="flex items-center gap-2 hover:bg-red-500 p-2 rounded-lg">
                <span class="bg-red-400 p-1 rounded text-sm">📅</span>
                <span>Calendar</span>
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
                <span class="text-yellow-300 font-semibold">Rankings</span>
            </a>
            <a href="leadership.php" class="flex items-center gap-2 bg-red-500 p-2 rounded-lg">
                <span class="bg-yellow-400 text-red-600 p-1 rounded text-sm">👥</span>
                <span>Leadership</span>
            </a>
            <a href="archive.php" class="flex items-center gap-2 hover:bg-red-500 p-2 rounded-lg">
                <span class="bg-red-400 p-1 rounded text-sm">🗂️</span>
                <span>Archive</span>
            </a>
        </nav>
    </div>

    <div class="flex-1 flex flex-col overflow-hidden">
        <header class="bg-red-600 text-white px-6 py-3 flex justify-between items-center shadow">
            <input type="text" placeholder="Search council..." class="px-4 py-2 rounded-full text-black w-1/3 focus:outline-none text-sm">
            <div class="flex items-center gap-3 relative">
                <button id="notifBtn" class="text-xl hover:bg-red-500 p-2 rounded-lg transition">🔔</button>
                <div class="relative">
                    <button id="userMenuBtn" class="flex items-center gap-2 hover:bg-red-500 px-3 py-2 rounded-lg transition">
                        <div class="w-7 h-7 rounded-full bg-yellow-400 text-red-600 flex items-center justify-center text-[10px] font-black border border-white/50 overflow-hidden">
                            <?php if (!empty($user_data['profile_pic'])): ?>
                                <img src="../uploads/<?= htmlspecialchars($user_data['profile_pic']) ?>" class="w-full h-full object-cover">
                            <?php else: ?>
                                <?= $initials ?>
                            <?php endif; ?>
                        </div>
                        <span class="font-semibold text-sm"><?= htmlspecialchars($full_name) ?></span>
                        <span class="text-[10px]">▼</span>
                    </button>
                </div>
            </div>
        </header>

        <main class="p-8 overflow-y-auto h-full bg-gray-50">
            <div class="flex justify-between items-end mb-8">
                <div>
                    <h1 class="text-3xl font-black text-gray-800 uppercase tracking-tight">Council Leadership</h1>
                    <p class="text-gray-500 font-medium italic">Official Directory for Barangay <?= htmlspecialchars($barangay_name) ?></p>
                </div>
                <button onclick="toggleModal()" class="bg-red-600 hover:bg-red-700 text-white px-6 py-3 rounded-2xl font-black uppercase text-[10px] tracking-widest shadow-lg transition-all flex items-center gap-2">
                    <span class="text-base">+</span> Add New Member
                </button>
            </div>

            <div class="bg-red-600 rounded-2xl p-6 text-white mb-8 shadow-md flex justify-between items-center">
                <div class="flex items-center gap-4">
                    <div class="bg-white/20 p-3 rounded-xl text-2xl">📍</div>
                    <div>
                        <h2 class="text-xl font-black uppercase tracking-tight">Barangay <?= htmlspecialchars($barangay_name) ?></h2>
                        <p class="text-xs opacity-80 font-medium">Current SK Administration</p>
                    </div>
                </div>
                <div class="text-right">
                    <span class="bg-white/10 px-4 py-2 rounded-full text-[10px] font-bold border border-white/20 uppercase tracking-widest">
                        <?= count($councilMembers) ?> Total Members
                    </span>
                </div>
            </div>

            <div class="bg-white rounded-3xl border border-gray-100 shadow-sm p-8 mb-8">
                <div class="flex items-center gap-2 mb-8 border-b border-gray-50 pb-4">
                    <span class="text-red-500 font-bold">🛡️</span>
                    <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Executive Officers</h3>
                </div>
                <div class="grid grid-cols-1 gap-4">
                    <?php foreach ($executives as $m): ?>
                        <div class="flex items-center gap-6 p-4 rounded-2xl border border-transparent hover:border-gray-100 hover:bg-gray-50 transition group">
                            <div class="w-16 h-16 rounded-full bg-red-100 flex items-center justify-center text-red-600 font-black text-xl border-4 border-white shadow-sm">
                                <?= strtoupper(substr($m['name'] ?? 'U', 0, 2)) ?>
                            </div>
                            <div class="flex-1">
                                <div class="flex items-center gap-3">
                                    <h4 class="text-lg font-black text-gray-800 uppercase leading-none"><?= htmlspecialchars($m['name']) ?></h4>
                                    <span class="px-3 py-1 rounded-full text-[9px] font-black uppercase bg-red-600 text-white">
                                        <?= htmlspecialchars($m['position']) ?>
                                    </span>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-3 mt-3 text-[11px] text-gray-500 font-medium gap-2">
                                    <div class="flex items-center gap-2"><span>📧</span> <?= htmlspecialchars($m['email']) ?></div>
                                    <div class="flex items-center gap-2"><span>📞</span> <?= htmlspecialchars($m['phone']) ?></div>
                                    <div class="flex items-center gap-2 uppercase tracking-tighter"><span>🗓️</span> <?= htmlspecialchars($m['term']) ?></div>
                                </div>
                            </div>
                            <?php if(isset($m['id']) && $m['id'] !== null): ?>
                            <a href="?delete_id=<?= $m['id'] ?>" onclick="return confirm('Remove this member?')" class="...">
                                🗑️
                            </a>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="bg-white rounded-3xl border border-gray-100 shadow-sm p-8">
                <div class="flex items-center gap-2 mb-8 border-b border-gray-50 pb-4">
                    <span class="text-yellow-500 font-bold">🌟</span>
                    <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">SK Councilors</h3>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <?php foreach ($kagawads as $m): ?>
                        <div class="bg-gray-50/50 p-5 rounded-2xl border border-gray-100 flex items-center gap-4 hover:shadow-md hover:bg-white transition group relative">
                            <div class="w-12 h-12 rounded-full bg-yellow-400 flex items-center justify-center text-white font-black shadow-sm border-2 border-white">
                                <?= strtoupper(substr($m['name'] ?? 'U', 0, 2)) ?>
                            </div>
                            <div class="flex-1">
                                <h4 class="text-xs font-black text-gray-800 uppercase leading-none"><?= htmlspecialchars($m['name']) ?></h4>
                                <p class="text-[9px] text-yellow-600 font-black uppercase mt-1"><?= htmlspecialchars($m['position']) ?></p>
                                <p class="text-[10px] text-gray-400 mt-2 font-medium">📞 <?= htmlspecialchars($m['phone']) ?></p>
                            </div>
                            <?php if(isset($m['id']) && $m['id'] !== null): ?>
                            <a href="?delete_id=<?= $m['id'] ?>" onclick="return confirm('Remove this member?')" class="absolute top-4 right-4 text-[10px] text-gray-300 hover:text-red-600 opacity-0 group-hover:opacity-100 transition">
                                🗑️
                            </a>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </main>
    </div>
</div>

<script>
    function toggleModal() {
        const modal = document.getElementById('addModal');
        modal.classList.toggle('hidden');
    }

    // Basic Dropdown Toggle for User Menu (Simpler version of Calendar's script)
    const userMenuBtn = document.getElementById('userMenuBtn');
    userMenuBtn.addEventListener('click', function(e) {
        // Toggle logic here or redirect to profile
        alert("Account Menu Clicked");
    });
</script>

</body>
</html>

<?php if (isset($_SESSION['status'])): ?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const status = "<?= $_SESSION['status'] ?>";
        
        if (status === 'added') {
            Swal.fire({
                title: 'Success!',
                text: 'New member has been added.',
                icon: 'success',
                confirmButtonColor: '#DC2626'
            });
        } else if (status === 'deleted') {
            Swal.fire({
                title: 'Deleted!',
                text: 'The member has been removed.',
                icon: 'success',
                confirmButtonColor: '#DC2626'
            });
        }
    });
</script>
<?php 
    unset($_SESSION['status']); // Importante: Burahin ang session pagkatapos ma-trigger
endif; 
?>