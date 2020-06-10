<?php

session_start();

include "functions.php";

$connection = mysqli_connect("localhost", "root", "", "workmanagement");

$current_user = $_SESSION['user'];
$current_timestamp = time();
$current_date = date('Y-m-d');

$ticket_id = $_GET['id'];
$job_id = $_GET['job_id'];

$active_ticket = GetActiveTicket($current_user);
$check_ticket = CheckUserTicket($current_user, $ticket_id);

if (empty($check_ticket)) {
    die("Radni nalog nije na vama!");
    header( "Refresh:1; url=tickets.php");
}

if ($active_ticket != NULL) {
    $time_spent = 0;
    $ticket_timestamp = GetTicketStartTime($ticket_id, $current_user);

    if (!empty($ticket_timestamp) AND $ticket_timestamp != NULL) {
        $time_spent = $current_timestamp - $ticket_timestamp;
    }

    if (!empty($time_spent)) {
        $sql = "INSERT INTO task_hours(task_id,user_id,time_spent,date_created) VALUES (?,?,?,'$current_date')";
        $statement = $connection->prepare($sql);
        $statement->bind_param('iii', $ticket_id, $current_user, $time_spent);
        $statement->execute();

        $sql = "UPDATE users SET active_ticket_id = NULL,time_started = NULL WHERE id = '$current_user'";
        $statement = $connection->prepare($sql);
        $statement->execute();
    }
    header( "Refresh:1; url=tickets.php" );
} else {
    echo "Greška!";
    header( "Refresh:1; url=tickets.php" );
}

?>