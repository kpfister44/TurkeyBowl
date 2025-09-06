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
                    
                case 'start_draft':
                    requireAdmin();
                    $draft_session_id = filter_var($_POST['draft_session_id'] ?? '', FILTER_VALIDATE_INT);
                    
                    if ($draft_session_id) {
                        // Auto-place captains on their teams before starting draft
                        $teamsQuery = $db->query('SELECT id, captain_player_id FROM draft_teams WHERE draft_session_id = ' . $draft_session_id . ' AND captain_player_id IS NOT NULL');
                        $pickNumber = 0;
                        
                        while ($team = $teamsQuery->fetchArray(SQLITE3_ASSOC)) {
                            $pickNumber++;
                            $captainPickStmt = $db->prepare('INSERT INTO draft_picks (draft_session_id, team_id, player_id, pick_number, pick_time) VALUES (?, ?, ?, ?, datetime("now"))');
                            $captainPickStmt->bindValue(1, $draft_session_id);
                            $captainPickStmt->bindValue(2, $team['id']);
                            $captainPickStmt->bindValue(3, $team['captain_player_id']);
                            $captainPickStmt->bindValue(4, $pickNumber);
                            $captainPickStmt->execute();
                        }
                        
                        // Start the draft
                        $stmt = $db->prepare('UPDATE draft_sessions SET status = "active", timer_expires_at = datetime("now", "+30 seconds") WHERE id = ? AND status = "setup"');
                        $stmt->bindValue(1, $draft_session_id);
                        
                        // Set first team to draft
                        $firstTeamStmt = $db->prepare('UPDATE draft_sessions SET current_pick_team_id = (SELECT id FROM draft_teams WHERE draft_session_id = ? AND draft_order = 1) WHERE id = ?');
                        $firstTeamStmt->bindValue(1, $draft_session_id);
                        $firstTeamStmt->bindValue(2, $draft_session_id);
                        $firstTeamStmt->execute();
                        
                        if ($stmt->execute() && $db->changes() > 0) {
                            $_SESSION['success_message'] = 'Draft started! Captains have been placed on their teams. Good luck!';
                        } else {
                            $_SESSION['error_message'] = 'Error starting draft or draft already in progress.';
                        }
                    } else {
                        $_SESSION['error_message'] = 'Invalid draft session.';
                    }
                    header('Location: ?page=admin&tab=draft');
                    exit;
                    break;
                    
                case 'reset_draft':
                    requireAdmin();
                    $draft_session_id = filter_var($_POST['draft_session_id'] ?? '', FILTER_VALIDATE_INT);
                    
                    if ($draft_session_id) {
                        // Delete draft picks
                        $db->prepare('DELETE FROM draft_picks WHERE draft_session_id = ?')->bindValue(1, $draft_session_id)->execute();
                        // Delete draft teams  
                        $db->prepare('DELETE FROM draft_teams WHERE draft_session_id = ?')->bindValue(1, $draft_session_id)->execute();
                        // Delete draft session
                        $db->prepare('DELETE FROM draft_sessions WHERE id = ?')->bindValue(1, $draft_session_id)->execute();
                        
                        $_SESSION['success_message'] = 'Draft reset successfully.';
                    } else {
                        $_SESSION['error_message'] = 'Invalid draft session.';
                    }
                    header('Location: ?page=admin&tab=draft');
                    exit;
                    break;
                    
                case 'end_draft':
                    requireAdmin();
                    $draft_session_id = filter_var($_POST['draft_session_id'] ?? '', FILTER_VALIDATE_INT);
                    
                    if ($draft_session_id) {
                        $stmt = $db->prepare('UPDATE draft_sessions SET status = "completed" WHERE id = ? AND status = "active"');
                        $stmt->bindValue(1, $draft_session_id);
                        
                        if ($stmt->execute() && $db->changes() > 0) {
                            $_SESSION['success_message'] = 'Draft ended successfully! Check the Teams tab to see final rosters.';
                        } else {
                            $_SESSION['error_message'] = 'Error ending draft or no active draft found.';
                        }
                    } else {
                        $_SESSION['error_message'] = 'Invalid draft session.';
                    }
                    header('Location: ?page=admin&tab=teams');
                    exit;
                    break;
                    
                case 'draft_player':
                    requireAdmin();
                    $player_id = filter_var($_POST['player_id'] ?? '', FILTER_VALIDATE_INT);
                    
                    // Get current active draft
                    $draft = $db->query('SELECT * FROM draft_sessions WHERE status = "active" ORDER BY created_at DESC LIMIT 1')->fetchArray(SQLITE3_ASSOC);
                    
                    if (!$draft) {
                        echo json_encode(['success' => false, 'message' => 'No active draft session']);
                        exit;
                    }
                    
                    $draft_session_id = $draft['id'];
                    $team_id = $draft['current_pick_team_id'];
                    
                    if ($draft_session_id && $team_id && $player_id) {
                        if ($draft) {
                            // Check if player is available (active this year and not drafted)
                            $playerCheck = $db->prepare('
                                SELECT p.* FROM players p 
                                WHERE p.id = ? AND p.current_year = 1 
                                AND NOT EXISTS (SELECT 1 FROM draft_picks dp WHERE dp.player_id = p.id AND dp.draft_session_id = ?)
                                AND NOT EXISTS (SELECT 1 FROM draft_teams dt WHERE dt.captain_player_id = p.id AND dt.draft_session_id = ?)
                            ');
                            $playerCheck->bindValue(1, $player_id);
                            $playerCheck->bindValue(2, $draft_session_id);
                            $playerCheck->bindValue(3, $draft_session_id);
                            $playerResult = $playerCheck->execute();
                            $player = $playerResult->fetchArray(SQLITE3_ASSOC);
                            
                            if ($player) {
                                // Calculate pick number (total picks including captains)
                                $totalPickCount = $db->query('SELECT COUNT(*) as count FROM draft_picks WHERE draft_session_id = ' . $draft_session_id)->fetchArray(SQLITE3_ASSOC);
                                $pickNumber = $totalPickCount['count'] + 1;
                                
                                // Calculate draft pick number (excluding captain picks for round calculation)
                                $nonCaptainPickCount = $db->query('
                                    SELECT COUNT(*) as count FROM draft_picks dp 
                                    WHERE dp.draft_session_id = ' . $draft_session_id . ' 
                                    AND NOT EXISTS (SELECT 1 FROM draft_teams dt WHERE dt.captain_player_id = dp.player_id AND dt.draft_session_id = ' . $draft_session_id . ')
                                ')->fetchArray(SQLITE3_ASSOC);
                                $draftPickNumber = $nonCaptainPickCount['count'] + 1;
                                $round = ceil($draftPickNumber / $draft['num_teams']);
                                
                                // Draft the player
                                $draftStmt = $db->prepare('
                                    INSERT INTO draft_picks (draft_session_id, team_id, player_id, round, pick_number) 
                                    VALUES (?, ?, ?, ?, ?)
                                ');
                                $draftStmt->bindValue(1, $draft_session_id);
                                $draftStmt->bindValue(2, $team_id);
                                $draftStmt->bindValue(3, $player_id);
                                $draftStmt->bindValue(4, $round);
                                $draftStmt->bindValue(5, $pickNumber);
                                
                                if ($draftStmt->execute()) {
                                    // Check if there are more available players
                                    $remainingPlayersCount = $db->query('
                                        SELECT COUNT(*) as count FROM players p 
                                        WHERE p.current_year = 1 
                                        AND NOT EXISTS (SELECT 1 FROM draft_picks dp WHERE dp.player_id = p.id AND dp.draft_session_id = ' . $draft_session_id . ')
                                        AND NOT EXISTS (SELECT 1 FROM draft_teams dt WHERE dt.captain_player_id = p.id AND dt.draft_session_id = ' . $draft_session_id . ')
                                    ')->fetchArray(SQLITE3_ASSOC);
                                    
                                    if ($remainingPlayersCount['count'] > 0) {
                                        // More players available - continue draft
                                        $currentPickInRound = ($draftPickNumber - 1) % $draft['num_teams'] + 1;
                                        $nextTeamOrder = getNextTeamOrder($draft['num_teams'], $round, $currentPickInRound);
                                        
                                        if ($nextTeamOrder) {
                                            // Get next team ID
                                            $nextTeamQuery = $db->prepare('SELECT id FROM draft_teams WHERE draft_session_id = ? AND draft_order = ?');
                                            $nextTeamQuery->bindValue(1, $draft_session_id);
                                            $nextTeamQuery->bindValue(2, $nextTeamOrder);
                                            $nextTeamResult = $nextTeamQuery->execute();
                                            $nextTeam = $nextTeamResult->fetchArray(SQLITE3_ASSOC);
                                            
                                            if ($nextTeam) {
                                                // Update current pick team and reset timer
                                                $updateDraft = $db->prepare('
                                                    UPDATE draft_sessions 
                                                    SET current_pick_team_id = ?, timer_expires_at = datetime("now", "+30 seconds") 
                                                    WHERE id = ?
                                                ');
                                                $updateDraft->bindValue(1, $nextTeam['id']);
                                                $updateDraft->bindValue(2, $draft_session_id);
                                                $updateDraft->execute();
                                            }
                                        }
                                    } else {
                                        // No more players available - draft is complete
                                        $db->prepare('UPDATE draft_sessions SET status = "completed" WHERE id = ?')->bindValue(1, $draft_session_id)->execute();
                                    }
                                    
                                    echo json_encode(['success' => true, 'message' => 'Player drafted successfully!']);
                                } else {
                                    echo json_encode(['success' => false, 'message' => 'Error drafting player.']);
                                }
                            } else {
                                echo json_encode(['success' => false, 'message' => 'Player not available.']);
                            }
                        } else {
                            echo json_encode(['success' => false, 'message' => 'Not your turn to draft.']);
                        }
                    } else {
                        echo json_encode(['success' => false, 'message' => 'Invalid request.']);
                    }
                    exit;
                    break;
                    
                case 'get_active_players':
                    requireAdmin();
                    $players = $db->query('SELECT id, name, nickname FROM players WHERE current_year = 1 ORDER BY name');
                    $playerList = [];
                    while ($player = $players->fetchArray(SQLITE3_ASSOC)) {
                        $playerList[] = $player;
                    }
                    header('Content-Type: application/json');
                    echo json_encode($playerList);
                    exit;
                    break;
                    
                case 'get_draft_state':
                    requireAdmin();
                    // Get current draft session
                    $draft = $db->query('SELECT * FROM draft_sessions WHERE status = "active" ORDER BY created_at DESC LIMIT 1')->fetchArray(SQLITE3_ASSOC);
                    
                    if (!$draft) {
                        echo json_encode(['success' => false, 'message' => 'No active draft']);
                        exit;
                    }
                    
                    // Get current team info
                    $currentTeam = null;
                    if ($draft['current_pick_team_id']) {
                        $currentTeam = $db->query('SELECT * FROM draft_teams WHERE id = ' . $draft['current_pick_team_id'])->fetchArray(SQLITE3_ASSOC);
                    }
                    
                    // Calculate time remaining
                    $timeRemaining = 0;
                    if ($draft['timer_expires_at']) {
                        $expiresAt = new DateTime($draft['timer_expires_at']);
                        $now = new DateTime();
                        $diff = $expiresAt->getTimestamp() - $now->getTimestamp();
                        $timeRemaining = max(0, $diff);
                    }
                    
                    // Get available players (exclude captains and already drafted players)
                    $availablePlayers = [];
                    $playersQuery = $db->query('
                        SELECT p.* FROM players p 
                        WHERE p.current_year = 1 
                        AND NOT EXISTS (SELECT 1 FROM draft_picks dp WHERE dp.player_id = p.id AND dp.draft_session_id = ' . $draft['id'] . ')
                        AND NOT EXISTS (SELECT 1 FROM draft_teams dt WHERE dt.captain_player_id = p.id AND dt.draft_session_id = ' . $draft['id'] . ')
                        ORDER BY p.name
                    ');
                    while ($player = $playersQuery->fetchArray(SQLITE3_ASSOC)) {
                        $availablePlayers[] = $player;
                    }
                    
                    // Get all teams with their players
                    $teams = [];
                    $teamsQuery = $db->query('SELECT * FROM draft_teams WHERE draft_session_id = ' . $draft['id'] . ' ORDER BY draft_order');
                    while ($team = $teamsQuery->fetchArray(SQLITE3_ASSOC)) {
                        // Get team players
                        $teamPlayers = [];
                        $playersQuery = $db->query('
                            SELECT p.* FROM draft_picks dp 
                            JOIN players p ON dp.player_id = p.id 
                            WHERE dp.team_id = ' . $team['id'] . ' 
                            ORDER BY dp.pick_number
                        ');
                        while ($player = $playersQuery->fetchArray(SQLITE3_ASSOC)) {
                            $teamPlayers[] = $player;
                        }
                        
                        $team['players'] = $teamPlayers;
                        $teams[] = $team;
                    }
                    
                    // Calculate current pick info
                    $totalPickCount = $db->query('SELECT COUNT(*) as count FROM draft_picks WHERE draft_session_id = ' . $draft['id'])->fetchArray(SQLITE3_ASSOC);
                    $totalPickNumber = $totalPickCount['count'] + 1;
                    
                    // Calculate draft pick info (excluding captain picks for display)
                    $nonCaptainPickCount = $db->query('
                        SELECT COUNT(*) as count FROM draft_picks dp 
                        WHERE dp.draft_session_id = ' . $draft['id'] . ' 
                        AND NOT EXISTS (SELECT 1 FROM draft_teams dt WHERE dt.captain_player_id = dp.player_id AND dt.draft_session_id = ' . $draft['id'] . ')
                    ')->fetchArray(SQLITE3_ASSOC);
                    $draftPickNumber = $nonCaptainPickCount['count'] + 1;
                    $round = ceil($draftPickNumber / $draft['num_teams']);
                    
                    echo json_encode([
                        'success' => true,
                        'currentTeam' => $currentTeam,
                        'currentTeamId' => $draft['current_pick_team_id'],
                        'round' => $round,
                        'pickNumber' => $draftPickNumber,
                        'timeRemaining' => $timeRemaining,
                        'availablePlayers' => $availablePlayers,
                        'teams' => $teams
                    ]);
                    exit;
                    break;
                    
                case 'transfer_player':
                    requireAdmin();
                    header('Content-Type: application/json');
                    
                    $draftPickId = filter_var($_POST['draft_pick_id'] ?? '', FILTER_VALIDATE_INT);
                    $newTeamId = filter_var($_POST['new_team_id'] ?? '', FILTER_VALIDATE_INT);
                    
                    if (!$draftPickId || !$newTeamId) {
                        echo json_encode(['success' => false, 'error' => 'Invalid parameters']);
                        exit;
                    }
                    
                    // Verify the draft pick exists and get current info
                    $checkQuery = $db->prepare('SELECT * FROM draft_picks WHERE id = ?');
                    $checkQuery->bindValue(1, $draftPickId);
                    $checkResult = $checkQuery->execute();
                    $draftPick = $checkResult->fetchArray(SQLITE3_ASSOC);
                    
                    if (!$draftPick) {
                        echo json_encode(['success' => false, 'error' => 'Draft pick not found']);
                        exit;
                    }
                    
                    // Verify the target team exists
                    $teamCheckQuery = $db->prepare('SELECT * FROM draft_teams WHERE id = ?');
                    $teamCheckQuery->bindValue(1, $newTeamId);
                    $teamCheckResult = $teamCheckQuery->execute();
                    $targetTeam = $teamCheckResult->fetchArray(SQLITE3_ASSOC);
                    
                    if (!$targetTeam) {
                        echo json_encode(['success' => false, 'error' => 'Target team not found']);
                        exit;
                    }
                    
                    // Update the draft pick to assign player to new team
                    $updateQuery = $db->prepare('UPDATE draft_picks SET team_id = ? WHERE id = ?');
                    $updateQuery->bindValue(1, $newTeamId);
                    $updateQuery->bindValue(2, $draftPickId);
                    
                    if ($updateQuery->execute()) {
                        echo json_encode(['success' => true]);
                    } else {
                        echo json_encode(['success' => false, 'error' => 'Database update failed']);
                    }
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
                    $checkQuery = $db->prepare('SELECT * FROM draft_teams WHERE id = ?');
                    $checkQuery->bindValue(1, $teamId);
                    $checkResult = $checkQuery->execute();
                    $team = $checkResult->fetchArray(SQLITE3_ASSOC);
                    
                    if (!$team) {
                        echo json_encode(['success' => false, 'error' => 'Team not found']);
                        exit;
                    }
                    
                    // Update the team
                    $updateQuery = $db->prepare('UPDATE draft_teams SET team_name = ?, team_color = ? WHERE id = ?');
                    $updateQuery->bindValue(1, $teamName);
                    $updateQuery->bindValue(2, $teamColor);
                    $updateQuery->bindValue(3, $teamId);
                    
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