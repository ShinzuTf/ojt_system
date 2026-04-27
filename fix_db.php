<?php
$mysqli = new mysqli("127.0.0.1", "root", "", "ojt_system");

// Check if student_number column exists
$result = $mysqli->query("SHOW COLUMNS FROM users WHERE Field = 'student_number'");
if($result->num_rows == 0) {
    echo "Adding missing columns..." . PHP_EOL;
    $mysqli->query("ALTER TABLE users ADD COLUMN student_number VARCHAR(20) NULL UNIQUE AFTER email");
    $mysqli->query("ALTER TABLE users ADD COLUMN course VARCHAR(10) NULL AFTER student_number");
    $mysqli->query("ALTER TABLE users ADD COLUMN year_level TINYINT NULL AFTER course");
    $mysqli->query("ALTER TABLE users ADD COLUMN coordinator_id BIGINT UNSIGNED NULL AFTER year_level");
    echo "Columns added successfully!" . PHP_EOL;
} else {
    echo "Columns already exist." . PHP_EOL;
}

// Verify columns
$result = $mysqli->query("DESCRIBE users");
while($row = $result->fetch_assoc()) {
    echo $row["Field"] . " - " . $row["Type"] . PHP_EOL;
}
