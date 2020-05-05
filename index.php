<?php require_once('Connections/conn_ura.php'); ?>
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
?>
<?php
// *** Validate request to login to this site.
if (!isset($_SESSION)) {
  session_start();
}

$loginFormAction = $_SERVER['PHP_SELF'];
if (isset($_GET['accesscheck'])) {
  $_SESSION['PrevUrl'] = $_GET['accesscheck'];
}

if (isset($_POST['username'])) {
  $loginUsername=$_POST['username'];
  $password=md5($_POST['password']);
  $MM_fldUserAuthorization = "Role";
  $MM_redirectLoginSuccess = "loginsuccess.php";
  $MM_redirectLoginFailed = "index.php?msg=Wrong username or password";
  $MM_redirecttoReferrer = false;
  mysql_select_db($database_conn_ura, $conn_ura);
  	
  $LoginRS__query=sprintf("SELECT Staff_id, CONCAT(Firstname,' ',Lastname) NAMES, dutystation, Username, Password, Role FROM staff WHERE Username=BINARY%s AND Password=BINARY%s",
  GetSQLValueString($loginUsername, "text"), GetSQLValueString($password, "text")); 
   
  $LoginRS = mysql_query($LoginRS__query, $conn_ura) or die(mysql_error());
  $loginFoundUser = mysql_num_rows($LoginRS);
  if ($loginFoundUser) {
    
    $loginStrGroup  = mysql_result($LoginRS,0,'Role');
    
	if (PHP_VERSION >= 5.1) {session_regenerate_id(true);} else {session_regenerate_id();}
    //declare two session variables and assign them
    $_SESSION['MM_Username'] = $loginUsername;
    $_SESSION['MM_UserGroup'] = $loginStrGroup;	
    $_SESSION['MM_UserID'] = mysql_result($LoginRS,0,'Staff_id');   
    $_SESSION['user_names'] = mysql_result($LoginRS,0,'NAMES'); 
    $_SESSION['StationCode'] = mysql_result($LoginRS,0,'dutystation');
	
	if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form1")) {
		$_SESSION['uname']=$_POST['uname'];
		$_SESSION['npass']=$_POST['npass'];
		$MM_redirectLoginSuccess = "updateProfile.php";
	}

    else if (isset($_SESSION['PrevUrl']) && false) {
      $MM_redirectLoginSuccess = $_SESSION['PrevUrl'];	
    }
	else ;
    header("Location: " . $MM_redirectLoginSuccess );
  }
  else {
    header("Location: ". $MM_redirectLoginFailed );
  }
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><!-- InstanceBegin template="/Templates/login.dwt.php" codeOutsideHTMLIsLocked="false" -->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<!-- InstanceBeginEditable name="doctitle" -->
<title>URA Offences.::.Login</title>
<link href="SpryAssets/SpryValidationPassword.css" rel="stylesheet" type="text/css" />
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
<!-- InstanceEndEditable -->
</head>

<body><div id="wrapper">
<div id="header"></div>
<div class="clearfloat"></div>
  <div id="content"><!-- InstanceBeginEditable name="formstablesreports" -->
  <h2>Login</h2>
  <form id="form1" name="form1" method="POST" action="<?php echo $loginFormAction; ?>"><fieldset>
    <p id="msg"><?php echo isset($_GET['msg'])?$_GET['msg']:""; ?></p>
  <center>
    <table width="0" border="0" class="fm_tbl" id="login">
      <tr>
      <th><label for="username">Username:</label></th>
      <td>
        <span id="spryusername">
          <input type="text" name="username" id="username" /><br/>
          <span class="textfieldRequiredMsg">Username missing.</span></span>
      </td>
    </tr>
    <tr>
      <th><label for="password">Password:</label></th>
      <td>
        <span id="sprypassword">
          <input type="password" name="password" id="password" /><br/>
          <span class="passwordRequiredMsg">Password missing.</span></span></td>
    </tr>
    <tr>
      <th>&nbsp;</th>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <th>&nbsp;</th>
      <td><div align="left">
        <input name="Submit" type="submit" class="submissions" id="button1" style="width:80px" value="Login" />
      </div></td>
    </tr>
    <tr>
      <th>&nbsp;</th>
      <td><input name="MM_update" type="hidden" id="MM_update" value="<?php echo isset($_POST["MM_update"])?$_POST["MM_update"]:""; ?>" />
        <input name="uname" type="hidden" id="uname" value="<?php echo  isset($_POST['usname'])?$_POST['usname']:""; ?>" />
        <input name="npass" type="hidden" id="npass" value="<?php echo  isset($_POST['newPass'])?$_POST['newPass']:""; ?>" /></td>
    </tr>
    </table>
  </center>
  </fieldset>
      </form>
    <script type="text/javascript">
var sprytextfield1 = new Spry.Widget.ValidationTextField("spryusername", "none", {validateOn:["blur"]});
var sprypassword1 = new Spry.Widget.ValidationPassword("sprypassword", {validateOn:["blur"]});
    </script>
  <!-- InstanceEndEditable --> </div>
  <!-- end #content -->
  <div class="clearfloat"></div>
<div id="footer"></div><!-- end #footer -->
</div><!-- end #wrapper -->
</body>
<!-- InstanceEnd --></html>