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

$MM_restrictGoTo = "index.php?msg=Access denied, contact admin";
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


mysql_select_db($database_conn_ura, $conn_ura);
$query_rsRegions = "SELECT region.name, region.rid FROM region";
if($_SESSION['MM_UserGroup']=="Officer"||$_SESSION['MM_UserGroup']=="In Charge"||$_SESSION['MM_UserGroup']=="Regional Supervisor")
$query_rsRegions = "SELECT region.name, region.rid FROM region WHERE region.rid=(SELECT region FROM station WHERE station.stationcode='".$_SESSION['StationCode']."')";

$rsRegions = mysql_query($query_rsRegions, $conn_ura) or die(mysql_error());
$row_rsRegions = mysql_fetch_assoc($rsRegions);
$totalRows_rsRegions = mysql_num_rows($rsRegions);

$monthCheck=" AND MONTHNAME(tbl_offence.rep_date)=MONTHNAME(CURDATE())";
$curmonth=" AND MONTHNAME(daterecorded)=MONTHNAME(CURDATE())";

if(isset($_POST['rep_month'])&&strlen($_POST['rep_month'])>0){
	$monthCheck=" AND MONTHNAME(tbl_offence.rep_date)='".$_POST['rep_month']."'";
	$curmonth=" AND MONTHNAME(daterecorded)='".$_POST['rep_month']."'";
}
if(isset($_POST['date1'])&&isset($_POST['date2'])){
	$datePortion="BETWEEN '".$_POST['date1']."' AND  '".$_POST['date2']."'";
	$monthCheck="AND tbl_offence.rep_date $datePortion";
	$curmonth="AND daterecorded $datePortion";
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><!-- InstanceBegin template="/Templates/other_pages.dwt.php" codeOutsideHTMLIsLocked="false" -->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<!-- InstanceBeginEditable name="doctitle" -->
<title>URA Offences.::.Reports</title>
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
<script src="SpryAssets/functions.js" type="text/javascript"></script>
<style>
#content{
	width:98%;
}
</style>
<!-- InstanceEndEditable -->
</head>

<body><div id="wrapper">
<div id="header"></div>
<div id="nav_bar"><?php echo $_SESSION['MM_Username']; ?> (<?php echo $_SESSION['user_names']; ?>) | <a href="loginsuccess.php">Home</a> | <a href="logout.php">Logout</a></div>
<div class="clearfloat"></div>
  <div id="content"><!-- InstanceBeginEditable name="formstablesreports" -->
    <div>
    <h1>REPORTS</h1>
    <center>
    <form id="frm_insert" name="frm_insert" method="post" action="">
      <table cellspacing="0" cellpadding="0">
        <tr>
          <th scope="row"><label for="rep_month">Month:
            <input name="filter" type="radio" id="ftl" value="ftl" checked="checked" />
          </label></th>
          <td><select name="rep_month" id="rep_month">
            <option value="January" <?php if (!(strcmp("Jan", isset($_POST['rep_month'])?substr($_POST['rep_month'],0,3):date('M')))) {echo "selected=\"selected\"";} ?>>January</option>
              <option value="February" <?php if (!(strcmp("Feb", isset($_POST['rep_month'])?substr($_POST['rep_month'],0,3):date('M')))) {echo "selected=\"selected\"";} ?>>February</option>
              <option value="March" <?php if (!(strcmp("Mar", isset($_POST['rep_month'])?substr($_POST['rep_month'],0,3):date('M')))) {echo "selected=\"selected\"";} ?>>March</option>
              <option value="April" <?php if (!(strcmp("Apr", isset($_POST['rep_month'])?substr($_POST['rep_month'],0,3):date('M')))) {echo "selected=\"selected\"";} ?>>April</option>
              <option value="May" <?php if (!(strcmp("May", isset($_POST['rep_month'])?substr($_POST['rep_month'],0,3):date('M')))) {echo "selected=\"selected\"";} ?>>May</option>
              <option value="June" <?php if (!(strcmp("Jun", isset($_POST['rep_month'])?substr($_POST['rep_month'],0,3):date('M')))) {echo "selected=\"selected\"";} ?>>June</option>
              <option value="July" <?php if (!(strcmp("Jul", isset($_POST['rep_month'])?substr($_POST['rep_month'],0,3):date('M')))) {echo "selected=\"selected\"";} ?>>July</option>
              <option value="August" <?php if (!(strcmp("Aug", isset($_POST['rep_month'])?substr($_POST['rep_month'],0,3):date('M')))) {echo "selected=\"selected\"";} ?>>August</option>
              <option value="September" <?php if (!(strcmp("Sep", isset($_POST['rep_month'])?substr($_POST['rep_month'],0,3):date('M')))) {echo "selected=\"selected\"";} ?>>September</option>
              <option value="October" <?php if (!(strcmp("Oct", isset($_POST['rep_month'])?substr($_POST['rep_month'],0,3):date('M')))) {echo "selected=\"selected\"";} ?>>October</option>
              <option value="November" <?php if (!(strcmp("Nov", isset($_POST['rep_month'])?substr($_POST['rep_month'],0,3):date('M')))) {echo "selected=\"selected\"";} ?>>November</option>
              <option value="December" <?php if (!(strcmp("Dec", isset($_POST['rep_month'])?substr($_POST['rep_month'],0,3):date('M')))) {echo "selected=\"selected\"";} ?>>December</option>
          </select></td>
          <td>&nbsp;</td>
          </tr>
        <tr>
          <td>From:
            <input type="radio" name="filter" id="dtr" value="dtr" /></td>
          <th scope="row"><span id="sprydate1">
            <input name="date1" type="text" disabled="disabled" id="date1" value="<?php if(isset($_POST['date1'])&&strlen($_POST['date1'])>0)echo $_POST['date1'];?>" />
            <span class="textfieldInvalidFormatMsg">Invalid format.</span><span class="textfieldRequiredMsg">date is required.</span></span></th>
          <th scope="row"><span id="sprydate2">
            <label for="date2">To:</label>
            <input name="date2" type="text" disabled="disabled" id="date2" value="<?php if(isset($_POST['date2'])&&strlen($_POST['date2'])>0) echo $_POST['date2'];?>" />
            <span class="textfieldInvalidFormatMsg">Invalid format.</span><span class="textfieldRequiredMsg">date is required.</span></span></th>
          </tr>
        <tr>
          <th scope="row">&nbsp;</th>
          <td><input type="submit" name="btn" id="btn" value="show" /></td>
          <td>&nbsp;</td>
          </tr>
      </table>
      <p>&nbsp;</p>
    </form>
    </center>
    <p>
    <a href="report_all.php#eom">End of Month</a> | <a href="#performanceByIntelligence">Intelligence Alerts</a> |<a href="#interceptedGoods">Other Intercepted Goods</a> |<a href="#goodsHandedToCustomsWH">Customs WH</a> | <a href="#impoundedVehiclesAtStation">Impounded Vehicles</a> <?php if($_SESSION['MM_UserGroup']!="Officer"&&$_SESSION['MM_UserGroup']!="In Charge"){?>| <a href="#performancePerStation">Performance per Station</a>| <a href="#percentageContribution">%AGE Contribution</a>| <a href="#performanceByNatureofOffence">Performance by nature of offence</a>|<a href="#topTenOffenders">Top 10 offenders</a> | <a href="#topTenRiskySmuggledItems">Top 10 risky smuggled Items</a> | <a href="#overallOperationsSummary">Operations Summary</a><?php }?></p>
    </div>
  <h2><a name="eom" id="eom"></a>OFFENCE REPORT	[<?php if(isset($_POST['rep_month']))
  $eom=$_POST['rep_month'].", ".date('Y');
  elseif(isset($_POST['date1'])&&isset($_POST['date2']))
  $eom=$_POST['date1']." - ".$_POST['date2'];
  else
  $eom=date('M, Y'); echo $eom; ?> ]</h2>
  <p>&nbsp;</p>
  <table cellpadding="1" border="1" cellspacing="0" class="tbl_report">
    <tr>
      <th scope="col">REGION</th>
      <th scope="col">S/N</th>
      <th scope="col">STATION</th>
      <th scope="col">No. OF SEIZURES ON NON DUTABLE GOODS</th>
      <th scope="col">No. OF SEIZURES ISSUED ON DUTABLE GOODS</th>
      <th scope="col">TOTAL FRAUD VALUE ON SEIZED NON DUTABLE ITEMS [<span class="required">USD</span>]</th>
      <th scope="col">TOTAL FRAUD VALUE ON SEIZED DUTABLE ITEMS[<span class="required">USD</span>]</th>
      <th scope="col">TOTAL RECOVERY</th>
      <th scope="col"> <p>OUT<br />RIGHT<br/>SMUGGLING</p></th>
      <th scope="col">% AGE</th>
      <th scope="col">UNDER<br />VALUATION</th>
      <th scope="col">% AGE</th>
      <th scope="col">MISDE<br/>CLARATION</th>
      <th scope="col">% AGE</th>
      <th scope="col">CONCE<br/>ALMENT</th>
      <th scope="col">% AGE</th>
      <th scope="col">MIS<br/>CLASSI<br/>FICATION</th>
      <th scope="col">%AGE</th>
      <th scope="col">OTHER<br/>OFFENCES</th>
      <th scope="col">%AGE</th>
    </tr>
      <?php 
	  //for the excel file, this is needed
	  $csv_hdr= "END OF MONTH OFFENCE REPORT	[$eom]\nREGION, S/N, STATION, No. OF SEIZURES ON NON DUTABLE GOODS, No. OF SEIZURES ISSUED ON DUTABLE GOODS, TOTAL FRAUD VALUE ON SEIZED NON DUTABLE ITEMS [USD], TOTAL FRAUD VALUE ON SEIZED DUTABLE ITEMS[USD], TOTAL RECOVERY, OUTRIGHT SMUGGLING, % AGE, UNDER VALUATION, % AGE, MISDECLARATION, % AGE, CONCEALMENT, % AGE, MISCLASSIFICATION, % AGE, OTHER OFFENCES, % AGE";
	  $csv_output=$csv_table2=$csv_table4=$csv_table5=$csv_table6=$csv_table7=$csv_table8=$csv_table9=$csv_table10=$csv_table11="";
	  //Variables for the respective station sums
	$tot1a=$tot2a=$tot3a=$tot4a=$tot5a=$tot6a=$tot7a=$tot8a=$tot9a=$tot10a=$tot11a=$totTaxes=$totFines=0;
	$sn=1;
$table6=$table4=$table5="";
	//array of colors for the respective stations
	$row_colors=array('EASTERN'=>"#D89898",'SCANNER'=>"#75933D",'NORTHERN'=>"#C3D897",'SOUTH WESTERN'=>"#B4E0E9",'MARINE'=>"#B3C7BC",'ENTEBBE'=>"#96B2D7",'KAMPALA'=>"#FF0");
	//VARIABLES FOR TABLE 2
	$intUnits=$intSeizs=$intTaxes=0;
	$enfUnits=$enfSeizs=$enfTaxes=0;
	$scanUnits=$scanSeizs=$scanTaxes=0;
	$tmuUnits=$tmuSeizs=$tmuTaxes=0;
	$otherUnits=$otherSeizs=$otherTaxes=0;
	
	//Variables holding values for TABLE 3
	$pharmaQty=$pharmaVal=$pharmaTax=0;
	$narcoticsQty=$narcoticsVal=$narcoticsTax=0;
	$prohibitedQty=$prohibitedVal=$prohibitedTax=0;
	$restrictedQty=$restrictedVal=$restrictedTax=0;
	$counterfeitQty=$counterfeitVal=$counterfeitTax=0;
	
	?>
    <?php do { ?>
    <?php //Variables for the respective station totals
	$tot1=$tot2=$tot3=$tot4=$tot5=$tot6=$tot7=$tot8=$tot9=$tot10=$tot11=0;
$regionId=$row_rsRegions['rid'];
	 		$dol_val=2600;
			$regionname=$row_rsRegions['name'];
mysql_select_db($database_conn_ura, $conn_ura);
$query_rsStns = "SELECT station.name, station.stationcode FROM station WHERE station.region='$regionId'";
if($_SESSION['MM_UserGroup']=="Officer"||$_SESSION['MM_UserGroup']=="In Charge")
$query_rsStns = "SELECT station.name, station.stationcode FROM station WHERE station.stationcode='".$_SESSION['StationCode']."'";
$rsStns = mysql_query($query_rsStns, $conn_ura) or die(mysql_error());
$row_rsStns = mysql_fetch_assoc($rsStns);
$totalRows_rsStns = mysql_num_rows($rsStns);

	?>
    <?php
			 do { 
$stncode=$row_rsStns['stationcode'];
mysql_select_db($database_conn_ura, $conn_ura);
$query_rsDutable = "SELECT COUNT(dutable) dtbl FROM tbl_offence WHERE tbl_offence.stationcode='$stncode' AND tbl_offence.dutable=1 $monthCheck";
$rsDutable = mysql_query($query_rsDutable, $conn_ura) or die(mysql_error());
$row_rsDutable = mysql_fetch_assoc($rsDutable);

$query_rsDtblGdsVal = "SELECT SUM(t.goods_val) val FROM (SELECT tbl_capturedgoods.goods_val FROM tbl_offence JOIN tbl_capturedgoods ON tbl_offence.off_id=tbl_capturedgoods.off_id WHERE tbl_offence.stationcode='$stncode' AND tbl_offence.dutable=1 $monthCheck) t";
$rsDtblGdsVal = mysql_query($query_rsDtblGdsVal, $conn_ura) or die(mysql_error());
$row_rsDtblGdsVal = mysql_fetch_assoc($rsDtblGdsVal);

$query_rsNdtbl = "SELECT COUNT(dutable) ndtbll FROM tbl_offence WHERE tbl_offence.stationcode='$stncode' AND tbl_offence.dutable=0 $monthCheck";
$rsNdtbl = mysql_query($query_rsNdtbl, $conn_ura) or die(mysql_error());
$row_rsNdtbl = mysql_fetch_assoc($rsNdtbl);

$query_rsNDtblGdsVal = "SELECT SUM(t.goods_val) ndval FROM (SELECT tbl_capturedgoods.goods_val FROM tbl_offence JOIN tbl_capturedgoods ON tbl_offence.off_id=tbl_capturedgoods.off_id WHERE tbl_offence.stationcode='$stncode' AND tbl_offence.dutable=0 $monthCheck) t";
$rsNDtblGdsVal = mysql_query($query_rsNDtblGdsVal, $conn_ura) or die(mysql_error());
$row_rsNDtblGdsVal = mysql_fetch_assoc($rsNDtblGdsVal);

$query_rsTaxes = "SELECT SUM(tbl_capturedgoods.taxes) taxes FROM tbl_offence JOIN tbl_capturedgoods ON tbl_offence.off_id=tbl_capturedgoods.off_id WHERE tbl_offence.stationcode='$stncode' $monthCheck";
$rsTaxes = mysql_query($query_rsTaxes, $conn_ura) or die(mysql_error());
$row_rsTaxes = mysql_fetch_assoc($rsTaxes);
$totTaxes+=$row_rsTaxes['taxes'];

$query_rsFines = "SELECT SUM(fines) fines FROM tbl_offence WHERE tbl_offence.stationcode='$stncode' $monthCheck";
$rsFines = mysql_query($query_rsFines, $conn_ura) or die(mysql_error());
$row_rsFines = mysql_fetch_assoc($rsFines);
$totFines+=$row_rsFines['fines'];

$query_rsORS = "SELECT SUM(t.goods_val) outrightsmuggling FROM (SELECT tbl_capturedgoods.goods_val FROM tbl_offence JOIN tbl_capturedgoods ON tbl_offence.off_id=tbl_capturedgoods.off_id WHERE tbl_offence.stationcode='$stncode' AND  tbl_offence.nature_offence='OUT RIGHT SMUGGLING' $monthCheck) t";
$rsORS = mysql_query($query_rsORS, $conn_ura) or die(mysql_error());
$row_rsORS = mysql_fetch_assoc($rsORS);

$query_rsUndVal = "SELECT SUM(t.goods_val) UndVal FROM (SELECT tbl_capturedgoods.goods_val FROM tbl_offence JOIN tbl_capturedgoods ON tbl_offence.off_id=tbl_capturedgoods.off_id WHERE tbl_offence.stationcode='$stncode' AND  tbl_offence.nature_offence='UNDER VALUATION' $monthCheck) t";
$rsUndVal = mysql_query($query_rsUndVal, $conn_ura) or die(mysql_error());
$row_rsUndVal = mysql_fetch_assoc($rsUndVal);

$query_rsMsdec = "SELECT SUM(t.goods_val) Msdec FROM (SELECT tbl_capturedgoods.goods_val FROM tbl_offence JOIN tbl_capturedgoods ON tbl_offence.off_id=tbl_capturedgoods.off_id WHERE tbl_offence.stationcode='$stncode' AND  tbl_offence.nature_offence='MISDECLARATION' $monthCheck) t";
$rsMsdec = mysql_query($query_rsMsdec, $conn_ura) or die(mysql_error());
$row_rsMsdec = mysql_fetch_assoc($rsMsdec);

$query_rsConceal = "SELECT SUM(t.goods_val) Conceal FROM (SELECT tbl_capturedgoods.goods_val FROM tbl_offence JOIN tbl_capturedgoods ON tbl_offence.off_id=tbl_capturedgoods.off_id WHERE tbl_offence.stationcode='$stncode' AND  tbl_offence.nature_offence='CONCEALMENT' $monthCheck) t";
$rsConceal = mysql_query($query_rsConceal, $conn_ura) or die(mysql_error());
$row_rsConceal = mysql_fetch_assoc($rsConceal);

$query_rsOthers = "SELECT SUM(t.goods_val) Others FROM (SELECT tbl_capturedgoods.goods_val FROM tbl_offence JOIN tbl_capturedgoods ON tbl_offence.off_id=tbl_capturedgoods.off_id WHERE tbl_offence.stationcode='$stncode' AND  tbl_offence.nature_offence NOT IN('CONCEALMENT','MISDECLARATION','UNDER VALUATION','OUT RIGHT SMUGGLING','MISCLASSIFICATION' $monthCheck)) t";
$rsOthers = mysql_query($query_rsOthers, $conn_ura) or die(mysql_error());
$row_rsOthers = mysql_fetch_assoc($rsOthers);

$query_rsMsclasfn = "SELECT SUM(t.goods_val) Msclasfn FROM (SELECT tbl_capturedgoods.goods_val FROM tbl_offence JOIN tbl_capturedgoods ON tbl_offence.off_id=tbl_capturedgoods.off_id WHERE tbl_offence.stationcode='$stncode' AND  tbl_offence.nature_offence='MISCLASSIFICATION' $monthCheck) t";
$rsMsclasfn = mysql_query($query_rsMsclasfn, $conn_ura) or die(mysql_error());
$row_rsMsclasfn = mysql_fetch_assoc($rsMsclasfn);
//TOTAL FRAUD VALUE
 $stnTotal=$row_rsDtblGdsVal['val']+ $row_rsNDtblGdsVal['ndval'];
 
 //Other intercepted goods parameters (retrieval)
$query_rsOtherGoods = "SELECT COUNT(t.good_name) Qty, SUM(t.goods_val) Val, SUM(t.taxes) Tax FROM (SELECT tbl_capturedgoods.goods_val, tbl_capturedgoods.good_name, tbl_capturedgoods.taxes FROM tbl_offence JOIN tbl_capturedgoods ON tbl_offence.off_id=tbl_capturedgoods.off_id WHERE tbl_offence.stationcode='$stncode' AND tbl_capturedgoods.category='PHARMACEUTICALS' $monthCheck) t";
$rsOtherGoods = mysql_query($query_rsOtherGoods, $conn_ura) or die(mysql_error());
$row_rsOtherGoods = mysql_fetch_assoc($rsOtherGoods);
	$pharmaQty+=$row_rsOtherGoods['Qty']; $pharmaVal+=$row_rsOtherGoods['Val']; $pharmaTax+=$row_rsOtherGoods['Tax']; 
	
$query_rsOtherGoods = "SELECT COUNT(t.good_name) Qty, SUM(t.goods_val) Val, SUM(t.taxes) Tax FROM (SELECT tbl_capturedgoods.goods_val, tbl_capturedgoods.good_name, tbl_capturedgoods.taxes FROM tbl_offence JOIN tbl_capturedgoods ON tbl_offence.off_id=tbl_capturedgoods.off_id WHERE tbl_offence.stationcode='$stncode' AND tbl_capturedgoods.category='NARCOTICS' $monthCheck) t";
$rsOtherGoods = mysql_query($query_rsOtherGoods, $conn_ura) or die(mysql_error());
$row_rsOtherGoods = mysql_fetch_assoc($rsOtherGoods);
	$narcoticsQty+=$row_rsOtherGoods['Qty']; $narcoticsVal+=$row_rsOtherGoods['Val']; $narcoticsTax+=$row_rsOtherGoods['Tax'];
	
$query_rsOtherGoods = "SELECT COUNT(t.good_name) Qty, SUM(t.goods_val) Val, SUM(t.taxes) Tax FROM (SELECT tbl_capturedgoods.goods_val, tbl_capturedgoods.good_name, tbl_capturedgoods.taxes FROM tbl_offence JOIN tbl_capturedgoods ON tbl_offence.off_id=tbl_capturedgoods.off_id WHERE tbl_offence.stationcode='$stncode' AND tbl_capturedgoods.category='COUNTERFEIT GOODS' $monthCheck) t";
$rsOtherGoods = mysql_query($query_rsOtherGoods, $conn_ura) or die(mysql_error());
$row_rsOtherGoods = mysql_fetch_assoc($rsOtherGoods);
	$counterfeitQty+=$row_rsOtherGoods['Qty']; $counterfeitVal+=$row_rsOtherGoods['Val']; $counterfeitTax+=$row_rsOtherGoods['Tax'];
	
$query_rsOtherGoods = "SELECT COUNT(t.good_name) Qty, SUM(t.goods_val) Val, SUM(t.taxes) Tax FROM (SELECT tbl_capturedgoods.goods_val, tbl_capturedgoods.good_name, tbl_capturedgoods.taxes FROM tbl_offence JOIN tbl_capturedgoods ON tbl_offence.off_id=tbl_capturedgoods.off_id WHERE tbl_offence.stationcode='$stncode' AND tbl_capturedgoods.category='RESTRICTED GOODS' $monthCheck) t";
$rsOtherGoods = mysql_query($query_rsOtherGoods, $conn_ura) or die(mysql_error());
$row_rsOtherGoods = mysql_fetch_assoc($rsOtherGoods);
	$restrictedQty+=$row_rsOtherGoods['Qty']; $restrictedVal+=$row_rsOtherGoods['Val']; $restrictedTax+=$row_rsOtherGoods['Tax'];
	
$query_rsOtherGoods = "SELECT COUNT(t.good_name) Qty, SUM(t.goods_val) Val, SUM(t.taxes) Tax FROM (SELECT tbl_capturedgoods.goods_val, tbl_capturedgoods.good_name, tbl_capturedgoods.taxes FROM tbl_offence JOIN tbl_capturedgoods ON tbl_offence.off_id=tbl_capturedgoods.off_id WHERE tbl_offence.stationcode='$stncode' AND tbl_capturedgoods.category='PROHIBITED' $monthCheck) t";
$rsOtherGoods = mysql_query($query_rsOtherGoods, $conn_ura) or die(mysql_error());
$row_rsOtherGoods = mysql_fetch_assoc($rsOtherGoods);
	$prohibitedQty+=$row_rsOtherGoods['Qty']; $prohibitedVal+=$row_rsOtherGoods['Val']; $prohibitedTax+=$row_rsOtherGoods['Tax'];
	
//ALERTS
//INTELLIGENCE UNIT
$query_rsalerts = "SELECT SUM(t.taxes) Tax FROM (SELECT tbl_capturedgoods.taxes FROM tbl_offence JOIN tbl_capturedgoods ON tbl_offence.off_id=tbl_capturedgoods.off_id WHERE tbl_offence.stationcode='$stncode' AND tbl_offence.alertOrigin=1 $monthCheck) t";
$rsalerts = mysql_query($query_rsalerts, $conn_ura) or die(mysql_error());
$row_rsalerts = mysql_fetch_assoc($rsalerts);	

$query_rsalertUnitCnt = "SELECT COUNT(alertOrigin) alertOrigin FROM tbl_offence WHERE tbl_offence.stationcode='$stncode' AND tbl_offence.alertOrigin=1 $monthCheck";
$rsalertUnitCnt = mysql_query($query_rsalertUnitCnt, $conn_ura) or die(mysql_error());
$row_rsalertUnitCnt = mysql_fetch_assoc($rsalertUnitCnt);
	$intTaxes+=$row_rsalerts['Tax']; $intUnits+=$row_rsalertUnitCnt['alertOrigin']; $intSeizs+=$row_rsalertUnitCnt['alertOrigin']; 

//ENFORCEMENT FIELD OPERATIONS UNIT
$query_rsalerts = "SELECT SUM(t.taxes) Tax FROM (SELECT tbl_capturedgoods.taxes FROM tbl_offence JOIN tbl_capturedgoods ON tbl_offence.off_id=tbl_capturedgoods.off_id WHERE tbl_offence.stationcode='$stncode' AND tbl_offence.alertOrigin=2 $monthCheck) t";
$rsalerts = mysql_query($query_rsalerts, $conn_ura) or die(mysql_error());
$row_rsalerts = mysql_fetch_assoc($rsalerts);	

$query_rsalertUnitCnt = "SELECT COUNT(alertOrigin) alertOrigin FROM tbl_offence WHERE tbl_offence.stationcode='$stncode' AND tbl_offence.alertOrigin=2 $monthCheck";
$rsalertUnitCnt = mysql_query($query_rsalertUnitCnt, $conn_ura) or die(mysql_error());
$row_rsalertUnitCnt = mysql_fetch_assoc($rsalertUnitCnt);
	$enfTaxes+=$row_rsalerts['Tax']; $enfUnits+=$row_rsalertUnitCnt['alertOrigin']; $enfSeizs+=$row_rsalertUnitCnt['alertOrigin'];

//SCANNER UNIT
$query_rsalerts = "SELECT SUM(t.taxes) Tax FROM (SELECT tbl_capturedgoods.taxes FROM tbl_offence JOIN tbl_capturedgoods ON tbl_offence.off_id=tbl_capturedgoods.off_id WHERE tbl_offence.stationcode='$stncode' AND tbl_offence.alertOrigin=3 $monthCheck) t";
$rsalerts = mysql_query($query_rsalerts, $conn_ura) or die(mysql_error());
$row_rsalerts = mysql_fetch_assoc($rsalerts);	

$query_rsalertUnitCnt = "SELECT COUNT(alertOrigin) alertOrigin FROM tbl_offence WHERE tbl_offence.stationcode='$stncode' AND tbl_offence.alertOrigin=3 $monthCheck";
$rsalertUnitCnt = mysql_query($query_rsalertUnitCnt, $conn_ura) or die(mysql_error());
$row_rsalertUnitCnt = mysql_fetch_assoc($rsalertUnitCnt);
	$scanTaxes+=$row_rsalerts['Tax']; $scanUnits+=$row_rsalertUnitCnt['alertOrigin']; $scanSeizs+=$row_rsalertUnitCnt['alertOrigin'];

//TMU
$query_rsalerts = "SELECT SUM(t.taxes) Tax FROM (SELECT tbl_capturedgoods.taxes FROM tbl_offence JOIN tbl_capturedgoods ON tbl_offence.off_id=tbl_capturedgoods.off_id WHERE tbl_offence.stationcode='$stncode' AND tbl_offence.alertOrigin=4 $monthCheck) t";
$rsalerts = mysql_query($query_rsalerts, $conn_ura) or die(mysql_error());
$row_rsalerts = mysql_fetch_assoc($rsalerts);	

$query_rsalertUnitCnt = "SELECT COUNT(alertOrigin) alertOrigin FROM tbl_offence WHERE tbl_offence.stationcode='$stncode' AND tbl_offence.alertOrigin=4 $monthCheck";
$rsalertUnitCnt = mysql_query($query_rsalertUnitCnt, $conn_ura) or die(mysql_error());
$row_rsalertUnitCnt = mysql_fetch_assoc($rsalertUnitCnt);
	$tmuTaxes+=$row_rsalerts['Tax']; $tmuUnits+=$row_rsalertUnitCnt['alertOrigin']; $tmuSeizs+=$row_rsalertUnitCnt['alertOrigin'];

//OTHER UNITS OUTSIDE Enf Division
$query_rsalerts = "SELECT SUM(t.taxes) Tax FROM (SELECT tbl_capturedgoods.taxes FROM tbl_offence JOIN tbl_capturedgoods ON tbl_offence.off_id=tbl_capturedgoods.off_id WHERE tbl_offence.stationcode='$stncode' AND tbl_offence.alertOrigin=5 $monthCheck) t";
$rsalerts = mysql_query($query_rsalerts, $conn_ura) or die(mysql_error());
$row_rsalerts = mysql_fetch_assoc($rsalerts);	

$query_rsalertUnitCnt = "SELECT COUNT(alertOrigin) alertOrigin FROM tbl_offence WHERE tbl_offence.stationcode='$stncode' AND tbl_offence.alertOrigin=5 $monthCheck";
$rsalertUnitCnt = mysql_query($query_rsalertUnitCnt, $conn_ura) or die(mysql_error());
$row_rsalertUnitCnt = mysql_fetch_assoc($rsalertUnitCnt);
	$otherTaxes+=$row_rsalerts['Tax']; $otherUnits+=$row_rsalertUnitCnt['alertOrigin']; $otherSeizs+=$row_rsalertUnitCnt['alertOrigin'];

 
 switch($row_rsStns['name']){
	 case 'SCANNER':
	 $colorKey='SCANNER';
	 break;
	 case 'ENTEBBE':
	 $colorKey='ENTEBBE';
	 break;
	 case 'KAMPALA':
	 $colorKey='KAMPALA';
	 break;
	 default:
	 $colorKey=$row_rsRegions['name'];
	 break;
 }
 ?>
    <tr bgcolor="<?php echo $row_colors[$colorKey]; ?>">
      <th><?php echo $regionname; $csv_output .= $regionname . ", ";?></th>
      <td><?php echo $sn; $csv_output .=($sn++) . ", "; ?></td>
      <td><?php echo $row_rsStns['name']; $csv_output .=$row_rsStns['name'] . ", "; ?></td>
      <td><?php echo $row_rsNdtbl['ndtbll']; $tot1+=$row_rsNdtbl['ndtbll']; $csv_output .=$row_rsNdtbl['ndtbll'] . ", "; ?></td>
      <td><?php echo $row_rsDutable['dtbl']; $tot2+=$row_rsDutable['dtbl']; $csv_output .=$row_rsDutable['dtbl'] . ", "; ?></td>
      <td><?php echo $row_rsNDtblGdsVal['ndval']; $tot3+=$row_rsNDtblGdsVal['ndval']; $csv_output .=$row_rsNDtblGdsVal['ndval'] . ", "; ?></td>
      <td><?php echo $row_rsDtblGdsVal['val']; $tot4+=$row_rsDtblGdsVal['val'];  $csv_output .=$row_rsDtblGdsVal['val'] . ", "; ?></td>
      <td><?php echo $stnTotal*$dol_val; $tot5+=$stnTotal*$dol_val; $csv_output .=($stnTotal*$dol_val) . ", "; ?></td>
      <td><?php echo $row_rsORS['outrightsmuggling']*$dol_val; $tot6+=$row_rsORS['outrightsmuggling']*$dol_val; $csv_output .=($row_rsORS['outrightsmuggling']*$dol_val) . ", "; ?></td>
      <td><?php $perORS=($stnTotal!=0)?round((($row_rsORS['outrightsmuggling']/$stnTotal)*100),2):0; echo $perORS; $csv_output .=$perORS . ", "; ?></td>
      <td><?php echo $row_rsUndVal['UndVal']*$dol_val; $tot7+=$row_rsUndVal['UndVal']*$dol_val; $csv_output .=($row_rsUndVal['UndVal']*$dol_val) . ", "; ?></td>
      <td><?php $perUndVal=($stnTotal!=0)?round((($row_rsUndVal['UndVal']/$stnTotal)*100),2):0; echo $perUndVal; $csv_output .=$perUndVal . ", "; ?></td>
      <td><?php echo $row_rsMsdec['Msdec']*$dol_val; $tot8+=$row_rsMsdec['Msdec']*$dol_val; $csv_output .=($row_rsMsdec['Msdec']*$dol_val) . ", "; ?></td>
      <td><?php $perMsdec=($stnTotal!=0)?round((($row_rsMsdec['Msdec']/$stnTotal)*100),2):0; echo $perMsdec; $csv_output .=$perMsdec . ", "; ?></td>
      <td><?php echo $row_rsConceal['Conceal']*$dol_val; $tot9+=$row_rsConceal['Conceal']*$dol_val; $csv_output .=($row_rsConceal['Conceal']*$dol_val) . ", "; ?></td>
      <td><?php $perConceal=($stnTotal!=0)?round((($row_rsConceal['Conceal']/$stnTotal)*100),2):0; echo $perConceal; $csv_output .=$perConceal . ", "; ?></td>
      <td><?php echo $row_rsMsclasfn['Msclasfn']*$dol_val; $tot10+=$row_rsMsclasfn['Msclasfn']*$dol_val; $csv_output .=($row_rsMsclasfn['Msclasfn']*$dol_val) . ", "; ?></td>
      <td><?php $perMsclasfn=($stnTotal!=0)?round((($row_rsMsclasfn['Msclasfn']/$stnTotal)*100),2):0; echo $perMsclasfn; $csv_output .=$perMsclasfn . ", "; ?></td>
      <td><?php echo $row_rsOthers['Others']*$dol_val; $tot11+=$row_rsOthers['Others']*$dol_val; $csv_output .=($row_rsOthers['Others']*$dol_val) . ", "; ?></td>
      <td><?php $perOther=($stnTotal!=0)?round((($row_rsOthers['Others']/$stnTotal)*100),2):0;echo $perOther; $csv_output .=$perOther . "\n"; ?></td>
    </tr>
    <?php 
    $table4.="<tr>
      <td colspan='4' style=\"text-align:center\">". $row_rsStns['name']."</td>
    </tr>";
	$csv_table4=",". $row_rsStns['name']."\n";
$station=$row_rsStns['stationcode'];
$query_rsHCWH = "SELECT tbl_offence.file_no, t.desc_goods, t.tax, t.goods_val FROM tbl_offence JOIN (SELECT off_id,GROUP_CONCAT(good_name,'-',good_descpn) desc_goods, sum(taxes) tax, SUM(goods_val) goods_val FROM tbl_capturedgoods GROUP BY off_id ORDER BY tax) t ON tbl_offence.off_id=t.off_id WHERE tbl_offence.handedCWH=1 AND tbl_offence.stationcode='$station' $monthCheck";
$rsHCWH = mysql_query($query_rsHCWH, $conn_ura) or die(mysql_error());
$totalRows_rsHCWH = mysql_num_rows($rsHCWH);
while ($row_rsHCWH = mysql_fetch_assoc($rsHCWH)){ 
$table4.=
      "<tr>
        <td>".$row_rsHCWH['file_no']."</td>
        <td>".$row_rsHCWH['desc_goods']."</td>
        <td>".$row_rsHCWH['tax']."</td>
        <td>".$row_rsHCWH['goods_val']."</td>
      </tr>";
	  $csv_table4.=$row_rsHCWH['file_no'].",".$row_rsHCWH['desc_goods'].",".$row_rsHCWH['tax'].",".$row_rsHCWH['goods_val']."\n";
	  }
	mysql_free_result($rsHCWH);
?>
    <?php 
    $table5.="<tr>
      <td colspan='4' style=\"text-align:center\">". $row_rsStns['name']."</td>
    </tr>";
	$csv_table5=",". $row_rsStns['name']."\n";
$station=$row_rsStns['stationcode'];
$query_rsimpVehicle = "SELECT tbl_offence.file_no, tbl_capturedgoods.good_descpn , tbl_capturedgoods.goods_val, tbl_capturedgoods.taxes FROM tbl_offence JOIN tbl_capturedgoods ON tbl_offence.off_id=tbl_capturedgoods.off_id JOIN station ON tbl_offence.stationcode=station.stationcode WHERE tbl_offence.stationcode='$station' $monthCheck AND (tbl_capturedgoods.good_name LIKE '%VEHICLE%') OR (tbl_capturedgoods.good_name LIKE '%CAR%')";
$rsimpVehicle = mysql_query($query_rsimpVehicle, $conn_ura) or die(mysql_error());
$totalRows_rsimpVehicle = mysql_num_rows($rsimpVehicle);
while ($row_rsimpVehicle = mysql_fetch_assoc($rsimpVehicle)){ 
$table5.=
      "<tr>
        <td>".$row_rsimpVehicle['file_no']."</td>
        <td>".$row_rsimpVehicle['good_descpn']."</td>
        <td>".$row_rsimpVehicle['taxes']."</td>
        <td>".$row_rsimpVehicle['goods_val']."</td>
      </tr>";
	  $csv_table5.=$row_rsimpVehicle['file_no'].",".$row_rsimpVehicle['good_descpn'].",".$row_rsimpVehicle['taxes'].",".$row_rsimpVehicle['goods_val']."\n";
	  }
	mysql_free_result($rsimpVehicle);
?>
    <?php $table6.="<tr>
      <td colspan='5' style='text-align:center; font-weight:700px;'>".$row_rsStns['name']."</td>
    </tr>";
	$csv_table6=",,". $row_rsStns['name']."\n";
	 ?>
    <?php 
$query_rsTopTenOff = "SELECT tbl_offence.offender_names, tbl_offence.nature_offence, t.desc_goods, t.tax FROM tbl_offence JOIN (SELECT off_id,GROUP_CONCAT(good_name,'-',good_descpn) desc_goods, sum(taxes) tax FROM tbl_capturedgoods GROUP BY off_id ORDER BY tax LIMIT 0,10) t ON tbl_offence.off_id=t.off_id WHERE tbl_offence.stationcode='$station' $monthCheck";
$rsTopTenOff = mysql_query($query_rsTopTenOff, $conn_ura) or die(mysql_error());
$totalRows_rsTopTenOff = mysql_num_rows($rsTopTenOff);
$count=1; while ($row_rsTopTenOff = mysql_fetch_assoc($rsTopTenOff)){ 
$cont=($totalRows_rsTopTenOff!=0)?$count++:"";
$table6.=
      "<tr>
        <td align='right'>".$cont."</td>
        <td>".$row_rsTopTenOff['offender_names']."</td>
        <td>".$row_rsTopTenOff['nature_offence']."</td>
        <td>".$row_rsTopTenOff['desc_goods']."</td>
        <td>".$row_rsTopTenOff['tax']."</td>
      </tr>";
	  $csv_table6.=$cont.",".$row_rsTopTenOff['offender_names'].",".$row_rsTopTenOff['nature_offence'].",".$row_rsTopTenOff['desc_goods'].",".$row_rsTopTenOff['tax']."\n";

?>
      <?php }
	mysql_free_result($rsTopTenOff);
?>
      <?php 

mysql_free_result($rsDutable);

mysql_free_result($rsNdtbl);

mysql_free_result($rsDtblGdsVal);
mysql_free_result($rsNDtblGdsVal);

mysql_free_result($rsORS);

mysql_free_result($rsUndVal);

mysql_free_result($rsMsdec);

mysql_free_result($rsConceal);

mysql_free_result($rsOthers);
mysql_free_result($rsOtherGoods);
 $regionname=""; 
mysql_free_result($rsMsclasfn);
mysql_free_result($rsalerts);
mysql_free_result($rsalertUnitCnt);
} while ($row_rsStns = mysql_fetch_assoc($rsStns)); ?>
<?php if($_SESSION['MM_UserGroup']!="Officer"&&$_SESSION['MM_UserGroup']!="In Charge"){?>
    <tr>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td class="required">Total[s]</td>
      <td><?php echo $tot1; ?></td>
      <td><?php echo $tot2; ?></td>
      <td><?php echo $tot3; ?></td>
      <td><?php echo $tot4; ?></td>
      <td><?php echo $tot5; ?></td>
      <td><?php echo $tot6; ?></td>
      <td><?php $tot6z=($tot6!=0)?round((($tot6/$tot5)*100),2):0; echo $tot6z; ?></td>
      <td><?php echo $tot7; ?></td>
      <td><?php $tot7z=($tot7!=0)?round((($tot7/$tot5)*100),2):0; echo $tot7z; ?></td>
      <td><?php echo $tot8; ?></td>
      <td><?php $tot8z=($tot8!=0)?round((($tot8/$tot5)*100),2):0; echo $tot8z; ?></td>
      <td><?php echo $tot9; ?></td>
      <td><?php $tot9z=($tot9!=0)?round((($tot9/$tot5)*100),2):0; echo $tot9z; ?></td>
      <td><?php echo $tot10; ?></td>
      <td><?php $tot10z=($tot10!=0)?round((($tot10/$tot5)*100),2):0; echo $tot10z; ?></td>
      <td><?php echo $tot11; ?></td>
      <td><?php $tot11z=($tot11!=0)?round((($tot11/$tot5)*100),2):0; echo $tot11z; ?></td>
      <?php $csv_output.=" , , Total[s], $tot1, $tot2, $tot3, $tot4, $tot5, $tot6, $tot6z, $tot7, $tot7z, $tot8, $tot8z, $tot9, $tot9z, $tot10, $tot10z, $tot11, $tot11z\n"; ?>
    </tr>
      <?php 
$tot1a+=$tot1; $tot2a+=$tot2;$tot3a+=$tot3;$tot4a+=$tot4;$tot5a+=$tot5;$tot6a+=$tot6;$tot7a+=$tot7;$tot8a+=$tot8;$tot9a+=$tot9;$tot10a+=$tot10;$tot11a+=$tot11; }
mysql_free_result($rsStns);
} while ($row_rsRegions = mysql_fetch_assoc($rsRegions));
?>
<?php if($_SESSION['MM_UserGroup']=="Asst Comm"||$_SESSION['MM_UserGroup']=="Manager")
{?>
    <tr>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td bgcolor="#32849A">TOTAL</td>
      <td bgcolor="#32849A"><?php echo $tot1a; ?></td>
      <td bgcolor="#32849A"><?php echo $tot2a; ?></td>
      <td bgcolor="#32849A"><?php echo $tot3a; ?></td>
      <td bgcolor="#32849A"><?php echo $tot4a; ?></td>
      <td bgcolor="#32849A"><?php echo $tot5a; ?></td>
      <td bgcolor="#32849A"><?php echo $tot6a; ?></td>
      <td bgcolor="#32849A"><?php $tot6az=($tot6a!=0)?round((($tot6a/$tot5a)*100),2):0;echo $tot6az; ?></td>
      <td bgcolor="#32849A"><?php echo $tot7a; ?></td>
      <td bgcolor="#32849A"><?php $tot7az=($tot7a!=0)?round((($tot7a/$tot5a)*100),2):0; echo $tot7az; ?></td>
      <td bgcolor="#32849A"><?php echo $tot8a; ?></td>
      <td bgcolor="#32849A"><?php $tot8az=($tot8a!=0)?round((($tot8a/$tot5a)*100),2):0;echo $tot8az; ?></td>
      <td bgcolor="#32849A"><?php echo $tot9a; ?></td>
      <td bgcolor="#32849A"><?php $tot9az=($tot9a!=0)?round((($tot9a/$tot5a)*100),2):0;echo $tot9az; ?></td>
      <td bgcolor="#32849A"><?php echo $tot10a; ?></td>
      <td bgcolor="#32849A"><?php $tot10az=($tot10a!=0)?round((($tot10a/$tot5a)*100),2):0; echo $tot10az; ?></td>
      <td bgcolor="#32849A"><?php echo $tot11a; ?></td>
      <td bgcolor="#32849A"><?php $tot11az=($tot11a!=0)?round((($tot11a/$tot5a)*100),2):0;echo $tot11az; ?></td>
      <?php $csv_output .= " , , TOTAL, $tot1a, $tot2a, $tot3a, $tot4a, $tot5a, $tot6a, $tot6az, $tot7a, $tot7az, $tot8a, $tot8az, $tot9a, $tot9az, $tot10a, $tot10az, $tot11a, $tot11az \n";?>
    </tr>
    <?php }?>
  </table>
  <p>&nbsp;</p>
  <p>&nbsp;</p>
  <h3><a name="performanceByIntelligence" id="performanceByIntelligence"></a>TABLE 2: ANALYSIS OF PERFORMANCE BY INTELLIGENCE ALERTS GENERATED/RECEIVED</h3>
  <p>&nbsp;</p>
  <center>
  <table border="1" cellpadding="1" cellspacing="0" class="tbl_report">
    <tr>
      <th>ALERT ORIGINATING UNIT</th>
      <th>No. OF ALERTS GENERATED/ RECEIVED</th>
      <th>No. OF SEIZURES RAISED</th>
      <th>PROPORTION OF SUCCESSFUL ALERTS [%AGE]</th>
      <th>SUM OF TOP UP TAXES COLLECTED</th>
    </tr>
    <?php $csv_table2="\nTABLE 2: ANALYSIS OF PERFORMANCE BY INTELLIGENCE ALERTS GENERATED/RECEIVED\n\nALERT ORIGINATING UNIT, No. OF ALERTS GENERATED/ RECEIVED, No. OF SEIZURES RAISED, PROPORTION OF SUCCESSFUL ALERTS [%AGE], SUM OF TOP UP TAXES COLLECTED\n";?>
    <tr>
      <td>INTELLIGENCE UNIT</td>
      <td><?php echo  $intUnits; ?></td>
      <td><?php echo $intSeizs; ?></td>
      <td><?php $perIntUnit=($intUnits!=0)?round((($intSeizs/$intUnits)*100),2):0;echo $perIntUnit;?>%</td>
      <td><?php echo $intTaxes; $csv_table2.="INTELLIGENCE UNIT, $intUnits, $intSeizs, $perIntUnit%, $intTaxes\n";?></td>
    </tr>
    <tr>
      <td>ENFORCEMENT FIELD OPERATIONS UNIT</td>
      <td><?php echo $enfUnits ?></td>
      <td><?php echo $enfSeizs ?></td>
      <td><?php $perIntUnit=($enfUnits!=0)?round((($enfSeizs/$enfUnits)*100),2):0;echo $perIntUnit;?>%</td>
      <td><?php echo $enfTaxes; $csv_table2.="ENFORCEMENT FIELD OPERATIONS UNIT, $enfUnits, $enfSeizs, $perIntUnit%, $enfTaxes\n";?></td>
    </tr>
    <tr>
      <td>SCANNER UNIT</td>
      <td><?php echo $scanUnits ?></td>
      <td><?php echo $scanSeizs ?></td>
      <td><?php $perIntUnit=($scanUnits!=0)?round((($scanSeizs/$scanUnits)*100),2):0;echo $perIntUnit;?>%</td>
      <td><?php echo $scanTaxes; $csv_table2.="SCANNER UNIT, $scanUnits, $scanSeizs, $perIntUnit%, $scanTaxes\n";?></td>
    </tr>
    <tr>
      <td>TMU</td>
      <td><?php echo $tmuUnits ?></td>
      <td><?php echo $tmuSeizs ?></td>
      <td><?php $perIntUnit=($tmuUnits!=0)?round((($tmuSeizs/$tmuUnits)*100),2):0;echo $perIntUnit;?>%</td>
      <td><?php echo $tmuTaxes; $csv_table2.="TMU, $tmuUnits, $tmuSeizs, $perIntUnit%, $tmuTaxes\n";?></td>
    </tr>
    <tr>
      <td>OTHER UNITS OUTSIDE Enf Division</td>
      <td><?php echo $otherUnits ?></td>
      <td><?php echo $otherSeizs ?></td>
      <td><?php echo ($otherUnits!=0)?round((($otherSeizs/$otherUnits)*100),2):0;?>%</td>
      <td><?php echo $otherTaxes; $csv_table2.="OTHER UNITS OUTSIDE Enf Division, $otherUnits, $otherSeizs, $perIntUnit%, $otherTaxes\n"; ?></td>
    </tr>
  </table>
  </center>
  <p>&nbsp;</p>
  <h3><a name="interceptedGoods" id="interceptedGoods"></a>TABLE 3: OTHER INTERCEPTED GOODS</h3>
  <p>&nbsp;</p>
  <center>
  <table border="1" cellpadding="1" cellspacing="0" class="tbl_report">
    <tr>
      <th>&nbsp;</th>
      <th>QUANTITIES</th>
      <th>GOODS DESCRIPTION</th>
      <th>FRAUD VALUE</th>
      <th>TAX LIABILITY WHERE APPLICABLE</th>
    </tr>
    <?php $csv_table2.="\nTABLE 3: OTHER INTERCEPTED GOODS\n\n , QUANTITIES, GOODS DESCRIPTION, FRAUD VALUE, TAX LIABILITY WHERE APPLICABLE\n";?>
    <tr>
      <td>PHARMACEUTICALS</td>
      <td><?php echo $pharmaQty; ?></td>
      <td>NIL</td>
      <td><?php echo $pharmaVal; ?></td>
      <td><?php echo $pharmaTax; $csv_table2.="PHARMACEUTICALS, $pharmaQty, NIL, $pharmaVal, $pharmaTax\n";?></td>
    </tr>
    <tr>
      <td>NARCOTICS</td>
      <td><?php echo $narcoticsQty; ?></td>
      <td>NIL</td>
      <td><?php echo $narcoticsVal; ?></td>
      <td><?php echo $narcoticsTax; $csv_table2.="NARCOTICS, $narcoticsQty, NIL, $narcoticsVal, $narcoticsTax\n"; ?></td>
    </tr>
    <tr>
      <td>PROHIBITED GOODS</td>
      <td><?php echo $prohibitedQty; ?></td>
      <td>NIL</td>
      <td><?php echo $prohibitedVal; ?></td>
      <td><?php echo $prohibitedTax; $csv_table2.="PROHIBITED GOODS, $prohibitedQty, NIL, $prohibitedVal, $prohibitedTax\n"; ?></td>
    </tr>
    <tr>
      <td>RESTRICTED GOODS</td>
      <td><?php echo $restrictedQty; ?></td>
      <td>NIL</td>
      <td><?php echo $restrictedVal; ?></td>
      <td><?php echo $restrictedTax; $csv_table2.="RESTRICTED GOODS, $restrictedQty, NIL, $restrictedVal, $restrictedTax\n"; ?></td>
    </tr>
    <tr>
      <td>COUNTERFEIT GOODS</td>
      <td><?php echo $counterfeitQty; ?></td>
      <td>NIL</td>
      <td><?php echo $counterfeitVal; ?></td>
      <td><?php echo $counterfeitTax; $csv_table2.="COUNTERFEIT GOODS, $counterfeitQty, NIL, $counterfeitVal, $counterfeitTax\n"; ?></td>
    </tr>
  </table></center>
  <p>&nbsp;</p>
  <h3><a name="goodsHandedToCustomsWH" id="goodsHandedToCustomsWH"></a>TABLE 4: GOODS HANDED OVER TO CUSTOMS WAREHOUSE</h3>
  <p>&nbsp;</p>
  <center>
  <table border="1" cellpadding="1" cellspacing="0" class="tbl_report">
    <tr>
      <th>OFFENCE REFERENCE NUMBER</th>
      <th>GOODS DESCRIPTION</th>
      <th>FRAUD VALUE</th>
      <th>TAX LIABILITY</th>
    </tr>
    <?php $csv_table4="\nTABLE 4: GOODS HANDED OVER TO CUSTOMS WAREHOUSE\n\nOFFENCE REFERENCE NUMBER,GOODS DESCRIPTION,FRAUD VALUE,TAX LIABILITY\n".$csv_table4; echo $table4; ?>
  </table>
  </center>
  <p>&nbsp;</p>
  <h3><a name="impoundedVehiclesAtStation" id="impoundedVehiclesAtStation"></a>TABLE 5: IMPOUNDED VEHICLES AT THE STATION</h3>
  <p>&nbsp;</p>
  <center>
  <table border="1" cellpadding="1" cellspacing="0" class="tbl_report">
    <tr>
      <th>OFFENCE REFERENCE NUMBER</th>
      <th>REGISTRATION</th>
      <th>FRAUD VALUE</th>
      <th>TAX LIABILITY</th>
    </tr>
    <?php echo $table5; $csv_table5="\nOFFENCE REFERENCE NUMBER, REGISTRATION, REGISTRATION, FRAUD VALUE, TAX LIABILITY\n".$csv_table5;?>
  </table>
  </center>
  <p>&nbsp;</p>
<?php if($_SESSION['MM_UserGroup']!="Officer"&&$_SESSION['MM_UserGroup']!="In Charge"){?>
		<!-- load api -->
		<script type="text/javascript" src="http://www.google.com/jsapi"></script>
		
		<script type="text/javascript">
			//load package
			google.load('visualization', '1', {packages: ['corechart']});
		</script>
  <?php 
	//include database connection
	include 'db_connect.php';
	
$query_rsStations = "SELECT station.stationcode FROM station";
$stnID="";
//$station="'".$_SESSION['StationCode']."'";
if($_SESSION['MM_UserGroup']=="Regional Supervisor")
$stnID= " WHERE station.region=(SELECT region FROM station WHERE station.stationcode='".$_SESSION['StationCode']."') ";
$stationID= " WHERE tbl_offence.stationcode IN (".$query_rsStations."$stnID) ".$monthCheck;

$query3="SELECT station.name, SUM(t.taxes) tax FROM tbl_offence  JOIN  tbl_capturedgoods t ON tbl_offence.off_id=t.off_id JOIN station ON tbl_offence.stationcode=station.stationcode ".$stationID." GROUP BY station.name";
	//execute the query
	$result3 = $mysqli->query($query3);
	//get number of rows returned
	$num_results3 = $result3->num_rows;
	?>
    <h3><a name="performancePerStation" id="performancePerStation"></a>CHART 1 : PERFORMANCE PER STATION</h3>
	<?php if( $num_results3 > 0){?>
  <script type="text/javascript">
      google.setOnLoadCallback(drawChart);
      function drawChart() {
        var data = google.visualization.arrayToDataTable([
					['name', 'Tax'],
					<?php
					while( $row = $result3->fetch_assoc() ){
						extract($row);
						echo "['{$name}', {$tax}],";
					}
					?>
        ]);

        var options = {
          title: '',
          hAxis: {title: 'Station', titleTextStyle: {color: 'red'}}
        };

        var chart = new google.visualization.ColumnChart(document.getElementById('barchart_div'));
        chart.draw(data, options);
      }
    </script>
	<!-- where the chart will be rendered-->
  <div id="barchart_div" style="height: 500px; text-align:center"></div>
	<?php

	}else{
		echo "No data for display.";
	}
	?>
  <h3><a name="percentageContribution" id="percentageContribution"></a>CHART 2: PERCENTAGE CONTRIBUTION</h3>
	
	<?php
$query="SELECT station.name, SUM(t.taxes) tax FROM tbl_offence  JOIN  tbl_capturedgoods t ON tbl_offence.off_id=t.off_id JOIN station ON tbl_offence.stationcode=station.stationcode ".$stationID." GROUP BY station.name";
	//execute the query
	$result = $mysqli->query( $query );
	//get number of rows returned
	$num_results = $result->num_rows;
	if( $num_results > 0){

	?>
		<script type="text/javascript">
			function drawVisualization() {
				// Create and populate the data table.
				var data = google.visualization.arrayToDataTable([
					['name', 'tax'],
					<?php
					while( $row = $result->fetch_assoc() ){
						extract($row);
						echo "['{$name}', {$tax}],";
					}
					?>
				]);

				// Create and draw the visualization.
				new google.visualization.PieChart(document.getElementById('visualization')).
				draw(data, {title:""});
			}

			google.setOnLoadCallback(drawVisualization);
		</script>
	<!-- where the chart will be rendered-->
	<div id="visualization" style="height: 400px; text-align:center" ></div>
	<?php

	}else{
		echo "No data for display.";
	}
	?>
	
  <h3><a name="performanceByNatureofOffence" id="performanceByNatureofOffence"></a>CHART 3: PERFORMANCE BY NATURE OF OFFENCE</h3>
	<!-- where the chart will be rendered-->
  <?php 
	
	//PERFORMANCE BY NATURE OF OFFENCE CHART
	$query2="SELECT tbl_offence.nature_offence, SUM(t.taxes) tax FROM tbl_offence  JOIN  tbl_capturedgoods t ON tbl_offence.off_id=t.off_id  ".$stationID." GROUP BY nature_offence ORDER BY tax";

	//execute the query
	$result = $mysqli->query( $query2 );
	//get number of rows returned
	$num_results = $result->num_rows;
	if( $num_results > 0){
  ?>
  <script type="text/javascript">
			function drawVisualization() {
				// Create and populate the data table.
				var data = google.visualization.arrayToDataTable([
					['nature_offence', 'tax'],
					<?php
					while( $row = $result->fetch_assoc() ){
						extract($row);
						echo "['{$nature_offence}', {$tax}],";
					}
					?>
				]);

				// Create and draw the visualization.
				new google.visualization.PieChart(document.getElementById('perfByOffence')).
				draw(data, {title:""});
			}

			google.setOnLoadCallback(drawVisualization);
		</script>
  <div id="perfByOffence" style="height: 400px; text-align:center"></div>
	<?php

	}else{
		echo "No data for display.";
	}
	?>
  <h3><a name="topTenOffenders" id="topTenOffenders"></a>TABLE 6: LIST OF TOP TEN OFFENDERS</h3>
  <p>&nbsp;</p>
  <center>
  <table border="1" cellpadding="1" cellspacing="0" class="tbl_report">
    <tr>
      <th>S/N</th>
      <th>OFFENDER</th>
      <th>OFFENCE COMMITED</th>
      <th>NATURE OF OFFENCE/QUANTITIES</th>
      <th>REMARKS</th>
    </tr>
    <?php echo $table6; $csv_table6="\nTABLE 6: LIST OF TOP TEN OFFENDERS\n\nS/N,OFFENDER,OFFENCE COMMITED,NATURE OF OFFENCE/QUANTITIES,REMARKS\n";?>
  </table>
  </center>
  <p>&nbsp;</p>
  <h3><a name="topTenRiskySmuggledItems" id="topTenRiskySmuggledItems"></a>TABLE 7: TOP TEN RISKY SMUGGLED ITEMS</h3>
  <p>&nbsp;</p>
  <center>
  <table border="1" cellpadding="1" cellspacing="0" class="tbl_report">
    <tr>
      <th>S/N</th>
      <th>ITEM</th>
      <th>No. OF SEIZURES</th>
      <th>TOTAL FRAUD VALUE [$]</th>
      <th>TOTAL TAXES RECOVERED</th>
      <th>REMARKS</th>
    </tr>
  <?php 
  $csv_table7="\nTABLE 7: TOP TEN RISKY SMUGGLED ITEMS\n\nS/N,ITEM,No. OF SEIZURES,TOTAL FRAUD VALUE [$],TOTAL TAXES RECOVERED,REMARKS\n";
mysql_select_db($database_conn_ura, $conn_ura);
$query_rsTopTenRisky = "SELECT t.good_name, t.seizures, t.tax, t.gval FROM tbl_offence JOIN (SELECT  off_id, good_name, SUM(taxes) tax, SUM(goods_val) gval, COUNT(good_name) seizures FROM tbl_capturedgoods GROUP BY good_name ORDER BY seizures LIMIT 0 , 10) t ON tbl_offence.off_id=t.off_id ".$stationID;
$rsTopTenRisky = mysql_query($query_rsTopTenRisky, $conn_ura) or die(mysql_error());
$totalRows_rsTopTenRisky = mysql_num_rows($rsTopTenRisky);
$row_rsTopTenRisky = mysql_fetch_assoc($rsTopTenRisky);
$totalTax=0;$count=1;
 do { ?>
     <tr>
      <td align="right"><?php echo $count; ?></td>
      <td><?php echo $row_rsTopTenRisky['good_name']; ?></td>
      <td align="right"><?php echo $row_rsTopTenRisky['seizures']; ?></td>
      <td ><?php echo $row_rsTopTenRisky['gval']; ?></td>
      <td align="right"><?php echo $row_rsTopTenRisky['tax']; $totalTax+=$row_rsTopTenRisky['tax']; ?></td>
      <td ></td>
    </tr>
<?php
$csv_table7.=($count++).",".$row_rsTopTenRisky['good_name'].",".$row_rsTopTenRisky['good_name'].",".$row_rsTopTenRisky['seizures'].",".$row_rsTopTenRisky['gval']."\n";;
 } while ($row_rsTopTenRisky = mysql_fetch_assoc($rsTopTenRisky));
	mysql_free_result($rsTopTenRisky);
	?>
    <tr>
      <td></td>
      <td></td>
      <td></td>
      <td></td>
      <th align="right"><?php echo $totalTax; $csv_table7.=" , , , ,$totalTax, \n";?></th>
      <td></td>
    </tr>
  </table>
  </center>
  <p>&nbsp;</p>
  <h3><a name="overallOperationsSummary" id="overallOperationsSummary"></a>OVERALL OPERATIONS SUMMARY</h3>
  <p>&nbsp;</p>
  <center>
  <table border="1" cellpadding="1" cellspacing="0" class="tbl_report">
    <tr>
      <td >No. of violations(seizures) on dutable goods</td>
      <td align="right" width="413"><?php echo $tot2a; ?></td>
    </tr>
    <tr>
      <td>No. of violations(seizures) on non dutable goods</td>
      <td align="right"><?php echo $tot1a; ?></td>
    </tr>
    <tr>
      <td>Amount of duties recovered</td>
      <td align="right"><?php echo $totTaxes ?></td>
    </tr>
    <tr>
      <td >Amount of fines/ penalties levied</td>
      <td >&nbsp;</td>
    </tr>
    <tr>
      <td >Amount of fines/ penalties collected</td>
      <td align="right"><?php echo $totFines; ?>&nbsp;</td>
    </tr>
    <tr>
      <td>Fraud value of dutiable seized goods</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td>Fraud value of non dutiable seized goods</td>
      <td >&nbsp;</td>
    </tr>
    <tr>
      <td>Revenue from sale of seized goods</td>
      <td align="right">0</td>
    </tr>
    <tr>
      <td>Number of cases sent for prosecution</td>
      <td align="right">0</td>
    </tr>
  </table>
  </center>
  <p>
    <?php
	$csv_table8="\nOVERALL OPERATIONS SUMMARY
	No. of violations(seizures) on dutable goods,$tot2a
	No. of violations(seizures) on non dutable goods,$tot1a
	Amount of duties recovered, $totTaxes
	Amount of fines/ penalties levied,
	Amount of fines/ penalties collected,$totFines
	Fraud value of dutiable seized goods,
	Fraud value of non dutiable seized goods,
	Revenue from sale of seized goods, 
	Number of cases sent for prosecution,\n";
	 }
if($_SESSION['MM_UserGroup']!="Officer"){
mysql_select_db($database_conn_ura, $conn_ura);
$query_rsChallenges = "SELECT station.name, GROUP_CONCAT(tbl_challenges.challenge) CHALL, GROUP_CONCAT(tbl_challenges.op_undertaken) OPS, GROUP_CONCAT(tbl_challenges.summary) SUMARY FROM tbl_challenges JOIN station ON station.stationcode=tbl_challenges.stationID";
if($_SESSION['MM_UserGroup']=="Regional Supervisor")
$query_rsChallenges .= " WHERE tbl_challenges.stationID IN(SELECT station.stationcode FROM station WHERE station.region=(SELECT region FROM station WHERE station.stationcode='".$_SESSION['StationCode']."'))";
if($_SESSION['MM_UserGroup']=="In Charge")
$query_rsChallenges .= " WHERE tbl_challenges.stationID='".$_SESSION['StationCode']."' AND tbl_challenges.recordedBy=".$_SESSION['MM_UserID'];
$query_rsChallenges .= "$curmonth GROUP BY station.name";

$rsChallenges = mysql_query($query_rsChallenges, $conn_ura) or die(mysql_error());
$totalRows_rsChallenges = mysql_num_rows($rsChallenges);
$challenges=$focussedOps=$summary=" ";
$cnt1=$cnt2=$cnt3=1;
while ($row_rsChallenges = mysql_fetch_assoc($rsChallenges)){
	$challenges.="<tr><th scope='row'>".$row_rsChallenges['name']."</th><td>".$row_rsChallenges['CHALL']."</td></tr>";
	$focussedOps.="<tr><th scope='row'>".$row_rsChallenges['name']."</th><td>".$row_rsChallenges['OPS']."</td></tr>";
	$summary.="<tr><th scope='row'>".$row_rsChallenges['name']."</th><td>".$row_rsChallenges['SUMARY']."</td></tr>";
	$csv_table9.=$row_rsChallenges['name'].",".$row_rsChallenges['CHALL']."\n";
	$csv_table10.=$row_rsChallenges['name'].",".$row_rsChallenges['OPS']."\n";
	$csv_table11.=$row_rsChallenges['name'].",".$row_rsChallenges['SUMARY']."\n";
}
?>
  </p>
  <h3>CHALLENGES FACED </h3>
  <center>
  <table width="0" border="1" cellspacing="0">
  <?php $csv_table9="\nCHALLENGES FACED\n".$csv_table9; echo $challenges; ?>
  </table>
  </center>
  <p>&nbsp;</p>
  <h3>FOCUSED OPERATION UNDERTAKEN</h3>
  <center>
  <table width="0" border="1" cellspacing="0">
  <?php $csv_table10="\nFOCUSED OPERATION UNDERTAKEN\n".$csv_table10; echo $focussedOps; ?>
  </table>
  </center>
  <p>&nbsp;</p>
  <h3>SUMMARY OF EFFORTS UNDERTAKEN TO IMPROVE COMPLIANCE AT THE STATION</h3>
  <center>
  <table width="0" border="1" cellspacing="0">
  <?php $csv_table11="\nSUMMARY OF EFFORTS UNDERTAKEN TO IMPROVE COMPLIANCE AT THE STATION\n".$csv_table11; echo $summary; ?>
  </table>
  </center>
  <p>&nbsp;</p>
<?php mysql_free_result($rsChallenges); }?>
<form name="export" action="export.php" method="post">
  <input type="submit" value="Download Excel File">
  <input type="hidden" value="<?php echo $csv_hdr; ?>" name="csv_hdr">
  <input type="hidden" value="<?php echo $csv_output.$csv_table2.$csv_table4.$csv_table5.$csv_table6.$csv_table7.$csv_table8.$csv_table9.$csv_table10.$csv_table11; ?>" name="csv_output">
</form>
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

mysql_free_result($rsRegions);

?>
