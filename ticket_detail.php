<!DOCTYPE html>
<html lang="en">
<head>
    <?php session_start(); ?>
    <?php if (!isset($_SESSION['user']) OR isset($_POST['logout'])): ?>
        <?php session_destroy(); ?>
        <?php header("Location: login.php"); ?>
    <?php endif; ?>
    <?php include "functions.php"; ?>
    <title>Workify</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.2/css/all.css" integrity="sha384-oS3vJWv+0UjzBfQzYUhtDYW+Pj2yciDJxpsK1OYPAYjqT085Qq/1cq5FLXAZQ7Ay" crossorigin="anonymous">
    <link href="css/sidebar.css" rel="stylesheet">
    <link href="css/navbar.css" rel="stylesheet">
    <link href="css/bottom-content.css" rel="stylesheet">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.0/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/js/bootstrap.min.js"></script>
</head>
<body>
<?php $ticket_id = $_GET["ticket_id"]; ?>
<?php $ticket_name = $_GET["ticket_name"]; ?>
<?php $job_name = $_GET["job_name"]; ?>
<?php $job_id = $_GET["job_id"]; ?>

<?php $statuses = GetJobStatuses(); ?>
<?php $users = GetUsers(); ?>
<?php $ticket = GetTicket($ticket_id); ?>
<?php $ticket_user = GetTicketUser($ticket_id); ?>
<?php $ticket_user = GetUser($ticket_user); ?>
<?php $ticket_status = GetTicketStatus($ticket_id); ?>
<?php $ticket_notes = GetTicketNotes($ticket_id); ?>
<?php $active_ticket = GetActiveTicket($_SESSION['user']); ?>
<?php $active_ticket_current = GetCurrentActiveTicket($_SESSION['user']); ?>
<?php if (!empty($active_ticket_current)): ?>
    <?php $active_ticket_name = GetTicket($active_ticket_current); ?>
<?php endif; ?>
<?php if (isset($_POST['add_note'])): ?>
    <?php $insert_note = CreateTicketNote($_SESSION['user'], $ticket_id, $_POST['ticket_note']); ?>
    <?php $update_status = UpdateTicketStatus($_POST['ticket_status'], $ticket_id); ?>
    <?php header("Refresh:2"); ?>
<?php endif; ?>
<?php if (isset($_POST['assign_user'])): ?>
    <?php $assing_user = ChangeTicketUser($_SESSION['privilege'], $ticket_id, $_POST['users']); ?>
<?php endif; ?>
<div class="d-flex" id="wrapper">
    <div class="bg-light border-right" id="sidebar-wrapper">
        <div class="sidebar-heading">
            <a href="index.php"><img class="workify-logo" src="icons/logo.png"></a>
        </div>
        <div class="list-group list-group-flush">
            <a href="jobs.php" class="list-group-item list-group-item-action bg-light"><span class="fas fa-clipboard-list"></span> Poslovi</a>
            <a href="tickets.php" class="list-group-item list-group-item-action bg-light"><span class="fas fa-clipboard-list"></span> Radni nalozi</a>
            <?php if ($_SESSION['privilege'] == 'superuser'): ?>
                <a href="all_tickets.php" class="list-group-item list-group-item-action bg-light"><span class="fas fa-clipboard-list"></span> Svi radni nalozi</a>
                <a href="finished_tickets.php" class="list-group-item list-group-item-action bg-light"><span class="fas fa-clipboard-list"></span> Završeni nalozi</a>
            <?php endif; ?>
            <a href="users.php" class="list-group-item list-group-item-action bg-light"><span class="fas fa-users"></span> Korisnici</a>
            <?php if ($_SESSION['privilege'] != 'staff'): ?>
                <a href="create_job.php" class="list-group-item list-group-item-action bg-light"><span class="fas fa-plus"></span> Kreiraj posao</a>
                <a href="create_ticket.php" class="list-group-item list-group-item-action bg-light"><span class="fas fa-plus"></span> Kreiraj radni nalog</a>
            <?php endif; ?>
            <?php if ($_SESSION['privilege'] == 'superuser'): ?>
                <a href="create_user.php" class="list-group-item list-group-item-action bg-light"><span class="fas fa-plus"></span> Kreiraj korisnika</a>
            <?php endif; ?>
            <?php if (!empty($active_ticket_current)): ?>
                <a href="tickets.php" class="list-group-item list-group-item-action bg-light"><span class="badge badge-success">Radiš</span> <?php echo $active_ticket_name['name']; ?></a>
            <?php else: ?>
                <a href="tickets.php" class="list-group-item list-group-item-action bg-light"><span class="badge badge-danger">Ne radiš</span></a>
            <?php endif; ?>
        </div>
    </div>
    <div id="page-content-wrapper">
        <nav class="navbar navbar-expand-lg navbar-dark bg-primary border-bottom">
            <div class="collapse navbar-collapse">
                <ul class="navbar-nav ml-auto mt-2 mt-lg-0">
                    <li class="nav-item">
                        <a class="nav-link" href="my_profile.php">Moj profil</a>
                    </li>
                    <form method="post">
                        <input type="submit" name="logout" class="btn btn-light" value="Odjava">
                    </form>
                </ul>
            </div>
        </nav>
        <div class="container-fluid">
           <div class="row">
               <div class="col-sm-9">
                   <h1 class="mt-4"><?php echo $ticket_name; ?></h1>
                   <p>Posao: <span class="badge badge-primary"><?php echo $job_name; ?></span> Status: <span class="badge badge-info"><?php echo $ticket_status; ?></span> </p>
                   <p>Trenutni korisnik: <span class="badge badge-primary"><?php echo $ticket_user['username']; ?></span></p>
                   <h4 class="mt-4"> Opis radnog naloga </h4>
                   <p> <?php echo $ticket['description']; ?></p>
                   <h5 class="mt-4"> Bilješke </h5>
                   <br/>
                   <?php if (!empty($ticket_notes)): ?>
                        <?php foreach ($ticket_notes AS $note): ?>
                           <table class="table">
                               <thead>
                               <tr>
                                   <span class="badge badge-primary"><?php echo date('d.m.Y', strtotime($note['date_created'])); ?></span>
                                   <?php echo $note['username']; ?>
                               </tr>
                               </thead>
                               <tbody>
                                    <td><?php echo $note['note']; ?></td>
                               </tbody>
                            </table>
                        <?php endforeach; ?>
                   <?php else: ?>
                       <div class="alert alert-info" role="alert">
                           Nema bilješki!
                       </div>
                   <?php endif; ?>
               </div>
               <div class="col-sm-3">
                   <?php if (!empty($active_ticket) AND $active_ticket == $ticket_id): ?>
                       <div class="float-right">
                           <button onclick="location.href='stop_work.php?id=<?php echo $ticket_id; ?>&job_id=<?php echo $job_id; ?>'" type="button" class="btn btn-danger">Završi rad</button>
                       </div>
                   <?php elseif (!empty($active_ticket) AND $active_ticket != $ticket_id OR $ticket_status == 'Zavrseno'): ?>
                       <div class="float-right">
                           <button onclick="location.href='start_work.php?id=<?php echo $ticket_id; ?>'" type="button" class="btn btn-success" disabled>Započni rad</button>
                       </div>
                   <?php else: ?>
                       <div class="float-right">
                           <button onclick="location.href='start_work.php?id=<?php echo $ticket_id; ?>'" type="button" class="btn btn-success">Započni rad</button>
                       </div>
                   <?php endif; ?>
                   <br>
                   <form method="post">
                       <div class="form-group">
                           <label for="exampleFormControlTextarea1">Bilješka</label>
                           <textarea name="ticket_note" class="form-control" id="exampleFormControlTextarea1" rows="5" placeholder="Bilješka"><?php if (!empty($_POST["ticket_note"])): echo $_POST["ticket_note"]; endif; ?></textarea>
                       </div>
                       <div class="form-group">
                           <label for="exampleFormControlSelect1">Status naloga</label>
                           <select name="ticket_status" class="form-control" id="exampleFormControlSelect1">
                               <?php foreach ($statuses as $status): ?>
                                   <?php $selected_status = $_POST["ticket_status"]; ?>
                                   <?php $status_id = $status["id"]; ?>
                                   <?php $status_name = $status["name"]; ?>
                                   <?php if ($selected_status == $status_id): ?>
                                       <option value = <?php echo $status_id; ?> selected> <?php echo $status_name; ?></option>
                                   <?php else: ?>
                                       <option value = <?php echo $status_id; ?>> <?php echo $status_name; ?></option>
                                   <?php endif; ?>
                               <?php endforeach; ?>
                           </select>
                       </div>
                       <div class="form-group">
                           <input name="add_note" type="submit" class="btn btn-primary" value="Spremi bilješku">
                       </div>
                   </form>
                   <?php if (!empty($insert_note)): ?>
                       <div class="alert alert-info" role="alert">
                           <?php echo $insert_note; ?>
                       </div>
                   <?php endif; ?>
                   <?php if (!empty($update_status)): ?>
                       <div class="alert alert-info" role="alert">
                           <?php echo $update_status; ?>
                       </div>
                   <?php endif; ?>
                   <?php if (!empty($assing_user)): ?>
                       <div class="alert alert-info" role="alert">
                           <?php echo $assing_user; ?>
                       </div>
                   <?php endif; ?>
                   <form method="post">
                       <div class="form-group">
                           <label for="exampleFormControlSelect1">Dodjela radniku</label>
                           <select name="users" class="form-control" id="exampleFormControlSelect1">
                               <?php foreach ($users as $user): ?>
                                   <?php $selected_user = $_POST["users"]; ?>
                                   <?php $user_id = $user["id"]; ?>
                                   <?php $user_name = $user["name"]; ?>
                                   <?php if ($_SESSION['user'] == $user_id): ?>
                                       <option value = <?php echo $user_id; ?> selected> <?php echo $user_name; ?></option>
                                   <?php else: ?>
                                       <option value = <?php echo $user_id; ?>> <?php echo $user_name; ?></option>
                                   <?php endif; ?>
                               <?php endforeach; ?>
                           </select>
                       </div>
                       <div class="form-group">
                           <input name="assign_user" type="submit" class="btn btn-primary" value="Dodjeli radniku">
                       </div>
                   </form>
               </div>
           </div>
        </div>
    </div>
</div>
</body>
</html>

