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

$MM_restrictGoTo = "index.php?msg=You are not allowed to view this page, please log in as admin";
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

$currentPage = $_SERVER["PHP_SELF"];

$maxRows_rsStaff = 10;
$pageNum_rsStaff = 0;
if (isset($_GET['pageNum_rsStaff'])) {
  $pageNum_rsStaff = $_GET['pageNum_rsStaff'];
}
$startRow_rsStaff = $pageNum_rsStaff * $maxRows_rsStaff;

mysql_select_db($database_conn_ura, $conn_ura);
$query_rsStaff = "SELECT staff.staff_id, CONCAT(staff.firstname,' ', staff.lastname) NAMES, staff.mobile, staff.email, staff.`role`,station.name dstation FROM staff JOIN station ON  station.stationcode=staff.dutystation ORDER BY staff.datemodified DESC";
$query_limit_rsStaff = sprintf("%s LIMIT %d, %d", $query_rsStaff, $startRow_rsStaff, $maxRows_rsStaff);
$rsStaff = mysql_query($query_limit_rsStaff, $conn_ura) or die(mysql_error());
$row_rsStaff = mysql_fetch_assoc($rsStaff);

if (isset($_GET['totalRows_rsStaff'])) {
  $totalRows_rsStaff = $_GET['totalRows_rsStaff'];
} else {
  $all_rsStaff = mysql_query($query_rsStaff);
  $totalRows_rsStaff = mysql_num_rows($all_rsStaff);
}
$totalPages_rsStaff = ceil($totalRows_rsStaff/$maxRows_rsStaff)-1;

$queryString_rsStaff = "";
if (!empty($_SERVER['QUERY_STRING'])) {
  $params = explode("&", $_SERVER['QUERY_STRING']);
  $newParams = array();
  foreach ($params as $param) {
    if (stristr($param, "pageNum_rsStaff") == false && 
        stristr($param, "totalRows_rsStaff") == false) {
      array_push($newParams, $param);
    }
  }
  if (count($newParams) != 0) {
    $queryString_rsStaff = "&" . htmlentities(implode("&", $newParams));
  }
}
$queryString_rsStaff = sprintf("&totalRows_rsStaff=%d%s", $totalRows_rsStaff, $queryString_rsStaff);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><!-- InstanceBegin template="/Templates/other_pages.dwt.php" codeOutsideHTMLIsLocked="false" -->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<!-- InstanceBeginEditable name="doctitle" -->
<title>URA Offences.::.View Staff</title>
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
  <h3>Staff</h3>
  <?php if ($totalRows_rsStaff == 0) { // Show if recordset empty ?>
  <p>No staff records found</p>
  <?php } // Show if recordset empty ?>
  <p><a href="add_user.php" class="small">Register new staff</a></p>
  <?php if ($totalRows_rsStaff > 0) { // Show if recordset not empty ?>
    <table width="0" border="0" id="tbl_repeat">
      <tr>
        <th scope="col">SID</th>
        <th scope="col">NAMES</th>
        <th scope="col">DUTY STATION</th>
        <th scope="col">MOBILE</th>
        <th scope="col">EMAIL</th>
        <th scope="col">ROLE</th>
      </tr>
      <?php do { ?>
        <tr>
          <td><?php echo $row_rsStaff['staff_id']; ?></td>
          <td><a href="staffDetail.php?staffId=<?php echo $row_rsStaff['staff_id']; ?>"><?php echo $row_rsStaff['NAMES']; ?></a></td>
          <td><?php echo $row_rsStaff['dstation']; ?></td>
          <td><?php echo $row_rsStaff['mobile']; ?></td>
          <td><?php echo $row_rsStaff['email']; ?></td>
          <td><?php echo $row_rsStaff['role']; ?></td>
        </tr>
        <?php } while ($row_rsStaff = mysql_fetch_assoc($rsStaff)); ?>
    </table>
    <p>&nbsp;</p>
    <p class="small"><?php echo ($startRow_rsStaff + 1) ?> - <?php echo min($startRow_rsStaff + $maxRows_rsStaff, $totalRows_rsStaff) ?> of <?php echo $totalRows_rsStaff ?> staff(users)<br />
      &nbsp;<a href="<?php printf("%s?pageNum_rsStaff=%d%s", $currentPage, 0, $queryString_rsStaff); ?>">First</a> | <a href="<?php printf("%s?pageNum_rsStaff=%d%s", $currentPage, max(0, $pageNum_rsStaff - 1), $queryString_rsStaff); ?>">Previous</a> | <a href="<?php printf("%s?pageNum_rsStaff=%d%s", $currentPage, min($totalPages_rsStaff, $pageNum_rsStaff + 1), $queryString_rsStaff); ?>">Next</a> | <a href="<?php printf("%s?pageNum_rsStaff=%d%s", $currentPage, $totalPages_rsStaff, $queryString_rsStaff); ?>">Last</a></p>
    <?php } // Show if recordset not empty ?>
  <!-- InstanceEndEditable --> </div>
  <!-- end #content -->
  <div class="clearfloat"></div>
<div id="footer"></div><!-- end #footer -->
</div><!-- end #wrapper -->
</body>
<!-- InstanceEnd --></html>
<?php
mysql_free_result($rsStaff);
?>
