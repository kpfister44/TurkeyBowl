<?php
session_start();

// Include all component files
require_once 'database.php';
require_once 'actions.php';
require_once 'pages.php';
require_once 'assets.php';

// Initialize database and get connection
$db = getDatabaseConnection();

// Get event settings
$eventSettings = $db->query('SELECT * FROM event_settings ORDER BY id DESC LIMIT 1')->fetchArray(SQLITE3_ASSOC);

// Handle form submissions
$loginError = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $loginError = handleFormSubmission($db);
}

// Get current page
$page = $_GET['page'] ?? 'home';

// Redirect to login if accessing admin without authentication
if ($page === 'admin' && !isLoggedIn()) {
    $page = 'login';
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Turkey Bowl 2025 - Annual Flag Football Championship</title>
    <meta name="description" content="Turkey Bowl 2025 - The ultimate flag football showdown featuring player drafts, team management, and legendary competition.">
    
    <?php generateAssets($eventSettings); ?>
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="container">
            <div class="header-content">
                <div class="logo">EG TURKEY BOWL</div>
                <nav>
                    <ul class="nav">
                        <li class="nav-item">
                            <a href="?page=home" class="nav-link <?= $page === 'home' ? 'active' : '' ?>">Home</a>
                        </li>
                        <li class="nav-item">
                            <a href="?page=history" class="nav-link <?= $page === 'history' ? 'active' : '' ?>">History</a>
                        </li>
                        <li class="nav-item">
                            <a href="?page=roster" class="nav-link <?= $page === 'roster' ? 'active' : '' ?>">Roster</a>
                        </li>
                        <li class="nav-item">
                            <a href="?page=teams" class="nav-link <?= $page === 'teams' ? 'active' : '' ?>">Teams</a>
                        </li>
                        <?php if (isLoggedIn()): ?>
                            <li class="nav-item">
                                <a href="?page=admin" class="nav-link <?= $page === 'admin' ? 'active' : '' ?>">Admin</a>
                            </li>
                            <li class="nav-item">
                                <form method="POST" style="display: inline;">
                                    <input type="hidden" name="action" value="logout">
                                    <button type="submit" class="nav-link" style="background: none; border: none; cursor: pointer; padding: 12px 20px;">Logout</button>
                                </form>
                            </li>
                        <?php else: ?>
                            <li class="nav-item">
                                <a href="?page=login" class="nav-link <?= $page === 'login' ? 'active' : '' ?>">Admin Login</a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </nav>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="main">
        <div class="container">
            <?php generatePageContent($page, $db, $eventSettings, $loginError); ?>
        </div>
    </main>
</body>
</html>