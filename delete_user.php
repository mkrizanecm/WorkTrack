<?php

include "functions.php";

session_start();

$connection = mysqli_connect("localhost", "root", "", "workmanagement");

$current_date = date('Y-m-d');
$current_timestamp = time();
$current_user_privilege = $_SESSION['privilege'];
$user_id = $_GET['id'];
$user_privilege = GetUserPrivilege($user_id);

if ($current_user_privilege != 'superuser' OR $user_privilege == 'superuser') {
    echo "Nemate ovlasti za ovu akciju.";
    header( "Refresh:1; url=users.php" );
    exit;
}

$user = GetUser($user_id);

if ($user['is_active']) {

    $active_ticket = GetActiveTicket($user_id);

    if (!empty($active_ticket) AND $active_ticket != NULL) {
        $time_spent = 0;
        $ticket_timestamp = GetTicketStartTime($active_ticket, $user_id);

        if (!empty($ticket_timestamp) AND $ticket_timestamp != NULL) {
            $time_spent = $current_timestamp - $ticket_timestamp;
        }

        if (!empty($time_spent)) {
            $sql = "INSERT INTO task_hours(task_id,user_id,time_spent,date_created) VALUES (?,?,?,'$current_date')";
            $statement = $connection->prepare($sql);
            $statement->bind_param('iii', $active_ticket, $user_id, $time_spent);
            $statement->execute();
        }
    }

    $sql = "UPDATE user_tasks SET user_id = ? WHERE user_id = '$user_id'";
    $statement = $connection->prepare($sql);
    $statement->bind_param('i', $_SESSION['user']);
    $statement->execute();

    $sql = "UPDATE users SET is_active = false,active_ticket_id = NULL,time_started = NULL WHERE id = '$user_id'";
    $statement = $connection->prepare($sql);
    $statement->execute();

} else {
    $sql = "UPDATE users SET is_active = true WHERE id = '$user_id'";
    $statement = $connection->prepare($sql);
    $statement->execute();
}

header("Location: users.php");

?>