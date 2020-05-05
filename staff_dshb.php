<?php
if (!isset($_SESSION)) {
  session_start();
}
$MM_authorizedUsers = "Officer,In Charge";
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

$MM_restrictGoTo = "index.php?msg=You are not authorised to view this page";
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
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><!-- InstanceBegin template="/Templates/other_pages.dwt.php" codeOutsideHTMLIsLocked="false" -->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<!-- InstanceBeginEditable name="doctitle" -->
<title>URA Offences.::.Staff dashboard</title>
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
<div id="nav_bar"><?php echo $_SESSION['MM_Username']; ?> (<?php echo $_SESSION['user_names']; ?>)</div>
<div class="clearfloat"></div>
  <div id="content"><!-- InstanceBeginEditable name="formstablesreports" --> 
  <h3><?php echo $_SESSION['MM_UserGroup']; ?> Dashboard</h3>
  <p>&nbsp;</p>
  <center>
    <table border="0" cellpadding="4" cellspacing="0" class="dash">
      <tbody>
        <tr style="text-align: left;">
         <?php if($_SESSION['MM_UserGroup']=="Officer"){?> <td>&nbsp;</td>
          <td><a href="add_offence.php"><img src="images/add_off.png" width="115" height="116" alt="Add offence" title="Add offence"/></a></td><?php }?>
          <td>&nbsp;</td>
          <td><a href="report_offences.php"><img src="images/view.png" width="115" height="116" alt="view" title="All Records/Offences" /></a></td>
        </tr><?php if($_SESSION['MM_UserGroup']=="In Charge"){?>
        <tr style="text-align: left;">
          <td>&nbsp;</td>
          <td><a href="challenges.php"><img src="images/add.png" width="115" height="116" alt="add" title="Add Challenges" /></a></td>
          <td>&nbsp;</td>
          <td><a href="http://ura.go.ug/" target="_blank"><img src="images/web.png" width="115" height="116" alt="web" title="URA Potal" /></a></td>
        </tr>
        <?php }?>
        <tr style="text-align: left;">
          <td>&nbsp;</td>
          <td><a href="report_all.php"><img src="images/report.png" width="115" height="116" alt="report" title="Monthly Report
          " /></a></td>
          <td>&nbsp;</td>
          <td><a href="#"><img src="images/adminc.png" width="115" height="116" alt="admin contact" title="contact Admin: 0704958164" /></a></td>
          </tr>
        <tr style="text-align: left;">
          <td>&nbsp;</td>
          <td><a href="editProfile.php"><img src="images/edit.png" width="115" height="116" alt="edit" title="Edit User Profile" /></a></td>
          <td>&nbsp;</td>
          <td><a href="logout.php"><img src="images/logout.png" width="115" height="116" alt="logout" title="logout" /></a></td>
        </tr>
      </tbody>
    </table>
    </center>
    <p>&nbsp;</p>
  <!-- InstanceEndEditable --> </div>
  <!-- end #content -->
  <div class="clearfloat"></div>
<div id="footer"></div><!-- end #footer -->
</div><!-- end #wrapper -->
</body>
<!-- InstanceEnd --></html>