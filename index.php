<?php
session_start();

// Database initialization
function initDatabase() {
    $db = new SQLite3('turkeybowl.db');
    
    // Create tables
    $db->exec('
        CREATE TABLE IF NOT EXISTS admin_users (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            email TEXT UNIQUE NOT NULL,
            password_hash TEXT NOT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )
    ');
    
    $db->exec('
        CREATE TABLE IF NOT EXISTS players (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT NOT NULL,
            nickname TEXT,
            position TEXT,
            bio TEXT,
            photo_path TEXT,
            years_played INTEGER DEFAULT 1,
            current_year BOOLEAN DEFAULT 1,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )
    ');
    
    $db->exec('
        CREATE TABLE IF NOT EXISTS teams (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT NOT NULL,
            captain_id INTEGER,
            logo_path TEXT,
            year INTEGER NOT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (captain_id) REFERENCES players (id)
        )
    ');
    
    $db->exec('
        CREATE TABLE IF NOT EXISTS team_players (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            team_id INTEGER NOT NULL,
            player_id INTEGER NOT NULL,
            draft_order INTEGER,
            FOREIGN KEY (team_id) REFERENCES teams (id),
            FOREIGN KEY (player_id) REFERENCES players (id)
        )
    ');
    
    $db->exec('
        CREATE TABLE IF NOT EXISTS championships (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            year INTEGER NOT NULL,
            team_name TEXT NOT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )
    ');
    
    $db->exec('
        CREATE TABLE IF NOT EXISTS awards (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            year INTEGER NOT NULL,
            award_name TEXT NOT NULL,
            player_name TEXT NOT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )
    ');
    
    $db->exec('
        CREATE TABLE IF NOT EXISTS records (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            year INTEGER NOT NULL,
            record_name TEXT NOT NULL,
            record_value TEXT NOT NULL,
            player_name TEXT,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )
    ');
    
    $db->exec('
        CREATE TABLE IF NOT EXISTS event_settings (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            event_date DATETIME,
            event_location TEXT,
            registration_deadline DATETIME,
            draft_date DATETIME,
            current_year INTEGER DEFAULT 2024,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )
    ');
    
    // Insert default admin user (password: admin123)
    $defaultAdmin = $db->prepare('INSERT OR IGNORE INTO admin_users (email, password_hash) VALUES (?, ?)');
    $defaultAdmin->bindValue(1, 'admin@turkeybowl.com');
    $defaultAdmin->bindValue(2, password_hash('admin123', PASSWORD_DEFAULT));
    $defaultAdmin->execute();
    
    // Insert default event settings
    $defaultSettings = $db->prepare('INSERT OR IGNORE INTO event_settings (event_date, event_location, registration_deadline, draft_date) VALUES (?, ?, ?, ?)');
    $defaultSettings->bindValue(1, '2024-11-28 10:00:00');
    $defaultSettings->bindValue(2, 'Central Park Field #3');
    $defaultSettings->bindValue(3, '2024-11-20 23:59:59');
    $defaultSettings->bindValue(4, '2024-11-25 19:00:00');
    $defaultSettings->execute();
    
    return $db;
}

// Initialize database
$db = initDatabase();

// Helper functions
function isLoggedIn() {
    return isset($_SESSION['admin_id']);
}

function requireAdmin() {
    if (!isLoggedIn()) {
        header('Location: ?page=login');
        exit;
    }
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'login':
                $email = $_POST['email'] ?? '';
                $password = $_POST['password'] ?? '';
                
                $stmt = $db->prepare('SELECT id, password_hash FROM admin_users WHERE email = ?');
                $stmt->bindValue(1, $email);
                $result = $stmt->execute();
                $user = $result->fetchArray(SQLITE3_ASSOC);
                
                if ($user && password_verify($password, $user['password_hash'])) {
                    $_SESSION['admin_id'] = $user['id'];
                    header('Location: ?page=admin');
                    exit;
                } else {
                    $loginError = 'Invalid email or password';
                }
                break;
                
            case 'logout':
                session_destroy();
                header('Location: ?page=home');
                exit;
                break;
        }
    }
}

// Get current page
$page = $_GET['page'] ?? 'home';

// Get event settings
$eventSettings = $db->query('SELECT * FROM event_settings ORDER BY id DESC LIMIT 1')->fetchArray(SQLITE3_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TurkeyBowl - Annual Flag Football Championship</title>
    <style>
        :root {
            --navy-blue: #1a2332;
            --bright-orange: #ff6600;
            --metallic-silver: #c0c0c0;
            --pure-white: #ffffff;
            --dark-gray: #333333;
            --gold-accent: #ffd700;
            --success-green: #228b22;
            --alert-red: #cc0000;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            background: linear-gradient(135deg, var(--navy-blue) 0%, var(--dark-gray) 100%);
            color: var(--pure-white);
            min-height: 100vh;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        /* Header */
        .header {
            background: linear-gradient(180deg, var(--navy-blue) 0%, #0f1419 100%);
            border-bottom: 3px solid var(--bright-orange);
            box-shadow: 0 4px 8px rgba(0,0,0,0.3);
        }

        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px 0;
        }

        .logo {
            font-family: 'Arial Black', Arial, sans-serif;
            font-size: 2.5rem;
            font-weight: 900;
            color: var(--bright-orange);
            text-shadow: 2px 2px 4px rgba(0,0,0,0.5);
        }

        /* Navigation */
        .nav {
            background: linear-gradient(180deg, var(--dark-gray) 0%, #2a2a2a 100%);
            border-bottom: 2px solid var(--metallic-silver);
        }

        .nav-content {
            display: flex;
            justify-content: center;
        }

        .nav-menu {
            display: flex;
            list-style: none;
        }

        .nav-item {
            margin: 0 5px;
        }

        .nav-link {
            display: block;
            padding: 15px 25px;
            text-decoration: none;
            color: var(--pure-white);
            font-weight: bold;
            text-transform: uppercase;
            background: linear-gradient(180deg, #555 0%, #333 100%);
            border: 2px solid var(--metallic-silver);
            border-radius: 8px 8px 0 0;
            transition: all 0.3s ease;
        }

        .nav-link:hover, .nav-link.active {
            background: linear-gradient(180deg, var(--bright-orange) 0%, #cc5500 100%);
            border-color: var(--gold-accent);
            transform: translateY(-2px);
        }

        /* Main Content */
        .main {
            padding: 40px 0;
            min-height: calc(100vh - 200px);
        }

        .card {
            background: linear-gradient(145deg, #2a2a2a 0%, #1a1a1a 100%);
            border: 2px solid var(--metallic-silver);
            border-radius: 8px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 0 8px 16px rgba(0,0,0,0.3);
        }

        .card-title {
            font-family: 'Arial Black', Arial, sans-serif;
            font-size: 2rem;
            color: var(--bright-orange);
            margin-bottom: 20px;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.5);
        }

        /* Buttons */
        .btn {
            display: inline-block;
            padding: 12px 24px;
            font-size: 1rem;
            font-weight: bold;
            text-decoration: none;
            text-align: center;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.3s ease;
            text-transform: uppercase;
        }

        .btn-primary {
            background: linear-gradient(180deg, var(--bright-orange) 0%, #cc5500 100%);
            color: var(--pure-white);
            border: 2px solid var(--gold-accent);
        }

        .btn-primary:hover {
            background: linear-gradient(180deg, #ff7700 0%, var(--bright-orange) 100%);
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(255,102,0,0.3);
        }

        .btn-secondary {
            background: linear-gradient(180deg, var(--metallic-silver) 0%, #999 100%);
            color: var(--dark-gray);
            border: 2px solid var(--pure-white);
        }

        /* Forms */
        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: var(--bright-orange);
        }

        .form-input {
            width: 100%;
            padding: 12px;
            font-size: 1rem;
            border: 2px solid var(--metallic-silver);
            border-radius: 6px;
            background: var(--pure-white);
            color: var(--dark-gray);
        }

        .form-input:focus {
            outline: none;
            border-color: var(--bright-orange);
            box-shadow: 0 0 8px rgba(255,102,0,0.3);
        }

        /* Countdown Timer */
        .countdown {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            margin: 30px 0;
        }

        .countdown-item {
            text-align: center;
            background: linear-gradient(145deg, var(--dark-gray) 0%, #1a1a1a 100%);
            border: 2px solid var(--bright-orange);
            border-radius: 8px;
            padding: 20px;
        }

        .countdown-number {
            font-family: 'Courier New', monospace;
            font-size: 2.5rem;
            font-weight: bold;
            color: var(--gold-accent);
            display: block;
        }

        .countdown-label {
            font-size: 0.9rem;
            color: var(--metallic-silver);
            text-transform: uppercase;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .header-content {
                flex-direction: column;
                gap: 15px;
            }

            .nav-menu {
                flex-wrap: wrap;
                justify-content: center;
            }

            .nav-item {
                margin: 5px;
            }

            .nav-link {
                padding: 10px 15px;
                font-size: 0.9rem;
            }

            .countdown {
                grid-template-columns: repeat(2, 1fr);
            }

            .logo {
                font-size: 2rem;
            }
        }

        .error-message {
            background: var(--alert-red);
            color: var(--pure-white);
            padding: 10px;
            border-radius: 6px;
            margin-bottom: 20px;
        }

        .success-message {
            background: var(--success-green);
            color: var(--pure-white);
            padding: 10px;
            border-radius: 6px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="container">
            <div class="header-content">
                <div class="logo">TURKEY BOWL</div>
                <?php if (isLoggedIn()): ?>
                    <form method="POST" style="display: inline;">
                        <input type="hidden" name="action" value="logout">
                        <button type="submit" class="btn btn-secondary">Logout</button>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </header>

    <nav class="nav">
        <div class="container">
            <div class="nav-content">
                <ul class="nav-menu">
                    <li class="nav-item">
                        <a href="?page=home" class="nav-link <?= $page === 'home' ? 'active' : '' ?>">Home</a>
                    </li>
                    <li class="nav-item">
                        <a href="?page=history" class="nav-link <?= $page === 'history' ? 'active' : '' ?>">Hall of Fame</a>
                    </li>
                    <li class="nav-item">
                        <a href="?page=roster" class="nav-link <?= $page === 'roster' ? 'active' : '' ?>">Roster</a>
                    </li>
                    <li class="nav-item">
                        <a href="?page=teams" class="nav-link <?= $page === 'teams' ? 'active' : '' ?>">Teams</a>
                    </li>
                    <?php if (isLoggedIn()): ?>
                        <li class="nav-item">
                            <a href="?page=draft" class="nav-link <?= $page === 'draft' ? 'active' : '' ?>">Draft</a>
                        </li>
                        <li class="nav-item">
                            <a href="?page=admin" class="nav-link <?= $page === 'admin' ? 'active' : '' ?>">Admin</a>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a href="?page=login" class="nav-link <?= $page === 'login' ? 'active' : '' ?>">Admin Login</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <main class="main">
        <div class="container">
            <?php
            // Page routing and content generation
            switch ($page) {
                case 'home':
                    echo '<div class="card">
                        <h1 class="card-title">Welcome to Turkey Bowl 2024</h1>
                        <p>The annual flag football championship returns! Get ready for another legendary battle on the gridiron.</p>
                        
                        <div class="card" style="background: linear-gradient(145deg, var(--dark-gray) 0%, #0f1419 100%); border-color: var(--bright-orange);">
                            <h2 style="color: var(--gold-accent); margin-bottom: 20px;">Event Information</h2>
                            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px;">
                                <div>
                                    <strong style="color: var(--bright-orange);">Game Day:</strong><br>
                                    ' . date('F j, Y \a\t g:i A', strtotime($eventSettings['event_date'])) . '
                                </div>
                                <div>
                                    <strong style="color: var(--bright-orange);">Location:</strong><br>
                                    ' . htmlspecialchars($eventSettings['event_location']) . '
                                </div>
                                <div>
                                    <strong style="color: var(--bright-orange);">Registration Deadline:</strong><br>
                                    ' . date('F j, Y \a\t g:i A', strtotime($eventSettings['registration_deadline'])) . '
                                </div>
                                <div>
                                    <strong style="color: var(--bright-orange);">Draft:</strong><br>
                                    ' . date('F j, Y \a\t g:i A', strtotime($eventSettings['draft_date'])) . '
                                </div>
                            </div>
                        </div>
                        
                        <div id="countdown-container">
                            <h2 style="color: var(--gold-accent); text-align: center; margin-bottom: 20px;">Countdown to Game Day</h2>
                            <div class="countdown">
                                <div class="countdown-item">
                                    <span class="countdown-number" id="days">0</span>
                                    <span class="countdown-label">Days</span>
                                </div>
                                <div class="countdown-item">
                                    <span class="countdown-number" id="hours">0</span>
                                    <span class="countdown-label">Hours</span>
                                </div>
                                <div class="countdown-item">
                                    <span class="countdown-number" id="minutes">0</span>
                                    <span class="countdown-label">Minutes</span>
                                </div>
                                <div class="countdown-item">
                                    <span class="countdown-number" id="seconds">0</span>
                                    <span class="countdown-label">Seconds</span>
                                </div>
                            </div>
                        </div>
                    </div>';
                    break;
                    
                case 'history':
                    // Get championships
                    $championships = $db->query('SELECT * FROM championships ORDER BY year DESC');
                    $awards = $db->query('SELECT * FROM awards ORDER BY year DESC, award_name');
                    $records = $db->query('SELECT * FROM records ORDER BY year DESC, record_name');
                    
                    echo '<div class="card">
                        <h1 class="card-title">Hall of Fame & History</h1>
                        <p>Celebrating the legends and memorable moments from Turkey Bowl history.</p>
                    </div>';
                    
                    // Championships Section
                    echo '<div class="card">
                        <h2 style="color: var(--gold-accent); margin-bottom: 20px; font-size: 1.8rem;">🏆 Championship Teams</h2>
                        <div style="overflow-x: auto;">
                            <table style="width: 100%; border-collapse: collapse;">
                                <thead>
                                    <tr style="background: linear-gradient(180deg, var(--bright-orange) 0%, #cc5500 100%); color: white;">
                                        <th style="padding: 15px; text-align: left; border: 2px solid var(--gold-accent);">Year</th>
                                        <th style="padding: 15px; text-align: left; border: 2px solid var(--gold-accent);">Champions</th>
                                    </tr>
                                </thead>
                                <tbody>';
                    
                    $hasChampionships = false;
                    while ($championship = $championships->fetchArray(SQLITE3_ASSOC)) {
                        $hasChampionships = true;
                        echo '<tr style="background: rgba(192,192,192,0.1); border-bottom: 1px solid var(--metallic-silver);">
                                <td style="padding: 12px; font-weight: bold; color: var(--gold-accent);">' . $championship['year'] . '</td>
                                <td style="padding: 12px;">' . htmlspecialchars($championship['team_name']) . '</td>
                              </tr>';
                    }
                    
                    if (!$hasChampionships) {
                        echo '<tr><td colspan="2" style="padding: 20px; text-align: center; color: var(--metallic-silver);">No championship history yet. Make history this year!</td></tr>';
                    }
                    
                    echo '    </tbody>
                            </table>
                        </div>
                    </div>';
                    
                    // Awards Section
                    echo '<div class="card">
                        <h2 style="color: var(--gold-accent); margin-bottom: 20px; font-size: 1.8rem;">🎖️ Individual Awards</h2>
                        <div style="overflow-x: auto;">
                            <table style="width: 100%; border-collapse: collapse;">
                                <thead>
                                    <tr style="background: linear-gradient(180deg, var(--bright-orange) 0%, #cc5500 100%); color: white;">
                                        <th style="padding: 15px; text-align: left; border: 2px solid var(--gold-accent);">Year</th>
                                        <th style="padding: 15px; text-align: left; border: 2px solid var(--gold-accent);">Award</th>
                                        <th style="padding: 15px; text-align: left; border: 2px solid var(--gold-accent);">Winner</th>
                                    </tr>
                                </thead>
                                <tbody>';
                    
                    $hasAwards = false;
                    while ($award = $awards->fetchArray(SQLITE3_ASSOC)) {
                        $hasAwards = true;
                        echo '<tr style="background: rgba(192,192,192,0.1); border-bottom: 1px solid var(--metallic-silver);">
                                <td style="padding: 12px; font-weight: bold; color: var(--gold-accent);">' . $award['year'] . '</td>
                                <td style="padding: 12px; color: var(--bright-orange);">' . htmlspecialchars($award['award_name']) . '</td>
                                <td style="padding: 12px;">' . htmlspecialchars($award['player_name']) . '</td>
                              </tr>';
                    }
                    
                    if (!$hasAwards) {
                        echo '<tr><td colspan="3" style="padding: 20px; text-align: center; color: var(--metallic-silver);">No awards recorded yet. Be the first to claim glory!</td></tr>';
                    }
                    
                    echo '    </tbody>
                            </table>
                        </div>
                    </div>';
                    
                    // Records Section
                    echo '<div class="card">
                        <h2 style="color: var(--gold-accent); margin-bottom: 20px; font-size: 1.8rem;">📊 Fun Records</h2>
                        <div style="overflow-x: auto;">
                            <table style="width: 100%; border-collapse: collapse;">
                                <thead>
                                    <tr style="background: linear-gradient(180deg, var(--bright-orange) 0%, #cc5500 100%); color: white;">
                                        <th style="padding: 15px; text-align: left; border: 2px solid var(--gold-accent);">Year</th>
                                        <th style="padding: 15px; text-align: left; border: 2px solid var(--gold-accent);">Record</th>
                                        <th style="padding: 15px; text-align: left; border: 2px solid var(--gold-accent);">Value</th>
                                        <th style="padding: 15px; text-align: left; border: 2px solid var(--gold-accent);">Player</th>
                                    </tr>
                                </thead>
                                <tbody>';
                    
                    $hasRecords = false;
                    while ($record = $records->fetchArray(SQLITE3_ASSOC)) {
                        $hasRecords = true;
                        echo '<tr style="background: rgba(192,192,192,0.1); border-bottom: 1px solid var(--metallic-silver);">
                                <td style="padding: 12px; font-weight: bold; color: var(--gold-accent);">' . $record['year'] . '</td>
                                <td style="padding: 12px; color: var(--bright-orange);">' . htmlspecialchars($record['record_name']) . '</td>
                                <td style="padding: 12px; font-family: monospace; font-weight: bold;">' . htmlspecialchars($record['record_value']) . '</td>
                                <td style="padding: 12px;">' . htmlspecialchars($record['player_name'] ?? 'N/A') . '</td>
                              </tr>';
                    }
                    
                    if (!$hasRecords) {
                        echo '<tr><td colspan="4" style="padding: 20px; text-align: center; color: var(--metallic-silver);">No records set yet. Time to make history!</td></tr>';
                    }
                    
                    echo '    </tbody>
                            </table>
                        </div>
                    </div>';
                    break;
                    
                case 'roster':
                    // Get current year players
                    $players = $db->query('SELECT * FROM players WHERE current_year = 1 ORDER BY name');
                    
                    echo '<div class="card">
                        <h1 class="card-title">2024 Turkey Bowl Roster</h1>
                        <p>Meet this year\'s warriors ready to battle for flag football supremacy!</p>
                    </div>';
                    
                    echo '<div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 20px;">';
                    
                    $hasPlayers = false;
                    while ($player = $players->fetchArray(SQLITE3_ASSOC)) {
                        $hasPlayers = true;
                        $photoPath = $player['photo_path'] ? htmlspecialchars($player['photo_path']) : 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjAwIiBoZWlnaHQ9IjIwMCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48cmVjdCB3aWR0aD0iMjAwIiBoZWlnaHQ9IjIwMCIgZmlsbD0iIzMzMyIvPjx0ZXh0IHg9IjUwJSIgeT0iNTAlIiBmb250LWZhbWlseT0iQXJpYWwiIGZvbnQtc2l6ZT0iMTQiIGZpbGw9IiNmZmYiIHRleHQtYW5jaG9yPSJtaWRkbGUiIGR5PSIuM2VtIj5ObyBQaG90bzwvdGV4dD48L3N2Zz4=';
                        
                        echo '<div class="card" style="background: linear-gradient(145deg, #2a2a2a 0%, #1a1a1a 100%); border: 2px solid var(--bright-orange); transition: all 0.3s ease;" onmouseover="this.style.transform=\'translateY(-5px)\'" onmouseout="this.style.transform=\'translateY(0)\'">
                                <div style="text-align: center;">
                                    <div style="width: 120px; height: 120px; margin: 0 auto 15px; border-radius: 50%; overflow: hidden; border: 3px solid var(--gold-accent); background: var(--dark-gray);">
                                        <img src="' . $photoPath . '" alt="' . htmlspecialchars($player['name']) . '" style="width: 100%; height: 100%; object-fit: cover;" onerror="this.src=\'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjAwIiBoZWlnaHQ9IjIwMCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48cmVjdCB3aWR0aD0iMjAwIiBoZWlnaHQ9IjIwMCIgZmlsbD0iIzMzMyIvPjx0ZXh0IHg9IjUwJSIgeT0iNTAlIiBmb250LWZhbWlseT0iQXJpYWwiIGZvbnQtc2l6ZT0iMTQiIGZpbGw9IiNmZmYiIHRleHQtYW5jaG9yPSJtaWRkbGUiIGR5PSIuM2VtIj5ObyBQaG90bzwvdGV4dD48L3N2Zz4=\'">
                                    </div>
                                    
                                    <h3 style="color: var(--bright-orange); font-size: 1.3rem; margin-bottom: 5px;">' . htmlspecialchars($player['name']) . '</h3>';
                        
                        if ($player['nickname']) {
                            echo '<p style="color: var(--gold-accent); font-style: italic; margin-bottom: 10px;">"' . htmlspecialchars($player['nickname']) . '"</p>';
                        }
                        
                        echo '<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin: 15px 0; text-align: left;">
                                        <div style="background: rgba(255,102,0,0.1); padding: 8px; border-radius: 4px; border-left: 3px solid var(--bright-orange);">
                                            <strong style="color: var(--bright-orange); font-size: 0.8rem;">POSITION</strong><br>
                                            <span style="color: var(--pure-white);">' . htmlspecialchars($player['position'] ?: 'Utility') . '</span>
                                        </div>
                                        <div style="background: rgba(255,215,0,0.1); padding: 8px; border-radius: 4px; border-left: 3px solid var(--gold-accent);">
                                            <strong style="color: var(--gold-accent); font-size: 0.8rem;">YEARS</strong><br>
                                            <span style="color: var(--pure-white); font-family: monospace;">' . $player['years_played'] . '</span>
                                        </div>
                                    </div>';
                        
                        if ($player['bio']) {
                            echo '<div style="background: rgba(192,192,192,0.1); padding: 12px; border-radius: 6px; border: 1px solid var(--metallic-silver); margin-top: 10px;">
                                    <p style="color: var(--metallic-silver); font-size: 0.9rem; line-height: 1.4;">' . htmlspecialchars($player['bio']) . '</p>
                                  </div>';
                        }
                        
                        echo '    </div>
                            </div>';
                    }
                    
                    if (!$hasPlayers) {
                        echo '<div class="card" style="grid-column: 1 / -1; text-align: center;">
                                <h2 style="color: var(--metallic-silver);">No Players Registered Yet</h2>
                                <p>Players will appear here once they\'re added to the roster.</p>
                              </div>';
                    }
                    
                    echo '</div>';
                    break;
                    
                case 'teams':
                    // Get current year teams and their players
                    $currentYear = $eventSettings['current_year'] ?? date('Y');
                    $teams = $db->query("SELECT * FROM teams WHERE year = $currentYear ORDER BY name");
                    
                    echo '<div class="card">
                        <h1 class="card-title">2024 Team Lineups</h1>
                        <p>Check out this year\'s team rosters and prepare for battle!</p>
                    </div>';
                    
                    $hasTeams = false;
                    while ($team = $teams->fetchArray(SQLITE3_ASSOC)) {
                        $hasTeams = true;
                        
                        // Get team players
                        $teamPlayersQuery = $db->prepare('
                            SELECT p.* FROM players p 
                            JOIN team_players tp ON p.id = tp.player_id 
                            WHERE tp.team_id = ? 
                            ORDER BY tp.draft_order, p.name
                        ');
                        $teamPlayersQuery->bindValue(1, $team['id']);
                        $teamPlayers = $teamPlayersQuery->execute();
                        
                        // Get captain info
                        $captain = null;
                        if ($team['captain_id']) {
                            $captainQuery = $db->prepare('SELECT name FROM players WHERE id = ?');
                            $captainQuery->bindValue(1, $team['captain_id']);
                            $captainResult = $captainQuery->execute();
                            $captain = $captainResult->fetchArray(SQLITE3_ASSOC);
                        }
                        
                        echo '<div class="card" style="border: 3px solid var(--bright-orange);">
                                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                                    <h2 style="color: var(--gold-accent); font-size: 2rem; margin: 0;">' . htmlspecialchars($team['name']) . '</h2>';
                        
                        if ($team['logo_path']) {
                            echo '<div style="width: 60px; height: 60px; border-radius: 50%; overflow: hidden; border: 2px solid var(--gold-accent);">
                                    <img src="' . htmlspecialchars($team['logo_path']) . '" alt="Team Logo" style="width: 100%; height: 100%; object-fit: cover;">
                                  </div>';
                        }
                        
                        echo '</div>';
                        
                        if ($captain) {
                            echo '<p style="color: var(--bright-orange); margin-bottom: 15px;">
                                    <strong>Captain:</strong> ' . htmlspecialchars($captain['name']) . '
                                  </p>';
                        }
                        
                        echo '<div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 15px;">';
                        
                        $playerCount = 0;
                        while ($player = $teamPlayers->fetchArray(SQLITE3_ASSOC)) {
                            $playerCount++;
                            $photoPath = $player['photo_path'] ? htmlspecialchars($player['photo_path']) : 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjAwIiBoZWlnaHQ9IjIwMCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48cmVjdCB3aWR0aD0iMjAwIiBoZWlnaHQ9IjIwMCIgZmlsbD0iIzMzMyIvPjx0ZXh0IHg9IjUwJSIgeT0iNTAlIiBmb250LWZhbWlseT0iQXJpYWwiIGZvbnQtc2l6ZT0iMTQiIGZpbGw9IiNmZmYiIHRleHQtYW5jaG9yPSJtaWRkbGUiIGR5PSIuM2VtIj5ObyBQaG90bzwvdGV4dD48L3N2Zz4=';
                            
                            echo '<div style="background: rgba(255,102,0,0.1); border: 1px solid var(--metallic-silver); border-radius: 6px; padding: 15px; text-align: center;">
                                    <div style="width: 60px; height: 60px; margin: 0 auto 10px; border-radius: 50%; overflow: hidden; border: 2px solid var(--bright-orange);">
                                        <img src="' . $photoPath . '" alt="' . htmlspecialchars($player['name']) . '" style="width: 100%; height: 100%; object-fit: cover;" onerror="this.src=\'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjAwIiBoZWlnaHQ9IjIwMCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48cmVjdCB3aWR0aD0iMjAwIiBoZWlnaHQ9IjIwMCIgZmlsbD0iIzMzMyIvPjx0ZXh0IHg9IjUwJSIgeT0iNTAlIiBmb250LWZhbWlseT0iQXJpYWwiIGZvbnQtc2l6ZT0iMTQiIGZpbGw9IiNmZmYiIHRleHQtYW5jaG9yPSJtaWRkbGUiIGR5PSIuM2VtIj5ObyBQaG90bzwvdGV4dD48L3N2Zz4=\'">
                                    </div>
                                    <h4 style="color: var(--pure-white); margin: 0 0 5px 0;">' . htmlspecialchars($player['name']) . '</h4>
                                    <p style="color: var(--metallic-silver); font-size: 0.8rem; margin: 0;">' . htmlspecialchars($player['position'] ?: 'Utility') . '</p>
                                  </div>';
                        }
                        
                        if ($playerCount === 0) {
                            echo '<div style="grid-column: 1 / -1; text-align: center; color: var(--metallic-silver); padding: 20px;">
                                    <p>No players drafted yet.</p>
                                  </div>';
                        }
                        
                        echo '</div>
                            </div>';
                    }
                    
                    if (!$hasTeams) {
                        echo '<div class="card" style="text-align: center;">
                                <h2 style="color: var(--metallic-silver);">No Teams Created Yet</h2>
                                <p>Teams will appear here after the draft begins.</p>
                              </div>';
                    }
                    break;
                    
                case 'login':
                    echo '<div class="card" style="max-width: 400px; margin: 0 auto;">
                        <h1 class="card-title">Admin Login</h1>';
                    
                    if (isset($loginError)) {
                        echo '<div class="error-message">' . htmlspecialchars($loginError) . '</div>';
                    }
                    
                    echo '<form method="POST">
                            <input type="hidden" name="action" value="login">
                            <div class="form-group">
                                <label for="email" class="form-label">Email:</label>
                                <input type="email" id="email" name="email" class="form-input" required>
                            </div>
                            <div class="form-group">
                                <label for="password" class="form-label">Password:</label>
                                <input type="password" id="password" name="password" class="form-input" required>
                            </div>
                            <button type="submit" class="btn btn-primary" style="width: 100%;">Login</button>
                        </form>
                        <p style="margin-top: 20px; text-align: center; color: var(--metallic-silver); font-size: 0.9rem;">
                            Default: admin@turkeybowl.com / admin123
                        </p>
                    </div>';
                    break;
                    
                default:
                    echo '<div class="card">
                        <h1 class="card-title">Page Coming Soon</h1>
                        <p>This page is under construction. Check back soon!</p>
                    </div>';
            }
            ?>
        </div>
    </main>

    <script>
        // Countdown timer functionality
        function updateCountdown() {
            const eventDate = new Date('<?= $eventSettings['event_date'] ?? '2024-11-28T10:00:00' ?>').getTime();
            const now = new Date().getTime();
            const distance = eventDate - now;

            if (distance > 0) {
                const days = Math.floor(distance / (1000 * 60 * 60 * 24));
                const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                const seconds = Math.floor((distance % (1000 * 60)) / 1000);

                document.getElementById('days').textContent = days;
                document.getElementById('hours').textContent = hours;
                document.getElementById('minutes').textContent = minutes;
                document.getElementById('seconds').textContent = seconds;
            } else {
                document.getElementById('countdown-container').innerHTML = '<h2 style="color: var(--gold-accent); text-align: center;">GAME DAY!</h2>';
            }
        }

        // Update countdown every second
        if (document.getElementById('countdown-container')) {
            updateCountdown();
            setInterval(updateCountdown, 1000);
        }
    </script>
</body>
</html>
