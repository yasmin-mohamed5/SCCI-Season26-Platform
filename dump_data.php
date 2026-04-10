<?php
require_once 'includes/config.php';
echo "--- Teams ---\n";
$res = mysqli_query($connect, "SELECT * FROM academic_teams");
while ($row = mysqli_fetch_assoc($res)) print_r($row);
echo "--- Workshops ---\n";
$res = mysqli_query($connect, "SELECT * FROM academic_workshops");
while ($row = mysqli_fetch_assoc($res)) print_r($row);
