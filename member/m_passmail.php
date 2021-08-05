<!-- 加入購物車 -->
<?php
//購物車開始
require_once("../connMysql.php");
require_once("../cart/mycart.php");
session_start();
$cart =& $_SESSION['cart']; // 將購物車的值設定為 Session
if(!is_object($cart)) $cart = new myCart();
// 新增購物車內容
if(isset($_POST["cartaction"]) && ($_POST["cartaction"]=="add")){
	$cart->add_item($_POST['id'],$_POST['qty'],$_POST['price'],$_POST['name']);
}
//購物車結束

//計算資料總筆數
$query_RecTotal = "SELECT count(productid) as totalNum FROM product";
$RecTotal = $db_link->query($query_RecTotal);
$row_RecTotal = $RecTotal->fetch_assoc();
?>

<!-- 登入 -->
<?php
// require_once("../connMysql.php");
// session_start();
//檢查是否經過登入，若有登入則重新導向
if(isset($_SESSION["loginMember"]) && ($_SESSION["loginMember"]!="")){
  //若帳號等級為 member 則導向會員中心
  if($_SESSION["memberLevel"]=="member"){
    // header("Location: member/member_center.php");
    $query_RecMember = "SELECT * FROM memberdata WHERE m_username = '{$_SESSION["loginMember"]}'";
    $RecMember = $db_link->query($query_RecMember); 
    $row_RecMember=$RecMember->fetch_assoc();
    
  //否則則導向管理中心
  }else{
    header("Location: member/m_admin.php"); 
  }
}
//執行會員登入
if(isset($_POST["username"]) && isset($_POST["passwd"])){
  //繫結登入會員資料
  $query_RecLogin = "SELECT m_username, m_passwd, m_level,m_valid FROM memberdata WHERE m_username=? ";
  $stmt=$db_link->prepare($query_RecLogin);
  $stmt->bind_param("s", $_POST["username"]);
  $stmt->execute();
  //取出帳號密碼的值綁定結果
  $stmt->bind_result($username, $passwd, $level,$m_valid); 
  $stmt->fetch();
  $stmt->close();
  if($m_valid==1){
    //比對密碼，若登入成功則呈現登入狀態
    if(password_verify($_POST["passwd"],$passwd)){
      //計算登入次數及更新登入時間
      $query_RecLoginUpdate = "UPDATE memberdata SET m_login=m_login+1, m_logintime=NOW() WHERE m_username=?";
      $stmt=$db_link->prepare($query_RecLoginUpdate);
        $stmt->bind_param("s", $username);
        $stmt->execute(); 
        $stmt->close();
      //設定登入者的名稱及等級
      $_SESSION["loginMember"]=$username;
      $_SESSION["memberLevel"]=$level;
      //使用Cookie記錄登入資料
      if(isset($_POST["rememberme"])&&($_POST["rememberme"]=="true")){
        setcookie("remUser", $_POST["username"], time()+365*24*60);
        setcookie("remPass", $_POST["passwd"], time()+365*24*60);
      }else{
        if(isset($_COOKIE["remUser"])){
          setcookie("remUser", $_POST["username"], time()-100);
          setcookie("remPass", $_POST["passwd"], time()-100);
        }
      }
      //若帳號等級為 member 則導向首頁
      if($_SESSION["memberLevel"]=="member"){
        // header("Location: index2.php");
        //繫結登入會員資料
        $query_RecMember = "SELECT * FROM memberdata WHERE m_username = '{$_SESSION["loginMember"]}'";
        $RecMember = $db_link->query($query_RecMember); 
        $row_RecMember=$RecMember->fetch_assoc();
        header("Location: ../index2.php");
        
      //否則則導向管理中心
      }else{
        header("Location: m_admin.php"); 
      }
    }else{
      header("Location: ../index2.php?errMsg=1");
    }
  }else{
    header("Location: ../index2.php?errMsg=2");
  }
}

//執行登出動作
if(isset($_GET["logout"]) && ($_GET["logout"]=="true")){
  unset($_SESSION["loginMember"]);
  unset($_SESSION["memberLevel"]);
  header("Location: ../index2.php");
}

//錯誤後重新登入
if(isset($_GET["errMsg"]) && ($_GET["errMsg"]=="1")||isset($_GET["errMsg"]) && ($_GET["errMsg"]=="2")){
  if(isset($_POST["username"]) && isset($_POST["passwd"])){
    //繫結登入會員資料
    $query_RecLogin = "SELECT m_username, m_passwd, m_level, m_valid FROM memberdata WHERE m_username=?";
    $stmt=$db_link->prepare($query_RecLogin);
    $stmt->bind_param("s", $_POST["username"]);
    $stmt->execute();
    //取出帳號密碼的值綁定結果
    $stmt->bind_result($username, $passwd, $level, $m_valid); 
    $stmt->fetch();
    $stmt->close();
    //比對密碼，若登入成功則呈現登入狀態
    if($m_valid==1){
      //比對密碼，若登入成功則呈現登入狀態
      if(password_verify($_POST["passwd"],$passwd)){
        //計算登入次數及更新登入時間
        $query_RecLoginUpdate = "UPDATE memberdata SET m_login=m_login+1, m_logintime=NOW() WHERE m_username=?";
        $stmt=$db_link->prepare($query_RecLoginUpdate);
          $stmt->bind_param("s", $username);
          $stmt->execute(); 
          $stmt->close();
        //設定登入者的名稱及等級
        $_SESSION["loginMember"]=$username;
        $_SESSION["memberLevel"]=$level;
        //使用Cookie記錄登入資料
        if(isset($_POST["rememberme"])&&($_POST["rememberme"]=="true")){
          setcookie("remUser", $_POST["username"], time()+365*24*60);
          setcookie("remPass", $_POST["passwd"], time()+365*24*60);
        }else{
          if(isset($_COOKIE["remUser"])){
            setcookie("remUser", $_POST["username"], time()-100);
            setcookie("remPass", $_POST["passwd"], time()-100);
          }
        }
        //若帳號等級為 member 則導向首頁
        if($_SESSION["memberLevel"]=="member"){
          // header("Location: index2.php");
          //繫結登入會員資料
          $query_RecMember = "SELECT * FROM memberdata WHERE m_username = '{$_SESSION["loginMember"]}'";
          $RecMember = $db_link->query($query_RecMember); 
          $row_RecMember=$RecMember->fetch_assoc();
          header("Location: ../index2.php");
          
        //否則則導向管理中心
        }else{
          header("Location: member/m_admin.php"); 
        }
      }else{
        header("Location: ../index2.php?errMsg=1");
      }
    }else{
      header("Location: ../index2.php?errMsg=2");
    }
  }
}
?>
<!-- 補記密碼信 -->
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


//函式：自動產生指定長度的密碼
function MakePass($length) { 
	$possible = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"; 
	$str = ""; 
	while(strlen($str)<$length){ 
	  $str .= substr($possible, rand(0, strlen($possible)), 1); 
	}
	return($str); 
}
//檢查是否經過登入，若有登入則重新導向
if(isset($_SESSION["loginMember"]) && ($_SESSION["loginMember"]!="")){
	//若帳號等級為 member 則導向會員中心
	if($_SESSION["memberLevel"]=="member"){
		header("Location: member_center.php");
	//否則則導向管理中心
	}else{
		header("Location: m_admin.php");	
	}
}
//檢查是否為會員
use PHPMailer\PHPMailer\PHPMailer;
if(isset($_POST["m_username"])){
	$muser = GetSQLValueString($_POST["m_username"], 'string');
	//找尋該會員資料
	$query_RecFindUser = "SELECT m_username, m_email FROM memberdata WHERE m_username='{$muser}'";
	$RecFindUser = $db_link->query($query_RecFindUser);	
  
	if ($RecFindUser->num_rows==0){
		header("Location: m_passmail.php?mailErr=1&username={$muser}");
	}else{	
   
	//取出帳號密碼的值
		$row_RecFindUser=$RecFindUser->fetch_assoc();
		$name  = $row_RecFindUser["m_username"];
		$email = $row_RecFindUser["m_email"];	
		//產生新密碼並更新
		$newpasswd = MakePass(10);
		$mpass = password_hash($newpasswd, PASSWORD_DEFAULT);
		$query_update = "UPDATE memberdata SET m_passwd='{$mpass}' WHERE m_username='{$name}'";
		$db_link->query($query_update);
		//補寄密碼信
    $subject ="=?UTF-8?B?" . base64_encode("補寄密碼信"). "?=";
    $body = "親愛的 {$name} 您好，<br/>您的新密碼為：{$newpasswd} <br/>如有任何問題請盡速與我們聯絡<br/>網址: http://localhost/2021phpreview/project202102/index2.php<br/>Email: youremailaddress@gmail.com "; 

    require_once "./PHPMailer/PHPMailer.php";
    require_once "./PHPMailer/SMTP.php";
    require_once "./PHPMailer/Exception.php";


    $mail = new PHPMailer();

    //SMTP Settings
    $mail->isSMTP();
    $mail->Host = "smtp.gmail.com";
    $mail->SMTPAuth = true;
    $mail->Username = "youremailaddress@gmail.com";  //your email address
    $mail->Password = 'yourpassword'; // your email password
    $mail->Port = 465;
    $mail->SMTPSecure = "ssl";

    //Email Settings
    $mail->isHTML(true);
    $mail->setFrom($email, $name);
    $mail->addAddress($email); 
    $mail->Subject = ("youremailaddress@gmail.com ($subject)"); //your email address
    $mail->Body = $body;

    if ($mail->send()) {
      $status = "success";
      $response = "Email is sent!";
  } else {
      $status = "failed";
      $response = "Something is wrong: <br><br>" . $mail->ErrorInfo;
  }
		header("Location: m_passmail.php?mailStats=1");

    
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
      
    </style>
  </head>
  <body>
  <?php if(isset($_GET["mailStats"]) && ($_GET["mailStats"]=="1")){?>
    <script>alert('密碼信補寄成功！');window.location.href='../index2.php';</script>
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

          
          <a class="text-light " href="member/member_center.php">
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
        <li class="breadcrumb-item active" aria-current="page">忘記密碼</li>
      </ol>
    </nav>
    <!-- main  -->
    
    <div class="container mb-4">
    <!-- searching place  -->
        <div class="row mt-2 d-flex justify-content-center">
        <div class="text-success mt-4"> </div>
      </div>
      <div class="row mt-2 d-flex justify-content-center">
        
        <h1 class="">忘記密碼</h1>
      </div>
     <!-- form -->
     <div class=" d-flex justify-content-center bg-light mt-2 pt-4 pb-3">
      <form class="needs-validation h5 w-75 " action="" method="POST" name="formJoin" id="formJoin" onSubmit="return checkForm();" novalidate>
        
        <div class="form-group " style="min-height: 224px">
        <!-- style="min-height: 200px" -->
          <div class="col-md-12 mb-3 " align="center" >
            <?php if(isset($_GET["mailErr"]) && ($_GET["mailErr"]=="1")){?>
              <div class="bg-danger text-white pt-2 rounded " style="height:3rem; opacity: 0.75" >帳號 :「 <strong><?php echo $_GET["username"];?></strong>」沒有人使用！</div>
            <?php }?>
          </div>
        
            <div class="col-md-12 mb-3 ">
              <h3 class="text-info">忘記密碼了嗎?</h3>
              <label for="m_username" class="mb-5">請輸入您申請的帳號，系統將自動產生一個十位數的密碼寄到您註冊的信箱。</label><br/>
              <label for="m_username" class="">帳號 :</label>
              <input
                type="text"
                class="form-control "
                id="m_mail"
                placeholder="請輸入您的帳號"
                name="m_username"
                value=""
                required
              />
              <div class="invalid-feedback">請輸入帳號!</div>
            </div>

        </div>
        
        <div class="d-flex justify-content-center mb-3">
          <input name="action" type="hidden" id="action" value="join">
          <input class="btn btn-info btn-lg" style="width:18rem" type="submit" name="Submit" value="確認送出">
        </div>
        <hr/>

        <div class="d-flex justify-content-center ">
          <input class="btn btn-outline-info btn-lg" style="width:18rem" name="Submit2" value="回上一頁" onClick="window.history.back();">
        </div>

      </form>
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
     <!-- open modal -->
     <?php if(isset($_GET["errMsg"]) && ($_GET["errMsg"]=="1")||isset($_GET["errMsg"]) &&($_GET["errMsg"]=="2")){
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

  </body>
</html>
