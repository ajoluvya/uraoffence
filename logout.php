<?php
// *** Logout the current user.
  if (isset($_SERVER['QUERY_STRING'])) {
    $msg= $_SERVER['QUERY_STRING'];
  }
  else
  $msg="msg=You have been logged out";
  $logoutGoTo = "index.php?".$msg;
if (!isset($_SESSION)) {
  session_start();
}
$_SESSION['MM_Username'] = NULL;
$_SESSION['MM_UserGroup'] = NULL;
$_SESSION['PrevUrl'] = NULL;
$_SESSION['MM_UserID'] = NULL;   
$_SESSION['user_names'] = NULL;
$_SESSION['StationCode'] = NULL;
unset($_SESSION['MM_Username']);
unset($_SESSION['MM_UserGroup']);
unset($_SESSION['PrevUrl']);
unset($_SESSION['MM_UserID']);   
unset($_SESSION['user_names']);
unset($_SESSION['StationCode']);
if ($logoutGoTo != "") {header("Location: $logoutGoTo");
exit;
}
?>