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
<!-- 註冊 -->
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

if(isset($_POST["action"])&&($_POST["action"]=="join")){
	
	//找尋帳號是否已經註冊
	$query_RecFindUser = "SELECT m_username FROM memberdata WHERE m_username='{$_POST["m_username"]}'";
	$RecFindUser=$db_link->query($query_RecFindUser);
	if ($RecFindUser->num_rows>0){
		header("Location: signup.php?joinErr=1&username={$_POST["m_username"]}");
	}else{
	//若沒有執行新增的動作	
		$query_insert = "INSERT INTO memberdata (m_name, m_username, m_passwd, m_sex, m_birthday, m_email, m_url, m_phone, m_address, m_jointime,m_valid) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(),1)";
		$stmt = $db_link->prepare($query_insert);
		$stmt->bind_param("sssssssss", 
			GetSQLValueString($_POST["m_name"], 'string'),
			GetSQLValueString($_POST["m_username"], 'string'),
			password_hash($_POST["m_passwd"], PASSWORD_DEFAULT),
			GetSQLValueString($_POST["m_sex"], 'string'),
			GetSQLValueString($_POST["m_birthday"], 'string'),
			GetSQLValueString($_POST["m_email"], 'email'),
			GetSQLValueString($_POST["m_url"], 'url'),
			GetSQLValueString($_POST["m_phone"], 'string'),
			GetSQLValueString($_POST["m_address"], 'string'));
		$stmt->execute();
		$stmt->close();
		$db_link->close();
		header("Location: signup.php?loginStats=1");
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
  <?php if(isset($_GET["loginStats"]) && ($_GET["loginStats"]=="1")){?>
  <script language="javascript">
  alert('會員新增成功\n請用申請的帳號密碼登入。');
  window.location.href='../index2.php';		  
  </script>
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

        <div class=" ml-4" style="display: inline-block;">
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
        <li class="breadcrumb-item active" aria-current="page">註冊會員</li>
      </ol>
    </nav>
    <!-- main  -->
    
    <div class="container mb-4">
    <!-- searching place  -->
        <div class="row mt-2 d-flex justify-content-center">
        <div class="text-success">sign up </div>
      </div>
      <div class="row mt-2 d-flex justify-content-center">
        
        <h1 class="">註冊會員</h1>
      </div>
     <!-- form -->
     <div class=" d-flex justify-content-center bg-light mt-2 pt-4 pb-3">
      <form class="needs-validation h5 w-75 " action="" method="POST" name="formJoin" id="formJoin" onSubmit="return checkForm();" novalidate>
        
        <div class="form-group  ">
        <div class="col-md-12 mb-3 " align="center">
          <?php if(isset($_GET["joinErr"]) && ($_GET["joinErr"]=="1")){?>
            <div class="bg-danger text-white pt-2 rounded " style="height:3rem; opacity: 0.75" >帳號 <?php echo $_GET["username"];?> 已經有人使用！</div>
          <?php }?>
          </div>
      
          <div class="col-md-12 mb-3 ">
            <label for="m_username"><i class="text-danger">* </i>會員帳號</label>
            <input
              type="text"
              class="form-control "
              id="m_username"
              placeholder="請輸入帳號"
              name="m_username"
              value=""
              required
            />
            <small>請填入5~12個字元以內的小寫英文字母、數字、以及_ 符號。</small>
            <div class="valid-feedback">Looks good!</div>
            <div class="invalid-feedback">請輸入帳號!</div>
          </div>
          <div class="col-md-12 mb-3">
            <label for="m_passwd"><i class="text-danger">* </i>設定密碼</label>
            <input 
              id="m_passwd" 
              type="password"
              class="form-control" 
              name="m_passwd" 
              value=""
              placeholder="請設定密碼"
              required>
            <i toggle="#password-field" class="fa fa-fw fa-eye-slash field_icon toggle-password float-right mr-4" style="margin-top:-28px"></i>
            <small>請填入5~10個字元以內的英文字母、數字、以及各種符號組合。</small>
            <div class="valid-feedback">Looks good!</div>
            <div id="mpassErr" class="invalid-feedback">請設定密碼!</div>
          </div>
          <div class="col-md-12 mb-3">
            <label for="validationCustom02"><i class="text-danger">* </i>密碼確認</label>
            <input 
              id="m_passwdrecheck" 
              type="password"
              class="form-control" 
              name="m_passwdrecheck" 
              value=""
              placeholder="請再次確認密碼"
              required>
            <i toggle="#password-field" class="fa fa-fw fa-eye-slash field_icon toggle-password2 float-right mr-4" style="margin-top:-28px"></i>
            <div class="valid-feedback">Looks good!</div>
            <div class="invalid-feedback">請確認密碼</div>
          </div>
          <div class="col-md-12 mb-3">
            <label for="m_name"><i class="text-danger">* </i>姓名</label>         
              <input
                type="text"
                class="form-control"
                id="m_name"
                name="m_name"
                placeholder="請輸入真實姓名"
                required
              />
              <div class="valid-feedback">Looks good!</div>
              <div class="invalid-feedback">請輸入您的姓名</div>
          
          </div>
          <div class="col-md-12 mb-3">
            <label for="validationCustomUsername"><i class="text-danger">* </i>生日</label>         
              <input 
                class="form-control" 
                type="date" 
                value="" 
                name="m_birthday" 
                id="m_birthday" 
                max="<?= date('Y-m-d'); ?>"
                required>
              <small>為西元格式(YYYY-MM-DD)。</small>
              <div class="valid-feedback">Looks good!</div>
              <div class="invalid-feedback">請輸入您的生日</div>
          
          </div>
          <div class="col-md-12 mb-3 ">
            <label for="validationCustomUsername"><i class="text-danger">* </i>性別</label>  
            <div class="row ml-1">   
              <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="m_sex"  value="男" checked>
                <label class="form-check-label" for="gridRadios1">
                男性
                </label>
              </div>
              <div class="form-check form-check-inline ml-4">
                <input class="form-check-input" type="radio" name="m_sex"  value="女" >
                <label class="form-check-label" for="gridRadios2">
                  女性
                </label>
              </div>
              
            </div>    
              <div class="valid-feedback">Looks good!</div>
              <div class="invalid-feedback">請輸入您的性別</div>
          
          </div>
          <div class="col-md-12 mb-3">
            <label for="validationCustomUsername"><i class="text-danger">* </i>Email 信箱</label>         
              <input 
                class="form-control" 
                type="email" 
                value="" 
                name="m_email" 
                id="m_email" 
                placeholder="請輸入您的email, ex: name@example.com"
                required>
              <small>
              請確定此email為可使用狀態，以方便未來系統使用，如補寄會員密碼信。</small>
              <div class="valid-feedback">Looks good!</div>
              <div class="invalid-feedback">請輸入您的email</div>
          
          </div>
          <div class="col-md-12 mb-3">
            <label for="validationCustomUsername"><i class="text-danger "> </i>電話</label>         
              <input 
                class="form-control" 
                type="text" 
                value="" 
                name="m_phone" 
                id="m_phone" 
                placeholder="請輸入您的電話"
                required
                >
             
          
          </div>
          <div class="col-md-12 mb-3">
            <label for="validationCustomUsername"><i class="text-danger "> </i>住址</label>         
              <input 
                class="form-control" 
                type="text" 
                value="" 
                name="m_address" 
                id="m_address" 
                placeholder="請輸入您的住址"
                >
          </div>
          
          <div class="form-check ml-3">
            <input
              class="form-check-input"
              type="checkbox"
              value=""
              id="invalidCheck"
              required
              checked
            />
            <label class="form-check-label" for="invalidCheck"><i class="text-danger">* </i>
            同意<a class="text-info"href="membership.php">使用者協定</a>
            </label>
            <div class="valid-feedback">Looks good!</div>
            <div class="invalid-feedback ml-3">
            請先閱讀並同意使用者協定
            </div>
          </div>
        </div>
        
        <div class="d-flex justify-content-center">
        
        <input name="action" type="hidden" id="action" value="join">
        <input class="btn btn-info btn-lg" style="width:18rem" type="submit" name="Submit2" value="確認送出">
        
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

    <script>
    
    function checkForm(){
	if(document.formJoin.m_username.value==""){		
		alert("請填寫帳號!");
		document.formJoin.m_username.focus();
		return false;
	}else{
		uid=document.formJoin.m_username.value;
		if(uid.length<5 || uid.length>12){
      document.getElementById("p1").innerHTML = "New text!";
			alert( "您的帳號長度只能5至12個字元!" );
			document.formJoin.m_username.focus();
			return false;}
		if(!(uid.charAt(0)>='a' && uid.charAt(0)<='z')){
			alert("您的帳號第一字元只能為小寫字母!" );
			document.formJoin.m_username.focus();
			return false;}
		for(idx=0;idx<uid.length;idx++){
			if(uid.charAt(idx)>='A'&&uid.charAt(idx)<='Z'){
				alert("帳號不可以含有大寫字元!" );
				document.formJoin.m_username.focus();
				return false;}
			if(!(( uid.charAt(idx)>='a'&&uid.charAt(idx)<='z')||(uid.charAt(idx)>='0'&& uid.charAt(idx)<='9')||( uid.charAt(idx)=='_'))){
				alert( "您的帳號只能是數字,英文字母及「_」等符號,其他的符號都不能使用!" );
				document.formJoin.m_username.focus();
				return false;}
			if(uid.charAt(idx)=='_'&&uid.charAt(idx-1)=='_'){
				alert( "「_」符號不可相連 !\n" );
				document.formJoin.m_username.focus();
				return false;}
		}
	}
	if(!check_passwd(document.formJoin.m_passwd.value,document.formJoin.m_passwdrecheck.value)){
		document.formJoin.m_passwd.focus();
		return false;}	
	if(document.formJoin.m_name.value==""){
		alert("請填寫姓名!");
		document.formJoin.m_name.focus();
		return false;}
	if(document.formJoin.m_birthday.value==""){
		alert("請填寫生日!");
		document.formJoin.m_birthday.focus();
		return false;}
	if(document.formJoin.m_email.value==""){
		alert("請填寫電子郵件!");
		document.formJoin.m_email.focus();
		return false;}
	if(!checkmail(document.formJoin.m_email)){
		document.formJoin.m_email.focus();
		return false;}
	return confirm('確定送出嗎？');
}
function check_passwd(pw1,pw2){
	if(pw1==''){
		alert("密碼不可以空白!");
		return false;}
	for(var idx=0;idx<pw1.length;idx++){
		if(pw1.charAt(idx) == ' ' || pw1.charAt(idx) == '\"'){
			alert("密碼不可以含有空白或雙引號 !\n");
			return false;}
		if(pw1.length<5 || pw1.length>10){
			alert( "密碼長度只能5到10個字母 !\n" );
			return false;}
		if(pw1!= pw2){
			alert("密碼二次輸入不一樣,請重新輸入 !\n");
			return false;}
	}
	return true;
}
function checkmail(myEmail) {
	var filter  = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
	if(filter.test(myEmail.value)){
		return true;}
	alert("電子郵件格式不正確");
	return false;
}
    
    </script>
    
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
