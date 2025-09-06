<?php
// Page content generation
function generatePageContent($page, $db, $eventSettings, $loginError = null) {
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
            generateHistoryPage($db);
            break;
            
        case 'roster':
            generateRosterPage($db, $eventSettings);
            break;
            
        case 'teams':
            generateTeamsPage($db, $eventSettings);
            break;
            
        case 'login':
            generateLoginPage($loginError);
            break;
            
        case 'admin':
            generateAdminPage($db, $eventSettings);
            break;
            
        default:
            echo '<div class="card">
                <h1 class="card-title">Page Coming Soon</h1>
                <p>This page is under construction. Check back soon!</p>
            </div>';
    }
}

function generateHistoryPage($db) {
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
}

function generateRosterPage($db, $eventSettings) {
    $currentYear = $eventSettings['current_year'] ?? date('Y');
    
    // Get current year players
    $players = $db->query('SELECT * FROM players WHERE current_year = 1 ORDER BY name');
    
    // Get captain IDs from teams for this year
    $captainIds = [];
    $captainTeams = [];
    $captainsQuery = $db->query('SELECT captain_id, name FROM teams WHERE year = ' . $currentYear . ' AND captain_id IS NOT NULL');
    while ($captain = $captainsQuery->fetchArray(SQLITE3_ASSOC)) {
        $captainIds[] = $captain['captain_id'];
        $captainTeams[$captain['captain_id']] = [
            'team_name' => $captain['name'],
            'team_color' => 'var(--bright-orange)'
        ];
    }
    
    echo '<div class="card">
        <h1 class="card-title">' . $currentYear . ' Turkey Bowl Roster</h1>
        <p>Meet this year\'s warriors ready to battle for flag football supremacy!</p>
    </div>';
    
    echo '<div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 20px;">';
    
    $hasPlayers = false;
    while ($player = $players->fetchArray(SQLITE3_ASSOC)) {
        $hasPlayers = true;
        $photoPath = $player['photo_path'] ? htmlspecialchars($player['photo_path']) : 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjAwIiBoZWlnaHQ9IjIwMCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48cmVjdCB3aWR0aD0iMjAwIiBoZWlnaHQ9IjIwMCIgZmlsbD0iIzMzMyIvPjx0ZXh0IHg9IjUwJSIgeT0iNTAlIiBmb250LWZhbWlseT0iQXJpYWwiIGZvbnQtc2l6ZT0iMTQiIGZpbGw9IiNmZmYiIHRleHQtYW5jaG9yPSJtaWRkbGUiIGR5PSIuM2VtIj5ObyBQaG90bzwvdGV4dD48L3N2Zz4=';
        $isCaptain = in_array($player['id'], $captainIds);
        $captainInfo = $isCaptain ? $captainTeams[$player['id']] : null;
        
        $cardBorder = $isCaptain ? $captainInfo['team_color'] : 'var(--bright-orange)';
        $photoBorder = $isCaptain ? $captainInfo['team_color'] : 'var(--gold-accent)';
        
        echo '<div class="card" style="background: linear-gradient(145deg, #2a2a2a 0%, #1a1a1a 100%); border: 2px solid ' . $cardBorder . '; transition: all 0.3s ease; position: relative;" onmouseover="this.style.transform=\'translateY(-5px)\'" onmouseout="this.style.transform=\'translateY(0)\'">
                <div style="text-align: center;">';
        
        // Captain badge
        if ($isCaptain) {
            echo '<div style="position: absolute; top: -8px; left: 50%; transform: translateX(-50%); background: linear-gradient(145deg, ' . $captainInfo['team_color'] . ' 0%, ' . $captainInfo['team_color'] . '80 100%); color: white; padding: 4px 12px; border-radius: 15px; font-size: 0.75rem; font-weight: bold; text-transform: uppercase; letter-spacing: 1px; border: 2px solid var(--gold-accent); box-shadow: 0 2px 8px rgba(0,0,0,0.3); z-index: 10;">
                    ⭐ CAPTAIN ⭐
                  </div>';
        }
        
        echo '<div style="width: 120px; height: 120px; margin: 0 auto 15px; border-radius: 50%; overflow: hidden; border: 3px solid ' . $photoBorder . '; background: var(--dark-gray); position: relative;">
                        <img src="' . $photoPath . '" alt="' . htmlspecialchars($player['name']) . '" style="width: 100%; height: 100%; object-fit: cover;" onerror="this.src=\'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjAwIiBoZWlnaHQ9IjIwMCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48cmVjdCB3aWR0aD0iMjAwIiBoZWlnaHQ9IjIwMCIgZmlsbD0iIzMzMyIvPjx0ZXh0IHg9IjUwJSIgeT0iNTAlIiBmb250LWZhbWlseT0iQXJpYWwiIGZvbnQtc2l6ZT0iMTQiIGZpbGw9IiNmZmYiIHRleHQtYW5jaG9yPSJtaWRkbGUiIGR5PSIuM2VtIj5ObyBQaG90bzwvdGV4dD48L3N2Zz4=\'">
                    </div>
                    
                    <h3 style="color: ' . ($isCaptain ? $captainInfo['team_color'] : 'var(--bright-orange)') . '; font-size: 1.3rem; margin-bottom: 5px;">' . htmlspecialchars($player['name']) . '</h3>';
        
        if ($player['nickname']) {
            echo '<p style="color: var(--gold-accent); font-style: italic; margin-bottom: 10px;">"' . htmlspecialchars($player['nickname']) . '"</p>';
        }
        
        // Show team name for captains
        if ($isCaptain) {
            echo '<div style="background: linear-gradient(145deg, ' . $captainInfo['team_color'] . '20, ' . $captainInfo['team_color'] . '10); padding: 6px 12px; border-radius: 20px; margin: 10px auto; display: inline-block; border: 1px solid ' . $captainInfo['team_color'] . ';">
                    <span style="color: ' . $captainInfo['team_color'] . '; font-weight: bold; font-size: 0.85rem; text-transform: uppercase;">' . htmlspecialchars($captainInfo['team_name']) . '</span>
                  </div>';
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
}

function generateTeamsPage($db, $eventSettings) {
    $currentYear = $eventSettings['current_year'] ?? date('Y');
    
    echo '<div class="card">
        <h1 class="card-title">' . $currentYear . ' Team Lineups</h1>
        <p>Check out this year\'s team rosters and prepare for battle!</p>
    </div>';
    
    $hasTeams = false;
    
    // Get teams from teams table
    $teams = $db->query("SELECT * FROM teams WHERE year = $currentYear ORDER BY name");
    
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
                    <p>No players on this team yet.</p>
                  </div>';
        }
        
        echo '</div>
            </div>';
    }
    
    if (!$hasTeams) {
        echo '<div class="card" style="text-align: center;">
                <h2 style="color: var(--metallic-silver);">No Teams Created Yet</h2>
                <p>Teams will appear here once they\'re created by the admin.</p>
              </div>';
    }
}

function generateLoginPage($loginError) {
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
}

function generateAdminPage($db, $eventSettings) {
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
            <button class="tab-button ' . ($currentTab === 'teams' ? 'active' : '') . '" onclick="switchTab(\'teams\')">Teams</button>
            <button class="tab-button ' . ($currentTab === 'championships' ? 'active' : '') . '" onclick="switchTab(\'championships\')">Championships</button>
            <button class="tab-button ' . ($currentTab === 'awards' ? 'active' : '') . '" onclick="switchTab(\'awards\')">Awards</button>
            <button class="tab-button ' . ($currentTab === 'records' ? 'active' : '') . '" onclick="switchTab(\'records\')">Records</button>
        </div>';
    
    // Generate individual admin tabs
    generateEventTab($currentTab, $eventSettings);
    generatePlayersTab($currentTab, $db);
    generateChampionshipsTab($currentTab, $db);
    generateAwardsTab($currentTab, $db);
    generateRecordsTab($currentTab, $db);
    generateTeamsTab($currentTab, $db);
    
    echo '</div>'; // Close tab-container
}

function generateEventTab($currentTab, $eventSettings) {
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
}

function generatePlayersTab($currentTab, $db) {
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
}

function generateChampionshipsTab($currentTab, $db) {
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
}

function generateAwardsTab($currentTab, $db) {
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
}

function generateRecordsTab($currentTab, $db) {
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
}


function generateTeamsTab($currentTab, $db) {
    echo '<div id="teams" class="tab-content ' . ($currentTab === 'teams' ? 'active' : '') . '">';
    
    // Get current year from event settings
    $eventSettings = $db->query('SELECT current_year FROM event_settings LIMIT 1')->fetchArray(SQLITE3_ASSOC);
    $currentYear = $eventSettings['current_year'] ?? date('Y');
    
    // Get existing teams for current year
    $teams = $db->query("SELECT * FROM teams WHERE year = $currentYear ORDER BY name");
    $hasTeams = false;
    
    echo '<div class="card">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h3 style="color: var(--gold-accent); margin: 0;">Team Management - ' . $currentYear . '</h3>
            <div class="team-controls">
                <button onclick="showAddTeamForm()" class="btn btn-primary">➕ Add Team</button>
            </div>
        </div>
        
        <!-- Add Team Form -->
        <div id="add-team-form" class="admin-form" style="display: none; margin-bottom: 20px; padding: 20px; background: var(--dark-gray); border-radius: 8px;">
            <h4 style="color: var(--gold-accent); margin-bottom: 15px;">Add New Team</h4>
            <form method="POST">
                <input type="hidden" name="action" value="add_team">
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Team Name:</label>
                        <input type="text" name="team_name" class="form-input" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Captain:</label>
                        <select name="captain_id" class="form-input">
                            <option value="">No Captain</option>';
                            
    // Get available players for captain selection
    $players = $db->query('SELECT id, name FROM players WHERE current_year = 1 ORDER BY name');
    while ($player = $players->fetchArray(SQLITE3_ASSOC)) {
        echo '<option value="' . $player['id'] . '">' . htmlspecialchars($player['name']) . '</option>';
    }
    
    echo '          </select>
                    </div>
                </div>
                <div class="form-row">
                    <button type="submit" class="btn btn-primary">💾 Add Team</button>
                    <button type="button" onclick="hideAddTeamForm()" class="btn btn-secondary">❌ Cancel</button>
                </div>
            </form>
        </div>
        
        <div class="teams-display">';
    
    while ($team = $teams->fetchArray(SQLITE3_ASSOC)) {
        $hasTeams = true;
        
        // Get team players count
        $playerCount = $db->query('SELECT COUNT(*) as count FROM team_players WHERE team_id = ' . $team['id'])->fetchArray(SQLITE3_ASSOC);
        
        // Get captain info
        $captain = null;
        if ($team['captain_id']) {
            $captainQuery = $db->prepare('SELECT name FROM players WHERE id = ?');
            $captainQuery->bindValue(1, $team['captain_id']);
            $captainResult = $captainQuery->execute();
            $captain = $captainResult->fetchArray(SQLITE3_ASSOC);
        }
        
        echo '<div class="team-card" style="border-top: 4px solid var(--bright-orange);">
            <div class="team-header">
                <div class="team-title-section">
                    <h4 style="color: var(--bright-orange);">' . htmlspecialchars($team['name']) . '</h4>
                </div>';
                
        if ($captain) {
            echo '<div class="captain-badge">
                    <span style="color: var(--gold-accent);">👑 Captain: ' . htmlspecialchars($captain['name']) . '</span>
                  </div>';
        }
        
        echo '  <div class="team-stats">
                    <span style="color: var(--metallic-silver);">' . $playerCount['count'] . ' Players</span>
                    <button onclick="manageTeamPlayers(' . $team['id'] . ')" class="btn btn-secondary btn-sm" style="margin-left: 10px;">👥 Manage Players</button>
                    <button onclick="deleteTeam(' . $team['id'] . ')" class="btn btn-danger btn-sm" style="margin-left: 5px;">🗑️ Delete</button>
                </div>
            </div>
            <div class="team-roster" id="team-' . $team['id'] . '-roster">';
        
        // Get team players
        $teamPlayers = $db->query('
            SELECT p.*, tp.draft_order 
            FROM players p 
            JOIN team_players tp ON p.id = tp.player_id 
            WHERE tp.team_id = ' . $team['id'] . ' 
            ORDER BY tp.draft_order, p.name
        ');
        
        while ($player = $teamPlayers->fetchArray(SQLITE3_ASSOC)) {
            $isCaptain = $player['id'] == $team['captain_id'];
            $captainIcon = $isCaptain ? '👑 ' : '';
            
            echo '<div class="roster-player">
                <div class="player-info">
                    ' . ($player['photo_path'] ? '<img src="' . htmlspecialchars($player['photo_path']) . '" alt="Photo" class="player-photo">' : '<div class="player-photo-placeholder">📷</div>') . '
                    <div class="player-details">
                        <strong style="color: var(--pure-white);">' . $captainIcon . htmlspecialchars($player['name']) . '</strong>';
            if ($player['nickname']) {
                echo '<br><small style="color: var(--gold-accent); font-style: italic;">"' . htmlspecialchars($player['nickname']) . '"</small>';
            }
            echo '<br><small style="color: var(--metallic-silver);">' . htmlspecialchars($player['position'] ?: 'Utility') . ' • ' . $player['years_played'] . ' years</small>
                    </div>
                </div>
                <button onclick="removePlayerFromTeam(' . $team['id'] . ', ' . $player['id'] . ')" class="btn btn-danger btn-sm">Remove</button>
            </div>';
        }
        
        // Add empty state message for teams with no players
        if ($playerCount['count'] == 0) {
            echo '<div class="empty-roster-message" style="padding: 20px; text-align: center; color: var(--metallic-silver); font-style: italic; border: 2px dashed var(--metallic-silver); border-radius: 8px; margin: 10px 0;">
                <p>No players on this team yet.</p>
            </div>';
        }
        
        echo '</div></div>';
    }
    
    if (!$hasTeams) {
        echo '<div style="text-align: center; padding: 40px; color: var(--metallic-silver);">
            <p>No teams created yet.</p>
            <p>Click "Add Team" to get started.</p>
        </div>';
    }
    
    echo '</div></div></div>';
}