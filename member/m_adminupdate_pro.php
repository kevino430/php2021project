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
//執行更新動作
if(isset($_POST["action"])&&($_POST["action"]=="update")){
  //更新商品資訊	
	$query_update = "UPDATE product SET categoryid=?, productname=?, productprice=?, description=?, p_amount=? WHERE productid=?";
	$stmt = $db_link->prepare($query_update);
	$stmt->bind_param("isisii", 		
		GetSQLValueString($_POST["categoryid"], 'int'),
		GetSQLValueString($_POST["productname"], 'string'),		
		GetSQLValueString($_POST["productprice"], 'int'),
		GetSQLValueString($_POST["description"], 'string'),
		GetSQLValueString($_POST["p_amount"], 'int'),
		GetSQLValueString($_POST["productid"], 'int'));
	$stmt->execute();
	$stmt->close();
	//執行照片檔案刪除
	for ($i=0; $i<count($_POST["delcheck"]); $i++) {
		$delid = $_POST["delcheck"][$i];
		$query_del = "DELETE FROM product_image WHERE picture_id={$_POST["picture_id"][$delid]}";	
		$db_link->query($query_del);
		unlink("../proimg/".$_POST["delfile"][$delid]);
	}
  //執行照片新增及檔案上傳
	for ($i=0; $i<count($_FILES["image_url"]); $i++) {
	  if ($_FILES["image_url"]["tmp_name"][$i] != "") {
		  $query_insert = "INSERT INTO product_image (product_id, image_url) VALUES (?, ?)";
		  $stmt = $db_link->prepare($query_insert);
		  $stmt->bind_param("is", 
		   GetSQLValueString($_POST["product_id"], "int"),
		   GetSQLValueString($_FILES["image_url"]["name"][$i], "string"));
		  $stmt->execute();
		  $stmt->close();
		  if(!move_uploaded_file($_FILES["image_url"]["tmp_name"][$i] , "../proimg/" . $_FILES["image_url"]["name"][$i])) die("檔案上傳失敗！");	  
	  }
	}		
	//重新導向回到本畫面
  header('Location: '. $_SERVER['REQUEST_URI']);
}
//選取管理員資料
$query_RecAdmin = "SELECT * FROM memberdata WHERE m_username='{$_SESSION["loginMember"]}'";
$RecAdmin = $db_link->query($query_RecAdmin);	
$row_RecAdmin=$RecAdmin->fetch_assoc();
//顯示相簿資訊SQL敘述句
$sid = 0;
if(isset($_GET["id"])&&($_GET["id"]!="")){
	$sid = GetSQLValueString($_GET["id"],"int");
}
$query_RecProduct = "SELECT * FROM product LEFT JOIN category ON category.categoryid=product.categoryid WHERE productid={$sid}";
//顯示照片SQL敘述句
$query_RecPhoto = "SELECT * FROM product_image WHERE product_id={$sid} ";
//顯示類別SQL敘述句
$query_RecCategory= "SELECT * FROM category ";
//將二個SQL敘述句查詢資料到 $RecProduct、$RecPhoto 中
$RecProduct = $db_link->query($query_RecProduct);
$RecPhoto = $db_link->query($query_RecPhoto);
$RecCategory = $db_link->query($query_RecCategory);
//計算照片總筆數
$total_records = $RecPhoto->num_rows;
//取得相簿資訊
$row_RecProduct=$RecProduct->fetch_assoc();
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
      input[type="file"] {
        display: none;
      }
      .custom-file-upload {
        border: 1px solid #ccc;
        display: inline-block;
        margin: 6px 12px;
        cursor: pointer;
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
        <li class="breadcrumb-item"><a href="m_admin_product.php">商品總覽</a></li>
        <li class="breadcrumb-item active" aria-current="page">修改商品資料-<?php echo $row_RecProduct["productname"];?></li>
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
            <a class="btn btn-outline-info " href="m_admin_product.php">
            <i class="fas fa-sign-out-alt" style="transform:scaleX(-1);"></i> 回到商品總攬
            
            </a>
          </div>

          </div>
        </div>
      </div>
      <div class="row mt-2 d-flex justify-content-center">
        
        <h1 class="">修改商品資料</h1>
      </div>
     <!-- form -->
     <div class=" d-flex justify-content-center bg-light mt-2 pt-4 pb-3">
      <form class="needs-validation h5 w-75 " action="" method="POST" enctype="multipart/form-data" name="form1" id="form1">
        
        <div class="form-group  ">
          <div class="col-md-12 mb-3 " align="center">
            <?php if(isset($_GET["joinErr"]) && ($_GET["joinErr"]=="1")){?>
              <div class="bg-danger text-white pt-2 rounded " style="height:3rem; opacity: 0.75" >商品 <?php echo $_GET["username"];?> 已存在！</div>
            <?php }?>
          </div>
      
          <div class="col-md-12 mb-3 ">
            <label for="productname"><i class="text-danger">* </i>商品名稱</label>
            <input
              type="text"
              class="form-control "
              id="productname"
              name="productname"
              placeholder="請輸入商品名稱"
              value="<?php echo $row_RecProduct["productname"];?>"
            />
          </div>
          <div class="col-md-12 mb-3">
            <label for="productprice"><i class="text-danger">* </i>商品價格</label>
            <div class="input-group">
              <div class="input-group-prepend">
                <div class="input-group-text">NT$</div>
              </div>        
              <input
                  type="number"
                  class="form-control"
                  id="productprice"
                  name="productprice"
                  placeholder="請輸入商品價格"
                  value="<?php echo $row_RecProduct["productprice"];?>"
                  min="1"
              />
            </div>
          </div>

          <div class="col-md-12 mb-3">
            <label for="categoryid"><i class="text-danger">* </i>商品品牌</label>    
              <select 
              id="categoryid"
              name="categoryid"
              class="form-control"
              >
              <?php while($row_RecCategory=$RecCategory->fetch_assoc()){ ?>
                <option 
                value="<?php echo $row_RecCategory["categoryid"];?>"
                <?php if($row_RecCategory["categoryid"]==$row_RecProduct["categoryid"]) echo "selected";?>
                >
                  <?php echo $row_RecCategory["categoryname"];?>
                </option>
              <?php }?>
              </select>
            </div>
          </div>
          <div class="col-md-12 mb-3">
            <label for="p_amount"><i class="text-danger">* </i>商品數量</label>   
              <input
                  type="number"
                  class="form-control"
                  id="p_amount"
                  name="p_amount"
                  placeholder="請輸入商品價格"
                  value="<?php echo $row_RecProduct["p_amount"];?>"
                  min="0"
              />
          </div>
          
          <div class="col-md-12 mb-3">
            <label for="description"><i class="text-danger ">* </i>商品描述</label>         
              <textarea 
              id="description"
              name="description"
              class="form-control"
              style="height:150px;"
              ><?php echo $row_RecProduct["description"];?></textarea>
          </div>
          <input name="productid" type="hidden" id="productid" value="<?php echo $row_RecProduct["productid"];?>" />
          <input name="product_id" type="hidden" id="product_id" value="<?php echo $row_RecProduct["productid"];?>" />
          <hr />

          <label for="">商品照片</label>
          <div class="row">
          <?php
            $checkid=0;
            while($row_RecPhoto=$RecPhoto->fetch_assoc()){
          ?>
            <div class="card col-3 mt-2 " style="padding:0; ">
            
            <img class="card-img-top " src="../proimg/<?php echo $row_RecPhoto["image_url"];?>" alt="<?php echo $row_RecProduct["productname"];?>" border="0"  id="<?php echo "Myimg".$row_RecPhoto["picture_id"];?>" height="200px"/>
            
              <div class="card-body" >
                <input name="picture_id[]" type="hidden" id="picture_id[]" value="<?php echo $row_RecPhoto["picture_id"];?>" />
                <input name="delfile[]" type="hidden" id="delfile[]" value="<?php echo $row_RecPhoto["image_url"];?>">
                <input name="delcheck[]" type="checkbox" id="delcheck[]" value="<?php echo $checkid;$checkid++?>" />刪除?
              </div>
            </div>
            <!-- .modal -->
            <div class="modal fade " id="<?php echo "Mymodal".$row_RecPhoto["picture_id"];?>">
              <div class="modal-dialog modal-dialog-centered">
               
                <div class="modal-content">
                <button type="button" class="close text-secondary" data-dismiss="modal" style="position: absolute; right:0px; top:0px;">×</button>
                  <img class="card-img-top " src="../proimg/<?php echo $row_RecPhoto["image_url"];?>" alt="<?php echo $row_RecProduct["productname"];?>" border="0" style="object-fit: contain;"  />
                  <div class="modal-footer">
                    <div><?php echo $row_RecPhoto["image_url"];?></div>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>   
                  </div>
                </div>                                                                       
              </div>                                      
            </div>
            

         
          <?php }?>
          </div>
          <hr />
          <div>
            <div class="row pl-3">  
              <p class="heading">新增商品照</p>
              <a class="btn btn-info mx-2 text-white" id="button">
                <i class="fas fa-folder-plus"></i>更多新增
              </a>
            </div>
        
            <div class="productPic row mt-2">  
              <div class="card col-3" style="padding: 0">
                <div id="1" class="product">
                  <img
                    id="blah1"
                    src="../images/add_pic.png"
                    alt="your image"
                    style="height: 200px"
                    class="card-img-top"
                  />
                </div>
                <label for="imgInp1" class="custom-file-upload btn btn-info">
                  <i class="fa fa-cloud-upload"></i> 選擇檔案
                </label>
                <input accept="image/*" type="file" id="imgInp1" name="image_url[]" />
              </div>
            </div>
          </div>

           <hr>
        <div class="d-flex justify-content-center mt-2">
        
        <input name="action" type="hidden" id="action" value="update">
        <input class="btn btn-info btn-lg " style="width:18rem" type="submit" name="button" value="確認送出">
        
        </div>
          

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
    <?php
    $RecPhoto->data_seek(0);
      $checkid=0;
      while($row_RecPhoto=$RecPhoto->fetch_assoc()){
    ?>
    $('#<?php echo "Myimg".$row_RecPhoto["picture_id"];?>').click(function(){
  		$('#<?php echo "Mymodal".$row_RecPhoto["picture_id"];?>').modal('show')
	  });
    <?php }?>
    </script>

    <!-- upload file  -->
    <script>
      $("#button").on("click", function () {
        var count = $("div.productPic .product").length,
          sid = count + 1;

        $("div.productPic").append(
          '<div class="card col-3" style="padding: 0"><div id="' +
            sid +
            '" class="product"> <img id="blah' +
            sid +
            '" src="../images/add_pic.png" alt="your image" style="height:200px" class="card-img-top"/>' +
            " </div>" +
            '<label for="imgInp' +
            sid +
            '" class="custom-file-upload btn btn-info">' +
            '<i class="fa fa-cloud-upload"></i> 選擇檔案' +
            "</label>" +
            '<input accept="image/*" type="file" id="imgInp' +
            sid +
            '"  name="image_url[]"/></div>'
        );

        var imgInput = "imgInp";
        var blashId = "blah";
        var fulImgInput = imgInput + sid;
        var fulBlash = blashId + sid;

        document.getElementById(fulImgInput).onchange = (evt) => {
          const [file] = document.getElementById(fulImgInput).files;
          if (file) {
            document.getElementById(fulBlash).src = URL.createObjectURL(file);
          }
        };
      });
    </script>
    <script>
      imgInp1.onchange = (evt) => {
        const [file] = imgInp1.files;
        if (file) {
          blah1.src = URL.createObjectURL(file);
        }
      };
    </script>
   
    
  </body>
</html>
<?php
	$db_link->close();
?>
