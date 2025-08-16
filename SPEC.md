TurkeyBowl is a simple web application that tracks information and history about an annual Turkey Bowl flag football event, including past champions, player stats, and the live draft of current-year teams.

Specifically it has the following features

* A public homepage that displays essential information about the upcoming Turkey Bowl, including:
    * Event date, time, and location
    * Registration deadline
    * Draft date and time
    * Countdown timer to game day
* A "Hall of Fame / History" section that shows:
    * A leaderboard of past championship teams by year
    * Individual player awards from past years (e.g. MVP, Best QB, Trash Talk King)
    * Fun records from past years (e.g. most touchdowns, most interceptions, longest run)
* A roster page with player profiles for all current-year participants (21–28 players)
    * Each profile shows a photo, nickname, number of years played, primary position, and short bio
* A "Teams" page that shows the current year's team lineups with team names and logos
* A draft page (admin-only access) that allows the admin to drag-and-drop player cards into team slots during the live draft
    * Admin can select the number of teams for this year (3–4)
    * As players are drafted, they are moved from the undrafted list to the team card
    * Drag-and-drop updates are instantly saved to the database
* After the draft is completed, team pages are viewable by any visitor and show:
    * Full player lineup
    * Team name and captain
    * Team photo (optional placeholder if not uploaded yet)

## Implementation details

* Admin users log in via email + password (simple login; no password reset/account confirmation flow)
* Public visitors do not need to log in to view historical data or current rosters
* Only the admin can access the draft drag-and-drop interface or change data
* Player profile photos must be uploaded image files (PNG or JPEG)
* Admin can edit player info (name, nickname, position, photo, bio) through a basic CRUD interface

## Technical details

* Use a single index.php script for the entire app
* SQLite for all database functionality
* No frameworks — just vanilla JavaScript and CSS (including drag-and-drop logic)
* No ORMs — use raw SQL
* Use a clean, minimalist, mobile-responsive design inspired by retro Madden Football Video Games (Madden 2003-2005 on the Xbox)
