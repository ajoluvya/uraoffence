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

$MM_restrictGoTo = "index.php?msg=You are not allowed to access this page, login as admin";
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

$maxRows_rsRegions = 10;
$pageNum_rsRegions = 0;
if (isset($_GET['pageNum_rsRegions'])) {
  $pageNum_rsRegions = $_GET['pageNum_rsRegions'];
}
$startRow_rsRegions = $pageNum_rsRegions * $maxRows_rsRegions;

mysql_select_db($database_conn_ura, $conn_ura);
$query_rsRegions = "SELECT region.rid, region.name FROM region ORDER BY region.name ASC";
$query_limit_rsRegions = sprintf("%s LIMIT %d, %d", $query_rsRegions, $startRow_rsRegions, $maxRows_rsRegions);
$rsRegions = mysql_query($query_limit_rsRegions, $conn_ura) or die(mysql_error());
$row_rsRegions = mysql_fetch_assoc($rsRegions);

if (isset($_GET['totalRows_rsRegions'])) {
  $totalRows_rsRegions = $_GET['totalRows_rsRegions'];
} else {
  $all_rsRegions = mysql_query($query_rsRegions);
  $totalRows_rsRegions = mysql_num_rows($all_rsRegions);
}
$totalPages_rsRegions = ceil($totalRows_rsRegions/$maxRows_rsRegions)-1;

$queryString_rsRegions = "";
if (!empty($_SERVER['QUERY_STRING'])) {
  $params = explode("&", $_SERVER['QUERY_STRING']);
  $newParams = array();
  foreach ($params as $param) {
    if (stristr($param, "pageNum_rsRegions") == false && 
        stristr($param, "totalRows_rsRegions") == false) {
      array_push($newParams, $param);
    }
  }
  if (count($newParams) != 0) {
    $queryString_rsRegions = "&" . htmlentities(implode("&", $newParams));
  }
}
$queryString_rsRegions = sprintf("&totalRows_rsRegions=%d%s", $totalRows_rsRegions, $queryString_rsRegions);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><!-- InstanceBegin template="/Templates/other_pages.dwt.php" codeOutsideHTMLIsLocked="false" -->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<!-- InstanceBeginEditable name="doctitle" -->
<title>URA Offences.::.Regions</title>
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
<script type="text/javascript">
function confirmdel(){
				  var really=confirm("Are you sure you really want to delete this region?");
				  return really;
		}
</script>
<!-- InstanceEndEditable -->
</head>

<body><div id="wrapper">
<div id="header"></div>
<div id="nav_bar"><?php echo $_SESSION['MM_Username']; ?> (<?php echo $_SESSION['user_names']; ?>) | <a href="loginsuccess.php">Home</a> | <a href="logout.php">Logout</a></div>
<div class="clearfloat"></div>
  <div id="content"><!-- InstanceBeginEditable name="formstablesreports" -->
  <h3>Regions</h3>
  <?php if ($totalRows_rsRegions == 0) { // Show if recordset empty ?>
  <p class="small">No records found</p>
  <?php } // Show if recordset empty ?>
<p class="small"><a href="add_region.php">Create new</a></p>
<?php if ($totalRows_rsRegions > 0) { // Show if recordset not empty ?>
<center>
  <table style="width:auto" width="0" border="0" id="tbl_repeat">
    <tr>
      <th class="small" scope="col">Region Code</th>
      <th class="small" scope="col">Region</th>
      <th class="small" scope="col">MODIFY</th>
    </tr>
    <?php do { ?>
      <tr>
        <td class="small"><?php echo $row_rsRegions['rid']; ?></td>
        <td class="small"><?php echo $row_rsRegions['name']; ?></td>
        <td class="small"><a id="edit" href="edit_region.php?regId=<?php echo $row_rsRegions['rid']; ?>">Edit</a> | <a href="delete.php?regId=<?php echo $row_rsRegions['rid']; ?>" onclick="return confirmdel()">Delete</a></td>
      </tr>
      <?php } while ($row_rsRegions = mysql_fetch_assoc($rsRegions)); ?>
  </table>
  </center>
  <p class="small"><?php echo ($startRow_rsRegions + 1) ?> - <?php echo min($startRow_rsRegions + $maxRows_rsRegions, $totalRows_rsRegions) ?> of <?php echo $totalRows_rsRegions ?> regions</p>
  <p class="small">&nbsp;<a href="<?php printf("%s?pageNum_rsRegions=%d%s", $currentPage, 0, $queryString_rsRegions); ?>">First</a> | <a href="<?php printf("%s?pageNum_rsRegions=%d%s", $currentPage, max(0, $pageNum_rsRegions - 1), $queryString_rsRegions); ?>">Previous</a> | <a href="<?php printf("%s?pageNum_rsRegions=%d%s", $currentPage, min($totalPages_rsRegions, $pageNum_rsRegions + 1), $queryString_rsRegions); ?>">Next</a> | <a href="<?php printf("%s?pageNum_rsRegions=%d%s", $currentPage, $totalPages_rsRegions, $queryString_rsRegions); ?>">Last</a></p>
  <?php } // Show if recordset not empty ?>
  <!-- InstanceEndEditable --> </div>
  <!-- end #content -->
  <div class="clearfloat"></div>
<div id="footer"></div><!-- end #footer -->
</div><!-- end #wrapper -->
</body>
<!-- InstanceEnd --></html>
<?php
mysql_free_result($rsRegions);
?>
