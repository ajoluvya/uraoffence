<?php require_once('Connections/conn_ura.php'); ?>
<?php
if (!isset($_SESSION)) {
  session_start();
}
$MM_authorizedUsers = "admin";
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

$MM_restrictGoTo = "index.php?msg=You not allowed to view the staff detail page, please contact the admin";
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

$stfId_rsStaffDtl = "0";
if (isset($_GET['staffId'])) {
  $stfId_rsStaffDtl = $_GET['staffId'];
}
mysql_select_db($database_conn_ura, $conn_ura);
$query_rsStaffDtl = sprintf("SELECT staff.staff_id, staff.firstname, staff.lastname, staff.username, staff.mobile, staff.email, staff.`role`, staff.address, station.name dutystation FROM staff JOIN station ON  station.stationcode=staff.dutystation WHERE staff.staff_id=%s", GetSQLValueString($stfId_rsStaffDtl, "int"));
$rsStaffDtl = mysql_query($query_rsStaffDtl, $conn_ura) or die(mysql_error());
$row_rsStaffDtl = mysql_fetch_assoc($rsStaffDtl);
$totalRows_rsStaffDtl = mysql_num_rows($rsStaffDtl);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><!-- InstanceBegin template="/Templates/other_pages.dwt.php" codeOutsideHTMLIsLocked="false" -->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<!-- InstanceBeginEditable name="doctitle" -->
<title>URA Offences.::.Staff Detail Info</title>
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
  <h3>STAFF BIO DATA</h3>
  <?php if ($totalRows_rsStaffDtl == 0) { // Show if recordset empty ?>
    <p class="small">No matching records</p>
    <?php } // Show if recordset empty ?>
<p><a href="add_user.php" class="small">Register new</a>
  <input type="button" name="button" id="print_btn" onclick="window.print()" />
</p>
<?php if ($totalRows_rsStaffDtl > 0) { // Show if recordset not empty ?>
<center>
  <table width="0" border="0">
    <tr>
      <td>Staff ID:</td>
      <td><?php echo $row_rsStaffDtl['staff_id']; ?></td>
      </tr>
    <tr>
      <td>Names:</td>
      <td><?php echo $row_rsStaffDtl['firstname']; ?> <?php echo $row_rsStaffDtl['lastname']; ?></td>
      </tr>
    <tr>
      <td>Username:</td>
      <td><?php echo $row_rsStaffDtl['username']; ?></td>
      </tr>
    <tr>
      <td>Duty Station:</td>
      <td><?php echo $row_rsStaffDtl['dutystation']; ?></td>
    </tr>
    <tr>
      <td>Mobile No:</td>
      <td><?php echo $row_rsStaffDtl['mobile']; ?></td>
      </tr>
    <tr>
      <td>Email:</td>
      <td><?php echo $row_rsStaffDtl['email']; ?></td>
      </tr>
    <tr>
      <td>Address:</td>
      <td><?php echo $row_rsStaffDtl['address']; ?></td>
      </tr>
    <tr>
      <td scope="row">Role:</td>
      <td><?php echo $row_rsStaffDtl['role']; ?></td>
      </tr>
  </table>
  </center>
  <?php } // Show if recordset not empty ?>
<p>&nbsp;</p>
  <!-- InstanceEndEditable --> </div>
  <!-- end #content -->
  <div class="clearfloat"></div>
<div id="footer"></div><!-- end #footer -->
</div><!-- end #wrapper -->
</body>
<!-- InstanceEnd --></html>
<?php
mysql_free_result($rsStaffDtl);
?>
