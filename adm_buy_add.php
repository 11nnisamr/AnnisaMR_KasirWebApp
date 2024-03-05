<?php
    
    include 'config/dbconnection.php';
    error_reporting(0);
    session_start();

    if (!isset($_SESSION['pnama_petugas'])) {
        header("Location: index.php");
        exit(); // Terminate script execution after the redirect
    }
    
    $user_id = $_SESSION['pid_petugas'];
    $pnama_petugas = $_SESSION['pnama_petugas'];
    $plevel = $_SESSION['plevel'];
    $propMessages="none";
    $TextMessages="";    
    $totalrec = 0;
    try {
    
        $sql = "select 0 suplier_id, '-- Pilih Suplier --' nama_suplier from DUAL union SELECT a.suplier_id,
                    a.nama_suplier FROM suplier a
                    order by suplier_id";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $dataSuplier = $stmt->fetchAll();
    
        } catch (PDOException $e) {
            $alertMessages = $e->getMessage();
            $propAlert = 'block';
        }
    
    if(isset($_POST["suplier"])){
        
        $suplier_id = $_POST["suplier"];
        $no_faktur = $_POST["no_faktur"];
        $tgl_pembelian = $_POST["tgl_pembelian"];

        try {

            $arrData = [
                'suplier_id' => $suplier_id,
            ];
    
            $sql = "SELECT ROW_NUMBER() OVER(ORDER BY a.produk_id asc) AS no, a.produk_id,
                        a.nama_produk, a.harga_beli,a.harga_jual FROM produk a,suplier b
                        where   a.suplier_id = b.suplier_id
                        and     b.suplier_id = :suplier_id
                        order by a.produk_id";
            $stmt = $conn->prepare($sql);
            $stmt->execute($arrData);
            $dataProduk = $stmt->fetchAll();
        
            } catch (PDOException $e) {
                echo $e->getMessage();
            }
    
    }
            
    if(isset($_POST["BtnSimpanSuplier"])){
        
        $nama_suplier = $_POST["nama_suplier"];
        $alamat_suplier = $_POST["alamat_suplier"];
        $telp_suplier = $_POST["telp_suplier"];
        
        try {
    
        $arrData = [
            'nama_suplier'=> $nama_suplier,
            'alamat_suplier' => $alamat_suplier,
            'telp_suplier' => $telp_suplier,
        ];


        $sql = "INSERT INTO `suplier` (`toko_id`, `produk_id`, `nama_suplier`, `tlp_hp`, `alamat_suplier`, `created_at`) VALUES ( '1', '0', :nama_suplier, :telp_suplier,:alamat_suplier,  current_timestamp())";
        $stmt = $conn->prepare($sql);
        $stmt->execute($arrData);
        $penjualan_id = $conn->lastInsertId();
        $TextMessages = "Data Supplier telah tersimpan.";
        } catch (PDOException $e) {
            $TextMessages = $e->getMessage();
        }

        $propMessages ='block';

        try {
    
            $sql = "select 0 suplier_id, '-- Pilih Suplier --' nama_suplier from DUAL union SELECT a.suplier_id,
                        a.nama_suplier FROM suplier a
                        order by suplier_id";
            $stmt = $conn->prepare($sql);
            $stmt->execute();
            $dataSuplier = $stmt->fetchAll();
            } catch (PDOException $e) {
                // echo $e->getMessage();
                $TextMessages = $e->getMessage();
                
            }


    }

    if(isset($_POST["BtnSimpanProduct"])){
        
        $list_suplier = $_POST["list_suplier"];
        $nama_produk = $_POST["nama_produk"];
        $kategori_id = $_POST["kategori_id"];
        $satuan = $_POST["satuan"];
        $harga_beli = $_POST["harga_beli"];
        $harga_jual = $_POST["harga_jual"];

        // echo $list_suplier.'-'.$nama_produk.'-'.$kategori_id.'-'.$satuan.'-'.$harga_beli.'-'.$harga_jual;
      
        try {
    
        $arrData1 = [
            'list_suplier'=> $list_suplier,
            'nama_produk' => $nama_produk,
            'kategori_id' => $kategori_id,
            'satuan' => $satuan,
            'harga_beli' => $harga_beli,
            'harga_jual' => $harga_jual,
        ];


        $sql = "INSERT INTO `produk` ( `toko_id`, `suplier_id`, `nama_produk`, `kategori_id`, `satuan`, `harga_beli`, `harga_jual`,  `created_at`) VALUES ('1', :list_suplier, :nama_produk, :kategori_id, :satuan, :harga_beli, :harga_jual, current_timestamp())";
        $stmt = $conn->prepare($sql);
        $stmt->execute($arrData1);
        $produk_id = $conn->lastInsertId();
        $TextMessages = "Data Product telah tersimpan.";
        } catch (PDOException $e) {
            $TextMessages = $e->getMessage();
        }
        
        $propMessages ='block';

        try {
            $suplier_id = $list_suplier;
            $arrData = [
                'suplier_id' => $suplier_id,
            ];
    
            $sql = "SELECT ROW_NUMBER() OVER(ORDER BY a.produk_id asc) AS no, a.produk_id,
                        a.nama_produk, a.harga_beli FROM produk a,suplier b
                        where   a.suplier_id = b.suplier_id
                        and     b.suplier_id = :suplier_id
                        order by a.produk_id";
            $stmt = $conn->prepare($sql);
            $stmt->execute($arrData);
            $dataProduk = $stmt->fetchAll();
        
            } catch (PDOException $e) {
                $TextMessages = $e->getMessage();
            }
    

    }


    if(isset($_POST["BtnSimpanPembelian"])){
        
        $no_faktur = $_POST["no_faktur"];
        $tgl_pembelian = $_POST["tgl_pembelian"];
        $suplier_id = $_POST["suplier"];

        if ($no_faktur =='') {
            $TextMessages="isi no faktur terlebih dahulu.";
            $propMessages="block";       
        }else if ($tgl_pembelian =='') {
            $TextMessages="isi tgl pembelian terlebih dahulu.";
            $propMessages="block";
        }else if ($suplier_id ==0){
            $TextMessages="Pilih Suplier terlebih dahulu.";
            $propMessages="block";
        }else
        {
            $totalrec =  $_POST["total_record"];
            $count = $totalrec;
            $total =0;
            
        
            try {

                $count = $totalrec;
                // echo "total record ".$count;
                for ($i=1;$i<=$count;$i++){
                    $jumlah_dibeli= $_POST["jumlah_dibeli".$i];
                    $sub_total = str_replace(',','',$jumlah_dibeli);
                    // $sub_total=str_replace(",","",$sub_total);
                    // echo $sub_total;
                    $total=$total+$sub_total;
                }
                
                if ($total == 0 ) {
                    
                    $TextMessages = "Isi Jumlah Produk yang akan dibeli.";
                    
                } else
                {    
                
                        $arrData = [
                        'user_id'=> $user_id,
                        'no_faktur' => $no_faktur,
                        'tgl_pembelian' => $tgl_pembelian,
                        'suplier_id' => $suplier_id,
                        'total' => $total,
                        ];
        
        
                        $sql = "INSERT INTO `pembelian` (`toko_id`, `user_id`, `no_faktur`, `tanggal_pembelian`, `suplier_id`, `total`, `bayar`, `sisa`, `keterangan`, `created_at`) 
                        VALUES ( '1', :user_id, :no_faktur, :tgl_pembelian, :suplier_id, :total, :total, '0', 'Testing', current_timestamp())";
                        $stmt = $conn->prepare($sql);
                        $stmt->execute($arrData);
                        $pembelian_id = $conn->lastInsertId();
                
                    for ($i=1;$i<=$count;$i++){

                        $qty = $_POST["jumlah_dibeli".$i];

                        if ($qty > 0) { 

                            $produk_id = $_POST["produk_id".$i];
                            $harga_jual = $_POST["harga_jual".$i];
                            $harga_beli = $_POST["harga_beli".$i];
                            $harga_beli = str_replace(',','',$harga_beli);

                            
                            $sub_total = $sub_total+$harga_beli;

                            $arrData1 = [
                                'pembelian_id'=> $pembelian_id,
                                'produk_id' => $produk_id,
                                'qty' => $qty,
                                'harga_beli' => $harga_beli,
                            ];
                            // echo $_POST["produk_id".$i]."-".$_POST["nama_produk".$i]."-".$_POST["harga_jual".$i]."-".$_POST["qty".$i]."-".$_POST["sub_total".$i];
                            $sql = "INSERT INTO `pembelian_detail` (`pembelian_id`, `produk_id`, `qty`, `harga_beli`, `created_at`) 
                            VALUES (:pembelian_id,:produk_id, :qty, :harga_beli, current_timestamp())";
                            $stmt = $conn->prepare($sql);
                            $stmt->execute($arrData1);
                
                            $arrData2 = [
                                'produk_id' => $produk_id,
                                'qty' => $qty,
                            ];
                
                            $sql = "update produk set jumlah_produk = jumlah_produk + :qty where produk_id  = :produk_id";
                            $stmt = $conn->prepare($sql);
                            $stmt->execute($arrData2);
                        }

                    }
                
                    $suplier_id = 0;
                    $no_faktur="";
                    $tgl_pembelian ="";

                    $sql = "SELECT ROW_NUMBER() OVER(ORDER BY a.produk_id asc) AS no, a.produk_id,
                                a.nama_produk, a.harga_beli FROM produk a,suplier b
                                where   a.suplier_id = b.suplier_id
                                and     b.suplier_id = 0
                                order by a.produk_id";
                    $stmt = $conn->prepare($sql);
                    $stmt->execute();
                    $dataProduk = $stmt->fetchAll();

                    $TextMessages = "Transaksi Pembelian telah tersimpan.";
                }
            } catch (PDOException $e) {
                $TextMessages = $e->getMessage();
            }

        }    
        $propMessages ='block';


        
    }

    // if(isset($_POST["BtnClear"])){
        
    
    // }


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
    <title>Transaksi Input Pembelian</title>
</head>

<!-- JAVASCRIPT & JQUERY -->
<script>
    $(document).ready(function() {
        
        $("#tableProduk").on('change', '#jumlah_dibeli', function(event) {
            var no = $(this).closest('tr').find('td:eq(0)').text();
            no = no.replace(' ','');
            // var harga_beli = $(this).closest('tr').find('td:eq(2)').text();
            var harga_beli = $(this).closest('tr').find("td:eq(2) input[type='text']").val();
            // alert(harga_beli);
                harga_beli=  parseInt(harga_beli.replace(',',''));
            var jumlah_dibeli = $(this).closest('tr').find("td:eq(3) input[type='number']").val();
                jumlah_dibeli = parseInt(jumlah_dibeli);
            var total =  harga_beli*jumlah_dibeli;
            // alert(total);
            var vtotal = "#total"+no;
            // alert(vtotal);
            $(vtotal).html(total.toLocaleString());

            // alert("totalnya adalah :"+ total);
            
        });

        $("#searchProduct").on("keyup", function() {
            var value = $(this).val().toLowerCase();
            $("#tBodyProduct tr").filter(function() {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
            });
        });

        $('#BtnInputSuplier').click(function(e) {
            document.getElementById('formInputSuplier').style.display = 'block';
            e.preventDefault();
        });

        $('#BtnInputProduct').click(function(e) {
            document.getElementById('formInputProduct').style.display = 'block';
            e.preventDefault();
        });

        $('#BtnCloseMessages').click(function(e) {
            document.getElementById('FormMessages').style.display = 'none';
            // e.preventDefault();
        });

        $('#BtnSimpanProduct').click(function(e) {
            var suplier = document.getElementById('list_suplier').value;
            var nama_produk = document.getElementById('nama_produk').value;
            var kategori_id = document.getElementById('kategori_id').value;
            var satuan = document.getElementById('satuan').value;
            var harga_beli = document.getElementById('harga_beli').value;
            var harga_jual = document.getElementById('harga_jual').value;
            if (suplier==0){
                document.getElementById('FormMessages').style.display = 'block';
                document.getElementById('Konfirmasi').innerText = "Pilih suplier terlebih dahulu.";
                e.preventDefault();
            }else if (nama_produk ==""){
                document.getElementById('FormMessages').style.display = 'block';
                document.getElementById('Konfirmasi').innerText = "Isi Nama Produk terlebih dahulu.";
                e.preventDefault();
            }else if (kategori_id ==0){
                document.getElementById('FormMessages').style.display = 'block';
                document.getElementById('Konfirmasi').innerText = "Pilih kategori terlebih dahulu.";
                e.preventDefault();
            }else if (satuan ==0){
                document.getElementById('FormMessages').style.display = 'block';
                document.getElementById('Konfirmasi').innerText = "Pilih satuan terlebih dahulu.";
                e.preventDefault();
            }else if (harga_beli ==0 || harga_beli ==""){
                document.getElementById('FormMessages').style.display = 'block';
                document.getElementById('Konfirmasi').innerText = "Harga Beli harus > 0";
                e.preventDefault();
            }else if (harga_jual ==0 || harga_jual ==""){
                document.getElementById('FormMessages').style.display = 'block';
                document.getElementById('Konfirmasi').innerText = "Harga Jual harus > 0";
                e.preventDefault();
            }

        });

        $('#BtnSimpanSuplier').click(function(e) {
            var nama_suplier = document.getElementById('nama_suplier').value;
            var alamat_suplier = document.getElementById('alamat_suplier').value;
            var telp_suplier = document.getElementById('telp_suplier').value;
            
            if (nama_suplier==""){
                document.getElementById('FormMessages').style.display = 'block';
                document.getElementById('Konfirmasi').innerText = "Isi Supplier terlebih dahulu.";
                e.preventDefault();
            }else if (alamat_suplier ==""){
                document.getElementById('FormMessages').style.display = 'block';
                document.getElementById('Konfirmasi').innerText = "Isi Alamat Supplier terlebih dahulu.";
                e.preventDefault();
            }else if (telp_suplier ==""){
                document.getElementById('FormMessages').style.display = 'block';
                document.getElementById('Konfirmasi').innerText = "Isi No Telp terlebih dahulu.";
                e.preventDefault();
            }

        });

    });    


    function use_number(node) {
        var empty_val = false;
        const value = node.value;
        if (node.value == '')
            empty_val = true;
        node.type = 'number';
        if (!empty_val)
            node.value = Number(value.replace(/,/g, '')); // or equivalent per locale
    }

    function use_text(node) {
        var empty_val = false;
        const value = Number(node.value);
        if (node.value == '')
            empty_val = true;
        node.type = 'text';
        if (!empty_val)
            node.value = value.toLocaleString('en');  // or other formatting
    }
</script>

<body style="font-size: 13px;">

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
        <div class="layout_form">
            <div class="forms">
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
                <div class="forms_content" >
                    <div  style="margin:20px 10px 20px 10px;padding:20px 20px 40px 20px;">
                    <form action="adm_buy.php" method ="POST">
                        <div class="col" style="display:flex;justify-content:start;margin-bottom:40px;">
                        <button name="BtnViewPenjualan" id="BtnViewPenjualan" type="submit" class="btn btn-default btn-md" style="margin-right:5px;padding-right:15px;background-color:#5074dc;color:white;font-size:14px;">
                            <span class="fa fa-table" style="padding-right:10px;"></span> History Pembelian 
                        </button>
                        
                    </form>
                    <form action="adm_buy_add.php" method="POST">
                        <button id="BtnInputSuplier" type="button"  class="btn btn-default btn-md" style="padding-right:15px;background-color:#5074dc;color:white;font-size:14px;height:35px;"><span class="fa fa-plus" style="padding-right:10px;"></span>Supplier</button>
                        <button id="BtnInputProduct" type="button" class="btn btn-default btn-md" style="padding-right:25px;background-color:#5074dc;color:white;font-size:14px;height:35px;"><span class="fa fa-plus" style="padding-right:10px;"></span>Product</button>
                        </div>
                        <div class="row mb-3" style="display: flex; align-items:center;">
                            <div class="col-2" >
                                <label class="form-cotrol" > No Faktur</label>
                            </div>
                            <div class="col-3">
                                <input type="text" id="no_faktur" placeholder="PO-NM-YYYYMMDD-XXX" class="form-control form-control-sm" name="no_faktur" value="<?php echo $no_faktur; ?>">
                            </div>
                        </div>
                        <div class="row mb-3" style="display: flex; align-items:center;">
                            <div class="col-2" >
                                <label class="form-cotrol"> Tgl Pembelian</label>
                            </div>
                            <div class="col-3">
                                <input type="date" id="tgl_pembelian" class="form-control form-control-sm" name="tgl_pembelian" value="<?php echo $tgl_pembelian; ?>">
                            </div>
                        </div>
                        <div class="row mb-3" style="display: flex; align-items:center;">
                            <div class="col-2" >
                                <label class="form-cotrol"> Suplier</label>
                            </div>
                            <div class="col-3">
                                <select id="suplier" class="form-select form-select-sm" name="suplier" onchange="this.form.submit()">
                                    <?php    
                                        foreach ($dataSuplier as $row) {
                                            $suplier_idx = $row['suplier_id'];
                                            $nama_suplier = $row['nama_suplier'];
                                        ?>
                                            <option <?php if ($suplier_id ==$suplier_idx) {echo "selected";} ?> value="<?php echo $suplier_idx; ?>">
                                                <?php echo $nama_suplier; ?>
                                            </option>
                                        <?php
                                        }
                                        ?>
                                </select>
                            </div>
                            <!-- <div class="col">
                                <button id="BtnInputSuplier" type="button" class="btn btn-primary"><i class="fa fa-plus">&nbsp;&nbsp; </i>Supplier</button>
                                <button id="BtnInputProduct" type="button" class="btn btn-primary"><i class="fa fa-plus">&nbsp;&nbsp; </i>Product</button>
                            </div> -->
                        </div>
                        <div>
                            <div class="row">
                                <div style="display:flex;flex-direction:row;width:100%;justify-content:end;">
                                    <div style="position:relative;">                                                                
                                        <input id="searchProduct" type="text" placeholder="Type product name here.." style="padding-left:10px;width:300px;border-top-left-radius:10px;border-color:rgb(247, 242, 242);border-width:0.5px; height:30px;" id="inama_produk" name="inama_produk" >
                                        <div style="position:absolute;top:3px;right:8px;">
                                            <i class="fa fa-search"></i>                                                        
                                        </div>                                                            
                                    </div>                                                                
                                </div>                    
                            </div>
                        <div class="row mb-3">
                            <div class="col">
                                <table id="tableProduk" class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th style="text-align:center;width:30px;">No</th>
                                            <th style="text-align:center;">Produk</th>
                                            <th style="width:150px;text-align:left;">Harga Beli</th>
                                            <th style="width:50px;text-align:center;">Jumlah</th>
                                            <th style="width:180px;text-align:center;">Total</th>
                                            <th hidden>Harga Jual</th>
                                            <th hidden>produk id</th>
                                            <!-- <th style="width:100px;text-align:center;">Action</th> -->
                                        </tr>
                                    </thead>
                                    <tbody id="tBodyProduct">
                                        <?php
                                        $totalrec=0;
                                        foreach ($dataProduk as $row) {
                                            $no = $row['no'];
                                            $produk_id = $row['produk_id'];
                                            $nama_produk = $row['nama_produk'];
                                            $harga_beli = $row['harga_beli'];
                                            $harga_jual = $row['harga_jual'];
                                            $totalrec=$totalrec+1;
                                        ?>  
                                        <tr>
                                        <td  style="text-align:center;"> <?php echo $no; ?></td>
                                        <td ><input type="text" style="border:none;border-style:none;outline:none;" name=<?php echo "nama_produk".$no; ?> value=<?php echo $nama_produk; ?>></td>
                                        <td  style="text-align:right;"><input type="text" style="border:none;border-style:none;outline:none;" name=<?php echo "harga_beli".$no; ?> value=<?php echo number_format($harga_beli); ?>></td>
                                        <td><input id="jumlah_dibeli" name=<?php echo "jumlah_dibeli".$no;?>  min=0 type="number" style="text-align:right;width:80px;" value=0></td>
                                        <td id=<?php echo "total".$no;?>  style="text-align:right;">0</td>     
                                        <td hidden><input type="text"  name=<?php echo "harga_jual".$no; ?> value=<?php echo $harga_jual; ?>></td>
                                        <td hidden><input type="text"  name=<?php echo "produk_id".$no; ?> value=<?php echo $produk_id; ?>></td>
                                        </tr>    
                                        <?php } 
                                        // echo 'totrec'.$totalrec;
                                        ?>
                                    </tbody>                                        
                                </table>
                                <input name="total_record" type="text" hidden value=<?php echo $totalrec; ?>> 
                            </div>
                        </div>
                        </div>
                        <div class="row mb-3 justify-content-end">
                            <!-- <button name="BtnClear" type="submit" class="btn btn-primary col-2" style="margin-right:10px; width:150px;"><i class="fa fa-trash">&nbsp;&nbsp; </i>Clear</button> -->
                            <button name="BtnSimpanPembelian" type="submit" class="btn btn-primary col-2" style="margin-right:10px; width:200px;"><i class="fa fa-save">&nbsp;&nbsp; </i>Simpan</button>
                            <!-- <button name="BtnBayarPembelian" type="button" class="btn btn-primary col-2" style="margin-right:10px;"><i class="fa fa-gratipay">&nbsp;&nbsp; </i>Bayar</button> -->
                        </div>
                        <div id="formInputSuplier" class="modal" style="width:100%;height:100vh; background-color: rgba(0, 0, 0, 0.5);display:<?php echo $propPilihProduct; ?>" tabindex="-1" role="dialog">
                            <div class="modal-dialog modal-dialog-centered modal-md" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Input Supplier</h5>
                                        <button id="BtnClose"class="btn-close"  aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="row mb-3">
                                            <div class="col-4" >
                                                <label class="form-cotrol" > Nama Supplier</label>
                                            </div>
                                            <div class="col">
                                                <input type="text" id="nama_suplier" class="form-control" name="nama_suplier" value=<?php $no_faktur; ?>>
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <div class="col-4" >
                                                <label class="form-cotrol"> Alamat</label>
                                            </div>
                                            <div class="col">
                                                <input type="text" id="alamat_suplier" class="form-control" name="alamat_suplier">
                                            </div>
                                        </div>    
                                        <div class="row mb-3">
                                            <div class="col-4" >
                                                <label class="form-cotrol">No Telp</label>
                                            </div>
                                            <div class="col">
                                                <input type="text" id="telp_suplier" class="form-control" name="telp_suplier" <?php $tgl_pembelian; ?>>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col" style="display:flex;justify-content:right;">
                                                <button id="BtnSimpanSuplier" name="BtnSimpanSuplier" type="submit" type="button" class="btn btn-primary"><i class="fa fa-save">&nbsp;&nbsp; </i>Simpan</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div id="formInputProduct" class="modal" style="width:100%;height:100vh; background-color: rgba(0, 0, 0, 0.5);display:<?php echo $propPilihProduct; ?>" tabindex="-1" role="dialog">
                            <div class="modal-dialog modal-dialog-centered modal-md" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Input Product</h5>
                                        <button id="BtnClose"class="btn-close"  aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="row mb-3">
                                            <div class="col-4" >
                                                <label class="form-cotrol" > Nama Supplier</label>
                                            </div>
                                            <div class="col">
                                                <select id="list_suplier" class="form-select" name="list_suplier">
                                                    <?php    
                                                        foreach ($dataSuplier as $row) {
                                                            $suplier_idx = $row['suplier_id'];
                                                            $nama_suplier = $row['nama_suplier'];
                                                        ?>
                                                            <option <?php if ($suplier_id ==$suplier_idx) {echo "selected";} ?> value="<?php echo $suplier_idx; ?>">
                                                                <?php echo $nama_suplier; ?>
                                                            </option>
                                                        <?php
                                                        }
                                                        ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <div class="col-4" >
                                                <label class="form-cotrol"> Nama Produk</label>
                                            </div>
                                            <div class="col">
                                                <input type="text" id="nama_produk" class="form-control" name="nama_produk">
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <div class="col-4" >
                                                <label class="form-cotrol"> Nama Kategori</label>
                                            </div>
                                            <div class="col">
                                                <select name="kategori_id" id="kategori_id" style="width: 100%;height: 40px;border: 0.5px solid #b1afaf;padding: 5px 10px;font-size: 14px;border-radius: 5px;background: transparent;outline: none;transition: .3s;" required>
                                                    <?php
                                                    $sql = "select 0 kategori_id, '--Pilih Kategori--' nama_kategori from DUAL union 
                                                        SELECT kategori_id, nama_kategori
                                                                FROM produk_kategori";
                                                    $stmt = $conn->prepare($sql);
                                                    $stmt->execute();
                                                    $data = $stmt->fetchAll();

                                                    foreach ($data as $row) {
                                                        $kategori_id = $row['kategori_id'];
                                                        $nama_kategori = $row['nama_kategori'];
                                                    ?>
                                                        <option value="<?php echo $kategori_id; ?>">
                                                            <?php echo $nama_kategori; ?>
                                                        </option>
                                                    <?php
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>    
                                        <div class="row mb-3">
                                            <div class="col-4" >
                                                <label class="form-cotrol"> Satuan</label>
                                            </div>
                                            <div class="col">
                                                <select id="satuan" class="form-select" name="satuan">
                                                    <option value="0">-- Pilih Satuan -- </option>
                                                    <option value="PCS">PCS </option>
                                                    <option value="PCS">Liters </option>
                                                    <option value="PCS">Gram </option>
                                                </select>
                                            </div>
                                        </div>    
                                        <div class="row mb-3">
                                            <div class="col-4" >
                                                <label class="form-cotrol">Harga Beli</label>
                                            </div>
                                            <div class="col">
                                                <input type="number" id="harga_beli" value="0" class="form-control" name="harga_beli" style="text-align: right;">
                                            </div>
                                        </div>
                                        <div class="row mb-4">
                                            <div class="col-4" >
                                                <label class="form-cotrol">Harga Jual</label>
                                            </div>
                                            <div class="col">
                                                <input type="number" id="harga_jual" value="0" class="form-control" name="harga_jual" style="text-align: right;">
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col" style="display:flex;justify-content:right;">
                                                <button id="BtnSimpanProduct" name="BtnSimpanProduct" type="submit" type="button" class="btn btn-primary"><i class="fa fa-save">&nbsp;&nbsp; </i>Simpan</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>    
                    </div>
                </div>
            </div>    
        </div>    
    </div>
    
    <div id="FormMessages" class="modal" tabindex="-1" role="dialog" style="width:100%;height:100vh; background-color: rgba(0, 0, 0, 0.5);display:<?php echo $propMessages; ?>;">
        <div class="modal-dialog modal-sm modal-dialog-centered modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">System Information</h5>
                </div>
                <div class="modal-body">
                    <span id="Konfirmasi"><?php echo $TextMessages; ?></span>
                </div>
                <div class="modal-footer">
                    <button id="BtnCloseMessages"  class="btn btn-primary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
       

</body>    