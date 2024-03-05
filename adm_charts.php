<?php

include 'config/dbconnection.php';

error_reporting(0);

session_start();

$sql = "select c.nama_kategori, sum(a.qty) qty from penjualan_detail a, produk b, produk_kategori c 
where a.produk_id = b.produk_id
and   b.kategori_id = c.kategori_id
group by c.nama_kategori";
$stmt = $conn->prepare($sql);
$stmt->execute();
$data = $stmt->fetchAll();


$produk_kategori= array();
$produk_kategori[]=0;
$qty = array();
$qty[]=0;

foreach ($data as $row) {

    $produk_kategori[] =$row['nama_kategori'];     
    $qty[]= $row['qty'];
    
}


?>

    
</body>
</html>

<html>
    <head>
        <style>body{width: 800px;}</style>
        <script src="https://cdn.jsdelivr.net/npm/chart.js@2.8.0"></script>
    </head>
    <body>
        <div>
            <canvas  id="myChart" width="700px" height="300px"></canvas>
        </div>      
        <?php
            $jsonpk = json_encode($produk_kategori);
            $jsonqty = json_encode($qty);
        ?>
        <script>
            var ctx = document.getElementById('myChart').getContext('2d');
            var chart = new Chart(ctx, {
              // The type of chart we want to create
              type: 'pie',

              // The data for our dataset
              data: {
                labels: <?php echo $jsonpk; ?>,
                datasets: [
                  {
                    label: 'Jumlah kategori Produk yang terjual dibulan ini',
                    borderColor: "yellow",
                    data: <?php echo $jsonqty; ?>,
                  },
                ]
              },

              // Configuration options go here
              options: {}
            });
        </script>
    </body>
</html>
