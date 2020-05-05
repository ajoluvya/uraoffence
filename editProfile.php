<?php require_once('Connections/conn_ura.php'); ?>
<?php
if (!isset($_SESSION)) {
  session_start();
}
$MM_authorizedUsers = "";
$MM_donotCheckaccess = "true";

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
    if (($strUsers == "") && true) { 
      $isValid = true; 
    } 
  } 
  return $isValid; 
}

$MM_restrictGoTo = "insert_success.php";
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
mysql_select_db($database_conn_ura, $conn_ura);
$query_rsStaffDetail = "SELECT staff.firstname, staff.lastname, staff.username, staff.password FROM staff WHERE staff.staff_id=".$_SESSION['MM_UserID'];
$rsStaffDetail = mysql_query($query_rsStaffDetail, $conn_ura) or die(mysql_error());
$row_rsStaffDetail = mysql_fetch_assoc($rsStaffDetail);
$totalRows_rsStaffDetail = mysql_num_rows($rsStaffDetail);

$editFormAction = "index.php?msg=Login with the old password to complete password change";

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><!-- InstanceBegin template="/Templates/other_pages.dwt.php" codeOutsideHTMLIsLocked="false" -->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<!-- InstanceBeginEditable name="doctitle" -->
<title>URA Offences.::.Edit profile</title>
<link href="SpryAssets/SpryValidationPassword.css" rel="stylesheet" type="text/css" />
<link href="SpryAssets/SpryValidationConfirm.css" rel="stylesheet" type="text/css" />
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
<script src="SpryAssets/SpryValidationPassword.js" type="text/javascript"></script>
<script src="SpryAssets/SpryValidationConfirm.js" type="text/javascript"></script>
<!-- InstanceEndEditable -->
</head>

<body><div id="wrapper">
<div id="header"></div>
<div id="nav_bar"><?php echo $_SESSION['MM_Username']; ?> (<?php echo $_SESSION['user_names']; ?>) | <a href="loginsuccess.php">Home</a> | <a href="logout.php">Logout</a></div>
<div class="clearfloat"></div>
  <div id="content"><!-- InstanceBeginEditable name="formstablesreports" -->
  <h2>Edit profile</h2>
  <center>
  <form id="form1" name="form1" method="POST" action="<?php echo $editFormAction; ?>">
    <table width="0" border="0" cellspacing="0" class="fm_tbl">
      <tr>
        <th scope="row"><label for="firstname">Firstname:</label></th>
        <td><input name="firstname" type="text" id="firstname" value="<?php echo $row_rsStaffDetail['firstname']; ?>" readonly="readonly" /></td>
      </tr>
      <tr>
        <th scope="row"><label for="lastname">Lastname:</label></th>
        <td><input name="lastname" type="text" id="lastname" value="<?php echo $row_rsStaffDetail['lastname']; ?>" readonly="readonly" /></td>
      </tr>
      <tr>
        <th scope="row"><label for="usname">Username:</label></th>
        <td><span id="sprytextfield1">
          <input name="usname" type="text" id="usname" value="<?php echo $row_rsStaffDetail['username']; ?>" />
          <span class="textfieldRequiredMsg">A value is required.</span></span></td>
      </tr>
      <tr>
        <th scope="row"><label for="newPass">New Password:</label></th>
        <td><span id="sprypassword3">
          <input type="password" name="newPass" id="newPass" />
          <br />
          <span class="passwordRequiredMsg">New password  must be entered.</span></span></td>
      </tr>
      <tr>
        <th scope="row"><label for="confirm">Confirm Password:</label></th>
        <td><span id="spryconfirm1">
          <input type="password" name="confirm" id="confirm" />
          <br />
          <span class="confirmRequiredMsg">Password is required.</span><span class="confirmInvalidMsg">Passwords don't match.</span></span></td>
      </tr>
      <tr>
        <th scope="row">&nbsp;</th>
        <td>&nbsp;</td>
      </tr>
      <tr>
        <th scope="row">&nbsp;</th>
        <td><input type="submit" name="button" id="button" value="Submit" style="width:80px" class="submissions" /></td>
      </tr>
    </table>
    <input type="hidden" name="MM_update" value="form1" />
  </form>
  </center>
  <p>&nbsp;</p>
  <script type="text/javascript">
var sprytextfield1 = new Spry.Widget.ValidationTextField("sprytextfield1");
var sprypassword3 = new Spry.Widget.ValidationPassword("sprypassword3");
var spryconfirm1 = new Spry.Widget.ValidationConfirm("spryconfirm1", "newPass", {validateOn:["blur"]});
  </script>
  <!-- InstanceEndEditable --> </div>
  <!-- end #content -->
  <div class="clearfloat"></div>
<div id="footer"></div><!-- end #footer -->
</div><!-- end #wrapper -->
</body>
<!-- InstanceEnd --></html>
<?php
mysql_free_result($rsStaffDetail);
?>
