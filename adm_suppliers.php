<!-- HALAMAN ADMIN - MENU SETUP USER -->

<?php

include 'config/dbconnection.php';

error_reporting(0);

session_start();


if (!isset($_SESSION['pnama_petugas'])) {
    header("Location: index.php");
    exit(); // Terminate script execution after the redirect
}

// --------------- Paging ----------------//

if (isset($_POST['records-limit'])) {
    $_SESSION['records-limit'] = $_POST['records-limit'];
}

$limit = isset($_SESSION['records-limit']) ? $_SESSION['records-limit'] : 7;
$page = (isset($_GET['page']) && is_numeric($_GET['page'])) ? $_GET['page'] : 1;
$paginationStart = ($page - 1) * $limit;

$stmt = $conn->prepare("SELECT ROW_NUMBER() OVER(ORDER BY suplier_id) AS no, suplier_id,nama_suplier,alamat_suplier, tlp_hp  FROM suplier order by suplier_id LIMIT $paginationStart, $limit");
$stmt->execute();
$data = $stmt->fetchAll();


// Get total records
$stmt = $conn->prepare("SELECT count(suplier_id) AS id FROM suplier");
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


    $nama_suplier = $_POST['inama_suplier'];
    $alamat_suplier = $_POST['ialamat_suplier'];
    $tlp_hp = $_POST['itlp_hp'];

    if ($level == "none") {
        $propAlert = 'block';
        $alertMessages = "Data tidak berhasil disimpan, pilih Level terbelih dahulu.";
    } else {
        try {
            $arrData = [
                'nama_suplier' => $nama_suplier,
                'alamat_suplier' => $alamat_suplier,
                'tlp_hp' => $tlp_hp
            ];

            $sql = "INSERT INTO suplier (toko_id,nama_suplier, alamat_suplier,tlp_hp,created_at) VALUES (1,:nama_suplier, :alamat_suplier, :tlp_hp,sysdate())";
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

    $suplier_id = $_POST['esuplier_id'];
    $nama_suplier = $_POST['enama_suplier'];
    $alamat_suplier = $_POST['ealamat_suplier'];
    $tlp_hp = $_POST['etlp_hp'];

    try {
        $arrData = [
            'suplier_id' => $suplier_id,
            'nama_suplier' => $nama_suplier,
            'alamat_suplier' => $alamat_suplier,
            'tlp_hp' => $tlp_hp,
        ];

        $sql = "update suplier set nama_suplier = :nama_suplier, alamat_suplier = :alamat_suplier, tlp_hp =:tlp_hp where suplier_id = :suplier_id";
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

    $suplier_id = $_POST['hsuplier_id'];

    try {
        $arrData = [
            'suplier_id' => $suplier_id,
        ];

        $sql = "delete from suplier where suplier_id = :suplier_id";
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
    <title>Setup Supplier</title>
</head>

<!-- JAVASCRIPT & JQUERY -->
<script>
    $(document).ready(function() {

        $('#iBtnTambah').click(function() {
            document.getElementById('formTambah').style.display = 'block';
        });

        $("#tables").on('click', '#BtnEdit', function(event) {
            var suplier_id = $(this).closest('tr').find('td:eq(1)').text();
            var nama_suplier = $(this).closest('tr').find('td:eq(2)').text();
            var alamat_suplier = $(this).closest('tr').find('td:eq(3)').text();
            var tlp_hp = $(this).closest('tr').find('td:eq(4)').text();
            document.getElementById('formEdit').style.display = 'block';
            document.getElementById('esuplier_id').value = suplier_id;
            document.getElementById('enama_suplier').value = nama_suplier;
            document.getElementById('ealamat_suplier').value = alamat_suplier;
            document.getElementById('etlp_hp').value = tlp_hp;

            event.preventDefault();
        });

        $("#tables").on('click', '#BtnHapus', function(event) {
            document.getElementById('formHapus').style.display = 'block';
            var suplier_id = $(this).closest('tr').find('td:eq(1)').text();
            var nama_suplier = $(this).closest('tr').find('td:eq(2)').text();
            document.getElementById('hsuplier_id').value = id_petugas;
            document.getElementById('KonfirmasiHapus').innerText = 'Anda yakin akan menghapus data suplier ' + nama_suplier + '?';
            event.preventDefault();
        });

        $("#cUser").on("keyup", function() {
            var value = $(this).val().toLowerCase();
            $("#tableCariUser tr").filter(function() {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
            });
        });

        $('#iBtnClose').click(function(event) {
            document.getElementById("inama_suplier").required = false;
            document.getElementById("ialamat_suplier").required = false;
            document.getElementById("itlp_hp").required = false;
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
                            <div class="menu_detail_icon">
                                <i class="fa fa-lock"></i> &nbsp; User
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
                            <div class="menu_detail_icon menu_detail_data_active">
                                <i class="fa fa-building-o"></i> &nbsp; Supplier &nbsp; <i class="fa fa-check"></i>
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
                        
                    </div>

                    <div class="forms_content_box">

                        <!-- TOOLBAR TAMBAH USER -->
                        <div class="forms_content_box_toolbar">
                            <div class="forms_content_box_toolbar_item">
                                <!-- <button id="iBtnTambah" type="submit" class="btn btn-default btn-md" style="padding-right:30px;background-color:#5074dc;color:white;font-size:14px;">
                                    <span class="fa fa-plus" style="padding-right:10px;"></span> Input Supplier
                                </button> -->
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

                                <table id="tables" class="table table-bordered table-hover">
                                    <thead>
                                        <tr>
                                            <th style="text-align:center;width:50px;">No</th>
                                            <th hidden>supplier id</th>
                                            <th>Nama Supplier</th>
                                            <th>Alamat</th>
                                            <th>No Telp</th>
                                            <th style="width:50px;text-align:center;">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tableCariUser">
                                        <?php
                                        foreach ($data as $row) {
                                            $no = $row['no'];
                                            $suplier_id = $row['suplier_id'];
                                            $nama_suplier = $row['nama_suplier'];
                                            $alamat_suplier = $row['alamat_suplier'];
                                            $tlp_hp =  $row['tlp_hp'];
                                        ?>
                                            <tr>
                                                <td style="text-align:center"><?php echo $no; ?></td>
                                                <td hidden><?php echo $suplier_id; ?></td>
                                                <td><?php echo $nama_suplier; ?></td>
                                                <td><?php echo $alamat_suplier; ?></td>
                                                <td><?php echo $tlp_hp; ?></td>
                                                <td>
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
                                                <a class="page-link" style="font-size:13px;" href="adm_suppliers.php?page=<?= $i; ?>"> <?= $i; ?> </a>
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
    <form action="adm_suppliers.php" method="POST">
        <div id="formTambah" class="modal" style="width:100%;height:100vh; background-color: rgba(0, 0, 0, 0.5);" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Form Tambah Supplier</h5>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="mb-3">
                                <label for="nama_suplier" class="form-label">Nama Supplier</label>
                                <input type="text" class="form-control" id="inama_suplier" name="inama_suplier" required>
                            </div>
                            <div class="mb-3">
                                <label for="ialamat_suplier" class="form-label">Alamat Supplier</label>
                                <input type="text" class="form-control" name="ialamat_suplier" id="ialamat_suplier" autocomplete=”off” readonly onclick="this.removeAttribute('readOnly');" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="itlp_hp" class="form-label">No Telp</label>
                            <input type="text" class="form-control" id="itlp_hp" name="itlp_hp" required>
                        </div>
                        <div class="mb-3">
                            <label for="ialamat_suplier" class="form-label">Produk </label>
                            <select class="form-select" name="ialamat_suplier" id="ialamat_suplier"> </select>                            
                        </div>
                        <div class="mb-3">
                            <label for="ialamat_suplier" class="form-label">Satuan</label>
                            <input type="text" class="form-control" name="ialamat_suplier" id="ialamat_suplier" autocomplete=”off” readonly onclick="this.removeAttribute('readOnly');" required>
                        </div>
                        <div class="mb-3">
                            <label for="ialamat_suplier" class="form-label">Harga Beli</label>
                            <input type="text" class="form-control" name="ialamat_suplier" id="ialamat_suplier" autocomplete=”off” readonly onclick="this.removeAttribute('readOnly');" required>
                        </div>
                        <div class="mb-3">
                            <label for="ialamat_suplier" class="form-label">Harga Jual</label>
                            <input type="text" class="form-control" name="ialamat_suplier" id="ialamat_suplier" autocomplete=”off” readonly onclick="this.removeAttribute('readOnly');" required>
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
    <form action="adm_suppliers.php" method="POST">
        <div id="formEdit" class="modal" style="width:100%;height:100vh; background-color: rgba(0, 0, 0, 0.5);" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Form Edit Supplier</h5>
                    </div>
                    <div class="modal-body">
                        <div>
                            <input type="hidden" id="esuplier_id" name="esuplier_id">
                        </div>
                        <div class="mb-3">
                            <label for="enama_suplier" class="form-label">Nama Suplier</label>
                            <input type="text" class="form-control" id="enama_suplier" name="enama_suplier">
                        </div>
                        <div class="mb-3">
                            <label for="ealamat_suplier" class="form-label">Alamat Suplier</label>
                            <input type="text" class="form-control" id="ealamat_suplier" name="ealamat_suplier" autocomplete=”off” readonly onclick="this.removeAttribute('readOnly');">
                        </div>
                        <div class="mb-3">
                            <label for="etlp_hp" class="form-label">No Telp</label>
                            <input type="text" class="form-control" id="etlp_hp" name="etlp_hp">
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
    <form action="adm_suppliers.php" method="POST">
        <div id="formHapus" class="modal" style="width:100%;height:100vh; background-color: rgba(0, 0, 0, 0.5);" tabindex="-1" role="dialog" style="display:<?php echo $propHapus; ?>;">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Form Hapus Supplier</h5>
                    </div>
                    <div class="modal-body">
                        <div>
                            <input type="hidden" id="hsuplier_id" name="hsuplier_id">
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
    <form action="adm_suppliers.php" method="POST">
        <div id="loginAlert" class="modal" style="width:100%;height:100vh; background-color: rgba(0, 0, 0, 0.5); display:<?php echo $propAlert; ?>;" tabindex="-1" role="dialog">
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