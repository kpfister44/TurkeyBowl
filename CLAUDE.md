# TurkeyBowl Project - Claude Code Assistant Guide

## Project Overview
TurkeyBowl is a simple web application for managing an annual flag football event, including player drafts, team management, and historical records.

## Architecture
- **Single-file application**: Everything in `index.php`
- **Database**: SQLite with raw SQL queries (no ORMs)
- **Frontend**: Vanilla JavaScript and CSS only (no frameworks)
- **Design**: Clean, minimalist, mobile-responsive inspired by retro Madden Football Video Games (Madden 2003-2005 on the Xbox)

## Key Features to Remember
1. **Public pages**: Homepage, Hall of Fame/History, Roster, Teams
2. **Admin-only**: Draft interface with drag-and-drop functionality
3. **Player management**: CRUD operations for player profiles with photo uploads
4. **Draft system**: Supports 3-4 teams, real-time updates to database

## Development Guidelines
- Keep all code in `index.php`
- Use SQLite for data persistence
- Implement drag-and-drop with vanilla JavaScript
- No external frameworks or libraries
- Mobile-first responsive design
- Simple admin authentication (email/password, no recovery flow)

## Testing
- Test drag-and-drop functionality thoroughly
- Verify mobile responsiveness
- Check admin authentication flow
- Validate image upload for player photos

## Common Commands
- **Run locally**: `php -S localhost:8000`
- **Database**: SQLite file will be created automatically

## Important Notes
- Public users can view all content except draft interface
- Only admin can modify data
- Player photos must be PNG or JPEG
- Team count is configurable (3-4 teams)
- Draft updates save instantly to database

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


