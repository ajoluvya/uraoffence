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

$MM_restrictGoTo = "index.php?msg=Access denied, contact admin";
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

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form1")) {
  $updateSQL = sprintf("UPDATE region SET name=%s, modifiedby=%s WHERE rid=%s",
                       GetSQLValueString($_POST['name'], "text"),
                       GetSQLValueString($_POST['modifiedBy'], "int"),
                       GetSQLValueString($_POST['rid'], "int"));

  mysql_select_db($database_conn_ura, $conn_ura);
  $Result1 = mysql_query($updateSQL, $conn_ura) or die(mysql_error());

  $updateGoTo = "report_regions.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    $updateGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $updateGoTo));
}

$regsin_rsRegion = "0";
if (isset($_GET['regId'])) {
  $regsin_rsRegion = $_GET['regId'];
}
mysql_select_db($database_conn_ura, $conn_ura);
$query_rsRegion = sprintf("SELECT region.name FROM region WHERE region.rid=%s", GetSQLValueString($regsin_rsRegion, "int"));
$rsRegion = mysql_query($query_rsRegion, $conn_ura) or die(mysql_error());
$row_rsRegion = mysql_fetch_assoc($rsRegion);
$totalRows_rsRegion = mysql_num_rows($rsRegion);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><!-- InstanceBegin template="/Templates/other_pages.dwt.php" codeOutsideHTMLIsLocked="false" -->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<!-- InstanceBeginEditable name="doctitle" -->
<title>URA Offences.::.Edit region</title>
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
  <h2>Editing region</h2>
  <p><a href="report_regions.php">All regions</a></p>
  <form id="form1" name="form1" method="POST" action="<?php echo $editFormAction; ?>">
  <center>
    <table width="0" border="0" class="fm_tbl">
      <tr>
        <td><label for="name">Region name:</label></td>
        <td><span id="sprytextfield1">
          <input name="name" type="text" id="name" value="<?php echo $row_rsRegion['name']; ?>" />
          <span class="textfieldRequiredMsg">A value is required.</span></span></td>
</tr>
      <tr>
        <td>&nbsp;</td>
        <td><input name="modifiedBy" type="hidden" id="modifiedBy" value="<?php echo $_SESSION['MM_UserID']; ?>" />
          <input name="rid" type="hidden" id="rid" value="<?php echo $_GET['regId']; ?>" /></td>
      </tr>
      <tr>
        <td>&nbsp;</td>
        <td><input name="Submit" type="submit" class="submissions" id="button1" style="width:80px" value="Submit" /></td>
      </tr>
      <tr> </tr>
    </table>
    </center>
    <input type="hidden" name="MM_update" value="form1" />
  </form>
  <p>&nbsp;</p>
  <script type="text/javascript">
var sprytextfield1 = new Spry.Widget.ValidationTextField("sprytextfield1");
  </script>
  <!-- InstanceEndEditable --> </div>
  <!-- end #content -->
  <div class="clearfloat"></div>
<div id="footer"></div><!-- end #footer -->
</div><!-- end #wrapper -->
</body>
<!-- InstanceEnd --></html>
<?php
mysql_free_result($rsRegion);
?>
