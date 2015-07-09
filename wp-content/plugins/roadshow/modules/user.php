<?php 
/**
 * uploadUsers function
 * this funciton is to render a form to upload the users csv file on upload users page, where uploading functions are being written in user's operation file and main.js  for javascript funcnality
 * @return void
 * @author Ankit Balyan - sf.ankit@gmail.com
 **/
function uploadUsers()
{
	?>
	<form id="wp_csv_to_db_form" method="post" action="">
                    <table class="form-table"> 
                        <input type="hidden" value="rw_users" name="table_select" id="table_select">
                        
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
 * uploadUsers function
 * this funciton is to render a form to add a new user to shotym, a user can also be maped to a levels, and  brand as well.
 * @return void
 * @author Ankit Balyan - sf.ankit@gmail.com
 **/
function addUser()
{
	$users = getRwUsers(null,1);
	$levels = getRwUsersLvl();
	$brands = get_nested();
	?>
	<div class="container">
		<div class="row">
			<div class="col-md-8 col-sm-6 ">
				<section>
					<header>
						<h3>Create User</h3>
						<hr>
					</header>
				    <form class="form-horizontal" action="#" method="POST" name="createUser" id="createUser">
				        <div class="form-group form-group-lg">
				            <label class="col-xs-2 control-label">Username</label>
				            <div class="col-xs-10">
				                <input type="text" name="user[user_name]" maxlength="100" class="form-control text required" placeholder="Username">
				                <label for="user[user_name]" class=" help-block error"></label>
				            </div>
				        </div>
				        <div class="form-group form-group-lg">
				            <label class="col-xs-2 control-label">Email</label>
				            <div class="col-xs-10">
				                <input type="email" name="user[user_email]" maxlength="255" class="form-control email required" placeholder="Email Id">
				                <label for="user[user_email]" class=" help-block error"></label>
				                <span class="help-block error"></span>
				            </div>
				        </div>
				        <div class="form-group form-group-lg">
				            <label class="col-xs-2 control-label">Full Name</label>
				            <div class="col-xs-10">
				                <input type="text" name="user[user_fullname]" maxlength="255" class="form-control text required" placeholder="Fullname">
				                <label for="user[user_fullname]" class=" help-block error"></label>
				            </div>
				        </div>
				        <div class="form-group form-group-lg">
				            <label class="col-xs-2 control-label">Phone</label>
				            <div class="col-xs-10">
				                <input type="text" name="user[user_phone]" minlength="8" maxlength="20" class="form-control text required" placeholder="User Phone Number">
				                <label for="user[user_phone]" class=" help-block error"></label>
				            </div>
				        </div>
				        <div class="form-group form-group-lg">
				            <label class="col-xs-2 control-label">Related to</label>
				            <div class="col-xs-3">
				                <select name="user[user_parent_id]" class="selectPicker">
				                	<option value="">None</option>
				                	<?php foreach ($users as $user): ?>
				                		<option value="<?php echo $user->user_id ?>"><?php echo $user->user_name ?></option>
				                	<?php endforeach ?>
				                </select>
				                <label for="user[user_parent_id]" class=" help-block error"></label>
				            </div>
				            <label class="col-xs-2 control-label">Level</label>
				            <div class="col-xs-3">
				                <select name="user[user_lvl]" class="selectPicker">
				                	<?php foreach ($levels as $key => $level): ?>
				                		<option value="<?php echo $level->lvl_id ?>"><?php echo $level->lvl_name ?></option>	
				                	<?php endforeach ?>
				                </select>
				                <label for="user[user_lvl]" class=" help-block error"></label>
				            </div>

				        </div>
				        <div class="form-group form-group-lg">
				        	<label class="col-xs-2 control-label">Brand Access</label>
				            	<div class="col-sm-10 col-sm-offset-2 treeview" id="selectBrandsAccess">
								    <ul id="selectBrandsAccess" class="">
								        <?php foreach ($brands as $value): ?>
								                <li><span><i class="fa fa-angle-double-right"></i> <?php echo $value['brand_name'] ?></span><span class="checkbox-wrap"><span class="custom-checkbox"><input name="brand_box[]" type="checkbox" value="<?php echo $value['brand_id'] ?>" id="brand_id<?php echo $value['brand_id'] ?>" /><label for="brand_id<?php echo $value['brand_id'] ?>"></label></span></span><hr class="bottom-line"/>
<?php /*
								                <?php if(isset($value['children']) && is_array($value['children'])): ?>
								                    <ul>
								                        <?php foreach ($value['children'] as $subitem): ?>
								                            <li><i class="fa fa-angle-right"></i><span><?php echo $subitem['brand_name']; ?></span><span class="checkbox-wrap"><span class="custom-checkbox"><input name="brand_box[]" type="checkbox" value="<?php echo $subitem['brand_id']; ?>" id="brand_id<?php echo $subitem['brand_id']; ?>" /><label for="subbrand_id<?php echo $subitem['brand_id']; ?>"></label></span></span>
								                            </li>
								                        <?php endforeach ?>
								                    </ul>
								                <?php endif; ?>
*/?>
								                 </li>
								        <?php endforeach ?>
								    </ul>
							  	</div>

				        </div>
				        <div class="form-group form-group-lg">
				            <label class="col-xs-2 control-label">Source</label>
				            <div class="col-xs-10">
				                <input type="text" name="user[source]" class="form-control text" maxlength="255" placeholder="Source of user">
				                <label for="user[source]" class=" help-block error"></label>
				            </div>
				        </div>
				        <div class="form-group form-group-lg">
				            <label class="col-xs-2 control-label">Password</label>
				            <div class="col-xs-10">
				                <input type="password" name="user[user_password]" minlength="4" maxlength="15" class="form-control required" placeholder="Passowrd">
				                <label for="user[user_password]" class=" help-block error"></label>
				            </div>
				        </div>
		                <div class="form-group form-group-lg">
				            <div class="col-xs-offset-2 col-xs-10">
				                <input type="submit" class="submit btn btn-primary" value="Create User">
				            </div>
				        </div>
				        <div class="form-group form-group-lg">
				            <div class="col-xs-offset-2 col-xs-10">
				                <p class="text-warning" id="errors"></p>
				            </div>
				        </div>
				    </form>
				</section>
			</div>
		</div>
	</div>
<style>
	/*Treeview brand list style*/
 .treeview, .treeview ul {padding: 0;margin: 0;list-style: none;}
 .treeview li .bottom-line{height: 1px;clear: both;border: none;border-bottom: 1px solid #d2d2d2;width: 500px;margin: 0;padding: 5px 0;}
 .treeview ul {margin-top: 4px;}
 ul.treeview{margin-top: 4px;width: 500px;}
 .treeview .hitarea {}
* html .hitarea {display: inline;float:none;}/* fix for IE6 */
 .treeview li { margin: 0;padding: 5px 0pt 5px 16px;font-family: 'Lato', sans-serif;font-size:16px;color: #7b7b7b;text-transform: capitalize;position: relative;list-style: none;}
 .treeview li .checkbox-wrap{display: inline-block;float: right;}
 .treeview li .fa-angle-double-right,  .treeview .fa-angle-right{ font-size: 20px;padding-right: 8px;}
 .treeview li ul{background: transparent;list-style: none;}
 .treeview li ul li{padding: 5px 0pt 5px 30px;list-style: none!important;}
 .treeview li ul li .bottom-line{width: 470px;}
 .treeview li ul li ul li{padding: 7px 16px 7px 28px;border-bottom: 1px solid #d2d2d2;width: 470px;list-style: none!important;}
 .treeview li ul li ul li .bottom-line{width: 450px;}
 .treeview li ul li ul li ul li{padding: 7px 19px 7px 34px;width: 445px;border-bottom: none;border-top: 1px solid #d2d2d2;list-style: none!important;}
 .treeview a.selected {background-color: #eee;}
 #treecontrol { margin: 1em 0; display: none; }
 .treeview .hover { color:#000; cursor: pointer; }
 .treeview .expandable-hitarea { background-position: -82px -2px; }
 .treeview div.lastCollapsable-hitarea,  .treeview div.lastExpandable-hitarea { background-position: 0; }
</style>
	<script type="text/javascript">
		jQuery(document).ready(function($) {

			jQuery('#selectTopBrand').load(function(event) {
				loadSubrands(this.value);
			});
			jQuery('#selectTopBrand').change(function(event) {
				loadSubrands(this.value);
			});

			jQuery('#createUser').validate({
            submitHandler: function(form) {
                var formData = new FormData(jQuery('#createUser')[0]);
                formData.append("action",'add_user');
                jQuery.ajax({
                        type: 'POST',
                        url: ajaxurl,
                        data: formData,
                        cache: false,
                        contentType: false,
                        processData: false,
                         beforeSend:function (argument) {
                           jQuery('#errors').fadeIn('slow').html("Processing...");
                        },
                        success: function(result) {
                           jQuery('#errors').fadeIn('slow').html(result).delay(5000).fadeOut('slow');
                        }
                    });
             }
        	});
			jQuery("#selectBrandsAccess").treeview({
	            animated: "fast",
	            collapsed: true
    		});
		});

function loadSubrands (id) {
	jQuery.ajax({
        type: 'POST',
        url: ajaxurl,
        data: {action: 'subbrand_selectbox', brand_id: id},
        cache: false,
         beforeSend:function (argument) {
           //jQuery('.overlay').fadeIn('slow'); 
           //jQuery('.response').fadeIn('slow').html("Processing...");
        },
        success: function(result) {
        	jQuery('#AccessibleSubBrands').html(result);
           //jQuery('.response').fadeIn('slow').html(result).delay(5000).fadeOut('slow');
           //jQuery('.overlay').delay(5100).fadeOut('slow');
        }
    });
}
	</script>
	<?php
	
}

/**
 * displayUsers function
 * this funciton is to renders the list of all users with fewer info, also user can be made active/ inactive or delete from the same page.
 * @return void
 * @author Ankit Balyan - sf.ankit@gmail.com
 **/
function displayUsers()
{
	$rwUsers = getRwUsers();
	if(count($rwUsers))
	{?>
		<table id="displayUsers" class="table table-striped">
			<thead>
				<tr>
					<th>User Id</th>
					<th>Username</th>
					<th>Full Name</th>

					<th>Email</th>
					<th>Phone</th>
					<th>Source</th>

					<th>Level</th>
					<th>Related To</th>
					<th><input type="checkbox" name="user_status_check" id="user_id_0"  class="bootstrapSwitch" ></th>
					<th>Delete</th>
				</tr>
			</thead>
			<tbody>
			<?php foreach ($rwUsers as $user): ?>
				<tr>
					<td><a href=""><span class="glyphicon glyphicon-edit"></span></a> <?php echo $user->user_id ?></td>
					<td><?php echo $user->user_name ?></td>
					<td><?php echo $user->user_fullname ?></td>

					<td><?php echo $user->user_email ?></td>
					<td><?php echo $user->user_phone ?></td>
					<td><?php echo $user->source ?></td>

					<td><?php echo $user->lvl_name ?></td>
					<td><?php echo ($user->user_parent_id) ? $user->parent_user : 'None' ?></td>
					<td><input type="checkbox" name="user_status_check" id="user_id_<?php echo $user->user_id ?>" data-user-id="<?php echo $user->user_id ?>" class="bootstrapSwitch" <?php echo ($user->user_actv_ind) ? 'checked' : ''; ?>></td>
					<td><a href="javascript:void(0)" onclick="delUser(<?php echo $user->user_id ?>)" ><i class="fa fa-close fa-lg"></i></a></td>
				</tr>
			<?php endforeach ?>
			</tbody>
		</table>

		<script>
function delUser (id) {
	jQuery.ajax({
                        type: 'POST',
                        url: ajaxurl,
                        data: {action: 'user_delete', user_id: id},
                        cache: false,
                         beforeSend:function (argument) {
                           //jQuery('.overlay').fadeIn('slow'); 
                           //jQuery('.response').fadeIn('slow').html("Processing...");
                        },
                        success: function(result) {
                           //jQuery('.response').fadeIn('slow').html(result).delay(5000).fadeOut('slow');
                           //jQuery('.overlay').delay(5100).fadeOut('slow');
                           window.location.href = window.location.href;
                        }
                    });
}

		</script>
	<?php }
}