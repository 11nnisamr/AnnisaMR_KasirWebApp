
<?php

include 'config/dbconnection.php';

error_reporting(0);

session_start();

$user_id = $_SESSION['pid_petugas'];

if (!isset($_SESSION['pnama_petugas'])) {
    header("Location: index.php");
    exit(); // Terminate script execution after the redirect
}


$penjualan_id = $_SESSION['ppenjualan_id'];
$pnama_petugas = $_SESSION['pnama_petugas'];
$plevel = $_SESSION['plevel'];
$propAlert = 'none';
$alertMessages = "";
$propPilihProduct='none';

try {
    
    $sql = "SELECT ROW_NUMBER() OVER(ORDER BY a.produk_id asc) AS no, a.produk_id,
                a.nama_produk, a.harga_jual, a.jumlah_produk FROM produk a
                where   a.jumlah_produk > 0
                order by a.produk_id";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $data = $stmt->fetchAll();

    } catch (PDOException $e) {
        $alertMessages = $e->getMessage();
        $propAlert = 'block';
    }


if(isset($_POST["BtnCari"])){
    
    try {
    
        $sql = "SELECT ROW_NUMBER() OVER(ORDER BY a.produk_id asc) AS no, a.produk_id,
                    a.nama_produk, a.harga_jual, a.jumlah_produk FROM produk a
                    where   a.jumlah_produk > 0
                    order by a.produk_id";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $data = $stmt->fetchAll();
    
        } catch (PDOException $e) {
            $alertMessages = $e->getMessage();
            $propAlert = 'block';
        }

    $propPilihProduct = 'block';
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
    <title>Transaksi Input Penjualan</title>
</head>

<!-- JAVASCRIPT & JQUERY -->
<script>
    $(document).ready(function() {
        var i=1;
        var jumlah=0;
        var total=0;
        
        // $('#BtnCari').click(function(e) {
            // document.getElementById('formPilihProduct').style.display = 'block';
            
            // e.preventDefault();
        // });

        $("#searchProduct").on("keyup", function() {
            var value = $(this).val().toLowerCase();
            $("#tBodyPilihProduct tr").filter(function() {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
            });
        });


        $('#BtnSubmit').click(function(e) {
            
            var wrong = "false";
            var vRows = 0;
            $('#listProduct tr').each(function(row, tr){            
                var produk_id = $(this).closest('tr').find('td:eq(1)').text();
                var nama_produk = $(this).closest('tr').find('td:eq(2)').text();
                var harga_jual = $(this).closest('tr').find('td:eq(3)').text();
                var jumlah_produk = parseInt($(this).closest('tr').find('td:eq(4)').text());
                var qty = $(this).closest('tr').find("td:eq(5) input[type='number']").val();
                var sub_total = harga_jual.replace(",","")*qty;      
                if (qty > 0)
                {
                    vRows++;  
                    var  htmlvar = "";
                    htmlvar = htmlvar.concat("<td hidden><input type='text' name='produk_id"+i+"' value='"+produk_id+"'></td>",
                                        "<td hidden><input type='text' name='produk_rows' value='"+i+"'></td>",
                                        "<td style='text-align:center; width:50px;'>"+i+"</td>",
                                        "<td><input style='border-style:none; width:250px;' readOnly type='text' name='nama_produk"+i+"' value='"+nama_produk+"'></td>",
                                        "<td><input style='text-align:right;width:80px;border-style:none;' readOnly type='text' name='harga_jual"+i+"' value='"+harga_jual+"'></td>",
                                        "<td><input style='text-align:right;width:80px;border-style:none;' readOnly type='text' name='qty"+i+"' value='"+qty+"'></td>",
                                        "<td><input style='text-align:right;width:80px;border-style:none;' readOnly type='text' name='sub_total"+i+"' value='"+sub_total.toLocaleString()+"'></td>",
                                        '<td style="text-align:center;"><button id="BtnClear" name = "BtnClear" type="submit" class="btn btn-default btn-sm" style="background-color:rgb(150, 0, 47);color:white; font-size:13px;"> <span class="fa fa-trash"></span></button></td>'
                                        );
                    $('#tRows'+i).html(htmlvar);
                    $('#listPenjualan').append('<tr id="tRows'+(i+1)+'"></tr>');
                    jumlah = jumlah + parseInt(qty);
                    total = total + parseInt(sub_total);
                    document.getElementById('input_jumlah').value = parseInt(jumlah);
                    document.getElementById('input_total').value = parseInt(total).toLocaleString('en-ID');
                    i++;                                     
                }    
            })

            if (vRows==0){
                document.getElementById('loginAlert').style.display = 'block';
                document.getElementById('Konfirmasi').innerText = 'Pilih Produk terlebih dahulu.';
            }    
            else if (wrong =="false" && vRows > 0) {
                document.getElementById('formPilihProduct').style.display = 'none';
            }   
            
            e.preventDefault();
        })

        $('#BtnCetak').click(function() {
            // alert("tes");
            var discount = document.getElementById('input_discount').value;
            discount = parseInt(discount.replace(',',''));

            var total = document.getElementById('input_total').value;
            total = parseInt(total.replace(',',''));
            
            var bayar = document.getElementById('input_bayar').value;
            bayar = parseInt(bayar.replace(',',''));

            document.getElementById('pbayar').value = bayar;     
            document.getElementById('pkembali').value = bayar +discount-total;  
            document.getElementById('pdiscount').value = discount; 

            if (total == 0 ){
                document.getElementById('loginAlert').style.display = 'block';
                document.getElementById('Konfirmasi').innerText = 'Tidak ada transaksi yang akan diproses..';
            }
            else if (bayar == 0 || bayar < (total -discount) && total > 0){
                document.getElementById('loginAlert').style.display = 'block';
                document.getElementById('Konfirmasi').innerText = 'Pembayaran tidak mencukupi.';
            }
            else if (total > 0 && bayar > 0)
            {
                document.getElementById('formKonfirmasi').style.display = 'block';
                document.getElementById('KonfirmasiCetak').innerText = 'Anda yakin akan memproses transaksi ini ?';
            }

        });
        
        $("#listPenjualan").on('click', '#BtnHapus', function(event) {
            var produk_id = $(this).closest('tr').find('td:eq(1)').text();
            var nama_produk = $(this).closest('tr').find('td:eq(2)').text();
            document.getElementById('hkategori_id').value = kategori_id;
            document.getElementById('formHapus').style.display = 'block';
            document.getElementById('KonfirmasiHapus').innerText = 'Anda yakin akan menghapus data produk ' + nama_produk + '?';
            event.preventDefault();
        });


        $('#BtnCloseAlert').click(function(event) {
            document.getElementById('loginAlert').style.display = 'none';
            event.preventDefault();
        });

        $('#BtnCloseCetak').click(function(event) {
            document.getElementById('formKonfirmasi').style.display = 'none';
            event.preventDefault();
        
        });

        $('#BtnClose').click(function(event) {
            
            document.getElementById('formPilihProduct').style.display = 'none';
            event.preventDefault();
        });

        $('#input_pelanggan').change(function(event) {
            document.getElementById('pelanggan_id').value = document.getElementById('input_pelanggan').value;
            event.preventDefault();
        });
        
        $('#input_bayar').change(function(event) {
            
            var discount = document.getElementById('input_discount').value;
            discount  = parseInt(discount.replace(',',''));

            var total = document.getElementById('input_total').value;
            total = parseInt(total.replace(',',''));
            
            var bayar = document.getElementById('input_bayar').value;
            bayar = parseInt(bayar.replace(',',''));
            // alert(bayar+" - "+total+" - "+discount);

            if (bayar == 0 || bayar < (total -discount) ){
                document.getElementById('loginAlert').style.display = 'block';
                document.getElementById('Konfirmasi').innerText = 'Pembayaran tidak mencukupi.';
                document.getElementById('input_bayar').value= 0;
            }else {
                var kembali = bayar+discount-total;
                // alert(kembali);
                document.getElementById('input_kembali').value = kembali.toLocaleString(); 
            }
            event.preventDefault();
        });

        // $('#input_discount').change(function(event) {
        //     var discount = document.getElementById('input_discount').value;
        //     bayar = parseInt(discount.replace(',',''));

        //     var total = document.getElementById('input_total').value;
        //     total = parseInt(total.replace(',',''));
            
        //     var bayar = document.getElementById('input_bayar').value;
        //     bayar = parseInt(bayar.replace(',',''));

        //     if (bayar == 0 || bayar < (total -discount) ){
        //         document.getElementById('loginAlert').style.display = 'block';
        //         document.getElementById('Konfirmasi').innerText = 'Pembayaran tidak mencukupi.';
        //         // document.getElementById('input_discount').value= 0;
        //         document.getElementById('input_bayar').value= 0;
        //     }else {
        //         var kembali = bayar-total-discount;
        //         document.getElementById('input_kembali').value = kembali.toLocaleString('en-ID'); 
        //     }
        //     event.preventDefault();
        // });

        $("#listPenjualan").on('click', '#BtnClear', function(event) {
            $(this).closest("tr").remove();
            event.preventDefault();
        });

        $('#BtnSimpan').click(function(event) {
            // alert("tes");
            document.getElementById('formKonfirmasi').style.display = "none";
            // event.preventDefault();
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
                    
                    <div class="menu_detail_title">
                        SETUP
                    </div>
                    
                    <div class="menu_detail_data">
                        <a href="csh_customers.php">
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
                <div class="menu_detail">
                    <div class="menu_detail_title">
                        TRANSAKSI
                    </div>
                    <div class="menu_detail_data">
                        <a href="csh_sale_add.php">
                            <div class="menu_detail_icon menu_detail_data_active">
                                <i class="fa fa-shopping-bag"></i> &nbsp; Penjualan &nbsp; <i class="fa fa-check"></i>
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
                <div class="forms_content" style="overflow:hidden;">

                    <div style="display:flex;flex-direction:column;margin:20px 10px 0px 10px;">

                        <!-- TOOLBAR TAMBAH USER -->
                        <div class="forms_content_box_toolbar" style="justify-content:left;">
                            <div class="forms_content_box_toolbar_item" style="margin-bottom:20px;">
                                <!-- <div style="position:absolute;top:7px;left:35px;color:white;">
                                    <i class="fa fa-search"></i>                                                        
                                </div>  -->
                                <form action="csh_sale_add.php" method ="POST">
                                    <!-- <button name="BtnCari" id="BtnCari" type="submit" class="btn btn-primary" style="padding-left:50px;padding-right:50px;" data-dismiss="modal">Cari</button> -->
                                    <button name="BtnCari" id="BtnCari" type="submit" class="btn btn-default btn-md" style="padding-right:30px;background-color:#5074dc;color:white;font-size:14px;">
                                        <span class="fa fa-plus" style="padding-right:10px;"></span> Input Produk 
                                    </button>
                                </form>
                                <form action="csh_sale.php" method ="POST">
                                    <!-- <button name="BtnCari" id="BtnCari" type="submit" class="btn btn-primary" style="padding-left:50px;padding-right:50px;" data-dismiss="modal">Cari</button> -->
                                    <button name="BtnViewPenjualan" id="BtnViewPenjualan" type="submit" class="btn btn-default btn-md" style="margin-left:10px;padding-right:30px;background-color:#5074dc;color:white;font-size:14px;">
                                        <span class="fa fa-table" style="padding-right:10px;"></span> History Penjualan 
                                    </button>
                                </form>
                            </div>
                        </div>
                            <div style="display:flex;flex-direction:row;margin:0px 10px 0px 10px;">
                                <div style="display:flex;flex:4;flex-direction:column;margin:0px 10px 0px 0px;">
                                    <div style="margin:10px 10px 10px 10px;font-size:14px;">
                                        <form action="adm_sale_print.php" target="_blank" method="POST">                    
                                        <input type="text" name="pdiscount" id="pdiscount" hidden  />
                                        <input type="text" name="pbayar" id ="pbayar"  hidden/>
                                        <input type="text" name="pkembali" id="pkembali"  hidden/>
                                        <table id="listPenjualan" class="table table-bordered table-hover">
                                            <thead>
                                                <tr>
                                                    <th hidden>product id</th>
                                                    <th hidden>secreet</th>
                                                    <th style="text-align:center;width:30px;">No</th>
                                                    <th style="width:200px;text-align:left;">Produk</th>
                                                    <th style="width:50px;text-align:center;">Harga</th>
                                                    <th style="width:50px;text-align:center;">Jumlah</th>
                                                    <th style="width:50px;text-align:center;">Total</th>
                                                    <th style="width:30px;text-align:center;">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr id="tRows1">
                                                </tr>
                                            </tbody>    
                                        </table>
                                        <div id="formKonfirmasi" class="modal " tabindex="-1" role="dialog" style="width:100%;height:100vh; background-color: rgba(0, 0, 0, 0.5);display:<?php echo $propHapus; ?>;">
                                            <div class="modal-dialog modal-dialog-centered" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">System Confirmation</h5>
                                                    </div>
                                                    <div class="modal-body">
                                                        <span id="KonfirmasiCetak">...</span>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button id="BtnCloseCetak" name="close" class="btn btn-secondary">Tidak</button>
                                                        <button type="submit" name="BtnSimpan" id="BtnSimpan" class="btn btn-primary" style="padding:0x 20px 0px 20px;">Ya</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        </form>
                                    </div>
                                </div>
                                <div style="display:flex;flex:1.8;flex-direction:column;margin:10px 10px 0px 0px;width:100%;box-shadow: rgba(0, 0, 0, 0.35) 0px 5px 15px;">
                                    <div style="margin:30px 10px 10px 20px;font-size:14px;">
                                        <div style="display:flex;flex-direction:row; margin:0px 10px 15px 0px;align-items:center;">
                                            <div>
                                                <label class="form-label" style="width:100px;">
                                                    Member
                                                </label>
                                            </div>
                                            <!-- <div> -->
                                                <select name="input_pelanggan" id="input_pelanggan" class="form-select form-select-md" style="font-size:14px;" required>
                                                    <?php
                                                    $sql = "select 0 pelanggan_id, '-- Pilih Member --' nama_pelanggan from DUAL union 
                                                        SELECT pelanggan_id, nama_pelanggan
                                                                FROM pelanggan";
                                                    $stmt = $conn->prepare($sql);
                                                    $stmt->execute();
                                                    $data1 = $stmt->fetchAll();

                                                    foreach ($data1 as $row) {
                                                        $pelanggan_id = $row['pelanggan_id'];
                                                        $nama_pelanggan = $row['nama_pelanggan'];
                                                    ?>
                                                        <option value="<?php echo $pelanggan_id; ?>">
                                                            <?php echo $nama_pelanggan; ?>
                                                        </option>
                                                    <?php
                                                    }
                                                    ?>
                                                </select>
                                            <!-- </div> -->
                                        </div>
                                        <div style="display:flex;flex-direction:row; margin:0px 10px 15px 0px;align-items:center;">
                                            <div>
                                                <label class="form-label" style="width:100px;">
                                                    Jumlah
                                                </label>
                                            </div>
                                            <input type="text" readonly class="form-control form-control-sm"   name="input_jumlah" id="input_jumlah" value =0 style="text-align:right;padding-right:10px;">            
                                        </div>
                                        <div style="display:flex;flex-direction:row; margin:0px 10px 15px 0px;align-items:center;">
                                            <div>
                                                <label class="form-label" style="width:100px;">
                                                    Total
                                                </label>
                                            </div>
                                            <input type="text" readonly class="form-control form-control-sm"   name="input_total" id="input_total" value =0 style="text-align:right;padding-right:10px;">            
                                        </div>
                                        <div style="display:flex;flex-direction:row; margin:0px 10px 15px 0px;align-items:center;">
                                            <div>
                                                <label class="form-label" style="width:100px;">
                                                    Discount
                                                </label>
                                            </div>
                                            <input  type="text" inputmode="numeric" class="form-control form-control-sm"   name="input_discount" id="input_discount" value =0 style="text-align:right;" min=0 onfocus="use_number(this)" onblur="use_text(this)">            
                                            
                                        </div>
                                        <div style="display:flex;flex-direction:row; margin:0px 10px 15px 0px;align-items:center;">
                                            <div>
                                                <label class="form-label" style="width:100px;">
                                                    Bayar
                                                </label>
                                            </div>
                                            <input type="text" inputmode="numeric" class="form-control form-control-sm"   name="input_bayar" id="input_bayar" value =0 style="text-align:right;" min=0 onfocus="use_number(this)" onblur="use_text(this)">            
                                            
                                        </div>
                                        <div style="display:flex;flex-direction:row; margin:0px 10px 15px 0px;align-items:center;">
                                            <div>
                                                <label class="form-label" style="width:100px;">
                                                    Kembali
                                                </label>
                                            </div>
                                            
                                            <input type="text" readonly class="form-control form-control-sm"   name="input_kembali" id="input_kembali" value =0 style="text-align:right;padding-right:10px;">            
                                            
                                        </div>
                                        
                                                
                                        <div style="display:flex;flex-direction:row; margin:0px 10px 15px 0px;align-items:center;justify-content:center;flex:1;">
                                            <form action="csh_sale_add.php" method="POST">
                                                <button name="BtnSelesai" type="submit"  class="btn btn-secondary" style="padding-left:40px;padding-right:40px;margin-right:5px;width:100%;">Kosongkan</button>                    
                                            </form>          
                                            <button name="BtnCetak" class="btn btn-primary" style="padding-left:35px;padding-right:35px;margin-left:5px;width:100%;" data-dismiss="modal" id="BtnCetak">Cetak</button>
                                            <!-- <a href="adm_sale_print.php" target="_BLANK">TES PRINT</a>        -->
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
    
        <!-- FORM TAMBAH -->
        <div id="formPilihProduct" class="modal" style="width:100%;height:100vh; background-color: rgba(0, 0, 0, 0.5);display:<?php echo $propPilihProduct; ?>" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Input Produk</h5>
                        <button id="BtnClose"class="btn-close"  aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                              <!-- TABLE DATA USERS     -->
                            <div class="forms_content_box_tables">
                                <div style="display:flex;flex-direction:row;width:100%;justify-content:end;">
                                    <div style="position:relative;">                                                                
                                        <input id="searchProduct" type="text" placeholder="Type product name here.." style="padding-left:10px;width:300px;border-top-left-radius:10px;border-color:rgb(247, 242, 242);border-width:0.5px;height:30px;" id="inama_produk" name="inama_produk" >
                                        <div style="position:absolute;top:3px;right:8px;">
                                            <i class="fa fa-search"></i>                                                        
                                        </div>                                                            
                                    </div>                                                                
                                </div>
                                <div style="display: block;overflow-x:auto; white-space: nowrap;width:100%;">
                                    <table id="listProduct" class="table table-bordered table-hover">
                                        <thead>
                                            <tr>
                                                <!-- <th>#</th> -->
                                                <th style="text-align:center;width:50px;">No</th>

                                                <th hidden>penjualan id</th>

                                                <th >Produk</th>
                                                <th style="width:50px;text-align:center;">Harga Jual</th>
                                                <th style="width:50px;text-align:center;">Stock</th>
                                                <th style="width:50px;text-align:center;">Jumlah</th>
                                                <!-- <th style="width:50px;text-align:center;">Action</th> -->

                                            </tr>
                                        </thead>
                                        <tbody id="tBodyPilihProduct">
                                            <?php
                                            foreach ($data as $row) {
                                                $no = $row['no'];    
                                                $produk_id = $row['produk_id'];
                                                $nama_produk = $row['nama_produk'];
                                                $harga_jual = number_format($row['harga_jual']);
                                                $jumlah_produk = number_format($row['jumlah_produk']);
                                                  
                                            ?>
                                                <tr>
                                                    <!-- <td style="text-align:center;width:20px;" class="tableProduct"><input type="checkbox"></td> -->
                                                    
                                                    <td style="text-align:center"><?php echo $no; ?></td>

                                                    <td hidden class="table_produk_id"><?php echo $produk_id; ?></td>
                                                    <td class="table_nama_produk"><?php echo $nama_produk; ?></td>
                                                    <td class="table_harga_jual" style="text-align: right; width:150px;"><?php echo $harga_jual; ?></td>
                                                    <td class="table_jumlah_produk" style="text-align: right;"><?php echo $jumlah_produk; ?></td>
                                                    <td class="table_qty"><input type="number"  min=0 id="qty" style="text-align:right;width:80px;" value=0> </td>
                                                </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>                
                                </div>
                                <div style="display:flex;width:100%;justify-content:end;"><button id="BtnSubmit" class="btn btn-primary" style="margin:20px 0px 0px 0px; width:150px;">Submit</button></div>
                            </div>
                        </div>
                    </div>
                    
                </div>
            </div>
        </div>
    
        
    <!-- FORM HAPUS -->
    <form action="adm_sale_add.php" method="POST">
        <div id="formHapus" class="modal " tabindex="-1" role="dialog" style="display:<?php echo $propHapus; ?>;">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Form Hapus Penjualan</h5>
                    </div>
                    <div class="modal-body">
                        <div>
                            <input type="hidden" id="hproduk_id" name="hproduk_id">
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

    <div id="loginAlert" class="modal" tabindex="-1" role="dialog" style="width:100%;height:100vh; background-color: rgba(0, 0, 0, 0.5);display:<?php echo $propAlert; ?>;">
        <div class="modal-dialog  modal-dialog-centered modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">System Information</h5>
                </div>
                <div class="modal-body">
                    <span id="Konfirmasi"><?php echo $alertMessages; ?></span>
                </div>
                <div class="modal-footer">
                    <button id="BtnCloseAlert"  class="btn btn-primary" >Close</button>
                </div>
            </div>
        </div>
    </div>
    
</body>

</html>