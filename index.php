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
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.0/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/js/bootstrap.min.js"></script>
</head>
<body>
<?php $active_ticket = GetCurrentActiveTicket($_SESSION['user']); ?>
<?php $expired_tickets = GetExpiredTickets($_SESSION['privilege'], $_SESSION['user']); ?>
<?php if (!empty($active_ticket)): ?>
    <?php $active_ticket_name = GetTicket($active_ticket); ?>
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
            <?php if (!empty($active_ticket)): ?>
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
            <div align="center">
                <h4>Dobrodošli u </h4>
                    <a href="index.php"><img src="icons/logo-index.png"></a>
                <h4><?php echo $_SESSION['full_name']; ?> </h4>
            </div>
             <?php if (empty($expired_tickets)): ?>
                 <br/>
                 <div class="alert alert-info" role="alert">
                     <h5 align="center">Nema novosti!</h5>
                 </div>
             <?php else: ?>
                 <h4>Nalozi kojima je prošao rok:</h4>
                 <?php foreach ($expired_tickets as $expired): ?>
                     <div class="alert alert-info" role="alert">
                         <p><?php echo $expired['name'] . " "; ?> <span class="badge badge-danger"><?php echo date('d.m.Y', strtotime($expired["date_due"])); ?></span></p>
                     </div>
                 <?php endforeach; ?>
             <?php endif; ?>
            </table>
        </div>
    </div>
<div>
</body>
</html>
