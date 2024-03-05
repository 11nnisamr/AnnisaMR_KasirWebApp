<!-- HALAMAN ADMIN - MENU SETUP USER -->

<?php

    include 'config/dbconnection.php';

    error_reporting(0);

    session_start();

    if (!isset($_SESSION['pnama_petugas'])) {
        header("Location: index.php");
        exit(); // Terminate script execution after the redirect
    }
    
    if ($_SESSION['plevel']<>"admin"){
        header("HTTP/1.0 404 Not Found");
        echo "<h1>404 Not Found</h1>";
        echo "The page that you have requested could not be found.";
        exit();
    }
    

    if (isset($_POST['records-limit'])) {
        $_SESSION['records-limit'] = $_POST['records-limit'];
    }

    $limit = isset($_SESSION['records-limit']) ? $_SESSION['records-limit'] : 7;
    $page = (isset($_GET['page']) && is_numeric($_GET['page'])) ? $_GET['page'] : 1;
    $paginationStart = ($page - 1) * $limit;

    $stmt = $conn->prepare("SELECT ROW_NUMBER() OVER(ORDER BY user_id) AS no, user_id id_petugas,username,password,nama_lengkap as nama_petugas,access_level as level FROM user where user_id != 3 order by user_id LIMIT $paginationStart, $limit");
    $stmt->execute();
    $data = $stmt->fetchAll();


    // Get total records
    $stmt = $conn->prepare("SELECT count(user_id) AS id FROM user where user_id !=3");
    $stmt->execute();
    $countdata = $stmt->fetchAll();
    $allRecrods = $countdata[0]['id'];

    // Calculate total pages
    $totoalPages = ceil($allRecrods / $limit);

    $prev = $page - 1;
    $next = $page + 1;


// --------------- Paging ----------------//


$pnama_petugas = $_SESSION['pnama_petugas'];
$plevel = $_SESSION['plevel'];
$propAlert = 'none';
$alertMessages = "";


// Form Tambah Klik Tombol Simpan Data User
if (isset($_POST['iBtnSimpan'])) {


    $username = $_POST['iusername'];
    $password = $_POST['ipassword'];
    $nama_petugas = $_POST['inama_petugas'];
    $level = $_POST['ilevel'];

    if ($level == "none") {
        $propAlert = 'block';
        $alertMessages = "Data tidak berhasil disimpan, pilih Level terbelih dahulu.";
    } else {
        try {
            $arrData = [
                'username' => $username,
                'password' => $password,
                'nama_petugas' => $nama_petugas,
                'level' => $level,
            ];

            $sql = "INSERT INTO user (username, password, nama_lengkap,access_level) VALUES (:username, :password, :nama_petugas,:level)";
            $stmt = $conn->prepare($sql);
            $stmt->execute($arrData);
            $alertMessages = "Data berhasil disimpan";
        } catch (PDOException $e) {
            $alertMessages = $e->getMessage();
        }
        $propAlert = 'block';
    }
}

// Form Edit Klik Tombol Simpan Data User
if (isset($_POST['eBtnSimpan'])) {

    $id_petugas = $_POST['eid_petugas'];
    $username = $_POST['eusername'];
    $password = $_POST['epassword'];
    $nama_petugas = $_POST['enama_petugas'];
    $level = $_POST['elevel'];

    try {
        $arrData = [
            'username' => $username,
            'password' => $password,
            'nama_petugas' => $nama_petugas,
            'level' => $level,
            'id_petugas' => $id_petugas,
        ];

        $sql = "update user set username = :username, password = :password, nama_lengkap =:nama_petugas, access_level =:level where user_id = :id_petugas";
        $stmt = $conn->prepare($sql);
        $stmt->execute($arrData);
        $alertMessages = "Data berhasil disimpan";

    } catch (PDOException $e) {
        $alertMessages = $e->getMessage();
    }

    $propAlert = 'block';
}

// Form Edit Klik Tombol Hapus Data User
if (isset($_POST['hBtnSimpan'])) {

    $id_petugas = $_POST['hid_petugas'];

    try {
        $arrData = [
            'id_petugas' => $id_petugas,
        ];

        $sql = "delete from user where user_id = :id_petugas";
        $stmt = $conn->prepare($sql);
        $stmt->execute($arrData);
        $alertMessages = "Data berhasil dihapus";

    } catch (PDOException $e) {
        $alertMessages = $e->getMessage();
    }

    $propAlert = 'block';
}


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/layout.css">
    <link rel="stylesheet" href="css/menu.css">
    <link rel="stylesheet" href="css/forms.css">
    <link rel="stylesheet" href="font-awesome/css/font-awesome.min.css">
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="icon" type="image/x-icon" href="images/sppico.png">
    <title>Setup User</title>
</head>

<!-- JAVASCRIPT & JQUERY -->
<script>
    $(document).ready(function() {

        $('#iBtnTambah').click(function() {
            document.getElementById('formTambah').style.display = 'block';
        });

        $("#tables").on('click', '#BtnEdit', function(event) {
            
            var id_petugas = $(this).closest('tr').find('td:eq(1)').text();
            var username = $(this).closest('tr').find('td:eq(2)').text();
            var password = $(this).closest('tr').find('td:eq(3)').text();
            var nama_petugas = $(this).closest('tr').find('td:eq(4)').text();
            var level = $(this).closest('tr').find('td:eq(5)').text();
            document.getElementById('formEdit').style.display = 'block';
            document.getElementById('eid_petugas').value = id_petugas;
            document.getElementById('eusername').value = username;
            document.getElementById('epassword').value = password;
            document.getElementById('enama_petugas').value = nama_petugas;
            document.getElementById('elevel').value = level;
            event.preventDefault();
        });

        $("#tables").on('click', '#BtnHapus', function(event) {
            document.getElementById('formHapus').style.display = 'block';
            var id_petugas = $(this).closest('tr').find('td:eq(1)').text();
            var username = $(this).closest('tr').find('td:eq(2)').text();
            document.getElementById('hid_petugas').value = id_petugas;
            document.getElementById('KonfirmasiHapus').innerText = 'Anda yakin akan menghapus data ID User ' + username + '?';
            event.preventDefault();
        });

        $("#cUser").on("keyup", function() {
            var value = $(this).val().toLowerCase();
            $("#tableCariUser tr").filter(function() {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
            });
        });

        $('#iBtnClose').click(function(event) {
            document.getElementById("iusername").required = false;
            document.getElementById("ipassword").required = false;
            document.getElementById("inama_petugas").required = false;
            document.getElementById("ilevel").required = false;
            document.getElementById('formTambah').style.display = 'none';
            event.preventDefault();

        });

        $('#eBtnClose').click(function(event) {
            document.getElementById('formEdit').style.display = 'none';
            event.preventDefault();
        });

        $('#hBtnClose').click(function(event) {
            document.getElementById('formHapus').style.display = 'none';
            event.preventDefault();
        });

    });
</script>

<body>

    <!-- LAYOUT & FORM PAGES-->
    <div class="layout_container">
        <!-- LAYOUT MENU -->
        <div class="layout_menu">
            <!-- MENU -->
            <div class="menu">
                <!-- MENU HEADER -->
                <div class="menu_header">
                <div class="menu_header_logo">
                        <img src="images/logo.png" alt="" style="background-color:rgb(213, 219, 236);height:150px; top:-60px;left:30px;position:absolute;border-radius:30%;">
                    </div>
                    <div style="display:flex;margin-top:60px;height:45px;flex:1;background-color:#5074dc;"></div>
                </div>
                <!-- MENU CONTENT -->
                <!-- SETUP -->
                <div class="menu_detail">
                    <div class="menu_detail_data">
                        <a href="adm_dashboard.php">
                            <div class="menu_detail_icon">
                                <i class="fa fa-home"></i> &nbsp; Dashboard
                            </div>
                        </a>
                    </div>

                    <div class="menu_detail_title">
                        SETUP
                    </div>
                    <div class="menu_detail_data">
                        <a href="adm_users.php">
                            <div class="menu_detail_icon  menu_detail_data_active">
                                <i class="fa fa-lock"></i> &nbsp; User  &nbsp; <i class="fa fa-check"></i>
                            </div>
                        </a>
                    </div>

                    <!--                     
                        <div class = "menu_detail_data">
                            <a href="adminb.php">
                                <div class="menu_detail_icon">
                                    <i class="fa fa-th-large"></i> &nbsp; Petugas
                                </div>    
                            </a>    
                        </div> -->

                    <div class="menu_detail_data">
                        <a href="adm_suppliers.php">
                            <div class="menu_detail_icon">
                                <i class="fa fa-building-o"></i> &nbsp; Supplier
                            </div>
                        </a>
                    </div>

                    <div class="menu_detail_data">
                        <a href="adm_product_categories.php">
                            <div class="menu_detail_icon">
                                <i class="fa fa-credit-card"></i> &nbsp; Product Category
                            </div>
                        </a>
                    </div>
                    <div class="menu_detail_data">
                        <a href="adm_products.php">
                            <div class="menu_detail_icon">
                                <i class="fa fa-credit-card"></i> &nbsp; Product
                            </div>
                        </a>
                    </div>

                    <div class="menu_detail_data">
                        <a href="adm_customers.php">
                            <div class="menu_detail_icon">
                                <i class="fa fa-user"></i> &nbsp; Customer
                            </div>
                        </a>
                    </div>

                </div>
                <!-- DIVIDER -->
                <div class="menu_divider">
                    <div class="menu_divider_style"></div>
                </div>
                <!-- TRANSAKSI -->
                <div class="menu_detail">
                    <div class="menu_detail_title">
                        TRANSAKSI
                    </div>
                    <div class="menu_detail_data">
                        <a href="adm_buy_add.php">
                            <div class="menu_detail_icon">
                                <i class="fa fa-money"></i> &nbsp; Pembelian
                            </div>
                        </a>
                    </div>
                    <div class="menu_detail_data">
                        <a href="adm_sale_add.php">
                            <div class="menu_detail_icon">
                                <i class="fa fa-shopping-bag"></i> &nbsp; Penjualan 
                            </div>
                        </a>
                    </div>
                </div>
                <!-- DIVIDER -->
                <div class="menu_divider">
                    <div class="menu_divider_style"></div>
                </div>
                <!-- LAPORAN -->

                <!-- MENU FOOTER -->
                <div class="menu_footer">
                    <div class="menu_detail_data">
                        <a href="logout.php">
                            <div class="menu_detail_icon">
                                <i class="fa fa-power-off"></i> &nbsp; Logout
                            </div>
                        </a>
                    </div>

                </div>
            </div>
        </div>

        <!-- LAYOUT FORM -->
        <div class="layout_form">
            <div class="forms">
                <!-- FORM HEADER -->
                <div class="forms_header">
                    <div class="forms_header_title" style="position:relative;">
                        <!-- <div class="forms_content_title_icon">
                            <i class="fa fa-lock"></i>
                        </div>
                        <div class="forms_content_title_description">
                            Setup User
                        </div> -->
                        <!-- <div>
                            <img src="images/slogan.png" alt="" style="height:60px; top:-20px;left:20px;position:absolute;border-radius:100%;">
                        </div>     -->
                        <div style=" display:flex;top:-10px;position:absolute;left:20px;width:100%;">
                            𝙉𝙞𝙨𝙖𝙢𝙖𝙧𝙩, 𝙏𝙚𝙢𝙥𝙖𝙩 𝘽𝙚𝙡𝙖𝙣𝙟𝙖 𝙏𝙚𝙧𝙗𝙖𝙞𝙠 𝙙𝙚𝙣𝙜𝙖𝙣 𝙃𝙖𝙧𝙜𝙖 𝙏𝙚𝙧𝙟𝙖𝙣𝙜𝙠𝙖𝙪 ...
                        </div>
                    </div>
                    <div class="forms_header_login">
                        <div class="forms_header_login_user">
                            <div class="forms_header_login_username">
                                <?php echo $pnama_petugas; ?>
                            </div>
                            <div class="forms_header_login_useraccess">
                                Login as <?php echo ucfirst($plevel); ?>
                            </div>
                        </div>
                        <div class="forms_header_login_userpic">
                            <img src="images/user.png" alt="" style="height: 30px;">
                        </div>
                    </div>
                </div>
                <!-- FORM CONTENT -->
                <div class="forms_content">
                    <div class="forms_content_title">
                        <!-- <div class="forms_content_title_icon">
                            <i class="fa fa-lock"></i>
                        </div>
                        <div class="forms_content_title_description">
                            Setup User
                        </div> -->
                    </div>

                    <div class="forms_content_box">

                        <!-- TOOLBAR TAMBAH USER -->
                        <div class="forms_content_box_toolbar">
                            <div class="forms_content_box_toolbar_item">
                                <button id="iBtnTambah" type="submit" class="btn btn-default btn-md" style="padding-right:30px;background-color:#5074dc;color:white;font-size:14px;">
                                    <span class="fa fa-plus" style="padding-right:10px;"></span> Input User
                                </button>
                            </div>
                            <div class="forms_content_box_toolbar_search">
                                <div style="margin-left:10px;">
                                    <input type="text" class="form-control" placeholder="Ketik pencarian.." id="cUser" style="width:250px;height:30px;border-style:solid;border-width:0.5px; border-color:#808080;padding-left:5px;font-size:14px;">
                                    <i class="fa fa-search" style="position:absolute;right:8px;top:5px;font-size:18px;"></i>
                                </div>
                            </div>
                        </div>

                        <!-- TABLE DATA USERS     -->
                        <div class="forms_content_box_tables">
                            <div style="display: block;overflow-x:auto; white-space: nowrap;width:100%;">

                                <table id="tables" class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th style="text-align:center;width:50px;">No</th>
                                            <th hidden>user id</th>
                                            <th>ID User</th>
                                            <th>Password</th>
                                            <th>Nama User</th>
                                            <th>Level</th>
                                            <th style="width:50px;text-align:center;">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tableCariUser">
                                        <?php
                                        foreach ($data as $row) {
                                            $no = $row['no'];
                                            $id_petugas = $row['id_petugas'];
                                            $username = $row['username'];
                                            $password = $row['password'];
                                            $nama_petugas =  $row['nama_petugas'];
                                            $level =  $row['level'];
                                        ?>
                                            <tr>
                                                <td style="text-align:center"><?php echo $no; ?></td>
                                                <td hidden><?php echo $id_petugas; ?></td>
                                                <td><?php echo $username; ?></td>
                                                <td style="width: 100px;"><input type="password" style="border: none; outline:none; width:150px;" value=<?php echo $password; ?>></td>
                                                <td><?php echo $nama_petugas; ?></td>
                                                <td><?php echo $level; ?></td>
                                                <td style="text-align:center">
                                                    <form action="" method="POST">
                                                        <button id="BtnEdit" type="submit" class="btn btn-default btn-sm" style="background-color:#5074dc;color:white;font-size:13px;">
                                                            <span class="fa fa-edit"></span>
                                                        </button>
                                                        <!-- <button id="BtnHapus" type="submit" class="btn btn-default btn-sm" style="background-color:rgb(133, 47, 47);color:white; font-size:13px;">
                                                            <span class="fa fa-trash "></span>

                                                        </button> -->
                                                    </form>
                                                </td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>

                                <!-- PAGING -->
                                <nav aria-label="Page navigation example mt-5">
                                    <ul class="pagination justify-content-center">
                                        <li class="page-item <?php if ($page <= 1) {
                                                                    echo 'disabled';
                                                                } ?>">
                                            <a class="page-link" style="font-size:13px;" href="<?php if ($page <= 1) {
                                                                                                    echo '#';
                                                                                                } else {
                                                                                                    echo "?page=" . $prev;
                                                                                                } ?>">Prev</a>
                                        </li>

                                        <?php for ($i = 1; $i <= $totoalPages; $i++) : ?>
                                            <li class="page-item <?php if ($page == $i) {
                                                                        echo 'active';
                                                                    } ?>">
                                                <a class="page-link" style="font-size:13px;" href="adm_users.php?page=<?= $i; ?>"> <?= $i; ?> </a>
                                            </li>
                                        <?php endfor; ?>

                                        <li class="page-item <?php if ($page >= $totoalPages) {
                                                                    echo 'disabled';
                                                                } ?>">
                                            <a class="page-link" style="font-size:13px;" href="<?php if ($page >= $totoalPages) {
                                                                                                    echo '#';
                                                                                                } else {
                                                                                                    echo "?page=" . $next;
                                                                                                } ?>">Next</a>
                                        </li>
                                    </ul>
                                </nav>
                                <!-- PAGING -->
                            </div>
                        </div>

                    </div>

                </div>
            </div>
        </div>
    </div>

    <!-- FORM TAMBAH -->
    <form action="adm_users.php" method="POST">
        <div id="formTambah" class="modal" style="width:100%;height:100vh; background-color: rgba(0, 0, 0, 0.5);" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Form Tambah User</h5>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="username" class="form-label">User ID</label>
                            <input type="text" class="form-control" id="iusername" name="iusername" required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" name="ipassword" id="ipassword" autocomplete=”off” readonly onclick="this.removeAttribute('readOnly');" required>
                        </div>
                        <div class="mb-3">
                            <label for="nama_petugas" class="form-label">Nama User</label>
                            <input type="text" class="form-control" id="inama_petugas" name="inama_petugas" required>
                        </div>
                        <div class="mb-3">
                            <label for="level" class="form-label">Level</label>
                            <select class="form-select" aria-label="Default select example" name="ilevel">
                                <option value="none" selected>--Pilih Level --</option>
                                <option value="admin">admin</option>
                                <option value="cashier">cashier</option>
                                <!-- <option value="purchasing">purchasing</option> -->
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" id="iBtnClose" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="submit" id="iBtnSimpan" name="iBtnSimpan" class="btn btn-primary">Simpan</button>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <!-- FORM EDIT -->
    <form action="adm_users.php" method="POST">
        <div id="formEdit" class="modal" style="width:100%;height:100vh; background-color: rgba(0, 0, 0, 0.5);" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Form Edit User</h5>
                    </div>
                    <div class="modal-body">
                        <div>
                            <input type="hidden" id="eid_petugas" name="eid_petugas">
                        </div>
                        <div class="mb-3">
                            <label for="username" class="form-label">User ID</label>
                            <input type="text" class="form-control" id="eusername" name="eusername">
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="epassword" name="epassword" autocomplete=”off” readonly onclick="this.removeAttribute('readOnly');">
                        </div>
                        <div class="mb-3">
                            <label for="nama_petugas" class="form-label">Nama User</label>
                            <input type="text" class="form-control" id="enama_petugas" name="enama_petugas">
                        </div>
                        <div class="mb-3">
                            <label for="level" class="form-label">Level</label>
                            <select class="form-select" aria-label="Default select example" id="elevel" name="elevel">
                                <option value="admin">admin</option>
                                <option value="cashier">cashier</option>
                                <!-- <option value="purchasing">purchasing</option> -->
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" id="eBtnClose" name="close" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="submit" id="eBtnSimpan" name="eBtnSimpan" class="btn btn-primary">Simpan</button>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <!-- FORM HAPUS -->
    <form action="adm_users.php" method="POST">
        <div id="formHapus" class="modal"  style="width:100%;height:100vh; background-color: rgba(0, 0, 0, 0.5);" tabindex="-1" role="dialog" >
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Form Hapus User</h5>
                    </div>
                    <div class="modal-body">
                        <div>
                            <input type="hidden" id="hid_petugas" name="hid_petugas">
                        </div>
                        <span id="KonfirmasiHapus">...</span>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" id="hBtnClose" name="close" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="submit" name="hBtnSimpan" class="btn btn-primary">Simpan</button>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <!-- FORM ALERT -->
    <form action="adm_users.php" method="POST">
        <div id="loginAlert" class="modal" style="width:100%;height:100vh; background-color: rgba(0, 0, 0, 0.5);display:<?php echo $propAlert; ?>;" tabindex="-1" role="dialog" >
            <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">System Information</h5>
                    </div>
                    <div class="modal-body">
                        <p><?php echo $alertMessages; ?></p>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" id="BtnClose" name="BtnClose" class="btn btn-primary" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    </form>

</body>

</html>