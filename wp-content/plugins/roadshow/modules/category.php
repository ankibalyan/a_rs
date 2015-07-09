<?php
/**
 * uploadCategory function
 * this funciton is to render a form to upload the categroy csv file on upload categroy page.
 * @return void
 * @author Ankit Balyan - sf.ankit@gmail.com
 **/
function uploadCategory()
{
	?>
	<form id="wp_csv_to_db_form" method="post" action="">
                    <table class="form-table"> 
                        <input type="hidden" value="rw_category" name="table_select" id="table_select">
                        
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
 * addCategory function
 * this funciton is to render a form to add a new category to shotym, but it will not affect to the front end, without maping a category to a brand and user.
 * @return void
 * @author Ankit Balyan - sf.ankit@gmail.com
 **/
function addCategory()
{
	?>
	<div class="container">
		<div class="row">
			<div class="col-md-8 col-sm-6 ">
				<section>
					<header>
						<h3>Add Category</h3>
						<hr>
					</header>
				    <form class="form-horizontal" action="#" method="POST" name="createCategory" id="createCategory">
				        <div class="form-group form-group-lg">
				            <label class="col-xs-2 control-label">Name</label>
				            <div class="col-xs-10">
				                <input type="text" name="category[category_name]" maxlength="255" class="form-control text required" placeholder="Category Name">
				                <label for="category[category_name]" class=" help-block error"></label>
				            </div>
				        </div>
				        <div class="form-group form-group-lg">
				            <label class="col-xs-2 control-label">Shortcode</label>
				            <div class="col-xs-10">
				                <input type="text" name="category[category_shortcode]" maxlength="4" class="form-control" placeholder="Category Shortcode">
				                <label for="category[category_shortcode]" class=" help-block error"></label>
				                <span class="help-block error"></span>
				            </div>
				        </div>

		                <div class="form-group form-group-lg">
				            <div class="col-xs-offset-2 col-xs-10">
				                <input type="submit" class="submit btn btn-primary" value="Create Category">
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


			jQuery('#createCategory').validate({
            submitHandler: function(form) {
                var formData = new FormData(jQuery('#createCategory')[0]);
                formData.append("action",'add_category');
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
 * displayCategories function
 * this funciton is to renders the list of all categories that can be used by any of the brands.
 * @return void
 * @author Ankit Balyan - sf.ankit@gmail.com
 **/
function displayCategories()
{
	$Categories = getCategories();
	if(count($Categories))
	{?>
		<table id="displayCategory" class="table table-striped">
			<thead>
				<tr>
					<th>Category Id</th>
					<th>Category Name</th>

					<th>Shortcode</th>
					<th>Size Type</th>
					<th>Created date</th>
				</tr>
			</thead>
			<tbody>
			<?php foreach ($Categories as $category): ?>
				<tr>
					<td><a href=""><span class="glyphicon glyphicon-edit"></span></a> <?php echo $category->category_id ?></td>
					<td><?php echo $category->category_name ?></td>

					<td><?php echo $category->category_shortcode ?></td>
					<td><?php echo $category->category_attr_type ?></td>
					<td><?php echo date('d M, Y', strtotime($category->created_dt))  ?></td>
				</tr>
			<?php endforeach ?>
			</tbody>
		</table>
	<?php }
}