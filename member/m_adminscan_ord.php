<?php
function GetSQLValueString($theValue, $theType) {
  switch ($theType) {
    case "string":
      $theValue = ($theValue != "") ? filter_var($theValue, FILTER_SANITIZE_MAGIC_QUOTES) : "";
      break;
    case "int":
      $theValue = ($theValue != "") ? filter_var($theValue, FILTER_SANITIZE_NUMBER_INT) : "";
      break;
    case "email":
      $theValue = ($theValue != "") ? filter_var($theValue, FILTER_VALIDATE_EMAIL) : "";
      break;
    case "url":
      $theValue = ($theValue != "") ? filter_var($theValue, FILTER_VALIDATE_URL) : "";
      break;      
  }
  return $theValue;
}
require_once("../connMysql.php");
session_start();
//檢查是否經過登入
if(!isset($_SESSION["loginMember"]) || ($_SESSION["loginMember"]=="")){
	header("Location: ../index2.php");
}
//檢查權限是否足夠
if($_SESSION["memberLevel"]=="member"){
	header("Location: m_center.php");
}
//執行登出動作
if(isset($_GET["logout"]) && ($_GET["logout"]=="true")){
	unset($_SESSION["loginMember"]);
	unset($_SESSION["memberLevel"]);
	header("Location: ../index2.php");
}

//選取管理員資料
$query_RecAdmin = "SELECT * FROM memberdata WHERE m_username='{$_SESSION["loginMember"]}'";
$RecAdmin = $db_link->query($query_RecAdmin);	
$row_RecAdmin=$RecAdmin->fetch_assoc();


//繫結選取訂單資料
$query_RecOrder = "SELECT * FROM orders WHERE orderid='{$_GET["id"]}'";
$RecOrder = $db_link->query($query_RecOrder);	
$row_RecOrder=$RecOrder->fetch_assoc();

//繫結選取訂單細節
$query_RecDetail = "SELECT * FROM orderdetail WHERE orderid='{$_GET["id"]}'";
$RecDetail = $db_link->query($query_RecDetail);	
$row_RecDetail=$RecDetail->fetch_assoc();

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

          
          <a class="text-light " href="m_admin.php">
          <!-- 頭像 -->
          <i class="fas fa-user-md h3"></i>
          <span><strong><?php echo $row_RecAdmin["m_name"];?></strong> 
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
        <li class="breadcrumb-item"><a href="m_admin.php">管理者首頁</a></li>
        <li class="breadcrumb-item"><a href="m_admin_order.php">客戶訂單總覽</a></li>
        <li class="breadcrumb-item active" aria-current="page"><?php echo $row_RecOrder["invoice"]?></li>
      </ol>
    </nav>
    <!-- main  -->
    
    <div class="container mb-4">
    <!-- searching place  -->
      <div class="row mt-2 d-flex ml-1 mb-3 ">
      <div class="d-flex justify-content-between ">
          <div class="d-flex">
            <div class="rounded-circle bg-success text-white mr-2 ml-1 pt-2" style="height:100px; width:100px" align="center" >
              <h1 class="mt-2"><i class="fas fa-user-md " style="font-size:64px"></i></h1>
            </div>
          <!-- greading -->
          <h1 class="mt-3 ml-2">
          <span><strong><?php echo $row_RecAdmin["m_name"];?></strong>
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
              <h6 class="mt-2">這次登入的時間為：<?php echo $row_RecAdmin["m_logintime"];?></h6>
          </h1>
          <div class="mt-5 pt-1 ml-5" align="center">
            <a class="btn btn-outline-info " href="m_admin_order.php">
            <i class="fas fa-sign-out-alt" style="transform:scaleX(-1);"></i> 回到訂單資料
            
            </a>
          </div>

          </div>
        </div>
      </div>
      <div class="row mt-2 d-flex justify-content-center">
        
        <h1 class="">訂單資料</h1>
      </div>
     <!-- form -->
     <div class=" bg-light mt-2 pt-3 pb-3 px-2">
      <?php 
      $RecOrder->data_seek(0);
      while($row_RecOrder=$RecOrder->fetch_assoc()){
      ?>
        <table class="table table-borderless">
          <tbody>
            <tr>
              <th scope="row" width="10%">訂單編號</th>
              <td colspan="2"><?php echo $row_RecOrder["invoice"]?></td>
              <td></td>
              <td></td>
            </tr>
            <tr>
              <th scope="row">購買時間</th>
              <td colspan="2"><?php echo $row_RecOrder["ordertime"]?></td>
              <td></td>
              <td></td>
            </tr>
            <tr>
              <th scope="row">客戶資料</th>
              <td width="10%">客戶名稱</td>
              <td width="30%"><?php echo $row_RecOrder["customername"]?></td>
              <td></td>
            </tr>
            <tr>
              <th scope="row"></th>
              <td width="10%">聯絡方式</td>
              <td>email : <?php echo $row_RecOrder["customeremail"]?></td>
              <td>phone : <?php echo $row_RecOrder["customerphone"]?></td>
            </tr>
            <tr>
              <th scope="row"></th>
              <td width="10%"></td>
              <td colspan="2">地址 : <?php echo $row_RecOrder["customeraddress"]?></td>
            </tr>
          </tbody>
        </table>
        <h6 class="font-weight-bold ml-2" >訂單內容</h6>
        <table class="table table-borderless "> 
        <thead>
              <th scope="row" width="8%">編號</th>
              <th width="20%">商品名稱</th>
              <th class="text-right" width="13%">商品單價</th>
              <th width="3%"></th>
              <th class="text-right" width="10%">數量</th>
              <th class="text-right" width="18%">小計</th>

          </thead>
          <tbody>
          <?php 
          $RecDetail->data_seek(0);
          $num=0;
          while($row_RecDetail=$RecDetail->fetch_assoc()){
          $num++;
          $subtotal= $row_RecDetail["quantity"]*$row_RecDetail["unitprice"]
          ?>
            <tr>
              <th scope="row"><?php echo $num;?>. </th>
              <td><?php echo $row_RecDetail["productname"]?></td>
              <td class="text-right"><?php echo number_format($row_RecDetail["unitprice"])?></td>
              <td class="text-right"></td>
              <td class="text-right"> x <?php echo $row_RecDetail["quantity"]?></td>
              <td class="text-right">  <?php echo  number_format($subtotal)?></td>
            </tr>
          <?php }?>
          
            <tr class="border-top">
              <th scope="row" colspan="2">消費金額</th>
              <td colspan="3"></td>
              <td class="text-right">  <?php echo number_format($row_RecOrder["total"])?></td>
            </tr>
            <tr>
              <th scope="row" colspan="2">運費</th>
              <td colspan="3"></td>
              <td class="text-right"><?php echo number_format($row_RecOrder["deliverfee"])?></td>
            </tr>
            <tr>
              <th scope="row" colspan="2">總金額</th>
              <td colspan="3"></td>
              <td 
              class="text-right font-weight-bold" 
              style="text-decoration: underline; text-decoration-style: double;">
                NT$ <?php echo number_format($row_RecOrder["grandtotal"])?>
              </td>
            </tr>
           
            
          </tbody>
        </table>

      <?php }?>

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
   
    
  </body>
</html>
<?php
	$db_link->close();
?>
