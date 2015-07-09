<?php 
/*ini_set('display_errors',1); 
 error_reporting(E_ALL);
*/
$u_file = "Sales";

 if($_POST)
 {
  require('login/config.php');

  require 'PHPExcel/Classes/PHPExcel.php';

  require_once 'PHPExcel/Classes/PHPExcel/IOFactory.php';

  if(isset($_POST['submit'])){

  $user_id=$_POST['user_id'];
  $allowed = array("xlsx","xls","csv");
  $file = $_FILES['file']['name'];
  $tmp_name = $_FILES['file']['tmp_name'];
  $extension = pathinfo($file, PATHINFO_EXTENSION);
  $newFile = "upload_tmp/uploaded.$extension";
  move_uploaded_file( $tmp_name, $newFile);

  $srow=2;
  $dropdownval='sales';
  $tname= '';
  if(!in_array($extension, $allowed))
  {
    header("location:index.php?msg=e3&u_f=$u_file");
    die;
  }
  else
  {

  $objPHPExcel = PHPExcel_IOFactory::load($newFile);
  foreach ($objPHPExcel->getWorksheetIterator() as $worksheet) {
      $worksheetTitle     = $worksheet->getTitle();
      $highestRow         = $worksheet->getHighestRow(); // e.g. 10
      $highestColumn      = $worksheet->getHighestColumn(); // e.g 'F'
      $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
      $nrColumns = ord($highestColumn) - 64;
      echo "<br>The worksheet ".$worksheetTitle." has ";
      echo $nrColumns . ' columns (A-' . $highestColumn . ') ';
      echo ' and ' . $highestRow . ' row.<BR><BR>';
  }

  $first_val = array();
  for($f_col=0; $f_col<$highestColumnIndex; ++$f_col)
  {
    $frow_cell = $worksheet->getCellByColumnAndRow($f_col, 1);
    $first_val[] = $frow_cell->getValue();
  }
  if(strcasecmp(trim($first_val[0],"*"),"STORE_CODE")!=0 || strcasecmp(trim($first_val[1],"*"),"EAN")!=0 || strcasecmp(trim($first_val[2],"*"),"SOLD_DATE")!=0 || strcasecmp(trim($first_val[3],"*"),"SOLD_QTY")!=0 || strcasecmp(trim($first_val[4],"*"),"GSV")!=0 || strcasecmp(trim($first_val[5],"*"),"Chain Name")!=0 || strcasecmp(trim($first_val[6],"*"),"Store Name")!=0)
  {
    header("location:index.php?msg=e4&u_f=$u_file");
    die;
  }
  /*

  Below code is used to display the compleate excel shee data
  echo 'Data:<BR><BR><BR><table width="100%" cellpadding="3" cellspacing="0" border=1 bordercolor="green"><tr>';
  for ($row = $srow-1; $row <= $highestRow-2; ++ $row) {
     echo '<tr>';
     for ($col = 0; $col < $highestColumnIndex; ++ $col) {
         $cell = $worksheet->getCellByColumnAndRow($col, $row);
         $val = $cell->getValue();
         if($row === 1)
              echo '<td style="background:#000; color:#fff;">' . $val . '</td>';
         else
             echo '<td>' . $val . '</td>';
     }
  echo '</tr>';
  }
  echo '</table>';
  */
    $get_last_id = mysql_query("SELECT max(id) FROM sales");
    $last_id = mysql_result($get_last_id,0);
    $get_dist_name = mysql_query("SELECT distributor_name FROM users WHERE id=$user_id AND user_role='d'");
    $get_dist_name_r = mysql_result($get_dist_name,0);
    $sql = "SELECT store_code FROM distributor_stroecode WHERE distributor_name ='$get_dist_name_r'";
    $get_store_codes = mysql_query($sql);
    $store_codes = array();
    while ($get_store_codes_r = mysql_fetch_row($get_store_codes)) {
     array_push($store_codes, $get_store_codes_r[0]);
    }

    $pattern = '/^[0-9]{4}-(((0[13578]|(10|12))-(0[1-9]|[1-2][0-9]|3[0-1]))|(02-(0[1-9]|[1-2][0-9]))|((0[469]|11)-(0[1-9]|[1-2][0-9]|30)))$/';/*'/^((19|20)\d\d+)-(0[1-9]|1[012]+)-(0[1-9]|[12][0-9]|3[01])$/';*//*'/(^(((0[1-9]|[12][0-8])[.](0[1-9]|1[012]))|((29|30|31)[.](0[13578]|1[02]))|((29|30)[.](0[4,6,9]|11)))[.](19|[2-9][0-9])\\d\\d$)|(^29[.]02[.](19|[2-9][0-9])(00|04|08|12|16|20|24|28|32|36|40|44|48|52|56|60|64|68|72|76|80|84|88|92|96)$)/'*/  
  for ($row = $srow; $row <= $highestRow; ++ $row) {
     $val=array();

  for ($col = 0; $col < $highestColumnIndex; ++ $col) {
    $cell = $worksheet->getCellByColumnAndRow($col, $row);
    $val[] = $cell->getValue();
    
    }
    if(is_null($val[0])||is_null($val[1])||is_null($val[2])||is_null($val[3])||is_null($val[4])||is_null($val[6]))
    {
      header("location:index.php?msg=e1&row=$row&u_f=$u_file");
      if($last_id=="")
      $sql_del = "DELETE FROM $dropdownval";
      else 
      $sql_del = "DELETE FROM $dropdownval WHERE id > $last_id";
      mysql_query($sql_del);
      die;
    }elseif(preg_match("/^[0-9]+$/",$val[3])==FALSE || $val[3] < 0) 
    { 
      header("location:index.php?msg=e5&row=$row&u_f=$u_file");
      if($last_id=="")
      $sql_del = "DELETE FROM $dropdownval";
      else  
      $sql_del = "DELETE FROM $dropdownval WHERE id > $last_id";
      mysql_query($sql_del);
      die;
    }elseif(strlen($val[1])!=13 || preg_match("/^\d+$/",$val[1]) == FALSE)
    {    
      header("location:index.php?msg=e6&row=$row&u_f=$u_file");
      if($last_id=="")
      $sql_del = "DELETE FROM $dropdownval";
      else 
      $sql_del = "DELETE FROM $dropdownval WHERE id > $last_id";
      mysql_query($sql_del);
      die;
    }
    elseif (preg_match($pattern, $val[2])!=1 || date('Y-m-d',strtotime($val[2])) > date('Y-m-d',time())) {
      header("location:index.php?msg=e7&row=$row&u_f=$u_file");
      if($last_id=="")
      $sql_del = "DELETE FROM $dropdownval";
      else 
      $sql_del = "DELETE FROM $dropdownval WHERE id > $last_id";
      mysql_query($sql_del);
      die;
    }elseif (!in_array($val[0],$store_codes)){
      header("location:index.php?msg=e8&row=$row&u_f=$u_file");
      if($last_id=="")
      $sql_del = "DELETE FROM $dropdownval";
      else 
      $sql_del = "DELETE FROM $dropdownval WHERE id > $last_id";
      mysql_query($sql_del);
      die;
    }

    $sql="INSERT INTO $dropdownval(`sa_store_code`,`sa_ean`,`sa_sold_date`,`sa_sold_qty`,`sa_gsv`,`sa_updated_date`,`sa_chain_name`,`sa_store_name`,`distributor_name`,`uploader_id`)
    VALUES('".$val[0]."','".$val[1]."','".$val[2]."','" .$val[3]."','".$val[4]."',CURDATE(),'".$val[5]."','".$val[6]."','".$get_dist_name_r."','".$user_id."')" ;
    
    $result = mysql_query($sql);
    
      if(!$result)
      {
        if(stristr(mysql_error(),'Duplicate'))
        {
          if($last_id=="")
            $sql_del = "DELETE FROM $dropdownval";
          else 
            $sql_del = "DELETE FROM $dropdownval WHERE id > $last_id";
          mysql_query($sql_del);
          header("location:index.php?msg=e2&row=$row&u_f=$u_file");
          die;
        }
      }

      }
      if($result)
      {
        header("location:index.php?msg=e0&$result&u_f=$u_file");
        $current_date= date("Y-m-d");
        $mdate = strtotime("$current_date monday this week");
        $startdate = date('Y-m-d',$mdate);
        $startdate;
        $sql = mysql_query("SELECT count(*) FROM user_upload_track WHERE every_monday='".$startdate."'");
        $sql_r = mysql_result($sql,0);
        if($sql_r==0)
        {
         $sql = mysql_query("INSERT INTO user_upload_track(`every_monday`,`uploaded_user_ids`) VALUES('".$startdate."','".$user_id."')");
        }else{
         $sql = mysql_query("UPDATE user_upload_track SET uploaded_user_ids= CONCAT(uploaded_user_ids,',".$user_id."') WHERE every_monday='".$startdate."'");
        }
      }
    }
  }
}
else
{
  header("location:index.php");
}
?>
