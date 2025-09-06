<?php
// Handle form submissions
function handleFormSubmission($db) {
    $loginError = null;
    
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
                    $current_year = filter_var($_POST['current_year'] ?? '', FILTER_VALIDATE_INT);
                    
                    if ($event_date && $event_location && $registration_deadline && $current_year) {
                        // Convert datetime-local format to SQLite format
                        $event_date_formatted = date('Y-m-d H:i:s', strtotime($event_date));
                        $registration_deadline_formatted = date('Y-m-d H:i:s', strtotime($registration_deadline));
                        
                        // Validate dates
                        $eventDateTime = strtotime($event_date);
                        $regDateTime = strtotime($registration_deadline);
                        
                        if ($eventDateTime && $regDateTime && $current_year >= 2020 && $current_year <= 2030) {
                            // Clean up any duplicate records - keep only the most recent one
                            $cleanupStmt = $db->prepare('DELETE FROM event_settings WHERE id NOT IN (SELECT MAX(id) FROM event_settings)');
                            $cleanupStmt->execute();
                            
                            // Get the single remaining record
                            $checkStmt = $db->query('SELECT * FROM event_settings LIMIT 1');
                            $existingRecord = $checkStmt->fetchArray(SQLITE3_ASSOC);
                            
                            if ($existingRecord) {
                                // Update the existing record
                                $stmt = $db->prepare('UPDATE event_settings SET event_date = ?, event_location = ?, registration_deadline = ?, current_year = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?');
                                $stmt->bindValue(1, $event_date_formatted);
                                $stmt->bindValue(2, $event_location);
                                $stmt->bindValue(3, $registration_deadline_formatted);
                                $stmt->bindValue(4, $current_year);
                                $stmt->bindValue(5, $existingRecord['id']);
                            } else {
                                // Insert new record
                                $stmt = $db->prepare('INSERT INTO event_settings (event_date, event_location, registration_deadline, current_year) VALUES (?, ?, ?, ?)');
                                $stmt->bindValue(1, $event_date_formatted);
                                $stmt->bindValue(2, $event_location);
                                $stmt->bindValue(3, $registration_deadline_formatted);
                                $stmt->bindValue(4, $current_year);
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
                    
                // Team management actions
                case 'add_team':
                    requireAdmin();
                    $teamName = trim($_POST['team_name'] ?? '');
                    $captainId = filter_var($_POST['captain_id'] ?? '', FILTER_VALIDATE_INT) ?: null;
                    
                    if ($teamName) {
                        // Get current year from event settings
                        $eventSettings = $db->query('SELECT current_year FROM event_settings LIMIT 1')->fetchArray(SQLITE3_ASSOC);
                        $currentYear = $eventSettings['current_year'] ?? date('Y');
                        
                        $stmt = $db->prepare('INSERT INTO teams (name, captain_id, year) VALUES (?, ?, ?)');
                        $stmt->bindValue(1, $teamName);
                        $stmt->bindValue(2, $captainId);
                        $stmt->bindValue(3, $currentYear);
                        
                        if ($stmt->execute()) {
                            $_SESSION['success_message'] = 'Team added successfully!';
                        } else {
                            $_SESSION['error_message'] = 'Error adding team.';
                        }
                    } else {
                        $_SESSION['error_message'] = 'Team name is required.';
                    }
                    header('Location: ?page=admin&tab=teams');
                    exit;
                    break;
                    
                case 'delete_team':
                    requireAdmin();
                    $teamId = filter_var($_POST['team_id'] ?? '', FILTER_VALIDATE_INT);
                    
                    if ($teamId) {
                        // Delete team players first
                        $db->prepare('DELETE FROM team_players WHERE team_id = ?')->bindValue(1, $teamId)->execute();
                        // Delete the team
                        $stmt = $db->prepare('DELETE FROM teams WHERE id = ?');
                        $stmt->bindValue(1, $teamId);
                        
                        if ($stmt->execute()) {
                            $_SESSION['success_message'] = 'Team deleted successfully!';
                        } else {
                            $_SESSION['error_message'] = 'Error deleting team.';
                        }
                    } else {
                        $_SESSION['error_message'] = 'Invalid team ID.';
                    }
                    header('Location: ?page=admin&tab=teams');
                    exit;
                    break;
                    
                case 'add_player_to_team':
                    requireAdmin();
                    header('Content-Type: application/json');
                    
                    $teamId = filter_var($_POST['team_id'] ?? '', FILTER_VALIDATE_INT);
                    $playerId = filter_var($_POST['player_id'] ?? '', FILTER_VALIDATE_INT);
                    
                    if (!$teamId || !$playerId) {
                        echo json_encode(['success' => false, 'error' => 'Invalid parameters']);
                        exit;
                    }
                    
                    // Check if player is already on a team
                    $existingTeam = $db->prepare('SELECT team_id FROM team_players WHERE player_id = ?');
                    $existingTeam->bindValue(1, $playerId);
                    $existingResult = $existingTeam->execute();
                    $existing = $existingResult->fetchArray(SQLITE3_ASSOC);
                    
                    if ($existing) {
                        echo json_encode(['success' => false, 'error' => 'Player is already on a team']);
                        exit;
                    }
                    
                    // Get highest draft_order for this team
                    $maxOrder = $db->query('SELECT MAX(draft_order) as max_order FROM team_players WHERE team_id = ' . $teamId)->fetchArray(SQLITE3_ASSOC);
                    $newOrder = ($maxOrder['max_order'] ?? 0) + 1;
                    
                    // Add player to team
                    $stmt = $db->prepare('INSERT INTO team_players (team_id, player_id, draft_order) VALUES (?, ?, ?)');
                    $stmt->bindValue(1, $teamId);
                    $stmt->bindValue(2, $playerId);
                    $stmt->bindValue(3, $newOrder);
                    
                    if ($stmt->execute()) {
                        echo json_encode(['success' => true]);
                    } else {
                        echo json_encode(['success' => false, 'error' => 'Database error']);
                    }
                    exit;
                    break;
                    
                case 'remove_player_from_team':
                    requireAdmin();
                    header('Content-Type: application/json');
                    
                    $teamId = filter_var($_POST['team_id'] ?? '', FILTER_VALIDATE_INT);
                    $playerId = filter_var($_POST['player_id'] ?? '', FILTER_VALIDATE_INT);
                    
                    if (!$teamId || !$playerId) {
                        echo json_encode(['success' => false, 'error' => 'Invalid parameters']);
                        exit;
                    }
                    
                    $stmt = $db->prepare('DELETE FROM team_players WHERE team_id = ? AND player_id = ?');
                    $stmt->bindValue(1, $teamId);
                    $stmt->bindValue(2, $playerId);
                    
                    if ($stmt->execute()) {
                        echo json_encode(['success' => true]);
                    } else {
                        echo json_encode(['success' => false, 'error' => 'Database error']);
                    }
                    exit;
                    break;
                    
                case 'get_active_players':
                    requireAdmin();
                    header('Content-Type: application/json');
                    
                    $players = $db->query('SELECT id, name, nickname FROM players WHERE current_year = 1 ORDER BY name');
                    $playerList = [];
                    while ($player = $players->fetchArray(SQLITE3_ASSOC)) {
                        $playerList[] = $player;
                    }
                    echo json_encode($playerList);
                    exit;
                    break;
                    
                case 'update_team':
                    requireAdmin();
                    header('Content-Type: application/json');
                    
                    $teamId = filter_var($_POST['team_id'] ?? '', FILTER_VALIDATE_INT);
                    $teamName = trim($_POST['team_name'] ?? '');
                    $teamColor = trim($_POST['team_color'] ?? '');
                    
                    if (!$teamId || !$teamName || !$teamColor) {
                        echo json_encode(['success' => false, 'error' => 'Invalid parameters']);
                        exit;
                    }
                    
                    // Validate color format (hex color)
                    if (!preg_match('/^#[a-fA-F0-9]{6}$/', $teamColor)) {
                        echo json_encode(['success' => false, 'error' => 'Invalid color format']);
                        exit;
                    }
                    
                    // Verify team exists
                    $checkQuery = $db->prepare('SELECT * FROM teams WHERE id = ?');
                    $checkQuery->bindValue(1, $teamId);
                    $checkResult = $checkQuery->execute();
                    $team = $checkResult->fetchArray(SQLITE3_ASSOC);
                    
                    if (!$team) {
                        echo json_encode(['success' => false, 'error' => 'Team not found']);
                        exit;
                    }
                    
                    // Update the team
                    $updateQuery = $db->prepare('UPDATE teams SET name = ? WHERE id = ?');
                    $updateQuery->bindValue(1, $teamName);
                    $updateQuery->bindValue(2, $teamId);
                    
                    if ($updateQuery->execute()) {
                        echo json_encode(['success' => true]);
                    } else {
                        echo json_encode(['success' => false, 'error' => 'Database update failed']);
                    }
                    exit;
                    break;
            }
        }
    }
    
    return $loginError;
}