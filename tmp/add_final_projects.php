<?php
require_once 'includes/config.php';

$teams = [1, 2, 3, 4];
foreach ($teams as $tid) {
    // 1. Ensure Workshop exists
    $checkWs = mysqli_query($connect, "SELECT workshop_id FROM academic_workshops WHERE workshop_name = 'Final Project' AND team_id = $tid");
    if (mysqli_num_rows($checkWs) == 0) {
        mysqli_query($connect, "INSERT INTO academic_workshops (workshop_name, team_id) VALUES ('Final Project', $tid)");
        $wsId = mysqli_insert_id($connect);
        echo "Created Final Project workshop for Team $tid (ID: $wsId)\n";
    } else {
        $row = mysqli_fetch_assoc($checkWs);
        $wsId = $row['workshop_id'];
        echo "Final Project workshop already exists for Team $tid (ID: $wsId)\n";
    }

    // 2. Ensure Task exists for this workshop
    $checkTask = mysqli_query($connect, "SELECT task_id FROM academic_tasks WHERE workshop_id = $wsId AND task_title = 'Final Project Submission'");
    if (mysqli_num_rows($checkTask) == 0) {
        $title = "Final Project Submission";
        $desc = "Submit your team's final project here. This task is for all members of the team. Only the leader can upload.";
        $deadline = date('Y-12-31 23:59:59'); // Far deadline for now
        
        $stmt = mysqli_prepare($connect, "INSERT INTO academic_tasks (task_title, task_description, workshop_id, deadline) VALUES (?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "ssis", $title, $desc, $wsId, $deadline);
        mysqli_stmt_execute($stmt);
        echo "Created Final Project Submission task for Workshop $wsId\n";
    } else {
        echo "Final Project Submission task already exists for Workshop $wsId\n";
    }
}
echo "\nSetup Complete.\n";
