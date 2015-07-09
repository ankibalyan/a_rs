<?php 
/**
 * uploadBrands function
 * this funciton is to render a form to upload the brands csv file on upload brand page.
 * @return void
 * @author Ankit Balyan - sf.ankit@gmail.com
 **/
function uploadBrands()
{
	?>
	<form id="wp_csv_to_db_form" method="post" action="">
                    <table class="form-table"> 
                        <input type="hidden" value="rw_brands" name="table_select" id="table_select">
                        
                        <tr valign="top"><th scope="row"><?php _e('Select Input File:','wp_csv_to_db'); ?></th>
                            <td>
                                <?php $repop_file = isset($_POST['csv_file']) ? $_POST['csv_file'] : null; ?>
                                <?php $repop_csv_cols = isset($_POST['num_cols_csv_file']) ? $_POST['num_cols_csv_file'] : '0'; ?>
                                <input id="csv_file" name="csv_file"  type="text" size="70" value="<?php echo $repop_file; ?>" />
                                <input id="csv_file_button" type="button" value="Upload" />
                                <input id="num_cols" name="num_cols" type="hidden" value="" />
                                <input id="num_cols_csv_file" name="num_cols_csv_file" type="hidden" value="" />
                                <br><?php _e('File must end with a .csv extension.','wp_csv_to_db'); ?>
                                <br><?php _e('Number of .csv file Columns:','wp_csv_to_db'); echo ' '; ?><span id="return_csv_col_count"><?php echo $repop_csv_cols; ?></span>
                            </td>
                        </tr>
                        <tr valign="top"><th scope="row"><?php _e('Select Starting Row:','wp_csv_to_db'); ?></th>
                            <td>
                            	<?php $repop_row = isset($_POST['sel_start_row']) ? $_POST['sel_start_row'] : null; ?>
                                <input id="sel_start_row" name="sel_start_row" type="text" size="10" value="<?php echo $repop_row; ?>" />
                                <br><?php _e('Defaults to row 1 (top row) of .csv file.','wp_csv_to_db'); ?>
                            </td>
                        </tr>
                        <!-- <tr valign="top"><th scope="row"><?php _e('Disable "auto_increment" Column:','wp_csv_to_db'); ?></th>
                            <td>
                                <input id="remove_autoinc_column" name="remove_autoinc_column" type="checkbox" />
                                <br><?php _e('Bypasses the "auto_increment" column;','wp_csv_to_db'); ?>
                                <br><?php _e('This will reduce (for the purposes of importation) the number of DB columns by "1".','wp_csv_to_db'); ?>
                            </td>
                        </tr> -->
                        <tr valign="top"><th scope="row"><?php _e('Update Database Rows:','wp_csv_to_db'); ?></th>
                            <td>
                                <input id="update_db" name="update_db" type="checkbox" />
                                <br><?php _e('Will update exisiting database rows when a duplicated primary key is encountered.','wp_csv_to_db'); ?>
                                <br><?php _e('Defaults to all rows inserted as new rows.','wp_csv_to_db'); ?>
                            </td>
                        </tr>
                    </table>
                    
                    <p class="submit">
                        <input id="execute_button" name="execute_button" type="submit" class="button-primary" value="<?php _e('Import to DB', 'wp_csv_to_db') ?>" />
                        <!-- <input id="export_to_csv_button" name="export_to_csv_button" type="submit" class="button-secondary" value="<?php _e('Export to CSV', 'wp_csv_to_db') ?>" /> -->
                        <input type="hidden" id="delete_db_button_hidden" name="delete_db_button_hidden" value="" />
                    </p>
                    </form>
<?php

}
/**
 * uploadBrands function
 * this funciton is to render a form to add a new brand to shotym, provided  the brand should be mapped to a user then only it is accessble from the user.
 * @return void
 * @author Ankit Balyan - sf.ankit@gmail.com
 **/
function addBrand()
{
	?>
	<div class="container">
		<div class="row">
			<div class="col-md-8 col-sm-6 ">
				<section>
					<header>
						<h3>Add Brand</h3>
						<hr>
					</header>
				    <form class="form-horizontal" action="#" method="POST" name="createBrand" id="createBrand">
				    	<div class="form-group form-group-lg">
				    		<label class="col-xs-2 control-label">Active</label>
				    		<div class="col-xs-4">
				                <select name="brand[brand_actv_ind]" class="selectpicker">
				                	<option value="1">Yes</option>
				                	<option value="0">No</option>
				                </select>
				                <label for="brand[brand_actv_ind]" class=" help-block error"></label>
				            </div>
				            <label class="col-xs-2 control-label">Parent Brand</label>
				            <div class="col-xs-4">
				                <select name="brand[brand_parent_id]" class="selectpicker">
				                	<option value="0">Root Brand</option>
				                	<option value="10">Brand1 </option>
				                	<option value="5">Brand2</option>
				                </select>
				                <label for="brand[brand_parent_id]" class=" help-block error"></label>
				    		</div>
				    	</div>
				        <div class="form-group form-group-lg">
				            <label class="col-xs-2 control-label">Brand Name</label>
				            <div class="col-xs-10">
				                <input type="text" name="brand[brand_name]" maxlength="100" class="form-control text required" placeholder="Brand Name">
				                <label for="brand[brand_name]" class=" help-block error"></label>
				            </div>
				        </div>
				        <div class="form-group form-group-lg">
				            <label class="col-xs-2 control-label">Description</label>
				            <div class="col-xs-10">
				                <textarea name="brand[brand_fullname]" class="form-control textDescription text required" placeholder="Brand Description"></textarea>
				                <label for="brand[brand_fullname]" class=" help-block error"></label>
				            </div>
				        </div>
		                <div class="form-group form-group-lg">
				            <div class="col-xs-offset-2 col-xs-10">
				                <input type="submit" class="submit btn btn-primary" value="Add Brand">
				            </div>
				        </div>
				    </form>
				</section>
			</div>
		</div>
	</div>
	<script type="text/javascript">
		jQuery(document).ready(function($) {
			     jQuery('.selectpicker').selectpicker({
				    //style: 'btn-default',
				    //size: 4
				  });


			jQuery('#createBrand').validate({
            submitHandler: function(form) {
                var formData = new FormData(jQuery('#createBrand')[0]);
                formData.append("action",'add_brand');
                jQuery.ajax({
                        type: 'POST',
                        url: ajaxurl,
                        data: formData,
                        cache: false,
                        contentType: false,
                        processData: false,
                         beforeSend:function (argument) {
                           //jQuery('.overlay').fadeIn('slow'); 
                           //jQuery('.response').fadeIn('slow').html("Processing...");
                        },
                        success: function(result) {
                        	alert(result);
                           //jQuery('.response').fadeIn('slow').html(result).delay(5000).fadeOut('slow');
                           //jQuery('.overlay').delay(5100).fadeOut('slow');
                           //window.location.href = window.location.href;
                        }
                    });
             }
        	});
			
		});
	</script>
	<?php
	
}

/**
 * displayBrands function
 * this funciton is to renders the list of all brands and aub brand alond with thier parent and child relation.
 * @return void
 * @author Ankit Balyan - sf.ankit@gmail.com
 **/
function displayBrands()
{
	global $status;
	$Brands = getBrands();
	if(count($Brands))
	{?>
		<table id="displayBrand" class="table table-striped">
			<thead>
				<tr>
					<th>Brand Name</th>
					<th>Brand Description</th>
					<th>Patrent Brand</th>

					<th>Status</th>
					<th>Created date</th>
				</tr>
			</thead>
			<tbody>
			<?php foreach ($Brands as $brand): ?>
				<tr>
					<td><a href=""><span class="glyphicon glyphicon-edit"></span></a> <?php echo $brand->brand_name ?></td>
					<td><?php echo $brand->brand_desc ?></td>
					<td><?php echo $brand->brand_parent_id ? $brand->parent_brand : '-- Main Brand --' ?></td>

					<td><?php echo $status["$brand->brand_actv_ind"] ?></td>
					<td><?php echo date('d M, Y', strtotime($brand->created_dt))  ?></td>
				</tr>
			<?php endforeach ?>
			</tbody>
		</table>
	<?php }
}