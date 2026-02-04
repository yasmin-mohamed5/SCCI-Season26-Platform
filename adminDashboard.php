<?php
ob_start();
include './includes/config.php';

// --- AUTH CHECK ---
if (!isset($_SESSION['user_id'])) {
    header("Location:./auth/login.php");
    exit;
}

$adminId = (int) $_SESSION['user_id'];

// Verify Admin Role (5)
$stmt = $connect->prepare("SELECT role FROM users WHERE user_id = ? AND status = 1");
$stmt->bind_param("i", $adminId);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

if (!$user || (int)$user['role'] !== 5) {
    http_response_code(403);
    die("Access Denied: Admins Only.");
}

// --- DATA FETCHING HELPERS ---

// 1. Overview Stats
function getStats($connect) {
    $stats = [];
    
    // Total Users
    $res = $connect->query("SELECT COUNT(*) as cnt FROM users");
    $stats['total_users'] = $res->fetch_assoc()['cnt'];

    // Participants
    $res = $connect->query("SELECT COUNT(*) as cnt FROM users WHERE role = 1");
    $stats['participants'] = $res->fetch_assoc()['cnt'];

    // Crew
    $res = $connect->query("SELECT COUNT(*) as cnt FROM users WHERE role IN (2, 3, 4, 5)");
    $stats['crew'] = $res->fetch_assoc()['cnt'];

    // Workshops
    $res = $connect->query("SELECT COUNT(*) as cnt FROM workshops");
    $stats['workshops'] = $res->fetch_assoc()['cnt'];

    return $stats;
}

// 2. Fetch Users (with basic filtering)
$roleFilter = $_GET['role'] ?? '';
$searchQuery = $_GET['search'] ?? '';
$usersSql = "SELECT u.*, w.workshop_name, c.committe_name 
             FROM users u 
             LEFT JOIN workshops w ON u.workshop_id = w.workshop_id 
             LEFT JOIN committees c ON u.committee_id = c.committee_id
             WHERE 1=1";

if ($roleFilter !== '') {
    // Mapping role names to IDs for filter simplicity
    $roleMap = ['participant' => 1, 'member' => 2, 'head' => 3, 'board' => 4, 'admin' => 5];
    if (isset($roleMap[$roleFilter])) {
        $usersSql .= " AND u.role = " . $roleMap[$roleFilter];
    }
}

if ($searchQuery !== '') {
    $safeSearch = $connect->real_escape_string($searchQuery);
    $usersSql .= " AND (u.user_name LIKE '%$safeSearch%' OR u.email LIKE '%$safeSearch%')";
}

$usersSql .= " ORDER BY u.user_id DESC LIMIT 50"; // Limit for performance
$usersResult = $connect->query($usersSql);

// 3. Workshops & Drill-down Logic
$workshopsResult = $connect->query("SELECT * FROM workshops");

// Current selection
$selectedWorkshopId = isset($_GET['workshop_id']) ? (int)$_GET['workshop_id'] : 0;
$selectedSessionId = isset($_GET['session_id']) ? (int)$_GET['session_id'] : 0;

// Fetch sessions if workshop selected
$sessions = [];
if ($selectedWorkshopId > 0) {
    $stmt = $connect->prepare("
        SELECT ws.workshop_session_id, s.session_name, s.session_id
        FROM workshop_session ws
        JOIN sessions s ON s.session_id = ws.session_id
        WHERE ws.workshop_id = ?
        ORDER BY s.session_id ASC
    ");
    $stmt->bind_param("i", $selectedWorkshopId);
    $stmt->execute();
    $sessions = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

// Fetch details if session selected
$attendanceData = [];
$tasksData = [];

if ($selectedWorkshopId > 0 && $selectedSessionId > 0) {
    // Get proper workshop_session_id
    $wsId = 0;
    foreach ($sessions as $s) {
        if ($s['session_id'] == $selectedSessionId) {
            $wsId = $s['workshop_session_id'];
            break;
        }
    }

    if ($wsId > 0) {
        // A. Attendance
        // Get all participants of this workshop
        $stmt = $connect->prepare("
            SELECT u.user_id, u.user_name, a.status
            FROM users u
            LEFT JOIN attendance a ON u.user_id = a.user_id AND a.workshop_id = ? AND a.session_id = ?
            WHERE u.workshop_id = ? AND u.role = 1 AND u.status = 1
            ORDER BY u.user_name ASC
        ");
        $stmt->bind_param("iii", $selectedWorkshopId, $selectedSessionId, $selectedWorkshopId);
        $stmt->execute();
        $attendanceData = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        // B. Tasks & Submissions
        $stmt = $connect->prepare("
            SELECT t.task_id, t.taskName, t.taskDeadline, t.task_file,
                   (SELECT COUNT(*) FROM task_submissions ts WHERE ts.task_id = t.task_id AND ts.status='submitted') as submission_count
            FROM tasks t
            WHERE t.workshop_session_id = ?
        ");
        $stmt->bind_param("i", $wsId);
        $stmt->execute();
        $tasksData = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        // C. Materials
        $stmt = $connect->prepare("
            SELECT m.material_title, m.material_type, m.file_path, u.user_name as uploaded_by_name
            FROM session_materials m
            LEFT JOIN users u ON m.uploaded_by = u.user_id
            WHERE m.workshop_session_id = ?
        ");
        $stmt->bind_param("i", $wsId);
        $stmt->execute();
        $materialsData = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
}

// 4. View Submissions Logic (New View)
$viewSubmissionsTaskId = isset($_GET['view_submissions']) ? (int)$_GET['view_submissions'] : 0;
$submissionsData = [];
$selectedTaskName = '';
if ($viewSubmissionsTaskId > 0) {
    // Get task name
    $tq = $connect->query("SELECT taskName FROM tasks WHERE task_id = $viewSubmissionsTaskId");
    if($tq && $tr = $tq->fetch_assoc()) $selectedTaskName = $tr['taskName'];

    // Get submissions
    $stmt = $connect->prepare("
        SELECT ts.*, u.user_name, u.email
        FROM task_submissions ts
        JOIN users u ON ts.user_id = u.user_id
        WHERE ts.task_id = ? AND ts.status = 'submitted'
        ORDER BY ts.submission_id DESC
    ");
    $stmt->bind_param("i", $viewSubmissionsTaskId);
    $stmt->execute();
    $submissionsData = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}


// --- POST HANDLERS (Actions) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['block_user_id'])) {
        $uid = (int) $_POST['block_user_id'];
        // Toggle status: if 1 set to 0, if 0 set to 1, or just Block (0) as per request?
        // Assuming Block means set status = 0
        $connect->query("UPDATE users SET status = IF(status=1, 0, 1) WHERE user_id = $uid");
        header("Location: " . $_SERVER['REQUEST_URI']);
        exit;
    }
}

$stats = getStats($connect);
$activeTab = $_GET['tab'] ?? 'overview';

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SCCI - System Admin Dashboard</title>
    <!-- google fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
        rel="stylesheet" />
    <!-- CSS -->
    <link rel="stylesheet" href="./assets/css/root.css?v=<?= ASSET_VERSION ?>">
    <link rel="stylesheet" href="./assets/css/adminDashboard.css?v=<?= ASSET_VERSION ?>">
    <link rel="stylesheet" href="./assets/css/all.min.css?v=<?= ASSET_VERSION ?>">
</head>
<body>

    <?php include('./includes/nav.php'); ?>

    <div class="dashboard-container">
        <!-- SIDEBAR -->
        <aside class="dashboard-sidebar">
            <div class="menu-title">Main Menu</div>
            <a href="?tab=overview" class="sidebar-link <?= $activeTab == 'overview' ? 'active' : '' ?>">
                <i class="fa-solid fa-chart-pie"></i> Overview
            </a>
            <a href="?tab=users" class="sidebar-link <?= $activeTab == 'users' ? 'active' : '' ?>">
                <i class="fa-solid fa-users"></i> User Management
            </a>
            <a href="?tab=workshops" class="sidebar-link <?= $activeTab == 'workshops' ? 'active' : '' ?>">
                <i class="fa-solid fa-layer-group"></i> Operations Center
            </a>
            <!-- Additional links can go here (Committees, etc) -->
        </aside>

        <!-- MAIN CONTENT -->
        <main class="dashboard-content">
            
            <?php if ($activeTab == 'overview'): ?>
                <div class="section-title">Dashboard Overview <span style="font-size:0.9rem; color:#999; font-weight:400"><?= date('l, d F Y') ?></span></div>
                
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-icon"><i class="fa-solid fa-users"></i></div>
                        <div class="stat-info">
                            <h3><?= $stats['total_users'] ?></h3>
                            <p>Total Registered Users</p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon"><i class="fa-solid fa-user-graduate"></i></div>
                        <div class="stat-info">
                            <h3><?= $stats['participants'] ?></h3>
                            <p>Active Participants</p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon"><i class="fa-solid fa-people-group"></i></div>
                        <div class="stat-info">
                            <h3><?= $stats['crew'] ?></h3>
                            <p>Crew & Board Members</p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon"><i class="fa-solid fa-briefcase"></i></div>
                        <div class="stat-info">
                            <h3><?= $stats['workshops'] ?></h3>
                            <p>Active Workshops</p>
                        </div>
                    </div>
                </div>

            <?php elseif ($activeTab == 'users'): ?>
                <div class="section-title">User Management</div>

                <div class="table-container">
                    <div class="controls-bar" style="padding: 20px; border-bottom: 1px solid #eee;">
                        <input type="text" id="adminSearchInput" class="search-input" placeholder="Search users...">
                        <select id="roleFilter" class="filter-select">
                            <option value="">All Roles</option>
                            <option value="participant">Participant (1)</option>
                            <option value="member">Member (2)</option>
                            <option value="head">Head (3)</option>
                            <option value="board">Board (4)</option>
                            <option value="admin">Admin (5)</option>
                        </select>
                    </div>

                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Affiliation</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            
                            <?php while($user = $usersResult->fetch_assoc()): 
                                // Determind Affiliation (Workshop or Committee)
                                $aff = 'N/A';
                                if (!empty($user['workshop_name'])) $aff = $user['workshop_name'];
                                elseif (!empty($user['committe_name'])) $aff = $user['committe_name'];

                                // Map role ID to Name
                                $roleName = 'Unknown';
                                switch($user['role']) {
                                    case 1: $roleName = 'Participant'; break;
                                    case 2: $roleName = 'Member'; break;
                                    case 3: $roleName = 'Head'; break;
                                    case 4: $roleName = 'Board'; break;
                                    case 5: $roleName = 'Admin'; break;
                                }
                            ?>
                            <tr data-role="<?= strtolower($roleName) ?>">
                                <td><?= htmlspecialchars($user['user_name']) ?></td>
                                <td><?= htmlspecialchars($user['email']) ?></td>
                                <td><?= $roleName ?></td>
                                <td><?= htmlspecialchars($aff) ?></td>
                                <td>
                                    <?php if($user['status'] == 1): ?>
                                        <span class="status-badge status-active">Active</span>
                                    <?php else: ?>
                                        <span class="status-badge status-blocked">Blocked</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <form method="POST" style="display:inline">
                                        <input type="hidden" name="block_user_id" value="<?= $user['user_id'] ?>">
                                        <button type="submit" class="action-btn delete btn-block-user" title="Block/Unblock">
                                            <i class="fa-solid fa-ban"></i>
                                        </button>
                                    </form>
                                    <!-- <button class="action-btn edit"><i class="fa-solid fa-pen"></i></button> -->
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>

            <?php elseif ($activeTab == 'workshops'): ?>
                <div class="section-title">Operations Center</div>

                <!-- Breadcrumb Navigation -->
                <div class="breadcrumb">
                    <a href="?tab=workshops">Workshops</a>
                    <?php if ($selectedWorkshopId > 0): ?>
                        <span>/</span> 
                        <a href="?tab=workshops&workshop_id=<?= $selectedWorkshopId ?>">Selected Workshop</a>
                    <?php endif; ?>
                    <?php if ($selectedSessionId > 0): ?>
                        <span>/</span> Session Details
                    <?php endif; ?>
                </div>

                <!-- View 1: List Workshops -->
                <?php if ($selectedWorkshopId == 0): ?>
                    <div class="stats-grid">
                        <?php while($ws = $workshopsResult->fetch_assoc()): ?>
                            <div class="stat-card" style="cursor:pointer" onclick="window.location.href='?tab=workshops&workshop_id=<?= $ws['workshop_id'] ?>'">
                                <div class="stat-icon" style="background:var(--color-primary); color:#fff">
                                    <i class="fa-solid fa-graduation-cap"></i>
                                </div>
                                <div class="stat-info">
                                    <h3><?= htmlspecialchars($ws['workshop_name']) ?></h3>
                                    <p>Click to manage sessions</p>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    </div>

                <!-- View 2: List Sessions -->
                <?php elseif ($selectedWorkshopId > 0 && $selectedSessionId == 0): ?>
                    <h3 style="margin-bottom:20px;">Select a Session to Manage</h3>
                    <div class="stats-grid">
                        <?php 
                        if (count($sessions) == 0) echo "<p>No sessions found for this workshop.</p>";
                        foreach($sessions as $s): 
                        ?>
                            <div class="stat-card session-card" style="cursor:pointer" 
                                 onclick="window.location.href='?tab=workshops&workshop_id=<?= $selectedWorkshopId ?>&session_id=<?= $s['session_id'] ?>'">
                                <div class="stat-info" style="width:100%">
                                    <h3><?= htmlspecialchars($s['session_name']) ?></h3>
                                    <p>View Attendance & Tasks</p>
                                </div>
                                <i class="fa-solid fa-arrow-right" style="color:#ddd"></i>
                            </div>
                        <?php endforeach; ?>
                    </div>

                <!-- View 4: View Submissions (Deepest level) -->
                <?php elseif ($viewSubmissionsTaskId > 0): ?>
                     <div class="breadcrumb">
                        <a href="?tab=workshops">Workshops</a> 
                        <span>/</span> <a href="javascript:history.back()">Session Details</a>
                        <span>/</span> Submissions for "<?= htmlspecialchars($selectedTaskName) ?>"
                    </div>

                    <div class="table-container">
                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th>Student Name</th>
                                    <th>Email</th>
                                    <th>Submission Link</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(empty($submissionsData)): ?>
                                    <tr><td colspan="4">No submissions found.</td></tr>
                                <?php else: ?>
                                    <?php foreach($submissionsData as $sub): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($sub['user_name']) ?></td>
                                            <td><?= htmlspecialchars($sub['email']) ?></td>
                                            <td>
                                                <a href="<?= htmlspecialchars($sub['submit_link']) ?>" target="_blank" class="status-badge status-active">
                                                    View File <i class="fa-solid fa-external-link-alt"></i>
                                                </a>
                                            </td>
                                            <td>
                                                <!-- Could add grading here later -->
                                                <span style="color:#999">-</span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                <!-- View 3: Session Details (The real meat) -->
                <?php elseif ($selectedWorkshopId > 0 && $selectedSessionId > 0): ?>
                    
                    <div class="table-container" style="padding:20px;">
                        
                        <!-- Tabs -->
                        <div class="nav-tabs">
                            <div class="nav-tab active" data-target="tab-attendance">Attendance</div>
                            <div class="nav-tab" data-target="tab-tasks">Tasks & Submissions</div>
                            <div class="nav-tab" data-target="tab-materials">Materials</div>
                        </div>

                        <!-- Tab 1: Attendance -->
                        <div id="tab-attendance" class="tab-content active">
                            <h3 style="margin-bottom:15px">Participant Attendance</h3>
                            <table class="admin-table">
                                <thead>
                                    <tr>
                                        <th>Participant Name</th>
                                        <th>Status (For this session)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if(empty($attendanceData)): ?>
                                        <tr><td colspan="2">No participants found.</td></tr>
                                    <?php else: ?>
                                        <?php foreach($attendanceData as $p): ?>
                                            <tr>
                                                <td><?= htmlspecialchars($p['user_name']) ?></td>
                                                <td>
                                                    <?php if ($p['status'] == 'present'): ?>
                                                        <span class="status-badge status-active">Present</span>
                                                    <?php elseif ($p['status'] == 'absent'): ?>
                                                        <span class="status-badge status-blocked">Absent</span>
                                                    <?php else: ?>
                                                        <span style="color:#999; font-style:italic">Not Marked</span>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>

                        <!-- Tab 2: Tasks -->
                        <div id="tab-tasks" class="tab-content">
                            <h3 style="margin-bottom:15px">Session Tasks</h3>
                            
                            <?php if(empty($tasksData)): ?>
                                <p>No tasks assigned for this session.</p>
                            <?php else: ?>
                                <table class="admin-table">
                                    <thead>
                                        <tr>
                                            <th>Task Name</th>
                                            <th>Deadline</th>
                                            <th>Downloads</th>
                                            <th>Submissions</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach($tasksData as $t): ?>
                                            <tr>
                                                <td><?= htmlspecialchars($t['taskName']) ?></td>
                                                <td><?= htmlspecialchars($t['taskDeadline']) ?></td>
                                                <td>
                                                    <?php if(!empty($t['task_file'])): ?>
                                                        <a href="<?= htmlspecialchars($t['task_file']) ?>" target="_blank"><i class="fa-solid fa-download"></i> File</a>
                                                    <?php else: ?>
                                                        <span style="color:#ccc">No File</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <span class="status-badge status-active"><?= $t['submission_count'] ?> Submitted</span>
                                                </td>
                                                <td>
                                                    <a href="?tab=workshops&workshop_id=<?= $selectedWorkshopId ?>&session_id=<?= $selectedSessionId ?>&view_submissions=<?= $t['task_id'] ?>" class="action-btn" title="View Submissions"><i class="fa-solid fa-eye"></i> View</a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            <?php endif; ?>
                        </div>

                        <!-- Tab 3: Materials -->
                        <div id="tab-materials" class="tab-content">
                            <h3 style="margin-bottom:15px">Session Materials</h3>
                            
                            <?php if(empty($materialsData)): ?>
                                <p>No materials uploaded.</p>
                            <?php else: ?>
                                <table class="admin-table">
                                    <thead>
                                        <tr>
                                            <th>Title</th>
                                            <th>Type</th>
                                            <th>Uploaded By</th>
                                            <th>Link</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach($materialsData as $m): ?>
                                            <tr>
                                                <td><?= htmlspecialchars($m['material_title']) ?></td>
                                                <td>
                                                    <?php if($m['material_type'] == 'technical'): ?>
                                                        <span style="color:#2563eb"><i class="fa-solid fa-code"></i> Technical</span>
                                                    <?php else: ?>
                                                        <span style="color:#d97706"><i class="fa-solid fa-lightbulb"></i> Soft Skills</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td><?= htmlspecialchars($m['uploaded_by_name'] ?? 'Unknown') ?></td>
                                                <td>
                                                    <a href="<?= htmlspecialchars($m['file_path']) ?>" target="_blank" class="action-btn"><i class="fa-solid fa-external-link-alt"></i> Open</a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            <?php endif; ?>
                        </div>

                    </div>

                <?php endif; ?>

            <?php endif; ?>

        </main>
    </div>

    <!-- Scripts -->
    <script src="./assets/js/adminDashboard.js?v=<?= ASSET_VERSION ?>"></script>
</body>
</html>
