<?php
// CSS and JavaScript assets
function generateAssets($eventSettings) {
    generateCSS();
    generateJavaScript($eventSettings);
}

function generateCSS() {
    echo '<style>
        :root {
            --pure-white: #ffffff;
            --dark-gray: #333;
            --bright-orange: #ff6600;
            --gold-accent: #ffd700;
            --metallic-silver: #c0c0c0;
            --navy-blue: #1a2332;
            --success-green: #28a745;
            --alert-red: #dc3545;
        }

        /* Global Styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: \'Segoe UI\', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, var(--navy-blue) 0%, #0f1419 50%, #1a1a1a 100%);
            color: var(--pure-white);
            line-height: 1.6;
            min-height: 100vh;
            background-attachment: fixed;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        /* Header */
        .header {
            background: linear-gradient(180deg, rgba(26,35,50,0.95) 0%, rgba(15,20,25,0.98) 100%);
            padding: 20px 0;
            box-shadow: 
                0 4px 8px rgba(0,0,0,0.3),
                inset 0 1px 0 rgba(255,255,255,0.1);
            position: relative;
            border-bottom: 2px solid var(--metallic-silver);
        }

        .header::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: 
                radial-gradient(ellipse at top, rgba(255,102,0,0.1) 0%, transparent 50%),
                radial-gradient(ellipse at bottom, rgba(255,215,0,0.05) 0%, transparent 50%);
            pointer-events: none;
        }

        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: relative;
        }

        .logo {
            font-family: \'Arial Black\', Arial, sans-serif;
            font-size: 1.8rem;
            font-weight: 900;
            color: var(--bright-orange);
            text-shadow: 
                2px 2px 4px rgba(0,0,0,0.8),
                0 0 10px rgba(255,102,0,0.5);
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        /* Navigation */
        .nav {
            display: flex;
            gap: 5px;
        }

        .nav-item {
            list-style: none;
        }

        .nav-link {
            display: block;
            padding: 12px 20px;
            color: var(--pure-white);
            text-decoration: none;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.9rem;
            letter-spacing: 1px;
            border-radius: 6px;
            background: linear-gradient(180deg, #444 0%, #333 50%, #222 100%);
            border: 1px solid #555;
            box-shadow: 
                0 2px 4px rgba(0,0,0,0.3),
                inset 0 1px 0 rgba(255,255,255,0.1),
                inset 0 -1px 0 rgba(0,0,0,0.3);
            transition: all 0.3s ease;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.5);
        }

        .nav-link:hover {
            background: linear-gradient(180deg, #555 0%, #444 50%, #333 100%);
            transform: translateY(-1px);
            box-shadow: 
                0 4px 8px rgba(0,0,0,0.4),
                inset 0 1px 0 rgba(255,255,255,0.15),
                inset 0 -1px 0 rgba(0,0,0,0.4);
        }

        .nav-link.active {
            background: linear-gradient(180deg, var(--bright-orange) 0%, #cc5500 100%);
            color: var(--pure-white);
            border-color: var(--gold-accent);
            box-shadow: 
                0 0 15px rgba(255,102,0,0.5),
                inset 0 1px 0 rgba(255,255,255,0.2),
                inset 0 -1px 0 rgba(0,0,0,0.3);
        }

        .nav-link.active:hover {
            background: linear-gradient(180deg, #ff7700 0%, var(--bright-orange) 100%);
            transform: translateY(-1px);
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
            content: \'\';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 1px;
            background: linear-gradient(90deg, transparent 0%, var(--metallic-silver) 50%, transparent 100%);
            opacity: 0.5;
        }

        .card-title {
            font-family: \'Arial Black\', Arial, sans-serif;
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

        .btn-success {
            background: linear-gradient(180deg, var(--success-green) 0%, #1e7e34 100%);
            color: var(--pure-white);
            border: 2px solid var(--gold-accent);
        }

        .btn-success:hover {
            background: linear-gradient(180deg, #34ce57 0%, var(--success-green) 100%);
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(40,167,69,0.3);
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
            border-radius: 4px;
            background: rgba(255,255,255,0.1);
            color: var(--pure-white);
            transition: all 0.3s ease;
        }

        .form-input:focus {
            outline: none;
            border-color: var(--bright-orange);
            background: rgba(255,255,255,0.15);
            box-shadow: 0 0 10px rgba(255,102,0,0.3);
        }

        /* Countdown */
        .countdown {
            display: flex;
            justify-content: center;
            gap: 40px;
            margin: 30px 0;
            flex-wrap: wrap;
        }

        .countdown-item {
            text-align: center;
            background: linear-gradient(145deg, #333 0%, #1a1a1a 100%);
            border: 2px solid var(--gold-accent);
            border-radius: 12px;
            padding: 20px;
            min-width: 80px;
            box-shadow: 
                0 8px 16px rgba(0,0,0,0.4),
                inset 0 1px 0 rgba(255,255,255,0.1);
        }

        .countdown-number {
            display: block;
            font-size: 2.5rem;
            font-weight: bold;
            color: var(--bright-orange);
            text-shadow: 0 0 10px rgba(255,102,0,0.5);
            font-family: \'Arial Black\', Arial, sans-serif;
        }

        .countdown-label {
            font-size: 0.9rem;
            color: var(--metallic-silver);
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-top: 5px;
        }

        /* Tables */
        .admin-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .admin-table th {
            background: linear-gradient(180deg, var(--bright-orange) 0%, #cc5500 100%);
            color: var(--pure-white);
            padding: 15px 12px;
            text-align: left;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
            border: 1px solid var(--gold-accent);
        }

        .admin-table td {
            padding: 15px 12px;
            border-bottom: 1px solid #444;
            background: rgba(255,255,255,0.05);
        }

        .admin-table tr:hover td {
            background: rgba(255,102,0,0.1);
        }

        /* Tab System */
        .tab-container {
            margin-top: 30px;
        }

        .tab-nav {
            display: flex;
            gap: 5px;
            margin-bottom: 0;
            flex-wrap: wrap;
            background: linear-gradient(180deg, #333 0%, #222 100%);
            padding: 10px;
            border-radius: 8px 8px 0 0;
            border: 2px solid var(--metallic-silver);
            border-bottom: none;
        }

        .tab-button {
            padding: 12px 20px;
            background: linear-gradient(180deg, #555 0%, #444 100%);
            color: var(--pure-white);
            border: 1px solid #666;
            cursor: pointer;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-size: 0.85rem;
            border-radius: 4px;
            transition: all 0.3s ease;
            box-shadow: inset 0 1px 0 rgba(255,255,255,0.1);
        }

        .tab-button:hover {
            background: linear-gradient(180deg, #666 0%, #555 100%);
            transform: translateY(-1px);
        }

        .tab-button.active {
            background: linear-gradient(180deg, var(--bright-orange) 0%, #cc5500 100%);
            border-color: var(--gold-accent);
            color: var(--pure-white);
            box-shadow: 
                0 0 15px rgba(255,102,0,0.5),
                inset 0 1px 0 rgba(255,255,255,0.2);
        }

        .tab-content {
            display: none;
            background: linear-gradient(145deg, #2a2a2a 0%, #1a1a1a 100%);
            border: 2px solid var(--metallic-silver);
            border-top: none;
            border-radius: 0 0 8px 8px;
            padding: 30px;
            min-height: 400px;
        }

        .tab-content.active {
            display: block;
        }

        /* Admin Forms */
        .admin-form {
            background: rgba(255,102,0,0.05);
            border: 2px solid var(--bright-orange);
            border-radius: 8px;
            padding: 25px;
            margin-bottom: 30px;
        }

        .form-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
        }

        .admin-actions {
            white-space: nowrap;
        }

        .btn-small {
            padding: 8px 16px;
            font-size: 0.85rem;
            margin-right: 5px;
        }

        /* Messages */
        .success-message, .error-message {
            padding: 15px 20px;
            margin-bottom: 20px;
            border-radius: 6px;
            font-weight: bold;
            text-align: center;
        }

        .success-message {
            background: rgba(40,167,69,0.9);
            color: var(--pure-white);
            border: 2px solid var(--success-green);
        }

        .error-message {
            background: rgba(220,53,69,0.9);
            color: var(--pure-white);
            border: 2px solid var(--alert-red);
        }

        /* Inline Edit Forms */
        .inline-edit-row {
            background: rgba(255,102,0,0.1);
        }

        .inline-edit-form {
            padding: 20px;
            background: linear-gradient(145deg, rgba(26,35,50,0.8) 0%, rgba(15,20,25,0.9) 100%);
            border: 2px solid var(--bright-orange);
            border-radius: 8px;
            margin: 10px 0;
        }

        .form-actions {
            display: flex;
            gap: 10px;
            align-items: center;
        }

        /* Draft System Styles */
        .draft-teams-preview {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin: 20px 0;
        }

        .team-preview {
            padding: 20px;
            border-radius: 8px;
            border: 2px solid transparent;
        }

        .draft-header {
            background: linear-gradient(145deg, #2a2a2a 0%, #1a1a1a 100%);
            border: 2px solid var(--gold-accent);
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            text-align: center;
        }

        .draft-status {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin: 15px 0;
            flex-wrap: wrap;
            gap: 20px;
        }

        .current-pick-info {
            font-size: 1.2rem;
            font-weight: bold;
            color: var(--bright-orange);
        }

        .draft-timer {
            font-size: 3rem;
            font-weight: bold;
            color: var(--pure-white);
            background: linear-gradient(145deg, #333 0%, #1a1a1a 100%);
            border: 3px solid var(--gold-accent);
            border-radius: 50%;
            width: 100px;
            height: 100px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 0 20px rgba(255,215,0,0.3);
        }

        .draft-main {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 30px;
            margin-top: 20px;
        }

        .draft-board {
            background: linear-gradient(145deg, #2a2a2a 0%, #1a1a1a 100%);
            border: 2px solid var(--metallic-silver);
            border-radius: 8px;
            padding: 20px;
        }

        .player-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            gap: 15px;
            max-height: 600px;
            overflow-y: auto;
        }

        .draft-player-card {
            background: linear-gradient(145deg, #333 0%, #222 100%);
            border: 2px solid var(--metallic-silver);
            border-radius: 8px;
            padding: 15px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .draft-player-card:hover,
        .draft-player-card.current-turn {
            border-color: var(--bright-orange);
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(255,102,0,0.3);
        }

        .draft-player-photo {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            margin-bottom: 10px;
            object-fit: cover;
            border: 2px solid var(--metallic-silver);
        }

        .draft-player-placeholder {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            margin: 0 auto 10px;
            background: var(--dark-gray);
            display: flex;
            align-items: center;
            justify-content: center;
            border: 2px solid var(--metallic-silver);
            font-size: 24px;
        }

        .team-roster-card {
            background: linear-gradient(145deg, #2a2a2a 0%, #1a1a1a 100%);
            border: 2px solid var(--metallic-silver);
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
        }

        .team-roster-card.current-pick {
            border-color: var(--bright-orange);
            box-shadow: 0 0 20px rgba(255,102,0,0.3);
        }

        .team-roster-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }

        .team-roster-players {
            max-height: 300px;
            overflow-y: auto;
        }

        .roster-player-mini {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 8px;
            margin-bottom: 8px;
            background: rgba(255,255,255,0.05);
            border-radius: 4px;
        }

        .roster-player-mini img,
        .roster-placeholder {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            object-fit: cover;
            border: 1px solid var(--metallic-silver);
        }

        .roster-placeholder {
            background: var(--dark-gray);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
        }

        /* Teams Display */
        .teams-display {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 25px;
        }

        .team-card {
            background: linear-gradient(145deg, #2a2a2a 0%, #1a1a1a 100%);
            border: 2px solid var(--metallic-silver);
            border-radius: 8px;
            padding: 25px;
            margin-bottom: 25px;
            box-shadow: 0 8px 16px rgba(0,0,0,0.3);
        }

        .team-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 20px;
            flex-wrap: wrap;
            gap: 15px;
        }

        .captain-badge {
            background: linear-gradient(145deg, rgba(255,215,0,0.2) 0%, rgba(255,215,0,0.1) 100%);
            padding: 8px 15px;
            border-radius: 20px;
            border: 1px solid var(--gold-accent);
        }

        .roster-player {
            display: flex;
            align-items: center;
            padding: 12px;
            margin-bottom: 10px;
            background: rgba(255,255,255,0.05);
            border-radius: 6px;
            border-left: 3px solid var(--metallic-silver);
            transition: all 0.3s ease;
        }

        .roster-player:hover {
            background: rgba(255,102,0,0.1);
            border-left-color: var(--bright-orange);
        }

        .player-info {
            display: flex;
            align-items: center;
            gap: 15px;
            width: 100%;
        }

        .player-photo {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid var(--metallic-silver);
        }

        .player-photo-placeholder {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: var(--dark-gray);
            display: flex;
            align-items: center;
            justify-content: center;
            border: 2px solid var(--metallic-silver);
            font-size: 20px;
        }

        /* Mobile Responsiveness */
        @media (max-width: 768px) {
            .header-content {
                flex-direction: column;
                gap: 20px;
            }

            .nav {
                flex-wrap: wrap;
                justify-content: center;
            }

            .nav-link {
                padding: 10px 15px;
                font-size: 0.8rem;
            }

            .logo {
                font-size: 1.4rem;
            }

            .card {
                padding: 20px;
            }

            .card-title {
                font-size: 1.5rem;
            }

            .countdown {
                gap: 20px;
            }

            .countdown-number {
                font-size: 2rem;
            }

            .draft-main {
                grid-template-columns: 1fr;
            }

            .form-row {
                grid-template-columns: 1fr;
            }

            .tab-nav {
                justify-content: center;
            }

            .tab-button {
                padding: 10px 15px;
                font-size: 0.75rem;
            }

            .admin-table {
                font-size: 0.9rem;
            }

            .admin-table th,
            .admin-table td {
                padding: 10px 8px;
            }

            .teams-display {
                grid-template-columns: 1fr;
            }
        }
        
        /* Team Management Styles */
        .team-controls {
            display: flex;
            gap: 10px;
            align-items: center;
        }
        
        .team-title-section {
            position: relative;
        }
        
        .team-edit-form {
            background: rgba(0,0,0,0.3);
            padding: 15px;
            border-radius: 8px;
            border: 2px solid var(--bright-orange);
        }
        
        .team-edit-form .form-input {
            width: 100%;
            max-width: 200px;
        }
        
        .team-buttons {
            display: flex;
            gap: 10px;
            margin-top: 10px;
        }
        
        .btn-sm {
            padding: 8px 12px;
            font-size: 0.8rem;
        }
        
        /* Drag and Drop Styles */
        .draggable-player {
            cursor: grab;
            transition: all 0.2s ease;
            position: relative;
        }
        
        .draggable-player:hover {
            background: rgba(255,102,0,0.1);
            transform: translateX(5px);
        }
        
        .draggable-player[draggable="true"] .drag-handle {
            opacity: 1;
        }
        
        .draggable-player:active {
            cursor: grabbing;
        }
        
        .team-roster.drop-zone {
            min-height: 100px;
            border: 2px dashed transparent;
            border-radius: 8px;
            transition: all 0.3s ease;
        }
        
        .team-roster.drop-zone.drag-over {
            border-color: var(--bright-orange);
            background: rgba(255,102,0,0.1);
            transform: scale(1.02);
        }
        
        .team-roster.drop-zone.drag-over .empty-roster-message {
            border-color: var(--bright-orange);
            background: rgba(255,102,0,0.2);
            color: var(--bright-orange);
        }
        
        .empty-roster-message {
            transition: all 0.3s ease;
        }
        
        .drag-handle {
            font-size: 1.2rem;
            opacity: 0.5;
            transition: opacity 0.2s ease;
        }
        
        .drag-handle:hover {
            opacity: 1;
            color: var(--bright-orange);
        }
        
        .edit-team-btn:hover {
            background: var(--gold-accent) !important;
            transform: scale(1.05);
        }
        
        /* Success/Error message styles */
        .temp-message {
            animation: slideIn 0.3s ease-out;
        }
        
        @keyframes slideIn {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
    </style>';
}

function generateJavaScript($eventSettings) {
    echo '<script>
        // Countdown timer functionality
        function updateCountdown() {
            const eventDate = new Date(\'' . ($eventSettings['event_date'] ?? '2024-11-28T10:00:00') . '\').getTime();
            const now = new Date().getTime();
            const distance = eventDate - now;

            if (distance > 0) {
                const days = Math.floor(distance / (1000 * 60 * 60 * 24));
                const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                const seconds = Math.floor((distance % (1000 * 60)) / 1000);

                if (document.getElementById(\'days\')) document.getElementById(\'days\').textContent = days;
                if (document.getElementById(\'hours\')) document.getElementById(\'hours\').textContent = hours;
                if (document.getElementById(\'minutes\')) document.getElementById(\'minutes\').textContent = minutes;
                if (document.getElementById(\'seconds\')) document.getElementById(\'seconds\').textContent = seconds;
            } else {
                if (document.getElementById(\'countdown-container\')) {
                    document.getElementById(\'countdown-container\').innerHTML = \'<h1 style="color: var(--gold-accent); text-align: center; font-size: 4rem; font-family: \\\'Arial Black\\\', Arial, sans-serif; text-shadow: 3px 3px 6px rgba(0,0,0,0.8), 0 0 20px rgba(255,215,0,0.5); margin: 40px 0; animation: pulse 2s infinite;">GAME DAY!</h1><style>@keyframes pulse { 0%, 100% { transform: scale(1); } 50% { transform: scale(1.05); } }</style>\';
                }
            }
        }

        // Update countdown every second
        if (document.getElementById(\'countdown-container\')) {
            updateCountdown();
            setInterval(updateCountdown, 1000);
        }

        // Inline edit form management
        function closeAllInlineEditForms() {
            const editRows = document.querySelectorAll(\'.inline-edit-row\');
            editRows.forEach(row => row.remove());
        }

        // Admin functionality
        function switchTab(tabName) {
            // Hide all tabs
            const tabs = document.querySelectorAll(\'.tab-content\');
            tabs.forEach(tab => tab.classList.remove(\'active\'));
            
            // Remove active class from all buttons
            const buttons = document.querySelectorAll(\'.tab-button\');
            buttons.forEach(button => button.classList.remove(\'active\'));
            
            // Show selected tab
            document.getElementById(tabName).classList.add(\'active\');
            
            // Activate selected button
            event.target.classList.add(\'active\');
            
            // Update URL without page reload
            const url = new URL(window.location);
            url.searchParams.set(\'tab\', tabName);
            window.history.replaceState({}, \'\', url);
        }

        // Team Management Functions
        let editModeActive = false;
        
        function toggleEditMode() {
            editModeActive = !editModeActive;
            const btn = document.getElementById(\'edit-mode-btn\');
            const dragHandles = document.querySelectorAll(\'.drag-handle\');
            const editButtons = document.querySelectorAll(\'.edit-team-btn\');
            const players = document.querySelectorAll(\'.draggable-player\');
            const teamCards = document.querySelectorAll(\'.team-roster\');
            
            if (editModeActive) {
                btn.textContent = \'✅ Done Editing\';
                btn.classList.remove(\'btn-secondary\');
                btn.classList.add(\'btn-success\');
                
                // Show drag handles and edit buttons
                dragHandles.forEach(handle => handle.style.display = \'block\');
                editButtons.forEach(button => button.style.display = \'inline-block\');
                
                // Enable dragging
                players.forEach(player => {
                    player.draggable = true;
                    player.addEventListener(\'dragstart\', handleDragStart);
                    player.addEventListener(\'dragend\', handleDragEnd);
                });
                
                // Enable drop zones
                teamCards.forEach(roster => {
                    roster.addEventListener(\'dragover\', handleDragOver);
                    roster.addEventListener(\'dragleave\', handleDragLeave);
                    roster.addEventListener(\'drop\', handleDrop);
                    roster.classList.add(\'drop-zone\');
                });
            } else {
                btn.textContent = \'📝 Edit Teams\';
                btn.classList.remove(\'btn-success\');
                btn.classList.add(\'btn-secondary\');
                
                // Hide drag handles and edit buttons
                dragHandles.forEach(handle => handle.style.display = \'none\');
                editButtons.forEach(button => button.style.display = \'none\');
                
                // Disable dragging
                players.forEach(player => {
                    player.draggable = false;
                    player.removeEventListener(\'dragstart\', handleDragStart);
                    player.removeEventListener(\'dragend\', handleDragEnd);
                });
                
                // Disable drop zones
                teamCards.forEach(roster => {
                    roster.removeEventListener(\'dragover\', handleDragOver);
                    roster.removeEventListener(\'dragleave\', handleDragLeave);
                    roster.removeEventListener(\'drop\', handleDrop);
                    roster.classList.remove(\'drop-zone\');
                });
                
                // Hide any open edit forms
                document.querySelectorAll(\'.team-edit-form\').forEach(form => {
                    form.style.display = \'none\';
                });
                document.querySelectorAll(\'.team-name-display\').forEach(display => {
                    display.style.display = \'block\';
                });
            }
        }
        
        // Drag and Drop Functions
        let draggedPlayer = null;
        
        function handleDragStart(e) {
            // Find the draggable player element
            draggedPlayer = e.target.closest(\'.draggable-player\');
            if (draggedPlayer) {
                draggedPlayer.style.opacity = \'0.5\';
                e.dataTransfer.effectAllowed = \'move\';
            }
        }
        
        function handleDragEnd(e) {
            if (draggedPlayer) {
                draggedPlayer.style.opacity = \'1\';
            }
            // Clean up all drag-over classes
            document.querySelectorAll(\'.drag-over\').forEach(el => {
                el.classList.remove(\'drag-over\');
            });
            draggedPlayer = null;
        }
        
        function handleDragOver(e) {
            e.preventDefault();
            e.dataTransfer.dropEffect = \'move\';
            e.currentTarget.classList.add(\'drag-over\');
        }
        
        function handleDragLeave(e) {
            // Only remove if we\'re truly leaving the drop zone
            if (!e.currentTarget.contains(e.relatedTarget)) {
                e.currentTarget.classList.remove(\'drag-over\');
            }
        }
        
        function handleDrop(e) {
            e.preventDefault();
            const dropZone = e.currentTarget;
            dropZone.classList.remove(\'drag-over\');
            
            if (!draggedPlayer) return;
            
            const targetTeamRoster = dropZone;
            const targetTeamId = targetTeamRoster.id.match(/team-(\\d+)-roster/)[1];
            const currentTeamId = draggedPlayer.dataset.currentTeam;
            
            if (targetTeamId === currentTeamId) return; // Same team
            
            // Move player to new team
            targetTeamRoster.appendChild(draggedPlayer);
            draggedPlayer.dataset.currentTeam = targetTeamId;
            
            // Send update to server
            transferPlayer(draggedPlayer.dataset.draftPickId, targetTeamId);
        }
        
        function transferPlayer(draftPickId, newTeamId) {
            fetch(window.location.href, {
                method: \'POST\',
                headers: {
                    \'Content-Type\': \'application/x-www-form-urlencoded\',
                },
                body: `action=transfer_player&draft_pick_id=${draftPickId}&new_team_id=${newTeamId}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showMessage(\'Player transferred successfully!\', \'success\');
                    // Update team stats
                    setTimeout(() => window.location.reload(), 1000);
                } else {
                    showMessage(\'Error transferring player: \' + (data.error || \'Unknown error\'), \'error\');
                    window.location.reload(); // Revert on error
                }
            })
            .catch(error => {
                console.error(\'Transfer error:\', error);
                showMessage(\'Transfer failed. Refreshing page...\', \'error\');
                setTimeout(() => window.location.reload(), 1500);
            });
        }
        
        function editTeam(teamId) {
            const teamCard = document.querySelector(`[data-team-id="${teamId}"]`);
            const nameDisplay = teamCard.querySelector(\'.team-name-display\');
            const editForm = teamCard.querySelector(\'.team-edit-form\');
            
            nameDisplay.style.display = \'none\';
            editForm.style.display = \'block\';
        }
        
        function cancelTeamEdit(teamId) {
            const teamCard = document.querySelector(`[data-team-id="${teamId}"]`);
            const nameDisplay = teamCard.querySelector(\'.team-name-display\');
            const editForm = teamCard.querySelector(\'.team-edit-form\');
            
            nameDisplay.style.display = \'block\';
            editForm.style.display = \'none\';
        }
        
        function saveTeam(teamId) {
            const teamCard = document.querySelector(`[data-team-id="${teamId}"]`);
            const nameInput = teamCard.querySelector(\'.team-name-input\');
            const colorInput = teamCard.querySelector(\'.team-color-input\');
            
            const newName = nameInput.value.trim();
            const newColor = colorInput.value;
            
            if (!newName) {
                showMessage(\'Team name cannot be empty\', \'error\');
                return;
            }
            
            fetch(window.location.href, {
                method: \'POST\',
                headers: {
                    \'Content-Type\': \'application/x-www-form-urlencoded\',
                },
                body: `action=update_team&team_id=${teamId}&team_name=${encodeURIComponent(newName)}&team_color=${encodeURIComponent(newColor)}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showMessage(\'Team updated successfully!\', \'success\');
                    setTimeout(() => window.location.reload(), 1000);
                } else {
                    showMessage(\'Error updating team: \' + (data.error || \'Unknown error\'), \'error\');
                }
            })
            .catch(error => {
                console.error(\'Update error:\', error);
                showMessage(\'Update failed\', \'error\');
            });
        }
        
        function showMessage(message, type) {
            // Create or update message display
            let messageDiv = document.querySelector(\'.temp-message\');
            if (!messageDiv) {
                messageDiv = document.createElement(\'div\');
                messageDiv.className = \'temp-message\';
                messageDiv.style.cssText = \'position: fixed; top: 20px; right: 20px; padding: 15px; border-radius: 5px; color: white; font-weight: bold; z-index: 1000; max-width: 300px;\';
                document.body.appendChild(messageDiv);
            }
            
            messageDiv.textContent = message;
            messageDiv.style.backgroundColor = type === \'success\' ? \'var(--bright-orange)\' : \'#dc3545\';
            messageDiv.style.display = \'block\';
            
            setTimeout(() => {
                messageDiv.style.display = \'none\';
            }, 3000);
        }

        // Player functions
        function editPlayer(id, name, nickname, position, bio, yearsPlayed, isCurrentYear) {
            // Close any existing edit forms
            closeAllInlineEditForms();
            
            // Create the inline edit form
            const editRow = document.createElement(\'tr\');
            editRow.id = `edit-player-${id}`;
            editRow.className = \'inline-edit-row\';
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
                                    <option value="1" ${isCurrentYear ? \'selected\' : \'\'}>Yes - Active</option>
                                    <option value="0" ${!isCurrentYear ? \'selected\' : \'\'}>No - Inactive</option>
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
                const form = document.createElement(\'form\');
                form.method = \'POST\';
                form.innerHTML = `
                    <input type="hidden" name="action" value="edit_player">
                    <input type="hidden" name="id" value="${id}">
                    <input type="hidden" name="name" value="${name}">
                    <input type="hidden" name="nickname" value="${nickname || \'\'}">
                    <input type="hidden" name="position" value="${position || \'\'}">
                    <input type="hidden" name="bio" value="${bio || \'\'}">
                    <input type="hidden" name="years_played" value="${yearsPlayed}">
                    ${isCurrentYear === \'1\' ? \'<input type="hidden" name="current_year" value="1">\' : \'\'}
                `;
                document.body.appendChild(form);
                form.submit();
            } else {
                alert(\'Please provide a valid name and years played (1-20).\');
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
                const form = document.createElement(\'form\');
                form.method = \'POST\';
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
            const editRow = document.createElement(\'tr\');
            editRow.id = `edit-championship-${id}`;
            editRow.className = \'inline-edit-row\';
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
                const form = document.createElement(\'form\');
                form.method = \'POST\';
                form.innerHTML = `
                    <input type="hidden" name="action" value="edit_championship">
                    <input type="hidden" name="id" value="${id}">
                    <input type="hidden" name="year" value="${year}">
                    <input type="hidden" name="team_name" value="${teamName}">
                `;
                document.body.appendChild(form);
                form.submit();
            } else {
                alert(\'Please fill in all required fields with valid values.\');
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
                const form = document.createElement(\'form\');
                form.method = \'POST\';
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
            const editRow = document.createElement(\'tr\');
            editRow.id = `edit-award-${id}`;
            editRow.className = \'inline-edit-row\';
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
                const form = document.createElement(\'form\');
                form.method = \'POST\';
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
                alert(\'Please fill in all required fields with valid values.\');
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
                const form = document.createElement(\'form\');
                form.method = \'POST\';
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
            const editRow = document.createElement(\'tr\');
            editRow.id = `edit-record-${id}`;
            editRow.className = \'inline-edit-row\';
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
                const form = document.createElement(\'form\');
                form.method = \'POST\';
                form.innerHTML = `
                    <input type="hidden" name="action" value="edit_record">
                    <input type="hidden" name="id" value="${id}">
                    <input type="hidden" name="year" value="${year}">
                    <input type="hidden" name="record_name" value="${recordName}">
                    <input type="hidden" name="record_value" value="${recordValue}">
                    <input type="hidden" name="player_name" value="${playerName || \'\'}">
                `;
                document.body.appendChild(form);
                form.submit();
            } else {
                alert(\'Please fill in all required fields with valid values.\');
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
                const form = document.createElement(\'form\');
                form.method = \'POST\';
                form.innerHTML = `
                    <input type="hidden" name="action" value="delete_record">
                    <input type="hidden" name="id" value="${id}">
                `;
                document.body.appendChild(form);
                form.submit();
            }
        }
        
        // Draft System JavaScript
        
        // Draft setup form - captain selection
        document.addEventListener(\'DOMContentLoaded\', function() {
            const numTeamsSelect = document.querySelector(\'select[name="num_teams"]\');
            const captainsContainer = document.getElementById(\'team-captains-container\');
            const captainSelects = document.getElementById(\'captain-selects\');
            const setupBtn = document.getElementById(\'setup-draft-btn\');
            
            if (numTeamsSelect) {
                numTeamsSelect.addEventListener(\'change\', function() {
                    const numTeams = parseInt(this.value);
                    if (numTeams >= 2 && numTeams <= 4) {
                        // Fetch active players for captain selection
                        fetch(window.location.href + \'&action=get_active_players\', {
                            method: \'POST\',
                            headers: {
                                \'Content-Type\': \'application/x-www-form-urlencoded\',
                            },
                            body: \'action=get_active_players\'
                        })
                            .then(response => response.json())
                            .then(players => {
                                console.log(\'Fetched players:\', players);
                                if (players && players.length > 0) {
                                    generateCaptainSelects(numTeams, players);
                                    captainsContainer.style.display = \'block\';
                                } else {
                                    alert(\'No active players found. Please add some active players first.\');
                                }
                            })
                            .catch(error => {
                                console.error(\'Error fetching players:\', error);
                                alert(\'Error fetching players. Please refresh and try again.\');
                            });
                    } else {
                        captainsContainer.style.display = \'none\';
                    }
                });
            }
            
            function generateCaptainSelects(numTeams, players) {
                let html = \'\';
                for (let i = 1; i <= numTeams; i++) {
                    html += `
                        <div class="form-group" style="margin-bottom: 15px;">
                            <label class="form-label">Team ${i} Captain:</label>
                            <select name="captains[]" class="form-input" required>
                                <option value="">Select Captain...</option>
                                ${players.map(player => 
                                    `<option value="${player.id}">${player.name}${player.nickname ? \' "\' + player.nickname + \'"\' : \'\'}</option>`
                                ).join(\'\')}
                            </select>
                        </div>
                    `;
                }
                captainSelects.innerHTML = html;
            }
            
            // Initialize draft interface if active
            if (document.getElementById(\'draft-interface\')) {
                initializeDraftInterface();
            }
        });
        
        // Draft interface functionality
        function initializeDraftInterface() {
            loadDraftState();
            startTimer();
            
            // Refresh every 5 seconds to sync with other users
            setInterval(loadDraftState, 5000);
        }
        
        function loadDraftState() {
            fetch(window.location.href, {
                method: \'POST\',
                headers: {
                    \'Content-Type\': \'application/x-www-form-urlencoded\',
                },
                body: \'action=get_draft_state\'
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        updateCurrentPickInfo(data.currentTeam, data.round, data.pickNumber);
                        updateTimer(data.timeRemaining);
                        updateAvailablePlayers(data.availablePlayers, data.currentTeamId);
                        updateTeamRosters(data.teams, data.currentTeamId);
                    } else if (data.message === \'No active draft\') {
                        // Draft has ended - redirect to Teams tab
                        showDraftMessage(\'Draft completed! Redirecting to Teams...\', \'success\');
                        setTimeout(() => {
                            window.location.href = \'?page=admin&tab=teams\';
                        }, 2000);
                    }
                })
                .catch(error => {
                    console.error(\'Error loading draft state:\', error);
                });
        }
        
        function updateCurrentPickInfo(team, round, pickNumber) {
            const pickText = document.getElementById(\'current-pick-text\');
            if (pickText && team) {
                pickText.textContent = `${team.team_name} - Round ${round}, Pick ${pickNumber}`;
                pickText.style.color = team.team_color;
            }
        }
        
        function updateTimer(seconds) {
            const timerDisplay = document.getElementById(\'timer-display\');
            if (timerDisplay) {
                timerDisplay.textContent = Math.max(0, seconds);
                
                // Change color based on time remaining
                if (seconds <= 10) {
                    timerDisplay.style.color = \'#ff0000\';
                } else if (seconds <= 20) {
                    timerDisplay.style.color = \'#ffaa00\';
                } else {
                    timerDisplay.style.color = \'white\';
                }
            }
        }
        
        function startTimer() {
            setInterval(function() {
                const timerDisplay = document.getElementById(\'timer-display\');
                if (timerDisplay) {
                    let currentTime = parseInt(timerDisplay.textContent);
                    if (currentTime > 0) {
                        updateTimer(currentTime - 1);
                    }
                }
            }, 1000);
        }
        
        function updateAvailablePlayers(players, currentTeamId) {
            const playersGrid = document.getElementById(\'available-players\');
            if (!playersGrid) return;
            
            let html = \'\';
            players.forEach(player => {
                const photoHtml = player.photo_path 
                    ? `<img src="${player.photo_path}" alt="${player.name}" class="draft-player-photo">`
                    : `<div class="draft-player-placeholder">📷</div>`;
                    
                html += `
                    <div class="draft-player-card" onclick="draftPlayer(${player.id})" data-player-id="${player.id}">
                        ${photoHtml}
                        <div style="color: var(--pure-white); font-weight: bold; margin-bottom: 5px;">
                            ${player.name}
                        </div>
                        ${player.nickname ? `<div style="color: var(--gold-accent); font-size: 12px; font-style: italic; margin-bottom: 5px;">"${player.nickname}"</div>` : \'\'}
                        <div style="color: var(--metallic-silver); font-size: 12px;">
                            ${player.position || \'Utility\'} • ${player.years_played} years
                        </div>
                    </div>
                `;
            });
            
            playersGrid.innerHTML = html;
            
            // Highlight cards if it\'s current team\'s turn
            if (currentTeamId) {
                const playerCards = playersGrid.querySelectorAll(\'.draft-player-card\');
                playerCards.forEach(card => {
                    card.classList.add(\'current-turn\');
                });
            }
        }
        
        function updateTeamRosters(teams, currentTeamId) {
            const teamRosters = document.getElementById(\'team-rosters\');
            if (!teamRosters) return;
            
            let html = \'\';
            teams.forEach(team => {
                const isCurrentPick = team.id === currentTeamId;
                html += `
                    <div class="team-roster-card ${isCurrentPick ? \'current-pick\' : \'\'}" style="border-color: ${team.team_color};">
                        <div class="team-roster-header">
                            <h4 style="color: ${team.team_color}; margin: 0;">${team.team_name}</h4>
                            <span style="color: var(--metallic-silver);">${team.players ? team.players.length : 0}/8 players</span>
                        </div>
                        <div class="team-roster-players">
                            ${team.players ? team.players.map(player => {
                                const isCaptain = player.id === team.captain_player_id;
                                const photoHtml = player.photo_path 
                                    ? `<img src="${player.photo_path}" alt="${player.name}">`
                                    : `<div class="roster-placeholder">📷</div>`;
                                return `
                                    <div class="roster-player-mini">
                                        ${photoHtml}
                                        <div style="color: var(--pure-white); font-size: 12px;">
                                            ${isCaptain ? \'👑 \' : \'\'}${player.name}
                                            ${player.nickname ? ` "${player.nickname}"` : \'\'}
                                        </div>
                                    </div>
                                `;
                            }).join(\'\') : \'<div style="color: var(--metallic-silver); font-style: italic; text-align: center; padding: 20px;">No players drafted yet</div>\'}
                        </div>
                    </div>
                `;
            });
            
            teamRosters.innerHTML = html;
        }
        
        function draftPlayer(playerId) {
            // Get current draft info from DOM
            const draftInterface = document.getElementById(\'draft-interface\');
            if (!draftInterface) return;
            
            // Make AJAX call to draft the player
            const formData = new FormData();
            formData.append(\'action\', \'draft_player\');
            formData.append(\'player_id\', playerId);
            
            fetch(window.location.href, {
                method: \'POST\',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Refresh the draft state
                    loadDraftState();
                    
                    // Show success message briefly
                    showDraftMessage(data.message, \'success\');
                } else {
                    showDraftMessage(data.message, \'error\');
                }
            })
            .catch(error => {
                console.error(\'Error drafting player:\', error);
                showDraftMessage(\'Error drafting player. Please try again.\', \'error\');
            });
        }
        
        function showDraftMessage(message, type) {
            // Create temporary message element
            const messageDiv = document.createElement(\'div\');
            messageDiv.style.cssText = `
                position: fixed;
                top: 20px;
                right: 20px;
                padding: 15px 25px;
                border-radius: 8px;
                color: white;
                font-weight: bold;
                z-index: 1000;
                background: ${type === \'success\' ? \'var(--success-green)\' : \'var(--alert-red)\'};
                box-shadow: 0 4px 8px rgba(0,0,0,0.3);
                transition: all 0.3s ease;
            `;
            messageDiv.textContent = message;
            
            document.body.appendChild(messageDiv);
            
            // Remove after 3 seconds
            setTimeout(() => {
                messageDiv.style.opacity = \'0\';
                messageDiv.style.transform = \'translateX(100px)\';
                setTimeout(() => messageDiv.remove(), 300);
            }, 3000);
        }
    </script>';
}
?>