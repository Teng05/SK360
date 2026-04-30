<?php
session_start();
require_once '../classes/database.php';

$db = new Database();

// 1. Check Login - Chairman Only
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

// 2. Variables for UI Consistency
$full_name = $user_data['first_name'] . ' ' . $user_data['last_name'];
$barangay_id = $user_data['barangay_id'];
$barangay_name = $db->getBarangayName($barangay_id);
$initials = strtoupper(substr($user_data['first_name'], 0, 1) . substr($user_data['last_name'], 0, 1));

// (Mock Data para sa 3 Cards - Pwede mong i-konekta sa totoong database mo later)
$current_month = date('F');
$is_submitted_this_month = false; // Palitan ng true para maging kulay green
$next_deadline = date('F 15, Y', strtotime('+1 month'));
$completion_rate = 80; // 80%

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports | SK 360°</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-gray-100 overflow-hidden">

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
            <a href="home.php" class="flex items-center gap-2 hover:bg-red-500 p-2 rounded-lg transition">
                <span class="bg-red-400 p-1 rounded text-sm">🏠</span><span>Home</span>
            </a>
            <a href="reports.php" class="flex items-center gap-2 bg-red-500 p-2 rounded-lg transition shadow-inner">
                <span class="bg-yellow-400 text-red-600 p-1 rounded text-sm">📊</span>
                <span class="text-yellow-300 font-semibold">Reports</span>
            </a>
            <a href="budget.php" class="flex items-center gap-2 hover:bg-red-500 p-2 rounded-lg transition">
                <span class="bg-red-400 p-1 rounded text-sm">💰</span><span>Budget</span>
            </a>
            <a href="announcements.php" class="flex items-center gap-2 hover:bg-red-500 p-2 rounded-lg transition">
                <span class="bg-red-400 p-1 rounded text-sm">📢</span><span>Announcements</span>
            </a>
            <a href="calendar.php" class="flex items-center gap-2 hover:bg-red-500 p-2 rounded-lg transition">
                <span class="bg-red-400 p-1 rounded text-sm">📅</span><span>Calendar</span>
            </a>
            <a href="chat.php" class="flex items-center gap-2 hover:bg-red-500 p-2 rounded-lg transition">
                <span class="bg-red-400 p-1 rounded text-sm">💬</span><span>Chat</span>
            </a>
            <a href="meetings.php" class="flex items-center gap-2 hover:bg-red-500 p-2 rounded-lg transition">
                <span class="bg-red-400 p-1 rounded text-sm">📞</span><span>Meetings</span>
            </a>
            <a href="rankings.php" class="flex items-center gap-2 hover:bg-red-500 p-2 rounded-lg transition">
                <span class="bg-red-400 p-1 rounded text-sm">🏆</span><span>Rankings</span>
            </a>
            <a href="leadership.php" class="flex items-center gap-2 hover:bg-red-500 p-2 rounded-lg transition">
                <span class="bg-red-400 p-1 rounded text-sm">👥</span><span>Leadership</span>
            </a>
            <a href="archive.php" class="flex items-center gap-2 hover:bg-red-500 p-2 rounded-lg transition">
                <span class="bg-red-400 p-1 rounded text-sm">🗂️</span><span>Archive</span>
            </a>
        </nav>
    </div>

    <div class="flex-1 flex flex-col overflow-hidden">
        
        <header class="bg-red-600 text-white px-6 py-3 flex justify-between items-center shadow">
            <input type="text" placeholder="Search reports..." class="px-4 py-2 rounded-full text-black w-1/3 focus:outline-none text-sm">

            <div class="flex items-center gap-3 relative">
                <div class="relative">
                    <button onclick="triggerAlert('Notifications', 'No new notifications right now.')" class="text-xl hover:bg-red-500 p-2 rounded-lg transition">🔔</button>
                </div>

                <div class="relative">
                    <button class="flex items-center gap-2 hover:bg-red-500 px-3 py-2 rounded-lg transition cursor-pointer">
                        <div class="w-7 h-7 rounded-full bg-yellow-400 text-red-600 flex items-center justify-center text-[10px] font-black border border-white/50 overflow-hidden">
                            <?php if (!empty($user_data['profile_pic'])): ?>
                                <img src="../uploads/<?= htmlspecialchars($user_data['profile_pic']) ?>" class="w-full h-full object-cover">
                            <?php else: ?>
                                <?= $initials ?>
                            <?php endif; ?>
                        </div>
                        <span class="font-semibold text-sm"><?= htmlspecialchars($full_name) ?></span>
                    </button>
                </div>
            </div>
        </header>

        <main class="flex-1 overflow-y-auto p-8 bg-gray-50 h-full">
            <div class="max-w-6xl mx-auto">
                
                <div class="flex justify-between items-end mb-8">
                    <div>
                        <h1 class="text-3xl font-black text-gray-800 uppercase tracking-tighter">Reports Submission</h1>
                        <p class="text-gray-500 font-medium italic">Manage and track documents for <?= htmlspecialchars($barangay_name) ?></p>
                    </div>
                    <button onclick="toggleModal('submitReportModal')" class="bg-red-600 text-white px-6 py-3 rounded-2xl font-bold uppercase text-xs shadow-lg hover:bg-red-700 active:scale-95 transition flex items-center gap-2">
                        <i class="bi bi-cloud-arrow-up-fill text-lg"></i> Submit New Report
                    </button>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                    
                    <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100 flex flex-col justify-center relative overflow-hidden group">
                        <div class="absolute top-0 left-0 w-2 h-full <?= $is_submitted_this_month ? 'bg-green-500' : 'bg-red-500' ?>"></div>
                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-2">Status for <?= $current_month ?></p>
                        
                        <?php if ($is_submitted_this_month): ?>
                            <h3 class="text-2xl font-black text-green-600 mb-2 ml-2 tracking-tight">Submitted</h3>
                            <p class="text-xs text-gray-500 font-medium ml-2"><i class="bi bi-check-circle-fill text-green-500 mr-1"></i> All requirements met.</p>
                        <?php else: ?>
                            <h3 class="text-2xl font-black text-red-600 mb-2 ml-2 tracking-tight">Pending</h3>
                            <div class="bg-red-50 text-red-600 text-[10px] font-bold p-3 rounded-xl ml-2 border border-red-100 uppercase flex items-start gap-2">
                                <i class="bi bi-exclamation-triangle-fill text-sm"></i>
                                <span>Paalala: Please submit your mandatory reports for this month to avoid penalties.</span>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100 flex flex-col justify-center relative overflow-hidden">
                        <div class="absolute top-0 left-0 w-2 h-full bg-blue-500"></div>
                        <div class="flex justify-between items-start ml-2">
                            <div>
                                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Next Deadline</p>
                                <h3 class="text-2xl font-black text-gray-800 tracking-tight"><?= $next_deadline ?></h3>
                            </div>
                            <div class="bg-blue-50 p-3 rounded-2xl text-blue-600">
                                <i class="bi bi-calendar-event-fill text-xl"></i>
                            </div>
                        </div>
                        <p class="text-xs text-gray-500 font-medium mt-3 ml-2 italic">Applicable for monthly submissions.</p>
                    </div>

                    <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100 flex flex-col justify-center relative overflow-hidden">
                        <div class="absolute top-0 left-0 w-2 h-full bg-yellow-400"></div>
                        <div class="flex justify-between items-end mb-3 ml-2">
                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Completion Rate</p>
                            <h3 class="text-2xl font-black text-gray-800 tracking-tight"><?= $completion_rate ?>%</h3>
                        </div>
                        <div class="w-full bg-gray-100 rounded-full h-3 mb-3 ml-2 overflow-hidden">
                            <div class="bg-yellow-400 h-3 rounded-full" style="width: <?= $completion_rate ?>%"></div>
                        </div>
                        <p class="text-[10px] text-gray-400 font-medium uppercase ml-2 tracking-wide">Overall progress for the year</p>
                    </div>

                </div>

                <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="px-8 py-5 border-b border-gray-50 flex justify-between items-center bg-gray-50/50">
                        <h3 class="font-black text-gray-800 uppercase tracking-tighter">Recent Submissions</h3>
                        <button onclick="triggerAlert('Filter', 'Filter options will be displayed here.')" class="text-xs font-bold text-gray-500 hover:text-red-600 transition bg-white border px-4 py-2 rounded-xl shadow-sm uppercase">
                            <i class="bi bi-funnel-fill mr-1"></i> Filter
                        </button>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <thead class="text-[10px] text-gray-400 uppercase font-black tracking-widest border-b bg-gray-50">
                                <tr>
                                    <th class="px-8 py-4">Report Details</th>
                                    <th class="px-6 py-4">Method</th>
                                    <th class="px-6 py-4">Date Submitted</th>
                                    <th class="px-6 py-4">Status</th>
                                    <th class="px-8 py-4 text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="text-sm text-gray-600">
                                
                                <tr class="hover:bg-gray-50 transition border-b border-gray-50">
                                    <td class="px-8 py-5">
                                        <div class="font-bold text-gray-800 uppercase tracking-tighter">Monthly Financial Report</div>
                                        <div class="text-[10px] text-gray-400 font-bold uppercase mt-1">January 2024</div>
                                    </td>
                                    <td class="px-6 py-5">
                                        <span class="bg-purple-100 text-purple-600 px-3 py-1 rounded-full text-[9px] font-black uppercase">PDF Upload</span>
                                    </td>
                                    <td class="px-6 py-5 text-xs font-semibold">Apr 30, 2026</td>
                                    <td class="px-6 py-5">
                                        <span class="px-3 py-1 rounded-full bg-yellow-100 text-yellow-600 text-[9px] font-black uppercase">Pending</span>
                                    </td>
                                    <td class="px-8 py-5 text-right space-x-2">
                                        <button onclick="actionView('Monthly Financial Report')" class="w-8 h-8 rounded-xl bg-gray-100 text-gray-500 hover:bg-blue-100 hover:text-blue-600 transition shadow-sm"><i class="bi bi-eye-fill"></i></button>
                                        <button onclick="actionDownload('Monthly Financial Report')" class="w-8 h-8 rounded-xl bg-gray-100 text-gray-500 hover:bg-green-100 hover:text-green-600 transition shadow-sm"><i class="bi bi-download"></i></button>
                                    </td>
                                </tr>

                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-8 py-5">
                                        <div class="font-bold text-gray-800 uppercase tracking-tighter">Annual Statement of Receipts</div>
                                        <div class="text-[10px] text-gray-400 font-bold uppercase mt-1">Year 2023</div>
                                    </td>
                                    <td class="px-6 py-5">
                                        <span class="bg-blue-100 text-blue-600 px-3 py-1 rounded-full text-[9px] font-black uppercase">Template</span>
                                    </td>
                                    <td class="px-6 py-5 text-xs font-semibold">Feb 15, 2026</td>
                                    <td class="px-6 py-5">
                                        <span class="px-3 py-1 rounded-full bg-green-100 text-green-600 text-[9px] font-black uppercase">Approved</span>
                                    </td>
                                    <td class="px-8 py-5 text-right space-x-2">
                                        <button onclick="actionView('Annual Statement of Receipts')" class="w-8 h-8 rounded-xl bg-gray-100 text-gray-500 hover:bg-blue-100 hover:text-blue-600 transition shadow-sm"><i class="bi bi-eye-fill"></i></button>
                                        <button onclick="actionDownload('Annual Statement of Receipts')" class="w-8 h-8 rounded-xl bg-gray-100 text-gray-500 hover:bg-green-100 hover:text-green-600 transition shadow-sm"><i class="bi bi-download"></i></button>
                                    </td>
                                </tr>

                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </main>
    </div>
</div>

<div id="submitReportModal" class="hidden fixed inset-0 bg-black/60 backdrop-blur-sm z-[100] flex items-center justify-center p-4">
    <div class="bg-white w-full max-w-md rounded-3xl shadow-2xl overflow-hidden">
        <div class="bg-red-600 px-6 py-5 text-white flex justify-between items-center">
            <h3 class="font-black uppercase tracking-tighter">Submit Report</h3>
            <button onclick="toggleModal('submitReportModal')" class="text-white hover:rotate-90 transition">✕</button>
        </div>
        
        <form action="reports.php" method="POST" enctype="multipart/form-data" class="p-8 space-y-6" onsubmit="mockSubmit(event)">
            
            <div>
                <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1">Report Title / Name</label>
                <input type="text" name="report_title" placeholder="e.g. Monthly Report for May" required class="w-full bg-gray-50 border border-gray-100 px-4 py-3 rounded-xl focus:outline-none focus:ring-2 focus:ring-red-500 text-sm font-semibold">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <input type="radio" name="sub_method" id="m_temp" value="template" checked class="hidden peer" onclick="toggleFileInput(false)">
                <label for="m_temp" class="text-center p-3 rounded-xl border-2 border-gray-100 bg-gray-50 cursor-pointer peer-checked:border-red-600 peer-checked:bg-red-50 peer-checked:text-red-600 text-[10px] font-black uppercase transition">
                    System Template
                </label>

                <input type="radio" name="sub_method" id="m_pdf" value="pdf" class="hidden peer" onclick="toggleFileInput(true)">
                <label for="m_pdf" class="text-center p-3 rounded-xl border-2 border-gray-100 bg-gray-50 cursor-pointer peer-checked:border-red-600 peer-checked:bg-red-50 peer-checked:text-red-600 text-[10px] font-black uppercase transition">
                    PDF Upload
                </label>
            </div>

            <div id="fileSection" class="hidden animate-in fade-in duration-300">
                <label class="block text-[10px] font-black text-red-500 uppercase mb-2 ml-1">Select PDF File</label>
                <div class="border-2 border-dashed border-red-200 bg-red-50 p-4 rounded-xl text-center relative hover:bg-red-100 transition cursor-pointer">
                    <input type="file" name="report_file" accept=".pdf" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                    <i class="bi bi-file-earmark-pdf text-3xl text-red-500 mb-1"></i>
                    <p class="text-[10px] font-black text-red-600 uppercase">Click or Drag PDF Here</p>
                </div>
            </div>

            <button type="submit" name="submit_report" class="w-full bg-red-600 text-white py-4 rounded-2xl font-black uppercase tracking-tighter shadow-lg hover:bg-red-700 active:scale-95 transition">
                Submit Document
            </button>
        </form>
    </div>
</div>

<script>
    // Modal Controller
    function toggleModal(modalID) { 
        document.getElementById(modalID).classList.toggle('hidden'); 
    }

    // Toggle PDF field
    function toggleFileInput(showPDF) {
        document.getElementById('fileSection').classList.toggle('hidden', !showPDF);
    }

    // --- SWEETALERT FUNCTIONS (Para walang dead buttons) ---
    function triggerAlert(title, text) {
        Swal.fire({ title: title, text: text, icon: 'info', confirmButtonColor: '#DC2626' });
    }

    function actionView(reportName) {
        Swal.fire({
            title: 'Viewing Report',
            text: 'Opening file viewer for: ' + reportName,
            icon: 'success',
            confirmButtonColor: '#DC2626'
        });
    }

    function actionDownload(reportName) {
        Swal.fire({
            title: 'Downloading...',
            text: reportName + ' is being downloaded to your device.',
            icon: 'success',
            confirmButtonColor: '#16a34a'
        });
    }

    // Mock form submission para makita mo ang success message kahit wala pang backend
    function mockSubmit(event) {
        event.preventDefault(); 
        toggleModal('submitReportModal');
        Swal.fire({
            title: 'Report Submitted!',
            text: 'Your document has been sent successfully.',
            icon: 'success',
            confirmButtonColor: '#DC2626'
        }).then(() => {
            // Uncomment to allow real backend submission
            // event.target.submit(); 
        });
    }
</script>

</body>
</html>