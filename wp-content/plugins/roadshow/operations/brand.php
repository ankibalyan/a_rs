<?php 
//add_action( $tag, $function_to_add, $priority, $accepted_args );
add_action('wp_ajax_add_brand','createBrand');

/**
 * createBrand function
 * this funciton is create or add a new brand or sub brand in the database table 'rw_brnads'
 * this function can be called via ajax from the admin login only.
 * @return void
 * @author Ankit Balyan - sf.ankit@gmail.com
 **/
	function createBrand()
	{
		extract($_POST);
		global $wpdb;
		$table = 'rw_brands';
		$data = array(
			'brand_name' => (isset($brand['brand_name'])) ? $brand['brand_name'] : '',
			'brand_slug' => (isset($brand['brand_slug'])) ? $brand['brand_slug'] : '',
			'brand_desc' => (isset($brand['brand_desc'])) ? $brand['brand_desc'] : '',

			'brand_parent_id' => (isset($brand['brand_parent_id'])) ? $brand['brand_parent_id'] : '',
			'brand_actv_ind' => (isset($brand['brand_actv_ind'])) ? $brand['brand_actv_ind'] : '',
			'created_dt' => date('Y-m-d H:i:s'),

			'modified_dt' => date('Y-m-d H:i:s'),
		);
		$format = array('%s','%s','%s', '%d','%d','%s', '%s');
		$wpdb->insert($table,$data,$format);
		echo "Brand created Sucessfuly";
		die;
	}

/**
 * getBrands function
 * this funciton is get the details of a particluar brand specified by the id or provide a list of all brands and sub brands.
 * @param id - brand Id to get the details of this brand (optional)
 * @return stdObject
 * @author Ankit Balyan - sf.ankit@gmail.com
 **/
function getBrands($id = NULL)
{
	global $wpdb;
	if ($id) {
		$sql = "SELECT parent.*, child.brand_name as parent_brand,child.brand_image as parent_image 
				FROM rw_brands AS parent
			        INNER JOIN
			    rw_brands AS child ON parent.brand_parent_id = child.brand_id
			     where parent.brand_id = $id";
	}
	else{

		$sql = "SELECT parent.*, child.brand_name as parent_brand
				FROM
			    rw_brands AS parent
			        INNER JOIN
			    rw_brands AS child ON parent.brand_parent_id = child.brand_id
			    OR parent.brand_parent_id = 0
			    group by parent.brand_id";
	}
	
	return $wpdb->get_results($sql);
}

/**
 * getTopOrSubBrands function
 * this funciton is get list of all the top brands or the subbrands or a particluar brands if id is specified.
 * @param id - brand Id to get the list of sub brands of this particular top brand
 * @return stdObject
 * @author Ankit Balyan - sf.ankit@gmail.com
 **/
function getTopOrSubBrands($id = NULL)
{
	global $wpdb;
	if ($id) {
		$sql = "SELECT parent.*, child.brand_name as parent_brand,child.brand_image as parent_image 
				FROM rw_brands AS parent
			        LEFT OUTER JOIN
			    rw_brands AS child ON parent.brand_parent_id = child.brand_id
			     where parent.brand_parent_id = $id";
	}
	else{

		$sql = "SELECT parent.*
				FROM
			    rw_brands AS parent
				where parent.brand_parent_id = 0";
	}
	return $wpdb->get_results($sql);
}
add_action('wp_ajax_subbrand_selectbox','subbrandSelectbox');

/**
 * subbrandSelectbox function
 * this funciton is generates the select box for a particular top brand.
 * the funciton can be called via ajax by action command 'subbrand_selectbox', provided brand_id as post variable.
 * @return void
 * @author Ankit Balyan - sf.ankit@gmail.com
 **/
function subbrandSelectbox()
{
	if(isset($_POST['brand_id']) && $_POST['brand_id'])
	{
		$subbrands = getTopOrSubBrands($_POST['brand_id']);
		?>
			<label class="col-xs-2 control-label">Sub Brand</label>
	        <div class="col-xs-3">
	            <select name="brand[subbrand_id]" class="selectPicker" id="">
	            	<option value="0">None</option>
	            	<?php foreach ($subbrands as $key => $subbrand): ?>
	            		<option value="<?php echo $subbrand->brand_id ?>"><?php echo $subbrand->brand_name ?></option>
	            	<?php endforeach ?>
	            </select>
	            <label for="brand[subbrand_id]" class=" help-block error"></label>
	        </div>
		<?php
	}
	die;
}