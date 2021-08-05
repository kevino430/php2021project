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
//執行新增動作
if(isset($_POST["action"])&&($_POST["action"]=="add")){
  //找尋廠牌是否已存在
	$query_RecFindCategory = "SELECT categoryname FROM category WHERE categoryname='{$_POST["categoryname"]}'";
  $RecFindCategory=$db_link->query($query_RecFindCategory );
  if ($RecFindCategory->num_rows>0){
		header("Location: ?joinErr=1&categoryname={$_POST["categoryname"]}");
  }else{	
    //執行照片新增及檔案上傳
    for ($i=0; $i<count($_FILES["categoryimg"]); $i++) {
      if ($_FILES["categoryimg"]["tmp_name"][$i] != "") {
        $query_insert = "INSERT INTO category (categoryname,categorysort, categoryimg,category_valid) VALUES (?,?,?,?)";
        $stmt = $db_link->prepare($query_insert);
        $stmt->bind_param("sisi", 
        GetSQLValueString($_POST["categoryname"], 'string'),
        GetSQLValueString($_POST["categorysort"], 'int'),	
        GetSQLValueString($_FILES["categoryimg"]["name"][$i], "string"),
        GetSQLValueString(1, 'int'));
        $stmt->execute();
        if(!move_uploaded_file($_FILES["categoryimg"]["tmp_name"][$i] , "../images/" . $_FILES["categoryimg"]["name"][$i])) die("檔案上傳失敗！");
        $stmt->close();  
      }
    }		
    //重新導向回到本畫面
    header('Location: addcategory.php');
  }
}
//選取管理員資料
$query_RecAdmin = "SELECT * FROM memberdata WHERE m_username='{$_SESSION["loginMember"]}'";
$RecAdmin = $db_link->query($query_RecAdmin);	
$row_RecAdmin=$RecAdmin->fetch_assoc();

//顯示類別SQL敘述句
$query_RecCategory= "SELECT *,(category.categoryid)as cid FROM category LEFT JOIN product ON category.categoryid=product.categoryid WHERE category.category_valid=1 GROUP BY category.categoryid";
$RecCategory = $db_link->query($query_RecCategory);


//執行更新動作
if(isset($_POST["action2"])&&($_POST["action2"]=="update")){

  //執行廠牌更新及檔案上傳
	  if ($_FILES["categoryimg"]["tmp_name"] != "") {
      // 刪除舊照片
      $query_OldPic="SELECT categoryimg From category WHERE categoryid={$_POST["categoryid"]}";
      $RecOldPic= $db_link->query($query_OldPic);
      $row_RecOldPic=$RecOldPic->fetch_assoc();
      unlink("../images/".$row_RecOldPic["categoryimg"]);
      // 新增新照片
		  $query_Update = "UPDATE category SET categoryname=?, categorysort=?, categoryimg=?,category_valid=? WHERE categoryid=?";
		  $stmt = $db_link->prepare($query_Update);
      $stmt->bind_param("sisii", 
      GetSQLValueString($_POST["categoryname"], 'string'),
      GetSQLValueString($_POST["categorysort"], 'int'),	
      GetSQLValueString($_FILES["categoryimg"]["name"], "string"),
      GetSQLValueString(1, 'int'),
      GetSQLValueString($_POST["categoryid"],'int'));
		  $stmt->execute();
		  $stmt->close();
		  if(!move_uploaded_file($_FILES["categoryimg"]["tmp_name"] , "../images/" . $_FILES["categoryimg"]["name"])) die("檔案上傳失敗！");	  
	  }else{
      $query_Update = "UPDATE category SET categoryname=?, categorysort=?,category_valid=? WHERE categoryid=?";
		  $stmt = $db_link->prepare($query_Update);
      $stmt->bind_param("siii", 
      GetSQLValueString($_POST["categoryname"], 'string'),
      GetSQLValueString($_POST["categorysort"], 'int'),	
      GetSQLValueString(1, 'int'),
      GetSQLValueString($_POST["categoryid"],'int'));
		  $stmt->execute();
		  $stmt->close();

    }	
	//重新導向回到本畫面
  header('Location: '. $_SERVER['REQUEST_URI']);
}

//刪除廠牌         
if(isset($_GET["action"])&&($_GET["action"]=="delete")){
 $query_delCategory = "DELETE FROM category WHERE categoryid=?";
 $stmt=$db_link->prepare($query_delCategory );
 $stmt->bind_param("i", $_GET["id"]);
 $stmt->execute();
 $stmt->close();
 //重新導向回到主畫面
 // header("Location: m_admin_product.php");
 // header('Location: '. $_SERVER['REQUEST_URI']);
 header('Location: ' . $_SERVER['HTTP_REFERER']);
}


?>

<script language="javascript">
    function deletesure(){
        if (confirm('\n您確定要刪除此廠牌嗎? 刪除後將無法恢復')) return true;
        return false;
    }
    </script>
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
        <li class="breadcrumb-item active" aria-current="page">廠牌管理</li>
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
        
        <h1 class="">新增廠牌</h1>
      </div>
     <!-- form -->
     <div class=" d-flex justify-content-center bg-light mt-2 pt-4 pb-3">
      <form class="needs-validation h5 w-75 " action="" method="POST" enctype="multipart/form-data" name="form1" id="form1">
        
        <div class="form-group  ">
          <div class="col-md-12 mb-3 " align="center">
            <?php if(isset($_GET["joinErr"]) && ($_GET["joinErr"]=="1")){?>
              <div class="bg-danger text-white pt-2 rounded " style="height:3rem; opacity: 0.75" >廠牌 <?php echo $_GET["categoryname"];?> 已存在！</div>
            <?php }?>
          </div>
      
          <div class="col-md-12 mb-3 ">
            <label for="categoryname"><i class="text-danger">* </i>廠牌名稱</label>
            <input
              type="text"
              class="form-control "
              id="categoryname"
              name="categoryname"
              placeholder="請輸入品牌名稱"
              value=""
              required
            />
          </div>
          
          <hr />
          <div>        
            <div class="productPic row mt-2 d-flex justify-content-center">  
              <div class="card col-5" style="padding: 0">
                <div id="1" class="product">
                  <img
                    id="blah"
                    src="../images/add_pic.png"
                    alt="your image"
                    style="max-height: 300px"
                    class="card-img-top"
                  />
                </div>
                <label for="imgInp" class="custom-file-upload btn btn-info">
                  <i class="fa fa-cloud-upload"></i> 選擇檔案
                </label>
                <input accept="image/*" type="file" id="imgInp" name="categoryimg[]" />
              </div>
            </div>
          </div>
          

        </div>
        
        <div class="d-flex justify-content-center">
        
        <input name="action" type="hidden" id="action" value="add">
        <input class="btn btn-info btn-lg" style="width:18rem" type="submit" name="button" value="確認送出">
        
        </div>
      </form>
      
      </div>
      <table class="table">
        <thead class="thead-dark">
          <tr>
            <th width="15%"scope="col">廠牌編號</th>
            <th width="17%" scope="col">廠牌名稱</th>
            <th width="20%" class="text-center" scope="col">廠牌照片</th>
            <th width="10%" ></th>
            <th width="17%" class="text-center" scope="col">功能</th>
          </tr>
        </thead>
        <tbody>
        <?php while($row_RecCategory=$RecCategory->fetch_assoc()){ ?> 
          <tr>
            <th scope="row"><?php echo $row_RecCategory["cid"];?></th>
            <td class="h5"><?php echo $row_RecCategory["categoryname"];?></td>
            <td class="text-center">
            <img class=" " src="../images/<?php echo $row_RecCategory["categoryimg"];?>" alt="<?php echo $row_RecCategory["categoryimg"];?>"  style="object-fit: contain; max-width:120px; max-height:80px"/>
            </td>

            <!-- Update Modal start-->
            <div class="modal fade" id="exampleModalCenter<?php echo $row_RecCategory["cid"];?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
              <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                  <div class="modal-header">
                  <!-- php test -->
                  
                    <h5 class="modal-title" id="exampleModalLongTitle">廠牌更新</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                      <span aria-hidden="true">&times;</span>
                    </button>
                  </div>
                  <div class="modal-body">
                  <!-- form start -->
                  <form class="needs-validation h5 " action="" method="POST" enctype="multipart/form-data" name="form2" id="form2">
                    <div class="form-group  ">
                      <div class="col-md-12 mb-3 ">
                        <label for="categoryname"><i class="text-danger">* </i>廠品名稱</label>
                        <input
                          type="text"
                          class="form-control "
                          id="categoryname"
                          name="categoryname"
                          placeholder="請輸入品牌名稱"
                          value="<?php echo $row_RecCategory["categoryname"];?>"
                          required
                        />
                      </div>
                      
                      <hr />
                      <div>        
                        <div class="productPic row mt-2 d-flex justify-content-center">  
                          <div class="card col-7" style="padding: 0">
                            <div id="1" class="product" >
                              <img
                                id="blah<?php echo $row_RecCategory["cid"];?>"
                                src="../images/<?php echo $row_RecCategory["categoryimg"];?>"
                                alt="your image"
                                style="max-height: 300px"
                                class="card-img-top"
                              />
                            </div>
                            <label for="imgInp<?php echo $row_RecCategory["cid"];?>" class="custom-file-upload btn btn-info">
                              <i class="fa fa-cloud-upload"></i> 選擇檔案
                            </label>
                            <input accept="image/*" type="file" id="imgInp<?php echo $row_RecCategory["cid"];?>" name="categoryimg" />
                          </div>
                        </div>
                      </div>
                      

                    </div>
                    
                    <div class="d-flex justify-content-center">
                    <input 
                    name="categoryid" 
                    type="hidden" 
                    id="category<?php echo $row_RecCategory["cid"];?>" 
                    value="<?php echo $row_RecCategory["cid"];?>"
                    >
                    <input name="action2" type="hidden" id="action2" value="update">
                    <input class="btn btn-info btn-lg" style="width:18rem" type="submit" name="button" value="確認送出">
                    
                    </div>
                  </form>
                  <!-- form end -->
                  </div>
                 
                </div>
              </div>
            </div>
            <!-- Update Modal end  -->
            <td></td>
            <td class="text-center"> 
                <a type="button" class="btn btn-outline-warning ft2" href="" data-toggle="modal" data-target="#exampleModalCenter<?php echo $row_RecCategory["cid"];?>">
                <i class="bi bi-pencil-square"></i> 修改
                </a>
                <?php if($row_RecCategory["productname"]!=""){ ?>
                <a type="button" class="btn btn-outline-danger ft2 disabled" href="" onClick="return deletesure();">
                <i class="bi bi-trash-fill"></i> 刪除
                </a>
                <?php } else {?>
                <a type="button" class="btn btn-outline-danger ft2 " href="?action=delete&id=<?php echo $row_RecCategory["cid"];?>" onClick="return deletesure();">
                  <i class="bi bi-trash-fill"></i> 刪除
                </a>
                <?php } ?>
                
            </td>
          </tr>
      
        <?php }?>  
        </tbody>
      </table>
    
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
      imgInp.onchange = (evt) => {
        const [file] = imgInp.files;
        if (file) {
          blah.src = URL.createObjectURL(file);
        }
      };
    </script>

     <script>
     <?php
     $RecCategory->data_seek(0); 
     while($row_RecCategory=$RecCategory->fetch_assoc()){?>
      imgInp<?php echo $row_RecCategory["cid"];?>.onchange = (evt) => {
        const [file] = imgInp<?php echo $row_RecCategory["cid"];?>.files;
        if (file) {
          blah<?php echo $row_RecCategory["cid"];?>.src = URL.createObjectURL(file);
        }
      };
    <?php }?>
    </script>
   
    
  </body>
</html>
<?php
	$db_link->close();
?>