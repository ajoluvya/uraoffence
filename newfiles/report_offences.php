<?php require_once('Connections/conn_ura.php'); ?>
<?php
if (!isset($_SESSION)) {
  session_start();
}
$MM_authorizedUsers = "Officer,Manager,Ass Comm,In Charge,Regional Supervisor";
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

$MM_restrictGoTo = "index.php?msg=You are not authorized to view this page, contact the admin";
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
mysql_select_db($database_conn_ura, $conn_ura);

$query_rsReport = "SELECT * FROM loginout";
$rsReport = mysql_query($query_rsReport, $conn_ura) or die(mysql_error());
$row_rsReport = mysql_fetch_assoc($rsReport);
$totalRows_rsReport = mysql_num_rows($rsReport);

$maxRows_offence_recds = 10;
$pageNum_offence_recds = 0;
if (isset($_GET['pageNum_offence_recds'])) {
  $pageNum_offence_recds = $_GET['pageNum_offence_recds'];
}
$startRow_offence_recds = $pageNum_offence_recds * $maxRows_offence_recds;


mysql_select_db($database_conn_ura, $conn_ura);
    mysql_set_charset('utf8');
    mysql_query('SET NAMES UTF-8');
$period=" AND MONTH(tbl_offence.rep_date)=MONTH(CURDATE())";
if(isset($_GET['month'])&&strlen($_GET['month'])>0)
$period=" AND MONTH(tbl_offence.rep_date)=".$_GET['month'];
if(isset($_GET['date1'])&&isset($_GET['date2'])){
	$datePortion="BETWEEN '".$_GET['date1']."' AND  '".$_GET['date2']."'";
	$period="AND tbl_offence.rep_date $datePortion";
}
$orderBy="$period ORDER BY tbl_offence.rep_date DESC";
$query_offence_recds = "SELECT * FROM tbl_offence WHERE stationcode='".$_SESSION['StationCode']."'";
	
	
if ($_SESSION['MM_UserGroup']=="Regional Supervisor") {
  $UGBST_rsSup = $_SESSION['StationCode'];
$query_offence_recds = sprintf("SELECT * FROM tbl_offence  WHERE stationcode IN (SELECT stationcode FROM station WHERE region=(SELECT region FROM station WHERE stationcode = %s)) ", GetSQLValueString($UGBST_rsSup, "text"));
}
if ($_SESSION['MM_UserGroup']=="Ass Comm"||$_SESSION['MM_UserGroup']=="Manager")
$query_offence_recds = "SELECT * FROM tbl_offence";

if(isset($_GET['frmsch']) && $_GET['frmsch']!='' ){
	$srch=$_GET['frmsch'];
	$query_offence_recds .= " AND (tbl_offence.file_no LIKE '%$srch%' OR tbl_offence.entry_no LIKE '%$srch%' OR tbl_offence.offender_names LIKE '%$srch%' OR tbl_offence.nature_offence LIKE '%$srch%' OR tbl_offence.sect_law LIKE '%$srch%' OR '%$srch%' OR tbl_offence.det_method LIKE '%$srch%' OR tbl_offence.remarks LIKE '%$srch%')".$orderBy; 
}
else
$query_offence_recds .=$orderBy;

$query_limit_offence_recds = sprintf("%s LIMIT %d, %d", $query_offence_recds, $startRow_offence_recds, $maxRows_offence_recds);
$offence_recds = mysql_query($query_limit_offence_recds, $conn_ura) or die(mysql_error());
$row_offence_recds = mysql_fetch_assoc($offence_recds);

if (isset($_GET['totalRows_offence_recds'])) {
  $totalRows_offence_recds = $_GET['totalRows_offence_recds'];
} else {
  $all_offence_recds = mysql_query($query_offence_recds);
  $totalRows_offence_recds = mysql_num_rows($all_offence_recds);
}
$totalPages_offence_recds = ceil($totalRows_offence_recds/$maxRows_offence_recds)-1;


$queryString_offence_recds = "";
if (!empty($_SERVER['QUERY_STRING'])) {
  $params = explode("&", $_SERVER['QUERY_STRING']);
  $newParams = array();
  foreach ($params as $param) {
    if (stristr($param, "pageNum_offence_recds") == false && 
        stristr($param, "totalRows_offence_recds") == false) {
      array_push($newParams, $param);
    }
  }
  if (count($newParams) != 0) {
    $queryString_offence_recds = "&" . htmlentities(implode("&", $newParams));
  }
}
$queryString_offence_recds = sprintf("&totalRows_offence_recds=%d%s", $totalRows_offence_recds, $queryString_offence_recds);
if(isset($_POST['fwdToCWH']) and $_POST['fwdToCWH']=="form2") {
// you can optimize below into a single query, but let's keep it simple and clear for now:
foreach($_POST['cwh_ids'] as $id) {
$updateSQL = "UPDATE tbl_offence SET handedCWH=1 WHERE off_id=$id";
  mysql_select_db($database_conn_ura, $conn_ura);
  $Result1 = mysql_query($updateSQL, $conn_ura) or die(mysql_error());
}
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><!-- InstanceBegin template="/Templates/other_pages.dwt.php" codeOutsideHTMLIsLocked="false" -->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<!-- InstanceBeginEditable name="doctitle" -->
<title>URA Offences.::.Offences Report</title>
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
<script type="text/javascript" src="SpryAssets/jquery.min.js"></script>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>
<script type="text/javascript" src="SpryAssets/functions.js"></script>
<!--<link type="text/css" href="css/jquery.datepick.css" rel="stylesheet" />
<script type="text/javascript" src="SpryAssets/jquery.datepick.js"></script>-->
<script type="text/javascript">
/*$(function() {
	$('#date1').datepick();
	$('#date2').datepick();
});*/
function confirmdel(){
				  var really=confirm("Are you sure you really want to delete this record?");
				  return really;
		}
</script>
<style>
#content{
	width:90%;
}
</style>
<!-- InstanceEndEditable -->
</head>

<body><div id="wrapper">
<div id="header"></div>
<div id="nav_bar"><?php echo $_SESSION['MM_Username']; ?> (<?php echo $_SESSION['user_names']; ?>) | <a href="loginsuccess.php">Home</a> | <a href="logout.php">Logout</a></div>
<div class="clearfloat"></div>
  <div id="content"><!-- InstanceBeginEditable name="formstablesreports" -->
  <h2>Tax Offences</h2>
<center>
  <form id="frm_insert" name="frm_insert" method="get" action="">
    <table cellspacing="0" cellpadding="0">
      <tr>
        <th scope="row"><label for="frmsch3">Search for:</label></th>
        <th scope="row">&nbsp;</th>
        <th scope="row"><input name="frmsch" type="text" class="frm_fld" id="frmsch" value="<?php echo (isset($_GET['frmsch']))?$_GET['frmsch']:"";?>"/></th>
        <td>&nbsp;</td>
      </tr>
      <tr>
        <th scope="row"><label for="month2">Month:</label></th>
        <th scope="row"><input name="filter" type="radio" id="ftl" value="ftl" checked="checked" /></th>
        <td scope="row"><select name="month" id="month">
          <option value="1" <?php if (!(strcmp(1, isset($_GET['month'])?$_GET['month']:date('n')))) {echo "selected=\"selected\"";} ?>>January</option>
          <option value="2" <?php if (!(strcmp(2, isset($_GET['month'])?$_GET['month']:date('n')))) {echo "selected=\"selected\"";} ?>>February</option>
          <option value="3" <?php if (!(strcmp(3, isset($_GET['month'])?$_GET['month']:date('n')))) {echo "selected=\"selected\"";} ?>>March</option>
          <option value="4" <?php if (!(strcmp(4, isset($_GET['month'])?$_GET['month']:date('n')))) {echo "selected=\"selected\"";} ?>>April</option>
          <option value="5" <?php if (!(strcmp(5, isset($_GET['month'])?$_GET['month']:date('n')))) {echo "selected=\"selected\"";} ?>>May</option>
          <option value="6" <?php if (!(strcmp(6, isset($_GET['month'])?$_GET['month']:date('n')))) {echo "selected=\"selected\"";} ?>>June</option>
          <option value="7" <?php if (!(strcmp(7, isset($_GET['month'])?$_GET['month']:date('n')))) {echo "selected=\"selected\"";} ?>>July</option>
          <option value="8" <?php if (!(strcmp(8, isset($_GET['month'])?$_GET['month']:date('n')))) {echo "selected=\"selected\"";} ?>>August</option>
          <option value="9" <?php if (!(strcmp(9, isset($_GET['month'])?$_GET['month']:date('n')))) {echo "selected=\"selected\"";} ?>>September</option>
          <option value="10" <?php if (!(strcmp(10, isset($_GET['month'])?$_GET['month']:date('n')))) {echo "selected=\"selected\"";} ?>>October</option>
          <option value="11" <?php if (!(strcmp(11, isset($_GET['month'])?$_GET['month']:date('n')))) {echo "selected=\"selected\"";} ?>>November</option>
          <option value="12" <?php if (!(strcmp(12, isset($_GET['month'])?$_GET['month']:date('n')))) {echo "selected=\"selected\"";} ?>>December</option>
        </select></td>
        <td>&nbsp;</td>
        </tr>
      <tr>
        <th scope="row">From:</th>
        <th scope="row"><input type="radio" name="filter" id="dtr" value="dtr" /></th>
        <th scope="row"><span id="sprydate1">
        <input name="date1" type="text" disabled="disabled" id="date1" value="<?php if(isset($_GET['date1'])&&strlen($_GET['date1'])>0)echo $_GET['date1'];?>" />
        <span class="textfieldInvalidFormatMsg">Invalid format.</span><span class="textfieldRequiredMsg">date is required.</span></span>
        </th>
        <td><span id="sprydate2">
          <label for="date2">To:</label>
          <input name="date2" type="text" disabled="disabled" id="date2" value="<?php if(isset($_GET['date2'])&&strlen($_GET['date2'])>0) echo $_GET['date2'];?>" />
          <span class="textfieldInvalidFormatMsg">Invalid format.</span><span class="textfieldRequiredMsg">date is required.</span></span>
          </td>
        </tr>
      <tr>
        <th scope="row">&nbsp;</th>
        <th scope="row">&nbsp;</th>
        <th scope="row"><input type="submit" name="btn" id="btn" value="SEARCH" /></th>
        <td>&nbsp;</td>
        </tr>
    </table>
    <p>&nbsp;</p>
  </form>
 
<?php if ($totalRows_offence_recds == 0) { // Show if recordset empty
      if(isset($_GET['frmsch'])) {?><p class="small">Sorry there are no records that match your search, try again</p><?php } else {?>
      <p class="small">No records to display</p>
      <?php } } // Show if recordset empty ?>
      <p class="small"><?php if($_SESSION['MM_UserGroup']=="Officer"){?><a href="add_offence.php">Add another</a> |<?php if(isset($_GET['frmsch']) && $_GET['frmsch']!='' ){?>
      <?php } ?><?php }?>
      <a href="report_offences.php"> All offences</a>
      </p>
    <?php if ($totalRows_offence_recds > 0) { // Show if recordset not empty ?>
   <button id="print_btn" onclick="window.print()" title="Print"></button>
  
  <form action="report_offences.php" method="post" id="form2">
  <table width="0" border="0" cellspacing="0" id="tbl_repeat">
    <tr>
      <th scope="col">S/N</th>
      <th scope="col">DATE</th>
      <th scope="col">ENTRY N0</th>
      <th scope="col">OFFENCE NUMBER</th>
      <th scope="col">OFFENDER'S NAMES</th>
      <th scope="col">NATURE OF OFFENCE</th>
      <th scope="col">SECTION OF THE LAW</th>
      <th scope="col">DESCRIPTION OF GOODS</th>
      <th scope="col">METHOD OF DETECTION</th>
      <th scope="col">VALUE OF GOODS [$]</th>
      <th scope="col">TAXES</th>
      <th scope="col">FINES</th>
      <th scope="col">TOTAL</th>
      <th scope="col">REMARK[S]</th>
      <?php if($_SESSION['MM_UserGroup']=="Officer"||$_SESSION['MM_UserGroup']=="In Charge"){?>
      <th scope="col">MODIFY</th>
      <th>Forward to Customs</th>
	  <?php }?>
    </tr>
    <?php //Now that we've created such a nice heading for our html table, lets create a heading for our csv table
    $csv_hdr = "DATE, ENTRY N0, OFFENCE NUMBER, OFFENDER's NAMES, NATURE OF OFFENCE, SECTION OF THE LAW, DESCRIPTION OF GOODS, METHOD OF DETECTION, VALUE OF GOODS [$], TAXES, FINES, TOTAL, REMARK[S]";
//Quickly create a variable for our output that'll go into the CSV file (we'll make it blank to start).
    $csv_output="";
do { 

mysql_select_db($database_conn_ura, $conn_ura);
$query_rsCapGoods = "SELECT tbl_capturedgoods.good_name, tbl_capturedgoods.good_descpn, tbl_capturedgoods.goods_val, tbl_capturedgoods.taxes FROM tbl_capturedgoods WHERE tbl_capturedgoods.off_id=".$row_offence_recds['off_id']."";
$rsCapGoods = mysql_query($query_rsCapGoods, $conn_ura) or die(mysql_error());
$row_rsCapGoods = mysql_fetch_assoc($rsCapGoods);
$totalRows_rsCapGoods = mysql_num_rows($rsCapGoods);
$descGood=""; $goodsVal=0; $taxes=0;
$descGood1="";
do {
	$descGood.=$row_rsCapGoods['good_name']."  ";
	$descGood1.=$row_rsCapGoods['good_name']."-".$row_rsCapGoods['good_descpn']."  ";
	$goodsVal+=$row_rsCapGoods['goods_val'];
	$taxes+=$row_rsCapGoods['taxes'];
}while($row_rsCapGoods = mysql_fetch_assoc($rsCapGoods));
?>
      <tr>
        <td><a href="detail_off.php?offId=<?php echo $row_offence_recds['off_id']; ?>"><?php echo $row_offence_recds['off_id']; ?></a></td>
        <td><?php echo $row_offence_recds['rep_date']; $csv_output .= $row_offence_recds['rep_date'] . ", ";?></td>
        <td><?php echo $row_offence_recds['entry_no']; $csv_output .= $row_offence_recds['entry_no'] . ", ";?></td>
        <td><?php echo $row_offence_recds['file_no']; $csv_output .= $row_offence_recds['file_no'] . ", ";?></td>
        <td><?php echo $row_offence_recds['offender_names']; $csv_output .= $row_offence_recds['offender_names'] . ", ";?></td>
        <td><?php echo $row_offence_recds['nature_offence']; $csv_output .= $row_offence_recds['nature_offence'] . ", ";?></td>
        <td><?php echo $row_offence_recds['sect_law']; $csv_output .= $row_offence_recds['sect_law'] . ", ";?></td>
        <td><?php echo $descGood; $csv_output .=$descGood1 . ", ";?></td>
        <td><?php echo $row_offence_recds['det_method']; $csv_output .= $row_offence_recds['det_method'] . ", ";?></td>
        <td><?php echo $goodsVal; $csv_output .= $goodsVal . ", ";?></td>
        <td><?php echo $taxes; $csv_output .= $taxes . ", ";?></td>
        <td><?php echo $row_offence_recds['fines']; $csv_output .= $row_offence_recds['fines'] . ", ";?></td>
        <td><?php echo $row_offence_recds['fines']+$taxes; $csv_output .= $row_offence_recds['fines']+$taxes . ", ";?></td>
        <td><?php echo $row_offence_recds['remarks']; $csv_output .= $row_offence_recds['remarks'] . "\n";?></td>
        <?php if($_SESSION['MM_UserGroup']=="Officer"||$_SESSION['MM_UserGroup']=="In Charge"){?> 
        <td><?php if($_SESSION['MM_UserGroup']=="In Charge"){?><a href="delete.php?offId=<?php echo $row_offence_recds['off_id']; ?>" onclick="return confirmdel()">Delete</a><?php }?> <a href="edit_offence.php?offId=<?php echo $row_offence_recds['off_id']; ?>">Edit</a></td>
        <td><input type="checkbox" name="cwh_ids[]" value="<?php echo $row_offence_recds['off_id']; ?>"></td>
		<?php }?>
      </tr>
      <?php } while ($row_offence_recds = mysql_fetch_assoc($offence_recds)); if($_SESSION['MM_UserGroup']=="Officer"||$_SESSION['MM_UserGroup']=="In Charge"){?>
      <tr>
      <td colspan="16" style="text-align:right">
      <input name="fwdToCWH" type="hidden" value="form2" />
      <input type="submit" class="submissions" value="Forward" />
      </td>
      </tr><?php }?>
  </table>
  </form>
<form name="export" action="export.php" method="post">
    <input type="submit" value="Download Excel File" />
    <input type="hidden" value="<?php echo $csv_hdr; ?>" name="csv_hdr" />
    <input type="hidden" value="<?php echo $csv_output; ?>" name="csv_output" />
</form>
  <?php
mysql_free_result($rsCapGoods); ?>
  </center>
    <p class="small">&nbsp;</p>
    <p class="small"><?php echo ($startRow_offence_recds + 1) ?> - <?php echo min($startRow_offence_recds + $maxRows_offence_recds, $totalRows_offence_recds) ?> of <?php echo $totalRows_offence_recds ?> record(s)</p>
  <p class="small">
      <a href="<?php printf("%s?pageNum_offence_recds=%d%s", $currentPage, 0, $queryString_offence_recds); ?>">First</a>|<a href="<?php printf("%s?pageNum_offence_recds=%d%s", $currentPage, max(0, $pageNum_offence_recds - 1), $queryString_offence_recds); ?>">Previous</a>|<a href="<?php printf("%s?pageNum_offence_recds=%d%s", $currentPage, min($totalPages_offence_recds, $pageNum_offence_recds + 1), $queryString_offence_recds); ?>">Next</a>|<a href="<?php printf("%s?pageNum_offence_recds=%d%s", $currentPage, $totalPages_offence_recds, $queryString_offence_recds); ?>">Last</a>
      <?php } // Show if recordset not empty ?>
  </p>
  <script type="text/javascript">
var sprytextfield1 = new Spry.Widget.ValidationTextField("sprydate2", "date", {format:"yyyy-mm-dd", useCharacterMasking:true, validateOn:["blur"], hint:"Eg: 2014-12-31", isRequired:false});
var sprytextfield2 = new Spry.Widget.ValidationTextField("sprydate1", "date", {validateOn:["blur"], format:"yyyy-mm-dd", hint:"Eg: 2010-01-31", useCharacterMasking:true, isRequired:false});
  </script>
  <!-- InstanceEndEditable --> </div>
  <!-- end #content -->
  <div class="clearfloat"></div>
<div id="footer"></div><!-- end #footer -->
</div><!-- end #wrapper -->
</body>
<!-- InstanceEnd --></html>
<?php

mysql_free_result($rsReport);

mysql_free_result($offence_recds);
?>
