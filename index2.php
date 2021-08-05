<!-- 加入購物車 -->
<?php
require_once("connMysql.php");
//購物車開始
require_once("cart/mycart.php");
session_start();
$cart =& $_SESSION['cart']; // 將購物車的值設定為 Session
if(!is_object($cart)) $cart = new myCart();
// 新增購物車內容
if(isset($_POST["cartaction"]) && ($_POST["cartaction"]=="add")){
	$cart->add_item($_POST['id'],$_POST['qty'],$_POST['price'],$_POST['name']);
	// header("Location: index2.php");
  header('Location: '. $_SERVER['REQUEST_URI']);
}

//購物車結束
?>
<!-- 會員頁面 -->
<?php
require_once("connMysql.php");
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
        header("Location: index2.php");
        
      //否則則導向管理中心
      }else{
        header("Location: member/m_admin.php"); 
      }
    }else{
      header("Location: index2.php?errMsg=1");
    }
  }else{
    header("Location: index2.php?errMsg=2");
  }
}

//執行登出動作
if(isset($_GET["logout"]) && ($_GET["logout"]=="true")){
  unset($_SESSION["loginMember"]);
  unset($_SESSION["memberLevel"]);
  header("Location: index2.php");
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
          header("Location: index2.php");
          
        //否則則導向管理中心
        }else{
          header("Location: member/m_admin.php"); 
        }
      }else{
        header("Location: index2.php?errMsg=1");
      }
    }else{
      header("Location: index2.php?errMsg=2");
    }
  }
}
?>
<!-- 商品頁面 -->
<?php
//預設每頁筆數
$pageRow_records = 8;
//預設頁數
$num_pages = 1;
//若已經有翻頁，將頁數更新
if (isset($_GET['page'])) {
  $num_pages = $_GET['page'];
}
//本頁開始記錄筆數 = (頁數-1)*每頁記錄筆數
$startRow_records = ($num_pages -1) * $pageRow_records;
if(isset($_SESSION["loginMember"]) && ($_SESSION["loginMember"]!="")){
  //若有分類關鍵字時未加限制顯示筆數的SQL敘述句
  if(isset($_GET["cid"])&&($_GET["cid"]!="")){
    $query_RecProduct = "SELECT * FROM product LEFT JOIN (SELECT* FROM phplove WHERE phplove.memberid={$row_RecMember["m_id"]}) phplove ON product.productid=phplove.product_id LEFT JOIN category ON product.categoryid = category.categoryid LEFT JOIN product_image ON product.productid=product_image.product_id WHERE p_amount>0 && product.categoryid=? GROUP BY product.productname ORDER BY product.productid DESC";
    $stmt = $db_link->prepare($query_RecProduct);
    $stmt->bind_param("i", $_GET["cid"]);
  //若有搜尋關鍵字時未加限制顯示筆數的SQL敘述句
  }elseif(isset($_GET["keyword"])&&($_GET["keyword"]!="")){
    $query_RecProduct = "SELECT * FROM product LEFT JOIN (SELECT* FROM phplove WHERE phplove.memberid={$row_RecMember["m_id"]}) phplove ON product.productid=phplove.product_id LEFT JOIN category ON product.categoryid = category.categoryid LEFT JOIN product_image ON product.productid=product_image.product_id WHERE p_amount>0 && productname LIKE ? OR description LIKE ? GROUP BY product.productname ORDER BY product.productid DESC";
    $stmt = $db_link->prepare($query_RecProduct);
    $keyword = "%".$_GET["keyword"]."%";
    $stmt->bind_param("ss", $keyword, $keyword);	
  //若有價格區間關鍵字時未加限制顯示筆數的SQL敘述句
  }elseif(isset($_GET["price1"]) && isset($_GET["price2"]) && ($_GET["price1"]<=$_GET["price2"])){
    $query_RecProduct = "SELECT * FROM product LEFT JOIN (SELECT* FROM phplove WHERE phplove.memberid={$row_RecMember["m_id"]}) phplove ON product.productid=phplove.product_id LEFT JOIN category ON product.categoryid = category.categoryid LEFT JOIN product_image ON product.productid=product_image.product_id WHERE p_amount >0 && productprice BETWEEN ? AND ? GROUP BY product.productname ORDER BY product.productid DESC";
    $stmt = $db_link->prepare($query_RecProduct);
    $stmt->bind_param("ii", $_GET["price1"], $_GET["price2"]);
  //預設狀況下未加限制顯示筆數的SQL敘述句
  }else{
    $query_RecProduct = "SELECT * FROM product LEFT JOIN (SELECT* FROM phplove WHERE phplove.memberid={$row_RecMember["m_id"]}) phplove ON product.productid=phplove.product_id LEFT JOIN category ON product.categoryid = category.categoryid LEFT JOIN product_image ON product.productid=product_image.product_id WHERE p_amount >0 GROUP BY product.productname ORDER BY product.productid DESC";
    $stmt = $db_link->prepare($query_RecProduct);
  }
  
}else{
  //若有分類關鍵字時未加限制顯示筆數的SQL敘述句
  if(isset($_GET["cid"])&&($_GET["cid"]!="")){
    $query_RecProduct = "SELECT * FROM product LEFT JOIN category ON product.categoryid = category.categoryid LEFT JOIN product_image ON product.productid=product_image.product_id WHERE p_amount >0 && product.categoryid=? GROUP BY product.productname ORDER BY product.productid DESC";
    $stmt = $db_link->prepare($query_RecProduct);
    $stmt->bind_param("i", $_GET["cid"]);
  //若有搜尋關鍵字時未加限制顯示筆數的SQL敘述句
  }elseif(isset($_GET["keyword"])&&($_GET["keyword"]!="")){
    $query_RecProduct = "SELECT * FROM product LEFT JOIN category ON product.categoryid = category.categoryid LEFT JOIN product_image ON product.productid=product_image.product_id WHERE p_amount>0 && productname LIKE ? OR description LIKE ? GROUP BY product.productname ORDER BY product.productid DESC";
    $stmt = $db_link->prepare($query_RecProduct);
    $keyword = "%".$_GET["keyword"]."%";
    $stmt->bind_param("ss", $keyword, $keyword);	
  //若有價格區間關鍵字時未加限制顯示筆數的SQL敘述句
  }elseif(isset($_GET["price1"]) && isset($_GET["price2"]) && ($_GET["price1"]<=$_GET["price2"])){
    $query_RecProduct = "SELECT * FROM product LEFT JOIN category ON product.categoryid = category.categoryid LEFT JOIN product_image ON product.productid=product_image.product_id WHERE p_amount>0 && productprice BETWEEN ? AND ? GROUP BY product.productname ORDER BY product.productid DESC";
    $stmt = $db_link->prepare($query_RecProduct);
    $stmt->bind_param("ii", $_GET["price1"], $_GET["price2"]);
  //預設狀況下未加限制顯示筆數的SQL敘述句
  }else{
    $query_RecProduct = "SELECT * FROM product LEFT JOIN category ON product.categoryid = category.categoryid LEFT JOIN product_image ON product.productid=product_image.product_id WHERE p_amount>0 GROUP BY product.productname ORDER BY product.productid DESC";
    $stmt = $db_link->prepare($query_RecProduct);
  }}

$stmt->execute();
//以未加上限制顯示筆數的SQL敘述句查詢資料到 $all_RecProduct 中
$all_RecProduct = $stmt->get_result();
//計算總筆數
$total_records = $all_RecProduct->num_rows;
//計算總頁數=(總筆數/每頁筆數)後無條件進位。
$total_pages = ceil($total_records/$pageRow_records);
// echo $total_page;
//繫結產品目錄資料
$query_RecCategory = "SELECT *, count(product.productid) as productNum FROM category LEFT JOIN product ON category.categoryid = product.categoryid WHERE productname!='' && p_amount>0 GROUP BY category.categoryid, category.categoryname, category.categorysort ORDER BY category.categoryid ASC";
$RecCategory = $db_link->query($query_RecCategory);
//計算資料總筆數
$query_RecTotal = "SELECT count(productid) as totalNum FROM product";
$RecTotal = $db_link->query($query_RecTotal);
$row_RecTotal = $RecTotal->fetch_assoc();
//返回 URL 參數

function keepURL(){
	$keepURL = "";
	if(isset($_GET["keyword"])) $keepURL.="&keyword=".urlencode($_GET["keyword"]);
	if(isset($_GET["price1"])) $keepURL.="&price1=".$_GET["price1"];
	if(isset($_GET["price2"])) $keepURL.="&price2=".$_GET["price2"];	
	if(isset($_GET["cid"])) $keepURL.="&cid=".$_GET["cid"];
	return $keepURL;
}
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
    <link href="mystyle.css" rel="stylesheet" type="text/css">
    <style>
      .bg-image{
        /* The image used */
        background-image: url("images/7.jpg");

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
      .checkbox {
        font-family: Arial, sans-serif;
        font-size: 30px;
        display: block;
        margin: 5px;
        cursor: pointer;
      }

      .checkbox input {
        display: none;
        position: relative;
        z-index: -9999;
      }

      .checkbox input:checked + i.fa-heart {
        color: crimson;
        position: relative; 
        top:-44px;
        right:0.8px;
        
      }
      .checkbox input + i.fa-heart {
        color: rgba(99, 99, 99, 0.5);
        position: relative;
        top:-44px;
        right:0.8px;
        
      }
      .boxitem {
        width: 38px;
        height: 38px;
        background: rgb(141, 141, 141, 0.5);
        border-radius: 50%;
        position: relative;
        
      }
      .heartbox{
        position: absolute;
        top:160px;
        right:1.5px;
      }
    </style>
  </head>
  <body>
    <!-- navbar -->

    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
      <a class="navbar-brand" href="index2.php">Kevino430 Shop</a>
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

          <a type="button" class="btn btn-outline-info ml-3" href="member/signup.php">
            註冊
          </a>
          
          <?php }else{ ?>

          
          <a class="text-light " href="member/m_center.php">
          <!-- 頭像 -->
          <?php if($row_RecMember["m_sex"]=="男"){ ?>
              <img style="height:30px" src="images/boy.png" alt="">
          <?php }else{?>
              <img style="height:30px" src="images/girl.png" alt="">
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
          <div class=" ml-4" style="display: inline-block;">
              <a href="cart/cart.php">
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
          <div class="modal-header bg-image " style="padding:0px; height:20rem">
          
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
                  <a class="text-secondary float-right" href="member/m_passmail.php">
                    <i class="far fa-question-circle"></i>忘記密碼
                  </a>
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
                  href="member/signup.php"
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
        <?php 
          if(isset($_GET["cid"])&&($_GET["cid"]!="")){?>
          <li class="breadcrumb-item"><a href="index2.php">首頁</a></li>
        <?php }else{ ?>
          <li class="breadcrumb-item active" aria-current="page">首頁</li>
        <?php } ?>
        
        <li class="breadcrumb-item active" aria-current="page">
          <?php 
          if(isset($_GET["cid"])&&($_GET["cid"]!="")){
            $query_RecCa = "SELECT categoryname FROM category WHERE categoryid='{$_GET["cid"]}'";
            $RecCa = $db_link->query($query_RecCa);
            $row_RecCa=$RecCa->fetch_assoc();
            echo $row_RecCa["categoryname"];
          } 
          ?>
        </li>
      </ol>
    </nav>
    <!-- main  -->
    <div class="container mb-4">
      <!-- searching place  -->
      <div class="">
      <form name="form1" method="get" action="index2.php">
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
              <a class=" btn btn-outline-secondary mt-2 ml-2 text-nowrap" href="index2.php" >
                所有產品
                <span class="badge badge-light border"><?php echo $row_RecTotal["totalNum"];?></span>
              </a>
            </li>
            <?php	
            while($row_RecCategory=$RecCategory->fetch_assoc()){ 
            ?>
            <li class="nav-item">
              
                <a class="btn btn-outline-secondary mt-2 ml-2 text-nowrap" href="index2.php?cid=<?php echo $row_RecCategory["categoryid"];?>" >
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

     
      <!-- product place  -->
      <div>
      <div class="row mt-1" id="productPlace">
        <!-- product card -->
        <?php
        //加上限制顯示筆數的SQL敘述句，由本頁開始記錄筆數開始，每頁顯示預設筆數
        $query_limit_RecProduct = $query_RecProduct." LIMIT {$startRow_records}, {$pageRow_records}";
        //以加上限制顯示筆數的SQL敘述句查詢資料到 $RecProduct 中
        $stmt = $db_link->prepare($query_limit_RecProduct);
        //若有分類關鍵字時未加限制顯示筆數的SQL敘述句
        if(isset($_GET["cid"])&&($_GET["cid"]!="")){
          $stmt->bind_param("i", $_GET["cid"]);
        //若有搜尋關鍵字時未加限制顯示筆數的SQL敘述句
        }elseif(isset($_GET["keyword"])&&($_GET["keyword"]!="")){
          $keyword = "%".$_GET["keyword"]."%";
          $stmt->bind_param("ss", $keyword, $keyword);	
        //若有價格區間關鍵字時未加限制顯示筆數的SQL敘述句
        }elseif(isset($_GET["price1"]) && isset($_GET["price2"]) && ($_GET["price1"]<=$_GET["price2"])){
          $stmt->bind_param("ii", $_GET["price1"], $_GET["price2"]);
        }
        $stmt->execute();            
        $RecProduct = $stmt->get_result();
        while($row_RecProduct=$RecProduct->fetch_assoc()){

      ?>
      <!-- card start -->
          <div class="cardSize col-sm-3 mt-2">
            
            <div class="card" >
              <a href="cart/product2.php?id=<?php echo $row_RecProduct["productid"];?>">
              <?php if($row_RecProduct["image_url"]==""){?>
                <img class="card-img-top border-bottom" src="images/nopic.png" alt="暫無圖片"  border="0" height="180px" style="object-fit: contain;"/>
                <?php }else{?>
                <img class="card-img-top border-bottom" src="proimg/<?php echo $row_RecProduct["image_url"];?>" alt="<?php echo $row_RecProduct["productname"];?>" height="180px" style="object-fit: contain;"/>
                <?php }?>
                
              </a>
              <!-- heart start -->
              <?php
              if(isset($_SESSION["loginMember"]) && ($_SESSION["loginMember"]!="")){?>
              <form method="post" action="" >
                <div class="heartbox">
                    <div class="boxitem"></div>
                    <label class="checkbox">
                      <input type="checkbox" id="heart<?php echo $row_RecProduct["productid"];?>" onchange="this.form.submit()" 
                      <?php if ($row_RecProduct["love_or_hate"]==1){echo "checked";} ?>/>

                      <i class="fa fa-heart" ></i>
                      
                      
                    </label>
                    <input name="action" class="fallinlove" type="hidden" id="fallinlove<?php echo $row_RecProduct["productid"];?>" 
                    value="<?php if ($row_RecProduct["love_or_hate"]!=1){echo "love";}else{echo "hate";} ?>"/>

                    <input name="memberid" type="hidden" value="<?php echo $row_RecMember["m_id"];?>">
                    <input name="productid" type="hidden" value="<?php echo $row_RecProduct["productid"];?>">
                </div>
              </form>
                     
              <?php }else{ ?>
                    <a class="heartbox" onclick="myFunction()" data-toggle="modal" data-target="#exampleModalCenterLogin">
                      <div class="boxitem"></div>
                      <label class="checkbox" >
                        <input type="checkbox" disabled/>
                        <i class="fa fa-heart" ></i>
                        <script>
                        function myFunction() {
                          alert("登入後才可加入最愛!");
                        }
                        </script>
                    </a>
                    
              <?php }?>

              
              
              <!-- heart end -->
              <div class="card-body">
                <div style="height:48px">
                <p class="card-title" style="font-size: 19px; font-weight:600;">
                  <a class="text-dark" style="text-decoration:none;" href="cart/product2.php?id=<?php echo $row_RecProduct["productid"];?>">
                    <?php echo $row_RecProduct["productname"];?>
                  </a>
                </p>
                
                </div>

                <img class=" mt-1" src="images/<?php echo $row_RecProduct["categoryimg"];?>" alt="<?php echo $row_RecProduct["categoryimg"];?>" height="32"/>
                <div class="card-text mt-2 text-secondary row" style="font-size:19px">
                  <div class="col">NT$</div>
                  <div class="col h4"><?php echo number_format($row_RecProduct["productprice"]);?></div>
                </div>
                
                <div align="center">
                <!-- add to shopping cart -->
                <form name="form3" method="post" action="">
                <input name="id" type="hidden" id="id" value="<?php echo $row_RecProduct["productid"];?>">
                <input name="name" type="hidden" id="name" value="<?php echo $row_RecProduct["productname"];?>">
                <input name="price" type="hidden" id="price" value="<?php echo $row_RecProduct["productprice"];?>">
                <input name="qty" type="hidden" id="qty" value="1">
                <input name="cartaction" type="hidden" id="cartaction" value="add">
                <button type="submit" class="btn btn-outline-info mt-2 w-100" ><i class="bi bi-cart-plus-fill h4 mr-2"></i>加入購物車</button>
                </form>
                </div>
              </div>
            </div>
            
          </div>
         
          
          <!-- card end -->
          <?php }?>
        </div>
         <!-- 查無資料 -->
          <div id="search_none">
          <div class="" align="center"><img style="width:45%" src="images/nothing-found.png" alt=""></div>
          <div class=" h4" align="center">非常抱歉，目前並無此相關商品。<a href="index2.php">回到商城</a></div>
          </div>

          <script>
          var parent = document.getElementById("productPlace");
          var nodesSameClass = parent.getElementsByClassName("cardSize");
          var p_length = nodesSameClass.length;
          // console.log(p_length);

          var x = document.getElementById("search_none");
          if (p_length!=0) {
          x.style.display = "none";
          } else {
            
            x.style.display = "block";
          }
          </script>

        
      </div>
      </div>
      
    <!-- pagenation -->
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
    <!-- password eye -->
    <script>
      const togglePassword = document.querySelector("#togglePassword");
      const password = document.querySelector("#password");
      togglePassword.addEventListener("click", function (e) {
        // toggle the type attribute
        const type =
          password.getAttribute("type") === "password" ? "text" : "password";
        password.setAttribute("type", type);
        // toggle the eye slash icon
        this.classList.toggle("fa-eye-slash");
      });
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
    <!-- product card sizing -->
    <script>
      $(window).width(function() {
        if ($(window).width() >= 1200) {

          $('.cardSize').addClass("col-sm-3");
          $('.cardSize').removeClass("col-sm-4");
          $('.cardSize').removeClass("col-sm-6");

        } else if ($(window).width() < 1200 && $(window).width()>= 992) {
          
          $('.cardSize').addClass("col-sm-4");      
          $('.cardSize').removeClass("col-sm-3");
          $('.cardSize').removeClass("col-sm-6");
          
        } else if ($(window).width() < 992) {      
          
          $('.cardSize').addClass("col-sm-6");
          $('.cardSize').removeClass("col-sm-3");
          $('.cardSize').removeClass("col-sm-4");
        } else{
          // $('.cardSize').addClass("mx-5");
        }
      });
    </script>
    <!-- product card resizing -->
    <script>
      $(window).resize(function() {
        if ($(window).width() >= 1200) {

          $('.cardSize').addClass("col-sm-3");
          $('.cardSize').removeClass("col-sm-4");
          $('.cardSize').removeClass("col-sm-6");

        } else if ($(window).width() < 1200 && $(window).width()>= 992) {
          
          $('.cardSize').addClass("col-sm-4");      
          $('.cardSize').removeClass("col-sm-3");
          $('.cardSize').removeClass("col-sm-6");
          
        } else if ($(window).width() < 992) {      
          
          $('.cardSize').addClass("col-sm-6");
          $('.cardSize').removeClass("col-sm-3");
          $('.cardSize').removeClass("col-sm-4");
        } else{
          // $('.cardSize').addClass("mx-5");
        }
      });
    </script>
       <!-- heart like or not, start -->
       <?php
        //加上限制顯示筆數的SQL敘述句，由本頁開始記錄筆數開始，每頁顯示預設筆數
        $query_limit_RecProduct = $query_RecProduct." LIMIT {$startRow_records}, {$pageRow_records}";
        //以加上限制顯示筆數的SQL敘述句查詢資料到 $RecProduct 中
        $stmt = $db_link->prepare($query_limit_RecProduct);
        //若有分類關鍵字時未加限制顯示筆數的SQL敘述句
        if(isset($_GET["cid"])&&($_GET["cid"]!="")){
          $stmt->bind_param("i", $_GET["cid"]);
        //若有搜尋關鍵字時未加限制顯示筆數的SQL敘述句
        }elseif(isset($_GET["keyword"])&&($_GET["keyword"]!="")){
          $keyword = "%".$_GET["keyword"]."%";
          $stmt->bind_param("ss", $keyword, $keyword);	
        //若有價格區間關鍵字時未加限制顯示筆數的SQL敘述句
        }elseif(isset($_GET["price1"]) && isset($_GET["price2"]) && ($_GET["price1"]<=$_GET["price2"])){
          $stmt->bind_param("ii", $_GET["price1"], $_GET["price2"]);
        }
        $stmt->execute();            
        $RecProduct = $stmt->get_result();
        while($row_RecProduct=$RecProduct->fetch_assoc()){ 
    ?>
    <script>
      $('input#heart<?php echo $row_RecProduct["productid"];?>').on('change',function(){
        if($(this).is(':checked')){
          $("input#fallinlove<?php echo $row_RecProduct["productid"];?>").val("hate");
        }else{ $("input#fallinlove<?php echo $row_RecProduct["productid"];?>").val("love");}
      });
    </script>
    <?php } ?>
    <!-- heart like or not, end -->
     <!-- heart toggle -->
     <script>
      $(document).on('click', '.toggleHeart', function() {
      $(this).toggleClass("fas far");
      });
    </script>
     <script>
      var changeSearch = function (item) {
          document.getElementById(
              "searchDropDown")= item.value;
      };
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
   
  </body>
</html>

<?php
$stmt->close();
$db_link->close();
?>