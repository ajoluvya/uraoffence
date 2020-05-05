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

if ((isset($_GET['offId'])) && ($_GET['offId'] != "")) {
  $deleteSQL = sprintf("DELETE FROM tbl_offence WHERE off_id=%s",
                       GetSQLValueString($_GET['offId'], "int"));
					   
  $deleteSQL2 = sprintf("DELETE FROM tbl_capturedgoods WHERE off_id=%s",
                       GetSQLValueString($_GET['offId'], "int"));

  mysql_select_db($database_conn_ura, $conn_ura);
  $Result1 = mysql_query($deleteSQL, $conn_ura) or die(mysql_error());
  $Result2 = mysql_query($deleteSQL2, $conn_ura) or die(mysql_error());

  $deleteGoTo = "report_offences.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $deleteGoTo .= (strpos($deleteGoTo, '?')) ? "&" : "?";
    $deleteGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $deleteGoTo));
}
//when editing an offence and a good must be delete, we invoke this query
if ((isset($_GET['gid'])) && ($_GET['gid'] != "")) {			   
  $deleteSQL2 = sprintf("DELETE FROM tbl_capturedgoods WHERE off_id=%s",
                       GetSQLValueString($_GET['gid'], "int"));

  mysql_select_db($database_conn_ura, $conn_ura);
  $Result2 = mysql_query($deleteSQL2, $conn_ura) or die(mysql_error());
}
if ((isset($_GET['stationcode'])) && ($_GET['stationcode'] != "")) {
  $deleteSQL = sprintf("DELETE FROM station WHERE stationcode=%s",
                       GetSQLValueString($_GET['stationcode'], "text"));

  mysql_select_db($database_conn_ura, $conn_ura);
  $Result1 = mysql_query($deleteSQL, $conn_ura) or die(mysql_error());

  $deleteGoTo = "report_wstations.php?msg=Station record deleted";
  if (isset($_SERVER['QUERY_STRING'])) {
    $deleteGoTo .= (strpos($deleteGoTo, '?')) ? "&" : "?";
    $deleteGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $deleteGoTo));
}

if ((isset($_GET['regId'])) && ($_GET['regId'] != "")) {
  $deleteSQL = sprintf("DELETE FROM region WHERE rid=%s",
                       GetSQLValueString($_GET['regId'], "int"));

  mysql_select_db($database_conn_ura, $conn_ura);
  $Result1 = mysql_query($deleteSQL, $conn_ura) or die(mysql_error());

  $deleteGoTo = "report_regions.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $deleteGoTo .= (strpos($deleteGoTo, '?')) ? "&" : "?";
    $deleteGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $deleteGoTo));
}
?>