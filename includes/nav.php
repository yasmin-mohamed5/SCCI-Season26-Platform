<?php



// Fetch user's profile image if logged in
$user_image = 'default.png'; // Default fallback
$role = 0;
$committeeId = 0;
$isAcademicParticipant = false;
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $role = $_SESSION['role'] ?? 0;
    $committeeId = $_SESSION['committee_id'] ?? 0;
    $select_user_image = "SELECT image, role, committee_id FROM users WHERE user_id = ?";
    $stmt = mysqli_prepare($connect, $select_user_image);
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $user_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        if ($row = mysqli_fetch_assoc($result)) {
            $user_image = $row['image'] ?? 'default.png';
            $role = $row['role']; // Fetch role from DB to ensure it's available
            $committeeId = $row['committee_id'] ?? 0; // Fetch committee_id from DB

            // Add SCCI Board prefix for role 4 users
            if ($role == 4 or $role == 5) {
                $user_image = 'SCCI Board/' . $user_image;
            }
        }
        mysqli_stmt_close($stmt);
    }

    // Check if user is an academic participant (for academic participant panel nav link)
    $stmtAcad = mysqli_prepare($connect, "SELECT participant_id FROM academic_participants WHERE participant_id = ? AND role = 3 LIMIT 1");
    if ($stmtAcad) {
        mysqli_stmt_bind_param($stmtAcad, "i", $user_id);
        mysqli_stmt_execute($stmtAcad);
        $isAcademicParticipant = mysqli_stmt_get_result($stmtAcad)->num_rows > 0;
        mysqli_stmt_close($stmtAcad);
    }
}
?>

<head>
    <link rel="icon" href="assets/icons/logoSCCI.png" type="image/png">
    <!-- Irish Grover font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Irish+Grover&display=swap" rel="stylesheet">
</head>
<header data-aos="fade-down" data-aos-duration="800" data-aos-once="true">
    <a href="/SCCI-Season26-Platform/home.php" class="logo">
        <img src="/SCCI-Season26-Platform/assets/icons/logoSCCI.png" alt="SCCI logo" loading="lazy" />
        <h1 id="logo">SCCI</h1>
    </a>
    <nav class="navLinks navRespnsive">
        <a href="/SCCI-Season26-Platform/home.php" id="homeNavLine">home</a>
        <a href="/SCCI-Season26-Platform/about.php" id="aboutNavLine">about us</a>
        <a href="/SCCI-Season26-Platform/gallary.php" id="galleryNavLine">gallery</a>
        <a href="/SCCI-Season26-Platform/workshops.php" id="workshopsNavLine">workshops</a>
        <a href="/SCCI-Season26-Platform/crew.php" id="crewNavLine">crew</a>

        <?php
        if ($role == 2 || $role == 1 || $role == 4 || $role == 5 || $committeeId == 6) {
            echo '<div class="nav-separator"></div>';
        }
        ?>

        <?php
        // Build the panels dropdown only for users with panel access
        $panelLinks = [];

        if ($role == 2) {
            $panelLinks[] = ['href' => '/SCCI-Season26-Platform/memberWorkshopPanel.php',   'icon' => 'fa-user-group',    'label' => 'Member Panel'];
            $panelLinks[] = ['href' => '/SCCI-Season26-Platform/conferenceMemberPanel.php', 'icon' => 'fa-graduation-cap','label' => 'Conference Panel'];
        }
        if ($role == 1) {
            $panelLinks[] = ['href' => '/SCCI-Season26-Platform/participantWorkshopPanel.php',  'icon' => 'fa-user-graduate', 'label' => 'Participant Panel'];
            $panelLinks[] = ['href' => '/SCCI-Season26-Platform/conferenceParticipantPanel.php','icon' => 'fa-book-open',     'label' => 'Conference Panel'];
        }
        if ($role == 4) {
            $panelLinks[] = ['href' => '/SCCI-Season26-Platform/headPanel.php',    'icon' => 'fa-user-tie',     'label' => 'Head Panel'];
            $panelLinks[] = ['href' => '/SCCI-Season26-Platform/contactPanel.php', 'icon' => 'fa-address-book', 'label' => 'Contact Panel'];
        }
        if ($role == 5) {
            $panelLinks[] = ['href' => '/SCCI-Season26-Platform/headPanel.php',      'icon' => 'fa-user-tie',   'label' => 'Head Panel'];
            $panelLinks[] = ['href' => '/SCCI-Season26-Platform/adminDashboard.php', 'icon' => 'fa-gauge-high', 'label' => 'Admin Board'];
            $panelLinks[] = ['href' => '/SCCI-Season26-Platform/contactPanel.php',   'icon' => 'fa-address-book','label' => 'Contact Panel'];
        }
        if ($committeeId == 6) {
            $panelLinks[] = ['href' => '/SCCI-Season26-Platform/itPanel.php',         'icon' => 'fa-screwdriver-wrench','label' => 'IT Panel'];
            $panelLinks[] = ['href' => '/SCCI-Season26-Platform/itResetPassword.php', 'icon' => 'fa-key',              'label' => 'Reset Passwords'];
        }

        if (!empty($panelLinks)):
        ?>
        <div class="nav-dropdown" id="navPanelsDropdown">
            <button class="nav-dropdown-trigger" id="navPanelsTrigger" aria-haspopup="true" aria-expanded="false">
                <i class="fas fa-th-large"></i>
                My Panels
                <i class="fas fa-chevron-down nav-dropdown-arrow"></i>
            </button>
            <div class="nav-dropdown-menu" id="navPanelsMenu" role="menu">
                <?php foreach ($panelLinks as $pl): ?>
                <a href="<?= $pl['href'] ?>" class="nav-dropdown-item" role="menuitem">
                    <i class="fas <?= $pl['icon'] ?>"></i>
                    <span><?= $pl['label'] ?></span>
                </a>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
    </nav>
    <nav class="navAccount navRespnsive">
        <?php
        if (isset($_SESSION['user_id'])) {
            echo '<a href="/SCCI-Season26-Platform/profile.php" id="profileNav">
            <img loading="lazy" src="/SCCI-Season26-Platform/assets/img/profilePhoto.png" alt="profile img">
        </a>';
        } else {
            echo '<a href="/SCCI-Season26-Platform/auth/login.php" id="loginNav">Log In</a>';
        }
        ?>
    </nav>
    <button class="sideBtn">
        <span class="burger-line"></span>
        <span class="burger-line"></span>
        <span class="burger-line"></span>
    </button>
</header>

<!-- side navbar -->
<aside class="sideNav">
    <div class="sideNavLinks">
        <a href="/SCCI-Season26-Platform/home.php"><i class="fa-solid fa-house"></i> home</a>
        <a href="/SCCI-Season26-Platform/about.php"><i class="fa-solid fa-book-open"></i> about us</a>
        <a href="/SCCI-Season26-Platform/gallary.php"><i class="fa-solid fa-images"></i> gallery</a>
        <a href="/SCCI-Season26-Platform/workshops.php"><i class="fa-solid fa-graduation-cap"></i> workshops</a>
        <a href="/SCCI-Season26-Platform/crew.php"><i class="fa-solid fa-users"></i> crew</a>

        <?php
        if (!isset($_SESSION['user_id'])) {
            echo '<a href="/SCCI-Season26-Platform/auth/login.php"><i class="fa-solid fa-user"></i> LogIn</a>';
        }
        ?>
        <hr>
        <?php if (isset($_SESSION['user_id'])): ?>
            <?php
            if ($role == 2) {
                echo '<a href="/SCCI-Season26-Platform/memberWorkshopPanel.php"><i class="fa-solid fa-user-group"></i> member panel</a>';
                echo '<a href="/SCCI-Season26-Platform/conferenceMemberPanel.php"><i class="fa-solid fa-graduation-cap"></i> Conference Panel</a>';
            }
            ?>
            <?php
            if ($role == 1) {
                echo '<a href="/SCCI-Season26-Platform/participantWorkshopPanel.php"><i class="fa-solid fa-user-graduate"></i> participant panel</a>';
            }
            ?>
            <?php
            if ($isAcademicParticipant) {
                echo '<a href="/SCCI-Season26-Platform/conferenceParticipantPanel.php"><i class="fa-solid fa-book-open"></i> Conference Panel</a>';
            }
            ?>
            <?php
            if ($role == 4) {
                echo '<a href="/SCCI-Season26-Platform/contactPanel.php"><i class="fa-solid fa-address-book"></i> contact panel</a>';
                echo '<a href="/SCCI-Season26-Platform/headPanel.php"><i class="fa-solid fa-user-tie"></i> head panel</a>';
            }
            ?>
            <?php
            if ($role == 5) {
                echo '<a href="/SCCI-Season26-Platform/headPanel.php" id="homeNavLine">head panel</a>';
                echo '<a href="/SCCI-Season26-Platform/adminDashboard.php"><i class="fa-solid fa-gauge-high"></i> Admin Dashboard</a>';
            }
            ?>
            <?php
            if ($role == 5 or $role == 4) {
                echo '<a href="/SCCI-Season26-Platform/contactPanel.php" id="homeNavLine">contact panel</a>';

            }
            ?>

            <?php
            if ($committeeId == 6) {
                echo '<a href="/SCCI-Season26-Platform/itPanel.php"><i class="fa-solid fa-screwdriver-wrench"></i> IT panel</a>';
                echo '<a href="/SCCI-Season26-Platform/itResetPassword.php"><i class="fa-solid fa-key"></i> Reset Passwords</a>';
            }
            ?>

            <a href="/SCCI-Season26-Platform/profile.php" id="profileNav">
                <img loading="lazy" src="/SCCI-Season26-Platform/assets/img/profilePhoto.png" alt="profile img">
            </a>
        <?php endif; ?>
    </div>
</aside>
<script src="/SCCI-Season26-Platform/assets/js/index.js?v=<?= ASSET_VERSION ?>"></script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        // ── Active link highlighting ──────────────────
        const currentPath = window.location.pathname;
        const navLinks = document.querySelectorAll('.navLinks a, .sideNavLinks a, .nav-dropdown-item');
        navLinks.forEach(link => {
            const linkHref = link.getAttribute('href');
            if (linkHref && (currentPath.endsWith(linkHref) || (linkHref !== '/' && currentPath.includes(linkHref)))) {
                link.classList.add('active');
            }
        });

        // ── My Panels Dropdown ────────────────────────
        const dropdown = document.getElementById('navPanelsDropdown');
        const trigger  = document.getElementById('navPanelsTrigger');
        const menu     = document.getElementById('navPanelsMenu');

        function positionMenu() {
            if (!trigger || !menu) return;
            const rect = trigger.getBoundingClientRect();
            menu.style.top   = (rect.bottom + 8) + 'px';
            menu.style.left  = 'auto';
            menu.style.right = (window.innerWidth - rect.right) + 'px';
        }

        function openMenu() {
            positionMenu();
            dropdown.classList.add('open');
            trigger.setAttribute('aria-expanded', 'true');
        }

        function closeMenu() {
            dropdown.classList.remove('open');
            trigger.setAttribute('aria-expanded', 'false');
        }

        if (dropdown && trigger && menu) {
            trigger.addEventListener('click', function (e) {
                e.stopPropagation();
                dropdown.classList.contains('open') ? closeMenu() : openMenu();
            });

            document.addEventListener('click', function (e) {
                if (!dropdown.contains(e.target) && !menu.contains(e.target)) {
                    closeMenu();
                }
            });

            document.addEventListener('keydown', function (e) {
                if (e.key === 'Escape') closeMenu();
            });

            window.addEventListener('resize', function () {
                if (dropdown.classList.contains('open')) positionMenu();
            });
        }
    });
</script>
