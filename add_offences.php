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

$MM_restrictGoTo = "index.php";
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
  $insertSQL = sprintf("INSERT INTO tbl_offence (stationcode, rep_date, entry_no, file_no, topup, offender_names, nature_offence, sect_law, dutable, goodsnames, desc_goods, det_method, trans_means, goods_val, taxes, fines, total, rec_prn, remarks, modifiedBy) VALUES (%s, CURDATE(), %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
                       GetSQLValueString($_POST['station'], "int"),
                       GetSQLValueString($_POST['entry_no'], "text"),
                       GetSQLValueString($_POST['file_no'], "text"),
                       GetSQLValueString(isset($_POST['topup']) ? "true" : "", "defined","1","0"),
                       GetSQLValueString($_POST['offender_names'], "text"),
                       GetSQLValueString($_POST['nature_offence'], "text"),
                       GetSQLValueString($_POST['sect_law'], "text"),
                       GetSQLValueString(isset($_POST['dutable']) ? "true" : "", "defined","1","0"),
                       GetSQLValueString($_POST['goodsnames'], "text"),
                       GetSQLValueString($_POST['desc_goods'], "text"),
                       GetSQLValueString($_POST['det_method'], "text"),
                       GetSQLValueString($_POST['trans_means'], "text"),
                       GetSQLValueString($_POST['goods_val'], "int"),
                       GetSQLValueString($_POST['taxes'], "int"),
                       GetSQLValueString($_POST['fines'], "int"),
                       GetSQLValueString($_POST['total'], "int"),
                       GetSQLValueString($_POST['rec_prn'], "text"),
                       GetSQLValueString($_POST['remarks'], "text"),
                       GetSQLValueString($_POST['modifiedBy'], "int"));

  mysql_select_db($database_conn_ura, $conn_ura);
  $Result1 = mysql_query($insertSQL, $conn_ura) or die(mysql_error());

if ((isset($_POST["file_no"])) && ($_POST["topup"] != 1)) {
  $insertSQL = sprintf("INSERT INTO tbl_casenumbr (dummy) VALUES (%s)",
                       GetSQLValueString("dum", "text"));

  $Result1 = mysql_query($insertSQL, $conn_ura) or die(mysql_error());
}

  $insertGoTo = "insertdata_success.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
}

mysql_select_db($database_conn_ura, $conn_ura);
$query_rsOffenceNo = "SELECT tbl_casenumbr.`number` FROM tbl_casenumbr ORDER BY tbl_casenumbr.`number` DESC";
$rsOffenceNo = mysql_query($query_rsOffenceNo, $conn_ura) or die(mysql_error());
$row_rsOffenceNo = mysql_fetch_assoc($rsOffenceNo);

function offenceNO($stncode,$No){
	return $stncode."/OFF/".date("m/y/").$No;
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><!-- InstanceBegin template="/Templates/other_pages.dwt.php" codeOutsideHTMLIsLocked="false" -->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<!-- InstanceBeginEditable name="doctitle" -->
<title>URA Offences::.Offence Entry Form</title>
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
<!-- InstanceBeginEditable name="head" --><script type="text/javascript" src="SpryAssets/jquery.min.js"></script>
<script type="text/javascript">
$(document).ready(function(){
	$("#fines").change(function(){
		$("#total").val(parseInt($("#taxes").val())+parseInt($("#fines").val()));
		});
	$("#taxes").change(function(){
		$("#total").val(parseInt($("#fines").val())+parseInt($("#taxes").val()));
		});
		//If other offences then the person should specify which it was
	$("#nature_offence").change(function(){
		if($("#nature_offence").val()=="OTHER OFFENCES")
		$("#others").prop("disabled",false);
		else
		$("#others").prop("disabled",true);
	});
		//Whether it is a topup or not
	$("#topup").change(function(){
		if($("#topup").prop("checked")==true)
		$("#file_no").prop("disabled",true);
		else
		$("#file_no").prop("disabled",false);
	});
});
</script>

<!-- InstanceEndEditable -->
</head>

<body><div id="wrapper">
<div id="header"></div>
<div id="nav_bar"><?php echo $_SESSION['MM_Username']; ?> (<?php echo $_SESSION['user_names']; ?>) | <a href="loginsuccess.php">Home</a> | <a href="logout.php">Logout</a></div>
<div class="clearfloat"></div>
  <div id="content"><!-- InstanceBeginEditable name="formstablesreports" -->
  <center>
    <form id="form1" name="form1" method="POST" action="<?php echo $editFormAction; ?><?php echo $editFormAction; ?>">
      <fieldset><legend>OFFENCE ENTRY FORM</legend>
        <table width="0" border="0" class="fm_tbl">
          <tr>
            <td><label for="entry_no">ENTRY NUMBER:</label></td>
            <td><input type="text" name="entry_no" id="entry_no" /></td>
          </tr>
          <tr>
            <td>Topup?:</td>
            <td><input name="topup" type="checkbox" id="topup" style="width:auto;" value="1" /></td>
          </tr>
          <tr>
            <td><label for="file_no">OFFENCE NUMBER:</label></td>
            <td><input name="file_no" type="text" id="file_no" value="<?php echo offenceNO($_SESSION['StationCode'],$row_rsOffenceNo['number']); ?>" readonly="readonly" /></td>
            </tr>
          <tr>
            <td><label for="offender_names">OFFENDER'S NAMES:</label></td>
            <td><span id="sprytextfield3">
              <input type="text" name="offender_names" id="offender_names" />
              <span class="textfieldRequiredMsg">A value is required.</span></span></td>
            </tr>
          <tr>
            <td><label for="nature_offence">NATURE OF OFFENCE:</label></td>
            <td><span id="sprynoffnce">
              <select name="nature_offence" id="nature_offence">
                <option selected="selected">Select one...</option>
                <option value="OUT RIGHT SMUGGLING">OUT RIGHT SMUGGLING</option>
                <option value="UNDER VALUATION">UNDER VALUATION</option>
                <option value="MISDECLARATION">MISDECLARATION</option>
                <option value="CONCEALMENT">CONCEALMENT</option>
                <option value="OTHER OFFENCES">OTHER OFFENCES</option>
              </select>
              <br />
              <span class="selectRequiredMsg">Please select one...</span></span></td>
            </tr>
          <tr>
            <td>Specify:</td>
            <td><input name="others" type="text" disabled="disabled" id="others" /></td>
          </tr>
          <tr>
            <td><label for="sect_law">SECTION OF THE LAW:</label></td>
            <td><span id="sprytextfield5">
              <input type="text" name="sect_law" id="sect_law" />
              <br />
              <span class="textfieldRequiredMsg">A value is required.</span></span></td>
            </tr>
          <tr>
            <td>DUTABLE GOOD(S)?</td>
            <td><input type="checkbox" name="dutable" id="dutable" style="width:auto;" /></td>
          </tr>
          <tr>
            <td>NAME 0F CAPTURED GOODS</td>
            <td><span id="sprygoods">
              <input type="text" name="goodsnames" id="goodsnames" />
              <br />
              <span class="textfieldRequiredMsg">A value is required.</span></span></td>
          </tr>
          <tr>
            <td><label for="det_method">DESCRIPTION OF GOODS:</label></td>
            <td><span id="sprydesc_goods">
              <textarea name="desc_goods" id="desc_goods" cols="40" rows="5"></textarea>
              <br />
              <span class="textareaRequiredMsg">*</span></span></td>
            </tr>
          <tr>
            <td><label for="det_method">METHOD OF DETECTION</label>&nbsp;</td>
            <td><span id="sprytextfield6">
              <input name="det_method" type="text" id="det_method" />
              <br />
              <span class="textfieldRequiredMsg">A value is required.</span></span></td>
            </tr>
          <tr>
            <td><label for="trans_means">MEANS OF TRANSPORT:</label></td>
            <td><span id="sprytextfield7">
              <input type="text" name="trans_means" id="trans_means" />
              <br />
              <span class="textfieldRequiredMsg">A value is required.</span></span></td>
            </tr>
          <tr>
            <td><label for="goods_val">VALUE OF GOODS [$]:</label></td>
            <td><input type="text" name="goods_val" id="goods_val" /></td>
            </tr>
          <tr>
            <td><label for="taxes">TAXES:</label></td>
            <td><span id="sprytextfield9">
            <input name="taxes" type="text" id="taxes" value="0" />
            <br />
            <span class="textfieldRequiredMsg">A value is required.</span><span class="textfieldInvalidFormatMsg">Not a number.</span></span></td>
            </tr>
          <tr>
            <td height="32"><label for="fines">FINES:</label></td>
            <td><span id="sprytextfield10">
            <input name="fines" type="text" id="fines" value="0" />
            <br />
            <span class="textfieldRequiredMsg">A value is required.</span><span class="textfieldInvalidFormatMsg">Not a number.</span></span></td>
            </tr>
          <tr>
            <td><label for="total">TOTAL:</label></td>
            <td><span id="sprytextfield11">
              <input name="total" type="text" id="total" value="0" readonly="readonly" />
</span></td>
            </tr>
          <tr>
            <td><label for="rec_prn">RECEIPT /PRN:</label></td>
            <td><span id="sprytextfield12">
              <input type="text" name="rec_prn" id="rec_prn" />
              <br />
              <span class="textfieldRequiredMsg">A value is required.</span></span></td>
            </tr>
          <tr>
            <td><label for="remarks">REMARK[S]:</label></td>
            <td><span id="sprytextfield13"><span class="textfieldRequiredMsg">A value is required.</span></span><span id="spryremarks">
              <select name="remarks" id="remarks">
                <option>Select one...</option>
                <option value="Released">Released</option>
                <option value="Pending">Pending</option>
                <option value="Forfeited">Forfeited</option>
              </select>
              <br />
              <span class="selectRequiredMsg">Please select an item.</span></span></td>
            </tr>
          <tr>
            <td>&nbsp;</td>
            <td><input name="modifiedBy" type="hidden" id="modifiedBy" value="<?php echo $_SESSION['MM_UserID']; ?>" />
              <input name="station" type="hidden" id="station" value="<?php echo $_SESSION['StationCode']; ?>" /></td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td><div align="left">
              <input type="submit" name="Submit" id="button1" value="Submit" style="width:80px" />
              <input type="submit" name="reset" id="button2" value="Cancel" style="width:80px" />
            </div></td>
          </tr>
        </table>
  </fieldset>
      <input type="hidden" name="MM_insert" value="form1" />
    </form></center>
      <p>&nbsp;</p>
    
  <script type="text/javascript">
var sprytextfield3 = new Spry.Widget.ValidationTextField("sprytextfield3");
var sprytextfield5 = new Spry.Widget.ValidationTextField("sprytextfield5");
var sprytextfield6 = new Spry.Widget.ValidationTextField("sprytextfield6");
var sprytextarea1 = new Spry.Widget.ValidationTextarea("sprydesc_goods", {validateOn:["change"]});
var sprytextfield7 = new Spry.Widget.ValidationTextField("sprytextfield7", "none", {validateOn:["change"]});
var sprytextfield9 = new Spry.Widget.ValidationTextField("sprytextfield9", "integer", {validateOn:["change"]});
var sprytextfield10 = new Spry.Widget.ValidationTextField("sprytextfield10", "integer", {validateOn:["change"]});
var sprytextfield11 = new Spry.Widget.ValidationTextField("sprytextfield11", "none", {isRequired:false});
var sprytextfield12 = new Spry.Widget.ValidationTextField("sprytextfield12", "none", {validateOn:["change"]});
var sprytextfield13 = new Spry.Widget.ValidationTextField("sprytextfield13");
var spryselect1 = new Spry.Widget.ValidationSelect("spryremarks", {validateOn:["change"]});
var spryselect2 = new Spry.Widget.ValidationSelect("sprynoffnce", {validateOn:["change"]});
var sprytextfield1 = new Spry.Widget.ValidationTextField("sprygoods", "none", {validateOn:["change"]});
  </script>
  <!-- InstanceEndEditable --> </div>
  <!-- end #content -->
  <div class="clearfloat"></div>
<div id="footer"></div><!-- end #footer -->
</div><!-- end #wrapper -->
</body>
<!-- InstanceEnd --></html>
<?php
mysql_free_result($rsOffenceNo);
?>
