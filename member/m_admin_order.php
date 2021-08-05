<?php 
require_once("../connMysql.php");
session_start();
//檢查是否經過登入
if(!isset($_SESSION["loginMember"]) || ($_SESSION["loginMember"]=="")){
	header("Location: ../index2.php");
}
//檢查權限是否足夠
if($_SESSION["memberLevel"]=="member"){
	header("Location: member_center.php");
}
//執行登出動作
if(isset($_GET["logout"]) && ($_GET["logout"]=="true")){
	unset($_SESSION["loginMember"]);
	unset($_SESSION["memberLevel"]);
	header("Location: ../index2.php");
}

//選取管理員資料
$query_RecAdmin = "SELECT m_id, m_name, m_logintime FROM memberdata WHERE m_username=?";
$stmt=$db_link->prepare($query_RecAdmin);
$stmt->bind_param("s", $_SESSION["loginMember"]);
$stmt->execute();
$stmt->bind_result($mid, $mname, $mlogintime);
$stmt->fetch();
$stmt->close();
//選取所有一般會員資料
//預設每頁筆數
$pageRow_records = 10;

//預設頁數
$num_pages = 1;
//若已經有翻頁，將頁數更新
if (isset($_GET['page'])) {
  $num_pages = $_GET['page'];
}
//本頁開始記錄筆數 = (頁數-1)*每頁記錄筆數
$startRow_records = ($num_pages -1) * $pageRow_records;

//若有關鍵字時顯示筆數的SQL敘述句
if(isset($_GET["keyword"])&&($_GET["keyword"]!="")){
  $query_RecOrder = "SELECT * FROM orders WHERE or_valid!=0 && invoice LIKE ? OR customername LIKE ? ORDER BY orderid DESC";
  $stmt = $db_link->prepare($query_RecOrder);
  $keyword = "%".$_GET["keyword"]."%";
  $stmt->bind_param("ss", $keyword, $keyword);	

//若有時間區間關鍵字限制顯示筆數的SQL敘述句
}elseif(isset($_GET["time1"]) && isset($_GET["time2"]) && ($_GET["time1"]<=$_GET["time2"])){
	
  $query_RecOrder = "SELECT * FROM orders WHERE or_valid!=0 && ordertime BETWEEN ? AND ? ORDER BY orderid DESC";
	$stmt = $db_link->prepare($query_RecOrder);
	$stmt->bind_param("ss", $_GET["time1"], $_GET["time2"]);

//預設狀況下未加限制顯示筆數的SQL敘述句
}else{
  $query_RecOrder = "SELECT * FROM orders WHERE or_valid!=0 ORDER BY orderid DESC";

  $stmt = $db_link->prepare($query_RecOrder);
}
$stmt->execute();
//以未加上限制顯示筆數的SQL敘述句查詢資料到 $all_RecOrder 中
$all_RecOrder = $stmt->get_result();
//計算總筆數
$total_records = $all_RecOrder->num_rows;
//計算總頁數=(總筆數/每頁筆數)後無條件進位。
$total_pages = ceil($total_records/$pageRow_records);

function keepURL(){
	$keepURL = "";
	if(isset($_GET["keyword"])) $keepURL.="&keyword=".urlencode($_GET["keyword"]);
  if(isset($_GET["time1"])) $keepURL.="&time1=".$_GET["time1"];
	if(isset($_GET["time2"])) $keepURL.="&time2=".$_GET["time2"];
	return $keepURL;
}
//總銷售總數
$query_RecTotal = "SELECT SUM(grandtotal)AS totalsail FROM orders ORDER BY orderid DESC";
$RecTotal = $db_link->query($query_RecTotal);
$row_RecTotal = $RecTotal->fetch_assoc();
//本月銷售總數
$thisMonth= date('Y-m-01');
$query_ReMtotal = "SELECT SUM(grandtotal)AS totalsail,ordertime FROM orders WHERE ordertime BETWEEN '$thisMonth' AND NOW() ORDER BY orderid DESC";
$RecMtotal = $db_link->query($query_ReMtotal);
$row_RecMtotal = $RecMtotal->fetch_assoc();

// 銷售量前三
$query_RecTopsail="SELECT *, SUM(orderdetail.quantity) AS topsail FROM orderdetail WHERE od_valid!=0 GROUP BY productid ORDER BY topsail DESC LIMIT 3";
$RecTopsail = $db_link->query($query_RecTopsail);
$row_RecTopsail = $RecTopsail->fetch_assoc();

// 刪除訂單
if(isset($_GET["action"])&&($_GET["action"]=="delete")){
  // 取得商品數量
  $query_RecOrdamount = "SELECT * FROM orderdetail LEFT JOIN product ON orderdetail.productid=product.productid WHERE orderid='{$_GET["id"]}' ";
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

	//重新導向回到主畫面
  header('Location: ' . $_SERVER['HTTP_REFERER']);
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
      
    </style>
    <script language="javascript">
    function deletesure(){
        if (confirm('\n您確定要刪除這個訂單嗎?\n刪除後無法恢復!\n')) return true;
        return false;
    }
    
    </script>
  </head>
  <body>
  <?php if(isset($_GET["loginStats"]) && ($_GET["loginStats"]=="1")){?>

  <?php }?>
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

          
          <a class="text-light " href="">
           <!-- 頭像 -->
           <i class="fas fa-user-md h3"></i>
          <span><strong><?php echo $mname;?></strong>
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
        <li class="breadcrumb-item"><a href="../index2.php">管理者首頁</a></li>
        <li class="breadcrumb-item active" aria-current="page">客戶訂單總覽</li>
      </ol>
    </nav>
    <!-- main  -->
    <div class="container mb-4" style="min-height:528px">

     <!-- table -->
     <div class=" mt-2 pb-3">
        <div class="d-flex justify-content-between">
          <div class="d-flex">
            <div class="rounded-circle bg-success text-white mr-2 ml-2 pt-2" style="height:100px; width:100px" align="center" >
              <h1 class="mt-2"><i class="fas fa-user-md " style="font-size:64px"></i></h1>
            </div>
          <!-- greading -->
          <h1 class="mt-3 ml-2">
          <span><strong><?php echo $mname;?></strong>
              <?php
              date_default_timezone_set("Asia/Taipei");
              $hour = date("G");
              // echo $hour;
              if($hour>=6 && $hour<12){
                echo ", 早安 !";
              }elseif($hour>=12 && $hour<18){
                echo ", 午安 !";
              }elseif($hour>=18 && $hour<24 ){
                echo ", 晚安 !";
              }elseif($hour>=24 || $hour<6){
                echo ", 夜深了 !";
              }
              else{
                echo ", 您好 !";
              } 
              ?>
              <h6 class="mt-2">這次登入的時間為：<?php echo $mlogintime;?></h6>
          </h1>
          <div class="mt-5 pt-1 ml-5" align="center">
            <a class="btn btn-info" href="m_adminupdate.php?id=<?php echo $mid;?>">
            <i class="bi bi-pencil-square mr-1"></i>修改管理者資料
            </a>
          </div>
          </div>
        </div>
        <div class="mt-3 mb-1 d-flex flex-row justify-content-between " >
          <div class="d-flex flex-row">
            <h3>客戶訂單總覽</h3>
            
            <div class="mt-2 ml-3 ft2 float-right">目前資料筆數：
              <?php echo $total_records;?>
            </div>
          </div>
            

            <div class="col-auto mt-1  d-flex flex-row " >
              <div class="mt-2 ml-3 ft2 float-right">月銷售額：
                <span class="h5">NT$ <?php echo number_format($row_RecMtotal["totalsail"]);?></span>
              </div>
              <div class="mt-2 ml-3 ft2 float-right">總銷售總額：
                <span class="h5">NT$ <?php echo number_format($row_RecTotal["totalsail"]);?></span>
              </div>
            </div>
        </div>
        <ul class="nav nav-tabs">
        <li class="nav-item">
            <a class="nav-link text-secondary" href="m_admin.php">一般會員</a>
          </li>
          <li class="nav-item">
            <a class="nav-link text-secondary" href="m_admin2.php">已停用會員</a>
          </li>
          <li class="nav-item">
            <a class="nav-link active" href="#">客戶訂單總覽</a>
          </li>
          <li class="nav-item">
            <a class="nav-link text-secondary" href="m_admin_product.php">商品總覽</a>
          </li>
          
          
        </ul>

        <div class="mb-2">
          <form name="form1" method="get" action="m_admin_order.php">
            <div class="row px-3">
              <div class="input-group">

                <input
                  name="keyword"
                  type="text"
                  id="keyword"
                  class="form-control rounded-left"
                  placeholder="--搜尋客戶名稱或編號"
                  aria-label="Recipient's username"
                  aria-describedby="basic-addon2"
                  onClick="this.value='';"
                />
                <div class="input-group-append">
                  <button class="btn btn-outline-info form-control" type="submit">
                    <i class="fas fa-search mr-2"></i>搜尋
                  </button>
                </div>
                
              </div>
            </div>
          </form>  
        </div>
        <div class="my-1 d-flex justify-content-between ">
          <!-- 排名 -->
          <div class="d-flex">
            <div class="h6 font-weight-bold"> 商品排名</div>
            <div class="">
              <?php 
                $RecTopsail->data_seek(0);
                $num_rows = 0;
                while($row_RecTopsail = $RecTopsail->fetch_assoc()){
                  $num_rows ++ ;
                ?>
                
                <div class="d-flex">
                  <img src="../images/<?php echo $num_rows?>.png" alt="" style="width:30px">
                  <div class="h6" style="width:260px">
                    <?php echo $row_RecTopsail["productname"];?> 
                  </div>
                  <div>售出</div>
                  <div class="h6" >
                    x <?php echo $row_RecTopsail["topsail"];?> 
                  </div>
                </div>
              <?php }?>
            </div>
          </div>
          
          <form name="form2" method="get" action="m_admin_order.php">
            <div class="row" >
              <div class="col">
                <input 
                  class="form-control" 
                  type="date" 
                  value="<?php if(isset($_GET['time1']) && ($_GET['time1']!='')){ echo $_GET['time1'];}?>"
                  name="time1" 
                  id="time1" 
                  max="<?= date('Y-m-d'); ?>"
                >
              </div>
              <h4>~</h4>
              <div class="col">
                <input 
                  class="form-control" 
                  type="date" 
                  value="<?php if(isset($_GET['time2']) && ($_GET['time2']!='')){ echo $_GET['time2'];}?>"
                  name="time2" 
                  id="time2" 
                  max="<?= date('Y-m-d'); ?>"
                >
              </div>
              <div class="col">
              <input class="btn btn-info " type="submit" id="button2" value="查詢">
              </div>
           
            
            </div>
          </form>
        </div>
        
              
        <table class="table table-hover table-bordered ">
          <thead class="thead-dark">
            <tr>
              <th class="text-center" width="" scope="col">編號</th>
              <th class="text-center" width="" scope="col">客戶名稱</th>
              <th class="text-center" width="" scope="col">購買商品</th>
              <th class="text-center" width="" scope="col">銷售金額 </th>
              <th class="text-center" width="7%" scope="col">運費</th>
              <th class="text-center" width="" scope="col">銷售總額</th>
              <th class="text-center" width="" scope="col">銷售時間</th>
              <th class="text-center" width="" scope="col">功能</th>
            </tr>
          </thead>
          <tbody>
          <?php
            //加上限制顯示筆數的SQL敘述句，由本頁開始記錄筆數開始，每頁顯示預設筆數
            $query_limit_RecOrder = $query_RecOrder." LIMIT {$startRow_records}, {$pageRow_records}";
            //以加上限制顯示筆數的SQL敘述句查詢資料到 $RecOrder 中
            $stmt = $db_link->prepare($query_limit_RecOrder);
            //若有分類關鍵字時未加限制顯示筆數的SQL敘述句
            if(isset($_GET["keyword"])&&($_GET["keyword"]!="")){
              $keyword = "%".$_GET["keyword"]."%";
              $stmt->bind_param("ss", $keyword, $keyword);	
            //若有價格區間關鍵字時未加限制顯示筆數的SQL敘述句
            }elseif(isset($_GET["time1"]) && isset($_GET["time2"]) && ($_GET["time1"]<=$_GET["time2"])){
              $stmt->bind_param("ss", $_GET["time1"], $_GET["time2"]);
            }
            $stmt->execute();            
            $RecOrder = $stmt->get_result();
            while($row_RecOrder=$RecOrder->fetch_assoc()){
            
          ?>

            <tr>
              <td class="text-center" ><?php echo $row_RecOrder["invoice"];?> </td>
              <td class="text-center" ><?php echo $row_RecOrder["customername"];?> </td>
              <td class="" >
                <?php 
                $oid=$row_RecOrder["orderid"];
                $query_RecDetail = "SELECT * FROM orderdetail WHERE orderid=$oid ORDER BY orderdetailid DESC";
                $RecDetail = $db_link->query($query_RecDetail);	
                $row_RecDetail = $RecDetail->fetch_assoc();
                $RecDetail->data_seek(0);
                while($row_RecDetail=$RecDetail->fetch_assoc()){
                ?> 
                <span style="white-space:nowrap; font-size:14px; display:block; font-weight: bold;"><?php echo $row_RecDetail["productname"] ?></span>
                <?php }?>
              </td>
              <td class="text-right" ><?php echo number_format($row_RecOrder["total"]);?></td>
              <td class="text-right" ><?php echo number_format($row_RecOrder["deliverfee"]);?></td>
              <td class="text-right" ><span style="font-weight: bold;"><?php echo number_format($row_RecOrder["grandtotal"]);?></span></td>
              <td class="text-center" ><?php echo $row_RecOrder["ordertime"];?></td>

             
              <td align="center"> 
                <a type="button" class="btn btn-outline-success ft2" href="m_adminscan_ord.php?id=<?php echo $row_RecOrder["orderid"];?>">
                <i class="fas fa-search"></i>
                </a>
                
                <a type="button" class="btn btn-outline-danger ft2" href="?action=delete&id=<?php echo $row_RecOrder["orderid"];?>" onClick="return deletesure();">
                <i class="bi bi-trash-fill"></i>
                </a>
                
              </td>
            </tr>
          <?php }?>
          </tbody>
        </table>
        <!-- pagination -->
     <!-- 第一頁 前一頁 -->
<nav>
<ul class="pagination justify-content-center">

<?php if ($num_pages > 1) { // 若不是第一頁則顯示 ?>
<li class="page-item ">
    <a class="page-link" href="?page=1<?php echo keepURL();?>"  aria-disabled="flase"><i class="fas fa-step-backward"></i></a>
</li>
<li class="page-item ">
  <a class="page-link" href="?page=<?php echo $num_pages-1;?><?php echo keepURL();?>"  aria-disabled="false"><i class="fas fa-backward"></i></a>
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
            <a class="page-link" href="?page=<?php echo $i;?>&<?php echo keepURL();?>"><?php echo $i;?></a>
            </li>

          <?php }
      }
    ?>
    <!-- 最後頁 後一頁 -->

    <?php if ($num_pages < $total_pages) { // 若不是最後一頁則顯示 ?>

    <li class="page-item ">
      <a class="page-link" href="?page=<?php echo $num_pages+1;?><?php echo keepURL();?>"  aria-disabled="false"><i class="fas fa-forward"></i></a>
    </li>
    <li class="page-item ">
      <a class="page-link" href="?page=<?php echo $total_pages;?><?php echo keepURL();?>"  aria-disabled="flase"><i class="fas fa-step-forward"></i></a>
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
      
    </div>
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
     
     <script>
      // JavaScript for disabling form submissions if there are invalid fields
      (function () {
        "use strict";
        window.addEventListener(
          "load",
          function () {
            // Fetch all the forms we want to apply custom Bootstrap validation styles to
            var forms = document.getElementsByClassName("needs-validation");
            // Loop over them and prevent submission
            var validation = Array.prototype.filter.call(
              forms,
              function (form) {
                form.addEventListener(
                  "submit",
                  function (event) {
                    if (form.checkValidity() === false) {
                      event.preventDefault();
                      event.stopPropagation();
                    }
                    form.classList.add("was-validated");
                  },
                  false
                );
              }
            );
          },
          false
        );
      })();
    </script>
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
      $(document).on('click', '.togglePassword', function() {
      $(this).toggleClass("fa-eye-slash fa-eye");
      var input = $("#passwd");
      input.attr('type') === 'password' ? input.attr('type','text') : input.attr('type','password')
      });
    </script>
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
   
    
  </body>
</html>
