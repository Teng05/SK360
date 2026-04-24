<?php
session_start();
require_once '../classes/database.php';
$db = new Database();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'youth') {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$user_data = $db->getUserById($user_id);
$user_name = $user_data['first_name'] . ' ' . $user_data['last_name'];
$barangay_name = $db->getBarangayName($user_data['barangay_id']);

$status_message = "";

// --- LOGIC: CHANGE PHOTO ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['profile_pic'])) {
    $target_dir = "../uploads/profile_pics/";
    if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);

    $file_extension = pathinfo($_FILES["profile_pic"]["name"], PATHINFO_EXTENSION);
    $new_filename = "profile_" . $user_id . "_" . time() . "." . $file_extension;
    $target_file = $target_dir . $new_filename;

    if (move_uploaded_file($_FILES["profile_pic"]["tmp_name"], $target_file)) {
        // I-update ang database column (Siguraduhin na may 'profile_pic' column ka na sa users table)
        if($db->updateProfilePic($user_id, $new_filename)) {
            $status_message = "Photo updated successfully!";
            // Refresh user data para makita agad ang bagong pic
            $user_data = $db->getUserById($user_id);
        }
    }
}

// Logic for Password Update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_password'])) {
    $status_message = "Password updated successfully!";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile Settings | SK 360°</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        [x-cloak] { display: none !important; }
        .tab-active { border-bottom: 2px solid #ef4444; color: #ef4444; }
    </style>
</head>
<body class="bg-gray-50 font-sans" x-data="{ 
    activeTab: 'personal', 
    isEditing: false, 
    showPassModal: false,
    notifs: { email: true, push: false, announcements: true, events: true }
}">

<div class="flex h-screen overflow-hidden">
    <div class="w-64 bg-red-600 text-white flex flex-col p-3 shadow-xl z-20">
        <div class="flex items-center gap-2 mb-3">
            <img src="https://cdn-icons-png.flaticon.com/512/3135/3135715.png" class="w-7 h-7">
            <h2 class="text-base font-bold italic text-white">SK 360°</h2>
        </div>
        <div class="bg-red-500 rounded-lg p-2 flex items-center gap-2 mb-3 shadow text-xs">
            <div class="bg-yellow-400 text-red-600 p-1 rounded-full font-bold italic px-2">👤</div>
            <div>
                <p class="font-semibold"><?= htmlspecialchars($user_name) ?></p>
                <p class="opacity-80 text-[10px]">Youth Member</p>
            </div>
        </div>
        <nav class="space-y-1 text-xs">
            <a href="home.php" class="flex items-center gap-2 hover:bg-red-500 p-2 rounded-lg transition">
                <span class="bg-red-400 p-1 rounded">🏠</span><span>Home</span>
            </a>
            <a href="announcements.php" class="flex items-center gap-2 hover:bg-red-500 p-2 rounded-lg transition">
                <span class="bg-red-400 p-1 rounded">📢</span><span>Announcements</span>
            </a>
            <a href="calendar.php" class="flex items-center gap-2 hover:bg-red-500 p-2 rounded-lg transition">
                <span class="bg-red-400 p-1 rounded">📅</span><span>Event Calendar</span>
            </a>
            <a href="rankings.php" class="flex items-center gap-2 hover:bg-red-500 p-2 rounded-lg transition">
                <span class="bg-red-400 p-1 rounded">🏆</span><span>Rankings</span>
            </a>
            <a href="leadership.php" class="flex items-center gap-2 hover:bg-red-500 p-2 rounded-lg transition">
                <span class="bg-red-400 p-1 rounded">👥</span><span>Leadership</span>
            </a>
            <a href="profile.php" class="flex items-center gap-2 bg-red-500 p-2 rounded-lg transition text-yellow-300 font-bold border-l-4 border-yellow-300">
                <span class="bg-yellow-400 text-red-600 p-1 rounded">👤</span><span>Profile</span>
            </a>
        </nav>
    </div>

    <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
        <div class="bg-red-600 text-white px-6 py-3 flex justify-between items-center shadow relative z-10">
            <div class="w-1/4"></div> 
            <div class="w-1/3">
                <input type="text" placeholder="Search settings..." class="w-full px-4 py-2 rounded-full text-black text-sm outline-none">
            </div>
            <div class="w-1/4 flex justify-end items-center gap-5 text-sm">
                <button class="hover:opacity-80">🔔</button>
                <div class="relative">
                    <button id="profileDropdownBtn" class="flex items-center gap-2 font-semibold focus:outline-none hover:opacity-80 transition">
                        <span><?= htmlspecialchars($user_name) ?></span>
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

        <main class="flex-1 overflow-y-auto p-8 bg-gray-50">
            <div class="max-w-5xl mx-auto">
                <header class="mb-6">
                    <h1 class="text-2xl font-bold text-gray-800">Profile Settings</h1>
                    <p class="text-sm text-gray-500">Manage your profile and security settings for Lipa Youth.</p>
                </header>

                <div class="bg-white rounded-[32px] p-6 shadow-sm border border-gray-100 flex items-center justify-between mb-8">
                    <div class="flex items-center gap-6">
                        <div class="relative group">
                            <div class="w-24 h-24 bg-red-600 rounded-2xl flex items-center justify-center text-white text-4xl font-bold border-4 border-white shadow-md overflow-hidden italic">
                                <?php if(!empty($user_data['profile_pic'])): ?>
                                    <img src="../uploads/profile_pics/<?= $user_data['profile_pic'] ?>" class="w-full h-full object-cover">
                                <?php else: ?>
                                    <?= strtoupper(substr($user_data['first_name'], 0, 1)) ?>
                                <?php endif; ?>
                            </div>
                            
                            <form id="photoForm" action="" method="POST" enctype="multipart/form-data">
                                <label for="profile_pic" class="absolute -bottom-2 -right-2 bg-white p-2 rounded-xl shadow-lg border border-gray-100 cursor-pointer hover:scale-110 hover:bg-gray-50 transition">
                                    <span class="text-sm">📷</span>
                                    <input type="file" id="profile_pic" name="profile_pic" class="hidden" onchange="document.getElementById('photoForm').submit();">
                                </label>
                            </form>
                        </div>
                        <div>
                            <h2 class="text-xl font-bold text-gray-900"><?= htmlspecialchars($user_name) ?></h2>
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-widest italic">Barangay <?= $barangay_name ?></p>
                            <div class="mt-2 inline-flex items-center gap-1.5 px-3 py-1 bg-green-100 text-green-700 rounded-full text-[10px] font-black">
                                <span class="w-1.5 h-1.5 bg-green-500 rounded-full animate-pulse"></span> VERIFIED
                            </div>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4 text-sm pr-4 border-l pl-8 border-gray-100">
                        <div class="space-y-1">
                            <p class="text-[10px] font-black text-gray-400 uppercase">Term Start</p>
                            <p class="font-bold text-gray-700">June 2024</p>
                        </div>
                        <div class="space-y-1">
                            <p class="text-[10px] font-black text-gray-400 uppercase">Term End</p>
                            <p class="font-bold text-gray-700">June 2026</p>
                        </div>
                    </div>
                </div>

                <div class="flex gap-8 mb-8 border-b border-gray-200">
                    <button @click="activeTab = 'personal'" :class="activeTab === 'personal' ? 'tab-active' : 'text-gray-400'" class="pb-4 text-xs font-black uppercase tracking-widest transition-all">Personal</button>
                    <button @click="activeTab = 'security'" :class="activeTab === 'security' ? 'tab-active' : 'text-gray-400'" class="pb-4 text-xs font-black uppercase tracking-widest transition-all">Security</button>
                    <button @click="activeTab = 'notification'" :class="activeTab === 'notification' ? 'tab-active' : 'text-gray-400'" class="pb-4 text-xs font-black uppercase tracking-widest transition-all">Notification</button>
                </div>

                <div x-show="activeTab === 'personal'" x-transition x-cloak class="space-y-6">
                    <div class="bg-white rounded-[32px] p-8 shadow-sm border border-gray-100">
                        <div class="flex justify-between items-center mb-10">
                            <h3 class="text-sm font-black text-gray-400 uppercase tracking-widest">Personal Information</h3>
                            <button @click="isEditing = !isEditing" class="text-xs font-bold text-red-600 hover:bg-red-50 px-4 py-2 rounded-xl transition" x-text="isEditing ? 'Cancel Edit' : 'Edit Information'"></button>
                        </div>

                        <div class="grid grid-cols-2 gap-x-12 gap-y-8">
                            <div class="space-y-1">
                                <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">First Name</label>
                                <input type="text" value="<?= htmlspecialchars($user_data['first_name']) ?>" :disabled="!isEditing" :class="isEditing ? 'bg-gray-50 border-gray-200' : 'bg-transparent border-transparent cursor-default'" class="w-full p-2 text-sm font-bold text-gray-700 border-b outline-none transition">
                            </div>
                            <div class="space-y-1">
                                <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Last Name</label>
                                <input type="text" value="<?= htmlspecialchars($user_data['last_name']) ?>" :disabled="!isEditing" :class="isEditing ? 'bg-gray-50 border-gray-200' : 'bg-transparent border-transparent cursor-default'" class="w-full p-2 text-sm font-bold text-gray-700 border-b outline-none transition">
                            </div>
                            <div class="space-y-1">
                                <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Email Address</label>
                                <div class="flex items-center gap-2 bg-gray-50 rounded-2xl px-3">
                                    <input type="text" value="<?= htmlspecialchars($user_data['email']) ?>" disabled class="w-full py-3 text-sm font-bold text-gray-400 bg-transparent outline-none cursor-not-allowed">
                                    <span class="text-[8px] bg-gray-200 text-gray-500 px-2 py-1 rounded font-black tracking-widest">LOCKED</span>
                                </div>
                            </div>
                            <div class="space-y-1">
                                <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Phone Number</label>
                                <div class="flex items-center gap-2 bg-gray-50 rounded-2xl px-3">
                                    <input type="text" value="<?= htmlspecialchars($user_data['phone_number'] ?? '09XXXXXXXXX') ?>" disabled class="w-full py-3 text-sm font-bold text-gray-400 bg-transparent outline-none cursor-not-allowed">
                                    <span class="text-[8px] bg-gray-200 text-gray-500 px-2 py-1 rounded font-black tracking-widest">LOCKED</span>
                                </div>
                            </div>
                        </div>

                        <div class="mt-10 p-5 bg-blue-50/50 border border-blue-100 rounded-3xl flex gap-4 items-center">
                            <span class="text-xl">ℹ️</span>
                            <p class="text-[11px] text-blue-700 font-medium">To update your <strong>Email</strong> or <strong>Phone Number</strong>, please visit your <strong>Barangay SK Office</strong>.</p>
                        </div>

                        <div x-show="isEditing" class="mt-8 flex justify-end">
                            <button class="bg-red-600 text-white px-10 py-3 rounded-xl text-xs font-black shadow-lg shadow-red-100 hover:bg-red-700 transition uppercase tracking-widest">Save Changes</button>
                        </div>
                    </div>
                </div>

                <div x-show="activeTab === 'security'" x-transition x-cloak class="space-y-6">
                    <div class="bg-white rounded-3xl p-8 shadow-sm border border-gray-100">
                        <h3 class="text-sm font-black text-gray-400 uppercase tracking-widest mb-8">Manage Password</h3>
                        <div class="flex items-center justify-between p-6 bg-gray-50 rounded-3xl group hover:bg-red-50 transition">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 bg-white rounded-2xl flex items-center justify-center text-xl shadow-sm">🔑</div>
                                <div>
                                    <p class="text-sm font-bold text-gray-800">Password</p>
                                    <p class="text-[11px] text-gray-400">Update your account password regularly.</p>
                                </div>
                            </div>
                            <button @click="showPassModal = true" class="px-6 py-2.5 bg-white border border-gray-200 rounded-xl text-xs font-bold text-red-600 shadow-sm group-hover:bg-red-600 group-hover:text-white transition">Update Password</button>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<div x-show="showPassModal" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center p-6 bg-black/50 backdrop-blur-sm">
    <div class="bg-white w-full max-w-md rounded-[40px] p-10 shadow-2xl">
        <h2 class="text-2xl font-black text-gray-900 mb-2">Update Password</h2>
        <p class="text-xs text-gray-400 font-bold uppercase tracking-widest mb-8">Security Preference</p>
        <form action="" method="POST" class="space-y-6">
            <div class="space-y-1">
                <label class="text-[10px] font-black text-gray-400 uppercase ml-1">Current Password</label>
                <input type="password" required class="w-full bg-gray-50 p-4 rounded-2xl text-sm font-bold border-transparent focus:border-red-200 outline-none transition">
            </div>
            <div class="space-y-1">
                <label class="text-[10px] font-black text-gray-400 uppercase ml-1">New Password</label>
                <input type="password" required class="w-full bg-gray-50 p-4 rounded-2xl text-sm font-bold border-transparent focus:border-red-200 outline-none transition">
            </div>
            <div class="space-y-1">
                <label class="text-[10px] font-black text-gray-400 uppercase ml-1">Confirm New Password</label>
                <input type="password" required class="w-full bg-gray-50 p-4 rounded-2xl text-sm font-bold border-transparent focus:border-red-200 outline-none transition">
            </div>
            <div class="flex gap-4 pt-6">
                <button type="button" @click="showPassModal = false" class="flex-1 px-8 py-3 rounded-xl text-xs font-bold text-gray-400 hover:bg-gray-100 transition uppercase tracking-widest">Cancel</button>
                <button type="submit" name="update_password" class="flex-1 px-8 py-3 bg-red-600 text-white rounded-xl text-xs font-black shadow-lg shadow-red-100 hover:bg-red-700 transition uppercase tracking-widest">Update</button>
            </div>
        </form>
    </div>
</div>

<script>
    // Dropdown Logic
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
</script>

</body>
</html>