<?php
include 'config/dbconnection.php';

error_reporting(0);

session_start();

$prop = 'none';

if (isset($_POST['submit'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $stmt = $conn->prepare("SELECT user_id as id_petugas,username,nama_lengkap as nama_petugas,access_level as level 
    FROM user where username=:username and password=:password");
    $stmt->execute(['username' => $username, 'password' => $password]);
    $data = $stmt->fetchAll();

    foreach ($data as $row) {

        $plevel =  $row['level'];
        $pusername =  $row['username'];
        $pnama_petugas =  $row['nama_petugas'];
        $pid_petugas =  $row['id_petugas'];
        $_SESSION['pnama_petugas'] = $pnama_petugas;
        $_SESSION['plevel'] = $plevel;
        $_SESSION['pid_petugas'] = $pid_petugas;
        $_SESSION['pusername'] = $pusername;
    }

    if ($stmt->rowCount() > 0) {
        $prop = 'none';
        if ($plevel == 'admin') {
            header("Location: adm_dashboard.php");
        } else if ($plevel == 'cashier') {
            header("Location: csh_sale_add.php");
        } else if ($plevel == 'purchasing') {
            header("Location: pc_buy_add.php");
        }
    } else {
        $prop = 'block';
    }
}

if (isset($_POST['close'])) {
    $prop = 'none';
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
    <link rel="stylesheet" type="text/css" href="css/login.css">
    <link rel="icon" type="image/x-icon" href="images/sppico.png">
    <title>Aplikasi Cashier</title>
</head>

<body>
    <div class="login_container">
        <div class="login_box">
            <div class="login_box_header">
                <div class="login_box_header_left">
                    <img src="images/logo.png" alt="" style="height: 130px;">
                </div>
                <div class="login_box_header_right">
                    Sistem Informasi Aplikasi Cashier Online
                </div>
            </div>
            <form action="" method="POST">
                <div class="login_box_content">
                    <div class="login_box_content_item">
                        <input id="username" type="text" name="username" placeholder="USER ID" required autocomplete=”off”>
                    </div>
                    <div class="login_box_content_item">
                        <input id="password" type="password" name="password" placeholder="PASSWORD" required autocomplete=”off”>
                    </div>
                    <div class="login_box_content_item">
                        <input id="submit" name="submit" type="submit" value="Log In">
                    </div>
                </div>
            </form>
            <div class="login_box_footer">
                Developed by Annisa Mutia Rahma
            </div>
        </div>
    </div>
    <form action="" method="POST">
        <div id="loginAlert" class="modal" tabindex="-1" role="dialog" style="display:<?php echo $prop; ?>;">
            <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Warning</h5>
                    </div>
                    <div class="modal-body">
                        <p>Username atau password invalid.</p>
                    </div>
                    <div class="modal-footer">
                        <!-- <button type="button" class="btn btn-primary">Close</button> -->
                        <button type="submit" name="close" class="btn btn-primary" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    </form>



</body>

</html>