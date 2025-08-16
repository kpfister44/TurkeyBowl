<?php
// Sample data setup script
$db = new SQLite3('turkeybowl.db');

// Add sample players
$samplePlayers = [
    ['John "The Cannon" Smith', 'The Cannon', 'QB', 'Veteran quarterback with a rocket arm and championship experience. Known for his clutch plays in the final minutes.', 5],
    ['Mike "Flash" Johnson', 'Flash', 'WR', 'Lightning-fast receiver who can turn any short pass into a big play. Holds the record for most touchdowns in a single game.', 3],
    ['Dave "Tank" Wilson', 'Tank', 'RB', 'Powerful runner who bulldozes through defenders. Never goes down on first contact and always fights for extra yards.', 4],
    ['Steve "Sticky Hands" Brown', 'Sticky Hands', 'WR', 'Reliable receiver who never drops a pass. The go-to target when you need a first down or touchdown.', 2],
    ['Tom "The Wall" Davis', 'The Wall', 'Defense', 'Shutdown defender who quarterbacks the defense. Known for his bone-crushing tackles and interception skills.', 6],
    ['Chris "Speedy" Martinez', 'Speedy', 'RB/WR', 'Versatile player who can line up anywhere. His speed and agility make him a nightmare matchup for any defense.', 1],
    ['Mark "Hands" Anderson', 'Hands', 'WR', 'Tall receiver with great hands and body control. Perfect for jump balls and end zone targets.', 3],
    ['Paul "Blitz" Thompson', 'Blitz', 'Defense', 'Aggressive pass rusher who gets to the quarterback faster than anyone. The heart and soul of any defense.', 4],
    ['Ryan "Rocket" Garcia', 'Rocket', 'WR', 'Deep threat who can outrun any defender. When you need a big play, throw it to Rocket.', 2],
    ['Alex "Clutch" Lee', 'Clutch', 'QB', 'Cool under pressure and always delivers when it matters most. Never met a fourth down he didn\'t like.', 5],
    ['Danny "Hammer" White', 'Hammer', 'Defense', 'Hard-hitting safety who brings the pain on every play. Receivers think twice before going over the middle.', 3],
    ['Kevin "Glue" Taylor', 'Glue', 'WR', 'Possession receiver who catches everything thrown his way. The most reliable target in short-yardage situations.', 2],
    ['Jake "Tornado" Jackson', 'Tornado', 'RB', 'Elusive runner who spins and jukes his way through entire defenses. Impossible to tackle in the open field.', 1],
    ['Sam "Iron" Miller', 'Iron', 'Defense', 'Tough-as-nails defender who never backs down. The anchor of any defensive line.', 4],
    ['Nick "Wheels" Jones', 'Wheels', 'WR/RB', 'Multi-position threat with breakaway speed. Can score from anywhere on the field at any time.', 2],
    ['Luke "Magnet" Clark', 'Magnet', 'WR', 'Seems to attract the ball wherever he goes. Makes impossible catches look routine.', 3],
    ['Ben "Bulldozer" Moore', 'Bulldozer', 'RB', 'Power runner who moves piles and fights for every yard. The closer to the goal line, the more dangerous he becomes.', 5],
    ['Josh "Hawk" Lewis', 'Hawk', 'Defense', 'Ball-hawking defender with incredible instincts. Always seems to be in the right place at the right time.', 3],
    ['Matt "Lightning" Hall', 'Lightning', 'WR', 'Quick receiver who runs precise routes. Perfect timing with his quarterbacks makes him unstoppable.', 1],
    ['Tyler "Fortress" Allen', 'Fortress', 'Defense', 'Immovable object in the middle of the field. Nothing gets past the Fortress.', 4],
    ['Scott "Jet" Young', 'Jet', 'WR/RB', 'Versatile speedster who can line up anywhere. When he touches the ball, magic happens.', 2],
    ['Brad "Steel" King', 'Steel', 'Defense', 'Tough defender who delivers crushing hits. Opposing players hear footsteps when Steel is around.', 6]
];

$stmt = $db->prepare('INSERT INTO players (name, nickname, position, bio, years_played, current_year) VALUES (?, ?, ?, ?, ?, 1)');
foreach ($samplePlayers as $player) {
    $stmt->bindValue(1, $player[0]);
    $stmt->bindValue(2, $player[1]);
    $stmt->bindValue(3, $player[2]);
    $stmt->bindValue(4, $player[3]);
    $stmt->bindValue(5, $player[4]);
    $stmt->execute();
}

// Add sample championship history
$championships = [
    [2023, 'Thunder Hawks'],
    [2022, 'Storm Crushers'],
    [2021, 'Lightning Bolts'],
    [2020, 'Tornado Force'],
    [2019, 'Thunder Hawks'],
    [2018, 'Storm Crushers']
];

$stmt = $db->prepare('INSERT INTO championships (year, team_name) VALUES (?, ?)');
foreach ($championships as $champ) {
    $stmt->bindValue(1, $champ[0]);
    $stmt->bindValue(2, $champ[1]);
    $stmt->execute();
}

// Add sample awards
$awards = [
    [2023, 'MVP', 'John "The Cannon" Smith'],
    [2023, 'Best QB', 'Alex "Clutch" Lee'],
    [2023, 'Trash Talk King', 'Danny "Hammer" White'],
    [2023, 'Most Touchdowns', 'Mike "Flash" Johnson'],
    [2022, 'MVP', 'Dave "Tank" Wilson'],
    [2022, 'Best Defense', 'Tom "The Wall" Davis'],
    [2022, 'Best Rookie', 'Chris "Speedy" Martinez'],
    [2021, 'MVP', 'Steve "Sticky Hands" Brown'],
    [2021, 'Best QB', 'John "The Cannon" Smith'],
    [2021, 'Most Interceptions', 'Paul "Blitz" Thompson']
];

$stmt = $db->prepare('INSERT INTO awards (year, award_name, player_name) VALUES (?, ?, ?)');
foreach ($awards as $award) {
    $stmt->bindValue(1, $award[0]);
    $stmt->bindValue(2, $award[1]);
    $stmt->bindValue(3, $award[2]);
    $stmt->execute();
}

// Add sample records
$records = [
    [2023, 'Most Touchdowns in a Game', '7', 'Mike "Flash" Johnson'],
    [2023, 'Longest Run', '85 yards', 'Jake "Tornado" Jackson'],
    [2022, 'Most Interceptions in a Game', '4', 'Tom "The Wall" Davis'],
    [2022, 'Most Receiving Yards', '312 yards', 'Ryan "Rocket" Garcia'],
    [2021, 'Most Passing Yards', '498 yards', 'Alex "Clutch" Lee'],
    [2021, 'Most Tackles', '18', 'Sam "Iron" Miller']
];

$stmt = $db->prepare('INSERT INTO records (year, record_name, record_value, player_name) VALUES (?, ?, ?, ?)');
foreach ($records as $record) {
    $stmt->bindValue(1, $record[0]);
    $stmt->bindValue(2, $record[1]);
    $stmt->bindValue(3, $record[2]);
    $stmt->bindValue(4, $record[3]);
    $stmt->execute();
}

echo "Sample data added successfully!\n";
echo "You can now run: php -S localhost:8000\n";
echo "Then visit: http://localhost:8000\n";
echo "Admin login: admin@turkeybowl.com / admin123\n";
?>