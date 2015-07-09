<?php
/*ini_set('display_errors',1); 
error_reporting(E_ALL); */
include('login/config.php'); 
include('login/session.php'); 
require 'PHPExcel/Classes/PHPExcel.php';
require_once 'PHPExcel/Classes/PHPExcel/IOFactory.php';
require_once 'PHPMailer/PHPMailerAutoload.php';	
	

if(isset($_GET['u_f']))
{
	$upload_type = $_GET['u_f'];
}

$current_user_id= $_SESSION['id'];

/*find out the user type*/
$usr_type_sql = mysql_query("SELECT user_role FROM users WHERE id = '".$current_user_id."'");
$user_type = mysql_result($usr_type_sql,0);

/*get the email address of the current user*/
$sql = mysql_query("SELECT email from users WHERE id = $current_user_id");
$sql_r = mysql_result($sql,0);

/*columns to display and export*/
$sales_columns = 'sa_store_code,sa_ean,DATE_FORMAT(sa_sold_date, "%d-%m-%Y") as sa_sold_date,sa_sold_qty,sa_gsv,DATE_FORMAT(sa_updated_date, "%d-%m-%Y") as sa_updated_date,sa_chain_name,sa_store_name';
$grn_columns = 'store_code,po_number,invoice,ean,DATE_FORMAT(grn_date, "%d-%m-%Y") as grn_date,grn_qty,chain_name,store_name';
$stock_columns = 'store_code,ean,DATE_FORMAT(stock_date, "%d-%m-%Y") as stock_date,soh,chain_name,store_name,distributor_name';


/*get the assigned distributor name of the current user*/
$get_dist_name = mysql_query("SELECT distributor_name FROM users WHERE id=$current_user_id AND user_role='d'");
$get_dist_name_r = mysql_result($get_dist_name,0);

/*setting start and end date for data display query*/
$c_date= date("Y-m-d");
$mindate = strtotime("$c_date - 1 week");
$minu = date('d-m-Y',$mindate);
$datemon = strtotime("$minu monday this week");
$sdate= date('Y-m-d',$datemon);
$dateStam = strtotime("$minu sunday this week");
$edate= date('Y-m-d',$dateStam);

/*mail connection*/
$m = new PHPMailer;
$m->isSMTP();
$m->SMTPAuth = true;

$m->Host = 'smtp.gmail.com';
$m->Username = 'arvind.shotym@gmail.com';
$m->Password = 'arvind@123';
$m->SMTPSecure = 'ssl';
$m->Port = 465;

$m->From = 'arvind.shotym@gmail.com';
$m->FromName = 'Arvind brands';
$m->addAddress($sql_r);
$m->Subject = "$upload_type Upload Failed";
$m->Body = "Upload to $upload_type has failed, PFA of the error log file and try again.";
$m->addReplyTo('arvind.shotym@gmail.com','Reply address');

//export
if(isset($_GET['wek']))
{
	
	//getting the current date and setting the start date for export
	$week = $_GET['wek'];
	$current_date= date("Y-m-d");
	$mdate = strtotime("$current_date - $week week");
	$minus = date('d-m-Y',$mdate);
	$dateStamp = strtotime("$minus monday this week");
	$start_date= date('Y-m-d',$dateStamp);

	//creating and writing into excel sheet
	$sheet = new PHPExcel();
	$act = $sheet->getActiveSheet()->setTitle('Sales');

	//setting to bold characters in first row
	$act->getStyle('A1:G1')->getFont()->setBold(true)->setSize(12);

	//write headers
	$act->setCellValue('A1','STORE_CODE');
	$act->setCellValue('B1','EAN');
	$act->setCellValue('C1','SOLD_DATE');
	$act->setCellValue('D1','SOLD_QTY');
	$act->setCellValue('E1','GSV');
	$act->setCellValue('F1','UPDATED_DATE');
	$act->setCellValue('G1','Chain Name');
	$act->setCellValue('H1','Store Name');

	if($user_type == 'a')
		$sqle = mysql_query("SELECT $sales_columns FROM sales WHERE sa_sold_date > '".$start_date."' ");
	else	
		$sqle = mysql_query("SELECT $sales_columns FROM sales WHERE sa_sold_date > '".$start_date."' and sa_sold_date < '".$edate."' and distributor_name = '".$get_dist_name_r."' and uploader_id ='".$current_user_id."' ");
	$e_row = 2;
	while($tst_r = mysql_fetch_row($sqle))
	{
		$act->setCellValue("A$e_row",$tst_r[0]);
		$act->setCellValue("B$e_row",$tst_r[1]);
		$act->setCellValue("C$e_row",$tst_r[2]);
		$act->setCellValue("D$e_row",$tst_r[3]);
		$act->setCellValue("E$e_row",$tst_r[4]);
		$act->setCellValue("F$e_row",$tst_r[5]);
		$act->setCellValue("G$e_row",$tst_r[6]);
		$act->setCellValue("H$e_row",$tst_r[7]);

		$e_row++;
	}

	$act->getColumnDimension('A')->setAutoSize(true);
	$act->getColumnDimension('B')->setAutoSize(true);
	$act->getColumnDimension('C')->setAutoSize(true);
	$act->getColumnDimension('D')->setAutoSize(true);
	$act->getColumnDimension('E')->setAutoSize(true);
	$act->getColumnDimension('F')->setAutoSize(true);
	$act->getColumnDimension('G')->setAutoSize(true);
	$act->getColumnDimension('H')->setAutoSize(true);

	$sheet->getActiveSheet()->setTitle('Sales');

	/*----------------------------------------------------------------------*/
	$sheet->createSheet();
	$sheet->setActiveSheetIndex(1);
	$stock  = $sheet->getActiveSheet();

	//setting to bold characters in first row
	$stock->getStyle('A1:G1')->getFont()->setBold(true)->setSize(12);

	//write headers
	$stock->setCellValue('A1','STORE_CODE');
	$stock->setCellValue('B1','EAN');
	$stock->setCellValue('C1','STOCK_DATE');
	$stock->setCellValue('D1','QTY');
	$stock->setCellValue('E1','Chain Name');
	$stock->setCellValue('F1','Store Name');

	if($user_type == 'a')
		$sqls = mysql_query("SELECT $stock_columns FROM stock WHERE stock_date > '".$start_date."' ");
	else	
		$sqls = mysql_query("SELECT $stock_columns FROM stock WHERE stock_date > '".$start_date."' AND stock_date < '".$edate."' AND distributor_name = '".$get_dist_name_r."' AND uploader_id ='".$current_user_id."' ");
	$e_row = 2;
	while($tst_r = mysql_fetch_row($sqls))
	{
		$stock->setCellValue("A$e_row",$tst_r[0]);
		$stock->setCellValue("B$e_row",$tst_r[1]);
		$stock->setCellValue("C$e_row",$tst_r[2]);
		$stock->setCellValue("D$e_row",$tst_r[3]);
		$stock->setCellValue("E$e_row",$tst_r[4]);
		$stock->setCellValue("F$e_row",$tst_r[5]);

		$e_row++;
	}

	$stock->getColumnDimension('A')->setAutoSize(true);
	$stock->getColumnDimension('B')->setAutoSize(true);
	$stock->getColumnDimension('C')->setAutoSize(true);
	$stock->getColumnDimension('D')->setAutoSize(true);
	$stock->getColumnDimension('E')->setAutoSize(true);
	$stock->getColumnDimension('F')->setAutoSize(true);

	$sheet->getActiveSheet()->setTitle('Stock');

	/*----------------------------------------------------------------------*/
	$sheet->createSheet();
	$sheet->setActiveSheetIndex(2);
	$grn  = $sheet->getActiveSheet();

	//setting to bold characters in first row
	$grn->getStyle('A1:H1')->getFont()->setBold(true)->setSize(12);

	//write headers
	$grn->setCellValue('A1','STORE_CODE');
	$grn->setCellValue('B1','PO_NUMBER');
	$grn->setCellValue('C1','INVOICE');
	$grn->setCellValue('D1','EAN');
	$grn->setCellValue('E1','GRN_DATE');
	$grn->setCellValue('F1','GRN_QTY');
	$grn->setCellValue('G1','Chain Name');
	$grn->setCellValue('H1','Store Name');

	if($user_type == 'a')
		$sqlg = mysql_query("SELECT $grn_columns FROM grn WHERE grn_date > '".$start_date."' ");
	else
		$sqlg = mysql_query("SELECT $grn_columns FROM grn WHERE grn_date > '".$start_date."' AND grn_date < '".$edate."' AND distributor_name = '".$get_dist_name_r."' AND uploader_id ='".$current_user_id."' ");
	$e_row = 2;
	while($tst_r = mysql_fetch_row($sqlg))
	{
		$grn->setCellValue("A$e_row",$tst_r[0]);
		$grn->setCellValue("B$e_row",$tst_r[1]);
		$grn->setCellValue("C$e_row",$tst_r[2]);
		$grn->setCellValue("D$e_row",$tst_r[3]);
		$grn->setCellValue("E$e_row",$tst_r[4]);
		$grn->setCellValue("F$e_row",$tst_r[5]);
		$grn->setCellValue("G$e_row",$tst_r[6]);
		$grn->setCellValue("H$e_row",$tst_r[7]);

		$e_row++;
	}

	$grn->getColumnDimension('A')->setAutoSize(true);
	$grn->getColumnDimension('B')->setAutoSize(true);
	$grn->getColumnDimension('C')->setAutoSize(true);
	$grn->getColumnDimension('D')->setAutoSize(true);
	$grn->getColumnDimension('E')->setAutoSize(true);
	$grn->getColumnDimension('F')->setAutoSize(true);
	$grn->getColumnDimension('G')->setAutoSize(true);
	$grn->getColumnDimension('H')->setAutoSize(true);

	$sheet->getActiveSheet()->setTitle('GRN');

	header("Content-Type: application/vnd.ms-excel");
	header("Content-disposition: attachment; filename=data_export.xls");
	$writer = PHPExcel_IOFactory::createWriter($sheet,'Excel2007');
	$writer->save('php://output');
	die;
}
//custom date export
if(isset($_POST['custom_date']))
{
	$s_date = $_POST['start_date'];
	$e_date = $_POST['end_date'];

	//creating and writing into excel sheet
	$sheet = new PHPExcel();
	$sheet->setActiveSheetIndex(0);
	$act = $sheet->getActiveSheet();

	//setting to bold characters in first row
	$act->getStyle('A1:G1')->getFont()->setBold(true)->setSize(12);

	//write headers
	$act->setCellValue('A1','STORE_CODE');
	$act->setCellValue('B1','EAN');
	$act->setCellValue('C1','SOLD_DATE');
	$act->setCellValue('D1','SOLD_QTY');
	$act->setCellValue('E1','GSV');
	$act->setCellValue('F1','UPDATED_DATE');
	$act->setCellValue('G1','Chain Name');
	$act->setCellValue('H1','Store Name');

	if($user_type == 'a')
		$sqle = mysql_query("SELECT $sales_columns FROM sales WHERE sa_sold_date > '".$s_date."' AND sa_sold_date < '".$e_date."' ");
	else
		$sqle = mysql_query("SELECT $sales_columns FROM sales WHERE sa_sold_date > '".$s_date."' AND sa_sold_date < '".$e_date."' AND distributor_name = '".$get_dist_name_r."' AND uploader_id ='".$current_user_id."' ");
	$e_row = 2;
	while($tst_r = mysql_fetch_row($sqle))
	{
		$act->setCellValue("A$e_row",$tst_r[0]);
		$act->setCellValue("B$e_row",$tst_r[1]);
		$act->setCellValue("C$e_row",$tst_r[2]);
		$act->setCellValue("D$e_row",$tst_r[3]);
		$act->setCellValue("E$e_row",$tst_r[4]);
		$act->setCellValue("F$e_row",$tst_r[5]);
		$act->setCellValue("G$e_row",$tst_r[6]);
		$act->setCellValue("H$e_row",$tst_r[7]);

		$e_row++;
	}

	$act->getColumnDimension('A')->setAutoSize(true);
	$act->getColumnDimension('B')->setAutoSize(true);
	$act->getColumnDimension('C')->setAutoSize(true);
	$act->getColumnDimension('D')->setAutoSize(true);
	$act->getColumnDimension('E')->setAutoSize(true);
	$act->getColumnDimension('F')->setAutoSize(true);
	$act->getColumnDimension('G')->setAutoSize(true);
	$act->getColumnDimension('H')->setAutoSize(true);

	$sheet->getActiveSheet()->setTitle('Sales');
/*----------------------------------------------------------------------*/
	$sheet->createSheet();
	$sheet->setActiveSheetIndex(1);
	$stock  = $sheet->getActiveSheet();

	//setting to bold characters in first row
	$stock->getStyle('A1:G1')->getFont()->setBold(true)->setSize(12);

	//write headers
	$stock->setCellValue('A1','STORE_CODE');
	$stock->setCellValue('B1','EAN');
	$stock->setCellValue('C1','STOCK_DATE');
	$stock->setCellValue('D1','QTY');
	$stock->setCellValue('E1','Chain Name');
	$stock->setCellValue('F1','Store Name');

	if($user_type == 'a')
		$sqls = mysql_query("SELECT $stock_columns FROM stock WHERE stock_date > '".$s_date."' AND stock_date < '".$e_date."' ");
	else
		$sqls = mysql_query("SELECT $stock_columns FROM stock WHERE stock_date > '".$s_date."' AND stock_date < '".$e_date."' AND distributor_name = '".$get_dist_name_r."' AND uploader_id ='".$current_user_id."' ");
	$e_row = 2;
	while($tst_r = mysql_fetch_row($sqls))
	{
		$stock->setCellValue("A$e_row",$tst_r[0]);
		$stock->setCellValue("B$e_row",$tst_r[1]);
		$stock->setCellValue("C$e_row",$tst_r[2]);
		$stock->setCellValue("D$e_row",$tst_r[3]);
		$stock->setCellValue("E$e_row",$tst_r[4]);
		$stock->setCellValue("F$e_row",$tst_r[5]);

		$e_row++;
	}

	$stock->getColumnDimension('A')->setAutoSize(true);
	$stock->getColumnDimension('B')->setAutoSize(true);
	$stock->getColumnDimension('C')->setAutoSize(true);
	$stock->getColumnDimension('D')->setAutoSize(true);
	$stock->getColumnDimension('E')->setAutoSize(true);
	$stock->getColumnDimension('F')->setAutoSize(true);

	$sheet->getActiveSheet()->setTitle('Stock');

	/*----------------------------------------------------------------------*/
	$sheet->createSheet();
	$sheet->setActiveSheetIndex(2);
	$grn  = $sheet->getActiveSheet();

	//setting to bold characters in first row
	$grn->getStyle('A1:H1')->getFont()->setBold(true)->setSize(12);

	//write headers
	$grn->setCellValue('A1','STORE_CODE');
	$grn->setCellValue('B1','PO_NUMBER');
	$grn->setCellValue('C1','INVOICE');
	$grn->setCellValue('D1','EAN');
	$grn->setCellValue('E1','GRN_DATE');
	$grn->setCellValue('F1','GRN_QTY');
	$grn->setCellValue('G1','Chain Name');
	$grn->setCellValue('H1','Store Name');

	if($user_type == 'a')
		$sqlg = mysql_query("SELECT $grn_columns FROM grn WHERE grn_date > '".$s_date."' AND grn_date < '".$e_date."' ");
	else
		$sqlg = mysql_query("SELECT $grn_columns FROM grn WHERE grn_date > '".$s_date."' AND grn_date < '".$e_date."' AND distributor_name = '".$get_dist_name_r."' AND uploader_id ='".$current_user_id."' ");
	$e_row = 2;
	while($tst_r = mysql_fetch_row($sqlg))
	{
		$grn->setCellValue("A$e_row",$tst_r[0]);
		$grn->setCellValue("B$e_row",$tst_r[1]);
		$grn->setCellValue("C$e_row",$tst_r[2]);
		$grn->setCellValue("D$e_row",$tst_r[3]);
		$grn->setCellValue("E$e_row",$tst_r[4]);
		$grn->setCellValue("F$e_row",$tst_r[5]);
		$grn->setCellValue("G$e_row",$tst_r[6]);
		$grn->setCellValue("H$e_row",$tst_r[7]);

		$e_row++;
	}

	$grn->getColumnDimension('A')->setAutoSize(true);
	$grn->getColumnDimension('B')->setAutoSize(true);
	$grn->getColumnDimension('C')->setAutoSize(true);
	$grn->getColumnDimension('D')->setAutoSize(true);
	$grn->getColumnDimension('E')->setAutoSize(true);
	$grn->getColumnDimension('F')->setAutoSize(true);
	$grn->getColumnDimension('G')->setAutoSize(true);
	$grn->getColumnDimension('H')->setAutoSize(true);

	$sheet->getActiveSheet()->setTitle('GRN');

	header("Content-Type: application/vnd.ms-excel");
	header("Content-disposition: attachment; filename=data_export.xls");
	header('Cache-Control: max-age=0');
	$writer = PHPExcel_IOFactory::createWriter($sheet,'Excel2007');
	$writer->save('php://output');
	die;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Arvind data compiler</title>

	<link href="css/style.css" rel="stylesheet" type="text/css">
	<link rel="stylesheet" type="text/css" href="bootstrap-3.3.4-dist/css/bootstrap.css">
	<link rel="stylesheet" type="text/css" href="bootstrap-3.3.4-dist/css/bootstrap-theme.css">
	<link href="font-awesome-4.3.0/css/font-awesome.css" rel="stylesheet">
	<link href="jquery-ui/jquery-ui.css" rel="stylesheet">
	<link href='http://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css'>
	<script src="js/jquery-1.11.3.min.js"></script>
	<script src="bootstrap-3.3.4-dist/js/bootstrap.min.js"></script>
	 <script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
	<script src="js/date.js"></script>

	<?php
		if(isset($_GET['msg']) && $_GET['msg'] !='') {
			$msg =  $_GET['msg'];
			if($msg=='e1'){
				$e1_row = $_GET['row'];
				$error_msg = "Empty cell enocuntered at row $e1_row.<br> Upload Failed."; 
				echo "<script> $(document).ready(function(){ $('#clk').click(); });</script>";
				if($file = fopen("logs/err_log.txt",'w+'))
				{	
					fwrite($file, $error_msg);
					$m->addAttachment('logs/err_log.txt','error.txt');
					$m->send();
				}else{}
				
			}elseif ($msg=='e2') {
				$e_row = $_GET['row'];
				$error_msg = "Duplicate Entry ecnocuntered at row number $e_row.<br> Upload Failed.";
				echo "<script>$(document).ready(function(){ $('#clk').click(); });</script>";
				if($file = fopen("logs/err_log.txt",'w+'))
				{
					fwrite($file, $error_msg);
					$m->addAttachment('logs/err_log.txt','error.txt');
					$m->send();
				}else{}
			}elseif ($msg=="e0") {
				$error_msg = "Successfully uploaded"; 
				echo "<script>$(document).ready(function(){ $('#clk').click(); });</script>";
				$m->Subject = "$upload_type upload success";
				$m->Body = "$upload_type file uploaded successfully";
				$m->send();
			}elseif ($msg=="e3"){
				$error_msg = "The file format is not allowed, Please upload an excel file fromat.<br> Upload Failed."; 
				echo "<script>$(document).ready(function(){ $('#clk').click(); });</script>";
				if($file = fopen("logs/err_log.txt",'w+'))
				{
					fwrite($file, $error_msg);
					$m->addAttachment('logs/err_log.txt','error.txt');
					$m->send();
				}else{}
			}elseif ($msg=="e4") {
				$error_msg = "The column names do not match the standard data format, Please check the data format at row number 1.<br> Upload Failed."; 
				echo "<script>$(document).ready(function(){ $('#clk').click(); });</script>";
				if($file = fopen("logs/err_log.txt",'w+'))
				{
					fwrite($file, $error_msg);
					$m->addAttachment('logs/err_log.txt','error.txt');
					$m->send();
				}else{}
			}elseif ($msg=="e5") {
				$e5_row = $_GET['row'];
				$error_msg = "The quantity filed at row number $e5_row is invalid, Please check and reupload.<br> Upload Failed."; 
				echo "<script>$(document).ready(function(){ $('#clk').click(); });</script>";
				if($file = fopen("logs/err_log.txt",'w+'))
				{
					fwrite($file, $error_msg);
					$m->addAttachment('logs/err_log.txt','error.txt');
					$m->send();
				}else{}
			}elseif ($msg=="e6") {
				$e6_row = $_GET['row'];
				$error_msg = "Invalid EAN code at row number $e6_row.<br> Upload Failed."; 
				echo "<script>$(document).ready(function(){ $('#clk').click(); });</script>";
				if($file = fopen("logs/err_log.txt",'w+'))
				{
					fwrite($file, $error_msg);
					$m->addAttachment('logs/err_log.txt','error.txt');
					$m->send();
				}else{}
			}elseif ($msg=="e7") {
				$e7_row = $_GET['row'];
				$error_msg = "Invalid date format at row number $e7_row.<br> Upload Failed."; 
				echo "<script>$(document).ready(function(){ $('#clk').click(); });</script>";
				if($file = fopen("logs/err_log.txt",'w+'))
				{
					fwrite($file, $error_msg);
					$m->addAttachment('logs/err_log.txt','error.txt');
					$m->send();
				}else{}
			}elseif ($msg=="e8") {
				$e8_row = $_GET['row'];
				$error_msg = "Cannot upload data for the store code at row $e8_row.<br> Upload Failed."; 
				echo "<script>$(document).ready(function(){ $('#clk').click(); });</script>";
				if($file = fopen("logs/err_log.txt",'w+'))
				{
					fwrite($file, $error_msg);
					$m->addAttachment('logs/err_log.txt','error.txt');
					$m->send();
				}else{}	
			}
		}else
		{
		}
	?>
</head>
<body>
<?php 
include_once("header.php");
?>

	<div class="body_container">
		<div class="body_container_one">
			
			<div class="col-xs-10" style="margin:0px;  border-right: solid #A90332 1px;">

			  <div class="row" <?php if($user_type =='a') echo "style = display:none;"; else echo "";?> >
			  <div class="col-md-3 col-md-offset-1">
					<div class="body_container_two">
						<p> Sales Data Upload </p>
						<br/><br/>
						<form action="sales.php" method="POST" enctype="multipart/form-data">
							  <div class="form-group">
							    <input type="file" id="file" name="file" class="upload_button" accept=".csv,application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" required>
							    <input type="hidden" name="user_id"	value="<?php echo $current_user_id; ?>" />	    
							  </div>
							  <br/>
							  <div class="align_center"> <button type="submit" class="btn btn-primary" name="submit">Upload</button> </div>

						</form>
				</div>
			  </div>
			  <div class="col-md-3">
					<div class="body_container_two" >
						<p> Stock Data Upload </p>
						<br/><br/>
						<form action="stock.php" method="post" enctype="multipart/form-data">
							  <div class="form-group">
							    <input type="file" id="stock_file" name="stock_file" class="upload_button" accept=".csv,application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" required>		    
							    <input type="hidden" name="user_id"	value="<?php echo $current_user_id; ?>" />
							  </div>
							  <br/>
							  <div class="align_center"> <button type="submit" name="stock_submit" class="btn btn-primary">Upload</button> </div>
						</form>
				</div>
			  </div>

			  <div class="col-md-3">
						<div class="body_container_two">
						<p > GRN Data Upload </p>
						<br/><br/>
						<form action="grn.php" method="POST" enctype="multipart/form-data">
							  <div class="form-group">
							    <input type="file" id="grn_file" name="grn_file" class="upload_button" accept=".csv,application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" required>		    
							    <input type="hidden" name="user_id"	value="<?php echo $current_user_id; ?>" />
							  </div>
							  <br/>
							  <div class="align_center"> <button type="submit" name="grn_submit" class="btn btn-primary">Upload</button> </div>
						</form>
				</div>
			  </div>
			  </div>
			<div class="body_view col-md-10 col-md-offset-1"   style="padding-left: 0px;">
				<span style=" float: right;"><i>Records displayed from <?php echo date('d-m-Y',$datemon);?></i></span>
				<ul class="nav nav-tabs">
					<li  role="presentation" class="active"><a href="#sales_tab" data-toggle="tab"> SALES  </a></li>
					<li  role="presentation" ><a href="#stock_tab" data-toggle="tab"> STOCK </a> </li> 
					<li  role="presentation" ><a href="#grn_tab" data-toggle="tab"> GRN  </a></li>
				</ul>
				<div class="tab-content">
					<div class="tab-pane active scroll_tab" id="sales_tab">
						<?php 
							echo"<table class='table table-bordered' cellpadding='0' cellspacing='0'>
		 						<tr id='table_head'> 
									<th> STORE CODE </th>
									<th> EAN </th>
									<th> SOLD DATE </th>
									<th> SOLD QTY </th>
									<th> GSV </th>
									<th> UPDATED DATE </th>
									<th> CHAIN NAME  </th>
									<th> STORE NAME  </th>
			 					</tr>";	 
							if($user_type == 'a')
								$sql = mysql_query("SELECT $sales_columns from sales WHERE sa_sold_date > '".$sdate."' AND sa_sold_date < '".$edate."' ");
							else
								$sql = mysql_query("SELECT $sales_columns from sales WHERE distributor_name = '".$get_dist_name_r."' AND sa_sold_date > '".$sdate."' AND sa_sold_date < '".$edate."' AND uploader_id ='".$current_user_id."' ");
							while($row = mysql_fetch_assoc($sql))
								echo"
			 					 <tr> 
									<td> $row[sa_store_code] </td>
									<td> $row[sa_ean] </td>
									<td> $row[sa_sold_date] </td>
									<td> $row[sa_sold_qty] </td>
									<td> $row[sa_gsv] </td>
									<td> $row[sa_updated_date] </td>
									<td> $row[sa_chain_name]  </td>
									<td> $row[sa_store_name]  </td>
			 					 </tr>";
								echo"</table>"
						?>
					</div>
					<div class="tab-pane scroll_tab" id="stock_tab">
					<?php 
					echo"<table class='table table-bordered' cellpadding='0' cellspacing='0'>
 						<tr id='table_head'> 
							<th> STORE CODE </th>
							<th> EAN </th>
							<th> STOCK DATE </th>
							<th> QTY </th>
							<th> Chain Name  </th>
							<th> Store Name  </th>
	 					</tr>";	 
					if($user_type == 'a')
						$sql_grn = mysql_query("SELECT $stock_columns from stock WHERE stock_date > '".$sdate."' AND stock_date < '".$edate."' ");
					else
						$sql_grn = mysql_query("SELECT $stock_columns from stock WHERE distributor_name = '".$get_dist_name_r."' AND stock_date > '".$sdate."' AND stock_date < '".$edate."' AND uploader_id ='".$current_user_id."' ");
					while($row = mysql_fetch_assoc($sql_grn))
						echo"
	 					 <tr> 
							<td> $row[store_code] </td>
							<td> $row[ean] </td>
							<td> $row[stock_date] </td>
							<td> $row[soh] </td>
							<td> $row[chain_name]  </td>
							<td> $row[store_name]  </td>
	 					 </tr>";
						echo"</table>"
					?>
					</div>
					<div class="tab-pane scroll_tab" id="grn_tab">
					<?php 
					echo"<table class='table table-bordered' cellpadding='0' cellspacing='0'>
 						<tr id='table_head'> 
							<th> STORE CODE </th>
							<th> PO_NUM </th>
							<th> INVOICE </th>
							<th> EAN </th>
							<th> GRN_DATE </th>
							<th> GRNQTY  </th>
							<th> Chain Name  </th>
							<th> Store Name  </th>
	 					</tr>";	 
					if($user_type == 'a')
						$sql_grn = mysql_query("SELECT $grn_columns from grn WHERE grn_date > '".$sdate."' AND grn_date < '".$edate."' ");
	 				else	
						$sql_grn = mysql_query("SELECT $grn_columns from grn WHERE distributor_name = '".$get_dist_name_r."' AND grn_date > '".$sdate."' AND grn_date < '".$edate."' AND uploader_id ='".$current_user_id."' ");
					while($row = mysql_fetch_assoc($sql_grn))
						echo"
	 					 <tr> 
							<td> $row[store_code] </td>
							<td> $row[po_number] </td>
							<td> $row[invoice] </td>
							<td> $row[ean] </td>
							<td> $row[grn_date] </td>
							<td> $row[grn_qty]  </td>
							<td> $row[chain_name]  </td>
							<td> $row[store_name]  </td>
	 					 </tr>";
						echo"</table>"
					?>
					</div>
</div>
			</div>



			
		</div>
		<div class="col-xs-2 week_export">
					
				<p class="body_main_one"> Repository  </p> 
				<p class="main_data_ar"> Data Export </p>
					<ul class="main_data ">
						<a href="index.php?wek=1"> <li> - Last 1 Week </li> </a>
						<a href="index.php?wek=2"> <li> - Last 2 Week </li> </a>
						<a href="index.php?wek=12"> <li> - Last 12 Week </li> </a>
						<a href="index.php?wek=24"> <li> - Last 24 Week </li> </a>
						<a href="index.php?wek=52"> <li> - Last 52 Week </li> </a>
					</ul><br/>
					<div id="custom_dates">
						<form action="index.php" method="POST">
							<b>Start date</b>
							<input type="text" class="form-control datepicker readoly strt_dt" name="start_date" id="strt_dt" required>
							<b>End date</b>
							<input type="text" class=" form-control datepicker readoly nd_dt" name="end_date" id="nd_dt"  required>
							<input type="submit" value="Export" class="btn btn-default" name="custom_date" style="margin-top: 5px;">
						</form>
					</div>
				<p class="main_data_ar"><a href="#"> User Manual </a> </p>
				<p class="main_data_ar"><a href="template_format/Standard_fromat.zip"> Standard Template Format </a> </p>
			</div>
	</div>

<?php 

include_once("footer.php");
?>

<!-- Button trigger modal -->
<input type="hidden" id="clk" class="btn btn-primary btn-lg" data-toggle="modal" data-target="#myModal">
<!-- Modal -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Note</h4>
      </div>
      <div class="modal-body">
        <?php echo $error_msg; ?>
      </div>
      <!-- <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary">Save changes</button>
      </div> -->
    </div>
  </div>
</div>
</body>
</html>
  <script>
$(function() {

  	$( ".strt_dt" ).datepicker({ maxDate: new Date(),dateFormat: 'yy-mm-dd',
  		onSelect: function(selected) {

		$(".nd_dt").datepicker("option","minDate", selected)
		} 
	});

	$( ".nd_dt" ).datepicker({ maxDate: new Date(),dateFormat: 'yy-mm-dd',
  		onSelect: function(selected) {

		$(".strt_dt").datepicker("option","maxDate", selected)
		} 
	});

  	$(".readoly").keydown(function(e){
        e.preventDefault();
    });
});
</script>
