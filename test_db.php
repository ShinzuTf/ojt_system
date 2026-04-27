<?php
$mysqli = new mysqli("127.0.0.1", "root", "", "ojt_system");
$result = $mysqli->query("DESCRIBE users");
while($row = $result->fetch_assoc()) {
    echo $row["Field"] . " - " . $row["Type"] . PHP_EOL;
}
