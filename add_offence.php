<?php require_once('Connections/conn_ura.php'); ?>
<?php
if (!isset($_SESSION)) {
  session_start();
}
$MM_authorizedUsers = "Officer";
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



$MM_restrictGoTo = "index.php?msg=Only Officers can add offences";

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

$query_rsAlertUnits = "SELECT tbl_alertunit.ID, tbl_alertunit.unit FROM tbl_alertunit";

$rsAlertUnits = mysql_query($query_rsAlertUnits, $conn_ura) or die(mysql_error());

$row_rsAlertUnits = mysql_fetch_assoc($rsAlertUnits);

$totalRows_rsAlertUnits = mysql_num_rows($rsAlertUnits);



$query_rsPrevEntry = "SELECT file_no FROM tbl_offence WHERE off_id=(SELECT MAX(off_id) FROM tbl_offence)";

$rsPrevEntry = mysql_query($query_rsPrevEntry, $conn_ura) or die(mysql_error());

$row_rsPrevEntry= mysql_fetch_assoc($rsPrevEntry);



$editFormAction = $_SERVER['PHP_SELF'];

if (isset($_SERVER['QUERY_STRING'])) {

  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);

}



if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {

	$fileno="";

	if ($_POST["topup"] != 1) {

		mysql_select_db($database_conn_ura, $conn_ura);

		$query_rsOffenceNo = "SELECT number FROM tbl_casenumbr WHERE stationcode='".$_SESSION['StationCode']."' ORDER BY tbl_casenumbr.number DESC";

		$rsOffenceNo = mysql_query($query_rsOffenceNo, $conn_ura) or die(mysql_error());

		$row_rsOffenceNo = mysql_fetch_assoc($rsOffenceNo);

		$totalRows_rsOffenceNo = mysql_num_rows($rsOffenceNo);

		$offNo=($totalRows_rsOffenceNo>0)?$row_rsOffenceNo['number']:'001';

		$fileno= $_SESSION['StationCode']."/C37/".date("m/Y-").$offNo;

		

		mysql_free_result($rsOffenceNo);

		$insertSQL = sprintf("INSERT INTO tbl_casenumbr (number, stationcode) VALUES (%s,%s)",

                       GetSQLValueString($offNo+1, "int"),

                       GetSQLValueString($_SESSION['StationCode'], "text"));

					   

		$Result1 = mysql_query($insertSQL, $conn_ura) or die(mysql_error());

}

	/*$natureOfOffence=strtoupper((($_POST['nature_offence']!="OTHER OFFENCES")?$_POST['nature_offence']:$_POST['others']));

  $insertSQL = sprintf("INSERT INTO tbl_offence (stationcode, rep_date, alert, alertOrigin, entry_no, file_no, topup, offender_names, nature_offence, sect_law, dutable, det_method, trans_means, fines, rec_prn, remarks, modifiedBy) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",

                       GetSQLValueString($_POST['station'], "text"),

                       GetSQLValueString(isset($_POST['date']) ?$_POST['date']:date("Y-m-d"), "date"),

                       GetSQLValueString(isset($_POST['alert']) ? "true" : "", "defined","1","0"),

                       GetSQLValueString($_POST['unit'], "int"),

                       GetSQLValueString(strtoupper($_POST['entry_no']), "text"),

                       GetSQLValueString(strtoupper($fileno), "text"),

                       GetSQLValueString(isset($_POST['topup']) ? "true" : "", "defined","1","0"),

                       GetSQLValueString(strtoupper($_POST['offender_names']), "text"),

                       GetSQLValueString($natureOfOffence, "text"),

                       GetSQLValueString($_POST['sect_law'], "text"),

                       GetSQLValueString(isset($_POST['dutable']) ? "true" : "", "defined","1","0"),

                       GetSQLValueString(strtoupper($_POST['det_method']), "text"),

                       GetSQLValueString(strtoupper(isset($_POST['trans_means'])?$_POST['trans_means']:" "), "text"),

                       GetSQLValueString($_POST['fines'], "int"),

                       GetSQLValueString(strtoupper($_POST['rec_prn']), "text"),

                       GetSQLValueString(strtoupper($_POST['remarks']), "text"),

                       GetSQLValueString($_POST['modifiedBy'], "int"));

					     mysql_select_db($database_conn_ura, $conn_ura);

  $Result1 = mysql_query($insertSQL, $conn_ura) or die(mysql_error());

  

$query_rsOffenceID = "SELECT off_id FROM tbl_offence WHERE stationcode='".$_SESSION['StationCode']."' AND rep_date=CURDATE() AND offender_names='".strtoupper($_POST['offender_names'])."' AND nature_offence ='$natureOfOffence' AND sect_law='".$_POST['sect_law']."'";*/



$date=isset($_POST['date']) ?$_POST['date']:date("Y-m-d"); 

$natureOfOffence=strtoupper((($_POST['nature_offence']!="OTHER OFFENCES")?$_POST['nature_offence']:$_POST['others'])); $insertSQL = sprintf("INSERT INTO tbl_offence (stationcode, rep_date, alert, alertOrigin, entry_no, file_no, topup, offender_names, nature_offence, sect_law, dutable, det_method, trans_means, fines, rec_prn, remarks, modifiedBy) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",

			   GetSQLValueString($_POST['station'], "text"),

			   GetSQLValueString($date, "date"),

			   GetSQLValueString(isset($_POST['alert']) ? "true" : "", "defined","1","0"),

			   GetSQLValueString($_POST['unit'], "int"),

			   GetSQLValueString(strtoupper($_POST['entry_no']), "text"), 

			   GetSQLValueString(strtoupper($fileno), "text"), 

			   GetSQLValueString(isset($_POST['topup']) ? "true" : "", "defined","1","0"),

			   GetSQLValueString(strtoupper($_POST['offender_names']), "text"), 

			   GetSQLValueString($natureOfOffence, "text"), 

			   GetSQLValueString($_POST['sect_law'], "text"),

			   GetSQLValueString(isset($_POST['dutable']) ? "true" : "", "defined","1","0"),

			   GetSQLValueString(strtoupper($_POST['det_method']), "text"),

			   GetSQLValueString(strtoupper(isset($_POST['trans_means'])?$_POST['trans_means']:" "), "text"),

			   GetSQLValueString($_POST['fines'], "int"),

			   GetSQLValueString(strtoupper($_POST['rec_prn']), "text"), 

			   GetSQLValueString(strtoupper($_POST['remarks']), "text"),

		       GetSQLValueString($_POST['modifiedBy'], "int"));

			

			 mysql_select_db($database_conn_ura, $conn_ura);

			 

			  $Result1 = mysql_query($insertSQL, $conn_ura) or die(mysql_error()); 

			  $query_rsOffenceID = "SELECT off_id FROM tbl_offence WHERE stationcode='".$_SESSION['StationCode']."' AND rep_date='$date' AND offender_names='".strtoupper($_POST['offender_names'])."' AND nature_offence ='$natureOfOffence' AND sect_law='".$_POST['sect_law']."'";





$rsOffenceID = mysql_query($query_rsOffenceID, $conn_ura) or die(mysql_error());

$row_rsOffenceID = mysql_fetch_assoc($rsOffenceID);





foreach($_POST['goodsnames'] as $cnt => $goodsnames) {

  $insertSQL = sprintf("INSERT INTO tbl_capturedgoods (off_id, good_name, good_descpn, category, goods_val, taxes) VALUES (%s, %s, %s, %s, %s, %s)",

                       GetSQLValueString($row_rsOffenceID['off_id'], "int"),

                       GetSQLValueString(strtoupper($goodsnames), "text"),

                       GetSQLValueString(strtoupper($_POST['desc_goods'][$cnt]), "text"),

                       GetSQLValueString(strtoupper($_POST['category'][$cnt]), "text"),

                       GetSQLValueString($_POST['goods_val'][$cnt], "int"),

                       GetSQLValueString($_POST['taxes'][$cnt], "int"));

					   

  $Result1 = mysql_query($insertSQL, $conn_ura) or die(mysql_error());

}

  $insertGoTo = "insert_success.php?off_id=".$row_rsOffenceID['off_id'];

  if (isset($_SERVER['QUERY_STRING'])) {

    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";

    $insertGoTo .= $_SERVER['QUERY_STRING'];

  }

  header(sprintf("Location: %s", $insertGoTo));

mysql_free_result($rsOffenceID);

}



?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml"><!-- InstanceBegin template="/Templates/other_pages.dwt.php" codeOutsideHTMLIsLocked="false" -->

<head>

<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

<!-- InstanceBeginEditable name="doctitle" -->

<title>URA Offences.::.Offence Entry Form</title>

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

<link type="text/css" href="css/jquery.datepick.css" rel="stylesheet" />

<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>

<script type="text/javascript" src="SpryAssets/jquery.min.js"></script>

<script type="text/javascript" src="SpryAssets/jquery.datepick.js"></script>

<script type="text/javascript">

$(function() {

	$('#date').datepick();

});

$(document).ready(function(){

		$("#alertOrigin").hide(50); $("#specify").hide(50);

	$("#nature_offence").change(function(){

		if($("#nature_offence").val()=="OTHER OFFENCES"){

			$("#specify").show(50);

			$("#others").removeAttr("disabled");

		}

		else{

			$("#others").prop("disabled",true);

			$("#specify").hide(50);

		}

	});

		//Whether it is a topup or not

	$("#topup").change(function(){

		if($("#topup").prop("checked")==true)

		$("#file_no").prop("disabled",true);

		else

		$("#file_no").removeProp("disabled");

	});

	$("#alert").change(function(){

		if($(this).prop("checked")==true){

			$("#alertOrigin").show(50);

			//$("#det_method").val(" ");

			//$("#det_method").attr("readonly","readonly");

		}

		else {

			$("#alertOrigin").hide(50);

			//$("#det_method").val("");

			//$("#det_method").removeAttr("readonly");

		}

	});

	$("#goods_val,#sect_law").change(function(){

		total();

	})

	$("#dutable").change(function(){

		if($(this).prop("checked")==true)

		$("#dtbl").html("Dutable");

		else

		$("#dtbl").html("Non-dutable");

	});

});

function total(){

	var goods_val=0;

		$.each($("#goodsadded").children("input:hidden"),function(i,e) {

			if(e.name=="goods_val[]")

				goods_val+=parseInt(e.value);

        });	

		if($("#goods_val").val()!="")

			$("#totalValue").val(goods_val+parseInt($("#goods_val").val()));

		else

		$("#totalValue").val(goods_val);

			if($("#sect_law").val()=="200"){

				$("#fines").val(0.5*parseInt($("#totalValue").val())*2600);

				$("#fines").prop("readonly", true);

			}

			$("#fines").removeAttr("readonly");

}

	var itemNum = 0;

	function addItem(){

		if($("#goodsnames").val()!=""&&$("#desc_goods").val()!=""&&$("#goods_val").val()!=""&&$("#taxes").val()!=""){

		itemNum ++;

		var row = '<span id="rowNum'+itemNum+'" onclick="showItem('+itemNum+');">'+$("#goodsnames").val()+

		'<input type="hidden" name="hscode[]" value="'+$("#hscode").val()+

		'"><input type="hidden" name="goodsnames[]" value="'+$("#goodsnames").val()+

		'"><input type="hidden" name="desc_goods[]" value="'+$("#desc_goods").val()+

		'"><input type="hidden" name="unit_of_measure[]" value="'+$("#unit_of_measure").val()

		+'"><input type="hidden" name="category[]" value="'+$("#category").val()+

		'"><input type="hidden" name="goods_val[]" value="'+$("#goods_val").val()+

		'"><input type="hidden" name="taxes[]" id="tax'+itemNum+'" value="'+$("#taxes").val()+

		'"> <input type="button" value="X" title="Remove" style="font-size:9px; width:auto; padding:0;" onclick="removeItem('+itemNum+');"></span>';

		$('#goodsadded').append(row);

		$("#hscode").val("");

		$("#goodsnames").val("");

		$("#desc_goods").val("");

		$("#unit_of_measure").val("");

		$("#category").prop("selectedIndex", 0);

		$("#goods_val").val(0);

		$("#taxes").val(0);

		}

		else

		alert("Please ensure all the fields for a good are filled out");

	}

	function removeItem(rnum) {

		$("#rowNum"+rnum).remove();

	}

	function showItem(rnum) {

		addItem();

		$.each($("#rowNum"+rnum).children("input:hidden"),function(i,e) {

			switch(e.name){

				case "goodsnames[]":

				$("#goodsnames").val(e.value);

				break;

				case "desc_goods[]":

				$("#desc_goods").val(e.value);

				break;

				case "unit_of_measure[]":

				$("#unit_of_measure").val(e.value);

				break;

				case "goods_val[]":

				$("#goods_val").val(e.value);

				break;

				case "taxes[]":

				$("#taxes").val(e.value);

				break;

				case "category[]":

				$("#category").val(e.value);

				break;

				case "hscode[]":

				$("#hscode").val(e.value);

				break;

			}

        });

		//$("#total").val(parseInt($("#total").val())+parseInt($('#tax'+rnum).val()));	

		removeItem(rnum);	

	}

</script>



<!-- InstanceEndEditable -->

</head>



<body><div id="wrapper">

<div id="header"></div>

<div id="nav_bar"><?php echo $_SESSION['MM_Username']; ?> (<?php echo $_SESSION['user_names']; ?>) | <a href="loginsuccess.php">Home</a> | <a href="logout.php">Logout</a></div>

<div class="clearfloat"></div>

  <div id="content"><!-- InstanceBeginEditable name="formstablesreports" -->

    <h3>OFFENCE ENTRY FORM</h3>

    <form id="form1" name="form1" method="POST" action="<?php echo $editFormAction; ?><?php echo $editFormAction; ?>">

      <fieldset>

  <center>

        <table width="0" border="0" class="fm_tbl">

          <tr>

            <td><label for="entry_no">ENTRY NUMBER:</label></td>

            <td><input type="text" name="entry_no" id="entry_no" /></td>

            </tr>

          <tr>

            <td>DATE:</td>

            <td><input name="date" type="text" id="date" value="<?php echo date('Y-m-d'); ?>" /></td>

            </tr>

          <tr>

            <td>ALERT?:</td>

            <td><input name="alert" type="checkbox" id="alert" style="width:auto;" value="1" /></td>

            </tr>

          <tr id="alertOrigin">

            <td><label for="unit">ALERT ORIGIN:</label></td>

            <td>

              <select name="unit" id="unit">

                <option value="100">Select unit...</option>

                <?php

do {  

?>

                <option value="<?php echo $row_rsAlertUnits['ID']?>"><?php echo $row_rsAlertUnits['unit']?></option>

                <?php

} while ($row_rsAlertUnits = mysql_fetch_assoc($rsAlertUnits));

  $rows = mysql_num_rows($rsAlertUnits);

  if($rows > 0) {

      mysql_data_seek($rsAlertUnits, 0);

	  $row_rsAlertUnits = mysql_fetch_assoc($rsAlertUnits);

  }

?>

                </select>

            </td>

            </tr>

          <tr>

            <td>PREVIOUS FILE NUMBER</td>

            <td><strong><?php echo $row_rsPrevEntry['file_no']; ?></strong></td>

            </tr>

          <tr>

            <td>TOPUP?:</td>

            <td><input name="topup" type="checkbox" id="topup" style="width:auto;" value="1" /></td>

            </tr>

          <tr>

            <td><label for="offender_names">OFFENDER'S NAMES:</label></td>

            <td><span id="sprytextfield3">

              <input type="text" name="offender_names" id="offender_names" />

              <span class="textfieldRequiredMsg"><br />

              Names are required.</span></span></td>

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

                <option value="MISCLASSIFICATION">MISCLASSIFICATION</option>

                <option value="OTHER OFFENCES">OTHER OFFENCES</option>

                </select>

              <br />

              <span class="selectRequiredMsg">Please select one...</span></span></td>

            </tr>

          <tr id="specify">

            <td>Specify:</td>

            <td><input name="others" type="text" disabled="disabled" id="others" /></td>

            </tr>

          <tr>

            <td><label for="sect_law">SECTION OF THE LAW:</label></td>

            <td><span id="sprytextfield5">

              <input type="text" name="sect_law" id="sect_law" list="sectn_law"/>

              <datalist id="sectn_law">

                <option value="1"/> <option value="2"/> <option value="3"/> <option value="4"/> <option value="5"/> <option value="6"/> <option value="7"/> <option value="8"/> <option value="9"/> <option value="10"/> <option value="11"/> <option value="12"/> <option value="13"/> <option value="14"/> <option value="15"/> <option value="16"/> <option value="17"/> <option value="18"/> <option value="19"/> <option value="20"/> <option value="21"/> <option value="22"/> <option value="23"/> <option value="24"/> <option value="25"/> <option value="26"/> <option value="27"/> <option value="28"/> <option value="29"/> <option value="30"/> <option value="31"/> <option value="32"/> <option value="33"/> <option value="34"/> <option value="35"/> <option value="36"/> <option value="37"/> <option value="38"/> <option value="39"/> <option value="40"/> <option value="41"/> <option value="42"/> <option value="43"/> <option value="44"/> <option value="45"/> <option value="46"/> <option value="47"/> <option value="48"/> <option value="49"/> <option value="50"/> <option value="51"/> <option value="52"/> <option value="53"/> <option value="54"/> <option value="55"/> <option value="56"/> <option value="57"/> <option value="58"/> <option value="59"/> <option value="60"/> <option value="61"/> <option value="62"/> <option value="63"/> <option value="64"/> <option value="65"/> <option value="66"/> <option value="67"/> <option value="68"/> <option value="69"/> <option value="70"/> <option value="71"/> <option value="72"/> <option value="73"/> <option value="74"/> <option value="75"/> <option value="76"/> <option value="77"/> <option value="78"/> <option value="79"/> <option value="80"/> <option value="81"/> <option value="82"/> <option value="83"/> <option value="84"/> <option value="85"/> <option value="86"/> <option value="87"/> <option value="88"/> <option value="89"/> <option value="90"/> <option value="91"/> <option value="92"/> <option value="93"/> <option value="94"/> <option value="95"/> <option value="96"/> <option value="97"/> <option value="98"/> <option value="99"/> <option value="100"/> <option value="101"/> <option value="102"/> <option value="103"/> <option value="104"/> <option value="105"/> <option value="106"/> <option value="107"/> <option value="108"/> <option value="109"/> <option value="110"/> <option value="111"/> <option value="112"/> <option value="113"/> <option value="114"/> <option value="115"/> <option value="116"/> <option value="117"/> <option value="118"/> <option value="119"/> <option value="120"/> <option value="121"/> <option value="122"/> <option value="123"/> <option value="124"/> <option value="125"/> <option value="126"/> <option value="127"/> <option value="128"/> <option value="129"/> <option value="130"/> <option value="131"/> <option value="132"/> <option value="133"/> <option value="134"/> <option value="135"/> <option value="135/203"/> <option value="136"/> <option value="137"/> <option value="138"/> <option value="139"/> <option value="140"/> <option value="141"/> <option value="142"/> <option value="143"/> <option value="144"/> <option value="145"/> <option value="146"/> <option value="147"/> <option value="148"/> <option value="149"/> <option value="150"/> <option value="151"/> <option value="152"/> <option value="153"/> <option value="154"/> <option value="155"/> <option value="156"/> <option value="157"/> <option value="158"/> <option value="159"/> <option value="160"/> <option value="161"/> <option value="162"/> <option value="163"/> <option value="164"/> <option value="165"/> <option value="166"/> <option value="167"/> <option value="168"/> <option value="169"/> <option value="170"/> <option value="171"/> <option value="172"/> <option value="173"/> <option value="174"/> <option value="175"/> <option value="176"/> <option value="177"/> <option value="178"/> <option value="179"/> <option value="180"/> <option value="181"/> <option value="182"/> <option value="183"/> <option value="184"/> <option value="185"/> <option value="186"/> <option value="187"/> <option value="188"/> <option value="189"/> <option value="190"/> <option value="191"/> <option value="192"/> <option value="193"/> <option value="194"/> <option value="195"/> <option value="196"/> <option value="197"/> <option value="198"/> <option value="199"/> <option value="200"/><option value="200/203"/>

                </datalist>

              <br />

              <span class="textfieldRequiredMsg">Required.</span></span></td>

            </tr>

          <tr>

            <td>DUTABLE GOOD(S)?</td>

            <td><input type="checkbox" name="dutable" id="dutable" style="width:auto;" />

              &nbsp;&nbsp;<span id="dtbl">Non-dutable</span></td>

            </tr>

          <tr>

            <td><label for="det_method">METHOD OF DETECTION</label>&nbsp;</td>

            <td><span id="sprytextfield6">

              <input name="det_method" type="text" id="det_method" list="detmethod"/>

              <datalist id="detmethod">

                <option value="AMBUSH"/>

                <option value="CHECK POINT SEARCH"/>

                <option value="DOCUMENT CHECK"/>

                </datalist>

              <br />

              <span class="textfieldRequiredMsg">Method required.</span></span></td>

            </tr>

          <tr>

            <td><label for="trans_means">CONVEYANCE / MEANS OF TRANSPORT:</label></td>

            <td><span id="sprytextfield7">

              <input type="text" name="trans_means" id="trans_means"  list="transMeans"/>

              <datalist id="transMeans">
              
                <option value="AIR"/>
                <option value="MAIL"/>
                <option value="RAIL TRAIN"/>
                <option value="VEHICLE"/>
                <option value="VESSEL"/>
                <option value="OTHERS"/>

                </datalist>

              <br />

              <span class="textfieldRequiredMsg">Transport means required.</span></span></td>

            </tr>

          <tr id="goodsrow">

            <td>&nbsp;</td>

            <td id="goodsadded">&nbsp;</td>

            </tr>

          <tr>

            <td><label for="hscode">HS CODE:</label></td>

            <td><span id="sprytextfield2">

              <input type="text" name="hscode[]" id="hscode" />

              <span class="textfieldRequiredMsg"><br />

              HS code is equired.</span></span></td>

            </tr>

          <tr>

            <td>TYPE OF GOOD:</td>

            <td><span id="sprygoods">

              <input type="text" name="goodsnames[]" id="goodsnames" list="capturedGd" />

              <datalist id="capturedGd">

                <option value="IRISH POTATOES"/>

                <option value="PETROLEUM JELLY"/>

                <option value="RICE"/>

                <option value="WHEAT FLOUR"/>

                <option value="VEHICLE"/>

                <option value="CAR"/>

                </datalist>

              <br />

              <span class="textfieldRequiredMsg">Required</span></span></td>

            </tr>

          <tr>

            <td><label for="det_method">DESCRIPTION:</label></td>

            <td><span id="sprydesc_goods">

              <textarea name="desc_goods[]" id="desc_goods" cols="35" rows="2"></textarea>

              <br />

              <span class="textareaRequiredMsg">Required</span></span></td>

            </tr>

                   

          <tr>

            <td><label for="unit_of_measure">UNIT OF MEASURE:</label></td>

            <td><span id="sprytextfield4">

              <input type="text" name="unit_of_measure[]" id="unit_of_measure"  list="measureUnit" />

              <datalist id="measureUnit">

                <option value="Kg"/>

                <option value="Ltr"/>

                <option value="Carton"/>

                <option value="Pkts"/>

                </datalist>

              <br />

              <span class="textfieldRequiredMsg">Required.</span></span></td>

          </tr>

          <tr>

            <td><label for="category[]">GOODS CATEGORY:</label></td>

            <td><select name="category[]" id="category">

              <option value="Nill">Nill</option>
              
              <option value="Beverages">Beverages</option>
              <option value="Cites">Cites</option>
              <option value="Currency">Currency</option>
              <option value="Hazardous Material">Hazardous Material</option>
              <option value="IPR">IPR</option>
              <option value="Ponography/paedophilia">Ponography/paedophilia</option>
              <option value="Radioactive & Nuclear Material">Radioactive & Nuclear Material</option>

              <option value="Pharmaceuticals">Pharmaceuticals</option>

              <option value="Narcotics">Narcotics</option>

              <option value="Prohibited">Prohibited</option>

              <option value="Restricted Goods">Restricted Goods</option>

              <option value="Counterfeit Goods">Counterfeit Goods</option>
              <option value="Weapons & Explosives">Weapons & Explosives</option>

            </select></td>

            </tr>

          <tr>

            <td><label for="goods_val">VALUE OF GOODS [$]:</label></td>

            <td><input type="text" name="goods_val[]" id="goods_val" /></td>

            </tr>

          <tr>

            <td><label for="taxes">TAXES:</label></td>

            <td><span id="sprytextfield9">

              <input name="taxes[]" type="text" id="taxes" value="0" />

              <br />

              <span class="textfieldRequiredMsg">Tax value is required.</span><span class="textfieldInvalidFormatMsg">Not a number.</span></span></td>

            </tr>

          <tr>

            <td height="10">&nbsp;</td>

            <td><input type="button" name="button" id="addbtn" value="+" onclick="addItem()" style="font-size:9px; width:auto; padding:0;" title="Add another good"/>

              another good</td>

            </tr>

          <tr>

            <td height="32"><label for="totalValue">TOTAL VALUE OF GOODS[$]:</label></td>

            <td><input name="totalValue" type="text" id="totalValue" readonly="readonly" /></td>

            </tr>

          <tr>

            <td height="32"><label for="fines">FINES [Ushs]:</label></td>

            <td><span id="sprytextfield10">

              <input name="fines" type="text" id="fines" value="0" />

              <br />

              <span class="textfieldRequiredMsg">Fine is required.</span><span class="textfieldInvalidFormatMsg">Not a number.</span></span></td>

            </tr>

          <tr>

            <td><label for="rec_prn">RECEIPT/PRN:</label></td>

            <td><span id="sprytextfield12">

              <input type="text" name="rec_prn" id="rec_prn" />

              <br />

              <span class="textfieldRequiredMsg">PRN is required.</span></span></td>

            </tr>

          <tr>

            <td><label for="remarks">REMARK[S]:</label></td>

            <td><span id="spryremarks">

              <select name="remarks" id="remarks">

                <option>Select one...</option>

                <option value="Released">Released</option>

                <option value="Pending">Pending</option>

                <option value="Forfeited">Forfeited</option>

                </select>

              <br />

              <span class="selectRequiredMsg">Please select a remark.</span></span></td>

            </tr>

          <tr>

            <td>&nbsp;</td>

            <td><input name="modifiedBy" type="hidden" id="modifiedBy" value="<?php echo $_SESSION['MM_UserID']; ?>" />

              <input name="station" type="hidden" id="station" value="<?php echo $_SESSION['StationCode']; ?>" /></td>

            </tr>

          <tr>

            <td>&nbsp;</td>

            <td><div align="left">

              <input name="Submit" type="submit" class="submissions" id="button1" style="width:80px" value="Submit" />

              <input name="reset" type="reset" class="submissions" id="button2" style="width:80px" value="Cancel" />

            </div></td>

            </tr>

        </table></center>

  </fieldset>

      <input type="hidden" name="MM_insert" value="form1" />

    </form>

    <p>&nbsp;</p>

    

  <script type="text/javascript">

var sprytextfield3 = new Spry.Widget.ValidationTextField("sprytextfield3");

var sprytextfield5 = new Spry.Widget.ValidationTextField("sprytextfield5");

var sprytextfield6 = new Spry.Widget.ValidationTextField("sprytextfield6");

var sprytextarea1 = new Spry.Widget.ValidationTextarea("sprydesc_goods", {validateOn:["change"]});

var sprytextfield7 = new Spry.Widget.ValidationTextField("sprytextfield7", "none", {isRequired:false});

var sprytextfield9 = new Spry.Widget.ValidationTextField("sprytextfield9", "integer", {validateOn:["change"]});

var sprytextfield10 = new Spry.Widget.ValidationTextField("sprytextfield10", "integer", {validateOn:["change"]});

var sprytextfield12 = new Spry.Widget.ValidationTextField("sprytextfield12", "none", {validateOn:["change"]});

var spryselect1 = new Spry.Widget.ValidationSelect("spryremarks", {validateOn:["change"]});

var spryselect2 = new Spry.Widget.ValidationSelect("sprynoffnce", {validateOn:["change"]});

var sprytextfield1 = new Spry.Widget.ValidationTextField("sprygoods", "none", {validateOn:["change"]});

var sprytextfield2 = new Spry.Widget.ValidationTextField("sprytextfield2");

var sprytextfield4 = new Spry.Widget.ValidationTextField("sprytextfield4");

  </script>

  <!-- InstanceEndEditable --> </div>

  <!-- end #content -->

  <div class="clearfloat"></div>

<div id="footer"></div><!-- end #footer -->

</div><!-- end #wrapper -->

</body>

<!-- InstanceEnd --></html>

<?php

mysql_free_result($rsAlertUnits);



mysql_free_result($rsPrevEntry);

?>

