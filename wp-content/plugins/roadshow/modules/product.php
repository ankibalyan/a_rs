<?php 
/**
 * uploadProducts function
 * this funciton is to render a form to upload the products csv file on upload products page, where uploading functions are being written in products's operation file and main.js  for javascript funcnality
 * @return void
 * @author Ankit Balyan - sf.ankit@gmail.com
 **/
function uploadProducts()
{
	?>
	<form id="wp_csv_to_db_form" method="post" action="">
                    <table class="form-table"> 
                        <input type="hidden" value="rw_products" name="table_select" id="table_select">
                        
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
 * mapImages function
 * this funciton is to render a form to upload the product image mapping csv file on upload products's image upload page.
 * @return void
 * @author Ankit Balyan - sf.ankit@gmail.com
 **/
function mapImages()
{
	?>
	<form id="wp_csv_to_db_form" method="post" action="">
                    <table class="form-table"> 
                        <input type="hidden" value="rw_images" name="table_select" id="table_select">
                        
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
 * addProducts function
 * this funciton is to render a form to add a new product to shotym.
 * @todo createing the form and implementation
 * @return void
 * @author Ankit Balyan - sf.ankit@gmail.com
 **/
function addProducts()
{

}

/**
 * displayProducts function
 * this funciton is to renders the list of all products with fewer info.
 * @return void
 * @author Ankit Balyan - sf.ankit@gmail.com
 **/
function displayProducts()
{
	$products = getProducts();
	if(count($products))
	{?>
		<table id="displayUsers" class="table table-striped">
			<thead>
				<tr>
					<th>Product Code</th>
					<th>Product</th>
					<th>Description</th>

					<th>Price</th>
					<th>Color</th>
					<th>Code</th>

				</tr>
			</thead>
			<tbody>
			<?php foreach ($products as $product): ?>
				<tr>
					<td><a href=""><span class="glyphicon glyphicon-edit"></span></a> <?php echo $product->product_code ?></td>
					<td><?php echo $product->product_name ?></td>
					<td><?php echo $product->product_desc ?></td>

					<td><?php echo $product->product_price ?></td>
					<td><?php echo $product->product_color ?></td>
					<td><?php echo $product->product_code ?></td>

				</tr>
			<?php endforeach ?>
			</tbody>
		</table>
	<?php }
}