<?php

include 'config/dbconnection.php';

error_reporting(0);

session_start();

$user_id = $_SESSION['pid_petugas'];

if(isset($_POST["BtnSimpan"])){
    $pelanggan_id = $_POST["ppelanggan"];
    $bayar = $_POST["pbayar"];
    $kembali = $_POST["pkembali"];
    $discount = $_POST["pdiscount"];
    if ($pelanggan_id==''){
        $pelanggan_id=0;
    }

    try {

        $count = $_POST["produk_rows"];
        // echo $count;
        for ($i=1;$i<=$count;$i++){
            $sub_total= $_POST["sub_total".$i];
            
            $sub_total=str_replace(",","",$sub_total);
            // echo $sub_total;
            $total=$total+$sub_total;
        }
        // echo $total;
        
        $arrData = [
            'user_id'=> $user_id,
            'pelanggan_id' => $pelanggan_id,
            'total' => $total,
        ];


        $sql = "INSERT INTO `penjualan` ( `toko_id`, `user_id`, `tanggal_penjualan`, `pelanggan_id`, `total`, `bayar`, `sisa`, `keterangan`, `created_at`) 
                VALUES ( 1, :user_id, sysdate(), :pelanggan_id, :total, :total, 0, '', current_timestamp())";
        $stmt = $conn->prepare($sql);
        $stmt->execute($arrData);
        $penjualan_id = $conn->lastInsertId();

        for ($i=1;$i<=$count;$i++){
            $produk_id = $_POST["produk_id".$i];
            $qty = $_POST["qty".$i];
            $harga_jual = $_POST["harga_jual".$i];
            $harga_jual =str_replace(",","",$harga_jual);
            $sub_total = $_POST["sub_total".$i];
            $sub_total = str_replace(",","",$sub_total);
            $harga_beli = 0;
            $arrData1 = [
                'penjualan_id'=> $penjualan_id,
                'produk_id' => $produk_id,
                'qty' => $qty,
                'harga_beli'=> $harga_beli,
                'harga_jual' => $harga_jual,
            ];
            // echo $_POST["produk_id".$i]."-".$_POST["nama_produk".$i]."-".$_POST["harga_jual".$i]."-".$_POST["qty".$i]."-".$_POST["sub_total".$i];
            $sql = "INSERT INTO `penjualan_detail` (`penjualan_id`, `produk_id`, `qty`, `harga_beli`, `harga_jual`, `created_at`) 
            VALUES ( :penjualan_id, :produk_id, :qty, :harga_beli, :harga_jual, current_timestamp())";
            $stmt = $conn->prepare($sql);
            $stmt->execute($arrData1);

            $arrData2 = [
                'produk_id' => $produk_id,
                'qty' => $qty,
            ];

            $sql = "update produk set jumlah_produk = jumlah_produk - :qty where produk_id  = :produk_id";
            $stmt = $conn->prepare($sql);
            $stmt->execute($arrData2);
        }
        
        $arrData3 = [
            'penjualan_id' => $penjualan_id,
        ];
        
        $sql = "SELECT ROW_NUMBER() OVER(ORDER BY a.penjualan_detail_id asc) AS no, b.nama_produk, 
                a.harga_jual, a.qty, a.harga_jual*a.qty as sub_total
            from penjualan_detail a, produk b
            WHERE a.produk_id = b.produk_id
            AND	  a.penjualan_id = :penjualan_id
            order by penjualan_detail_id  asc";
        $stmt = $conn->prepare($sql);
        $stmt->execute($arrData3);
        $data = $stmt->fetchAll();


    } catch (PDOException $e) {
        echo $e->getMessage();
    }

}



?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
       
 <link rel="icon" type="image/x-icon" href="images/sppico.png">   
 <title>Print Pembayaran</title>
</head>
<body>
    <h5>Nisa Mart</h5>    
    <div style="display:flex;flex-direction:column;width:100%;">
        <div style="display:flex;flex-direction:row;width:100%;margin-bottom:10px;"> 
            <div style="width:50px;"> No</div>
            <div style="width:100px;">Produk</div>
            <div style="padding-left:10px;text-align:right;width:50px;">Harga</div>
            <div style="text-align:right;width:50px;">Qty</div>
            <div style="text-align:right;width:100px;">Sub total</div>
        </div>
        <?php
        $total=0;
        foreach ($data as $row) {
            $no = $row['no'];

            $nama_produk = $row['nama_produk'];
            $harga_jual = $row['harga_jual'];
            $qty = $row['qty'];
            $sub_total = $row['sub_total'];
            $total=$total+$sub_total;
        ?>
   
        <div style="display:flex;flex-direction:row;width:100%;"> 
            <div style="width:50px;"> <?php echo $no.".";  ?></div>
            <div style="width:100px;"> <?php echo $nama_produk;  ?></div>
            <div style="padding-left:10px;text-align:right;width:50px;"> <?php echo number_format($harga_jual);  ?></div>
            <div style="text-align:right;width:50px;"> <?php echo number_format($qty);  ?></div>
            <div style="text-align:right;width:100px;"> <?php echo number_format($sub_total);  ?></div>
        </div>
        <?php } 
        
        ?>
        <div style="display:flex;flex-direction:row;width:100%;justify-content:end;margin-top:20px;"> 
            <div style="display:flex;flex-direction:row;width:100%;"> 
                <div style="width:50px;"></div>
                <div style="width:100px;"></div>
                <div style="padding-left:10px;text-align:right;width:50px;"></div>
                <div style="text-align:left;width:50px;">Total</div>
                <div style="text-align:right;width:100px;"><?php echo number_format($total);  ?></div>
            </div>
        </div>    

        <div style="display:flex;flex-direction:row;width:100%;justify-content:end;"> 
        <div style="display:flex;flex-direction:row;width:100%;"> 
                <div style="width:50px;"></div>
                <div style="width:100px;"></div>
                <div style="padding-left:10px;text-align:right;width:50px;"></div>
                <div style="text-align:left;width:50px;">Discount</div>
                <div style="text-align:right;width:100px;"><?php echo number_format($discount);  ?></div>
        </div>
        </div>    
        <div style="display:flex;flex-direction:row;width:100%;justify-content:end;"> 
            <div style="display:flex;flex-direction:row;width:100%;"> 
                <div style="width:50px;"></div>
                <div style="width:100px;"></div>
                <div style="padding-left:10px;text-align:right;width:50px;"></div>
                <div style="text-align:left;width:50px;">Bayar</div>
                <div style="text-align:right;width:100px;"><?php echo number_format($bayar);  ?></div>
            </div>
        </div>
        <div style="display:flex;flex-direction:row;width:100%;justify-content:end;">     
            <div style="display:flex;flex-direction:row;width:100%;"> 
                <div style="width:50px;"></div>
                <div style="width:100px;"></div>
                <div style="padding-left:10px;text-align:right;width:50px;"></div>
                <div style="text-align:left;width:50px;">Kembali</div>
                <div style="text-align:right;width:100px;"><?php echo number_format($kembali);  ?></div>
            </div>   
        </div>    
    </div>

 <script>
    window.print();
 </script>
 

</body>
</html>