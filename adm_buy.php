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

$stmt = $conn->prepare("SELECT ROW_NUMBER() OVER(ORDER BY a.pembelian_id asc) AS no, a.pembelian_id, a.no_faktur, DATE_FORMAT(a.tanggal_pembelian, '%d-%m-%Y') tanggal,
TIME_FORMAT(a.created_at, '%H:%i:%s') waktu, 
(select nama_suplier from suplier where suplier_id = a.suplier_id) nama_suplier, 
(select sum(qty) from pembelian_detail 
where pembelian_id = a.pembelian_id) total_item, total total_harga FROM pembelian a
LIMIT $paginationStart, $limit");
$stmt->execute();
$data = $stmt->fetchAll();


// Get total records
$stmt = $conn->prepare("SELECT count(pembelian_id) AS id FROM pembelian");
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



// Form Edit Klik Tombol Hapus Data User
if (isset($_POST['hBtnSimpan'])) {

    $pembelian_id = $_POST['hpembelian_id'];

    try {
        $arrData = [
            'pembelian_id' => $pembelian_id,
        ];

        $sql = "delete from pembelian where pembelian_id = :pembelian_id";
        $stmt = $conn->prepare($sql);
        $stmt->execute($arrData);

        $sql = "delete from pembelian_detail where pembelian_id = :pembelian_id";
        $stmt = $conn->prepare($sql);
        $stmt->execute($arrData);

        $alertMessages = "Data berhasil dihapus";
    } catch (PDOException $e) {
        $alertMessages = $e->getMessage();
    }

    $propAlert = 'block';
}

if (isset($_POST['BtnDetail'])) {
    $pembelian_id = $_POST['id'];
    $_SESSION['ppembelian_id'] = $pembelian_id;

    // echo "ID " . $penjualan_id;
    header("Location: adm_buy_view.php");
}


if (isset($_POST['iBtnTambah'])) {
    header("Location: adm_buy_add.php");
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
    <title>Transaksi Penjualan</title>
</head>

<!-- JAVASCRIPT & JQUERY -->
<script>
    $(document).ready(function() {

        $('#iBtnTambah').click(function() {
            document.getElementById('formTambah').style.display = 'block';
        });

        $("#tables").on('click', '#BtnEdit', function(event) {
            var produk_id = $(this).closest('tr').find('td:eq(1)').text();
            var nama_produk = $(this).closest('tr').find('td:eq(2)').text();
            var nama_kategori = $(this).closest('tr').find('td:eq(3)').text();
            var satuan = $(this).closest('tr').find('td:eq(4)').text();
            var harga_beli = $(this).closest('tr').find('td:eq(5)').text();
            var harga_jual = $(this).closest('tr').find('td:eq(6)').text();
            var jumlah_produk = $(this).closest('tr').find('td:eq(7)').text();
            var kategori_id = $(this).closest('tr').find('td:eq(8)').text();

            document.getElementById('formEdit').style.display = 'block';
            document.getElementById('eproduk_id').value = produk_id;
            document.getElementById('enama_produk').value = nama_produk;
            document.getElementById('ekategori_id').value = kategori_id;
            document.getElementById('esatuan').value = satuan;
            document.getElementById('eharga_beli').value = harga_beli;
            document.getElementById('eharga_jual').value = harga_jual;
            document.getElementById('ejumlah_produk').value = jumlah_produk;


            event.preventDefault();
        });

        $("#tables").on('click', '#BtnHapus', function(event) {
            document.getElementById('formHapus').style.display = 'block';
            var pembelian_id = $(this).closest('tr').find('td:eq(1)').text();
            document.getElementById('hpembelian_id').value = pembelian_id;
            document.getElementById('KonfirmasiHapus').innerText = 'Anda yakin akan menghapus data transaksi penjualan ini ?';
            event.preventDefault();
        });

        $("#cUser").on("keyup", function() {
            var value = $(this).val().toLowerCase();
            $("#tableCariUser tr").filter(function() {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
            });
        });

        $('#iBtnClose').click(function(event) {
            document.getElementById("inama_produk").required = false;
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
                            <div class="menu_detail_icon menu_detail_data_active">
                                <i class="fa fa-money"></i> &nbsp; Pembelian &nbsp; <i class="fa fa-check"></i>
                            </div>
                        </a>
                    </div>
                    <div class="menu_detail_data">
                        <a href="adm_sale.php">
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
                            <i class="fa fa-shopping-bag"></i>
                        </div>
                        <div class="forms_content_title_description">
                            Data Pembelian
                        </div> -->
                    </div>

                    <div class="forms_content_box">
                        <!-- TOOLBAR TAMBAH USER -->
                        <div class="forms_content_box_toolbar">
                            <div class="forms_content_box_toolbar_item">
                                <!-- <form action="" method="POST">
                                    <button name="iBtnTambah" type="submit" class="btn btn-default btn-sm" style="background-color:#5074dc;color:white;font-size:13px">
                                        <span class="fa fa-plus"></span> Tambah Pembelian
                                    </button>
                                </form> -->
                                <form action="adm_buy_add.php" method ="POST">
                                    <!-- <button name="BtnCari" id="BtnCari" type="submit" class="btn btn-primary" style="padding-left:50px;padding-right:50px;" data-dismiss="modal">Cari</button> -->
                                    <button name="BtnViewPenjualan" id="BtnViewPenjualan" type="submit" class="btn btn-default btn-md" style="margin-left:10px;padding-right:30px;background-color:#5074dc;color:white;font-size:14px;">
                                        <span class="fa fa-arrow-left" style="padding-right:10px;"></span> input Pembelian 
                                    </button>
                                </form>
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

                                            <th hidden>pembelian id</th>
                                            <th>No Faktur</th>
                                            <th style="width:50px;text-align:center;">Tanggal</th>
                                            <th style="width:50px;text-align:center;">Waktu</th>
                                            <th>Nama Supplier</th>
                                            <th style="width:50px;text-align:center;">Total Item</th>
                                            <th style="width:50px;text-align:center;">Total Belanja</th>
                                            <th style="width:50px;text-align:center;">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tableCariUser">
                                        <?php
                                        foreach ($data as $row) {
                                            $no = $row['no'];

                                            $pembelian_id = $row['pembelian_id'];
                                            $no_faktur = $row['no_faktur'];
                                            $tanggal = $row['tanggal'];
                                            $waktu = $row['waktu'];
                                            $nama_suplier = $row['nama_suplier'];
                                            $total_item = number_format($row['total_item']);
                                            $total_harga = number_format($row['total_harga']);

                                        ?>
                                            <tr>

                                                <td style="text-align:center"><?php echo $no; ?></td>

                                                <td hidden><?php echo $pembelian_id; ?></td>
                                                <td ><?php echo $no_faktur; ?></td>
                                                <td style="text-align: center;"><?php echo $tanggal; ?></td>
                                                <td style="text-align: center;"><?php echo $waktu; ?></td>
                                                <td><?php echo $nama_suplier; ?></td>
                                                <td style="text-align: right;"><?php echo $total_item; ?></td>
                                                <td style="text-align: right;"><?php echo $total_harga; ?></td>


                                                <td style="text-align: center;">
                                                        <!-- <div style="display:flex;flex-direction:row;"> -->
                                                            <form action="" method="POST">
                                                            <input type="text" hidden name="id" value=<?php echo  $row['pembelian_id'] ?> />
                                                            <button type="submit" class="btn btn-default btn-sm" name="BtnDetail" style="background-color:#5074dc;color:white;font-size:13px;margin-right:5px;">
                                                                <span class="fa fa-search "></span>
                                                            </button>
                                                            </form>
                                                            <!-- <button id="BtnHapus" type="submit" class="btn btn-default btn-sm" style="background-color:rgb(133, 47, 47);color:white; font-size:13px;">
                                                                <span class="fa fa-trash "></span>
                                                            </button> -->
                                                        <!-- </div> -->
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
                                                <a class="page-link" style="font-size:13px;" href="adm_buy.php?page=<?= $i; ?>"> <?= $i; ?> </a>
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

   
    <!-- FORM HAPUS -->
    <form action="adm_buy.php" method="POST">
        <div id="formHapus" class="modal"  style="width:100%;height:100vh; background-color: rgba(0, 0, 0, 0.5);" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Form Hapus Pembelian</h5>
                    </div>
                    <div class="modal-body">
                        <div>
                            <input type="hidden" id="hpembelian_id" name="hpembelian_id">
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
    <form action="adm_buy.php" method="POST">
        <div id="loginAlert" class="modal" style="width:100%;height:100vh; background-color: rgba(0, 0, 0, 0.5);display:<?php echo $propAlert; ?>;" tabindex="-1" role="dialog">
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