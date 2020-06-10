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
<?php $jobs = GetJobs('', 0, 0); ?>
<?php
    $results_per_page = 9;
    $result_number = count($jobs);
    $page_number = ceil($result_number / $results_per_page);
?>
<?php if (!isset($_GET['page'])): ?>
    <?php $page = 1; ?>
<?php else: ?>
    <?php $page = $_GET['page']; ?>
<?php endif; ?>
<?php $page_result_number = ($page-1) * $results_per_page; ?>
<?php $jobs = GetJobs('', $page_result_number, $results_per_page); ?>
<?php if (isset($_POST['search'])): ?>
    <?php $jobs = GetJobs($_POST['search_value'], 0, $results_per_page); ?>
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
            <h1 class="mt-4">Poslovi</h1>
            <?php if ($_SESSION['privilege'] != 'staff'): ?>
                <button onclick="location.href='create_job.php'" type="button" class="btn btn-secondary"><span class="fas fa-plus"></span> Kreiraj novi posao</button>
            <?php endif; ?>
            <br/><br/>
            <form method="post">
                <div class="col-4">
                    <div class="form-group">
                        <h4>Pretraživanje</h4>
                        <input type="text" name="search_value" class="form-control" value="<?php if (!empty($_POST['search_value'])): echo $_POST['search_value']; endif; ?>">
                    </div>
                    <div class="form-group">
                        <input type="submit" name="search" class="btn btn-primary" value="Traži">
                    </div>
                </div>
            </form>
            <table class="table">
                <?php if ($jobs == false OR empty($jobs)): ?>
                    <div class="alert alert-danger" role="alert">
                        Nema poslova!
                    </div>
                <?php else: ?>
                    <thead>
                    <tr>
                        <th scope='col'>Posao</th>
                        <th scope='col'>Tvoji / Sveukupno</th>
                        <th scope='col'>Potrošeno sati</th>
                        <?php if ($_SESSION['privilege'] != 'staff'): ?>
                            <th scope='col'></th>
                        <?php endif; ?>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($jobs as $job): ?>
                        <tr>
                            <?php $user_tickets = CountUserTickets($_SESSION['user'], $job['id']); ?>
                            <?php $job_tickets = CountJobTickets($job['id']); ?>
                            <?php $job_hours = GetJobHours($job['id']); ?>
                            <td><a href="job_tickets.php?job_id=<?php echo $job["id"]; ?>&job_name=<?php echo $job["name"]; ?>"> <?php echo $job["name"]; ?></a></td>
                            <?php if ($user_tickets == 0 AND $job_tickets == 0): ?>
                                <td><span class="badge badge-danger">Nema</span></td>
                            <?php else: ?>
                                <td> <?php echo $user_tickets; ?>/<?php echo $job_tickets; ?> </td>
                            <?php endif; ?>
                            <?php if (!empty($job_hours)): ?>
                                <td><h6><span class="badge badge-secondary"><?php echo $job_hours; ?></span></h6></td>
                            <?php else: ?>
                                <td><h6><span class="badge badge-secondary">0</span></h6></td>
                            <?php endif; ?>
                            <?php if ($_SESSION['privilege'] != 'staff'): ?>
                            <td>
                                <div class="btn-group" role="group">
                                    <button onclick="location.href='edit_job.php?id=<?php echo $job['id']; ?>'" type="button" class="btn btn-primary">Uredi posao</button>
                                </div>
                            </td>
                            <?php endif; ?>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                <?php endif; ?>
            </table>
            <ul class="pagination">
                <?php for ($page = 1; $page <= $page_number; $page++): ?>
                    <li class="page-item"><a class="page-link" href="jobs.php?page=<?php echo $page ?>"><?php echo $page; ?></a></li>
                <?php endfor; ?>
            </ul>
        </div>
    </div>
    <div>
</body>
</html>
