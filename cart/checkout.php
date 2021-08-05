<?php
require_once("../connMysql.php");
//購物車開始
include("mycart.php");
session_start();
$cart =& $_SESSION['cart']; // 將購物車的值設定為 Session
if(!is_object($cart)) $cart = new myCart();
//購物車結束
//繫結產品目錄資料
$query_RecCategory = "SELECT *, count(product.productid) as productNum FROM category LEFT JOIN product ON category.categoryid = product.categoryid WHERE productname!='' && p_amount>0 GROUP BY category.categoryid, category.categoryname, category.categorysort ORDER BY category.categoryid ASC";
$RecCategory = $db_link->query($query_RecCategory);
//計算資料總筆數
$query_RecTotal = "SELECT count(productid) as totalNum FROM product";
$RecTotal = $db_link->query($query_RecTotal);
$row_RecTotal = $RecTotal->fetch_assoc();
?>
<?php
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
    header("Location: ../member/m_admin.php"); 
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
  // 是否被停用
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
        // header("Location: ../index2.php");
        header("Location: checkout.php");
        
      //否則則導向管理中心
      }else{
        header("Location: ../member/m_admin.php"); 
      }
    }else{
      header("Location: checkout.php?errMsg=1");
    }
  }else{
    header("Location: checkout.php?errMsg=2");
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
          header("Location: checkout.php");
          
        //否則則導向管理中心
        }else{
          header("Location: member/m_admin.php"); 
        }
      }else{
        header("Location: checkout.php?errMsg=1");
      }
    }else{
      header("Location: checkout.php?errMsg=2");
    }
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

    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css">
    <!-- font awesome -->
    <script src="https://kit.fontawesome.com/5ea815c1d0.js"></script>
    <link href="../mystyle.css" rel="stylesheet" type="text/css">
    <!-- slick css -->
    <!-- <link
      href="https://cdn.jsdelivr.net/foundation/5.5.0/css/foundation.css"
      rel="stylesheet"
    /> -->
    <link
      href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.css"
      rel="stylesheet"
    />
    <link
      href="https://kenwheeler.github.io/slick/slick/slick-theme.css"
      rel="stylesheet"
    />
    <link href="../productpage.css" rel="stylesheet" type="text/css">
    
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

     /* input number */

     input[type="number"] {
        -webkit-appearance: textfield;
        -moz-appearance: textfield;
        appearance: textfield;
      }

      input[type="number"]::-webkit-inner-spin-button,
      input[type="number"]::-webkit-outer-spin-button {
        -webkit-appearance: none;
      }

      .number-input {
        border: 2px solid #ddd;
        display: inline-flex;
        height:2.25rem
      }

      .number-input,
      .number-input * {
        box-sizing: border-box;
      }

      .number-input button {
        outline: none;
        -webkit-appearance: none;
        background-color: transparent;
        border: none;
        align-items: center;
        justify-content: center;
        width: 1.5rem;
        height: 1.75rem;
        cursor: pointer;
        margin: 0;
        position: relative;
      }

      .number-input button:after {
        display: inline-block;
        position: absolute;
        font-family: "Font Awesome 5 Free";
        font-weight: 900;
        content: "\f077";
        transform: translate(-50%, -50%) rotate(180deg);
      }
      .number-input button.plus:after {
        transform: translate(-50%, -50%) rotate(0deg);
      }

      .number-input input[type="number"] {
        font-family: sans-serif;
        max-width: 6rem;
        padding: 0.5rem;
        border: solid #ddd;
        border-width: 0 2px;
        font-size: 1.5rem;
        height: 2rem;
        
        /* font-weight: bold; */
        text-align: center;
      }
      
    </style>
    <script language="javascript">
      function checkForm(){	
        if(document.cartform.customername.value==""){
          alert("請填寫姓名!");
          document.cartform.customername.focus();
          return false;
        }
        if(document.cartform.customeremail.value==""){
          alert("請填寫電子郵件!");
          document.cartform.customeremail.focus();
          return false;
        }
        if(!checkmail(document.cartform.customeremail)){
          document.cartform.customeremail.focus();
          return false;
        }	
        if(document.cartform.customerphone.value==""){
          alert("請填寫電話!");
          document.cartform.customerphone.focus();
          return false;
        }
        if(document.cartform.customeraddress.value==""){
          alert("請填寫地址!");
          document.cartform.customeraddress.focus();
          return false;
        }
        return confirm('確定送出嗎？');
      }
      function checkmail(myEmail) {
        var filter  = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
        if(filter.test(myEmail.value)){
          return true;
        }
        alert("電子郵件格式不正確");
        return false;
      }
    </script>
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

          <a type="button" class="btn btn-outline-info ml-3" href="../member/signup.php">
            註冊
          </a>
          
          <?php }else{ ?>

          
          <a class="text-light " href="../member/m_center.php">
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
            您的帳號已停用！<br>
            如要回復請聯絡: zongyongduan23@gmail.com
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
                  <a class="text-secondary float-right" href="../member/m_passmail.php"
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
                  href="../member/signup.php"
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
        <li class="breadcrumb-item"><a href="cart.php">購物車</a></li>
        <li class="breadcrumb-item active" aria-current="page">結帳</li>
      </ol>
    </nav>
    <!-- main  -->
    
    <div class="container mb-4">
     <!-- searching place  -->
     <div class="">
      <form name="form1" method="get" action="../index2.php">
        <div class="row px-3">
          <div class="input-group">

            <input
              name="keyword"
              type="text"
              id="keyword"
              class="form-control rounded-left"
              placeholder="--商品查詢"
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

      <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <!-- <a class="navbar-brand" href="#">Navbar w/ text</a> -->
          <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarText" aria-controls="navbarText" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
          </button>
        <div class="collapse navbar-collapse row d-flex justify-content-between" id="navbarText">
          <ul class="navbar-nav mr-auto col">
            <li class="nav-item ">
              <a class="  btn btn-outline-secondary mt-2 ml-2 mr-1 text-nowrap" href="../index2.php" >
                所有產品
                <span class="badge badge-light border"><?php echo $row_RecTotal["totalNum"];?></span>
              </a>
            </li>
            <?php	while($row_RecCategory=$RecCategory->fetch_assoc()){ ?>
            <li class="nav-item">
              
                <a class="btn btn-outline-secondary mt-2 mr-1 text-nowrap" href="../index2.php?cid=<?php echo $row_RecCategory["categoryid"];?>" >
                <?php echo $row_RecCategory["categoryname"];?> 
                <span class="badge badge-light border"><?php echo $row_RecCategory["productNum"];?></span>
                </a>
             
            </li>
            <?php }?>
          </ul>

          <div class="mt-2 col-xl-4 col-lg-6 col-md-10">
            <form >
              <div id="price_select" class="form-row price_select" >
                <div div class="mt-2 mr-2 h6"> 價格: </div> 
                <div class="col-4">
                  <input  class="form-control" name="price1" type="number" id="price1" value="0" min="0">
                </div>
                <h6>~</h6>
                <div class="col-4">
                  <input  class="form-control" name="price2" type="number" id="price2" value="0" min="0" >
                </div>
                <input class="btn btn-info " type="submit" id="button2" value="查詢">
              </div>
            </form>
          </div>
        </div>
      </nav>

      <!-- cart detail -->
      <div class="mt-3 d-flex justify-content-between" > 
        <h3 style="padding:0; margin:0">
          <i class="h2 text-success fas fa-shopping-basket mr-3"></i>購物結帳
        </h3>
        <?php if($cart->itemcount>0){?>
        <div class=" pt-1">
          <a name="backbtn" class="h5 text-success" id="button4" href="../index2.php">
            <i class="bi bi-box-arrow-left h4 mr-2"></i>繼續購物
          </a>
        </div> 
        <?php }?>
        
      </div>
      <div class="mt-2">
      <td>
            <div class="normalDiv">
              <?php if($cart->itemcount > 0) {?>
               購物內容
              <table class="table">
                <thead>
                  <th>編號</th>
                  <th>產品名稱</th>
                  <th>數量</th>
                  <th>單價</th>
                  <th>小計</th>
                </thead>
                <?php		  
                  $i=0;
                foreach($cart->get_contents() as $item) {
                $i++;
                ?>
                <tbody >
                  <tr>
                    <td class="h6"><?php echo $i;?>.</td>
                    <td ><?php echo $item['info'];?></td>
                    <td >x <span class="h6"><?php echo $item['qty'];?></span></td>
                    <td class="h6">NT$ <?php echo number_format($item['price']);?></td>
                    <td class="h6">NT$ <?php echo number_format($item['subtotal']);?></td>
                  </tr>
                  <?php }?>
                  <tr class="bg-white">
                    <td class="h6">運費</td>
                    <td ></td>
                    <td ></td>
                    <td ></td>
                    <td class="h6">NT$ <?php echo number_format($cart->deliverfee);?></td>
                  </tr>
                  <tr>
                    <td class="h6">總計</td>
                    <td ></td>
                    <td ></td>
                    <td ></td>
                    <td class="text-danger h6">NT$ <?php echo number_format($cart->grandtotal);?></td>
                  </tr>
                </tbody>
              </table>
              <hr width="100%" size="1" />
              <div class="d-flex justify-content-between"><span>客戶資訊</span>  <span class="text-danger">* 為必填資料</span></div>
              <form action="cartreport.php" method="post" name="cartform" id="cartform" onSubmit="return checkForm();">
                <table class="table">
                  <tr>
                    <th >姓名 <span class="text-danger">*</span></th>
                    <td >
                        <input
                        class="form-control " 
                        type="text" 
                        name="customername" 
                        id="customername" 
                        value="<?php if(isset($_SESSION["loginMember"]) && ($_SESSION["loginMember"]!="")){echo $row_RecMember["m_name"];}?>"
                        <?php if(isset($_SESSION["loginMember"]) && ($_SESSION["loginMember"]!="")){echo "readonly";}?>
                        >
                        </td>
                  </tr>
                  <tr>
                    <th >電子郵件 <span class="text-danger">*</span></th>
                    <td >
                        <input
                        class="form-control " 
                        type="text" 
                        name="customeremail" 
                        id="customeremail" 
                        value="<?php if(isset($_SESSION["loginMember"]) && ($_SESSION["loginMember"]!="")){ echo $row_RecMember["m_email"];}?>"
                        <?php if(isset($_SESSION["loginMember"]) && ($_SESSION["loginMember"]!="")){echo "readonly";}?>
                        >
                        </td>
                  </tr>
                  <tr>
                    <th >電話 <span class="text-danger">*</span></th>
                    <td >
                        <input
                        class="form-control " 
                        type="text"
                        name="customerphone"
                        id="customerphone"
                        value="<?php if(isset($_SESSION["loginMember"]) && ($_SESSION["loginMember"]!="")){ echo $row_RecMember["m_phone"];}?>"
                        <?php if(isset($_SESSION["loginMember"]) && ($_SESSION["loginMember"]!="")){echo "readonly";}?>
                        >
                        </td>
                  </tr>
                  <tr>
                    <th >住址 <span class="text-danger">*</span></th>
                    <td >
                        <input 
                        class="form-control "
                        name="customeraddress" 
                        type="text" 
                        id="customeraddress" 
                        size="40" 
                        value="<?php if(isset($_SESSION["loginMember"]) && ($_SESSION["loginMember"]!="")){echo $row_RecMember["m_address"];}?>">
                        </td>
                  </tr>
                  <tr>
                    <th >付款方式 <span class="text-danger">*</span></th>
                    <td >
                        <select class="form-control " name="paytype" id="paytype">
                          <option value="ATM匯款" selected>ATM匯款</option>
                          <option value="線上刷卡"><i class="bi bi-credit-card"></i>線上刷卡</option>
                          <option value="貨到付款">貨到付款</option>
                        </select>
                      </td>
                  </tr>
                </table>
                <hr width="100%" size="1" />
                <div align="center">
                  <a name="backbtn" class=" btn btn-warning btn-lg text-white mx-2" id="button4" href="cart.php">
                    <i class="bi bi-cart"></i>
                    <span>回購物車</span>
                  </a>

                  <input name="cartaction" type="hidden" id="cartaction" value="update">

                  <button 
                  class="btn btn-success btn-lg text-white mx-2" 
                  type="submit" 
                  form="cartform" 
                  value="Submit"
                  >
                  <i class="fas fa-hand-holding-usd"></i> 送出訂單
                  </button>
                  
                </div>
              </form>
            </div>
            <?php }else{ ?>
              <div class="">
              <div align="center"><img class=" w-50"  src="../images/cart_empty.png" alt=""></div>
              
              <div class="h4" align="center">
                購物車空了喔! <a href="../index2.php"><i class="bi bi-box-arrow-left h4 mr-2"></i>回到商城</a>
              </div>
              </div>
            <?php } ?>
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
     
     
   
    
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/modernizr/2.8.3/modernizr.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.js"></script>
    
    <!-- <script
      src="https://code.jquery.com/jquery-3.3.1.slim.min.js"
      integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo"
      crossorigin="anonymous"
    ></script> -->
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
       $(window).resize(function() {
        if ($(window).width() < 992) {

          $('.price_select').removeClass("justify-content-end");

        }else{
          $('.price_select').addClass("justify-content-end");
        } 
      });
    </script>
  <script>
      $(document).on("keyup", ".amount", function () {
        var _this = $(this);
        var min = parseInt(_this.attr("min")) || 0; // if min attribute is not defined, 1 is default
        var max = parseInt(_this.attr("max")) || 100; // if max attribute is not defined, 100 is default
        var val = parseInt(_this.val()) || min - 1; // if input char is not a number the value will be (min - 1) so first condition will be true
        if (val < min) _this.val(min);
        if (val > max) _this.val(max);
      });
</script>
<script>
  <?php	foreach($cart->get_contents() as $item) { 
    $query_RecMax= "SELECT productid,p_amount FROM product WHERE productid= '{$item['id']}'";
    $RecMax = $db_link->query($query_RecMax);
    
    foreach($RecMax as $row){
    $qtMax=$row["p_amount"];
  ?>
  var max=<?php echo $qtMax?>;
  var amount=<?php echo $item['qty']?>;
  var form = document.getElementById("cartform");
  // console.log(max);
  // console.log(amount);
  // console.log(<?php echo $row["productid"]?>)
  if(amount>max){
    // console.log("it is too big~~~");
    cartform.submit();
      
  }else{
    // console.log("it is ok")
  }
  
  
  // var button3= document.getElementById("button3").value;
  // console.log(button3)
  <?php }} ?>
</script>
 

  </body>
</html>
<?php $db_link->close();?>