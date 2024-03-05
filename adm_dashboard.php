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

$stmt = $conn->prepare("SELECT ROW_NUMBER() OVER(ORDER BY user_id) AS no, user_id id_petugas,username,password,nama_lengkap as nama_petugas,access_level as level FROM user order by user_id LIMIT $paginationStart, $limit");
$stmt->execute();
$data = $stmt->fetchAll();


// Get total records
$stmt = $conn->prepare("SELECT count(user_id) AS id FROM user");
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

// PIE QUERY
// produk terlaris
$stmt = $conn->prepare("select c.nama_kategori,sum(qty) qty from penjualan_detail a, produk b,produk_kategori c
where a.produk_id = b.produk_id
AND   b.kategori_id = c.kategori_id
group by c.nama_kategori
order by qty desc");

$stmt->execute();
$data = $stmt->fetchAll();


// produk terlaris
$stmt = $conn->prepare("SELECT nama_produk,sum(jumlah_produk) stock FROM produk group by nama_produk order by jumlah_produk desc");

$stmt->execute();
$data1 = $stmt->fetchAll();


// transaksi per hari ini
$stmt = $conn->prepare("select count(1) total_transaksi from penjualan where date_format(created_at,'%Y-%m-%d') = CURRENT_DATE()");

$stmt->execute();
$countdata = $stmt->fetchAll();
$TotalTransaksi = $countdata[0]['total_transaksi'];


// transaksi per hari ini
$stmt = $conn->prepare("select sum(jumlah_produk) as total_stock from produk");

$stmt->execute();
$countdata = $stmt->fetchAll();
$TotalStock = $countdata[0]['total_stock'];

// Stock Masuk Hari ini
$stmt = $conn->prepare("select ifnull(sum(qty),0) as stock_in from pembelian_detail where date_format(created_at,'%Y-%m-%d') = CURRENT_DATE()");

$stmt->execute();
$countdata = $stmt->fetchAll();
$StockIn = $countdata[0]['stock_in'];


$sql = "select nama_kategori, (select ifnull(sum(a.qty),0) from penjualan_detail a, produk b
where a.produk_id = b.produk_id
and   b.kategori_id = c.kategori_id) as qty from produk_kategori c
group by nama_kategori";

$stmt = $conn->prepare($sql);
$stmt->execute();
$data0 = $stmt->fetchAll();


$produk_kategori= array();
$qty = array();

foreach ($data0 as $row) {

    $produk_kategori[] =$row['nama_kategori'];     
    $qty[]= $row['qty'];
    
}

$jsonpk = json_encode($produk_kategori);
$jsonqty = json_encode($qty);

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
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <link rel="icon" type="image/x-icon" href="images/sppico.png">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@2.8.0"></script>
    <title>Dashboard</title>
</head>

<!-- JAVASCRIPT & JQUERY -->
<script>
    $(document).ready(function() {

        var ctx = document.getElementById('myChart').getContext('2d');
            var chart = new Chart(ctx, {
              // The type of chart we want to create
              type: 'bar',
  
              // The data for our dataset
              data: {
                labels: <?php echo $jsonpk; ?>,
                datasets: [
                  {
                    label: 'Jumlah kategori Produk yang terjual dibulan ini',
                    borderColor: "red",
                    backgroundColor:'blue',
                    data: <?php echo $jsonqty; ?>,
                  },
                ]
              },

              // Configuration options go here
              options: {}
            });

        google.charts.load('current', {'packages':['corechart']});
        google.charts.setOnLoadCallback(drawChart);
        
        function drawChart() {
            var data = google.visualization.arrayToDataTable([
            ['nama_kategori', 'qty'],
            <?php
            foreach ($data as $row) {
                echo "['".$row['nama_kategori']."', ".$row['qty']."],";                                   
            }
            ?>
            ]);

            var options = {
                width: 550,
                height: 250,
                is3D: true,

            };
            
            var chart = new google.visualization.PieChart(document.getElementById('piechart'));
            chart.draw(data, options);
        }    

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
                            <div class="menu_detail_icon menu_detail_data_active">
                                <i class="fa fa-home"></i> &nbsp; Dashboard &nbsp; <i class="fa fa-check"></i>
                            </div>
                        </a>
                    </div>
                    <div class="menu_detail_title">
                        SETUP
                    </div>

                    <div class="menu_detail_data">
                        <a href="adm_users.php">
                            <div class="menu_detail_icon">
                                <i class="fa fa-lock"></i> &nbsp; User &nbsp;
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
                        <div>
                            <!-- <img src="images/slogan.png" alt="" style="height:60px; top:-20px;left:20px;position:absolute;border-radius:100%;"> -->
                        </div>    
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

                    <div style="display:flex;flex-direction:row;justify-content:space-between;width:100%;height:150px;align-items:center;margin-top:20px;">
                        <div style="display:flex;flex-direction:column;flex:1;height:150px;box-shadow: rgba(0, 0, 0, 0.35) 0px 5px 15px;margin:10px 20px 10px 20px; 
                        border-radius:5px; background-color:#7c91d1;color:white;">
                            <div style="font-size:28px;padding-left:20px;margin-top:10px;">
                                <label for="inama_produk" class="form-label"><?php echo $TotalTransaksi;?></label>   
                            </div>
                            <div style="font-size:20px;padding-left:20px;">
                                <label for="inama_produk" class="form-label">Transaksi Per hari ini</label>   
                            </div>
                        </div>
                        <div style="display:flex;flex-direction:column;flex:1;height:150px;box-shadow: 
                        rgba(0, 0, 0, 0.35) 0px 5px 15px;margin:10px 20px 10px 20px;border-radius:5px;background-color:#7c91d1;color:white;">
                            <div style="font-size:28px;padding-left:20px;margin-top:10px;">
                                <label for="inama_produk" class="form-label"><?php echo $TotalStock;?></label>   
                            </div>
                            <div style="font-size:20px;padding-left:20px;">
                                <label for="inama_produk" class="form-label">Total Stock</label>   
                            </div>   
                        </div>
                        <div style="display:flex;flex-direction:column;flex:1;height:150px;
                        box-shadow: rgba(0, 0, 0, 0.35) 0px 5px 15px;margin:10px 20px 10px 20px;border-radius:5px;background-color:#7c91d1;color:white;">
                        <div style="font-size:28px;padding-left:20px;margin-top:10px;">
                                <label for="inama_produk" class="form-label"><?php echo $StockIn;?></label>   
                            </div>
                            <div style="font-size:20px;padding-left:20px;">
                                <label for="inama_produk" class="form-label">Stock Masuk Hari ini</label>   
                            </div>        
                        </div>
                    </div>

                    <div style="display:flex;flex-direction:row;justify-content:space-between;width:100%;align-items:center;margin-top:20px;">
                        <div style="display:flex;flex-direction:column;flex:1;box-shadow: rgba(0, 0, 0, 0.35) 0px 5px 15px;margin:10px 20px 10px 20px; border-radius:5px; background-color:black;">
                            <div style="display:flex;font-size:18px;padding-left:10px; background-color:#5074dc;align-items:center;color:white;padding:5px 5px 5px 10px; border-top-left-radius:5px;border-top-right-radius:5px;">
                                <label>Produk Terlaris</label>   
                            </div>
                            <div id="piechart" style="display:flex;width:100%;"></div>

                        </div>
                        <div style="display:flex;flex-direction:column;flex:1;height:290px;box-shadow: rgba(0, 0, 0, 0.35) 0px 5px 15px;margin:10px 20px 10px 20px;border-radius:5px;">
                            <div style="display:flex;font-size:18px;padding-left:10px; background-color:orange;align-items:center;color:white;padding:5px 5px 5px 10px; border-top-left-radius:5px;border-top-right-radius:5px;">
                                <label>Stock Product</label>   
                            </div>
                            <div style="display:flex;flex-direction:column;width:100%;height:270px;overflow-y: scroll;">
                            <?php
                            foreach ($data1 as $row) {
                                ?>
                                <div style="display:flex;flex-direction:row;width:100%; align-items:center;">
                                    <div style="flex:1;padding:10px 10px 10px 20px;"> <?php echo $row['nama_produk']; ?></div> 
                                    <div style="padding-right:20px;"> <?php echo $row['stock']; ?></div>
                                </div>                               
                            <?php }
                            ?>
                            </div>   
                        </div>
                    </div>
                    <div style="margin-left:20px;margin-right:20px;margin-top:20px; ">
                        <canvas  id="myChart" width="700px" height="200px"></canvas>
                    </div>


                </div>
            </div>
        </div>
    </div>



</body>

</html>