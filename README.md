# TurkeyBowl - Annual Flag Football Championship

A retro Madden-style web application for managing an annual Turkey Bowl flag football event, including player drafts, team management, and historical records.

## Features

### Public Pages
- **Homepage**: Event information with live countdown timer
- **Hall of Fame**: Championship history, awards, and records
- **Roster**: Player profiles with photos, nicknames, and bios
- **Teams**: Current team lineups and rosters

### Admin Features
- **Live Draft Interface**: Drag-and-drop player drafting
- **Player Management**: CRUD operations for player data
- **Team Management**: Create and manage teams
- **History Management**: Add championships, awards, and records

## Technical Details

- **Single-file Architecture**: Everything in `index.php`
- **Database**: SQLite with raw SQL queries
- **Frontend**: Vanilla JavaScript and CSS (no frameworks)
- **Design**: Retro Madden NFL 2003-2005 Xbox aesthetic
- **Responsive**: Mobile-first design

## Quick Start

1. **Clone the repository**
   ```bash
   git clone <repository-url>
   cd TurkeyBowl
   ```

2. **Add sample data** (optional)
   ```bash
   php setup_sample_data.php
   ```

3. **Start the server**
   ```bash
   php -S localhost:8000
   ```

4. **Visit the website**
   - Public site: http://localhost:8000
   - Admin login: admin@turkeybowl.com / admin123

## Project Structure

```
TurkeyBowl/
├── index.php              # Main application file
├── setup_sample_data.php  # Sample data generator
├── SPEC.md                # Project specification
├── STYLE_GUIDE.md         # Design guidelines
├── CLAUDE.md              # Development notes
└── README.md              # This file
```

## Admin Access

Default admin credentials:
- **Email**: admin@turkeybowl.com
- **Password**: admin123

## Design Theme

Inspired by Madden NFL 2003-2005 on Xbox:
- Deep navy blue (#1a2332) and bright orange (#ff6600) color scheme
- Metallic silver accents with gradients
- Bold, chunky UI elements with 3D effects
- Sports-themed iconography and layouts

## Development

Built with Claude Code following these principles:
- Single-file simplicity
- No external dependencies
- Mobile-responsive design
- Retro gaming aesthetic
- Real-time functionality

## License

This project is open source and available under the MIT License.