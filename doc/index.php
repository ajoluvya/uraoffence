<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="content-type" content="text/html; charset=utf-8"/>
		<title>Pie Chart Demo (Google VAPI) - http://codeofaninja.com/</title>
	</head>
	
<body style="font-family: Arial;border: 0 none;">
	<!-- where the chart will be rendered -->
	<div id="visualization" style="width: 600px; height: 400px;"></div>

	<?php

	//include database connection
	include 'db_connect.php';

	//query all records from the database
	$query = "select * from programming_languages";

	//execute the query
	$result = $mysqli->query( $query );

	//get number of rows returned
	$num_results = $result->num_rows;

	if( $num_results > 0){

	?>
		<!-- load api -->
		<script type="text/javascript" src="http://www.google.com/jsapi"></script>
		
		<script type="text/javascript">
			//load package
			google.load('visualization', '1', {packages: ['corechart']});
		</script>

		<script type="text/javascript">
			function drawVisualization() {
				// Create and populate the data table.
				var data = google.visualization.arrayToDataTable([
					['PL', 'Ratings'],
					<?php
					while( $row = $result->fetch_assoc() ){
						extract($row);
						echo "['{$name}', {$ratings}],";
					}
					?>
				]);

				// Create and draw the visualization.
				new google.visualization.PieChart(document.getElementById('visualization')).
				draw(data, {title:"Tiobe Top Programming Languages for June 2012"});
			}

			google.setOnLoadCallback(drawVisualization);
		</script>
	<?php

	}else{
		echo "No programming languages found in the database.";
	}
	?>
	 <?php //let's assume you have the product data from the DB in variable called $products
foreach($products as $product):?>
<p id="oldRow<?=$product['id']?>">Item quantity: <input type="text" name="qty<?=$product['id']?>" size="4" value="<?=$product['qty']?>" /> Item name: <input type="text" name="name<?=$product['id']?>" value="<?=$product['name']?>" /> <input type="checkbox" name="delete_ids[]" value="<?=$product['id']?>"> Mark to delete</p>
<?php endforeach;
// first delete the records marked for deletion. Why? Because we don't want to process them in the code below
if(is_array($_POST['delete_ids']) and !empty($_POST['delete_ids'])) {
// you can optimize below into a single query, but let's keep it simple and clear for now:
foreach($_POST['delete_ids'] as $id) {
$sql = "DELETE FROM products WHERE id=$id";
// run the query - not shown
}
}
// now, to edit the existing data, we have to select all the records in a variable.
$sql="SELECT * FROM tbl_capturedgoods WHERE off_id=";
$products; // run your DB wrapper methods here to fill $products
// now edit them
foreach($products as $product) {
// remember how we constructed the field names above? This was with the idea to access the values easy now
$sql = "UPDATE products SET qty='".$_POST['qty'.$product['id']]."', name='".$_POST['name'.$product['id']]."' WHERE id='$product[id]'";
// run the query
}
// (feel free to optimize this so query is executed only when a product is actually changed)
?>
</body>
</html>
