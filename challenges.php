<?php require_once('Connections/conn_ura.php'); ?>
<?php
if (!isset($_SESSION)) {
  session_start();
}
$MM_authorizedUsers = "In Charge,Regional Supervisor";
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

$MM_restrictGoTo = "index.php?msg=Access denied, please login as Regional Supervisor or contact admin";
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

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {
  $insertSQL = sprintf("INSERT INTO tbl_challenges (challenge, stationID, op_undertaken, recordedBy, daterecorded, summary) VALUES (%s, %s, %s, %s,CURDATE(), %s)",
                       GetSQLValueString($_POST['challenge'], "text"),
                       GetSQLValueString($_SESSION['StationCode'], "text"),
                       GetSQLValueString($_POST['op_undertaken'], "text"),
                       GetSQLValueString($_SESSION['MM_UserID'], "int"),
                       GetSQLValueString($_POST['summary'], "text"));

  mysql_select_db($database_conn_ura, $conn_ura);
  $Result1 = mysql_query($insertSQL, $conn_ura) or die(mysql_error());

  $insertGoTo = "loginsuccess.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><!-- InstanceBegin template="/Templates/other_pages.dwt.php" codeOutsideHTMLIsLocked="false" -->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<!-- InstanceBeginEditable name="doctitle" -->
<title>URA Offences.::.Challenges, Focussed operations undertaken and Summary of efforts</title>
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
    <form id="form1" name="form1" method="POST" action="<?php echo $editFormAction; ?>">
      <h2>CHALLENGES, FOCUSSED OPERATIONS UNDERTAKEN</h2>
      <h2> AND SUMMARY OF EFFORTS UNDERTAKEN</h2>
      <p>&nbsp;</p>
      <center>
      <table width="0" border="0" class="fm_tbl">
  <tr>
    <th scope="row"><label for="challenge">CHALLENGES FACED:</label></th>
    <td><span id="sprytextarea1">
      <textarea name="challenge" id="challenge" cols="45" rows="5"></textarea>
      <span class="textareaRequiredMsg"><br />
      This is required.</span></span></td>
  </tr>
  <tr>
    <th scope="row"><label for="op_undertaken">FOCUSSED OPERATION<br />
UNDERTAKEN:</label></th>
    <td><span id="sprytextarea2">
      <textarea name="op_undertaken" id="op_undertaken" cols="45" rows="5"></textarea>
      <span class="textareaRequiredMsg"><br />
      This is required.</span></span></td>
  </tr>
  <tr>
    <th scope="row"><label for="op_undertaken">SUMMARY OF EFFORTS <br />
      UNDERTAKEN TO IMPROVE <br />
      COMPLIANCY AT <br />
      THE STATION</label></th>
    <td><span id="sprytextarea3">
      <textarea name="summary" id="op_undertaken" cols="45" rows="5"></textarea>
      <br />
      <span class="textareaRequiredMsg">This is required.</span></span></td>
  </tr>
  <tr>
    <th scope="row">&nbsp;</th>
    <td></td>
  </tr>
  <tr>
    <th scope="row">&nbsp;</th>
    <td><input name="button" type="submit" class="submissions" id="button" value="Submit" style="width:80px" /></td>
  </tr>
</table>
</center>
      <input type="hidden" name="MM_insert" value="form1" />

    </form>
  <p>&nbsp;</p>
  <script type="text/javascript">
var sprytextarea1 = new Spry.Widget.ValidationTextarea("sprytextarea1");
var sprytextarea2 = new Spry.Widget.ValidationTextarea("sprytextarea2");
var sprytextarea3 = new Spry.Widget.ValidationTextarea("sprytextarea3");
  </script>
  <!-- InstanceEndEditable --> </div>
  <!-- end #content -->
  <div class="clearfloat"></div>
<div id="footer"></div><!-- end #footer -->
</div><!-- end #wrapper -->
</body>
<!-- InstanceEnd --></html>
