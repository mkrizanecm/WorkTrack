<?php

function validate($data) {

    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);

    return $data;
}

function login($username = "", $password = "") {

    $username = validate($username);
    $password = validate($password);

    $check_username = CheckUsername($username);
    $check_password = CheckPassword($password, $username);

    if (empty($username) AND empty($password)) {
        return "Unesite korisničko ime i lozinku!";
    } elseif (!empty($username) AND empty($password)) {
        return "Unesite lozinku!";
    } elseif (empty($username) AND !empty($password)) {
        return "Unesite korisničko ime!";
    } elseif (!$check_username) {
        return "Korisničko ime ne postoji.";
    } elseif (!$check_password AND !empty($username) AND !empty($password)) {
        return "Pogrešno korisničko ime ili lozinka.";
    } else {
        $user_data = GetUserData($username);
        return $user_data;
    }
}

function CheckPassword($password = "", $username = "") {

    $connection = mysqli_connect("localhost", "root", "", "workmanagement");
    mysqli_set_charset($connection,"utf8");

    $password = validate($password);

    $query = mysqli_query($connection
        ,"SELECT password FROM users WHERE username = '$username' LIMIT 1");

    $user_password = "";

    while($row = mysqli_fetch_assoc($query)) {
        $user_password = $row["password"];
        break;
    }

    if (!empty($user_password) AND password_verify($password, $user_password)) {
        return true;
    } else {
        return false;
    }

}

function CheckUsername($username = "") {

    $connection = mysqli_connect("localhost", "root", "", "workmanagement");
    mysqli_set_charset($connection,"utf8");

    $query = mysqli_query($connection
        ,"SELECT username FROM users WHERE username = '$username' LIMIT 1");

    $user = [];

    while($row = mysqli_fetch_assoc($query))
    {
        array_push($user, $row);
    }

    if (!empty($user)) {
        return true;
    } else {
        return false;
    }

}

function CheckEmail($email = "") {

    $connection = mysqli_connect("localhost", "root", "", "workmanagement");
    mysqli_set_charset($connection,"utf8");

    $query = mysqli_query($connection
        ,"SELECT email FROM users WHERE email = '$email' LIMIT 1");

    $usermail = [];

    while($row = mysqli_fetch_assoc($query))
    {
        array_push($usermail, $row);
    }

    if (!empty($usermail)) {
        return true;
    } else {
        return false;
    }

}

function CheckTicketActivity($ticket_id = 0) {

    $connection = mysqli_connect("localhost", "root", "", "workmanagement");
    mysqli_set_charset($connection,"utf8");

    $query = mysqli_query($connection
        ,"SELECT active_ticket_id,time_started,id FROM users WHERE active_ticket_id = '$ticket_id' LIMIT 1");

    $ticket = [];

    while($row = mysqli_fetch_assoc($query))
    {
        $ticket = $row;
        break;
    }

    if (!empty($ticket)) {
        return $ticket;
    } else {
        return false;
    }

}

function GetUsers($search_value = "", $page_result_number = 0, $results_per_page = 0) {

    $connection = mysqli_connect("localhost", "root", "", "workmanagement");
    mysqli_set_charset($connection,"utf8");

    if (!empty($search_value)) {
        $query = mysqli_query($connection
            ,"SELECT id,email,is_active,name,username,gender,date_of_birth,phone FROM users 
                    WHERE name LIKE '%".$search_value."%' OR username  LIKE '%".$search_value."%'");
    } else {
        if (empty($page_result_number) AND empty($results_per_page)) {
            $query = mysqli_query($connection
                ,"SELECT id,email,is_active,name,username,gender,date_of_birth,phone FROM users");
        } else {
            $query = mysqli_query($connection
                ,"SELECT id,email,is_active,name,username,gender,date_of_birth,phone FROM users LIMIT $page_result_number,$results_per_page");
        }
    }

    $users = [];

    while($row = mysqli_fetch_assoc($query))
    {
        array_push($users, $row);
    }

    if (count($users)) {
        return $users;
    } else {
        return false;
    }

}

function GetUserData($username = "") {

    $connection = mysqli_connect("localhost", "root", "", "workmanagement");
    mysqli_set_charset($connection,"utf8");

    $query = mysqli_query($connection
        ,"SELECT usr.id,upr.name AS privilege,usr.name AS full_name,usr.is_active
                FROM users usr LEFT JOIN user_privileges upr
                ON upr.id = usr.privilege_id 
                WHERE username = '$username'");

    $user = [];

    while($row = mysqli_fetch_assoc($query))
    {
        array_push($user, $row);
    }

    if (count($user)) {
        return $user;
    } else {
        return false;
    }
}

function GetUser($id = 0){

    $connection = mysqli_connect("localhost", "root", "", "workmanagement");
    mysqli_set_charset($connection,"utf8");

    $query = mysqli_query($connection
        ,"SELECT upr.id AS privilege,usr.name AS full_name,
                usr.username,usr.email,usr.gender,usr.phone,usr.date_of_birth,usr.is_active
                FROM users usr LEFT JOIN user_privileges upr
                ON upr.id = usr.privilege_id 
                WHERE usr.id = '$id'");

    $user = [];

    while($row = mysqli_fetch_assoc($query))
    {
        $user = $row;
        break;
    }

    if (!empty($user)) {
        return $user;
    } else {
        return false;
    }

}

function GetJobStatuses() {

    $connection = mysqli_connect("localhost", "root", "", "workmanagement");
    mysqli_set_charset($connection,"utf8");

    $query = mysqli_query($connection
        ,"SELECT * FROM statuses");

    $statuses = [];

    while($row = mysqli_fetch_assoc($query))
    {
        array_push($statuses, $row);
    }

    return $statuses;

}

function GetPrivileges() {

    $connection = mysqli_connect("localhost", "root", "", "workmanagement");
    mysqli_set_charset($connection,"utf8");

    $query = mysqli_query($connection
        ,"SELECT * FROM user_privileges");

    $privileges = [];

    while($row = mysqli_fetch_assoc($query))
    {
        array_push($privileges, $row);
    }

    return $privileges;

}

function GetUserPrivilege($user_id = 0) {

    $connection = mysqli_connect("localhost", "root", "", "workmanagement");
    mysqli_set_charset($connection,"utf8");

    $query = mysqli_query($connection
        ,"SELECT upr.name AS privilege FROM user_privileges upr LEFT JOIN users usr ON usr.privilege_id = upr.id WHERE usr.id = '$user_id'");

    $privilege = "";

    while($row = mysqli_fetch_assoc($query))
    {
        $privilege = $row['privilege'];
        break;
    }

    return $privilege;
}

function GetJobs($search_value = "", $page_result_number = 0, $results_per_page = 0) {

    $connection = mysqli_connect("localhost", "root", "", "workmanagement");
    mysqli_set_charset($connection,"utf8");

    if (!empty($search_value)) {
        $query = mysqli_query($connection
            ,"SELECT * FROM jobs WHERE jobs.name LIKE '%".$search_value."%' ORDER BY date_created");
    } else {
        if (empty($page_result_number) AND empty($results_per_page)) {
            $query = mysqli_query($connection
                ,"SELECT * FROM jobs ORDER BY date_created");
        } else {
            $query = mysqli_query($connection
                ,"SELECT * FROM jobs ORDER BY date_created LIMIT $page_result_number,$results_per_page");
        }
    }

    $jobs = [];

    while($row = mysqli_fetch_assoc($query))
    {
        array_push($jobs, $row);
    }

    return $jobs;

}

function GetJob($job_id = 0) {

    $connection = mysqli_connect("localhost", "root", "", "workmanagement");
    mysqli_set_charset($connection,"utf8");

    $query = mysqli_query($connection
        ,"SELECT * FROM jobs WHERE id = '$job_id'");

    $job = [];

    while($row = mysqli_fetch_assoc($query))
    {
        $job = $row;
        break;
    }

    return $job;
}

function GetJobHours($job_id = 0) {

    $connection = mysqli_connect("localhost", "root", "", "workmanagement");
    mysqli_set_charset($connection,"utf8");

    $query = mysqli_query($connection
        ,"SELECT tsh.time_spent FROM task_hours tsh 
                LEFT JOIN tasks tsk 
                ON tsk.id = tsh.task_id WHERE tsk.job_id = '$job_id'");

    $time = [];
    $time_spent = 0;

    while($row = mysqli_fetch_assoc($query))
    {
        array_push($time, $row);
    }

    if (!empty($time)) {
        foreach ($time as $t) {
           $time_spent += $t['time_spent'];
        }
    }

    if (!empty($time_spent) AND 1 < $time_spent) {
        $hours = floor($time_spent / 3600);
        $minutes = floor(($time_spent / 60) % 60);
        $seconds = $time_spent % 60;

        return $hours. ':' . $minutes . ':' . $seconds;
    } else {
        return 0;
    }

}

function GetJobTickets($user_id = 0, $job_id = 0) {

    $connection = mysqli_connect("localhost", "root", "", "workmanagement");
    mysqli_set_charset($connection,"utf8");

    $query = mysqli_query($connection
        ,"SELECT sts.name AS status,tas.id AS task_id,tas.name AS task,job.name AS job,tas.date_due,job.description
                FROM tasks tas LEFT JOIN jobs job 
                ON job.id = tas.job_id LEFT JOIN statuses sts ON sts.id = tas.status_id
                LEFT JOIN user_tasks ust ON ust.task_id = tas.id
                WHERE ust.user_id = '$user_id' AND job.id = '$job_id' ORDER BY tas.date_due ASC");

    $jobs = [];

    while($row = mysqli_fetch_assoc($query))
    {
        array_push($jobs, $row);
    }

    return $jobs;
}

function GetTickets($user_id = 0, $search_value = "", $page_result_number = 0, $results_per_page = 0) {

    $connection = mysqli_connect("localhost", "root", "", "workmanagement");
    mysqli_set_charset($connection,"utf8");

    if (!empty($search_value)) {
        $query = mysqli_query($connection
            ,"SELECT sts.name AS status,tas.id,tas.name AS task,job.name AS job,tas.date_due,job.id AS job_id
                FROM tasks tas LEFT JOIN jobs job 
                ON job.id = tas.job_id LEFT JOIN user_tasks ust ON ust.task_id = tas.id
                LEFT JOIN statuses sts ON sts.id = tas.status_id
                WHERE ust.user_id = '$user_id' AND tas.name LIKE '%".$search_value."%'
                ORDER BY tas.date_due ASC");
    } else {
        if (empty($page_result_number) AND empty($results_per_page)) {
            $query = mysqli_query($connection
                ,"SELECT sts.name AS status,tas.id,tas.name AS task,job.name AS job,tas.date_due,job.id AS job_id
                FROM tasks tas LEFT JOIN jobs job 
                ON job.id = tas.job_id LEFT JOIN user_tasks ust ON ust.task_id = tas.id
                LEFT JOIN statuses sts ON sts.id = tas.status_id 
                WHERE ust.user_id = '$user_id' ORDER BY tas.date_due ASC");
        } else {
            $query = mysqli_query($connection
                ,"SELECT sts.name AS status,tas.id,tas.name AS task,job.name AS job,tas.date_due,job.id AS job_id
                FROM tasks tas LEFT JOIN jobs job 
                ON job.id = tas.job_id LEFT JOIN user_tasks ust ON ust.task_id = tas.id
                LEFT JOIN statuses sts ON sts.id = tas.status_id 
                WHERE ust.user_id = '$user_id' ORDER BY tas.date_due ASC LIMIT $page_result_number,$results_per_page");
        }
    }

    $tickets = [];

    while($row = mysqli_fetch_assoc($query))
    {
        array_push($tickets, $row);
    }

    return $tickets;
}

function GetAllTickets($user_privilege = '', $search_value = "", $page_result_number = 0, $results_per_page = 0) {

    if ($user_privilege != 'superuser') {
        header( "Refresh:1; url=tickets.php" );
        die("Nemate pristup!");
    }

    $connection = mysqli_connect("localhost", "root", "", "workmanagement");
    mysqli_set_charset($connection,"utf8");

    if (!empty($search_value)) {
        $query = mysqli_query($connection
            ,"SELECT sts.name AS status,tas.id,tas.name AS task,job.name AS job,tas.date_due,job.id AS job_id
                FROM tasks tas LEFT JOIN jobs job 
                ON job.id = tas.job_id LEFT JOIN statuses sts ON sts.id = tas.status_id
                LEFT JOIN user_tasks ust ON ust.task_id = tas.id 
                WHERE tas.name LIKE '%".$search_value."%'
                ORDER BY tas.date_due ASC");
    } else {
        if (empty($page_result_number) AND empty($results_per_page)) {
            $query = mysqli_query($connection
                ,"SELECT sts.name AS status,tas.id,tas.name AS task,job.name AS job,tas.date_due,job.id AS job_id
                FROM tasks tas LEFT JOIN jobs job 
                ON job.id = tas.job_id LEFT JOIN statuses sts ON sts.id = tas.status_id
                LEFT JOIN user_tasks ust ON ust.task_id = tas.id
                ORDER BY tas.date_due ASC");
        } else {
            $query = mysqli_query($connection
                ,"SELECT sts.name AS status,tas.id,tas.name AS task,job.name AS job,tas.date_due,job.id AS job_id
                FROM tasks tas LEFT JOIN jobs job 
                ON job.id = tas.job_id LEFT JOIN statuses sts ON sts.id = tas.status_id
                LEFT JOIN user_tasks ust ON ust.task_id = tas.id
                ORDER BY tas.date_due ASC LIMIT $page_result_number,$results_per_page");
        }
    }

    $tickets = [];

    while($row = mysqli_fetch_assoc($query))
    {
        array_push($tickets, $row);
    }

    return $tickets;

}

function GetFinishedTickets($user_privilege = '', $search_value = "", $page_results_number = 0, $results_per_page = 0) {

    if ($user_privilege != 'superuser') {
        header( "Refresh:1; url=tickets.php" );
        die("Nemate pristup!");
    }

    $connection = mysqli_connect("localhost", "root", "", "workmanagement");
    mysqli_set_charset($connection,"utf8");

    if (!empty($search_value)) {
        $query = mysqli_query($connection
            ,"SELECT sts.name AS status,tas.id,tas.name AS task,job.name AS job,tas.date_finished,job.id AS job_id
                FROM tasks tas LEFT JOIN jobs job 
                ON job.id = tas.job_id LEFT JOIN user_tasks ust ON ust.task_id = tas.id
                LEFT JOIN statuses sts ON sts.id = tas.status_id
                WHERE sts.name = 'Zavrseno' AND tas.name LIKE '%".$search_value."%' ORDER BY tas.date_finished ASC");
    } else {
        if (empty($page_results_number) AND empty($results_per_page)) {
            $query = mysqli_query($connection
                ,"SELECT sts.name AS status,tas.id,tas.name AS task,job.name AS job,tas.date_finished,job.id AS job_id
                FROM tasks tas LEFT JOIN jobs job 
                ON job.id = tas.job_id LEFT JOIN user_tasks ust ON ust.task_id = tas.id
                LEFT JOIN statuses sts ON sts.id = tas.status_id
                WHERE sts.name = 'Zavrseno' ORDER BY tas.date_finished ASC");
        } else {
            $query = mysqli_query($connection
                ,"SELECT sts.name AS status,tas.id,tas.name AS task,job.name AS job,tas.date_finished,job.id AS job_id
                FROM tasks tas LEFT JOIN jobs job 
                ON job.id = tas.job_id LEFT JOIN user_tasks ust ON ust.task_id = tas.id
                LEFT JOIN statuses sts ON sts.id = tas.status_id
                WHERE sts.name = 'Zavrseno' ORDER BY tas.date_finished ASC LIMIT $page_results_number,$results_per_page");
        }
    }

    $tickets = [];

    while($row = mysqli_fetch_assoc($query))
    {
        array_push($tickets, $row);
    }

    return $tickets;
}

function GetTicket($ticket_id = 0) {

    $connection = mysqli_connect("localhost", "root", "", "workmanagement");
    mysqli_set_charset($connection,"utf8");

    $query = mysqli_query($connection
        ,"SELECT * FROM tasks WHERE id = '$ticket_id'");

    $ticket = [];

    while($row = mysqli_fetch_assoc($query))
    {
        $ticket = $row;
        break;
    }

    return $ticket;

}

function GetTicketHours($ticket_id = 0) {

    $connection = mysqli_connect("localhost", "root", "", "workmanagement");
    mysqli_set_charset($connection,"utf8");

    $query = mysqli_query($connection
        ,"SELECT time_spent FROM task_hours WHERE task_id = '$ticket_id'");

    $time = [];

    while($row = mysqli_fetch_assoc($query))
    {
        array_push($time, $row);
    }

    if (!empty($time)) {
        $time_spent = 0;
        foreach ($time as $t) {
            $time_spent += $t['time_spent'];
        }
    }

    if (!empty($time_spent) AND 1 < $time_spent) {
        $hours = floor($time_spent / 3600);
        $minutes = floor(($time_spent / 60) % 60);
        $seconds = $time_spent % 60;

        return $hours. ':' . $minutes . ':' . $seconds;
    } else {
        return 0;
    }

}

function GetTicketNotes($ticket_id = 0) {

    $connection = mysqli_connect("localhost", "root", "", "workmanagement");
    mysqli_set_charset($connection,"utf8");

    $query = mysqli_query($connection
        ,"SELECT usr.username,tsn.note,tsn.date_created FROM task_notes tsn 
                LEFT JOIN users usr ON usr.id = tsn.user_id
                WHERE tsn.task_id = '$ticket_id' ORDER BY date_created ASC");

    $notes = [];

    while($row = mysqli_fetch_assoc($query))
    {
        array_push($notes, $row);
    }

    return $notes;

}

function GetActiveTicket($user_id = 0) {

    $connection = mysqli_connect("localhost", "root", "", "workmanagement");
    mysqli_set_charset($connection,"utf8");

    $query = mysqli_query($connection
        ,"SELECT usr.active_ticket_id,usr.time_started,tas.name AS task
                FROM users usr
                LEFT JOIN tasks tas ON tas.id = usr.active_ticket_id
                WHERE usr.id = '$user_id'");

    $active_ticket = [];

    while($row = mysqli_fetch_assoc($query))
    {
        $active_ticket = $row['active_ticket_id'];
        break;
    }

    return $active_ticket;

}

function GetCurrentActiveTicket($user_id = 0) {

    $connection = mysqli_connect("localhost", "root", "", "workmanagement");
    mysqli_set_charset($connection,"utf8");

    $query = mysqli_query($connection
        ,"SELECT usr.active_ticket_id,usr.time_started,tas.id AS task
                FROM users usr
                LEFT JOIN tasks tas ON tas.id = usr.active_ticket_id
                WHERE usr.id = '$user_id'");

    $current_ticket = [];

    while($row = mysqli_fetch_assoc($query))
    {
        $current_ticket = $row['task'];
        break;
    }

    return $current_ticket;
}

function GetExpiredTickets($privilege = "", $user_id = 0) {

    $connection = mysqli_connect("localhost", "root", "", "workmanagement");
    mysqli_set_charset($connection,"utf8");

    $current_date = date('Y-m-d');

    if ($privilege == 'superuser') {
        $query = mysqli_query($connection
            ,"SELECT tsk.name,tsk.date_due FROM tasks tsk 
                LEFT JOIN user_tasks ust ON ust.task_id = tsk.id 
                LEFT JOIN statuses sts ON sts.id = tsk.status_id
                WHERE tsk.date_due < '$current_date'
                AND sts.name NOT LIKE 'Zavrseno'");

    } else {
        $query = mysqli_query($connection
            ,"SELECT tsk.name,tsk.date_due FROM tasks tsk 
                LEFT JOIN user_tasks ust ON ust.task_id = tsk.id 
                LEFT JOIN statuses sts ON sts.id = tsk.status_id
                WHERE ust.user_id = '$user_id' AND tsk.date_due < '$current_date'
                AND sts.name NOT LIKE 'Zavrseno'");

    }

    $expired_tickets = [];

    while($row = mysqli_fetch_assoc($query))
    {
        array_push($expired_tickets, $row);
    }

    return $expired_tickets;

}

function GetTicketStartTime($ticket_id = 0, $user_id = 0) {

    $connection = mysqli_connect("localhost", "root", "", "workmanagement");
    mysqli_set_charset($connection,"utf8");

    $query = mysqli_query($connection
        ,"SELECT time_started FROM users WHERE active_ticket_id = '$ticket_id' AND id = '$user_id' ");

    $timestamp = [];

    while($row = mysqli_fetch_assoc($query))
    {
        $timestamp = $row['time_started'];
        break;
    }

    return $timestamp;

}

function GetTicketUser($ticket_id = 0) {

    $connection = mysqli_connect("localhost", "root", "", "workmanagement");
    mysqli_set_charset($connection,"utf8");

    $query = mysqli_query($connection
        ,"SELECT user_id FROM user_tasks WHERE task_id = '$ticket_id'");

    $user = [];

    while($row = mysqli_fetch_assoc($query))
    {
        $user = $row['user_id'];
        break;
    }

    return $user;
}

function GetTicketStatus($ticket_id = 0) {

    $connection = mysqli_connect("localhost", "root", "", "workmanagement");
    mysqli_set_charset($connection,"utf8");

    $query = mysqli_query($connection
        ,"SELECT sts.name AS status FROM statuses sts 
                LEFT JOIN tasks tsk ON tsk.status_id = sts.id 
                WHERE tsk.id = '$ticket_id'");

    $status = '';

    while($row = mysqli_fetch_assoc($query))
    {
        $status = $row['status'];
        break;
    }

    return $status;

}

function GetStatus($status_id = 0) {

    $connection = mysqli_connect("localhost", "root", "", "workmanagement");
    mysqli_set_charset($connection,"utf8");

    $query = mysqli_query($connection
        ,"SELECT name AS status FROM statuses WHERE id = '$status_id'");

    $status = '';

    while($row = mysqli_fetch_assoc($query))
    {
        $status = $row['status'];
        break;
    }

    return $status;
}

function CheckUserTicket($user_id = 0, $ticket_id  = 0) {

    $connection = mysqli_connect("localhost", "root", "", "workmanagement");
    mysqli_set_charset($connection,"utf8");

    $query = mysqli_query($connection
        ,"SELECT * FROM user_tasks WHERE user_id = '$user_id' AND task_id = '$ticket_id'");

    $user_ticket = [];

    while($row = mysqli_fetch_assoc($query))
    {
        $user_ticket = $row;
        break;
    }

    return $user_ticket;

}

function CreateJob($name = "", $note = "", $status_id = 0) {

    $connection = mysqli_connect("localhost", "root", "", "workmanagement");
    mysqli_set_charset($connection,"utf8");

    $name = validate($name);
    $note = validate($note);
    $current_date = date('Y-m-d');

    if (empty($name) AND empty($note)) {
        return "Ispunite sva polja!";
    }
    if (empty($note)) {
        return "Unesite opis posla!";
    }
    if (empty($name)) {
        return "Unesite ime posla!";
    }

    $sql = "INSERT INTO jobs(status_id,name,description,date_created) VALUES (?,?,?,'$current_date')";
    $connection->set_charset("utf8mb4");
    $statement = $connection->prepare($sql);
    $statement->bind_param('iss', $status_id, $name, $note);
    $statement->execute();

    return 1;
}

function EditJob($job_id = 0, $name = "", $note = "", $status_id = 0) {

    $connection = mysqli_connect("localhost", "root", "", "workmanagement");
    mysqli_set_charset($connection,"utf8");

    $name = validate($name);
    $note = validate($note);
    $current_date = date('Y-m-d');

    if (empty($name) AND empty($note)) {
        return "Ispunite sva polja!";
    }
    if (empty($note)) {
        return "Unesite opis posla!";
    }
    if (empty($name)) {
        return "Unesite ime posla!";
    }

    $sql = "UPDATE jobs SET name = ?, description = ?, status_id = ?, date_updated = '$current_date' WHERE id = '$job_id'";
    $connection->set_charset("utf8mb4");
    $statement = $connection->prepare($sql);
    $statement->bind_param('ssi', $name, $note, $status_id);
    $statement->execute();

    return 1;

}

function CreateTicket($name = "", $note = "", $user_id = 0, $job_id = 0, $status_id = 0, $date_due = 0) {

    $connection = mysqli_connect("localhost", "root", "", "workmanagement");
    mysqli_set_charset($connection,"utf8");

    $name = validate($name);
    $note = validate($note);
    $current_date = date('Y-m-d');

    $date_due = strtotime($date_due);
    $date_due = date('Y-m-d', $date_due);

    if (empty($name) AND empty($note) AND empty($date_due) OR (empty($name) AND empty($note)) OR (empty($date_due) AND empty($note)) OR (empty($date_due) AND empty($name))) {
        return "Ispunite sva polja!";
    }
    if (empty($note)) {
        return "Unesite opis radnog naloga!";
    }
    if (empty($name)) {
        return "Unesite naziv naloga!";
    }
    if (strtotime($date_due) <= strtotime($current_date) OR empty($date_due)) {
        return "Datum nije valjan!";
    }

    $sql = "INSERT INTO tasks(job_id,status_id,name,description,date_created,date_due) VALUES (?,?,?,?,'$current_date','$date_due')";
    $connection->set_charset("utf8mb4");
    $statement = $connection->prepare($sql);
    $statement->bind_param('iiss', $job_id,  $status_id, $name, $note);
    $statement->execute();

    $task_id = mysqli_insert_id($connection);

    $sql = "INSERT INTO user_tasks(user_id,task_id) VALUES (?,?)";
    $connection->set_charset("utf8mb4");
    $statement = $connection->prepare($sql);
    $statement->bind_param('ii', $user_id, $task_id);
    $statement->execute();

    return 1;
}

function EditTicket ($ticket_id = 0, $name = "", $note = "", $job_id = 0, $date_due = "") {

    $connection = mysqli_connect("localhost", "root", "", "workmanagement");
    mysqli_set_charset($connection,"utf8");

    $name = validate($name);
    $note = validate($note);
    $current_date = date('Y-m-d');

    $date_due = strtotime($date_due);
    $date_due = date('Y-m-d', $date_due);

    if (empty($name) AND empty($note) AND empty($date_due) OR (empty($name) AND empty($note)) OR (empty($date_due) AND empty($note)) OR (empty($date_due) AND empty($name))) {
        return "Ispunite sva polja!";
    }
    if (empty($note)) {
        return "Unesite opis radnog naloga!";
    }
    if (empty($name)) {
        return "Unesite naziv naloga!";
    }
    if (strtotime($date_due) <= strtotime($current_date) OR empty($date_due)) {
        return "Datum nije valjan!";
    }

    $sql = "UPDATE tasks SET name = ?, description = ?, job_id = ?, date_due = '$date_due' WHERE id = '$ticket_id'";
    $connection->set_charset("utf8mb4");
    $statement = $connection->prepare($sql);
    $statement->bind_param('ssi', $name,  $note, $job_id);
    $statement->execute();

    return 1;
}

function CreateTicketNote($user_id = 0, $ticket_id = 0, $note = "") {

    $connection = mysqli_connect("localhost", "root", "", "workmanagement");
    mysqli_set_charset($connection,"utf8");

    $note = validate($note);
    $current_date = date('Y-m-d');

    if (empty($note)) {
        return "Bilješka je prazna!";
    }

    $sql = "INSERT INTO task_notes(task_id,user_id,note,date_created) VALUES (?,?,?,'$current_date')";
    $connection->set_charset("utf8mb4");
    $statement = $connection->prepare($sql);
    $statement->bind_param('iis', $ticket_id, $user_id, $note);
    $statement->execute();

    sleep(1);

    return "Uspješan unos!";
}

function UpdateTicketStatus($status_id = 0, $ticket_id = 0){

    $connection = mysqli_connect("localhost", "root", "", "workmanagement");
    mysqli_set_charset($connection,"utf8");

    $status = GetStatus($status_id);

    if ($status == 'Zavrseno') {
        $current_date = date('Y-m-d');
        $check_ticket = CheckTicketActivity($ticket_id);
        if ($check_ticket == false) {
            $sql = "UPDATE tasks SET status_id = ?,date_finished = '$current_date' WHERE id = '$ticket_id'";
        } else {
            return "Završite rad!";
        }
    } else {
        $sql = "UPDATE tasks SET status_id = ? WHERE id = '$ticket_id'";

    }
    $connection->set_charset("utf8mb4");
    $statement = $connection->prepare($sql);
    $statement->bind_param('i', $status_id);
    $statement->execute();

    return "Uspješna promjena statusa!";
}

function ChangeTicketUser($current_user_privilege = '', $ticket_id = 0, $user_id = 0) {

    $connection = mysqli_connect("localhost", "root", "", "workmanagement");
    mysqli_set_charset($connection,"utf8");

    $check_ticket = CheckTicketActivity($ticket_id);

    if (!empty($check_ticket) AND $current_user_privilege != 'superuser') {
        return "Završite rad!";
    } else {
        if (!empty($check_ticket) AND $current_user_privilege == 'superuser') {
            $current_date = date('Y-m-d');
            $current_timestamp = time();
            $time_spent = 0;
            if (!empty($check_ticket['time_started']) AND $check_ticket['time_started'] != NULL) {
                $time_spent = $current_timestamp - $check_ticket['time_started'];
            }

            if (!empty($time_spent)) {
                $sql = "INSERT INTO task_hours(task_id,user_id,time_spent,date_created) VALUES (?,?,?,'$current_date')";
                $statement = $connection->prepare($sql);
                $statement->bind_param('iii', $ticket_id, $check_ticket['id'], $time_spent);
                $statement->execute();
            }

            $active_ticket_user = $check_ticket['id'];
            $sql = "UPDATE users SET active_ticket_id = NULL,time_started = NULL WHERE id = '$active_ticket_user'";
            $statement = $connection->prepare($sql);
            $statement->execute();

            $ticket_user = GetTicketUser($ticket_id);

            $sql = "UPDATE user_tasks SET user_id = ?
            WHERE user_id = '$ticket_user' AND task_id = '$ticket_id'";
            $connection->set_charset("utf8mb4");
            $statement = $connection->prepare($sql);
            $statement->bind_param('i', $user_id);
            $statement->execute();

            header('Location: tickets.php');
        } else {
            $ticket_user = GetTicketUser($ticket_id);

            $sql = "UPDATE user_tasks SET user_id = ?
            WHERE user_id = '$ticket_user' AND task_id = '$ticket_id'";
            $connection->set_charset("utf8mb4");
            $statement = $connection->prepare($sql);
            $statement->bind_param('i', $user_id);
            $statement->execute();

            header('Location: tickets.php');
        }
    }
}

function CreateUser($name = "", $gender = "", $phone = "", $username = "", $privilege_id = 0, $email = "", $password = "", $date_of_birth = 0) {

    $connection = mysqli_connect("localhost", "root", "", "workmanagement");
    mysqli_set_charset($connection,"utf8");

    $name = validate($name);
    $username = validate($username);
    $email = validate($email);
    $password = validate($password);
    $current_date = date('Y-m-d');

    $date_of_birth = strtotime($date_of_birth);
    $date_of_birth = date('Y-m-d', $date_of_birth);

    $fields = [
        'name' => $name,
        'gender' => $gender,
        'phone' => $phone,
        'username' => $username,
        'privilege_id' => $privilege_id,
        'email' => $email,
        'password' => $password,
        'date_of_birth' => $date_of_birth,
    ];

    $existing_username = CheckUsername($username);
    $existing_email = CheckEmail($email);

    $i = 0;
    foreach ($fields as $field) {
        if (empty($field)) {
            $i++;
        }
    }

    if ($i >= 2) {
        return "Popunite sva polja!";
    }
    if (empty($name)) {
        return "Unesite ime i prezime!";
    }
    if (empty($phone)) {
        return "Unesite broj telefona!";
    }
    if (empty($username)) {
        return "Unesite korisničko ime!";
    }
    if (empty($email)) {
        return "Unesite e-mail adresu!";
    }
    if (empty($password)) {
        return "Unesite lozinku!";
    }
    if (empty($date_of_birth)) {
        return "Unesite datum rođenja!";
    }
    if ($existing_username == TRUE) {
        return "Korisničko ime već postoji!";
    }
    if ($existing_email == TRUE) {
        return "E-mail već postoji!";
    }
    if (!ctype_alnum($username) OR strlen($username) > 15) {
        return "Korisničko ime smije sadržavati samo slova i brojeve te mora biti kraće od 15 znakova.";
    }
    if (!ctype_alnum($password) OR strlen($password) > 10) {
        return "Lozinka smije sadržavati samo slova i brojeve te mora biti kraća od 10 znakova.";
    }

    $password = password_hash($password, PASSWORD_DEFAULT);

    $sql = "INSERT INTO users(privilege_id,email,username,password,name,gender,phone,date_of_birth,date_created,is_active) VALUES (?,?,?,?,?,?,?,'$date_of_birth','$current_date',1)";
    $connection->set_charset("utf8mb4");
    $statement = $connection->prepare($sql);
    $statement->bind_param('issssss', $privilege_id, $email, $username, $password, $name, $gender, $phone);
    $statement->execute();

    return 1;
}

function EditUser($id = 0, $name = "", $gender = "", $phone = "", $privilege_id = 0, $date_of_birth = 0) {

    $connection = mysqli_connect("localhost", "root", "", "workmanagement");
    mysqli_set_charset($connection,"utf8");

    $name = validate($name);
    $current_date = date('Y-m-d');

    $date_of_birth = strtotime($date_of_birth);
    $date_of_birth = date('Y-m-d', $date_of_birth);

    $fields = [
        'name' => $name,
        'gender' => $gender,
        'phone' => $phone,
        'privilege_id' => $privilege_id,
        'date_of_birth' => $date_of_birth,
    ];

    $i = 0;
    foreach ($fields as $field) {
        if (empty($field)) {
            $i++;
        }
    }

    if ($i >= 1) {
        return "Popunite sva polja!";
    }
    if (empty($name)) {
        return "Unesite ime i prezime!";
    }
    if (empty($phone)) {
        return "Unesite broj telefona!";
    }
    if (empty($date_of_birth)) {
        return "Unesite datum rođenja!";
    }

    $sql = "UPDATE users SET privilege_id = ?, name = ?, gender = ?, phone = ?, date_of_birth = '$date_of_birth', date_updated = '$current_date' 
            WHERE id = '$id'";
    $connection->set_charset("utf8mb4");
    $statement = $connection->prepare($sql);
    $statement->bind_param('isss', $privilege_id,  $name, $gender, $phone);
    $statement->execute();

    return 1;
}

function ChangePassword($id = 0, $new_password = "") {

    $connection = mysqli_connect("localhost", "root", "", "workmanagement");
    mysqli_set_charset($connection,"utf8");

    $new_password = validate($new_password);

    if (empty($new_password)) {
        return "Unesite lozinku!";
    }
    if (!ctype_alnum($new_password) OR strlen($new_password) > 10) {
        return "Lozinka smije sadržavati samo slova i brojeve te mora biti kraća od 10 znakova.";
    }

    $new_password = password_hash($new_password, PASSWORD_DEFAULT);

    $sql = "UPDATE users SET password = ? WHERE id = '$id'";
    $connection->set_charset("utf8mb4");
    $statement = $connection->prepare($sql);
    $statement->bind_param('s', $new_password);
    $statement->execute();

    return 1;
}

function EditProfile($id = 0, $name = "", $gender = "", $phone = "", $date_of_birth = 0) {

    $connection = mysqli_connect("localhost", "root", "", "workmanagement");
    mysqli_set_charset($connection,"utf8");

    $name = validate($name);
    $phone = validate($phone);
    $current_date = date('Y-m-d');

    $date_of_birth = strtotime($date_of_birth);
    $date_of_birth = date('Y-m-d', $date_of_birth);

    $fields = [
        'name' => $name,
        'gender' => $gender,
        'phone' => $phone,
        'date_of_birth' => $date_of_birth,
    ];

    $i = 0;
    foreach ($fields as $field) {
        if (empty($field)) {
            $i++;
        }
    }

    if ($i >= 1) {
        return "Popunite sva polja!";
    }
    if (empty($name)) {
        return "Unesite ime i prezime!";
    }
    if (empty($phone)) {
        return "Unesite broj telefona!";
    }
    if (empty($date_of_birth)) {
        return "Unesite datum rođenja!";
    }

    $sql = "UPDATE users SET name = ?, gender = ?, phone = ?, date_of_birth = '$date_of_birth', date_updated = '$current_date' 
            WHERE id = '$id'";
    $connection->set_charset("utf8mb4");
    $statement = $connection->prepare($sql);
    $statement->bind_param('sss', $name, $gender, $phone);
    $statement->execute();

    return 1;
}

function CountUserTickets($user_id, $job_id){

    $connection = mysqli_connect("localhost", "root", "", "workmanagement");
    mysqli_set_charset($connection,"utf8");

    $query = mysqli_query($connection
        ,"SELECT COUNT(job.id) AS jobs
                FROM tasks tas LEFT JOIN jobs job 
                ON job.id = tas.job_id LEFT JOIN statuses sts ON sts.id = tas.status_id
                LEFT JOIN user_tasks ust ON ust.task_id = tas.id
                WHERE ust.user_id = '$user_id' AND job.id = '$job_id' AND sts.name != 'Zavrseno'
                ORDER BY tas.date_due ASC");

    $tickets = 0;

    while($row = mysqli_fetch_assoc($query))
    {
     $tickets = $row["jobs"];
     break;
    }

    return $tickets;

}

function CountJobTickets($job_id) {

    $connection = mysqli_connect("localhost", "root", "", "workmanagement");
    mysqli_set_charset($connection,"utf8");

    $query = mysqli_query($connection
        ,"SELECT COUNT(job.id) AS jobs
                FROM tasks tas LEFT JOIN jobs job 
                ON job.id = tas.job_id
                WHERE job.id = '$job_id' ORDER BY tas.date_due ASC");

    $tickets = 0;

    while($row = mysqli_fetch_assoc($query))
    {
        $tickets = $row["jobs"];
        break;
    }

    return $tickets;

}
?>