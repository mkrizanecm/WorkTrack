<?php

session_start();

include "functions.php";

$connection = mysqli_connect("localhost", "root", "", "workmanagement");

$current_user = $_SESSION['user'];
$current_timestamp = time();
$ticket_id = $_GET['id'];
$active_ticket = GetActiveTicket($current_user);
$ticket_status = GetTicketStatus($ticket_id);

if ($ticket_status == 'Zavrseno') {
    header( "Refresh:1; url=finished_tickets.php" );
    die("Nije moguć rad na ovom nalogu!");
}

$check_ticket = CheckUserTicket($current_user, $ticket_id);

if ($active_ticket != NULL) {
    echo "Završite rad na trenutnom nalogu!";
    header( "Refresh:1; url=tickets.php" );
} elseif (empty($check_ticket)) {
    echo "Radni nalog ne glasi na ovog korisnika!";
    header( "Refresh:1; url=tickets.php" );
} else {
    $sql = "UPDATE users SET active_ticket_id = ?,time_started = ? WHERE id = '$current_user'";
    $statement = $connection->prepare($sql);
    $statement->bind_param('ii', $ticket_id, $current_timestamp);
    $statement->execute();

    header( "Location: tickets.php" );
}

?>