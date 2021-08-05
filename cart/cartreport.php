<?php 
require_once("../connMysql.php");
use PHPMailer\PHPMailer\PHPMailer;
if(isset($_POST["customername"]) && ($_POST["customername"]!="")){
	//購物車開始
	require_once("mycart.php");
	session_start();
	$cart =& $_SESSION['cart']; // 將購物車的值設定為 Session
	if(!is_object($cart)) $cart = new myCart();
	//購物車結束

	// 新增訂單資料
	// 製作訂單編號
	// 確認時間
	$thisYear= date('Y');
	$thisMounth= date('m');
	// 計算當月訂單數
	$query_RecOrder = "SELECT count(orderid) as orderNum FROM orders WHERE YEAR(ordertime) = $thisYear AND MONTH(ordertime) = $thisMounth"; 
	$RecOrder = $db_link->query($query_RecOrder); 
	$row_RecOrder=$RecOrder->fetch_assoc();
	$newNu = ($row_RecOrder["orderNum"]+1);
	// 訂單編號後四碼補0
	$inNumber = sprintf("%04d",$newNu);
	// 訂單編號
	$invoice = "IN".$thisYear.$thisMounth.$inNumber;

	// 分辨是否有登入
	if(isset($_SESSION["loginMember"]) && ($_SESSION["loginMember"]!="")){
	// 如果有登入會員
	$query_RecMember = "SELECT * FROM memberdata WHERE m_username = '{$_SESSION["loginMember"]}'";
    $RecMember = $db_link->query($query_RecMember); 
    $row_RecMember=$RecMember->fetch_assoc();
	$memberid=$row_RecMember["m_id"];

	$sql_query = "INSERT INTO orders (member_id ,total ,deliverfee ,grandtotal ,customername ,customeremail ,customeraddress ,customerphone ,paytype, ordertime, invoice) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(),?)";
	$stmt = $db_link->prepare($sql_query);
	$stmt->bind_param("iiiissssss",$memberid, $cart->total, $cart->deliverfee, $cart->grandtotal, $_POST["customername"], $_POST["customeremail"], $_POST["customeraddress"], $_POST["customerphone"], $_POST["paytype"],$invoice);
	$stmt->execute();
	//取得新增的訂單編號
	$o_pid = $stmt->insert_id;
	$stmt->close();

	}else{

	$sql_query = "INSERT INTO orders (total ,deliverfee ,grandtotal ,customername ,customeremail ,customeraddress ,customerphone ,paytype, ordertime, invoice) VALUES (?, ?, ?, ?, ?, ?, ?, ?,NOW(),?)";
	$stmt = $db_link->prepare($sql_query);
	$stmt->bind_param("iiissssss", $cart->total, $cart->deliverfee, $cart->grandtotal, $_POST["customername"], $_POST["customeremail"], $_POST["customeraddress"], $_POST["customerphone"], $_POST["paytype"],$invoice);
	$stmt->execute();
	//取得新增的訂單編號
	$o_pid = $stmt->insert_id;
	$stmt->close();

	}

	//新增訂單內貨品資料
	if($cart->itemcount > 0) {
		foreach($cart->get_contents() as $item) {
			$sql_query="INSERT INTO orderdetail (orderid ,productid ,productname ,unitprice ,quantity) VALUES (?, ?, ?, ?, ?)";
			$stmt = $db_link->prepare($sql_query);
			$stmt->bind_param("iisii", $o_pid, $item['id'], $item['info'], $item['price'], $item['qty']);
			$stmt->execute();
			$stmt->close();
		}
	}

	//更改庫存量
	$query_RecProductOld = "SELECT *,(product.productid)AS pro_id FROM product JOIN orderdetail ON product.productid=orderdetail.productid WHERE orderdetail.orderid= $o_pid"; 
	$RecProductOld = $db_link->query($query_RecProductOld); 
	
	foreach($RecProductOld as $row){ 
		$OrderId=$row["pro_id"];
		$OldAmount=$row["p_amount"];
		$OrderAmount=$row["quantity"];
		$new_amount=$OldAmount-$OrderAmount;
		// echo $OrderId." : ".$OldAmount." - ".$OrderAmount."=".$new_amount."<br>";
		$sql_update="UPDATE product SET p_amount=? WHERE productid=?";
		$stmt = $db_link->prepare($sql_update);
		
		$stmt->bind_param("ii",$new_amount,$OrderId);
		$stmt->execute();
		$stmt->close();
	};


	//郵寄通知
	$mailSubject="=?UTF-8?B?" . base64_encode("Kevino430 Shop 訂單通知"). "?=";
	// 信件內容
	$cname = $_POST["customername"];
	$cmail = $_POST["customeremail"];
	$ctel = $_POST["customerphone"];
	$caddress = $_POST["customeraddress"];
	$cpaytype = $_POST["paytype"];
	$date= date("Y-m-d H:i");
	// 購物內容
	$items = "";
	foreach($cart->get_contents() as $item) {

	$items .= "<table><tr><th style='width:100px; text-align: left;'>".$item['info']."</th><td style='width:100px; text-align: right;'>NT$ ".number_format($item['price'])."</td><td style='width:50px; text-align: left;padding-left:4px'> x ".$item['qty']."</td></tr></table>" ;
		// echo $items;
	};
	// echo "<br>".$items;
	$del = "NT$".number_format($cart->deliverfee);
	$total = "NT$".number_format($cart->grandtotal);
	$mailcontent=<<<msg
	<h1 style="color:#0066CC; font-weight:bold;"><a href="http://localhost/2021phpreview/project202102/index2.php">Kevin shop</a></h1>
	<h2 style="color:#0066CC">親愛的 $cname 您好：</h2>
	<div style="color:#272727">
	感謝您的光臨
	<br>
	本次消費詳細資料如下：
	</div>
	<hr style="border: 1px dashed black;" />
	<div style="color:#272727">訂單編號：$invoice </div>
	<div style="color:#272727">購買時間：$date </div>
	<div style="color:#272727">客戶姓名：$cname </div>
	<div style="color:#272727">電子郵件：$cmail </div>
	<div style="color:#272727">電話：$ctel </div>
	<div style="color:#272727">住址：$caddress </div>
	<div style="color:#272727">付款方式：$cpaytype </div>
	<div style="color:#272727">消費項目：$items </div>
	<div style="color:#272727">運費項目：$del </div>
	<div style="color:#272727">消費金額：$total </div>
	<hr style="border: 1px dashed black;" />
	<div style="color:#272727">希望能再次為您服務 </div>
	<div style="color:#272727">Kevino430 Shop 敬上  </div>
	<div style="color:#272727">Email: zongyongduan23@gmail.com</div>
msg;

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
$mail->setFrom($cmail, $cname);
$mail->addAddress($cmail); 
$mail->Subject = ("youremailaddress@gmail.com ($mailSubject)"); //your email address
$mail->Body = $mailcontent;

if ($mail->send()) {
  $status = "success";
  $response = "Email is sent!";
} else {
  $status = "failed";
  $response = "Something is wrong: <br><br>" . $mail->ErrorInfo;
}



	//清空購物車
	$cart->empty_cart();
}	
?>
<script language="javascript">
alert("訂單:<?php echo $invoice;?>已送出;\n感謝您的購買，我們將儘快進行處理。");
window.location.href="../index2.php";
</script>