<?php 
//add_action( $tag, $function_to_add, $priority, $accepted_args );
global $products;
add_action('wp_ajax_add_product','createProduct');
/**
 * createProduct function
 * this function is to add a new product into the site from the admin side.
 * @todo add a new product from the admin panel
 * @return void
 * @author Ankit Balyan - sf.ankit@gmail.com
 **/
	function createProduct()
	{
		extract($_POST);
		global $wpdb;
		die;
	}

/******************************************************************************************
*
*This section used for importing the data to the 'rw_products' table from a csv file from the admin panel.
*
*
******************************************************************************************/
	$error_message = '';
	$success_message = '';
	$message_info_style = '';

		// If button is pressed to "Import to DB"
		if (isset($_POST['execute_button'])) {
			
			// If the "Select Table" input field is empty
			if(empty($_POST['table_select'])) {
				$error_message .= '* '.__('No Database Table was selected. Please select a Database Table.','wp_csv_to_db').'<br />';
			}
			// If the "Select Input File" input field is empty
			if(empty($_POST['csv_file'])) {
				$error_message .= '* '.__('No Input File was selected. Please enter an Input File.','wp_csv_to_db').'<br />';
			}
			// Check that "Input File" has proper .csv file extension
			$ext = pathinfo($_POST['csv_file'], PATHINFO_EXTENSION);
			if($ext !== 'csv') {
				$error_message .= '* '.__('The Input File does not contain the .csv file extension. Please choose a valid .csv file.','wp_csv_to_db');
			}
			
			// If all fields are input; and file is correct .csv format; continue
			if(!empty($_POST['table_select']) && !empty($_POST['csv_file']) && ($ext === 'csv')) {
				
				// If "disable auto_inc" is checked.. we need to skip the first column of the returned array (or the column will be duplicated)
				if(isset($_POST['remove_autoinc_column'])) {
					$db_cols = $wpdb->get_col( "DESC " . $_POST['table_select'], 0 );  
					unset($db_cols[0]);  // Remove first element of array (auto increment column)
				} 
				// Else we just grab all columns
				else {
					$db_cols = $wpdb->get_col( "DESC " . $_POST['table_select'], 0 );  // Array of db column names
				}
				// Get the number of columns from the hidden input field (re-auto-populated via jquery)
				$numColumns = $_POST['num_cols'];
				
				// Open the .csv file and get it's contents
				if(( $fh = @fopen($_POST['csv_file'], 'r')) !== false) {
					
					// Set variables
					$values = array();
					$too_many = '';  // Used to alert users if columns do not match
					
					while(( $row = fgetcsv($fh)) !== false) {  // Get file contents and set up row array
						//if(count($row) == $numColumns) {  // If .csv column count matches db column count
							$values[] = '("' . implode('", "', $row) . '")';  // Each new line of .csv file becomes an array
						//}
					}
					// If user elects to input a starting row for the .csv file
					if(isset($_POST['sel_start_row']) && (!empty($_POST['sel_start_row']))) {
						
						// Get row number from user
						$num_var = $_POST['sel_start_row'] - 1;  // Subtract one to make counting easy on the non-techie folk!  (1 is actually 0 in binary)
						
						// If user input number exceeds available .csv rows
						if($num_var > count($values)) {
							$error_message .= '* '.__('Starting Row value exceeds the number of entries being updated to the database from the .csv file.','wp_csv_to_db').'<br />';
							$too_many = 'true';  // set alert variable
						}
						// Else splice array and remove number (rows) user selected
						else {
							$values = array_slice($values, $num_var);
						}
					}
					
					// If there are no rows in the .csv file AND the user DID NOT input more rows than available from the .csv file
					if( empty( $values ) && ($too_many !== 'true')) {
						$error_message .= '* '.__('Columns do not match.','wp_csv_to_db').'<br />';
						$error_message .= '* '.__('The number of columns in the database for this table does not match the number of columns attempting to be imported from the .csv file.','wp_csv_to_db').'<br />';
						$error_message .= '* '.__('Please verify the number of columns attempting to be imported in the "Select Input File" exactly matches the number of columns displayed in the "Table Preview".','wp_csv_to_db').'<br />';
					}
					else {
						// If the user DID NOT input more rows than are available from the .csv file
						if($too_many !== 'true') {
							
							$db_query_update = '';
							$db_query_insert = '';
								
							// Format $db_cols to a string
							$db_cols_implode = implode(',', $db_cols);
								
							// Format $values to a string
							$values_implode = implode(',', $values);
							
							
							// If "Update DB Rows" was checked
							if (isset($_POST['update_db'])) {
								
								// Setup sql 'on duplicate update' loop
								$updateOnDuplicate = ' ON DUPLICATE KEY UPDATE ';
								foreach ($db_cols as $db_col) {
									$updateOnDuplicate .= "$db_col=VALUES($db_col),";
								}
								$updateOnDuplicate = rtrim($updateOnDuplicate, ',');
								
								
								$sql = 'INSERT INTO '.$_POST['table_select'] . ' (' . $db_cols_implode . ') ' . 'VALUES ' . $values_implode.$updateOnDuplicate;
								$db_query_update = $wpdb->query($sql);
							}
							else {
								$sql = 'INSERT INTO '.$_POST['table_select'] . ' (' . $db_cols_implode . ') ' . 'VALUES ' . $values_implode;
								$db_query_insert = $wpdb->query($sql);
							}
							
							// If db db_query_update is successful
							if ($db_query_update) {
								$success_message = __('Congratulations!  The database has been updated successfully.','wp_csv_to_db');
							}
							// If db db_query_insert is successful
							elseif ($db_query_insert) {
								$success_message = __('Congratulations!  The database has been updated successfully.','wp_csv_to_db');
								$success_message .= '<br /><strong>'.count($values).'</strong> '.__('record(s) were inserted into the', 'wp_csv_to_db').' <strong>'.$_POST['table_select'].'</strong> '.__('database table.','wp_csv_to_db');
							}
							// If db db_query_insert is successful AND there were no rows to udpate
							elseif( ($db_query_update === 0) && ($db_query_insert === '') ) {
								$message_info_style .= '* '.__('There were no rows to update. All .csv values already exist in the database.','wp_csv_to_db').'<br />';
							}
							else {
								$error_message .= '* '.__('There was a problem with the database query.','wp_csv_to_db').'<br />';
								$error_message .= '* '.__('A duplicate entry was found in the database for a .csv file entry.','wp_csv_to_db').'<br />';
								$error_message .= '* '.__('If necessary; please use the option below to "Update Database Rows".','wp_csv_to_db').'<br />';
							}
						}
					}
				}
				else {
					$error_message .= '* '.__('No valid .csv file was found at the specified url. Please check the "Select Input File" field and ensure it points to a valid .csv file.','wp_csv_to_db').'<br />';
				}
			}
		}
		// If there is a message - info-style
		if(!empty($message_info_style)) {
			echo '<div class="info_message_dismiss">';
			echo $message_info_style;
			echo '<br /><em>('.__('click to dismiss','wp_csv_to_db').')</em>';
			echo '</div>';
		}
		
		// If there is an error message	
		if(!empty($error_message)) {
			echo '<div class="error_message">';
			echo $error_message;
			echo '<br /><em>('.__('click to dismiss','wp_csv_to_db').')</em>';
			echo '</div>';
		}
		
		// If ther)e is a success message
		if(!empty($success_message)) {
			echo '<div class="success_message">';
			echo $success_message;
			echo '<br /><em>('.__('click to dismiss','wp_csv_to_db').')</em>';
			echo '</div>';
		}

/******************************************************************************************
*
*CSV imports ends here.
*
*
******************************************************************************************/
		
add_action('wp_ajax_product_load','loadMoreProducts');
add_action('wp_ajax_nopriv_product_load','loadMoreProducts');
/**
 * loadMoreProducts function
 * this funciton is to get more products from the same list (category, brand, price range) of products and outputs the json object for the product.
 * it can be called from ajax with action command 'product_load'.
 * @todo get the filter elements along with request the populate respective product.
 * @return void
 * @author Ankit Balyan - sf.ankit@gmail.com
 **/
function loadMoreProducts($id = NULL)
{
	global $wpdb;
	global $products;
	extract($_POST);
	$offset = (isset($offset)) ? $offset : 4;
	$start = (isset($start)) ? $start : 10;
    $table = "rw_products";
    $user_id = isLogin();
    	if(isArvindUSer())
    	{
    		$sql = "SELECT 
				    product.*, images.image_url
				FROM
				    rw_products AS product
				LEFT OUTER JOIN
				    rw_images AS images ON product.product_id = images.product_id and images.thumnail_ind = 1 LIMIT $offset, $start";
    	}
    else{
        	$sql = "SELECT 
				    product.*, images.image_url
				FROM
				    rw_products AS product
				LEFT OUTER JOIN
				    rw_images AS images ON product.product_id = images.product_id and images.thumnail_ind = 1 

				LEFT OUTER JOIN
                	rw_brand_user_map AS user_brand ON user_brand.user_id = $user_id
                LEFT OUTER JOIN
                	rw_brands AS brand ON brand.brand_id = user_brand.brand_id
                OR brand.brand_parent_id = user_brand.brand_id
	            where
	                product.brand_id = brand.brand_id
	            or
	                product.brand_id = brand.brand_parent_id
				LIMIT $offset, $start";
    	}
    $sql .= " GROUP BY product.product_id";
    $sql .= " LIMIT 0,100";
    $results = $wpdb->get_results($sql);
    wp_send_json( $results);
}

add_action('wp_ajax_search_product','searchProduct');
add_action('wp_ajax_nopriv_search_product','searchProduct');
/**
 * searchProduct function
 * this funciton is to get searched query products from the database table 'rw_product' of products based on 
 * product name, product description, product code and product color, and it will outputs the json object for the product.
 * @return void
 * @author Ankit Balyan - sf.ankit@gmail.com
 **/
	function searchProduct()
	{
		global $wpdb;
		extract($_POST);
		$sp = isset($sp) ? trim($sp) : '';
		$table = "rw_products";
		$user_id = isLogin();
		if(!isArvindUSer())
		{
			$sql = "SELECT 
				    product.*, images.image_url
				FROM
				    rw_products AS product
				        LEFT OUTER JOIN
				    rw_images AS images ON product.product_id = images.product_id
				        AND images.thumnail_ind = 1
				LEFT OUTER JOIN
                	rw_brand_user_map AS user_brand ON user_brand.user_id = $user_id
                LEFT OUTER JOIN
                	rw_brands AS brand ON brand.brand_id = user_brand.brand_id
                OR brand.brand_parent_id = user_brand.brand_id
	            where
	                (product.brand_id = brand.brand_id
	            or
	                product.brand_id = brand.brand_parent_id
	                )
				and
				    product.product_name LIKE '%$ps%'
				    OR product.product_desc LIKE '%$ps%'
				    OR product.product_code LIKE '%$ps%'
				    OR product.product_color LIKE '%$ps%'";
		}
		else
		{
	    	$sql = "SELECT 
				    product.*, images.image_url
				FROM
				    rw_products AS product
				        LEFT OUTER JOIN
				    rw_images AS images ON product.product_id = images.product_id
				        AND images.thumnail_ind = 1
				    where
				    product.product_name LIKE '%$ps%'
				    OR product.product_desc LIKE '%$ps%'
				    OR product.product_code LIKE '%$ps%'
				    OR product.product_color LIKE '%$ps%'";	
		}
		$sql .= " GROUP BY product.product_id";
		$sql .= " LIMIT 0,100";
		 $results = $wpdb->get_results($sql);
		 header('Content-Type: application/json');
		 wp_send_json( $results);
	}

add_action('wp_ajax_product_filter','filterProducts');
add_action('wp_ajax_nopriv_product_filter','filterProducts');
/**
 * filterProducts function
 * this funciton is to filter the products from the conditions specified by user, like category, brand, price range of products and outputs the json object for the product.
 * The funciton can be called from ajax method using action command 'product_filter'
 * @return void
 * @author Ankit Balyan - sf.ankit@gmail.com
 **/
	function filterProducts()
	{
		global $wpdb;
		$table = "rw_products";
		$user_id = isLogin();
		$where = getFilterCondition($_POST);
		$results =array();
		if(!isArvindUSer())
		{
			$sql = "SELECT 
				    product.*, images.image_url
				FROM
				    rw_products AS product
				        LEFT OUTER JOIN
				    rw_images AS images ON product.product_id = images.product_id
				        AND images.thumnail_ind = 1
				LEFT OUTER JOIN
                	rw_brand_user_map AS user_brand ON user_brand.user_id = $user_id
                LEFT OUTER JOIN
                	rw_brands AS brand ON brand.brand_id = user_brand.brand_id
                OR brand.brand_parent_id = user_brand.brand_id
	            where
	                (product.brand_id = brand.brand_id
	            or
	                product.brand_id = brand.brand_parent_id)
				and $where";
		}
		else
		{
			$sql = "SELECT 
				    product.*, images.image_url
				FROM
				    rw_products AS product
				        LEFT OUTER JOIN
				    rw_images AS images ON product.product_id = images.product_id
				        AND images.thumnail_ind = 1
				    where $where";
		}
		extract($_POST);
		if(isset($selecteBrandBox) && count($selecteBrandBox))
		{
			$results['categories'] = getCategories(null,$selecteBrandBox);
		}
	 		$sql .= " GROUP BY product.product_id";
	 		$sql .= " LIMIT 0,100";
	 	 $results['catelog'] = $wpdb->get_results($sql);

	 	 //json_encode($results);
	 	 header('Content-Type: application/json');
	 	 wp_send_json($results);
	}
/**
 * filterProducts function
 * this funciton is set the filter conditions for the filterProducts function, set where contion for category, brand, price range of product.
 * It will generates the sting and return it, if the conditions are not set, it will return 1 to make the sql statement true as it is.
 * @return string / TRUE
 * @author Ankit Balyan - sf.ankit@gmail.com
 **/
	function getFilterCondition($array=array())
	{
		extract($array);
		if (count($array))
		{
			$where = '';

			if(isset($selecteBrandBox) && count($selecteBrandBox))
			{
				foreach ($selecteBrandBox as $brand_id) {
					if($where == '') $where .= " product.brand_id = $brand_id";
					else $where .= " OR product.brand_id = $brand_id";
				}
			}

			if(isset($selectCategoryBox) && count($selectCategoryBox))
			{
				$k = 0;
				if($where != '')
					{ $where .= " and ( "; $k = 1; $i = 0;}
				foreach ($selectCategoryBox as $category_id) {
					if($i == 0)
					{
						if($where == '') $where .= " product.category_id = $category_id";
						else $where .= " product.category_id = $category_id";
					}
					else
					{
						if($where == '') $where .= " OR product.category_id = $category_id";
						else $where .= " OR product.category_id = $category_id";
					}
					$i++;
				}
				if($k == 1)
					$where .= " ) ";
			}

			if(isset($data_slider_value))
			{
				$data_slider_value = str_replace(' ','',$data_slider_value);
				$data_slider_value = str_replace(' ','',$data_slider_value);
				$data_slider_Array = explode(':', $data_slider_value);

				if($where == '') $where .= " product.product_price BETWEEN ".$data_slider_Array[0]." and ".$data_slider_Array[1];
					else $where .= " AND product.product_price BETWEEN ".$data_slider_Array[0]." and ".$data_slider_Array[1];
			}
			if($where == '')
				return 1;
			return $where;
		}
		return 1;
	}
/**
 * getProducts function
 * this funciton gives the details of the product if id is specified.
 * if id is not specified, it will generates the array of all product that can be accessed by the logged in user, it excludes the arvind IT level users.
 * for arvind IT users it gives array of all products
 * @param id - product id
 * @return void
 * @author Ankit Balyan - sf.ankit@gmail.com
 **/
function getProducts($id = NULL)
{
	global $wpdb;
    $table = "rw_products";
    $user_id = isLogin();
    if($id)
    {
        $sql = "SELECT * FROM $table where product_id = $id";
    }
    else{
    	if(!isArvindUSer())
    	{
    		$sql = "SELECT * FROM $table AS product
				
				LEFT OUTER JOIN
                	rw_brand_user_map AS user_brand ON user_brand.user_id = $user_id
                LEFT OUTER JOIN
                	rw_brands AS brand ON brand.brand_id = user_brand.brand_id
                OR brand.brand_parent_id = user_brand.brand_id
	            where
	                product.brand_id = brand.brand_id
	            or
	                product.brand_id = brand.brand_parent_id";
    	}
        else
        {
        	$sql = "SELECT * FROM $table";
        }
    }
    $sql .= " GROUP BY product.product_id";
    $sql .= " LIMIT 0,100";
    return $wpdb->get_results($sql);
}

/**
 * getColoredProducts function
 * this funciton is to provide the list of products from a particluar product's group code and excluded the the specified product. 
 * @param id, int - it is the product id to be excluded the from the list (required)
 * @param code, string - it is the prodict's group code (required)
 * @return stdObject
 * @author Ankit Balyan - sf.ankit@gmail.com
 **/
function getColoredProducts($id = NULL, $code = NULL)
{
	global $wpdb;
    $table = "rw_products";
    if($id && $code)
    {
        $sql = "SELECT 
				    product.*, images.image_url
				FROM
				    rw_products AS product
				        LEFT OUTER JOIN
				    rw_images AS images ON product.product_id = images.product_id 
				    	and images.thumnail_ind = 1 

				    where product.product_id != $id 
				    	and product.product_group_code = '$code' ";
    }
    else{
     	return array();  
    }

    return $wpdb->get_results($sql);
}


/**
 * getImages function
 * this funciton is get the all images of a particluar product excluded the thumnail images.
 * @param id, int - it is the product id of which images are required (required)
 * @return stdObject
 * @author Ankit Balyan - sf.ankit@gmail.com
 **/
function getImages($id='')
{
	global $wpdb;
    $table = "rw_images";
    if($id)
    {
        $sql = "SELECT 
				    images.*
				FROM
				    rw_images AS images
				    where images.product_id = $id and images.thumnail_ind = 0";
		return $wpdb->get_results($sql);
    }
    return array();
}

/**
 * getCatelog function
 * this funciton is get the all products with most of of the details alaong with them to render the catelog on home page, or get the details of a signle product if id is specified.
 * product catelog is based on the access level of current user, exluded from arvind Arvind IT Users
 * 
 * @param id, int - it is the product id of which images are required (optional)
 * @param offset - is to set the starting point of limits
 * @param limits - is to set the max total no. of results from the query 
 * @return stdObject
 * @author Ankit Balyan - sf.ankit@gmail.com
 **/
function getCatelog($id = NULL, $offSet = 0, $limits = 10)
{
	global $wpdb;
	global $products;
    $table = "rw_products";
    $user_id = isLogin();
    if($id)
    {
        $sql = "SELECT 
				    product.*, product.brand_id as brandId,product.category_id as categoryId, images.image_url, ratio.*
				FROM
				    rw_products AS product
				        LEFT OUTER JOIN
				    rw_images AS images ON product.product_id = images.product_id and images.thumnail_ind = 1
						LEFT OUTER JOIN
					user_category_size_map AS ratio
						ON product.brand_id = ratio.brand_id and product.category_id = ratio.category_id and ratio.user_id = ".isLogin()." 
				    where product.product_id = $id";
    }
    else{
    	if(!isArvindUser())
    	{
    		$sql = "SELECT 
				    product.*, images.image_url
				FROM
				    rw_products AS product
				        LEFT OUTER JOIN
				    rw_images AS images ON product.product_id = images.product_id and images.thumnail_ind = 1
				LEFT OUTER JOIN
                	rw_brand_user_map AS user_brand ON user_brand.user_id = $user_id
                LEFT OUTER JOIN
                	rw_brands AS brand ON brand.brand_id = user_brand.brand_id
                OR brand.brand_parent_id = user_brand.brand_id
	            where
	                product.brand_id = brand.brand_id
	            or
	                product.brand_id = brand.brand_parent_id";
	            //LIMIT $offSet, $limits";
    	}
    	else{
    		$sql = "SELECT 
				    product.*, images.image_url
				FROM
				    rw_products AS producty
				        LEFT OUTER JOIN
				    rw_images AS images ON product.product_id = images.product_id and images.thumnail_ind = 1";
	            //LIMIT $offSet, $limits";
    	}
    }
    $sql .= " GROUP BY product.product_id";
    $sql .= " LIMIT 0,100";
    $products = $wpdb->get_results($sql);
    return $wpdb->get_results($sql);
}
/**
 * getCatelogBy function
 * this funciton is get the calelog products by a particluar brand or by a caregory else it will retrun all the products
 * @param brand - gets the brand slug and fetches the results on that. (required if passing the second argument)
 * @param category - gets the category name and fetches the results on that.
 * @return stdObject
 * @author Ankit Balyan - sf.ankit@gmail.com
 **/
function getCatelogBy($brand = NULL, $category=NULL)
{
	global $wpdb;
	global $products;
    $table = "rw_products";
    $sql = '';
    if($brand !='')
    {
    	$brand = $wpdb->get_var("SELECT brand_id FROM rw_brands where brand_slug = '$brand'");

    	if($brand && $category != '')
    	{
    		$category = $wpdb->get_var("SELECT cmap.category_id FROM rw_brand_category_map as cmap 
    									LEFT OUTER JOIN rw_category as category on category.category_id = cmap.category_id
    									where cmap.brand_id = $brand and category.category_name = '$category'");

    		$sql = "SELECT 
				     product.*, images.image_url
				 	FROM
				     rw_products AS product
				    LEFT OUTER JOIN
				     rw_images AS images ON product.product_id = images.product_id and images.thumnail_ind = 1 
				     where product.brand_id = $brand and product.category_id = $category";
    	}
    	elseif($brand)
    	{
    		$sql = "SELECT 
				     product.*, images.image_url
				 	FROM
				     rw_products AS product
				    LEFT OUTER JOIN
				     rw_images AS images ON product.product_id = images.product_id and images.thumnail_ind = 1 
				     where product.brand_id = $brand";
    	}
    	else{
    		$sql = "SELECT 
				     product.*, images.image_url
				 	FROM
				     rw_products AS product
				    LEFT OUTER JOIN
				     rw_images AS images ON product.product_id = images.product_id and images.thumnail_ind = 1 
				     where 1";	
    	}
    }
    $sql .= " GROUP BY product.product_id";
    $sql .= " LIMIT 0,100";
    return $wpdb->get_results($sql);
}

/**
 * maxProductPrice function
 * this funciton is get maximum price from all the products
 * @return int
 * @author Ankit Balyan - sf.ankit@gmail.com
 **/
function maxProductPrice()
{
	global $wpdb;
	$sql = "SELECT MAX(product_price) FROM rw_products";
	return $wpdb->get_var($sql);
}

/**
 * minProductPrice function
 * this funciton is get minimum price from all the products
 * @return int
 * @author Ankit Balyan - sf.ankit@gmail.com
 **/
function minProductPrice()
{
	global $wpdb;
	$sql = "SELECT MIN(product_price) FROM rw_products";
	return $wpdb->get_var($sql);
}


/**
 * getSequenceProduct function
 * this funciton is get the sequenced product from the current productand gives you its previous and next product from a sequence defined in rw_product table iff it is available.
 * @return array
 * @author Ankit Balyan - sf.ankit@gmail.com
 **/
function getSequenceProduct($brand_id=NULL, $category_id = NULL, $seq = NULL)
{
	global $wpdb;
	$brand_id.$category_id.$seq;
	if($brand_id && $category_id && $seq)
	{
		$result = array();
		$sql = "SELECT product_id FROM rw_products where brand_id = $brand_id and category_id = $category_id and product_seq = $seq-1;";
		$result['prev'] = $wpdb->get_var($sql);
		$sql = "SELECT product_id FROM rw_products where brand_id = $brand_id and category_id = $category_id and product_seq = $seq+1;";
		$result['next'] = $wpdb->get_var($sql);
		return $result;
	}
}