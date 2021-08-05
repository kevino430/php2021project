<?php
require_once("../connMysql.php");
//購物車開始
require_once("mycart.php");
session_start();
$cart =& $_SESSION['cart']; // 將購物車的值設定為 Session
if(!is_object($cart)) $cart = new myCart();
// 新增購物車內容
if(isset($_POST["cartaction"]) && ($_POST["cartaction"]=="add")){
	$cart->add_item($_POST['id'],$_POST['qty'],$_POST['price'],$_POST['name']);
	// header("Location: ../index2.php");
  header('Location: ' . $_SERVER['REQUEST_URI']);
}
//購物車結束
?>
<!-- 登入 -->
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
        header("Location: product2.php?id=".$_GET["id"]);
        
      //否則則導向管理中心
      }else{
        header("Location: ../member/m_admin.php"); 
      }
    }else{
      header("Location: product2.php?id=".$_GET["id"]."&errMsg=1");
    }
  }else{
    header("Location: product2.php?id=".$_GET["id"]."&errMsg=2");
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
          header("Location: product2.php?id=".$_GET["id"]);
          
        //否則則導向管理中心
        }else{
          header("Location: ../member/m_admin.php"); 
        }
      }else{
        header("Location: product2.php?id=".$_GET["id"]."&errMsg=1");
      }
    }else{
      header("Location: product2.php?id=".$_GET["id"]."&errMsg=2");
    }
  }
}
?>
<?php
//繫結產品資料
if(isset($_SESSION["loginMember"]) && ($_SESSION["loginMember"]!="")){
  $query_RecProduct = "SELECT *,count( product_image.picture_id ) AS picNum FROM product LEFT JOIN (SELECT* FROM phplove WHERE phplove.memberid='{$row_RecMember["m_id"]}')phplove ON product.productid=phplove.product_id JOIN category ON product.categoryid = category.categoryid LEFT JOIN product_image ON product.productid=product_image.product_id WHERE product.productid=?";
}else{
  $query_RecProduct = "SELECT *,count( product_image.picture_id ) AS picNum FROM product JOIN category ON product.categoryid = category.categoryid LEFT JOIN product_image ON product.productid=product_image.product_id WHERE product.productid=?";
}

$stmt = $db_link->prepare($query_RecProduct);
$stmt->bind_param("i", $_GET["id"]);
$stmt->execute();
$RecProduct = $stmt->get_result();
$row_RecProduct = $RecProduct->fetch_assoc();
//繫結產品照片
$query_RecProimage = "SELECT * FROM product_image WHERE product_id=? ";
$stmt = $db_link->prepare($query_RecProimage);
$stmt->bind_param("i", $_GET["id"]);
$stmt->execute();
$RecProimage = $stmt->get_result();
$row_RecProimage = $RecProimage->fetch_assoc();
//繫結相關產品
$thisProductid=$row_RecProduct["productid"];
$RelatedCategoryid=$row_RecProduct["categoryid"];
$query_RealtedPro = "SELECT * FROM product LEFT JOIN category ON category.categoryid = product.categoryid LEFT JOIN product_image ON product.productid = product_image.product_id WHERE product.categoryid=? && p_amount!=0 GROUP BY product.productid ORDER BY product.productid DESC";
$stmt = $db_link->prepare($query_RealtedPro);
$stmt->bind_param("i", $RelatedCategoryid);
$stmt->execute();
$RealtedPro = $stmt->get_result();
$row_RealtedPro = $RealtedPro->fetch_assoc();
//繫結產品目錄資料
$query_RecCategory = "SELECT *, count(product.productid) as productNum FROM category LEFT JOIN product ON category.categoryid = product.categoryid WHERE productname!='' && p_amount>0 GROUP BY category.categoryid, category.categoryname, category.categorysort ORDER BY category.categoryid ASC";
$RecCategory = $db_link->query($query_RecCategory);
//計算資料總筆數
$query_RecTotal = "SELECT count(productid) as totalNum FROM product";
$RecTotal = $db_link->query($query_RecTotal);
$row_RecTotal = $RecTotal->fetch_assoc();
?>

<!-- 商品最愛 -->
<?php
//檢查是否經過登入，若有登入才進行商品最愛變更
if(isset($_SESSION["loginMember"]) && ($_SESSION["loginMember"]!="")){

// 加入最愛
if(isset($_POST["action"])&&($_POST["action"]=="love")){	
  $query_RecFindLove = "SELECT product_id FROM phplove WHERE memberid='{$_POST["memberid"]}' AND product_id='{$_POST["productid"]}'";
  $RecFindLove=$db_link->query($query_RecFindLove);
  if($RecFindLove->num_rows>0){
    // 二次加入最愛
  $sql_query = "UPDATE phplove SET love_or_hate=1 WHERE memberid=? AND product_id=?";
  $stmt = $db_link -> prepare($sql_query);
  $stmt -> bind_param("ii", $_POST["memberid"],$_POST["productid"]);
  $stmt -> execute();
  $stmt -> close();
  $db_link -> close();
  // echo "hi";
  //重新導向回到主畫面
  header('Location: '. $_SERVER['REQUEST_URI']);
  }else{
    // 如果沒有,則新增
    $sql_querylove = "INSERT INTO phplove (product_id,love_or_hate,memberid) VALUES (?, ?, ?)";
    $stmt = $db_link -> prepare($sql_querylove);
    $stmt -> bind_param("iii",$pid,$lh,$mid);

    $pid= $_POST["productid"];
    $lh = 1;
    $mid = $_POST["memberid"];
    

    $stmt -> execute();

    $stmt -> close();

    $db_link -> close();
    // echo "hello world";
    header('Location: '. $_SERVER['REQUEST_URI']);
  }
}
// 刪除最愛
if(isset($_POST["action"])&&($_POST["action"]=="hate")){	
  $sql_query = "DELETE FROM  phplove WHERE memberid=? AND product_id=?";
  
  $stmt = $db_link -> prepare($sql_query);
  $stmt -> bind_param("ii", $_POST["memberid"],$_POST["productid"]);
  $stmt -> execute();
  $stmt -> close();
  $db_link -> close();
  //重新導向回到主畫面
  header('Location: '. $_SERVER['REQUEST_URI']);
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

      /* slider css */
      .js .slider-single > div:nth-child(1n + 2) {
        display: none;
      }

      .js .slider-single.slick-initialized > div:nth-child(1n + 2) {
        display: block;
      }

      .slider-nav .slick-slide {
        cursor: pointer;
      }
      .slide-arrow {
        position: absolute;
        top: 50%;
        margin-top: -15px;
      }
      .prev-arrow {
        left: -20px;
      }
      .next-arrow {
        right: -20px;
      }

      /* heart css */
      .checkbox {
        position:absolute;
        font-family: Arial, sans-serif;
        font-size: 45px;
        display: block;
        margin: 5px;
        cursor: pointer;
      }
      .checkbox input {
        display: none;
        position: relative;
        z-index: -9999;
      }

      /* .checkbox input:checked + i {
        color: #fff;
      } */

      .checkbox input:checked + i.fa-heart {
        color: crimson;
        position: relative; 
        top:-60px;
        right:0.8px;
        
      }
      .checkbox input + i.fa-heart {
        color: rgba(99, 99, 99, 0.5);
        position: relative;
        top:-60px;
        right:0.8px;
        
      }
      .boxitem {
        width: 52px;
        height: 52px;
        background: rgb(141, 141, 141, 0.5);
        border-radius: 50%;
        position: relative;
        left:0.6px;
        
      }
      .heartbox{
        position: absolute;
        
        right:1.5px;
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
              <a href="cart.php">
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
        <li class="breadcrumb-item"><a href="../index2.php?cid=<?php echo $row_RecProduct["categoryid"];?>"><?php echo $row_RecProduct["categoryname"];?></a></li>
        <li class="breadcrumb-item active" aria-current="page"><?php echo $row_RecProduct["productname"];?></li>
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
              <a class=" btn btn-outline-secondary mt-2 ml-2 mr-1 text-nowrap" href="../index2.php" >
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

     
      <!-- product detail -->
      <!-- error test  -->
      <?php if($row_RecProduct["p_amount"]==0 || $row_RecProduct["p_amount"]==""){?> 
        <div align="center">
        <img src="../images/nothing-found.png" alt="">
        
        </div>
        <div class=" h4" align="center">非常抱歉，目前並無此相關商品。<a href="../index2.php">回到商城</a></div>
        
        <?php  }else{ ?>
      <div class="mt-2 row " >
         
        <div id="page" class="col-sm-6">
          <div class="row">
            <div class="column">
              <div class="slider slider-single mb-2 border" align="center">
              <?php if($row_RecProduct["picNum"]==0){?>
              <div style="">
                  <img src="../images/nopic.png" alt="暫無圖片" style="max-height:372px; width:100%; object-fit: contain;"/>  
              </div><?php }else{?>
              <?php 
              $RecProimage->data_seek(0);
              while($row_RecProimage=$RecProimage->fetch_assoc()){ ?>
                <div style="height:372px">
                <img src="../proimg/<?php echo $row_RecProimage["image_url"]?>" alt="" style="max-height:372px; width:100%; object-fit: contain;"/>
                </div> 
              <?php }?>
              <?php }?>
              </div>
              
              <div class="slider slider-nav border" style="max-height: 152px;">
                  <?php if($row_RecProduct["picNum"]==0){?>
                    <div class="mx-1">
                    <img src="../images/nopic.png" alt="暫無圖片" />
                    </div>
                  <?php }else{?>
                    <?php 
                    $RecProimage->data_seek(0);
                    while($row_RecProimage=$RecProimage->fetch_assoc()){ ?>
                    <div class="mx-1">
                    <img src="../proimg/<?php echo $row_RecProimage["image_url"];?>" alt="" style="max-height: 150px;" />
                    </div>
                    <?php }?>
                  <?php }?>
              </div>
              
            </div>
          </div>
        </div>
        <div class="col-sm-6">
          <h2 class="text-info"><?php echo $row_RecProduct["productname"];?></h2>
          <table class="table table-borderless border-0 ">
          <tbody>
            <tr>
              <th scope="row" class="h4 w-25 bg-white text-nowrap">產品品牌:</th>
              <td colspan="2" class="h4 bg-white">
              <!-- <img class=" mt-1" src="../images/<?php echo $row_RecProduct["categoryimg"];?>" alt="<?php echo $row_RecProduct["categoryimg"];?>" style="max-height:28px;"/> -->
              <?php echo $row_RecProduct["categoryname"];?>
              </td>
            </tr>
            <tr>
              <th scope="row" class="h4 w-25 bg-white text-nowrap">產品價格:</th>
              <td colspan="2" class="h5 bg-white">NT$ <?php echo $row_RecProduct["productprice"];?></td>
            </tr>
            <tr>
              <th scope="row" class="h4 w-25 bg-white text-nowrap">產品說明:</th>
              <td colspan="2" class="h5 bg-white"><?php echo nl2br($row_RecProduct["description"]);?></td>
            </tr>
            <?php if ($row_RecProduct["p_amount"]<=10){?>
            <tr>
              <th scope="row" class="h4 w-25 bg-white text-nowrap text-danger">只剩最後:</th>
              <td colspan="2" class="h3 bg-white text-danger"><?php echo nl2br($row_RecProduct["p_amount"]);?>台</td>
            </tr>
            <?php } ?>
            <tr>
              <th scope="row" class="h4 w-25 bg-white text-nowrap">
              <!-- heart start -->
              <?php if(isset($_SESSION["loginMember"]) && ($_SESSION["loginMember"]!="")){?>
              <form method="post" action="">
                <div class="">
                  <div class="boxitem"></div>
                  <label class="checkbox "  >
                    <input type="checkbox" id="heart" onchange="this.form.submit()"
                    <?php if ($row_RecProduct["love_or_hate"]==1){echo "checked";} ?>/>
                    <i class="fa fa-heart" ></i>
                  </label>
                  <input name="action" class="fallinlove" type="hidden" id="fallinlove" 
                  value="<?php if ($row_RecProduct["love_or_hate"]!=1){echo "love";}else{echo "hate";} ?>"/>
                  <input name="memberid" type="hidden" value="<?php echo $row_RecMember["m_id"];?>">
                  <input name="productid" type="hidden" value="<?php echo $row_RecProduct["productid"];?>">
                </div>
              </form>
              <?php }else{ ?>

                  <div class="">
                    <div class="boxitem"></div>
                    <label class="checkbox">
                      <input type="checkbox" disabled/>
                      <i class="fa fa-heart" onclick="myFunction()" data-toggle="modal" data-target="#exampleModalCenterLogin"></i>
                      <script>
                      function myFunction() {
                        alert("登入後才可加入最愛!");
                      }
                      </script>
                  </div>
                    
              <?php }?>
               
              <!-- heart start -->
              </th>
            </tr>
          </tbody>
          </table>
            
                
            
            <div class="row pr-4">
              <div class="col-lg-6 px-3 mt-2">
                <form name="form3" method="post" action="">
                  <input name="id" type="hidden" id="id" value="<?php echo $row_RecProduct["productid"];?>">
                  <input name="name" type="hidden" id="name" value="<?php echo $row_RecProduct["productname"];?>">
                  <input name="price" type="hidden" id="price" value="<?php echo $row_RecProduct["productprice"];?>">
                  <input name="qty" type="hidden" id="qty" value="1">
                  <input name="cartaction" type="hidden" id="cartaction" value="add">
                  <button type="submit" class="btn btn-outline-info text-nowrap w-100"><i class="bi bi-cart-plus-fill h4 mr-2"></i>加入購物車</button>
                </form>
              </div>
              <div class="col-lg-6 px-3 mt-2">
                <a class="btn btn-outline-warning text-nowrap w-100" href="../index2.php">
                  <i class="bi bi-box-arrow-left h4 mr-2"></i>繼續購物
                </a>
              </div>
            </div>
        </div>
      </div>
      <!-- other album -->
      <div class="mt-5 pt-5 ">
        <h2 class="text-info mt-6">相關產品</h2>
        <div class=" mt-3 slicerCard">
        <!-- card start -->
        <?php
        $RealtedPro ->data_seek(0);
        while($row_RealtedPro=$RealtedPro->fetch_assoc()){?>  <!-- fetch start -->
          <?php if($row_RealtedPro["productid"]==$thisProductid){?>
          <?php }else{?>
            <div class="card mx-2">
              <div class="border" >
                <a href="product2.php?id=<?php echo $row_RealtedPro["productid"];?>">
                  <?php if($row_RealtedPro["image_url"]==""){?>
                    <img class="card-img-top"  src="../images/nopic.png" alt="暫無圖片" style="height:180px;object-fit: contain;"/>
                  <?php }else{?>

                    <img class="card-img-top" src="../proimg/<?php echo $row_RealtedPro["image_url"];?>" alt="<?php echo $row_RealtedPro["image_url"];?>" style="height:180px;object-fit: contain;" />
                    
                  <?php }?>
                </a>
              </div>
              <div class="card-body">
              <div style="height:48px">
                <p class="card-title" style="font-size: 19px; font-weight:600;">
                  <a class="text-dark" style="text-decoration:none;" href="product2.php?id=<?php echo $row_RealtedPro["productid"];?>">
                    <?php echo $row_RealtedPro["productname"];?>
                  </a>
                </p>
                
                </div>

                <img class=" mt-1" src="../images/<?php echo $row_RealtedPro["categoryimg"];?>" alt="<?php echo $row_RealtedPro["categoryimg"];?>" style="height:20px"/>
                <div class="card-text mt-2 text-secondary row" style="font-size:19px">
                  <div class="col">NT$</div>
                  <div class="col h4"><?php echo $row_RealtedPro["productprice"];?></div>
                </div>
                
                <div align="center">
                <!-- go see see  -->
                <a type="submit" class="btn btn-outline-info mt-2 w-100" href="product2.php?id=<?php echo $row_RealtedPro["productid"];?>"><i class="fas fa-search mr-2"></i>去看看</a>
                </div>

              </div>
            </div>
          <?php }?>
        <?php } ?>
        
        <!-- card end -->
        
        </div>
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
    <!-- slick js -->
    <script>
      $(".slider-single").slick({
        slidesToShow: 1,
        slidesToScroll: 1,
        arrows: false,
        fade: false,
        adaptiveHeight: true,
        infinite: false,
        useTransform: true,
        speed: 400,
        cssEase: "cubic-bezier(0.77, 0, 0.18, 1)",
      });

      $(".slider-nav")
        .on("init", function (event, slick) {
          $(".slider-nav .slick-slide.slick-current").addClass("is-active");
        })
        .slick({
          slidesToShow: 3,
          slidesToScroll: 3,
          dots: false,
          focusOnSelect: false,
          arrows: true,
          infinite: true,
          prevArrow:
            '<a class="prev-arrow slide-arrow text-info h4"><i class="fas fa-chevron-left"></i></a>',
          nextArrow:
            '<a class="next-arrow slide-arrow text-info h4"><i class="fas fa-chevron-right"></i></a>',
          responsive: [
            {
              breakpoint: 1024,
              settings: {
                slidesToShow: 5,
                slidesToScroll: 5,
              },
            },
            {
              breakpoint: 640,
              settings: {
                slidesToShow: 4,
                slidesToScroll: 4,
              },
            },
            {
              breakpoint: 420,
              settings: {
                slidesToShow: 3,
                slidesToScroll: 3,
              },
            },
          ],
        });

      $(".slider-single").on(
        "afterChange",
        function (event, slick, currentSlide) {
          $(".slider-nav").slick("slickGoTo", currentSlide);
          var currrentNavSlideElem =
            '.slider-nav .slick-slide[data-slick-index="' + currentSlide + '"]';
          $(".slider-nav .slick-slide.is-active").removeClass("is-active");
          $(currrentNavSlideElem).addClass("is-active");
        }
      );

      $(".slider-nav").on("click", ".slick-slide", function (event) {
        event.preventDefault();
        var goToSingleSlide = $(this).data("slick-index");

        $(".slider-single").slick("slickGoTo", goToSingleSlide);
      });
    </script>
    <script>
      $(".slicerCard").slick({
        dots: false,
        infinite: false,
        speed: 300,
        slidesToShow: 4,
        slidesToScroll: 4,
        prevArrow:
            '<a class="prev-arrow slide-arrow text-primary h4"><i class="fas fa-chevron-left"></i></a>',
          nextArrow:
            '<a class="next-arrow slide-arrow text-primary h4"><i class="fas fa-chevron-right"></i></a>',
        responsive: [
          {
            breakpoint: 1024,
            settings: {
              slidesToShow: 3,
              slidesToScroll: 3,
              infinite: true,
              dots: false,
            },
          },
          {
            breakpoint: 600,
            settings: {
              slidesToShow: 2,
              slidesToScroll: 2,
            },
          },
          {
            breakpoint: 480,
            settings: {
              slidesToShow: 1,
              slidesToScroll: 1,
            },
          },
        ],
      });
    </script>
    <!-- heart like or not -->
    <script>
      $('input#heart').on('change',function(){
        if($(this).is(':checked')){
          $("input#fallinlove").val("hate");
        }else{ 
          $("input#fallinlove").val("love");
        }
      });
    </script>
  </body>
</html>
