<!DOCTYPE html>
<html lang="en">
<head>
    <?php session_start(); ?>
    <?php if (!isset($_SESSION['user']) OR isset($_POST['logout'])): ?>
        <?php session_destroy(); ?>
        <?php header("Location: login.php"); ?>
    <?php endif; ?>
    <?php if ($_SESSION['privilege'] == 'staff'): ?>
        <?php die("Nemate ovlasti za ovu stranicu!"); ?>
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
<?php $current_job_id = $_GET['id']; ?>
<?php $job = GetJob($current_job_id); ?>
<?php $job_status = $job['status_id']; ?>
<?php $statuses = GetJobStatuses(); ?>
<?php if (isset($_POST["edit_job"])): ?>
    <?php $edit_job = EditJob($current_job_id, $_POST["job_name"], $_POST["job_description"], $_POST["job_status"]); ?>
<?php endif; ?>
<?php $active_ticket = GetCurrentActiveTicket($_SESSION['user']); ?>
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
            <h1 class="mt-4">Uredi posao</h1>
            <?php if (isset($edit_job) AND !empty($edit_job)): ?>
                <?php if ($edit_job == 1): ?>
                    <div class="alert alert-success" role="alert">
                        Uspješna promjena podataka!
                        <?php $secondsWait = 1; header("Refresh:$secondsWait"); ?>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                <?php else: ?>
                    <div class="alert alert-danger" role="alert">
                        <?php echo $edit_job; ?>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
            <form method="post">
                <div class="form-group">
                    <label for="exampleFormControlInput1">Naziv posla</label>
                    <input name="job_name" type="text" class="form-control" id="exampleFormControlInput1" placeholder="Unesite naziv posla (max. 50 znakova)" value="<?php if (!empty($_POST["job_name"])): echo $_POST["job_name"] ; else: echo $job['name']; endif;?>">
                </div>
                <div class="form-group">
                    <label for="exampleFormControlTextarea1">Opis posla</label>
                    <textarea name="job_description" class="form-control" id="exampleFormControlTextarea1" rows="5" placeholder="Opišite posao"><?php if (!empty($_POST["job_description"])): echo $_POST["job_description"] ; else: echo $job['description']; endif;?></textarea>
                </div>
                <div class="form-group">
                    <label for="exampleFormControlSelect1">Status posla</label>
                    <select name="job_status" class="form-control" id="exampleFormControlSelect1">
                        <?php foreach ($statuses as $status): ?>
                            <?php $status_id = $status["id"]; ?>
                            <?php $status_name = $status["name"]; ?>
                            <?php if ($job_status == $status_id): ?>
                                <option value = <?php echo $status_id; ?> selected> <?php echo $status_name; ?></option>
                            <?php else: ?>
                                <option value = <?php echo $status_id; ?>> <?php echo $status_name; ?></option>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <input name="edit_job" type="submit" class="btn btn-primary" value="Uredi posao" >
                </div>
            </form>
        </div>
    </div>
    <div>
</body>
</html>
