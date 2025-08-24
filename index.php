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
    
    // Insert default event settings only if none exist
    $checkSettings = $db->query('SELECT COUNT(*) as count FROM event_settings');
    $settingsCount = $checkSettings->fetchArray(SQLITE3_ASSOC);
    
    if ($settingsCount['count'] == 0) {
        $defaultSettings = $db->prepare('INSERT INTO event_settings (event_date, event_location, registration_deadline, draft_date, current_year) VALUES (?, ?, ?, ?, ?)');
        $defaultSettings->bindValue(1, '2024-11-28 10:00:00');
        $defaultSettings->bindValue(2, 'Central Park Field #3');
        $defaultSettings->bindValue(3, '2024-11-20 23:59:59');
        $defaultSettings->bindValue(4, '2024-11-25 19:00:00');
        $defaultSettings->bindValue(5, 2024);
        $defaultSettings->execute();
    }
    
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
                
            // Championship CRUD operations
            case 'add_championship':
                requireAdmin();
                $year = filter_var($_POST['year'] ?? '', FILTER_VALIDATE_INT);
                $team_name = trim($_POST['team_name'] ?? '');
                
                if ($year && $team_name && $year >= 1900 && $year <= 2100) {
                    $stmt = $db->prepare('INSERT INTO championships (year, team_name) VALUES (?, ?)');
                    $stmt->bindValue(1, $year);
                    $stmt->bindValue(2, $team_name);
                    if ($stmt->execute()) {
                        $_SESSION['success_message'] = 'Championship added successfully!';
                    } else {
                        $_SESSION['error_message'] = 'Error adding championship.';
                    }
                } else {
                    $_SESSION['error_message'] = 'Please provide valid year and team name.';
                }
                header('Location: ?page=admin&tab=championships');
                exit;
                break;
                
            case 'edit_championship':
                requireAdmin();
                $id = filter_var($_POST['id'] ?? '', FILTER_VALIDATE_INT);
                $year = filter_var($_POST['year'] ?? '', FILTER_VALIDATE_INT);
                $team_name = trim($_POST['team_name'] ?? '');
                
                if ($id && $year && $team_name && $year >= 1900 && $year <= 2100) {
                    $stmt = $db->prepare('UPDATE championships SET year = ?, team_name = ? WHERE id = ?');
                    $stmt->bindValue(1, $year);
                    $stmt->bindValue(2, $team_name);
                    $stmt->bindValue(3, $id);
                    if ($stmt->execute()) {
                        $_SESSION['success_message'] = 'Championship updated successfully!';
                    } else {
                        $_SESSION['error_message'] = 'Error updating championship.';
                    }
                } else {
                    $_SESSION['error_message'] = 'Please provide valid data.';
                }
                header('Location: ?page=admin&tab=championships');
                exit;
                break;
                
            case 'delete_championship':
                requireAdmin();
                $id = filter_var($_POST['id'] ?? '', FILTER_VALIDATE_INT);
                
                if ($id) {
                    $stmt = $db->prepare('DELETE FROM championships WHERE id = ?');
                    $stmt->bindValue(1, $id);
                    if ($stmt->execute()) {
                        $_SESSION['success_message'] = 'Championship deleted successfully!';
                    } else {
                        $_SESSION['error_message'] = 'Error deleting championship.';
                    }
                } else {
                    $_SESSION['error_message'] = 'Invalid championship ID.';
                }
                header('Location: ?page=admin&tab=championships');
                exit;
                break;
                
            // Award CRUD operations
            case 'add_award':
                requireAdmin();
                $year = filter_var($_POST['year'] ?? '', FILTER_VALIDATE_INT);
                $award_name = trim($_POST['award_name'] ?? '');
                $player_name = trim($_POST['player_name'] ?? '');
                
                if ($year && $award_name && $player_name && $year >= 1900 && $year <= 2100) {
                    $stmt = $db->prepare('INSERT INTO awards (year, award_name, player_name) VALUES (?, ?, ?)');
                    $stmt->bindValue(1, $year);
                    $stmt->bindValue(2, $award_name);
                    $stmt->bindValue(3, $player_name);
                    if ($stmt->execute()) {
                        $_SESSION['success_message'] = 'Award added successfully!';
                    } else {
                        $_SESSION['error_message'] = 'Error adding award.';
                    }
                } else {
                    $_SESSION['error_message'] = 'Please provide valid year, award name, and player name.';
                }
                header('Location: ?page=admin&tab=awards');
                exit;
                break;
                
            case 'edit_award':
                requireAdmin();
                $id = filter_var($_POST['id'] ?? '', FILTER_VALIDATE_INT);
                $year = filter_var($_POST['year'] ?? '', FILTER_VALIDATE_INT);
                $award_name = trim($_POST['award_name'] ?? '');
                $player_name = trim($_POST['player_name'] ?? '');
                
                if ($id && $year && $award_name && $player_name && $year >= 1900 && $year <= 2100) {
                    $stmt = $db->prepare('UPDATE awards SET year = ?, award_name = ?, player_name = ? WHERE id = ?');
                    $stmt->bindValue(1, $year);
                    $stmt->bindValue(2, $award_name);
                    $stmt->bindValue(3, $player_name);
                    $stmt->bindValue(4, $id);
                    if ($stmt->execute()) {
                        $_SESSION['success_message'] = 'Award updated successfully!';
                    } else {
                        $_SESSION['error_message'] = 'Error updating award.';
                    }
                } else {
                    $_SESSION['error_message'] = 'Please provide valid data.';
                }
                header('Location: ?page=admin&tab=awards');
                exit;
                break;
                
            case 'delete_award':
                requireAdmin();
                $id = filter_var($_POST['id'] ?? '', FILTER_VALIDATE_INT);
                
                if ($id) {
                    $stmt = $db->prepare('DELETE FROM awards WHERE id = ?');
                    $stmt->bindValue(1, $id);
                    if ($stmt->execute()) {
                        $_SESSION['success_message'] = 'Award deleted successfully!';
                    } else {
                        $_SESSION['error_message'] = 'Error deleting award.';
                    }
                } else {
                    $_SESSION['error_message'] = 'Invalid award ID.';
                }
                header('Location: ?page=admin&tab=awards');
                exit;
                break;
                
            // Event settings update
            case 'update_event_settings':
                requireAdmin();
                $event_date = $_POST['event_date'] ?? '';
                $event_location = trim($_POST['event_location'] ?? '');
                $registration_deadline = $_POST['registration_deadline'] ?? '';
                $draft_date = $_POST['draft_date'] ?? '';
                $current_year = filter_var($_POST['current_year'] ?? '', FILTER_VALIDATE_INT);
                
                if ($event_date && $event_location && $registration_deadline && $draft_date && $current_year) {
                    // Convert datetime-local format to SQLite format
                    $event_date_formatted = date('Y-m-d H:i:s', strtotime($event_date));
                    $registration_deadline_formatted = date('Y-m-d H:i:s', strtotime($registration_deadline));
                    $draft_date_formatted = date('Y-m-d H:i:s', strtotime($draft_date));
                    
                    // Validate dates
                    $eventDateTime = strtotime($event_date);
                    $regDateTime = strtotime($registration_deadline);
                    $draftDateTime = strtotime($draft_date);
                    
                    if ($eventDateTime && $regDateTime && $draftDateTime && $current_year >= 2020 && $current_year <= 2030) {
                        // Clean up any duplicate records - keep only the most recent one
                        $cleanupStmt = $db->prepare('DELETE FROM event_settings WHERE id NOT IN (SELECT MAX(id) FROM event_settings)');
                        $cleanupStmt->execute();
                        
                        // Get the single remaining record
                        $checkStmt = $db->query('SELECT * FROM event_settings LIMIT 1');
                        $existingRecord = $checkStmt->fetchArray(SQLITE3_ASSOC);
                        
                        if ($existingRecord) {
                            // Update the existing record
                            $stmt = $db->prepare('UPDATE event_settings SET event_date = ?, event_location = ?, registration_deadline = ?, draft_date = ?, current_year = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?');
                            $stmt->bindValue(1, $event_date_formatted);
                            $stmt->bindValue(2, $event_location);
                            $stmt->bindValue(3, $registration_deadline_formatted);
                            $stmt->bindValue(4, $draft_date_formatted);
                            $stmt->bindValue(5, $current_year);
                            $stmt->bindValue(6, $existingRecord['id']);
                        } else {
                            // Insert new record
                            $stmt = $db->prepare('INSERT INTO event_settings (event_date, event_location, registration_deadline, draft_date, current_year) VALUES (?, ?, ?, ?, ?)');
                            $stmt->bindValue(1, $event_date_formatted);
                            $stmt->bindValue(2, $event_location);
                            $stmt->bindValue(3, $registration_deadline_formatted);
                            $stmt->bindValue(4, $draft_date_formatted);
                            $stmt->bindValue(5, $current_year);
                        }
                        
                        if ($stmt->execute()) {
                            $_SESSION['success_message'] = 'Event settings updated successfully!';
                        } else {
                            $_SESSION['error_message'] = 'Error updating event settings: ' . $db->lastErrorMsg();
                        }
                    } else {
                        $_SESSION['error_message'] = 'Please provide valid dates and year.';
                    }
                } else {
                    $_SESSION['error_message'] = 'Please fill in all required fields.';
                }
                header('Location: ?page=admin&tab=event');
                exit;
                break;
                
            // Player CRUD operations
            case 'add_player':
                requireAdmin();
                $name = trim($_POST['name'] ?? '');
                $nickname = trim($_POST['nickname'] ?? '');
                $position = trim($_POST['position'] ?? '');
                $bio = trim($_POST['bio'] ?? '');
                $years_played = filter_var($_POST['years_played'] ?? 1, FILTER_VALIDATE_INT);
                $current_year = isset($_POST['current_year']) ? 1 : 0;
                
                // Handle photo upload
                $photo_path = null;
                if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
                    $uploadDir = 'uploads/';
                    if (!is_dir($uploadDir)) {
                        mkdir($uploadDir, 0755, true);
                    }
                    
                    $fileExtension = strtolower(pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION));
                    if (in_array($fileExtension, ['jpg', 'jpeg', 'png'])) {
                        $fileName = uniqid() . '_' . preg_replace('/[^a-zA-Z0-9_.-]/', '', $_FILES['photo']['name']);
                        $photo_path = $uploadDir . $fileName;
                        
                        if (!move_uploaded_file($_FILES['photo']['tmp_name'], $photo_path)) {
                            $_SESSION['error_message'] = 'Error uploading photo.';
                            header('Location: ?page=admin&tab=players');
                            exit;
                        }
                    } else {
                        $_SESSION['error_message'] = 'Photo must be PNG or JPEG format.';
                        header('Location: ?page=admin&tab=players');
                        exit;
                    }
                }
                
                if ($name && $years_played && $years_played > 0 && $years_played <= 20) {
                    $stmt = $db->prepare('INSERT INTO players (name, nickname, position, bio, photo_path, years_played, current_year) VALUES (?, ?, ?, ?, ?, ?, ?)');
                    $stmt->bindValue(1, $name);
                    $stmt->bindValue(2, $nickname ?: null);
                    $stmt->bindValue(3, $position ?: null);
                    $stmt->bindValue(4, $bio ?: null);
                    $stmt->bindValue(5, $photo_path);
                    $stmt->bindValue(6, $years_played);
                    $stmt->bindValue(7, $current_year);
                    if ($stmt->execute()) {
                        $_SESSION['success_message'] = 'Player added successfully!';
                    } else {
                        $_SESSION['error_message'] = 'Error adding player.';
                    }
                } else {
                    $_SESSION['error_message'] = 'Please provide valid player name and years played (1-20).';
                }
                header('Location: ?page=admin&tab=players');
                exit;
                break;
                
            case 'edit_player':
                requireAdmin();
                $id = filter_var($_POST['id'] ?? '', FILTER_VALIDATE_INT);
                $name = trim($_POST['name'] ?? '');
                $nickname = trim($_POST['nickname'] ?? '');
                $position = trim($_POST['position'] ?? '');
                $bio = trim($_POST['bio'] ?? '');
                $years_played = filter_var($_POST['years_played'] ?? 1, FILTER_VALIDATE_INT);
                $current_year = isset($_POST['current_year']) ? 1 : 0;
                
                if ($id && $name && $years_played && $years_played > 0 && $years_played <= 20) {
                    // Get current player for photo cleanup
                    $currentPlayer = $db->prepare('SELECT photo_path FROM players WHERE id = ?');
                    $currentPlayer->bindValue(1, $id);
                    $currentPlayerResult = $currentPlayer->execute();
                    $currentPlayerData = $currentPlayerResult->fetchArray(SQLITE3_ASSOC);
                    
                    $photo_path = $currentPlayerData['photo_path'] ?? null;
                    
                    // Handle new photo upload
                    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
                        $uploadDir = 'uploads/';
                        if (!is_dir($uploadDir)) {
                            mkdir($uploadDir, 0755, true);
                        }
                        
                        $fileExtension = strtolower(pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION));
                        if (in_array($fileExtension, ['jpg', 'jpeg', 'png'])) {
                            $fileName = uniqid() . '_' . preg_replace('/[^a-zA-Z0-9_.-]/', '', $_FILES['photo']['name']);
                            $new_photo_path = $uploadDir . $fileName;
                            
                            if (move_uploaded_file($_FILES['photo']['tmp_name'], $new_photo_path)) {
                                // Delete old photo if exists
                                if ($photo_path && file_exists($photo_path)) {
                                    unlink($photo_path);
                                }
                                $photo_path = $new_photo_path;
                            } else {
                                $_SESSION['error_message'] = 'Error uploading new photo.';
                                header('Location: ?page=admin&tab=players');
                                exit;
                            }
                        } else {
                            $_SESSION['error_message'] = 'Photo must be PNG or JPEG format.';
                            header('Location: ?page=admin&tab=players');
                            exit;
                        }
                    }
                    
                    $stmt = $db->prepare('UPDATE players SET name = ?, nickname = ?, position = ?, bio = ?, photo_path = ?, years_played = ?, current_year = ? WHERE id = ?');
                    $stmt->bindValue(1, $name);
                    $stmt->bindValue(2, $nickname ?: null);
                    $stmt->bindValue(3, $position ?: null);
                    $stmt->bindValue(4, $bio ?: null);
                    $stmt->bindValue(5, $photo_path);
                    $stmt->bindValue(6, $years_played);
                    $stmt->bindValue(7, $current_year);
                    $stmt->bindValue(8, $id);
                    if ($stmt->execute()) {
                        $_SESSION['success_message'] = 'Player updated successfully!';
                    } else {
                        $_SESSION['error_message'] = 'Error updating player.';
                    }
                } else {
                    $_SESSION['error_message'] = 'Please provide valid player data.';
                }
                header('Location: ?page=admin&tab=players');
                exit;
                break;
                
            case 'delete_player':
                requireAdmin();
                $id = filter_var($_POST['id'] ?? '', FILTER_VALIDATE_INT);
                
                if ($id) {
                    // Get player photo for cleanup
                    $playerQuery = $db->prepare('SELECT photo_path FROM players WHERE id = ?');
                    $playerQuery->bindValue(1, $id);
                    $playerResult = $playerQuery->execute();
                    $playerData = $playerResult->fetchArray(SQLITE3_ASSOC);
                    
                    $stmt = $db->prepare('DELETE FROM players WHERE id = ?');
                    $stmt->bindValue(1, $id);
                    if ($stmt->execute()) {
                        // Delete photo file if exists
                        if ($playerData['photo_path'] && file_exists($playerData['photo_path'])) {
                            unlink($playerData['photo_path']);
                        }
                        $_SESSION['success_message'] = 'Player deleted successfully!';
                    } else {
                        $_SESSION['error_message'] = 'Error deleting player.';
                    }
                } else {
                    $_SESSION['error_message'] = 'Invalid player ID.';
                }
                header('Location: ?page=admin&tab=players');
                exit;
                break;
                
            // Record CRUD operations
            case 'add_record':
                requireAdmin();
                $year = filter_var($_POST['year'] ?? '', FILTER_VALIDATE_INT);
                $record_name = trim($_POST['record_name'] ?? '');
                $record_value = trim($_POST['record_value'] ?? '');
                $player_name = trim($_POST['player_name'] ?? '');
                
                if ($year && $record_name && $record_value && $year >= 1900 && $year <= 2100) {
                    $stmt = $db->prepare('INSERT INTO records (year, record_name, record_value, player_name) VALUES (?, ?, ?, ?)');
                    $stmt->bindValue(1, $year);
                    $stmt->bindValue(2, $record_name);
                    $stmt->bindValue(3, $record_value);
                    $stmt->bindValue(4, $player_name ?: null);
                    if ($stmt->execute()) {
                        $_SESSION['success_message'] = 'Record added successfully!';
                    } else {
                        $_SESSION['error_message'] = 'Error adding record.';
                    }
                } else {
                    $_SESSION['error_message'] = 'Please provide valid year, record name, and record value.';
                }
                header('Location: ?page=admin&tab=records');
                exit;
                break;
                
            case 'edit_record':
                requireAdmin();
                $id = filter_var($_POST['id'] ?? '', FILTER_VALIDATE_INT);
                $year = filter_var($_POST['year'] ?? '', FILTER_VALIDATE_INT);
                $record_name = trim($_POST['record_name'] ?? '');
                $record_value = trim($_POST['record_value'] ?? '');
                $player_name = trim($_POST['player_name'] ?? '');
                
                if ($id && $year && $record_name && $record_value && $year >= 1900 && $year <= 2100) {
                    $stmt = $db->prepare('UPDATE records SET year = ?, record_name = ?, record_value = ?, player_name = ? WHERE id = ?');
                    $stmt->bindValue(1, $year);
                    $stmt->bindValue(2, $record_name);
                    $stmt->bindValue(3, $record_value);
                    $stmt->bindValue(4, $player_name ?: null);
                    $stmt->bindValue(5, $id);
                    if ($stmt->execute()) {
                        $_SESSION['success_message'] = 'Record updated successfully!';
                    } else {
                        $_SESSION['error_message'] = 'Error updating record.';
                    }
                } else {
                    $_SESSION['error_message'] = 'Please provide valid data.';
                }
                header('Location: ?page=admin&tab=records');
                exit;
                break;
                
            case 'delete_record':
                requireAdmin();
                $id = filter_var($_POST['id'] ?? '', FILTER_VALIDATE_INT);
                
                if ($id) {
                    $stmt = $db->prepare('DELETE FROM records WHERE id = ?');
                    $stmt->bindValue(1, $id);
                    if ($stmt->execute()) {
                        $_SESSION['success_message'] = 'Record deleted successfully!';
                    } else {
                        $_SESSION['error_message'] = 'Error deleting record.';
                    }
                } else {
                    $_SESSION['error_message'] = 'Invalid record ID.';
                }
                header('Location: ?page=admin&tab=records');
                exit;
                break;
        }
    }
}

// Get current page
$page = $_GET['page'] ?? 'home';

// Get event settings (after POST processing to get fresh data)
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
            background: linear-gradient(180deg, var(--metallic-silver) 0%, #999 50%, #777 100%);
            border: 2px solid var(--pure-white);
            border-radius: 8px 8px 0 0;
            transition: all 0.3s ease;
            box-shadow: 
                0 4px 8px rgba(0,0,0,0.3),
                inset 0 1px 0 rgba(255,255,255,0.3),
                inset 0 -1px 0 rgba(0,0,0,0.3);
            text-shadow: 1px 1px 2px rgba(0,0,0,0.5);
            position: relative;
        }

        .nav-link:hover, .nav-link.active {
            background: linear-gradient(180deg, var(--bright-orange) 0%, #cc5500 50%, #aa4400 100%);
            border-color: var(--gold-accent);
            transform: translateY(-3px);
            box-shadow: 
                0 6px 12px rgba(0,0,0,0.4),
                inset 0 1px 0 rgba(255,255,255,0.4),
                inset 0 -1px 0 rgba(0,0,0,0.4),
                0 0 15px rgba(255,102,0,0.3);
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
            box-shadow: 
                0 8px 16px rgba(0,0,0,0.4),
                inset 0 1px 0 rgba(255,255,255,0.1),
                inset 0 -1px 0 rgba(0,0,0,0.3),
                0 0 30px rgba(192,192,192,0.1);
            position: relative;
        }

        .card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 1px;
            background: linear-gradient(90deg, transparent 0%, var(--metallic-silver) 50%, transparent 100%);
            opacity: 0.5;
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
            background: linear-gradient(180deg, var(--metallic-silver) 0%, #999 50%, #777 100%);
            color: var(--dark-gray);
            border: 2px solid var(--pure-white);
            box-shadow: 
                0 4px 8px rgba(0,0,0,0.3),
                inset 0 1px 0 rgba(255,255,255,0.4),
                inset 0 -1px 0 rgba(0,0,0,0.3);
            text-shadow: 1px 1px 2px rgba(255,255,255,0.3);
        }

        .btn-secondary:hover {
            background: linear-gradient(180deg, #d4d4d4 0%, var(--metallic-silver) 50%, #999 100%);
            transform: translateY(-2px);
            box-shadow: 
                0 6px 12px rgba(0,0,0,0.4),
                inset 0 1px 0 rgba(255,255,255,0.5),
                inset 0 -1px 0 rgba(0,0,0,0.4);
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

        /* Tab interface for admin */
        .tab-container {
            margin-bottom: 30px;
        }

        .tab-nav {
            display: flex;
            border-bottom: 3px solid var(--bright-orange);
            margin-bottom: 30px;
        }

        .tab-button {
            background: linear-gradient(180deg, var(--dark-gray) 0%, #1a1a1a 100%);
            border: 2px solid var(--metallic-silver);
            border-bottom: none;
            color: var(--pure-white);
            padding: 15px 25px;
            cursor: pointer;
            font-weight: bold;
            text-transform: uppercase;
            border-radius: 8px 8px 0 0;
            margin-right: 5px;
            transition: all 0.3s ease;
        }

        .tab-button:hover {
            background: linear-gradient(180deg, #444 0%, #2a2a2a 100%);
            transform: translateY(-2px);
        }

        .tab-button.active {
            background: linear-gradient(180deg, var(--bright-orange) 0%, #cc5500 100%);
            border-color: var(--gold-accent);
            color: var(--pure-white);
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(255,102,0,0.3);
        }

        .tab-content {
            display: none;
        }

        .tab-content.active {
            display: block;
        }

        /* Admin forms */
        .admin-form {
            background: linear-gradient(145deg, #333 0%, #222 100%);
            border: 2px solid var(--bright-orange);
            border-radius: 8px;
            padding: 25px;
            margin-bottom: 30px;
        }

        .admin-form h3 {
            color: var(--gold-accent);
            margin-bottom: 20px;
            font-size: 1.5rem;
        }

        .form-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 20px;
        }

        /* Admin tables */
        .admin-table {
            width: 100%;
            border-collapse: collapse;
            background: linear-gradient(145deg, #2a2a2a 0%, #1a1a1a 100%);
            border: 2px solid var(--metallic-silver);
            border-radius: 8px;
            overflow: hidden;
        }

        .admin-table th {
            background: linear-gradient(180deg, var(--bright-orange) 0%, #cc5500 100%);
            color: var(--pure-white);
            padding: 15px;
            text-align: left;
            font-weight: bold;
            text-transform: uppercase;
        }

        .admin-table td {
            padding: 12px 15px;
            border-bottom: 1px solid var(--metallic-silver);
            color: var(--pure-white);
            vertical-align: middle;
            height: 65px;
            box-sizing: border-box;
        }

        .admin-table tr:hover {
            background: rgba(255,102,0,0.1);
        }

        .admin-actions {
            display: flex;
            gap: 10px;
            align-items: center;
            justify-content: flex-start;
            height: 100%;
            min-height: 40px;
        }

        .btn-small {
            padding: 6px 12px;
            font-size: 0.8rem;
            border-radius: 4px;
        }

        /* Inline edit form styles */
        .inline-edit-row {
            background: rgba(192,192,192,0.1);
            animation: slideDown 0.3s ease-out;
        }

        .inline-edit-form {
            background: linear-gradient(135deg, var(--navy-base) 0%, rgba(26,35,50,0.9) 100%);
            border: 2px solid var(--metallic-silver);
            border-radius: 8px;
            margin: 10px;
            padding: 20px;
            box-shadow: 
                inset 0 1px 0 rgba(255,255,255,0.2),
                0 4px 8px rgba(0,0,0,0.3),
                0 0 20px rgba(192,192,192,0.1);
        }

        .inline-edit-form input[type="text"],
        .inline-edit-form input[type="number"],
        .inline-edit-form select,
        .inline-edit-form textarea {
            background: rgba(0,0,0,0.3);
            border: 1px solid var(--metallic-silver);
            border-radius: 4px;
            color: white;
            padding: 8px 12px;
            font-size: 14px;
            transition: border-color 0.3s ease;
        }

        .inline-edit-form input[type="text"]:focus,
        .inline-edit-form input[type="number"]:focus,
        .inline-edit-form select:focus,
        .inline-edit-form textarea:focus {
            border-color: var(--brand-orange);
            outline: none;
            box-shadow: 0 0 10px rgba(255,102,0,0.3);
        }

        .inline-edit-form .form-actions {
            display: flex;
            gap: 10px;
            justify-content: flex-start;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Mobile responsive adjustments for inline edit forms */
        @media (max-width: 768px) {
            .inline-edit-form form {
                grid-template-columns: 1fr !important;
            }
            
            .inline-edit-form .form-actions {
                grid-column: 1;
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="container">
            <div class="header-content">
                <div class="logo">EG TURKEY BOWL</div>
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
                        <h1 class="card-title">Welcome to Turkey Bowl 2025</h1>
                        <p>The annual flag football championship returns! Get ready for another legendary battle on the gridiron.</p>
                        
                        <div class="card" style="background: linear-gradient(145deg, var(--dark-gray) 0%, #0f1419 100%); border-color: var(--bright-orange); margin-top: 40px;">
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
                    
                case 'admin':
                    requireAdmin();
                    
                    // Get current tab
                    $currentTab = $_GET['tab'] ?? 'event';
                    
                    // Display success/error messages
                    if (isset($_SESSION['success_message'])) {
                        echo '<div class="success-message">' . htmlspecialchars($_SESSION['success_message']) . '</div>';
                        unset($_SESSION['success_message']);
                    }
                    if (isset($_SESSION['error_message'])) {
                        echo '<div class="error-message">' . htmlspecialchars($_SESSION['error_message']) . '</div>';
                        unset($_SESSION['error_message']);
                    }
                    
                    echo '<div class="card">
                        <h1 class="card-title">Admin Dashboard</h1>
                        <p>Manage all aspects of the Turkey Bowl event including settings, players, and Hall of Fame.</p>
                    </div>';
                    
                    // Tab navigation
                    echo '<div class="tab-container">
                        <div class="tab-nav">
                            <button class="tab-button ' . ($currentTab === 'event' ? 'active' : '') . '" onclick="switchTab(\'event\')">Event Settings</button>
                            <button class="tab-button ' . ($currentTab === 'players' ? 'active' : '') . '" onclick="switchTab(\'players\')">Players</button>
                            <button class="tab-button ' . ($currentTab === 'championships' ? 'active' : '') . '" onclick="switchTab(\'championships\')">Championships</button>
                            <button class="tab-button ' . ($currentTab === 'awards' ? 'active' : '') . '" onclick="switchTab(\'awards\')">Awards</button>
                            <button class="tab-button ' . ($currentTab === 'records' ? 'active' : '') . '" onclick="switchTab(\'records\')">Records</button>
                        </div>';
                    
                    // Event Settings Tab
                    echo '<div id="event" class="tab-content ' . ($currentTab === 'event' ? 'active' : '') . '">
                        <div class="admin-form">
                            <h3>Event Information</h3>
                            <form method="POST">
                                <input type="hidden" name="action" value="update_event_settings">
                                <div class="form-row">
                                    <div class="form-group">
                                        <label class="form-label">Event Date & Time:</label>
                                        <input type="datetime-local" name="event_date" class="form-input" value="' . date('Y-m-d\\TH:i', strtotime($eventSettings['event_date'])) . '" required>
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">Event Location:</label>
                                        <input type="text" name="event_location" class="form-input" value="' . htmlspecialchars($eventSettings['event_location']) . '" maxlength="200" required>
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group">
                                        <label class="form-label">Registration Deadline:</label>
                                        <input type="datetime-local" name="registration_deadline" class="form-input" value="' . date('Y-m-d\\TH:i', strtotime($eventSettings['registration_deadline'])) . '" required>
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">Draft Date & Time:</label>
                                        <input type="datetime-local" name="draft_date" class="form-input" value="' . date('Y-m-d\\TH:i', strtotime($eventSettings['draft_date'])) . '" required>
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group">
                                        <label class="form-label">Current Year:</label>
                                        <input type="number" name="current_year" class="form-input" min="2020" max="2030" value="' . ($eventSettings['current_year'] ?? date('Y')) . '" required>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-primary">Update Event Settings</button>
                            </form>
                        </div>
                    </div>';
                    
                    // Players Tab
                    echo '<div id="players" class="tab-content ' . ($currentTab === 'players' ? 'active' : '') . '">
                        <div class="admin-form">
                            <h3>Add New Player</h3>
                            <form method="POST" enctype="multipart/form-data">
                                <input type="hidden" name="action" value="add_player">
                                <div class="form-row">
                                    <div class="form-group">
                                        <label class="form-label">Full Name:</label>
                                        <input type="text" name="name" class="form-input" maxlength="100" required>
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">Nickname (Optional):</label>
                                        <input type="text" name="nickname" class="form-input" maxlength="50">
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group">
                                        <label class="form-label">Position:</label>
                                        <input type="text" name="position" class="form-input" maxlength="50" placeholder="QB, RB, WR, etc.">
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">Years Played:</label>
                                        <input type="number" name="years_played" class="form-input" min="1" max="20" value="1" required>
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group">
                                        <label class="form-label">Player Photo (PNG/JPEG):</label>
                                        <input type="file" name="photo" class="form-input" accept=".png,.jpg,.jpeg">
                                    </div>
                                    <div class="form-group" style="display: flex; align-items: end;">
                                        <label style="display: flex; align-items: center; color: var(--bright-orange); cursor: pointer;">
                                            <input type="checkbox" name="current_year" checked style="margin-right: 8px;">
                                            Playing This Year
                                        </label>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Bio/Notes (Optional):</label>
                                    <textarea name="bio" class="form-input" rows="3" maxlength="500" style="min-height: 80px; resize: vertical;"></textarea>
                                </div>
                                <button type="submit" class="btn btn-primary">Add Player</button>
                            </form>
                        </div>
                        
                        <div class="card">
                            <h3 style="color: var(--gold-accent); margin-bottom: 20px;">Existing Players</h3>
                            <table class="admin-table">
                                <thead>
                                    <tr>
                                        <th>Photo</th>
                                        <th>Name</th>
                                        <th>Position</th>
                                        <th>Years</th>
                                        <th>Active</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>';
                    
                    $players = $db->query('SELECT * FROM players ORDER BY name');
                    $hasPlayers = false;
                    while ($player = $players->fetchArray(SQLITE3_ASSOC)) {
                        $hasPlayers = true;
                        $photoPath = $player['photo_path'] ? htmlspecialchars($player['photo_path']) : null;
                        echo '<tr id="player-row-' . $player['id'] . '">
                                <td>';
                        if ($photoPath) {
                            echo '<div style="width: 40px; height: 40px; border-radius: 50%; overflow: hidden; border: 2px solid var(--bright-orange);">
                                    <img src="' . $photoPath . '" alt="Photo" style="width: 100%; height: 100%; object-fit: cover;">
                                  </div>';
                        } else {
                            echo '<div style="width: 40px; height: 40px; border-radius: 50%; background: var(--dark-gray); border: 2px solid var(--metallic-silver); display: flex; align-items: center; justify-content: center; font-size: 12px; color: var(--metallic-silver);">📷</div>';
                        }
                        echo '</td>
                                <td>
                                    <strong>' . htmlspecialchars($player['name']) . '</strong>';
                        if ($player['nickname']) {
                            echo '<br><small style="color: var(--gold-accent); font-style: italic;">"' . htmlspecialchars($player['nickname']) . '"</small>';
                        }
                        echo '</td>
                                <td>' . htmlspecialchars($player['position'] ?: 'Utility') . '</td>
                                <td>' . $player['years_played'] . '</td>
                                <td>' . ($player['current_year'] ? '<span style="color: var(--success-green);">✓ Active</span>' : '<span style="color: var(--metallic-silver);">Inactive</span>') . '</td>
                                <td class="admin-actions">
                                    <button onclick="editPlayer(' . $player['id'] . ', \'' . addslashes(htmlspecialchars($player['name'])) . '\', \'' . addslashes(htmlspecialchars($player['nickname'] ?: '')) . '\', \'' . addslashes(htmlspecialchars($player['position'] ?: '')) . '\', \'' . addslashes(htmlspecialchars($player['bio'] ?: '')) . '\', ' . $player['years_played'] . ', ' . ($player['current_year'] ? 'true' : 'false') . ')" class="btn btn-secondary btn-small">Edit</button>
                                    <button onclick="deletePlayer(' . $player['id'] . ', \'' . addslashes(htmlspecialchars($player['name'])) . '\')" class="btn btn-secondary btn-small" style="background: var(--alert-red);">Delete</button>
                                </td>
                              </tr>';
                    }
                    
                    if (!$hasPlayers) {
                        echo '<tr><td colspan="6" style="text-align: center; color: var(--metallic-silver);">No players registered yet.</td></tr>';
                    }
                    
                    echo '      </tbody>
                            </table>
                        </div>
                    </div>';
                    
                    // Championships Tab
                    echo '<div id="championships" class="tab-content ' . ($currentTab === 'championships' ? 'active' : '') . '">
                        <div class="admin-form">
                            <h3>Add New Championship</h3>
                            <form method="POST">
                                <input type="hidden" name="action" value="add_championship">
                                <div class="form-row">
                                    <div class="form-group">
                                        <label class="form-label">Year:</label>
                                        <input type="number" name="year" class="form-input" min="1900" max="2100" value="' . date('Y') . '" required>
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">Team Name:</label>
                                        <input type="text" name="team_name" class="form-input" maxlength="100" required>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-primary">Add Championship</button>
                            </form>
                        </div>
                        
                        <div class="card">
                            <h3 style="color: var(--gold-accent); margin-bottom: 20px;">Existing Championships</h3>
                            <table class="admin-table">
                                <thead>
                                    <tr>
                                        <th>Year</th>
                                        <th>Team Name</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>';
                    
                    $championships = $db->query('SELECT * FROM championships ORDER BY year DESC');
                    $hasChampionships = false;
                    while ($championship = $championships->fetchArray(SQLITE3_ASSOC)) {
                        $hasChampionships = true;
                        echo '<tr id="championship-row-' . $championship['id'] . '">
                                <td>' . $championship['year'] . '</td>
                                <td>' . htmlspecialchars($championship['team_name']) . '</td>
                                <td class="admin-actions">
                                    <button onclick="editChampionship(' . $championship['id'] . ', ' . $championship['year'] . ', \'' . addslashes(htmlspecialchars($championship['team_name'])) . '\')" class="btn btn-secondary btn-small">Edit</button>
                                    <button onclick="deleteChampionship(' . $championship['id'] . ', \'' . addslashes(htmlspecialchars($championship['team_name'])) . '\')" class="btn btn-secondary btn-small" style="background: var(--alert-red);">Delete</button>
                                </td>
                              </tr>';
                    }
                    
                    if (!$hasChampionships) {
                        echo '<tr><td colspan="3" style="text-align: center; color: var(--metallic-silver);">No championships recorded yet.</td></tr>';
                    }
                    
                    echo '      </tbody>
                            </table>
                        </div>
                    </div>';
                    
                    // Awards Tab
                    echo '<div id="awards" class="tab-content ' . ($currentTab === 'awards' ? 'active' : '') . '">
                        <div class="admin-form">
                            <h3>Add New Award</h3>
                            <form method="POST">
                                <input type="hidden" name="action" value="add_award">
                                <div class="form-row">
                                    <div class="form-group">
                                        <label class="form-label">Year:</label>
                                        <input type="number" name="year" class="form-input" min="1900" max="2100" value="' . date('Y') . '" required>
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">Award Name:</label>
                                        <input type="text" name="award_name" class="form-input" maxlength="100" placeholder="e.g., MVP, Most Touchdowns" required>
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">Player Name:</label>
                                        <input type="text" name="player_name" class="form-input" maxlength="100" required>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-primary">Add Award</button>
                            </form>
                        </div>
                        
                        <div class="card">
                            <h3 style="color: var(--gold-accent); margin-bottom: 20px;">Existing Awards</h3>
                            <table class="admin-table">
                                <thead>
                                    <tr>
                                        <th>Year</th>
                                        <th>Award</th>
                                        <th>Winner</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>';
                    
                    $awards = $db->query('SELECT * FROM awards ORDER BY year DESC, award_name');
                    $hasAwards = false;
                    while ($award = $awards->fetchArray(SQLITE3_ASSOC)) {
                        $hasAwards = true;
                        echo '<tr id="award-row-' . $award['id'] . '">
                                <td>' . $award['year'] . '</td>
                                <td>' . htmlspecialchars($award['award_name']) . '</td>
                                <td>' . htmlspecialchars($award['player_name']) . '</td>
                                <td class="admin-actions">
                                    <button onclick="editAward(' . $award['id'] . ', ' . $award['year'] . ', \'' . addslashes(htmlspecialchars($award['award_name'])) . '\', \'' . addslashes(htmlspecialchars($award['player_name'])) . '\')" class="btn btn-secondary btn-small">Edit</button>
                                    <button onclick="deleteAward(' . $award['id'] . ', \'' . addslashes(htmlspecialchars($award['award_name'])) . '\')" class="btn btn-secondary btn-small" style="background: var(--alert-red);">Delete</button>
                                </td>
                              </tr>';
                    }
                    
                    if (!$hasAwards) {
                        echo '<tr><td colspan="4" style="text-align: center; color: var(--metallic-silver);">No awards recorded yet.</td></tr>';
                    }
                    
                    echo '      </tbody>
                            </table>
                        </div>
                    </div>';
                    
                    // Records Tab
                    echo '<div id="records" class="tab-content ' . ($currentTab === 'records' ? 'active' : '') . '">
                        <div class="admin-form">
                            <h3>Add New Record</h3>
                            <form method="POST">
                                <input type="hidden" name="action" value="add_record">
                                <div class="form-row">
                                    <div class="form-group">
                                        <label class="form-label">Year:</label>
                                        <input type="number" name="year" class="form-input" min="1900" max="2100" value="' . date('Y') . '" required>
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">Record Name:</label>
                                        <input type="text" name="record_name" class="form-input" maxlength="100" placeholder="e.g., Most Interceptions, Longest Pass" required>
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">Record Value:</label>
                                        <input type="text" name="record_value" class="form-input" maxlength="50" placeholder="e.g., 5, 45 yards" required>
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">Player Name (Optional):</label>
                                        <input type="text" name="player_name" class="form-input" maxlength="100">
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-primary">Add Record</button>
                            </form>
                        </div>
                        
                        <div class="card">
                            <h3 style="color: var(--gold-accent); margin-bottom: 20px;">Existing Records</h3>
                            <table class="admin-table">
                                <thead>
                                    <tr>
                                        <th>Year</th>
                                        <th>Record</th>
                                        <th>Value</th>
                                        <th>Player</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>';
                    
                    $records = $db->query('SELECT * FROM records ORDER BY year DESC, record_name');
                    $hasRecords = false;
                    while ($record = $records->fetchArray(SQLITE3_ASSOC)) {
                        $hasRecords = true;
                        echo '<tr id="record-row-' . $record['id'] . '">
                                <td>' . $record['year'] . '</td>
                                <td>' . htmlspecialchars($record['record_name']) . '</td>
                                <td>' . htmlspecialchars($record['record_value']) . '</td>
                                <td>' . htmlspecialchars($record['player_name'] ?: 'N/A') . '</td>
                                <td class="admin-actions">
                                    <button onclick="editRecord(' . $record['id'] . ', ' . $record['year'] . ', \'' . addslashes(htmlspecialchars($record['record_name'])) . '\', \'' . addslashes(htmlspecialchars($record['record_value'])) . '\', \'' . addslashes(htmlspecialchars($record['player_name'] ?: '')) . '\')" class="btn btn-secondary btn-small">Edit</button>
                                    <button onclick="deleteRecord(' . $record['id'] . ', \'' . addslashes(htmlspecialchars($record['record_name'])) . '\')" class="btn btn-secondary btn-small" style="background: var(--alert-red);">Delete</button>
                                </td>
                              </tr>';
                    }
                    
                    if (!$hasRecords) {
                        echo '<tr><td colspan="5" style="text-align: center; color: var(--metallic-silver);">No records recorded yet.</td></tr>';
                    }
                    
                    echo '      </tbody>
                            </table>
                        </div>
                    </div>';
                    
                    echo '</div>'; // Close tab-container
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
                document.getElementById('countdown-container').innerHTML = '<h1 style="color: var(--gold-accent); text-align: center; font-size: 4rem; font-family: \'Arial Black\', Arial, sans-serif; text-shadow: 3px 3px 6px rgba(0,0,0,0.8), 0 0 20px rgba(255,215,0,0.5); margin: 40px 0; animation: pulse 2s infinite;">GAME DAY!</h1><style>@keyframes pulse { 0%, 100% { transform: scale(1); } 50% { transform: scale(1.05); } }</style>';
            }
        }

        // Update countdown every second
        if (document.getElementById('countdown-container')) {
            updateCountdown();
            setInterval(updateCountdown, 1000);
        }

        // Inline edit form management
        function closeAllInlineEditForms() {
            const editRows = document.querySelectorAll('.inline-edit-row');
            editRows.forEach(row => row.remove());
        }

        // Admin functionality
        function switchTab(tabName) {
            // Hide all tabs
            const tabs = document.querySelectorAll('.tab-content');
            tabs.forEach(tab => tab.classList.remove('active'));
            
            // Remove active class from all buttons
            const buttons = document.querySelectorAll('.tab-button');
            buttons.forEach(button => button.classList.remove('active'));
            
            // Show selected tab
            document.getElementById(tabName).classList.add('active');
            
            // Activate selected button
            event.target.classList.add('active');
            
            // Update URL without page reload
            const url = new URL(window.location);
            url.searchParams.set('tab', tabName);
            window.history.replaceState({}, '', url);
        }

        // Player functions
        function editPlayer(id, name, nickname, position, bio, yearsPlayed, isCurrentYear) {
            // Close any existing edit forms
            closeAllInlineEditForms();
            
            // Create the inline edit form
            const editRow = document.createElement('tr');
            editRow.id = `edit-player-${id}`;
            editRow.className = 'inline-edit-row';
            editRow.innerHTML = `
                <td colspan="6" style="padding: 0;">
                    <div class="inline-edit-form">
                        <h4 style="color: var(--gold-accent); margin-bottom: 15px;">Edit Player</h4>
                        <form id="edit-player-form-${id}" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; align-items: end;">
                            <div>
                                <label style="color: var(--metallic-silver); font-size: 14px; display: block; margin-bottom: 5px;">Name:</label>
                                <input type="text" id="edit-player-name-${id}" value="${name}" required style="width: 100%;">
                            </div>
                            <div>
                                <label style="color: var(--metallic-silver); font-size: 14px; display: block; margin-bottom: 5px;">Nickname (Optional):</label>
                                <input type="text" id="edit-player-nickname-${id}" value="${nickname}" style="width: 100%;">
                            </div>
                            <div>
                                <label style="color: var(--metallic-silver); font-size: 14px; display: block; margin-bottom: 5px;">Position:</label>
                                <input type="text" id="edit-player-position-${id}" value="${position}" style="width: 100%;">
                            </div>
                            <div>
                                <label style="color: var(--metallic-silver); font-size: 14px; display: block; margin-bottom: 5px;">Years Played:</label>
                                <input type="number" id="edit-player-years-${id}" value="${yearsPlayed}" min="1" max="20" required style="width: 100%;">
                            </div>
                            <div>
                                <label style="color: var(--metallic-silver); font-size: 14px; display: block; margin-bottom: 5px;">Active This Year:</label>
                                <select id="edit-player-current-${id}" required style="width: 100%;">
                                    <option value="1" ${isCurrentYear ? 'selected' : ''}>Yes - Active</option>
                                    <option value="0" ${!isCurrentYear ? 'selected' : ''}>No - Inactive</option>
                                </select>
                            </div>
                            <div style="grid-column: 1 / -1;">
                                <label style="color: var(--metallic-silver); font-size: 14px; display: block; margin-bottom: 5px;">Bio (Optional):</label>
                                <textarea id="edit-player-bio-${id}" rows="3" style="width: 100%; resize: vertical;">${bio}</textarea>
                            </div>
                            <div class="form-actions" style="grid-column: 1 / -1;">
                                <button type="button" onclick="savePlayerEdit(${id})" class="btn btn-primary btn-small">Save</button>
                                <button type="button" onclick="cancelPlayerEdit(${id})" class="btn btn-secondary btn-small">Cancel</button>
                            </div>
                        </form>
                    </div>
                </td>
            `;
            
            // Insert the edit row after the current row
            const currentRow = document.getElementById(`player-row-${id}`);
            currentRow.parentNode.insertBefore(editRow, currentRow.nextSibling);
            
            // Focus the first input
            document.getElementById(`edit-player-name-${id}`).focus();
        }
        
        function savePlayerEdit(id) {
            const name = document.getElementById(`edit-player-name-${id}`).value;
            const nickname = document.getElementById(`edit-player-nickname-${id}`).value;
            const position = document.getElementById(`edit-player-position-${id}`).value;
            const bio = document.getElementById(`edit-player-bio-${id}`).value;
            const yearsPlayed = document.getElementById(`edit-player-years-${id}`).value;
            const isCurrentYear = document.getElementById(`edit-player-current-${id}`).value;
            
            if (name && yearsPlayed && parseInt(yearsPlayed) >= 1 && parseInt(yearsPlayed) <= 20) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.innerHTML = `
                    <input type="hidden" name="action" value="edit_player">
                    <input type="hidden" name="id" value="${id}">
                    <input type="hidden" name="name" value="${name}">
                    <input type="hidden" name="nickname" value="${nickname || ''}">
                    <input type="hidden" name="position" value="${position || ''}">
                    <input type="hidden" name="bio" value="${bio || ''}">
                    <input type="hidden" name="years_played" value="${yearsPlayed}">
                    ${isCurrentYear === '1' ? '<input type="hidden" name="current_year" value="1">' : ''}
                `;
                document.body.appendChild(form);
                form.submit();
            } else {
                alert('Please provide a valid name and years played (1-20).');
            }
        }
        
        function cancelPlayerEdit(id) {
            const editRow = document.getElementById(`edit-player-${id}`);
            if (editRow) {
                editRow.remove();
            }
        }
        
        function deletePlayer(id, playerName) {
            if (confirm(`Are you sure you want to delete player "${playerName}"?\\n\\nThis will also delete their photo and remove them from any teams.\\n\\nThis action cannot be undone.`)) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.innerHTML = `
                    <input type="hidden" name="action" value="delete_player">
                    <input type="hidden" name="id" value="${id}">
                `;
                document.body.appendChild(form);
                form.submit();
            }
        }

        // Championship functions
        function editChampionship(id, year, teamName) {
            // Close any existing edit forms
            closeAllInlineEditForms();
            
            // Create the inline edit form
            const editRow = document.createElement('tr');
            editRow.id = `edit-championship-${id}`;
            editRow.className = 'inline-edit-row';
            editRow.innerHTML = `
                <td colspan="3" style="padding: 0;">
                    <div class="inline-edit-form">
                        <h4 style="color: var(--gold-accent); margin-bottom: 15px;">Edit Championship</h4>
                        <form id="edit-championship-form-${id}" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; align-items: end;">
                            <div>
                                <label style="color: var(--metallic-silver); font-size: 14px; display: block; margin-bottom: 5px;">Year:</label>
                                <input type="number" id="edit-championship-year-${id}" value="${year}" min="1900" max="2100" required style="width: 100%;">
                            </div>
                            <div>
                                <label style="color: var(--metallic-silver); font-size: 14px; display: block; margin-bottom: 5px;">Team Name:</label>
                                <input type="text" id="edit-championship-team-${id}" value="${teamName}" required style="width: 100%;">
                            </div>
                            <div class="form-actions">
                                <button type="button" onclick="saveChampionshipEdit(${id})" class="btn btn-primary btn-small">Save</button>
                                <button type="button" onclick="cancelChampionshipEdit(${id})" class="btn btn-secondary btn-small">Cancel</button>
                            </div>
                        </form>
                    </div>
                </td>
            `;
            
            // Insert the edit row after the current row
            const currentRow = document.getElementById(`championship-row-${id}`);
            currentRow.parentNode.insertBefore(editRow, currentRow.nextSibling);
            
            // Focus the first input
            document.getElementById(`edit-championship-year-${id}`).focus();
        }
        
        function saveChampionshipEdit(id) {
            const year = document.getElementById(`edit-championship-year-${id}`).value;
            const teamName = document.getElementById(`edit-championship-team-${id}`).value;
            
            if (teamName && year && year >= 1900 && year <= 2100) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.innerHTML = `
                    <input type="hidden" name="action" value="edit_championship">
                    <input type="hidden" name="id" value="${id}">
                    <input type="hidden" name="year" value="${year}">
                    <input type="hidden" name="team_name" value="${teamName}">
                `;
                document.body.appendChild(form);
                form.submit();
            } else {
                alert('Please fill in all required fields with valid values.');
            }
        }
        
        function cancelChampionshipEdit(id) {
            const editRow = document.getElementById(`edit-championship-${id}`);
            if (editRow) {
                editRow.remove();
            }
        }

        function deleteChampionship(id, teamName) {
            if (confirm(`Are you sure you want to delete the championship for "${teamName}"?\\n\\nThis action cannot be undone.`)) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.innerHTML = `
                    <input type="hidden" name="action" value="delete_championship">
                    <input type="hidden" name="id" value="${id}">
                `;
                document.body.appendChild(form);
                form.submit();
            }
        }

        // Award functions
        function editAward(id, year, awardName, playerName) {
            // Close any existing edit forms
            closeAllInlineEditForms();
            
            // Create the inline edit form
            const editRow = document.createElement('tr');
            editRow.id = `edit-award-${id}`;
            editRow.className = 'inline-edit-row';
            editRow.innerHTML = `
                <td colspan="4" style="padding: 0;">
                    <div class="inline-edit-form">
                        <h4 style="color: var(--gold-accent); margin-bottom: 15px;">Edit Award</h4>
                        <form id="edit-award-form-${id}" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; align-items: end;">
                            <div>
                                <label style="color: var(--metallic-silver); font-size: 14px; display: block; margin-bottom: 5px;">Year:</label>
                                <input type="number" id="edit-award-year-${id}" value="${year}" min="1900" max="2100" required style="width: 100%;">
                            </div>
                            <div>
                                <label style="color: var(--metallic-silver); font-size: 14px; display: block; margin-bottom: 5px;">Award Name:</label>
                                <input type="text" id="edit-award-name-${id}" value="${awardName}" required style="width: 100%;">
                            </div>
                            <div>
                                <label style="color: var(--metallic-silver); font-size: 14px; display: block; margin-bottom: 5px;">Player Name:</label>
                                <input type="text" id="edit-award-player-${id}" value="${playerName}" required style="width: 100%;">
                            </div>
                            <div class="form-actions">
                                <button type="button" onclick="saveAwardEdit(${id})" class="btn btn-primary btn-small">Save</button>
                                <button type="button" onclick="cancelAwardEdit(${id})" class="btn btn-secondary btn-small">Cancel</button>
                            </div>
                        </form>
                    </div>
                </td>
            `;
            
            // Insert the edit row after the current row
            const currentRow = document.getElementById(`award-row-${id}`);
            currentRow.parentNode.insertBefore(editRow, currentRow.nextSibling);
            
            // Focus the first input
            document.getElementById(`edit-award-year-${id}`).focus();
        }
        
        function saveAwardEdit(id) {
            const year = document.getElementById(`edit-award-year-${id}`).value;
            const awardName = document.getElementById(`edit-award-name-${id}`).value;
            const playerName = document.getElementById(`edit-award-player-${id}`).value;
            
            if (awardName && playerName && year && year >= 1900 && year <= 2100) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.innerHTML = `
                    <input type="hidden" name="action" value="edit_award">
                    <input type="hidden" name="id" value="${id}">
                    <input type="hidden" name="year" value="${year}">
                    <input type="hidden" name="award_name" value="${awardName}">
                    <input type="hidden" name="player_name" value="${playerName}">
                `;
                document.body.appendChild(form);
                form.submit();
            } else {
                alert('Please fill in all required fields with valid values.');
            }
        }
        
        function cancelAwardEdit(id) {
            const editRow = document.getElementById(`edit-award-${id}`);
            if (editRow) {
                editRow.remove();
            }
        }

        function deleteAward(id, awardName) {
            if (confirm(`Are you sure you want to delete the award "${awardName}"?\\n\\nThis action cannot be undone.`)) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.innerHTML = `
                    <input type="hidden" name="action" value="delete_award">
                    <input type="hidden" name="id" value="${id}">
                `;
                document.body.appendChild(form);
                form.submit();
            }
        }

        // Record functions
        function editRecord(id, year, recordName, recordValue, playerName) {
            // Close any existing edit forms
            closeAllInlineEditForms();
            
            // Create the inline edit form
            const editRow = document.createElement('tr');
            editRow.id = `edit-record-${id}`;
            editRow.className = 'inline-edit-row';
            editRow.innerHTML = `
                <td colspan="5" style="padding: 0;">
                    <div class="inline-edit-form">
                        <h4 style="color: var(--gold-accent); margin-bottom: 15px;">Edit Record</h4>
                        <form id="edit-record-form-${id}" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; align-items: end;">
                            <div>
                                <label style="color: var(--metallic-silver); font-size: 14px; display: block; margin-bottom: 5px;">Year:</label>
                                <input type="number" id="edit-record-year-${id}" value="${year}" min="1900" max="2100" required style="width: 100%;">
                            </div>
                            <div>
                                <label style="color: var(--metallic-silver); font-size: 14px; display: block; margin-bottom: 5px;">Record Name:</label>
                                <input type="text" id="edit-record-name-${id}" value="${recordName}" required style="width: 100%;">
                            </div>
                            <div>
                                <label style="color: var(--metallic-silver); font-size: 14px; display: block; margin-bottom: 5px;">Record Value:</label>
                                <input type="text" id="edit-record-value-${id}" value="${recordValue}" required style="width: 100%;">
                            </div>
                            <div>
                                <label style="color: var(--metallic-silver); font-size: 14px; display: block; margin-bottom: 5px;">Player Name (Optional):</label>
                                <input type="text" id="edit-record-player-${id}" value="${playerName}" style="width: 100%;">
                            </div>
                            <div class="form-actions">
                                <button type="button" onclick="saveRecordEdit(${id})" class="btn btn-primary btn-small">Save</button>
                                <button type="button" onclick="cancelRecordEdit(${id})" class="btn btn-secondary btn-small">Cancel</button>
                            </div>
                        </form>
                    </div>
                </td>
            `;
            
            // Insert the edit row after the current row
            const currentRow = document.getElementById(`record-row-${id}`);
            currentRow.parentNode.insertBefore(editRow, currentRow.nextSibling);
            
            // Focus the first input
            document.getElementById(`edit-record-year-${id}`).focus();
        }
        
        function saveRecordEdit(id) {
            const year = document.getElementById(`edit-record-year-${id}`).value;
            const recordName = document.getElementById(`edit-record-name-${id}`).value;
            const recordValue = document.getElementById(`edit-record-value-${id}`).value;
            const playerName = document.getElementById(`edit-record-player-${id}`).value;
            
            if (recordName && recordValue && year && year >= 1900 && year <= 2100) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.innerHTML = `
                    <input type="hidden" name="action" value="edit_record">
                    <input type="hidden" name="id" value="${id}">
                    <input type="hidden" name="year" value="${year}">
                    <input type="hidden" name="record_name" value="${recordName}">
                    <input type="hidden" name="record_value" value="${recordValue}">
                    <input type="hidden" name="player_name" value="${playerName || ''}">
                `;
                document.body.appendChild(form);
                form.submit();
            } else {
                alert('Please fill in all required fields with valid values.');
            }
        }
        
        function cancelRecordEdit(id) {
            const editRow = document.getElementById(`edit-record-${id}`);
            if (editRow) {
                editRow.remove();
            }
        }

        function deleteRecord(id, recordName) {
            if (confirm(`Are you sure you want to delete the record "${recordName}"?\\n\\nThis action cannot be undone.`)) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.innerHTML = `
                    <input type="hidden" name="action" value="delete_record">
                    <input type="hidden" name="id" value="${id}">
                `;
                document.body.appendChild(form);
                form.submit();
            }
        }
    </script>
</body>
</html>
