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

<?php $users = GetUsers('', 0, 0); ?>
<?php
    $results_per_page = 9;
    $result_number = count($users);
    $page_number = ceil($result_number / $results_per_page);
?>
<?php if (!isset($_GET['page'])): ?>
    <?php $page = 1; ?>
<?php else: ?>
    <?php $page = $_GET['page']; ?>
<?php endif; ?>
<?php $page_result_number = ($page-1) * $results_per_page; ?>
<?php $users = GetUsers('', $page_result_number, $results_per_page); ?>
<?php if (isset($_POST['search'])): ?>
    <?php $users = GetUsers($_POST['search_value'], 0, $results_per_page); ?>
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
            <h1 class="mt-4">Korisnici</h1>
            <?php if ($_SESSION['privilege'] == 'superuser'): ?>
                <button onclick="location.href='create_user.php'" type="button" class="btn btn-secondary"><span class="fas fa-plus"></span> Dodaj novog korisnika</button>
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
                <?php if ($users == false OR empty($users)): ?>
                    <p>Nema korisnika!</p>
                <?php else: ?>
                    <thead>
                        <tr>
                            <th scope='col'>Red. broj</th>
                            <th scope='col'>Ime i prezime</th>
                            <th scope='col'>E-mail</th>
                            <th scope='col'>Korisničko ime</th>
                            <th scope='col'>Spol</th>
                            <th scope='col'>Datum rođenja</th>
                            <th scope='col'>Broj telefona</th>
                            <?php if ($_SESSION['privilege'] == 'superuser'): ?>
                                <th scope='col'></th>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>
                    <?php $i = 0; ?>
                        <?php foreach ($users as $user): ?>
                            <tr>
                                <?php $i++; ?>
                                <th scope="row"><?php echo $i; ?></th>
                                <?php if ($user["is_active"] == true): ?>
                                    <td><?php echo $user["name"]; ?></td>
                                <?php else: ?>
                                    <td><del style="color: red"><?php echo $user["name"]; ?></del></td>
                                <?php endif; ?>
                                <td><?php echo $user["email"]; ?></td>
                                <td><?php echo $user["username"]; ?></td>
                                <?php if ($user["gender"] == 'M'): ?>
                                     <td>Muško</td>
                                <?php else: ?>
                                     <td>Žensko</td>
                                <?php endif; ?>
                                <td><?php echo date('d.m.Y', strtotime($user["date_of_birth"])); ?></td>
                                <td><?php echo $user["phone"]; ?></td>
                                <?php $user_privilege = GetUserPrivilege($user['id']); ?>
                                <?php if ($_SESSION['privilege'] == 'superuser' AND $user_privilege != 'superuser'): ?>
                                <td>
                                    <div class="btn-group" role="group" aria-label="Basic example">
                                        <button onclick="location.href='edit_user.php?id=<?php echo $user['id']; ?>'" type="button" class="btn btn-primary">Uredi korisnika</button>
                                        <button onclick="location.href='change_password.php?id=<?php echo $user['id']; ?>'" type="button" class="btn btn-info">Nova lozinka</button>
                                        <button onclick="location.href='delete_user.php?id=<?php echo $user['id']; ?>'" type="button" class="btn btn-danger">Zabrani pristup</button>
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
                    <li class="page-item"><a class="page-link" href="users.php?page=<?php echo $page ?>"><?php echo $page; ?></a></li>
                <?php endfor; ?>
            </ul>
        </div>
    </div>
    <div>
</body>
</html>
