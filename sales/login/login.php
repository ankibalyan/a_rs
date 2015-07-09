<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Arvind data compiler</title>
  
  <link href="../css/style.css" rel="stylesheet" type="text/css">
  <link rel="stylesheet" type="text/css" href="../bootstrap-3.3.4-dist/css/bootstrap.css">
  <link rel="stylesheet" type="text/css" href="../bootstrap-3.3.4-dist/css/bootstrap-theme.css">
  <link href="../font-awesome-4.3.0/css/font-awesome.css" rel="stylesheet">
  <link href='http://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css'>
  <script src="../js/jquery-1.11.3.min.js"></script>
  <script src="../bootstrap-3.3.4-dist/js/bootstrap.min.js"></script>
  <script type="text/javascript" charset="utf-8" src="../js/jquery.tubular.1.0.1/js/jquery.tubular.1.0.js"></script> 
<script>
 $().ready(function() {
    $(".login_video").tubular({videoId: '9ucHHnQA-AA',mute: false}); // where idOfYourVideo is the YouTube ID.
  });

</script>

</head>
<body>
  <style>
/* .header_top{
  z-index: 99999;
  position: fixed;
  top: 0;
  left: 0;
  height: 50px;
  margin-bottom: 100px;
} */
  </style>
  <div class="login_video">
  <div class="header_top">
    <div class="header_top_one">
      <div class="body_container_three col-md-3"><img src="../images/logo1.png" style="margin-top: 20px;"></div>
      <div class="body_container_three col-md-3"><img src="../images/Arrow(1).png" style="margin-top: 20px;"></div>
      <div class="body_container_three col-md-3"><img src="../images/izod.png" style="margin-top: 20px;"></div>
    </div>
  </div>
<div class='content_login'>
  <div class="body_container">
    <div class="body_container_one">
      <div class="row" style="margin:0px;">
        <div class="col-md-6 col-md-offset-3">
          <div class="body_container_two">
           


  <form class="form-horizontal" action="#" method="post">
  <div class="form-group">
    <label for="inputEmail3" class="col-sm-2 control-label">Email</label>
    <div class="col-sm-10">
      <input type="email" class="form-control" id="inputEmail3" placeholder="Email" name="email">
    </div>
  </div>
  <div class="form-group">
    <label for="inputPassword3" class="col-sm-2 control-label">Password</label>
    <div class="col-sm-10">
      <input type="password" class="form-control" id="inputPassword3" placeholder="Password"  name="password">
    </div>
  </div>
  <div class="form-group">
    <div class="col-sm-offset-2 col-sm-10">
      <button type="submit" class="btn btn-primary" name="login">Login</button> &nbsp 
    </div>
    </div>
  </form>
 <?php 

include_once('config.php');

if(isset($_POST['login']))
{
  $email = $_POST['email'];
  $password = $_POST['password'];
  $result = mysql_query("select * from users where email='$email' and password='$password'") or die('mysql error');
  $count = mysql_num_rows($result);
  $row = mysql_fetch_array($result);
  if($count > 0)
  {
    session_start();
    $_SESSION['id'] = $row['id'];
    $id=$row['id'];
    header("location: ../index.php");
  }
  else
  {
    $msg = "Wrong  Email or Password.. Please try again...";
   echo '<div class="container_four"> '.$msg.' </div>';
  }
}
?>  
          </div>
        </div>
      </div>

    </div>
</div>
  </div>
  <div>
</body>
</html>
