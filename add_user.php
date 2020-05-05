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

$MM_restrictGoTo = "index.php?msg=You do not have access to this page, login as admin";
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
	/*$alpha = "abcdefghijklmnopqrstuvwxyz";
	$alpha_upper = strtoupper($alpha);
	$numeric = "0123456789";
	$special = "_@$#*%";
	$chars = $alpha . $alpha_upper . $numeric.$special;
    $length = 9;
	$len = strlen($chars);
	$pw = '';
	for ($i=0;$i<$length;$i++)
        $pw .= substr($chars, rand(0, $len-1), 1);
	// the finished password
	$pw = str_shuffle($pw);*/
	$pw = $usernm=strtolower(substr($_POST['firstname'],0,1).$_POST['lastname']);
  $insertSQL = sprintf("INSERT INTO staff (firstname, lastname, username, password, dutystation, mobile, email, `role`, address) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s)",
                       GetSQLValueString(strtoupper($_POST['firstname']), "text"),
                       GetSQLValueString(strtoupper($_POST['lastname']), "text"),
                       GetSQLValueString($usernm, "text"),
                       GetSQLValueString(md5($pw), "text"),
                       GetSQLValueString($_POST['dutystation'], "text"),
                       GetSQLValueString($_POST['mobile'], "text"),
                       GetSQLValueString($_POST['email'], "text"),
                       GetSQLValueString($_POST['role'], "text"),
                       GetSQLValueString($_POST['address'], "text"));

  mysql_select_db($database_conn_ura, $conn_ura);
  $Result1 = mysql_query($insertSQL, $conn_ura) or die(mysql_error());

  $insertGoTo = "view_staff.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
}

mysql_select_db($database_conn_ura, $conn_ura);
$query_rsDS = "SELECT station.stationcode, station.name FROM station";
$rsDS = mysql_query($query_rsDS, $conn_ura) or die(mysql_error());
$row_rsDS = mysql_fetch_assoc($rsDS);
$totalRows_rsDS = mysql_num_rows($rsDS);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><!-- InstanceBegin template="/Templates/other_pages.dwt.php" codeOutsideHTMLIsLocked="false" -->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<!-- InstanceBeginEditable name="doctitle" -->
<title>URA Offences.::.Register User</title>
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
  <h4>Add staff (user)</h4>
  <p><a href="view_staff.php" class="small">View all</a></p>
  <form id="form1" name="form1" method="POST" action="<?php echo $editFormAction; ?>">
  <center>
    <table width="0" border="0" class="fm_tbl">
      <tr>
        <td><label for="firstname">Firstname:</label></td>
        <td><span id="sprytextfield1">
          <input type="text" name="firstname" id="firstname" />
          <span class="textfieldRequiredMsg">*</span></span></td>
        </tr>
      <tr>
        <td><label for="lastname">Lastname:</label></td>
        <td><span id="sprytextfield2">
          <input type="text" name="lastname" id="lastname" />
          <span class="textfieldRequiredMsg">*</span></span></td>
        </tr>
      <tr>
        <td><label for="mobile">Mobile:</label></td>
        <td><span id="sprytextfield3">
        <input type="text" name="mobile" id="mobile" />
        <span class="textfieldRequiredMsg">*</span><span class="textfieldInvalidFormatMsg">Invalid format.</span></span></td>
        </tr>
      <tr>
        <td><label for="email">Email:</label></td>
        <td><span id="sprytextfield4">
          <input type="text" name="email" id="email" />
        <span class="textfieldRequiredMsg">*</span><span class="textfieldInvalidFormatMsg">Invalid email</span></span></td>
        </tr>
      <tr>
        <td><label for="address">Address:</label></td>
        <td><span id="sprytextfield5">
          <input type="text" name="address" id="address" />
          <span class="textfieldRequiredMsg">*</span></span></td>
        </tr>
      <tr>
        <td><label for="role">Role:</label></td>
        <td><span id="spryselect1">
          <select name="role" id="role">
            <option>Select One...</option>
            <option value="Officer">Officer</option>
            <option value="In Charge">In Charge</option>
            <option value="Regional Supervisor">Regional Supervisor</option>
            <option value="Manager">Manager</option>
            <option value="Asst Comm">Asst Comm</option>
          </select>
          <span class="selectRequiredMsg">*</span></span></td>
        </tr>
      <tr>
        <td><label for="dutystation">Duty Station:</label></td>
        <td><span id="spryselect2">
          <select name="dutystation" id="dutystation">
            <option value="">Select one...</option>
            <?php
do {  
?>
            <option value="<?php echo $row_rsDS['stationcode']?>"><?php echo $row_rsDS['name']?></option>
            <?php
} while ($row_rsDS = mysql_fetch_assoc($rsDS));
  $rows = mysql_num_rows($rsDS);
  if($rows > 0) {
      mysql_data_seek($rsDS, 0);
	  $row_rsDS = mysql_fetch_assoc($rsDS);
  }
?>
          </select>
          <span class="selectRequiredMsg">*</span></span></td>
        </tr>
      <tr>
        <th scope="row">&nbsp;</th>
        <td>&nbsp;</td>
        </tr>
      <tr>
        <th scope="row">&nbsp;</th>
        <td><input name="Submit" type="submit" class="submissions" id="button1" style="width:80px" value="Register" /></td>
        </tr>
      </table>
      </center>
    <input type="hidden" name="MM_insert" value="form1" />
  </form>
  <script type="text/javascript">
var sprytextfield1 = new Spry.Widget.ValidationTextField("sprytextfield1", "none", {validateOn:["blur"]});
var sprytextfield2 = new Spry.Widget.ValidationTextField("sprytextfield2", "none", {validateOn:["blur"]});
var sprytextfield3 = new Spry.Widget.ValidationTextField("sprytextfield3", "phone_number", {format:"phone_custom", useCharacterMasking:true, pattern:"+256 000 000 000", hint:"+256 781 234 567", validateOn:["blur"]});
var sprytextfield4 = new Spry.Widget.ValidationTextField("sprytextfield4", "email", {hint:"eg: yourname@ura.go.ug", validateOn:["blur"], useCharacterMasking:true});
var sprytextfield5 = new Spry.Widget.ValidationTextField("sprytextfield5", "none", {validateOn:["blur"]});
var spryselect1 = new Spry.Widget.ValidationSelect("spryselect1", {validateOn:["blur"]});
var spryselect2 = new Spry.Widget.ValidationSelect("spryselect2", {validateOn:["blur"]});
  </script>
  <!-- InstanceEndEditable --> </div>
  <!-- end #content -->
  <div class="clearfloat"></div>
<div id="footer"></div><!-- end #footer -->
</div><!-- end #wrapper -->
</body>
<!-- InstanceEnd --></html>
<?php
mysql_free_result($rsDS);
?>
