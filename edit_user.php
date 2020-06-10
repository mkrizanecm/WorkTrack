<!DOCTYPE html>
<html lang="en">
<head>
    <?php session_start(); ?>
    <?php if (!isset($_SESSION['user']) OR isset($_POST['logout'])): ?>
        <?php session_destroy(); ?>
        <?php header("Location: login.php"); ?>
    <?php endif; ?>
    <?php if ($_SESSION['privilege'] != 'superuser'): ?>
        <?php header("Refresh:1 url=users.php"); ?>
        <?php die("Nemate ovlasti za ovu stranicu!"); ?>
    <?php endif; ?>
    <?php include "functions.php"; ?>
    <?php $user_privilege = GetUserPrivilege($_GET['id']); ?>
    <?php if ($user_privilege == 'superuser'): ?>
        <?php header("Refresh:1 url=users.php"); ?>
        <?php die("Nemate ovlasti za ovu stranicu!"); ?>
    <?php endif; ?>
    <title>Workify</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.2/css/all.css" integrity="sha384-oS3vJWv+0UjzBfQzYUhtDYW+Pj2yciDJxpsK1OYPAYjqT085Qq/1cq5FLXAZQ7Ay" crossorigin="anonymous">
    <link href="css/sidebar.css" rel="stylesheet">
    <link href="css/navbar.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.4.1/css/bootstrap-datepicker3.css"/>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.0/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.4.1/js/bootstrap-datepicker.min.js"></script>
</head>
<body>
<?php $privileges = GetPrivileges(); ?>
<?php $current_user_id = $_GET['id']; ?>
<?php $current_user = GetUser($current_user_id); ?>
<?php $current_privilege = $current_user['privilege']; ?>
<?php $current_gender = $current_user['gender']; ?>

<?php if (isset($_POST["edit_user"])): ?>
    <?php $edit_user = EditUser($current_user_id, $_POST['name'], $_POST['gender'], $_POST['mobile'], $_POST['privilege'], $_POST['date_of_birth']); ?>
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
            <a href="jobs.php" class="list-group-item list-group-item-action bg-light"><span class="fas fa-clipboard-list"></span> Projekti</a>
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
            <h1 class="mt-4">Uredi korisnika</h1>
            <?php if (isset($edit_user) AND !empty($edit_user)): ?>
                <?php if ($edit_user == 1): ?>
                    <div class="alert alert-success" role="alert">
                        Uspješna promjena podataka!
                        <?php $secondsWait = 1; header("Refresh:$secondsWait"); ?>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                <?php else: ?>
                    <div class="alert alert-danger" role="alert">
                        <?php echo $edit_user; ?>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
            <?php if (!empty($current_user) AND $current_user != false): ?>
            <form method="post">
                <div class="form-group">
                    <label for="exampleFormControlInput1">Ime i prezime</label>
                    <input name="name" type="text" class="form-control" id="exampleFormControlInput1" placeholder="Unesite ime i prezime korisnika" value="<?php if (!empty($_POST['name'])): echo $_POST['name']; else: echo $current_user['full_name']; endif; ?>">
                </div>
                <div class="form-group">
                    <label for="exampleFormControlSelect1">Spol</label>
                    <select name="gender" class="form-control" id="exampleFormControlSelect1">
                        <?php if ($current_gender == 'M'): ?>
                            <option value = <?php echo $current_gender; ?> selected>Muško</option>
                            <option value = "F">Žensko</option>
                        <?php else: ?>
                            <option value = <?php echo $current_gender; ?> selected>Žensko</option>
                            <option value = "M">Muško</option>
                        <?php endif; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="exampleFormControlInput1">Broj telefona</label>
                    <input name="mobile" type="text" class="form-control" id="exampleFormControlInput1" placeholder="Broj telefona / mobitela korisnika" value="<?php if (!empty($_POST["mobile"])): echo $_POST["mobile"]; else: echo $current_user['phone']; endif; ?>">
                </div>
                <div class="form-group">
                    <label for="exampleFormControlSelect1">Ovlasti korisnika</label>
                    <select name="privilege" class="form-control" id="exampleFormControlSelect1">
                        <?php foreach ($privileges as $privilege): ?>
                            <?php $privilege_id = $privilege["id"]; ?>
                            <?php $privilege_name = $privilege["name"]; ?>
                            <?php if ($current_privilege == $privilege_id): ?>
                                <option value = <?php echo $privilege_id; ?> selected> <?php echo $privilege_name; ?></option>
                            <?php else: ?>
                                <option value = <?php echo $privilege_id; ?>> <?php echo $privilege_name; ?></option>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="exampleFormControlSelect1">Datum rođenja</label>
                    <div class="input-group date">
                        <input name="date_of_birth" type="text" class="form-control" id="js-date" value="<?php if (!empty($_POST["date_of_birth"])): echo $_POST["date_of_birth"]; else: echo date('d-m-Y', strtotime($current_user['date_of_birth'])); endif; ?>"/>
                        <div class="input-group-addon">
                            <span class="glyphicon glyphicon-calendar"></span>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <input name="edit_user" type="submit" class="btn btn-primary" value="Uredi korisnika" >
                </div>
            </form>
            <?php endif; ?>
        </div>
    </div>
    <div>
        <script>
            $.fn.datepicker.defaults.format = "dd-mm-yyyy";
            $(document).ready(function() {
                $('#js-date').datepicker();
            });
        </script>
</body>
</html>
