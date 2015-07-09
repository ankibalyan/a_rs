
	<div class="header_top" style = "<?php  if(basename($_SERVER['PHP_SELF'])=='index.php') echo "background-color: rgba(169, 3, 50, 1);"; else "";?>">
		<!-- <div class="header_top_one"> -->
			<div class="row" style="margin:0px; text-align: center;">
			 	<div class="col-md-2 col-md-offset-1" style="padding-left: 0px;">
			  		<a href="index.php"> <img src="images/logo1.png" class="logo_style"> </a>
			  	</div>
			  	<div class="col-md-2">
			  		<img src="images/Arrow(1).png" class="logo_style">
			  	</div>
			  	<div class="col-md-2">
			  		<img src="images/izod.png" class="logo_style">
			  	</div>
			 	<div class="col-md-3 anchor">			
					<!-- <div class="col-md-8">-->
						<script type="text/javascript">
							document.write (' <span id="date-time">', new Date().toLocaleString(), '<\/span>')
								if (document.getElementById) onload = function () {
									setInterval ("document.getElementById ('date-time').firstChild.data = new Date().toLocaleString()", 50)
								}
						</script>
					<!-- </div> -->
			 	</div>
			  	<div class="col-md-1">
			  		<a href="login/logout.php"> <h4 class="logo_style"> <i class="fa fa-power-off"></i>  Logout </h4> </a>
			  	</div>
			</div>
			
		<!-- </div> -->
				
	</div>
