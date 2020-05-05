<?php require_once('Connections/conn_ura.php'); ?>
<?php
if (!isset($_SESSION)) {
  session_start();
}
$MM_authorizedUsers = "Officer,Manager,Ass Comm,In Charge,Regional Supervisor";
$MM_donotCheckaccess = "false";

// *** Restrict Access To Page: Grant or deny access to this page
function isAuthorized($strUsers, $strGroups, $UserName, $UserGroup) { 
  // For security, start by assuming the visitor is NOT authorized. 
  $isValid = False; 

  // When a visitor has logged into this site, the Session variable MM_Username set equal to their username. 
  // Therefore, we know that a user is NOT logged in if that Session variable is blank. 
  if (!empty($UserName)) { 
    // Besides being logged in, you may restrict access to only certain users based on an ID established when they login. 
    // Parse the strings into arrays. 
    $arrUsers = Explode(",", $strUsers); 
    $arrGroups = Explode(",", $strGroups); 
    if (in_array($UserName, $arrUsers)) { 
      $isValid = true; 
    } 
    // Or, you may restrict access to only certain users based on their username. 
    if (in_array($UserGroup, $arrGroups)) { 
      $isValid = true; 
    } 
    if (($strUsers == "") && false) { 
      $isValid = true; 
    } 
  } 
  return $isValid; 
}

$MM_restrictGoTo = "index.php?msg=Please login first to view this page";
if (!((isset($_SESSION['MM_Username'])) && (isAuthorized("",$MM_authorizedUsers, $_SESSION['MM_Username'], $_SESSION['MM_UserGroup'])))) {   
  $MM_qsChar = "?";
  $MM_referrer = $_SERVER['PHP_SELF'];
  if (strpos($MM_restrictGoTo, "?")) $MM_qsChar = "&";
  if (isset($_SERVER['QUERY_STRING']) && strlen($_SERVER['QUERY_STRING']) > 0) 
  $MM_referrer .= "?" . $_SERVER['QUERY_STRING'];
  $MM_restrictGoTo = $MM_restrictGoTo. $MM_qsChar . "accesscheck=" . urlencode($MM_referrer);
  header("Location: ". $MM_restrictGoTo); 
  exit;
}
?>
<?php
if (!function_exists("GetSQLValueString")) {
function GetSQLValueString($theValue, $theType, $theDefinedValue = "", $theNotDefinedValue = "") 
{
  if (PHP_VERSION < 6) {
    $theValue = get_magic_quotes_gpc() ? stripslashes($theValue) : $theValue;
  }

  $theValue = function_exists("mysql_real_escape_string") ? mysql_real_escape_string($theValue) : mysql_escape_string($theValue);

  switch ($theType) {
    case "text":
      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
      break;    
    case "long":
    case "int":
      $theValue = ($theValue != "") ? intval($theValue) : "NULL";
      break;
    case "double":
      $theValue = ($theValue != "") ? doubleval($theValue) : "NULL";
      break;
    case "date":
      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
      break;
    case "defined":
      $theValue = ($theValue != "") ? $theDefinedValue : $theNotDefinedValue;
      break;
  }
  return $theValue;
}
}

mysql_select_db($database_conn_ura, $conn_ura);
$query_rsOffDetail = "SELECT * FROM tbl_offence JOIN station ON station.stationcode=tbl_offence.stationcode WHERE tbl_offence.off_id=".$_GET['offId']."";
$rsOffDetail = mysql_query($query_rsOffDetail, $conn_ura) or die(mysql_error());
$row_rsOffDetail = mysql_fetch_assoc($rsOffDetail);
$totalRows_rsOffDetail = mysql_num_rows($rsOffDetail);

mysql_select_db($database_conn_ura, $conn_ura);
$query_rsImages = sprintf("SELECT tbl_img.filename FROM tbl_img WHERE tbl_img.off_id=%s", GetSQLValueString($_GET['offId'], "int"));
$rsImages = mysql_query($query_rsImages, $conn_ura) or die(mysql_error());
$totalRows_rsImages = mysql_num_rows($rsImages);

mysql_select_db($database_conn_ura, $conn_ura);
$query_rsCapGoods = "SELECT * FROM tbl_capturedgoods WHERE tbl_capturedgoods.off_id=".$row_rsOffDetail['off_id'];
$rsCapGoods = mysql_query($query_rsCapGoods, $conn_ura) or die(mysql_error());
$row_rsCapGoods = mysql_fetch_assoc($rsCapGoods);
$totalRows_rsCapGoods = mysql_num_rows($rsCapGoods);
$descGood=""; $goodsVal=0; $taxes=0;
do {
	$descGood.=$row_rsCapGoods['good_name']."HSCODE: ".$row_rsCapGoods['hscode']." - ".$row_rsCapGoods['good_descpn']." ".$row_rsCapGoods['unit_of_measure']."<br/>";
	$goodsVal+=$row_rsCapGoods['goods_val'];
	$taxes+=$row_rsCapGoods['taxes'];
}while($row_rsCapGoods = mysql_fetch_assoc($rsCapGoods));
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><!-- InstanceBegin template="/Templates/other_pages.dwt.php" codeOutsideHTMLIsLocked="false" -->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<!-- InstanceBeginEditable name="doctitle" -->
<title>URA Offences.::.Offence detail</title>
<!-- InstanceEndEditable -->
<script src="SpryAssets/SpryValidationSelect.js" type="text/javascript"></script>
<script src="SpryAssets/SpryValidationTextField.js" type="text/javascript"></script>
<script src="SpryAssets/SpryValidationTextarea.js" type="text/javascript"></script>
    <!--[if lt IE 7]>
    <style type="text/css">
    #wrapper { height:100%; }
    </style>
    <![endif]-->
<link href="SpryAssets/SpryValidationSelect.css" rel="stylesheet" type="text/css" />
<link href="SpryAssets/SpryValidationTextField.css" rel="stylesheet" type="text/css" />
<link href="SpryAssets/SpryValidationTextarea.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet" type="text/css" href="css/c.css" />
<!-- InstanceBeginEditable name="head" -->
<!-- InstanceEndEditable -->
</head>

<body><div id="wrapper">
<div id="header"></div>
<div id="nav_bar"><?php echo $_SESSION['MM_Username']; ?> (<?php echo $_SESSION['user_names']; ?>) | <a href="loginsuccess.php">Home</a> | <a href="logout.php">Logout</a></div>
<div class="clearfloat"></div>
  <div id="content"><!-- InstanceBeginEditable name="formstablesreports" -->
  <h3>Details</h3>
  <p><?php if($_SESSION['MM_UserGroup']=="Officer"||$_SESSION['MM_UserGroup']=="In Charge"){ ?><a href="edit_offence.php?offId=<?php echo $row_rsOffDetail['off_id']; ?>">Edit</a><?php }?>
|    <a href="report_offences.php">View all</a></p>
  <p>&nbsp;</p>
  <center>
  <table width="0" border="0" cellpadding="3" cellspacing="0" id="detailReport">
    <tr>
      <th>STATION:</th>
      <td><?php echo $row_rsOffDetail['name']; ?></td>
      <th>Date Reported:</th>
      <td><?php echo $row_rsOffDetail['rep_date']; ?></td>
    </tr>
    <tr>
      <th>Entry No:</th>
      <td><?php echo $row_rsOffDetail['entry_no']; ?></td>
      <th>OFFENCE NUMBER:</th>
      <td><?php echo $row_rsOffDetail['file_no']; ?></td>
    </tr>
    <tr>
      <td colspan="4"><?php echo ($row_rsOffDetail['topup'])?"Top up":""; ?></td>
    </tr>
    <tr>
      <th>Offenders Names:</th>
      <td><?php echo $row_rsOffDetail['offender_names']; ?></td>
      <th>Nature of offence:</th>
      <td><?php echo $row_rsOffDetail['nature_offence']; ?></td>
    </tr>
    <tr>
      <th>Section of the Law:</th>
      <td><?php echo $row_rsOffDetail['sect_law']; ?></td>
      <td>&nbsp;</td>
      <td><?php echo ($row_rsOffDetail['dutable'])?"Dutable good(s)":"Non dutable good(s)"; ?></td>
    </tr>
    <tr>
      <th>Description of goods</th>
      <td colspan="3"><?php echo $descGood; ?></td>
      </tr>
    <tr>
      <th>Method of detection:</th>
      <td><?php echo $row_rsOffDetail['det_method']; ?></td>
      <th>Transport means:</th>
      <td><?php echo $row_rsOffDetail['trans_means']; ?></td>
    </tr>
    <tr>
      <th>&nbsp;</th>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <th>Value of goods:</th>
      <td><?php echo $goodsVal; ?></td>
      <th>Taxes:</th>
      <td><?php echo $taxes; ?></td>
    </tr>
    <tr>
      <th>Fine:</th>
      <td><?php echo $row_rsOffDetail['fines']; ?></td>
      <th>Total:</th>
      <td><?php echo $taxes+$row_rsOffDetail['fines']; ?></td>
    </tr>
    <tr>
      <th>PRN:</th>
      <td><?php echo $row_rsOffDetail['rec_prn']; ?></td>
      <th>Remarks:</th>
      <td><?php echo $row_rsOffDetail['remarks']; ?></td>
    </tr>
    <?php 
	$totalRows_rsImages;
	$DestinationDirectory	= "up_photos/";?>
      <tr>
      <?php $col; for($col=0;($row_rsImages = mysql_fetch_assoc($rsImages))&&$col<4;$col++) {?>
        <td><img src="<?php echo $DestinationDirectory.$row_rsImages['filename']; ?>" alt="Evidence photos" width="100" height="100" /></td>
      <?php }?>
      </tr>
  </table>
  </center>
  <p>
<button id="print_btn" onclick="window.print()" title="Print"></button></p>
  <p>&nbsp;</p>
  <!-- InstanceEndEditable --> </div>
  <!-- end #content -->
  <div class="clearfloat"></div>
<div id="footer"></div><!-- end #footer -->
</div><!-- end #wrapper -->
</body>
<!-- InstanceEnd --></html>
<?php
mysql_free_result($rsOffDetail);

mysql_free_result($rsImages);

mysql_free_result($rsCapGoods);
?>
