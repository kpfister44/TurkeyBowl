# TurkeyBowl Project - Claude Code Assistant Guide

## Project Overview
TurkeyBowl is a simple web application for managing an annual flag football event, including simple team management and historical records.

## Architecture
- **5-file modular application**: Organized for optimal Claude Code development
- **Database**: SQLite with raw SQL queries (no ORMs)
- **Frontend**: Vanilla JavaScript and CSS only (no frameworks)
- **Design**: Clean, minimalist, mobile-responsive inspired by retro Madden Football Video Games (Madden 2003-2005 on the Xbox)

### **File Organization**
1. **`index.php`** (67 lines) - Application entry point and orchestration
   - Session management and authentication checks
   - HTML document structure and navigation rendering
   - Includes all component files

2. **`database.php`** (134 lines) - Database layer and core functions
   - Database initialization and table creation
   - Helper functions (`isLoggedIn`, `requireAdmin`)
   - SQLite connection management

3. **`actions.php`** (626 lines) - Request handlers and form processing
   - All POST request handling (login, CRUD operations)
   - Simple team management APIs
   - Authentication and form validation

4. **`pages.php`** (896 lines) - Content generation and page rendering
   - All page rendering functions (Home, History, Roster, Teams, Login, Admin)
   - Admin interface tabs and forms
   - Public page content generation

5. **`assets.php`** (1,785 lines) - CSS styling and JavaScript functionality
   - Complete retro Madden theme styling
   - Interactive JavaScript (team management, admin forms)
   - Responsive design and animations

**Benefits for Claude Code:**
- Single-responsibility files for easier navigation
- Clear separation of concerns
- Reduced context switching
- Maintainable and focused components

## Database Schema

The application uses SQLite with the following tables:

### **admin_users**
```sql
CREATE TABLE admin_users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    email TEXT UNIQUE NOT NULL,
    password_hash TEXT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);
```

### **players**
```sql
CREATE TABLE players (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    nickname TEXT,
    position TEXT,
    bio TEXT,
    photo_path TEXT,
    years_played INTEGER DEFAULT 1,
    current_year BOOLEAN DEFAULT 1,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);
```

### **teams**
```sql
CREATE TABLE teams (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    captain_id INTEGER,
    logo_path TEXT,
    year INTEGER NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (captain_id) REFERENCES players (id)
);
```

### **team_players**
```sql
CREATE TABLE team_players (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    team_id INTEGER NOT NULL,
    player_id INTEGER NOT NULL,
    draft_order INTEGER,
    FOREIGN KEY (team_id) REFERENCES teams (id),
    FOREIGN KEY (player_id) REFERENCES players (id)
);
```

### **championships**
```sql
CREATE TABLE championships (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    year INTEGER NOT NULL,
    team_name TEXT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);
```

### **awards**
```sql
CREATE TABLE awards (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    year INTEGER NOT NULL,
    award_name TEXT NOT NULL,
    player_name TEXT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);
```

### **records**
```sql
CREATE TABLE records (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    year INTEGER NOT NULL,
    record_name TEXT NOT NULL,
    record_value TEXT NOT NULL,
    player_name TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);
```

### **event_settings** ⚠️ 
```sql
CREATE TABLE event_settings (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    event_date DATETIME,
    event_location TEXT,
    registration_deadline DATETIME,
    current_year INTEGER DEFAULT 2024,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
);
```
*Note: This table should contain exactly ONE record. See Database Management section for critical duplicate prevention patterns.*

## Key Features to Remember
1. **Public pages**: Homepage, Hall of Fame/History, Roster, Teams
2. **Admin-only**: Simple team management interface, Hall of Fame management
3. **Player management**: CRUD operations for player profiles with photo uploads
4. **Team management**: Simple team creation and player assignment (no draft system)
5. **Hall of Fame CRUD**: Complete admin interface for managing championships, awards, and records

## Development Guidelines
- **File structure**: Maintain 5-file modular organization (index.php, database.php, actions.php, pages.php, assets.php)
- **Database**: Use SQLite for data persistence with raw SQL queries
- **Interactions**: Simple team management with modal interfaces
- **Dependencies**: No external frameworks or libraries
- **Design**: Mobile-first responsive design with retro Madden styling
- **Authentication**: Simple admin authentication (email/password, no recovery flow)
- **Backup**: Original single-file version preserved as `index_backup.php`

## Testing
- Test team management functionality thoroughly
- Verify mobile responsiveness
- Check admin authentication flow
- Validate image upload for player photos
- Test Hall of Fame CRUD operations (add/edit/delete championships, awards, records)
- Verify retro Madden styling consistency across all pages

### **Next Manual Testing Required**
**Comprehensive admin CRUD testing needed for newly implemented features:**

#### **Event Settings Tab**
- [x] Update event date and verify it persists on page refresh
- [x] Update event location and confirm it displays on homepage
- [x] Update registration deadline and verify format/persistence
- [x] Update current year and verify it affects other pages
- [x] Test form validation with invalid dates/empty fields
- [x] Verify success/error messages display correctly

#### **Players Tab**
- [x] Add new player with all fields (name, nickname, position, bio, years, current year status)
- [x] Add new player with photo upload (PNG/JPEG validation)
- [x] Test photo upload with invalid formats (should reject)
- [x] Edit existing player and verify all fields update correctly
- [x] Edit player photo (should replace old photo file)
- [x] Delete player and verify photo file cleanup
- [x] Test player form validation (required fields, years 1-20)
- [x] Verify player data displays correctly on public Roster page
- [x] Test active/inactive player status functionality

#### **Teams Tab** ✅
- [x] Create new teams with captain selection
- [x] Add players to teams via side-by-side interface
- [x] Remove individual players from teams
- [x] Delete entire teams with confirmation
- [x] Verify team rosters display correctly on public Teams page
- [x] Test team management JavaScript functions
- [x] Verify proper error handling and success messaging
- [x] Fix tab switching JavaScript errors
- [x] Implement consistent admin styling for team management interface

#### **Integration Testing**
- [x] Verify event settings changes reflect on homepage countdown and info
- [x] Confirm player changes appear correctly on public roster page
- [x] Test admin tab navigation and state persistence
- [x] Verify mobile responsiveness of new admin forms and tables
- [x] Check that existing Hall of Fame tabs still work correctly
- [x] Confirm team management workflow functions end-to-end

## Common Commands
- **Run locally**: `php -S localhost:8000`
- **Database**: SQLite file will be created automatically

## Important Notes
- Public users can view all content except admin pages
- Only admin can modify data (admin@turkeybowl.com / admin123)
- Player photos must be PNG or JPEG
- Team count is unlimited (admin creates teams as needed)
- Team changes save instantly to database
- Hall of Fame data can be managed through admin interface with tabbed navigation
- Current branding: "EG TURKEY BOWL" (header) and "Turkey Bowl 2025" (homepage)

## Admin Interface Features
- **Event Settings Management**: Update event date, location, registration deadline, and current year
- **Player Management**: Complete CRUD for roster with photo uploads, positions, years played, and active status
- **Simple Team Management**: Create teams, assign captains, add/remove players via side-by-side interface
- **Team Operations**: Add Team form, side-by-side player management, individual player removal, team deletion
- **Hall of Fame Management**: Tabbed interface for championships, awards, and records
- **CRUD Operations**: Add, edit, delete functionality with expandable inline edit forms
- **Inline Edit Forms**: Modern UX with forms that slide down below table rows, replacing old prompt() dialogs
- **Form Validation**: Input validation and success/error messaging
- **Consistent Styling**: All admin tabs use matching card-based design with metallic effects
- **Retro Styling**: Maintains Madden 2003-2005 aesthetic with metallic effects throughout

## Style Guide Implementation
- **Navigation**: 3D metallic buttons with hover effects and orange active states
- **Cards**: Enhanced shadows, metallic borders, and gradient effects
- **Typography**: Bold headers with text shadows and proper contrast
- **Colors**: Navy blue (#1a2332), bright orange (#ff6600), metallic silver (#c0c0c0), gold (#ffd700)

## Code Documentation Guidelines

### **Comment Philosophy**
Documentation should capture **design decisions and intentions** at the time of creation, not just functionality. Comments should explain the "why" and "when," not just the "what."

### **Good vs Poor Comments**

**❌ Poor (describes functionality):**
```python
# This function splits the input data into two equally sized chunks, 
# multiplies each chunk with Y and then adds it together
def process_chunks(data, multiplier):
```

**✅ Good (explains design decision):**
```python
# The hardware X that this code runs on has a cache size of Y which 
# makes this split necessary for optimal compute throughput
def process_chunks(data, multiplier):
```

### **Three Types of Documentation (Airplane Metaphor)**
1. **800-page manual** - Comprehensive but overwhelming ("Congratulations on purchasing your 747!")
2. **10-page guide** - Practical how-to ("How to change the oil in the engine")  
3. **5-item checklist** - Critical emergency procedures ("How to deal with a fire in the engine")

**Use type 2 and 3 for code comments** - focus on practical understanding and critical design decisions.

### **What to Document**
- **Hardware constraints** that influenced implementation choices
- **Performance considerations** that drove specific algorithms
- **Historical context** for unusual patterns ("This works around API limitation X")
- **Future considerations** ("When new hardware Y is available, consider Z approach")
- **Critical failure modes** and why specific safeguards exist

### **What NOT to Document**
- Obvious functionality that code already expresses
- Implementation details that are self-evident
- Redundant descriptions of what the code does

Remember: Future engineers need to understand **why** code exists in its current form to make informed decisions about changes.

## Database Management Critical Notes

### **Event Settings Table Issue (RESOLVED)**
**Problem discovered:** The `event_settings` table was accumulating duplicate records on every app initialization due to `INSERT OR IGNORE` logic in `initDatabase()`.

**Root cause:** `INSERT OR IGNORE` only prevents insertion if there's a unique constraint violation, but our table had auto-increment ID as the primary key, so multiple records were being created with identical event data but different IDs.

**Solution implemented:**
1. **Initialization fix:** Check `COUNT(*)` before inserting default event settings
2. **Update cleanup:** Delete duplicate records keeping only the most recent one during updates
3. **Date format handling:** Convert HTML5 datetime-local format to SQLite format for proper storage

**Code pattern to avoid:**
```php
// BAD - Creates duplicates on every load
$stmt = $db->prepare('INSERT OR IGNORE INTO event_settings (...) VALUES (...)');
```

**Code pattern to use:**
```php
// GOOD - Only insert if none exist
$count = $db->query('SELECT COUNT(*) as count FROM event_settings')->fetchArray(SQLITE3_ASSOC);
if ($count['count'] == 0) {
    $stmt = $db->prepare('INSERT INTO event_settings (...) VALUES (...)');
}
```

**Future database debugging tips:**
- Always check for duplicate records when CRUD operations show success but don't persist
- Use `$db->changes()` to verify row updates
- Query the exact record being displayed vs. the record being updated
- Consider adding unique constraints on business logic fields to prevent duplicates

## Recent Architecture Changes (2025)

### **Draft System Removal & Simple Team Management (Latest)**
**Status:** ✅ Completed - PR #4 merged
**Date:** January 2025

**Major Changes:**
- **Draft system completely removed**: All draft-related actions, database tables, and UI components eliminated
- **Simple team management implemented**: Clean admin interface for creating teams and managing players
- **Database schema updated**: Removed `draft_date` field and `getNextTeamOrder()` function
- **JavaScript modernized**: Fixed syntax errors, added team management functions

**New Team Management Workflow:**
1. Admin creates teams manually with optional captain selection
2. Admin uses "Manage Players" modal to add players to teams
3. Individual players can be removed from teams
4. Entire teams can be deleted with confirmation
5. Public Teams page displays final rosters

**Technical Details:**
- **Backend actions**: `delete_team`, `add_player_to_team`, `remove_player_from_team`
- **Frontend functions**: `showAddTeamForm()`, `manageTeamPlayers()`, `removePlayerFromTeam()`
- **File changes**: 8 files modified, 7,441 insertions, 3,853 deletions
- **Code cleanup**: 616 lines of unused draft code removed

### **File Restructuring (Completed)**
**Previous:** Single 3,884-line `index.php` file  
**Current:** 5-file modular architecture optimized for Claude Code

**Migration Status:** ✅ Complete with 100% functionality preservation
- All team management features working
- All admin CRUD operations functional
- Public pages rendering correctly
- Authentication and form processing intact

### **Key Improvements**
- **Simple Workflow**: Removed complex draft logic in favor of direct team management
- **Error Handling**: Improved login error handling and form validation
- **Modal Interfaces**: Clean player selection and team management modals
- **Code Organization**: Single-responsibility files for better maintainability

### **Team Management Interface Styling Fix (Latest)**
**Status:** ✅ Completed - January 2025
**Date:** Recent

**Issues Fixed:**
- **JavaScript Syntax Errors**: Fixed escaped quotes in countdown function and missing catch block closures
- **Tab Switching Bug**: Resolved ReferenceError preventing admin tab navigation
- **CSS Syntax Issues**: Fixed font-family and content property declarations in PHP strings
- **Interface Consistency**: Updated team management styling to match other admin tabs

**Technical Changes:**
- Fixed JavaScript syntax in `assets.php` (lines 930, 1947)
- Removed redundant team display section for cleaner two-column layout
- Applied consistent `.card` and `.admin-table` styling to team management interface
- Standardized colors, shadows, and typography across all admin tabs

**Result:** Clean, functional team management interface with consistent admin styling

### **Files Backup**
- `index_backup.php` - Original single-file version with draft system (preserved for reference)
- All functionality migrated to modular structure without loss

## Commit Message Guidelines

Keep commit messages concise and to the point:

- Use imperative mood: "Add user authentication" not "Added user authentication"
- First line under 50 characters, capitalize first word
- Focus on what the change accomplishes, not how
- Use present tense: "Fix login bug" not "Fixed login bug"

**Examples:**
- `Add student profile creation form`
- `Fix course join code validation`
- `Update database schema for multi-course support`
- `Refactor authentication middleware`


