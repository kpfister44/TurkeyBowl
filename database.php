<?php
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
            current_year INTEGER DEFAULT 2024,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )
    ');
    
    
    // Insert default admin user (password: KeP@01241992computer)
    $defaultAdmin = $db->prepare('INSERT OR IGNORE INTO admin_users (email, password_hash) VALUES (?, ?)');
    $defaultAdmin->bindValue(1, 'kpfister44');
    $defaultAdmin->bindValue(2, password_hash('KeP@01241992computer', PASSWORD_DEFAULT));
    $defaultAdmin->execute();
    
    // Insert default event settings only if none exist
    $checkSettings = $db->query('SELECT COUNT(*) as count FROM event_settings');
    $settingsCount = $checkSettings->fetchArray(SQLITE3_ASSOC);
    
    if ($settingsCount['count'] == 0) {
        $defaultSettings = $db->prepare('INSERT INTO event_settings (event_date, event_location, registration_deadline, current_year) VALUES (?, ?, ?, ?)');
        $defaultSettings->bindValue(1, '2024-11-28 10:00:00');
        $defaultSettings->bindValue(2, 'Central Park Field #3');
        $defaultSettings->bindValue(3, '2024-11-20 23:59:59');
        $defaultSettings->bindValue(4, '2024-11-25 19:00:00');
        $defaultSettings->bindValue(4, 2024);
        $defaultSettings->execute();
    }
    
    return $db;
}

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


// Initialize database and return connection
function getDatabaseConnection() {
    return initDatabase();
}