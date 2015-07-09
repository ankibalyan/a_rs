<?php include('login/config.php'); 
 include('login/session.php'); 

 require '../PHPExcel\Classes\PHPExcel.php';
require_once '../PHPExcel\Classes\PHPExcel\IOFactory.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="widtd=device-widtd, initial-scale=1">
	<title>Aravind</title>
	<script src="js/jquery-1.11.3.min.js"></script>
	<script src="js/date.js"></script>
	<script src="bootstrap-3.3.4-dist/js/bootstrap.min.js"></script>
	<link href="css/style.css" rel="stylesheet" type="text/css">
	<link rel="stylesheet" type="text/css" href="bootstrap-3.3.4-dist/css/bootstrap.css">
	<link rel="stylesheet" type="text/css" href="bootstrap-3.3.4-dist/css/bootstrap-tdeme.css">
	<link href="font-awesome-4.3.0/css/font-awesome.css" rel="stylesheet">
	<link href='http://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css'>
</head>
<body>
<?php 
include_once("header.php");
?>

	<div class="body_container">
		<div class="body_container_ten">
			<div class="body_view">
				<ul>
					<a href="#"> <li> SALES </li> </a>
					<a href="#"><li> STOCK </li> </a>
					<a href="#"><li> GRN </li> </a>
				</ul>
				<?php 
			echo"<table class='table table-bordered' cellpadding='0' cellspacing='0'>
 					 <tr id='table_head'> 
						<th> Sl NO </th>
						<th> STORE CODE </th>
						<th> EAN </th>
						<th> SOLD DATE </th>
						<th> SOLD QTY </th>
						<th> UPDATED DATE </th>
						<th> CHAIN NAME  </th>
						<th> STORE NAME  </th>
 					 </tr>";

$sql = mysql_query("select * from sales");
		while($row = mysql_fetch_assoc($sql))
		echo"
 					 <tr> 
						<td> $row[id] </td>
						<td> $row[sa_store_code] </td>
						<td> $row[sa_ean] </td>
						<td> $row[sa_sold_date] </td>
						<td> $row[sa_sold_qty] </td>
						<td> $row[sa_updated_date] </td>
						<td> $row[sa_chain_name]  </td>
						<td> $row[sa_store_name]  </td>
 					 </tr>";
	echo"</table>"
	?>

			</div>
		</div>
	</div>

<?php 

include_once("footer.php");
?>

	
</body>
</html>