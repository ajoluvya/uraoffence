<!DOCTYPE html><head>
<meta charset="utf-8">
<title>Orders Table</title>
</head>
    
<table align="center" border="1" cellpadding="0" cellspacing="0" width="90%">
	<tr class="dataTableRow">
		<td class="main" width="10%"><b>Name of good</b></td>
		<td class="main" width="15%">Description of Goods</td>
		<td class="main" width="10%">Value</td>
		<td class="main" width="10%">Tax Value</td>
	</tr>
<?php 
//Now that we've created such a nice heading for our html table, lets create a heading for our csv table
    $csv_hdr = "Name of good, Description of Good's, Value, Tax Value";
//Quickly create a variable for our output that'll go into the CSV file (we'll make it blank to start).
    $csv_output="";
  
// Ok, we're done with the table heading, lets connect to the database
    $database="uraoffences";
    mysql_connect("localhost","root","admin");
    mysql_select_db("$database");
    mysql_set_charset('utf8');
    mysql_query('SET NAMES UTF-8');

// Lets say we wanted a table with all orders, their products and totals...a summary report of sorts
    $result=mysql_query("SELECT tbl_capturedgoods.good_name, tbl_capturedgoods.good_descpn, tbl_capturedgoods.goods_val, tbl_capturedgoods.taxes FROM tbl_capturedgoods");

// If our query has some results, lets store them in array of rows.
    if (mysql_num_rows($result) > 0) {
    
        //While our rows array has stuff in it...meaning it has column data, lets print it to each of the cells in our table
        while ($row = mysql_fetch_assoc($result)) {
?> 
        <tr>
            <td align="left" valign="center">
            <br><?php echo $row['good_name']; //here we are displaying the contents of the field or column in our rows array for a particular row.
			//while we're at it we might as well store the data in comma separated values (csv) format in the csv_output variable for later use.
            $csv_output .= $row['good_name'] . ", ";?>
            </td>
            <td align="left" valign="center">
            <br><?php echo $row['good_descpn']; //repeat for all remaining fields or columns we have headings for...
            $csv_output .= $row['good_descpn'] . ", ";?>
            </td>
            <td align="left" valign="center">
            <br><?php echo $row['goods_val']; //repeat for all remaining fields or columns we have headings for...
            $csv_output .= $row['goods_val'] . ", ";?>
            </td>
            <td align="left" valign="center">
            <br><?php echo $row['taxes']; //repeat for all remaining fields or columns we have headings for...
            $csv_output .= $row['taxes'] . "\n"; //ensure the last column entry starts a new line ?>
            </td>
        </tr>
<?php
        } //closing while loop
    } //closing if stmnt
?>
    <!--closing the table-->
    </table>   
<?php 
/*
Here is the important part. we've got the 2 variables (csv_hdr & csv_output) to create our csv file, but we can't do it in this file.
Why? Because the header for this file has already been sent and will show up in our csv file if we generate it on this page. We don't
want any html header in our csv file, so we've got to post our 2 variables to another php page (export.php) on which we generate our csv
file.

Here's the code for a form & button that'll post our 2 variables as hidden _POST to export.php.
*/
?>
<br />
<center>
<form name="export" action="export.php" method="post">
    <input type="submit" value="Export table to CSV">
    <input type="hidden" value="<?php echo $csv_hdr; ?>" name="csv_hdr">
    <input type="hidden" value="<?php echo $csv_output; ?>" name="csv_output">
</form>
</center>
</html>