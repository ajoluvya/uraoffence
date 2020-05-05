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

$MM_restrictGoTo = "index.php?msg=You do not have access to this page, please login as an admin";
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

$maxRows_rsStations = 10;
$pageNum_rsStations = 0;
if (isset($_GET['pageNum_rsStations'])) {
  $pageNum_rsStations = $_GET['pageNum_rsStations'];
}
$startRow_rsStations = $pageNum_rsStations * $maxRows_rsStations;

mysql_select_db($database_conn_ura, $conn_ura);
$query_rsStations = "SELECT station.stationcode, station.name STN, region.name RGN FROM station JOIN region ON station.region= region.rid";
$query_limit_rsStations = sprintf("%s LIMIT %d, %d", $query_rsStations, $startRow_rsStations, $maxRows_rsStations);
$rsStations = mysql_query($query_limit_rsStations, $conn_ura) or die(mysql_error());
$row_rsStations = mysql_fetch_assoc($rsStations);

if (isset($_GET['totalRows_rsStations'])) {
  $totalRows_rsStations = $_GET['totalRows_rsStations'];
} else {
  $all_rsStations = mysql_query($query_rsStations);
  $totalRows_rsStations = mysql_num_rows($all_rsStations);
}
$totalPages_rsStations = ceil($totalRows_rsStations/$maxRows_rsStations)-1;

$queryString_rsStations = "";
if (!empty($_SERVER['QUERY_STRING'])) {
  $params = explode("&", $_SERVER['QUERY_STRING']);
  $newParams = array();
  foreach ($params as $param) {
    if (stristr($param, "pageNum_rsStations") == false && 
        stristr($param, "totalRows_rsStations") == false) {
      array_push($newParams, $param);
    }
  }
  if (count($newParams) != 0) {
    $queryString_rsStations = "&" . htmlentities(implode("&", $newParams));
  }
}
$queryString_rsStations = sprintf("&totalRows_rsStations=%d%s", $totalRows_rsStations, $queryString_rsStations);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><!-- InstanceBegin template="/Templates/other_pages.dwt.php" codeOutsideHTMLIsLocked="false" -->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<!-- InstanceBeginEditable name="doctitle" -->
<title>URA Offences.::.Workstations</title>
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
				  var really=confirm("Are you sure you really want to delete this station?");
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
<p><a href="add_wstation.php" class="small">Add new</a></p>
  <h3>Stations</h3>
  <?php if ($totalRows_rsStations == 0) { // Show if recordset empty ?>
  <p>No records found</p>
  <?php } // Show if recordset empty ?>
<?php if ($totalRows_rsStations > 0) { // Show if recordset not empty ?>
  <center>
  <table style="width:auto" width="0" border="0" id="tbl_repeat">
    <tr>
      <th scope="col">CODE</th>
      <th scope="col">STATION</th>
      <th scope="col">REGION</th>
      <th scope="col">MODIFY</th>
    </tr>
    <?php do { ?>
      <tr>
        <td><?php echo $row_rsStations['stationcode']; ?></td>
        <td><?php echo $row_rsStations['STN']; ?></td>
        <td><?php echo $row_rsStations['RGN']; ?></td>
        <td><a id="edit" href="edit_station.php?stationcode=<?php echo $row_rsStations['stationcode']; ?>">Edit</a> | <a href="delete.php?stationcode=<?php echo $row_rsStations['stationcode']; ?>" onclick="return confirmdel()">Delete</a></td>
      </tr>
      <?php } while ($row_rsStations = mysql_fetch_assoc($rsStations)); ?>
  </table>
  </center>
  <p class="small"><?php echo ($startRow_rsStations + 1) ?> - <?php echo min($startRow_rsStations + $maxRows_rsStations, $totalRows_rsStations) ?> of <?php echo $totalRows_rsStations ?> records</p>
  <p class="small">&nbsp;<a href="<?php printf("%s?pageNum_rsStations=%d%s", $currentPage, 0, $queryString_rsStations); ?>">First</a> | <a href="<?php printf("%s?pageNum_rsStations=%d%s", $currentPage, max(0, $pageNum_rsStations - 1), $queryString_rsStations); ?>">Previous</a> | <a href="<?php printf("%s?pageNum_rsStations=%d%s", $currentPage, min($totalPages_rsStations, $pageNum_rsStations + 1), $queryString_rsStations); ?>">Next</a> | <a href="<?php printf("%s?pageNum_rsStations=%d%s", $currentPage, $totalPages_rsStations, $queryString_rsStations); ?>">Last</a></p>
  <?php } // Show if recordset not empty ?>
  <!-- InstanceEndEditable --> </div>
  <!-- end #content -->
  <div class="clearfloat"></div>
<div id="footer"></div><!-- end #footer -->
</div><!-- end #wrapper -->
</body>
<!-- InstanceEnd --></html>
<?php
mysql_free_result($rsStations);
?>
