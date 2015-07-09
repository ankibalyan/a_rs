<?php 
//add_action( $tag, $function_to_add, $priority, $accepted_args );
add_action('wp_ajax_add_category','createCategory');
/**
 * createCategory function
 * this funciton is create or add a new brand or sub brand in the database table 'rw_brnads'
 * this function can be called via ajax from the admin login only.
 * @return void
 * @author Ankit Balyan - sf.ankit@gmail.com
 **/
	function createCategory()
	{
		extract($_POST);
		global $wpdb;
		$table = 'rw_category';
		$data = array(
			'category_name' => (isset($category['category_name'])) ? $category['category_name'] : '',
			'category_shortcode' => (isset($category['category_shortcode'])) ? $category['category_shortcode'] : '',

			'created_dt' => date('Y-m-d H:i:s'),
			'modified_dt' => date('Y-m-d H:i:s'),
		);
		$format = array('%s','%s', '%s','%s');
		$wpdb->insert($table,$data,$format);
		echo "Categroy created Sucessfuly";
		die;
	}
/**
 * getCategories function
 * this funciton is to get the list of all those categories which accessable to the current logged in user,. exculded arvind IT users,
 * it will also give the list of categories under a particluar brand if brand if is provided in parameters.
 * @todo get the details of a particular id id category id is provided
 * @param id - category id, to get the details of a particular category
 * @param brand_id - get the list of categories under this brand_id
 * @return stdObject
 * @author Ankit Balyan - sf.ankit@gmail.com
 **/
function getCategories($id = NULL, $brand_id = NULL)
{
	global $wpdb;
	$user_id = isLogin();
	if ($id) {
		return array();
	}
	else{
		if(!isArvindUser())
		{
			$sql = "SELECT 
					    category.*
					FROM
					    rw_category AS category
					        LEFT OUTER JOIN
					    rw_brand_user_map AS user_brand ON user_brand.user_id = $user_id
					        LEFT OUTER JOIN
					    rw_brands AS brand ON brand.brand_id = user_brand.brand_id
					        OR brand.brand_parent_id = user_brand.brand_id
					        LEFT OUTER JOIN
					    rw_brand_category_map AS brand_category 
							ON brand_category.brand_id = brand.brand_id
					WHERE
					    category.category_id = brand_category.category_id";

			if(count($brand_id))
			{

				if(is_array($brand_id))
				{	$i = 0;
					foreach ($brand_id as $key => $id) {
						if($i == 0)
						{
							$sql .= " and ( brand_category.brand_id = $id ";
						}
						else{
							$sql .= " or brand_category.brand_id = $id ";
						}
						$i++;
					}
					$sql .= " ) ";
				}
				else{
					$sql .= (isset($brand_id)) ? " and brand_category.brand_id = $brand_id" : "";
				}
			}

			$sql .= " group by category.category_id";

		}
		else{
			$sql = "SELECT * FROM rw_category";
		}
	}
	return $wpdb->get_results($sql);
}

/**
 * getSiteType function
 * this funciton is to get the type of sizes belongs to this particular category
 * @return stdObject
 * @author Ankit Balyan - sf.ankit@gmail.com
 **/
function getSiteType($id = NULL)
{
	global $wpdb;
	if ($id) {
		return array();
	}
	else{
		$sql = "SELECT * FROM rw_category_size_type";
	}

	return $wpdb->get_results($sql);
}