<?php
# FileName="Connection_php_mysql.htm"
# Type="MYSQL"
# HTTP="true"
$hostname_conn_ura = "localhost";
$database_conn_ura = "uraoffences";
$username_conn_ura = "root";
$password_conn_ura = "admin";
$conn_ura = mysql_pconnect($hostname_conn_ura, $username_conn_ura, $password_conn_ura) or trigger_error(mysql_error(),E_USER_ERROR); 
?>