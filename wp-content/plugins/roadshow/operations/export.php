<?php 
ob_start();
// add_action('wp_ajax_call_export','exportCsv');
// add_action('wp_ajax_nopriv_call_export','exportCsv');
if(isset($_POST['call_export']))
{
	exportXls(null, TRUE, isLogin());
}
if(isset($_POST['mail_export']))
{
	exportXls(null, FALSE);
}
?>

<?php 
/**
 * exportXls function
 * this funciton is to call all the denpendancy function to generate data, excel reports, save it or download at the browser
 * @todo report type implementation
 * @param report - type of repoert need to generated, not done,
 * @param attachment - if true force to download the excel file at browser else save it in rwFiles direcory
 * @param user_id - generated the report for a particular user
 * @return sting - path of file stored
 * @author Ankit Balyan - sf.ankit@gmail.com
 **/
function exportXls($report=NULL,$attachment = FALSE,$user_id = NULL)
{

	$data = createData($report,$user_id);
	return genxls($data,$attachment);
}

/**
 * createData function
 * this funciton is to prepare the data that to be imported to the xls sheet.
 * @todo report type implementation
 * @param report - type of repoert need to generated, not done,
 * @param user_id - generated the report for a particular user
 * @return void
 * @author Ankit Balyan - sf.ankit@gmail.com
 **/
function createData($report=null,$user_id = NULL)
{
	$data = array();
	$brandIds = getBrandIds($user_id, TRUE);
	$data = array();
	foreach ($brandIds as $brand_id) {
		$data[$brand_id] = brandLevelOrders($brand_id,TRUE);
	}
	return $data;
}

/**
 * genxls function
 * this funciton is to genterate the excel reports, save it or download at the browser
 * @param data - array of data that is need to be write on excel file
 * @param attachment - if true force to download the excel file at browser else save it in rwFiles direcory
 * @return sting - path of file stored
 * @author Ankit Balyan - sf.ankit@gmail.com
 **/
function  genxls($data = array(), $attachment = false)
{
	/** Error reporting */
	error_reporting(E_ALL);

	/** Include path **/
//	ini_set('include_path', ini_get('include_path').';../Classes/');

	/** PHPExcel */
	include ROADSHOW.'PHPExcel/Classes/PHPExcel.php';

	/** PHPExcel_Writer_Excel2007 */
	include ROADSHOW.'PHPExcel/Classes/PHPExcel/Writer/Excel2007.php';

	// Create new PHPExcel object
	//echo date('H:i:s') . " Create new PHPExcel object\n";
	$objPHPExcel = new PHPExcel();

	// Set properties
	//echo date('H:i:s') . " Set properties\n";
	$objPHPExcel->getProperties()->setCreator("Ankit Balyan");
	$objPHPExcel->getProperties()->setLastModifiedBy("Ankit Balyan");
	$objPHPExcel->getProperties()->setTitle("Office 2007 XLSX Report Title");
	$objPHPExcel->getProperties()->setSubject("Office 2007 XLSX Report Subject");
	$objPHPExcel->getProperties()->setDescription("This is a testing report document.");


	
	// Add some data
	//echo date('H:i:s') . " Add some data\n";
	$objPHPExcel->setActiveSheetIndex(0);

    //HEre your first sheet
    $objWorkSheet = $objPHPExcel->getActiveSheet();
    $brands = $data;
//    print_r($data);
    $i = 0;
    foreach ($brands as $key => $data) {
    	if(!isArvindUser())
    	{
	    	$objPHPExcel->getActiveSheet()->SetCellValue('A2', 'Order\'s Report');

	    	$objPHPExcel->getActiveSheet()->SetCellValue('A3', 'Customer Name:');
	    	$user = getRwUsers(isLogin());
	    	$objPHPExcel->getActiveSheet()->SetCellValue('B3', $user->user_fullname);

	    	$objPHPExcel->getActiveSheet()->SetCellValue('A4', 'Date:');
	    	$objPHPExcel->getActiveSheet()->SetCellValue('B4', date('d M Y'));
	    	$rowI = 5;
	    }
	    else{
	    	$rowI = 1;
	    }
    	if($i != 0)
	    $objWorkSheet = $objPHPExcel->createSheet($key); //Setting index when creating
	    $count = count($data);
	    if($count)
	    {
			$colI = 0;
		    foreach($data[0] as $k => $v){
				$colChar = PHPExcel_Cell::stringFromColumnIndex($colI++);
				$cellId = $colChar.($rowI+1);
				$objWorkSheet->SetCellValue($cellId, $k);
			}	
			$rowI++;
			foreach ($data as $key => $row) {
				$colI = 0;
			    foreach($row as $k => $v){
			      $colChar = PHPExcel_Cell::stringFromColumnIndex($colI++);
			      $cellId = $colChar.($rowI+1);
			      $objWorkSheet->SetCellValue($cellId, $v);
			    }
			    $rowI++;
			}
		  	// Rename sheet
			//	echo date('H:i:s') . " Rename sheet\n";
			$objWorkSheet->setTitle('Brand');
			$i++;
  	  	}
  	}

	// Save Excel 2007 file
	//	echo date('H:i:s') . " Write to Excel2007 format\n";
	$objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
	//$objWriter->save(str_replace('.php', '.xlsx', __FILE__));
	
	$filename = "data_".time().".xls";

    	$DIR = $_SERVER["DOCUMENT_ROOT"].'/rwFiles/';
		(!is_dir($DIR)) ? mkdir($DIR,0777,true) : '';
		$path = $DIR.$filename;
		//$url = "http://".$_SERVER['HTTP_HOST'].'/oddpodimages/'.$filename;
        $fp = fopen($path, 'w');
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
  		$objWriter->save($path);
  		fclose($fp);
  		$objPHPExcel->disconnectWorksheets(); 
		unset($objPHPExcel);
		if (file_exists($path) && $attachment)
		{
			header('Content-Description: File Transfer');
			header('Content-Transfer-Encoding: binary');
			header('Content-Disposition: attachment;filename='.$filename);
			header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
			header('Content-Length: ' . filesize($path));
			header('Pragma: no-cache');
			header('Expires: 0');
			ob_clean();
			flush();
			@readfile($path);
			exit;
		}
  		return $path;
	die;
}