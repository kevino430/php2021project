<!-- 加入購物車 -->
<?php
//購物車開始
require_once("../connMysql.php");
require_once("../cart/mycart.php");
session_start();
$cart =& $_SESSION['cart']; // 將購物車的值設定為 Session
if(!is_object($cart)) $cart = new myCart();
//購物車結束
?>
<?php

//檢查是否經過登入
if(!isset($_SESSION["loginMember"]) || ($_SESSION["loginMember"]=="")){
	header("Location: ../index2.php");
}
//執行登出動作
if(isset($_GET["logout"]) && ($_GET["logout"]=="true")){
	unset($_SESSION["loginMember"]);
	unset($_SESSION["memberLevel"]);
	header("Location: ../index2.php");
}

//繫結登入會員資料
$query_RecMember = "SELECT * FROM memberdata WHERE m_username='{$_SESSION["loginMember"]}'";
$RecMember = $db_link->query($query_RecMember);	
$row_RecMember = $RecMember->fetch_assoc();
?>


<!-- 連接客戶訂單 -->
<?php 
//預設每頁筆數
$pageRow_records = 10;
//預設頁數
$num_pages = 1;
//若已經有翻頁，將頁數更新
if (isset($_GET['page'])) {
  $num_pages = $_GET['page'];
}
//本頁開始記錄筆數 = (頁數-1)*每頁記錄筆數
$startRow_records = ($num_pages - 1) * $pageRow_records;

//未加限制顯示筆數的SQL敘述句
$query_order="SELECT * FROM orders WHERE or_valid!=0 && member_id='{$row_RecMember["m_id"]}' ORDER BY orders.ordertime DESC";

//加上限制顯示筆數的SQL敘述句，由本頁開始記錄筆數開始，每頁顯示預設筆數
$sql_query_limit = $query_order." LIMIT {$startRow_records},  {$pageRow_records}";

//以加上限制顯示筆數的SQL敘述句查詢資料到 $Recproductlove 中
$Recorder = $db_link->query($sql_query_limit);

//以未加上限制顯示筆數的SQL敘述句查詢資料到 $all_result 中
$all_result = $db_link->query($query_order);

//計算總筆數
$total_records = $all_result->num_rows;
//計算總頁數=(總筆數/每頁筆數)後無條件進位。
$total_pages = ceil($total_records/$pageRow_records);

$row_Recorder = $Recorder->fetch_assoc();

// 刪除訂單
if(isset($_GET["action"])&&($_GET["action"]=="delete")){
  // 判斷時間
  $query_Rectime="SELECT invoice,ordertime,NOW() AS now FROM orders WHERE orderid='{$_GET["id"]}'";
  $Rectime = $db_link->query($query_Rectime);
  $row_Rectime = $Rectime->fetch_assoc();
  $invoice=$row_Rectime['invoice'];
  $ot=$row_Rectime['ordertime'];
  $nt=$row_Rectime['now'];
  $diff = abs(strtotime($nt) - strtotime($ot));
  $days = floor($diff / (60*60*24));
  // 取得商品數量
  // echo $days.'<br>';
  if($days<3){
    $query_RecOrdamount = "SELECT * FROM orderdetail LEFT JOIN product ON orderdetail.productid=product.productid WHERE orderid='{$_GET["id"]}'";
    $RecOrdamount = $db_link->query($query_RecOrdamount);
    foreach($RecOrdamount as $row){ 
      $ProductId=$row["productid"];
      $OrderAmount=$row["quantity"];
      $OldAmount=$row["p_amount"];
      $new_amount=$OldAmount+$OrderAmount;
      // echo $ProductId." : ".$OldAmount." + ".$OrderAmount."=".$new_amount."<br>";
      // 回復庫存
      $sql_update="UPDATE product SET p_amount=? WHERE productid=?";
      $stmt = $db_link->prepare($sql_update);

      $stmt->bind_param("ii",$new_amount,$ProductId);
      $stmt->execute();
      $stmt->close();

    };
    // 刪除訂單
    $query_delOrder = "UPDATE orders SET or_valid=0 WHERE orderid=?";
    $stmt=$db_link->prepare($query_delOrder);
    $stmt->bind_param("i", $_GET["id"]);
    $stmt->execute();
    $stmt->close();
    // 刪除訂購項目
    $query_delOdet = "UPDATE orderdetail SET od_valid=0 WHERE orderid=?";
    $stmt=$db_link->prepare($query_delOdet);
    $stmt->bind_param("i", $_GET["id"]);
    $stmt->execute();
    $stmt->close();

    echo '<script language="javascript">';
    echo 'alert("訂單 : '.$invoice.' 刪除成功")';
    echo '</script>';

    //重新導向回到主畫面
    // header('Location: ' . $_SERVER['HTTP_REFERER']);
    }
    if($days>=3){
    echo '<script language="javascript">';
    echo 'alert("您的訂單已超出刪除時限(3日)\n如有需要請聯絡zongyongduan23@gmail.com \n謝謝")';
    echo '</script>';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <title>Kevino430's Website</title>
    <!-- Required meta tags -->
    <meta charset="utf-8" />
    <meta
      name="viewport"
      content="width=device-width, initial-scale=1, shrink-to-fit=no"
    />

    <!-- Bootstrap CSS -->
    <link
      rel="stylesheet"
      href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css"
      integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T"
      crossorigin="anonymous"
    />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css">
    <!-- font awesome -->
    <script src="https://kit.fontawesome.com/5ea815c1d0.js"></script>
    <link href="../mystyle.css" rel="stylesheet" type="text/css">
    
    <style>
      
      .bg-image{
        /* The image used */
        background-image: url("../images/7.jpg");

        /* Add the blur effect */
        filter: blur(2px);
        -webkit-filter: blur(2px);

        /* Full height */
        weight: 100%;

        /* Center and scale the image nicely */
        background-position: center;
        background-repeat: no-repeat;
        background-size: cover;

      }
      .find_none{
        width:72%
      }
      @media screen and (max-width:540px){
        .find_none{
        width:96%
        }
      }
      
    </style>
  </head>

  <body>

    <!-- navbar -->

    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
      <a class="navbar-brand"  href="../index2.php">Kevino430 Shop</a>
      <button
        class="navbar-toggler"
        type="button"
        data-toggle="collapse"
        data-target="#navbarSupportedContent"
        aria-controls="navbarSupportedContent"
        aria-expanded="false"
        aria-label="Toggle navigation"
      >
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav mr-auto">
          
        </ul>
        <?php if(!isset($_SESSION["loginMember"]) || ($_SESSION["loginMember"]=="")){?>
          <button
          type="button"
          class="btn btn-outline-info"
          data-toggle="modal"
          data-target="#exampleModalCenterLogin"
          >
            登入
          </button>

          <a type="button" class="btn btn-outline-info ml-3" href="signup.php">
            註冊
          </a>
          
          <?php }else{ ?>

          
          <a class="text-light " href="m_center.php">
          <!-- 頭像 -->
          <?php if($row_RecMember["m_sex"]=="男"){ ?>
              <img style="height:30px" src="../images/boy.png" alt="">
          <?php }else{?>
              <img style="height:30px" src="../images/girl.png" alt="">
          <?php }?>
          
          <span><strong><?php echo $row_RecMember["m_name"];?></strong> 
          <?php
          date_default_timezone_set("Asia/Taipei");
          $hour = date("G");
          // echo $hour;
          if($hour>=6 && $hour<12){
            echo ", 早安 !";
          }elseif($hour>=12 && $hour<18){
            echo ", 午安 !";
          }elseif($hour>=18 && $hour<24){
            echo ", 晚安 !";
          }else{
            echo ", 您好 !";
          }
          
          ?>
          </span>
          </a>
          <a
          type="button"
          class="btn btn-outline-info ml-3"
          href="?logout=true"
          >
            登出
          </a>        
        <?php } ?>

        <div class=" ml-4">
              <a href="../cart/cart.php">
                <i class="bi bi-cart-fill h3 text-light"></i>
                <?php
                    $sum = 0;
                    foreach($cart->get_contents() as $item) { 
                      $sum+= $item['qty'];} 
                ?> 
                <?php if ($sum!=0){ ?>
                <span class="badge badge-danger rounded-circle" style="margin-left:-15px; "><?php echo $sum;?></span>
                <?php } ?>
              </a>
          </div>
       
      </div>
    </nav>
    <!-- login modal -->

    <div
      class="modal fade"
      id="exampleModalCenterLogin"
      tabindex="-1"
      role="dialog"
      aria-labelledby="exampleModalCenterTitle"
      aria-hidden="true"
    >
      <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
          <div class="modal-header bg-image " style="padding: 0px;height:20rem">
          
            <button
              type="button"
              class="close text-light"
              data-dismiss="modal"
              aria-label="Close"
            >
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <!-- error -->
          <?php if(isset($_GET["errMsg"]) && ($_GET["errMsg"]=="1")){?>
          <div class="p-3 mb-2 bg-danger text-white">登入帳號或密碼錯誤！</div>
          <?php }?>
          <?php if(isset($_GET["errMsg"]) && ($_GET["errMsg"]=="2")){?>
          <div class="p-3 mb-2 bg-danger text-white">
            您的帳號 已停用 或 不存在！<br>
            請 註冊 或 聯絡: zongyongduan23@gmail.com
          </div>
          <?php }?>

          <div class="modal-title bg-text" id="exampleModalLongTitle">
            <h1><i class="far fa-user-circle"></i></h1>
            <h3> 會員登入</h3>
            </div>
          <div class="modal-body">
            <form method="post">
              <div class="form-group px-4 mt-2">
                <label for="formGroupExampleInput">會員帳號</label>
                <input
                  type="text"
                  class="form-control"
                  id="formGroupExampleInput"
                  placeholder="請輸入您的會員帳號"
                  id="username"
                  name="username"
                  value="<?php if(isset($_COOKIE["remUser"]) && ($_COOKIE["remUser"]!="")) echo $_COOKIE["remUser"];?>"
                />
              </div>
              
              <div class="form-group px-4 mt-4">
                <label for="formGroupExampleInput2">密碼</label>
                <div class="row px-3">
                 
                  <input 
                    type="password"
                    class="form-control" 
                    placeholder="請輸入密碼"
                    id="passwd"
                    name="passwd" 
                    value="<?php if(isset($_COOKIE["remPass"]) && ($_COOKIE["remPass"]!="")) echo $_COOKIE["remPass"];?>"
                    />
                  <i toggle="#password-field" class="field-icon fa fa-fw fa-eye-slash field_icon togglePassword " ></i>
                 
                  
                </div>
              </div>
              <div class="row mx-4 mt-4">
                <div class="form-check col">
                  <input
                    name="rememberme"
                    class="form-check-input"
                    type="checkbox"
                    value="true"
                    id="rememberme"
                    checked
                  />
                  <label class="form-check-label" for="flexCheckDefault">
                    記住我的帳號
                  </label>
                </div>
                <div class="col">
                  <a class="text-secondary float-right" href="m_passmail.php"
                    ><i class="far fa-question-circle"></i>忘記密碼</a
                  >
                </div>
              </div>
              <div
                class="d-flex justify-content-center mx-4 mt-4 pb-3 border-bottom"
              >
                <button
                  class="btn btn-info btn-lg mx-auto"
                  style="width: 18rem"
                >
                  登入
                </button>
              </div>
              <div class="d-flex justify-content-center mx-4 mt-4 pb-3">
                <a
                  class="btn btn-outline-info btn-lg mx-auto"
                  style="width: 18rem"
                  href="signup.php"
                >
                  <i class="far fa-user-circle"></i>
                  免費註冊
                </a>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>

    <!-- bread crumb -->
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="../index2.php">首頁</a></li>
        <li class="breadcrumb-item"><a href="m_center.php">會員中心</a></li>
        <li class="breadcrumb-item active" aria-current="page">訂單紀錄</li>
      </ol>
    </nav>
    <!-- main  -->
    
    <div class="container mb-4">
    <!-- searching place  -->
        <div class="row mt-2 d-flex justify-content-center">
        <div class="text-success">Order data</div>
      </div>
      <div class="row mt-2 d-flex justify-content-center">
        
        <h1 class="">訂單紀錄</h1>
      </div>
     <!-- form -->
     <div class="mt-2 pt-4 " id="productPlace" style="min-height:465px">
    
        <?php 
        $Recorder->data_seek(0);
        while($row_Recorder = $Recorder->fetch_assoc()){?>
          <!-- love start  -->
          <div class="card my-2 productcard">
            <div class="card-body bg-light">
              <div class="d-flex justify-content-between" >
                <div class="d-flex ">
                  <span class="h6 mr-1">訂單編號:</span>
                  <span class="h6"><?php echo $row_Recorder['invoice'] ?></span>
                </div>
                <div>
                  <span class="h6 mr-1 ">消費時間:</span>
                   
                  <?php 
                  $shoptime=strtotime($row_Recorder['ordertime']);
                  $new_date_format = date('Y 年 m 月 d 日,  g:i A', $shoptime);
                  echo $new_date_format?>
                </div>

              </div>
              
              <table class="table">
                <thead>
                  <tr>
                    <th scope="col">編號</th>
                    <th scope="col">名稱</th>
                    <th scope="col">單價</th>
                    <th scope="col">數量</th>
                    <th class="text-center pl-5" scope="col">小計</th>
                  </tr>
                </thead>
                <tbody>
                <?php 
                $oid=$row_Recorder['orderid'];
                $query_RecDet = "SELECT * FROM orderdetail LEFT JOIN product_image ON orderdetail.productid=product_image.product_id WHERE orderid='$oid' GROUP BY orderdetail.orderdetailid ";
                $RecDet = $db_link->query($query_RecDet);
                $num_rows = 0;
                foreach($RecDet as $row){ 
                $num_rows ++;
                ?>
                <div>
                
                </div>
                <div>
                     
                </div>
                <tr>
                    <th scope="row"><?php echo $num_rows ?></th>
                    <td>
                      <a class="text-secondary" href="../cart/product2.php?id=<?php echo $row["productid"];?>" style="text-decoration: none;">
                      <?php if($row["image_url"]!=""){?>
                        <img src="../proimg/<?php echo $row["image_url"]?>" alt="" style="width:80px">
                        <?php }else{?> 
                        <img  src="../images/nopic.png" alt="暫無圖片" style="width:80px" />  
                        <?php }?>
                        <?php echo $row["productname"]?>
                      </a>
                    </td>
                    <td><?php echo number_format($row["unitprice"])?></td>
                    <td> x <?php echo $row["quantity"]?></td>
                    <td class="text-right "> NT$ <?php echo number_format($row["quantity"]*$row["unitprice"])?></td>
                  </tr>
              
              
                <?php }?>
                <tr>
                  <th colspan="2">消費金額</th>
                  <td colspan="2"></td>
                  <td class="text-right "><?php echo number_format($row_Recorder['total']) ?></td>

                </tr>
                <tr>
                  <th colspan="2">運費</th>
                  <td colspan="2"></td>
                  <td class="text-right "><?php echo number_format($row_Recorder['deliverfee']) ?></td>

                </tr>
                <tr>
                  <th colspan="2">總金額</th>
                  <td colspan="2"></td>
                  <td 
                  class="text-right "
                  style="text-decoration: underline; text-decoration-style: double;">
                   NT$ <?php echo number_format($row_Recorder['grandtotal']) ?>
                  </td>

                </tr>

                  
                 
                  
                </tbody>
              </table>
              <div class="d-flex justify-content-end">
                <a 
                type="button" 
                class="btn btn-outline-danger ft2" 
                href="?action=delete&id=<?php echo $row_Recorder["orderid"];?>"
                onClick="return deletesure();"
                >
                <i class="bi bi-trash-fill"></i>取消訂單
                </a>
              </div>
              
             
            </div>
          </div>
          <!-- card end -->
        <?php } ?> 
      

        <!-- 目前無資料 -->
        <div id="search_none">
          <div class="" align="center"><img class="find_none" src="../images/cart_empty.png" alt=""></div>
          <div class=" h4" align="center">您目前沒有訂單。<a href="../index2.php">快到商城看看吧~</a></div>
          </div>

          <script>
          var parent = document.getElementById("productPlace");
          var nodesSameClass = parent.getElementsByClassName("productcard");
          var p_length = nodesSameClass.length;
          console.log(p_length);

          var x = document.getElementById("search_none");
          if (p_length!=0) {
          x.style.display = "none";
          } else {
            x.style.display = "block";
          }
          
          </script> 
        </div>
      </div>
           <!-- pagination -->
        
           <nav aria-label="Page navigation ">
  <!-- 第一頁 前一頁 -->
  <ul class="pagination justify-content-center">

    <?php if ($num_pages > 1) { // 若不是第一頁則顯示 ?>
    <li class="page-item ">
        <a class="page-link" href="m_order.php?page=1"  aria-disabled="flase"><i class="fas fa-step-backward"></i></a>
    </li>
    <li class="page-item ">
      <a class="page-link" href="m_order.php?page=<?php echo $num_pages-1;?>"  aria-disabled="false"><i class="fas fa-backward"></i></a>
    </li>
    <?php }else{ ?>
    <li class="page-item disabled">
      <a class="page-link" href="#" aria-disabled="true"><i class="fas fa-step-backward"></i></a>
    </li>
    <li class="page-item disabled">
      <a class="page-link" href="#" aria-disabled="true"><i class="fas fa-backward"></i></a>
    </li>

    <?php } ?>


    <!-- 頁數顯示 -->
    <?php
  	  for($i=1;$i<=$total_pages;$i++){
  	  	  if($i==$num_pages){?>
        
          <li class="page-item"><a class="page-link" ><?php echo $i;?></a></li>

          <?php }else{ ?>

            <li class="page-item">
            <a class="page-link" href="m_order.php?page=<?php echo $i;?>"><?php echo $i;?></a>
            </li>

          <?php }
  	  }
  	?>
  <!-- 最後頁 後一頁 -->

     <?php if ($num_pages < $total_pages) { // 若不是最後一頁則顯示 ?>

    <li class="page-item ">
      <a class="page-link" href="m_order.php?page=<?php echo $num_pages+1;?>"  aria-disabled="false"><i class="fas fa-forward"></i></a>
    </li>
    <li class="page-item ">
      <a class="page-link" href="m_order.php?page=<?php echo $total_pages;?>"  aria-disabled="flase"><i class="fas fa-step-forward"></i></a>
    </li>
    
    <?php }else{ ?>
    <li class="page-item disabled">
      <a class="page-link" href="#" aria-disabled="true"><i class="fas fa-forward"></i></a>
    </li>
    <li class="page-item disabled">
      <a class="page-link" href="#" aria-disabled="true"><i class="fas fa-step-forward"></i></a>
    </li>

    <?php } ?>

  </ul>
</nav>
    </div>
   
   
    <!-- footer -->
    <footer class="bg-info text-light text-center text-lg-start mt-5">
      <!-- Grid container -->
      <div class="container p-4">
        <!--Grid row-->
        <div class="row">
           <!--Grid column-->
           <div class="col-lg-6 col-md-12 mb-4 mb-md-0">
            <h5 class="text-uppercase">Kevino430 online stroe </h5>
            <p class="text-left">
              我是段宗翔，2020/9-2021/1資策會-前端工程師養成班，582小時密集技術培訓。
              此網站是我第一個個人完成的php網站，其中前端使用Bootstrap、jQuery套件，架設時使用XAMPP並使用原生PHP連接後端資料庫，PHPMailer Gmail smtp 發送信件。<br>
              此網站是一個簡易的購物網站，其中包含:<br>
              客戶方: 客戶建立修改、商品購買、加入最愛、系統發送密碼及訂單郵件。<br>
              管理方: 客戶管理、商品管理、訂單管理。<br>

            </p>
          </div>

          <!--Grid column-->
          <div class="col-lg-6 col-md-6 mb-4 mb-md-0">
            <h5 class="text-uppercase">我的聯絡方式</h5>

            <ul class="list-unstyled mb-0 text-left pl-5 ml-5">
              <li>
                <i class="far fa-envelope h5 mr-2"></i> Gamil : ruby004949@gmail.com
              </li>
              <li>
                <i class="fas fa-mobile-alt h5 mr-2"></i> cel : 0970619427
              </li>
              <li>
                <i class="fab fa-line h4 mr-2"></i> Line : kevino430
              </li>
              
            </ul>
          </div>
          <!--Grid column-->

        </div>
        <!--Grid row-->
      </div>
      <!-- Grid container -->

      <!-- Copyright -->
      <div class="text-center p-3" style="background-color: rgba(0, 0, 0, 0.2)">
        © 2021 Copyright:
        <a class="text-dark" href="https://github.com/kevino430">github.com/kevino430</a>
      </div>
      <!-- Copyright -->
    </footer>
    <!-- Footer -->

    <!-- Optional JavaScript -->
     
     
    <!-- check form  -->

   
    
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->

    <script
      src="https://code.jquery.com/jquery-3.3.1.slim.min.js"
      integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo"
      crossorigin="anonymous"
    ></script>
    <script
      src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"
      integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1"
      crossorigin="anonymous"
    ></script>
    <script
      src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"
      integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM"
      crossorigin="anonymous"
    ></script>
    <!-- open modal -->
    <?php if(isset($_GET["errMsg"]) && ($_GET["errMsg"]=="1")){
      echo'<script type="text/javascript">';
      echo '$(document).ready(function () {
            setTimeout(function () {
          $("#exampleModalCenterLogin").modal("show");
          }, 0000);
      });';
        echo'</script>';
    }?>

    <!-- signup password eye -->

    <script>
      $(document).on('click', '.toggle-password', function() {
      $(this).toggleClass("fa-eye-slash fa-eye");
      var input = $("#m_passwd");
      input.attr('type') === 'password' ? input.attr('type','text') : input.attr('type','password')
      });
    </script>
    <script>
      $(document).on('click', '.toggle-password2', function() {
      $(this).toggleClass("fa-eye-slash fa-eye");
      var input = $("#m_passwdrecheck");
      input.attr('type') === 'password' ? input.attr('type','text') : input.attr('type','password')
      });
    </script>
    <script>
      var page= <?php echo $num_pages;?>;
      var page_perious=page-1;

      console.log(page);
      console.log(page_perious);
      if(page>1){
            console.log("hi");
            if(p_length==0){
              // console.log("hi")
              window.location.href='m_productlove.php?page='+page_perious
            }
           
      }
          
    </script>
    <script language="javascript">
    function deletesure(){
        if (confirm('\n您確定要刪除此筆訂單嗎?')) return true;
        return false;
    }
    </script>
  </body>
</html>
<?php
	$db_link->close();
?>
