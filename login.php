<!DOCTYPE html>
<html lang="en">
<head>
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
<?php if (isset($_POST["login"])): ?>
    <?php $login = login($_POST["username"], $_POST["password"]); ?>
    <?php if (is_array($login)): ?>
        <?php
            foreach($login as $data){
                $user_id = $data["id"];
                $user_name = $data["full_name"];
                $user_privilege = $data["privilege"];
                $user_active = $data["is_active"];
            }
            if ($user_active != true) {
                $error = "Nemate pristup aplikaciji.";
            } else {
                session_start();
                $_SESSION['user'] = $user_id;
                $_SESSION['privilege'] = $user_privilege;
                $_SESSION['full_name'] = $user_name;
                header("Location: index.php");
            }
        ?>
    <?php endif; ?>
<?php endif; ?>
<div class="row justify-content-center align-items-center" style="height:100vh">
    <form method="post">
        <?php if (isset($login) AND !is_array($login)): ?>
            <div class="alert alert-danger" role="alert">
                <?php echo $login; ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        <?php endif; ?>
        <?php if (isset($error)): ?>
            <div class="alert alert-danger" role="alert">
                <?php echo $error; ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        <?php endif; ?>
        <a href="login.php"><img class="workify-logo-login" src="icons/logo.png"></a>
        <div class="form-group">
            <label for="exampleFormControlInput1">Korisničko ime</label>
            <input name="username" type="text" class="form-control" placeholder="Korisničko ime" value="<?php if (!empty($_POST['username'])): echo $_POST['username']; endif;?>">
        </div>
        <div class="form-group">
            <label for="exampleFormControlInput1">Lozinka</label>
            <input name="password" type="password" class="form-control" placeholder="Lozinka" value="<?php if (!empty($_POST['password'])): echo $_POST['password']; endif;?>">
        </div>
        <input name="login" type="submit" class="btn btn-primary" value="Potvrdi">
    </form>
</div>
</body>
</html>
