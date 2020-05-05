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

$MM_restrictGoTo = "index.php?msg=Please login as admin to access this page";
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

$dere_rsStn = "mde";
if (isset($_GET['stationcode'])) {
  $dere_rsStn = $_GET['stationcode'];
}
mysql_select_db($database_conn_ura, $conn_ura);
$query_rsStn = sprintf("SELECT station.stationcode, station.name, station.region, station.stationcode FROM station WHERE station.stationcode=%s", GetSQLValueString($dere_rsStn, "text"));
$rsStn = mysql_query($query_rsStn, $conn_ura) or die(mysql_error());
$row_rsStn = mysql_fetch_assoc($rsStn);
$totalRows_rsStn = mysql_num_rows($rsStn);

mysql_select_db($database_conn_ura, $conn_ura);
$query_region = "SELECT region.rid, region.name FROM region";
$region = mysql_query($query_region, $conn_ura) or die(mysql_error());
$row_region = mysql_fetch_assoc($region);
$totalRows_region = mysql_num_rows($region);

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form1")) {
  $updateSQL = sprintf("UPDATE station SET stationcode=%s, name=%s, region=%s, modifiedby=%s WHERE stationcode=%s",
                       GetSQLValueString($_POST['code'], "text"),
                       GetSQLValueString($_POST['name'], "text"),
                       GetSQLValueString($_POST['region'], "int"),
                       GetSQLValueString($_POST['modifiedby'], "int"),
                       GetSQLValueString($row_rsStn['stationcode'], "text"));

  mysql_select_db($database_conn_ura, $conn_ura);
  $Result1 = mysql_query($updateSQL, $conn_ura) or die(mysql_error());

  $updateGoTo = "report_wstations.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    $updateGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $updateGoTo));
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><!-- InstanceBegin template="/Templates/other_pages.dwt.php" codeOutsideHTMLIsLocked="false" -->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<!-- InstanceBeginEditable name="doctitle" -->
<title>URA Offences.::.Edit Station</title>
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
<div id="nav_bar"><?php echo $_SESSION['MM_Username']; ?>
(<?php echo $_SESSION['user_names']; ?>) | <a href="loginsuccess.php">Home</a> | <a href="logout.php">Logout</a>
</div>
<div class="clearfloat"></div>
<div id="content"><!-- InstanceBeginEditable name="formstablesreports" -->
  <center>
    <form id="form1" name="form1" method="POST" action="<?php echo $editFormAction; ?>">
      <fieldset>
        <legend>New station</legend>
        <p><a href="report_wstations.php" class="small">View All Stations</a></p>
        <p>&nbsp;</p>
        <table width="0" border="0" class="fm_tbl">
          <tr>
            <td><label for="name">Station name:</label></td>
            <td><span id="spryname">
              <input name="name" type="text" id="name" value="<?php echo $row_rsStn['name']; ?>" />
              <span class="textfieldRequiredMsg">*</span></span></td>
          </tr>
          <tr>
            <td>Code:</td>
            <td><span id="sprytextfield2">
              <label for="code"></label>
              <input name="code" type="text" id="code" value="<?php echo $row_rsStn['stationcode']; ?>" size="5" maxlength="5" />
              <span class="textfieldRequiredMsg">*</span><span class="textfieldInvalidFormatMsg">Invalid code</span></span></td>
          </tr>
          <tr>
            <td><label for="region">Region:</label></td>
            <td><span id="spryregion">
              <select name="region" id="region">
                <option value="" <?php if (!(strcmp("", $row_rsStn['region']))) {echo "selected=\"selected\"";} ?>>Select region</option>
                <?php
do {  
?>
                <option value="<?php echo $row_region['rid']?>"<?php if (!(strcmp($row_region['rid'], $row_rsStn['region']))) {echo "selected=\"selected\"";} ?>><?php echo $row_region['name']?></option>
                <?php
} while ($row_region = mysql_fetch_assoc($region));
  $rows = mysql_num_rows($region);
  if($rows > 0) {
      mysql_data_seek($region, 0);
	  $row_region = mysql_fetch_assoc($region);
  }
?>
              </select>
              <span class="selectRequiredMsg">*</span></span></td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td><input name="modifiedby" type="hidden" id="modifiedby" value="<?php echo $_SESSION['MM_UserID']; ?>" />
              <input name="stationcode" type="hidden" id="stationcode" value="<?php echo $row_rsStn['stationcode']; ?>" /></td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td><input name="Submit" type="submit" class="submissions" id="button1" style="width:80px" value="Save" /></td>
          </tr>
        </table>
        <p>&nbsp;</p>
      </fieldset>
      <input name="MM_update" type="hidden" id="MM_update" value="form1" />
      <input type="hidden" name="MM_update" value="form1" />
    </form>
  </center>
  <script type="text/javascript">
var sprytextfield1 = new Spry.Widget.ValidationTextField("spryname");
var spryselect1 = new Spry.Widget.ValidationSelect("spryregion");
var sprytextfield2 = new Spry.Widget.ValidationTextField("sprytextfield2", "custom", {validateOn:["blur"], pattern:"UGXXX"});
    </script>
<!-- InstanceEndEditable --></div>
<!-- end #content -->
<div class="clearfloat"></div>
<div id="footer"></div>
<!-- end #footer -->
</div>
<!-- end #wrapper -->
</body><!-- InstanceEnd -->
</html>
<?php
mysql_free_result($rsStn);
mysql_free_result($region);
?>
