<?php 
//add_action( $tag, $function_to_add, $priority, $accepted_args );
global $orders;
add_action('wp_ajax_product_order','createOrder');
add_action('wp_ajax_nopriv_product_order','createOrder');

/**
 * createOrder function
 * this funciton is place the order or add new order from the front end users
 * this fucntion will also checks whether the same ordering product is already placed in review or not, if it is then fallback.
 * this fuction also calulates the quantities based on the ratio's selected by the user and call's saveRatio to save it to along with user and category maping table.
 * the function can be called by ajax method using action command 'product_order'
 * @return void
 * @author Ankit Balyan - sf.ankit@gmail.com
 **/
	function createOrder()
	{
		extract($_POST);
		global $wpdb;
		$table = 'rw_orders';
		$category_id = getCategoryIdByProduct($product_id);
		$brand_id = getBrandIdByProduct($product_id);
		if($product_id)
		{
			$sql = "SELECT * FROM $table WHERE order_confirm_ind !=1 and product_id = $product_id and created_by = ".isLogin();
			$onReview = $wpdb->get_row($sql);
			if(count($onReview))
			{
				echo "Order is already placed for this product, Go to review page to edit or confirm.";
				die;
			}
		}
		if(isset($save_ratio))
			{
				$data = array(
					'user_id' => isLogin(),
					'brand_id' => (isset($brand_id)) ? $brand_id:'',
					'category_id' => (isset($category_id)) ? $category_id : '',				
					'category_size1' => (isset($product_size_type['product_size1'])) ? $product_size_type['product_size1'] : '',
					'category_size2' => (isset($product_size_type['product_size2'])) ? $product_size_type['product_size2'] : '',
					'category_size3' => (isset($product_size_type['product_size3'])) ? $product_size_type['product_size3'] : '',
					'category_size4' => (isset($product_size_type['product_size4'])) ? $product_size_type['product_size4'] : '',
					'category_size5' => (isset($product_size_type['product_size5'])) ? $product_size_type['product_size5'] : '',
					'category_size6' => (isset($product_size_type['product_size6'])) ? $product_size_type['product_size6'] : '',
					'category_size7' => (isset($product_size_type['product_size7'])) ? $product_size_type['product_size7'] : '',
					'category_size8' => (isset($product_size_type['product_size8'])) ? $product_size_type['product_size8'] : '',

					'modified_dt' => date('Y-m-d H:i:s'),
					'created_dt' => date('Y-m-d H:i:s')
				);
				$format = array('%d','%d', '%s','%s','%s','%s','%s','%s','%s','%s', '%s','%s');
				$insert = $wpdb->insert('user_category_size_map',$data,$format);

			}

		if(isset($order_type) && $order_type)
		{
			if(isset($total_qty) && $total_qty)
			{	
				$ratioSum = 0;
				foreach ($product_size_type as $size_type => $size_qty) {
					$ratioSum += $size_qty;
				}
				$each1 = $total_qty / $ratioSum;
				foreach ($product_size_type as $size_type => $size_qty) {
					$product_size_type[$size_type] = round($size_qty * $each1);
				}

				$total_qty = 0;
				foreach ($product_size_type as $size_type => $size_qty) {
					$total_qty +=  $size_qty;
				}
			}
			else{
				echo "Please Select Total Quantity";
				die;
			}
		}
			if(!$total_qty && !$product_grade)
			{
				echo "Invalid Data, Please Select either Grade or Quantities.";
				die;
			}
			$data = array(
				'product_id' => (isset($product_id)) ? $product_id : '',
				'product_grade' => (isset($product_grade)) ? $product_grade : '',
				'total_qty' => (isset($total_qty)) ? $total_qty : '',
				'total_price' => (isset($total_price)) ? $total_price : '',

				'order_size1' => (isset($product_size_type['product_size1'])) ? $product_size_type['product_size1'] : '',
				'order_size2' => (isset($product_size_type['product_size2'])) ? $product_size_type['product_size2'] : '',
				'order_size3' => (isset($product_size_type['product_size3'])) ? $product_size_type['product_size3'] : '',
				'order_size4' => (isset($product_size_type['product_size4'])) ? $product_size_type['product_size4'] : '',
				'order_size5' => (isset($product_size_type['product_size5'])) ? $product_size_type['product_size5'] : '',
				'order_size6' => (isset($product_size_type['product_size6'])) ? $product_size_type['product_size6'] : '',
				'order_size7' => (isset($product_size_type['product_size7'])) ? $product_size_type['product_size7'] : '',
				'order_size8' => (isset($product_size_type['product_size8'])) ? $product_size_type['product_size8'] : '',

				'created_by' => isLogin(),
				'modified_dt' => date('Y-m-d H:i:s'),
				'created_dt' => date('Y-m-d H:i:s')
			);
			$format = array('%d','%s','%d','%d', '%s','%s','%s','%s','%s','%s','%s','%s', '%d','%s','%s');
			$insert = $wpdb->insert($table,$data,$format);
		echo "Order Placed Sucessfuly";
		die;
	}
add_action('wp_ajax_save_review_order','confirmOrder');
add_action('wp_ajax_nopriv_save_review_order','confirmOrder');

/**
 * confirmOrder function
 * this funciton is to change the status of a order, like confirm or not, it also save the quantities or ratio's of the current  ordered item.
 * the function can be called by ajax method using action command 'save_review_order'
 * @return void
 * @author Ankit Balyan - sf.ankit@gmail.com
 **/
	function confirmOrder()
	{
		extract($_POST);
		global $wpdb;
		if(isset($selectByRatioButtonId) && $selectByRatioButtonId)
		{
			if(isset($total_qty) && $total_qty)
			{	
				$ratioSum = 0;
				foreach ($product_size_type as $size_type => $size_qty) {
					$ratioSum += $size_qty;
				}
				$each1 = $total_qty / $ratioSum;
				foreach ($product_size_type as $size_type => $size_qty) {
					$product_size_type[$size_type] = round($size_qty * $each1);
				}
			}
			else{
				echo "Please Select Total Quantity";
				die;
			}
		}
		if(isset($order_id) && $order_id)
		{
			$table = 'rw_orders';
			$data = array(
				
				'total_qty' => (isset($total_qty)) ? $total_qty : '',
				'total_price' => (isset($total_price)) ? $total_price : '',

				'order_size1' => (isset($product_size_type['product_size1'])) ? $product_size_type['product_size1'] : '',
				'order_size2' => (isset($product_size_type['product_size2'])) ? $product_size_type['product_size2'] : '',
				'order_size3' => (isset($product_size_type['product_size3'])) ? $product_size_type['product_size3'] : '',
				'order_size4' => (isset($product_size_type['product_size4'])) ? $product_size_type['product_size4'] : '',
				'order_size5' => (isset($product_size_type['product_size5'])) ? $product_size_type['product_size5'] : '',
				'order_size6' => (isset($product_size_type['product_size6'])) ? $product_size_type['product_size6'] : '',
				'order_size7' => (isset($product_size_type['product_size7'])) ? $product_size_type['product_size7'] : '',
				'order_size8' => (isset($product_size_type['product_size8'])) ? $product_size_type['product_size8'] : '',

				'order_confirm_ind' => (isset($confirmOrderStatus)) ? $confirmOrderStatus : 0,
				'modified_dt' => date('Y-m-d H:i:s')
			);
			$format = array('%d','%d', '%s','%s','%s','%s','%s','%s','%s','%s', '%d','%s');
			$where = array('order_id' => $order_id);
			$where_format = array('%d');
			$update = $wpdb->update( $table, $data, $where, $format = null, $where_format = null );
			if($update)
			echo "Order Saved Sucessfuly";
			else
			echo "Please try again later!";
		}
		die;
	}
add_action('wp_ajax_save_review_order_ratio','saveOrderRatio');
add_action('wp_ajax_nopriv_save_review_order_ratio','saveOrderRatio');
/**
 * saveOrderRatio function
 * this funciton is to save the order's quantitiy to the particluar placed order only
 * the function can be called by ajax method using action command 'save_review_order'
 * @return void
 * @author Ankit Balyan - sf.ankit@gmail.com
 **/
	function saveOrderRatio()
	{
		extract($_POST);
		global $wpdb;
		print_r($_POST);
		if(isset($order_id) && $order_id)
		{
			$table = 'rw_orders';
			$data = array(

				'order_size1' => (isset($product_size_type['product_size1'])) ? $product_size_type['product_size1'] : '',
				'order_size2' => (isset($product_size_type['product_size2'])) ? $product_size_type['product_size2'] : '',
				'order_size3' => (isset($product_size_type['product_size3'])) ? $product_size_type['product_size3'] : '',
				'order_size4' => (isset($product_size_type['product_size4'])) ? $product_size_type['product_size4'] : '',
				'order_size5' => (isset($product_size_type['product_size5'])) ? $product_size_type['product_size5'] : '',
				'order_size6' => (isset($product_size_type['product_size6'])) ? $product_size_type['product_size6'] : '',
				'order_size7' => (isset($product_size_type['product_size7'])) ? $product_size_type['product_size7'] : '',
				'order_size8' => (isset($product_size_type['product_size8'])) ? $product_size_type['product_size8'] : '',

				'modified_dt' => date('Y-m-d H:i:s')
			);
			$format = array('%s','%s','%s','%s','%s','%s','%s','%s', '%s');
			$where = array('order_id' => $order_id);
			$where_format = array('%d');
			$update = $wpdb->update( $table, $data, $where, $format = null, $where_format = null );
			if($update)
			echo "Ratio saved Sucessfuly";
			else
			echo "Please try again later!";
		}
		die;
	}	

add_action('wp_ajax_confrim_dealer_orders','confrimDealerOrders');
add_action('wp_ajax_nopriv_confrim_dealer_orders','confrimDealerOrders');

/**
 * confirmDeslerOrders function
 * this funciton is to confirm all the pending orders's of a particular dealer, iff he has filled some order's quantity. this function also check if a dealer is confirming his own order's or a distributor is confirming thier all dealers.
 * it also send mail to that particular dearler or user with his all order's  report in excel format, and cc to roadshow's admin's email as well.
 * the function can be called by ajax method using action command 'confirm_dealer_orders'
 * @return void
 * @author Ankit Balyan - sf.ankit@gmail.com
 **/
function confrimDealerOrders()
{
	global $wpdb;
	$table = 'rw_orders';
	$user_id = isset($_POST['user_id']) ? $_POST['user_id'] : isLogin();
	if($user_id)
	{
		if(isDealer())
		{
			$sql = "UPDATE $table SET `order_confirm_ind` = '1' WHERE `created_by` = $user_id and `total_qty` > 0 ";
			$update = $wpdb->query($sql);
			if($update)
			{
				$user = getRwUsers($user_id);
				if($user->user_parent_id)
					$dist = getRwUsers($user->user_parent_id);
				$body = "Dear ".$user->user_name.",You order has been received. Please find attached the details for your order. Our team will get in touch for final verification and confirmation. Meanwhile if you have any doubts, please contact our support team at arvindcare@arvindbrands.com<br><br><br>thanks and regards…";
				$mailData = array(
						'from' =>'',
						'to' => (isset($user->user_email)) ? $user->user_email : '',
						'cc' => get_option( 'admin_email' ),
						'bcc' => (isset($dist))? $dist->user_email: '',
						'replyto' => get_option( 'admin_email' ),
						'replytoname' => 'Shotym Admin',
						'subject' => 'Order\'s Report',
						'message' => $body,
						'attachment' => exportXls(null,FALSE),
					 );
				echo "All Orders have been Confirmed ";
				if(rwMail($mailData)){
					echo " and mailed ";
				}
				echo " sucessfully";
			}
			else
			echo "Please try again later!";
		}
		elseif($dist = isDistributor())
		{
			$data = array(
				'order_confirm_ind' => 1,
				'modified_dt' => date('Y-m-d H:i:s')
			);
			$sql = "SELECT 
					    users.*
					FROM
					    rw_users AS users
					        LEFT OUTER JOIN
					    rw_orders AS orders ON created_by = ".isLogin()."
					        LEFT OUTER JOIN
					    rw_products AS products ON orders.product_id = products.product_id
					        LEFT OUTER JOIN
					    rw_brands AS brands ON brands.brand_id = products.brand_id
					        LEFT OUTER JOIN
					    rw_brand_user_map AS brandUser ON brandUser.brand_id = brands.brand_id
					        OR brandUser.brand_id = brands.brand_parent_id
					WHERE
					    users.user_id = brandUser.user_id
					        AND users.user_lvl = 3";
			$brandUsers = $wpdb->get_results($sql);
			$sql = "UPDATE $table SET `order_confirm_ind` = '1' WHERE `created_by` = $user_id and `total_qty` > 0 ";
			$update = $wpdb->query($sql);
			if($update)
			{
				$user = getRwUsers($user_id);
				if($user->user_parent_id || isDistributor($user_id))
					$body = "Dear \"".$user->user_name."\",<br><br>Your final order has been received,<br><br>Please find attached the details for your order.
<br> <br> Our team will get in touch for final verification and confirmation. Meanwhile if you have any doubts, please contact our support team at arvindcare@arvindbrands.com<br><br><br>thanks and regards…";
					$mailData = array(
						'from' => '',
						'to' => (isset($user->user_email)) ? $user->user_email : '',
						'cc' => (isset($dist[0]))? $dist[0]->user_email: '',
						'bcc' => (isset($dist[0]))? $dist[0]->user_email: '',
						'replyto' => 'sf.ankit@gmail.com',
						'replytoname' => 'Roadshow Admin',
						'subject' => 'Order\'s Report',
						'message' => $body,
						'attachment' => exportXls(null,FALSE),
					);
				echo "All Orders have been Confirmed ";
				if($brandUsers)
				{
					foreach ($brandUsers as $key => $busr) {
					$brandData = array(
						'from' => '',
						'to' => (isset($busr->user_email)) ? $busr->user_email : '',
						'cc' => '',
						'bcc' => 'ankit@retailinsights.com',
						'replyto' => 'sf.ankit@gmail.com',
						'replytoname' => 'Roadshow Admin',
						'subject' => 'Order\'s Report',
						'message' => 'Your Brand\'s product is being Confirmed by the'.$user->user_fullname.' with email id '.$user->user_email.' <br> you may check your reports at http://shotym.com with your usersname and password.<br><br><br>thanks and regards…',
						'attachment' => '',
					);
					rwMail($brandData);
				}
				}
				if(rwMail($mailData)){
					echo " and mailed ";
				}
				echo " sucessfully";
			}
			else
			echo "No update Taken place! Please Try After Some Time";
		}
	}
	die;
}

add_action('wp_ajax_confrim_distributor_orders','confrimDistributorOrders');
add_action('wp_ajax_nopriv_confrim_distributor_orders','confrimDistributorOrders');

/**
 * confirmDistributorOrders function
 * this funciton is to confirm all the pending orders's of a particular distributir only, iff he has filled some order's quantity.
 * it also send a mail to that particular distibutor with his and his dealer's all order's report in excel format, and cc to roadshow's admin's email as well.
 * the function can be called by ajax method using action command 'confirm_dealer_orders'
 * @return void
 * @author Ankit Balyan - sf.ankit@gmail.com
 **/
function confrimDistributorOrders()
{
	global $wpdb;
	$table = 'rw_orders';
	$user_id = isset($_POST['user_id']) ? $_POST['user_id'] : isLogin();
	if($user_id)
	{
		if($dist = isDistributor())
		{
			$sql = "UPDATE $table SET `order_confirm_ind` = '1' WHERE `created_by` = $user_id and `total_qty` > 0 ";
			$update = $wpdb->query($sql);
			if($update)
			{
				$user = getRwUsers($user_id);
				$body = "Dear ".$user->user_name.",<br><br>Your final order has been received,<br><br>Please find attached the details for your order.
<br> <br> Our team will get in touch for final verification and confirmation. Meanwhile if you have any doubts, please contact our support team at arvindcare@arvindbrands.com<br><br><br>thanks and regards…";
					$mailData = array(
						'from' => '',
						'to' => (isset($user->user_email)) ? $user->user_email : '',
						'cc' => get_option( 'admin_email' ),
						'bcc' => (isset($dist[0]))? $dist[0]->user_email: '',
						'replyto' => get_option( 'admin_email' ),
						'replytoname' => 'Roadshow Admin',
						'subject' => 'Order\'s Report',
						'message' => $body,
						'attachment' => exportXls(null,FALSE),
					);
				echo "All Orders have been Confirmed ";
				
				if(rwMail($mailData)){
					echo " and mailed ";
				}
				echo " sucessfully";
			}
			else
			echo "No update Taken place! Please Try After Some Time";
		}
	}
	die;
}

add_action('wp_ajax_del_mass_order','deleteMassOrders');
add_action('wp_ajax_nopriv_del_mass_order','deleteMassOrders');

/**
 * deleteMassOrders function
 * this funciton is to delete the mass orders of a particular users by selecting the grad levels. it will delete only unconfirmed orders.
 * the function can be called by ajax method using action command 'del_mass_order'
 * @todo delete mass orders by a user, brand, subbrands, or categories.
 * @return void
 * @author Ankit Balyan - sf.ankit@gmail.com
 **/
	function deleteMassOrders($user_id = NULL)
	{
		extract($_POST);
		global $wpdb;
		$table = 'rw_orders';
		$user_id = ($user_id) ? $user_id : isLogin();
		// if(isset($massDel['user']) && $massDel['user'])
		// {
		// 	$sql = "DELETE FROM rw_orders WHERE order_confirm_ind != 1 and created_by = $user_id or created_by = ".$massDel['user'];
		// 	$wpdb->query($sql);
		// }

		// if(isset($massDel['brand']) && $massDel['brand'])
		// {
		// 	$sql = "DELETE FROM `rw_orders` orders 
		// 		LEFT OUTER JOIN
		// 			rw_products AS product ON orders.product_id = product.product_id
  //               LEFT OUTER JOIN
  //               	rw_brands AS brand ON brand.brand_id = product.brand_id
  //               OR brand.brand_parent_id = product.brand_id
		// 		WHERE orders.order_confirm_ind != 1 and orders.created_by = $user_id and brand.brand_id = ".$massDel['brand'];
		// 	$wpdb->query($sql);
		// }

		// if(isset($massDel['subbrand']) && $massDel['subbrand'])
		// {
		// 	$sql = "DELETE FROM `rw_orders` orders 
		// 		LEFT OUTER JOIN
		// 			rw_products AS product ON orders.product_id = product.product_id
  //               WHERE orders.order_confirm_ind != 1 and orders.created_by = $user_id and product.brand_id = ".$massDel['subbraad'];
		// 	$wpdb->query($sql);
		// }

		// if(isset($massDel['category']) && $massDel['category'])
		// {
		// 	$sql = "DELETE FROM `rw_orders` orders 
		// 		LEFT OUTER JOIN
		// 			rw_products AS product ON orders.product_id = product.product_id
  //               WHERE orders.order_confirm_ind != 1 and orders.created_by = $user_id and product.category_id = ".$massDel['category'];
		// 	$wpdb->query($sql);
		// }

		if(isset($massDel['grade']) && $massDel['grade'])
		{
			$sql = "DELETE FROM rw_orders WHERE order_confirm_ind != 1 and created_by = $user_id and product_grade = '".$massDel['grade']."'";
			$wpdb->query($sql);
		}

		echo "Orders deleted Sucessfuly";
		die;
	}


add_action('wp_ajax_delete_order','deleteOrder');
add_action('wp_ajax_nopriv_delete_order','deleteOrder');

/**
 * deleteOrder function
 * this funciton is to delete a particluar order from the inline orders (one by one) from the order review page
 * the function can be called by ajax method using action command 'delete_order'
 * @return void
 * @author Ankit Balyan - sf.ankit@gmail.com
 **/
	function deleteOrder()
	{
		extract($_POST);
		global $wpdb;
		if(isset($order_id) && $order_id)
		{
			$table = 'rw_orders';

			$where = array('order_id' => $order_id);
			$where_format = array('%d');
			$wpdb->delete( $table, $where, $where_format = null );
			echo "Order deleted Sucessfuly";
		}
		die;
	}
add_action('wp_ajax_search_order','searchOrder');

/**
 * searchOrder function
 * this funciton is to search in between the placed order.
 * the function can be called by ajax method using action command 'search_order'
 * @todo implementation of order search
 * @return void
 * @author Ankit Balyan - sf.ankit@gmail.com
 **/
	function searchOrder()
	{
		global $wpdb;
		extract($_POST);
		$sp = isset($sp) ? trim($sp) : '';
		$table = "rw_orders";
	    $sql = "SELECT 
				    order.*, images.image_url
				FROM
				    rw_orders AS order
				        LEFT OUTER JOIN
				    rw_images AS images ON order.order_id = images.order_id
				        AND images.thumnail_ind = 1
				WHERE
				    order.order_name LIKE '%$ps%'
				    OR order.order_desc LIKE '%$ps%'
				    OR order.order_code LIKE '%$ps%'
				    OR order.order_size LIKE '%$ps%'
				    OR order.order_color LIKE '%$ps%'";
	    $results = $wpdb->get_results($sql);
	   	wp_send_json( $results);
	}

/**
 * getOrders function
 * this funciton is to get the order's details o with a particluar id or  get all the orders of a that current logged in user.
 * it will also gives the order's detail of all the dealers if the current user is a distributor.
 * 
 * @param int id to get the details of the particular orde only.
 * @return void
 * @author Ankit Balyan - sf.ankit@gmail.com
 **/	
function getOrders($id = NULL)
{
	global $wpdb;
    $table = "rw_orders";
    if($id)
    {
        $sql = "SELECT * FROM $table where order_id = $id and created_by = ".isLogin();
    }
    else{
        $sql = "SELECT * FROM $table where created_by = ".isLogin();
        if(isDistributor(isLogin()))
		{
			$dealers = getDealersByDistributor(isLogin());
			foreach ($dealers as $dealer) {
				$sql .= " OR created_by =".$dealer->user_id;
			}
		}
    }
    $sql .= " order by product_grade ";
    return $wpdb->get_results($sql);
}


/**
 * getOrderedId function
 * this funciton is to get the all orders's id from along with brand, product and categories id. to match  to a particular brand or category.
 * it will also allows you to get the only confiremed ordered id's as well if you pass the second argument as true
 * 
 * @param id - id of the order, to get the details
 * @param confirmed - true to get the confirmed order's idss
 * @return array
 * @author Ankit Balyan - sf.ankit@gmail.com
 **/
function getOrederedId($id='', $confirmed = FALSE)
{
	$confirmed = ($confirmed) ? " and orders.order_confirm_ind = $confirmed " : ' ';
	global $wpdb;
    $table = "rw_orders";
    if($id)
    {
        $sql = "SELECT order.order_id, order.product_id product.brand_id product.category_id 
        		FROM $table 
				LEFT INNER JOIN 'rw_products' as product ON order.product_id = product.product_id
        		where order.order_id = $id";
    }
    else{
        $sql = " SELECT 
				    orders.order_id,
				    orders.product_id,
				    product.brand_id,
				    product.category_id,
				    brand.brand_parent_id
				FROM
				    rw_orders AS orders
				        LEFT OUTER JOIN
				    rw_products AS product ON orders.product_id = product.product_id
				    	LEFT OUTER JOIN rw_brands as brand ON product.brand_id = brand.brand_id 
				    	where  orders.created_by = ".isLogin() . " $confirmed ";
    }
    $results = $wpdb->get_results($sql);
    $array = array();
    foreach ($results as $key => $value) {
    	$array['order_id'][] = $value->order_id;
    	$array['brand_id'][] = $value->brand_id;
    	$array['brand_id'][] = $value->brand_parent_id;
    	$array['product_id'][] = $value->product_id;
    	$array['category_id'][] = $value->category_id;
    }
    if (isset($array['brand_id'])) {
    	$array['brand_id'] = array_unique($array['brand_id']);
    }
    
    return $array;
}

/**
 * getOrderedIdByUser function
 * this funciton is to get orders id, along with brand, category and sub brand  by providing the user id or  conrirm true to get their confirmed orders
 * 
 * @param  int id -  it is the user id of which order's ids will be generated.
 * @return array
 * @author Ankit Balyan - sf.ankit@gmail.com
 **/
function getOrderedIdByUser($id= NULL,$confirmed = FALSE)
{
	global $wpdb;
	$confirmed = ($confirmed ) ? " and orders.order_confirm_ind = $confirmed " : ' ';
    $table = "rw_orders";
    if($id)
    {
        $sql = "SELECT 
				    orders.order_id,
				    orders.product_id,
				    product.brand_id,
				    product.category_id,
				    brand.brand_parent_id
				FROM
				    rw_orders AS orders
				        LEFT OUTER JOIN
				    rw_products AS product ON orders.product_id = product.product_id
				    	LEFT OUTER JOIN rw_brands as brand ON product.brand_id = brand.brand_id where   orders.created_by = $id $confirmed";
    }
    $results = $wpdb->get_results($sql);
    $array = array();
    foreach ($results as $key => $value) {
    	$array['order_id'][] = $value->order_id;
    	$array['brand_id'][] = $value->brand_id;
    	$array['brand_id'][] = $value->brand_parent_id;
    	$array['product_id'][] = $value->product_id;
    	$array['category_id'][] = $value->category_id;
    }
    if (isset($array['brand_id'])) {
    	$array['brand_id'] = array_unique($array['brand_id']);
    }

    return $array;
}


/**
 * getOrderedIDBrand function
 * this funciton is to get orders id, along with brand, category and sub brand  by providing the brand Id, along with user id or conrirm true to get a user's orders or only confirmed orders.
 * if not provided it will gives you a list of all orders of that brand or sub brand
 * @return array
 * @author Ankit Balyan - sf.ankit@gmail.com
 **/
function getOrederedIdByBrand($id= NULL, $user_id = NULL, $confirmed = FALSE)
{
	global $wpdb;
	$confirmed = ($confirmed ) ? " and orders.order_confirm_ind = $confirmed " : ' ';
    $table = "rw_orders";

    if($id && $user_id)
    {
    	$sql = "SELECT 
				    orders.order_id,
				    orders.product_id,
				    product.brand_id,
				    product.category_id,
				    brand.brand_parent_id
				FROM
				    rw_orders AS orders
				        LEFT OUTER JOIN
				    rw_products AS product ON orders.product_id = product.product_id
				    	LEFT OUTER JOIN rw_brands as brand ON product.brand_id = brand.brand_id 
				    	where product.brand_id = $id 
				    	and orders.created_by = $user_id $confirmed";
    }
    elseif($id)
    {
        $sql = "SELECT 
				    orders.order_id,
				    orders.product_id,
				    product.brand_id,
				    product.category_id,
				    brand.brand_parent_id
				FROM
				    rw_orders AS orders
				        LEFT OUTER JOIN
				    rw_products AS product ON orders.product_id = product.product_id
				    	LEFT OUTER JOIN rw_brands as brand ON product.brand_id = brand.brand_id where product.brand_id = $id $confirmed";
    }

    else{
    	$sql = "SELECT 
				    orders.order_id,
				    orders.product_id,
				    product.brand_id,
				    product.category_id,
				    brand.brand_parent_id
				FROM
				    rw_orders AS orders
				        LEFT OUTER JOIN
				    rw_products AS product ON orders.product_id = product.product_id
				    	LEFT OUTER JOIN rw_brands as brand ON product.brand_id = brand.brand_id where 1 $confirmed";
    }
    $results = $wpdb->get_results($sql);
    $array = array();
    foreach ($results as $key => $value) {
    	$array['order_id'][] = $value->order_id;
    	$array['brand_id'][] = $value->brand_id;
    	$array['brand_id'][] = $value->brand_parent_id;
    	$array['product_id'][] = $value->product_id;
    	$array['category_id'][] = $value->category_id;
    }
    if (isset($array['brand_id'])) {
    	$array['brand_id'] = array_unique($array['brand_id']);

    }
    if (isset($array['product_id'])) {
    	$array['product_id'] = array_unique($array['product_id']);
    }

    return $array;
}

/**
 * isAllOrdersConfrimed function
 * this funciton is to check whether all the order against a particular user are confirmed or not, 
 * return all confirmed orders against that user. or else FALSE
 * if not provided it will gives you a list of all orders of that brand or sub brand
 * @param int id - user id of whcih recordds need to be checked,
 * @return stdObject/ FALSE
 * @author Ankit Balyan - sf.ankit@gmail.com
 **/
function isAllOrdersConfrimed($id= NULL)
{
	global $wpdb;
    $table = "rw_orders";
    if($id)
    {
        $sql = "SELECT 
				    orders.order_id 
				FROM
				    rw_orders AS orders
				        where  orders.created_by = $id and orders.order_confirm_ind = 0";
    }
    $results = $wpdb->get_results($sql);
    if(count($results))
    	return $results;
	else
		return FALSE;
}
/**
 * getBrandIds function
 * this funciton is to get unique array of brand id's where the order's are being placed, also faciliates with user id and confirmeded orders.
 * @return stdObject
 * @author Ankit Balyan - sf.ankit@gmail.com
 **/
function getBrandIds($user_id= NULL,$confirmed = FALSE)
{
	global $wpdb;
	$confirmed = ($confirmed) ? " and orders.order_confirm_ind = $confirmed " : ' ';
    $table = "rw_orders";
    $array = array();
    if(isArvindUser())
    {
    	$sql = "SELECT 
				    DISTINCT (product.brand_id), brand.brand_parent_id
				FROM
				    rw_orders AS orders
				        LEFT OUTER JOIN
				    rw_products AS product ON orders.product_id = product.product_id
                LEFT OUTER JOIN
                	rw_brands AS brand ON brand.brand_id = product.brand_id
                OR brand.brand_parent_id = product.brand_id
	            where 1 $confirmed";
    }
    elseif(isBrandUser() && $user_id)
    {
    	$sql = "SELECT 
				    product.brand_id,
				    brand.brand_parent_id
				FROM
				    rw_orders AS orders
				        LEFT OUTER JOIN
				    rw_products AS product ON orders.product_id = product.product_id
				    	LEFT OUTER JOIN
                	rw_brand_user_map AS user_brand ON user_brand.user_id = $user_id
                LEFT OUTER JOIN
                	rw_brands AS brand ON brand.brand_id = user_brand.brand_id
                OR brand.brand_parent_id = user_brand.brand_id
	            where
	                product.brand_id = brand.brand_id
	            or
	                product.brand_id = brand.brand_parent_id $confirmed";
    }
    elseif($user_id)
    {
        $sql = "SELECT 
				    product.brand_id,
				    brand.brand_parent_id
				FROM
				    rw_orders AS orders
				        LEFT OUTER JOIN
				    rw_products AS product ON orders.product_id = product.product_id
				    	LEFT OUTER JOIN rw_brands as brand ON product.brand_id = brand.brand_id where   orders.created_by = $user_id $confirmed";
    }
    else{
    	$sql = "SELECT 
				    product.brand_id,
				    brand.brand_parent_id
				FROM
				    rw_orders AS orders
				        LEFT OUTER JOIN
				    rw_products AS product ON orders.product_id = product.product_id
				    	LEFT OUTER JOIN
                	rw_brand_user_map AS user_brand ON user_brand.user_id = ".isLogin()." 
                LEFT OUTER JOIN
                	rw_brands AS brand ON brand.brand_id = user_brand.brand_id
                OR brand.brand_parent_id = user_brand.brand_id
	            where
	                product.brand_id = brand.brand_id
	            or
	                product.brand_id = brand.brand_parent_id $confirmed";
    }
    $results = $wpdb->get_results($sql);
		foreach ($results as $key => $value) {
    		$array[] = $value->brand_parent_id;
    	}
    if (count($array)) {
    	$array = array_unique($array);
    }

    return $array;
}
/**
 * getOrderByCategory function
 * this funciton is to get all the orders against a particular category, along with by providing user id and brand id
 * @param int category_id to get the orders from this particular category id, (required)
 * @param int user_id to get the category results against this user (optional)
 * @param int brand_id to get the category results against this brand (optional)
 * @return stdObject
 * @author Ankit Balyan - sf.ankit@gmail.com
 **/
function getOrderByCategory($category_id = NULL, $user_id = NULL, $brand_id = NULL)
{
	global $wpdb;
	if($category_id && !$user_id)
	{
        $sql = "SELECT 
				  orders.*, products.*, ratio.*
				FROM
				    rw_orders AS orders
				        LEFT OUTER JOIN
				    rw_products AS products ON orders.product_id = products.product_id
				    	LEFT OUTER JOIN
					user_category_size_map AS ratio
						ON product.brand_id = ratio.brand_id and product.category_id = ratio.category_id and ratio.user_id = ".isLogin()."
				    where products.category_id = $category_id 
				    and orders.created_by = ".isLogin();
				//GROUP BY orders.order_id";
		// if(isDistributor(isLogin()))
		// {
		// 	$dealers = getDealersByDistributor(isLogin());
		// 	foreach ($dealers as $dealer) {
		// 		$sql .= " OR orders.created_by =".$dealer->user_id;
		// 	}
		// }
    }
    if($category_id && $user_id)
	{
        $sql = "SELECT 
				   orders.*, products.*
				FROM
				    rw_orders AS orders
				        LEFT OUTER JOIN
				    rw_products AS products ON orders.product_id = products.product_id
				    where products.category_id = $category_id 
				    and orders.created_by = ".$user_id;
				//GROUP BY orders.order_id";
		// if(isDistributor(isLogin()))
		// {
		// 	$dealers = getDealersByDistributor(isLogin());
		// 	foreach ($dealers as $dealer) {
		// 		$sql .= " OR orders.created_by =".$dealer->user_id;
		// 	}
		// }
    }
     if($category_id && $user_id && $brand_id)
	{
        $sql = "SELECT 
				   orders.*, products.*
				FROM
				    rw_orders AS orders
				        LEFT OUTER JOIN
				    rw_products AS products ON orders.product_id = products.product_id
				    where products.category_id = $category_id 
				    and products.brand_id = $brand_id
				    and orders.created_by = $user_id";
				//GROUP BY orders.order_id";
		// if(isDistributor(isLogin()))
		// {
		// 	$dealers = getDealersByDistributor(isLogin());
		// 	foreach ($dealers as $dealer) {
		// 		$sql .= " OR orders.created_by =".$dealer->user_id;
		// 	}
		// }
    }
    $sql .= " order by product_grade ";
    return $wpdb->get_results($sql);
}

/**
 * getConfirmOrderByCategory function
 * this funciton is to get all confirmed orders for a category only against a particular, user or brand. 
 * @param int category_id results for this particular caregory (required)
 * @param int user_id filter if the user id provided.
 * @param int brand_id filter if the brand id provided.
 * @return stdObject
 * @author Ankit Balyan - sf.ankit@gmail.com
 **/
function getConfirmOrderByCategory($category_id = NULL, $user_id = NULL, $brand_id = NULL)
{
	global $wpdb;
	if(isBrandUser() || isArvindUser())
	{
		if($category_id && $brand_id)
		{
		 $sql = "SELECT 
				   orders.*, products.*
				FROM
				    rw_orders AS orders
				        LEFT OUTER JOIN
				    rw_products AS products ON orders.product_id = products.product_id
				    where orders.order_confirm_ind = 1
				    and products.category_id = $category_id 
				    and products.brand_id = $brand_id";
		}
		else
		{
			$sql = "SELECT 
				   orders.*, products.*
				FROM
				    rw_orders AS orders
				        LEFT OUTER JOIN
				    rw_products AS products ON orders.product_id = products.product_id
				    where orders.order_confirm_ind = 1";
		}
	}

	elseif($category_id && !$user_id)
	{
        $sql = "SELECT 
				  orders.*, products.*
				FROM
				    rw_orders AS orders
				        LEFT OUTER JOIN
				    rw_products AS products ON orders.product_id = products.product_id
				    where orders.order_confirm_ind = 1
				    and products.category_id = $category_id 
				    and orders.created_by = ".isLogin();
				//GROUP BY orders.order_id";
		// if(isDistributor(isLogin()))
		// {
		// 	$dealers = getDealersByDistributor(isLogin());
		// 	foreach ($dealers as $dealer) {
		// 		$sql .= " OR orders.created_by =".$dealer->user_id;
		// 	}
		// }
    }
    elseif($category_id && $user_id && !$brand_id)
	{
        $sql = "SELECT 
				   orders.*, products.*
				FROM
				    rw_orders AS orders
				        LEFT OUTER JOIN
				    rw_products AS products ON orders.product_id = products.product_id
				    where orders.order_confirm_ind = 1
				    and products.category_id = $category_id 
				    and orders.created_by = ".$user_id;
				//GROUP BY orders.order_id";
		// if(isDistributor(isLogin()))
		// {
		// 	$dealers = getDealersByDistributor(isLogin());
		// 	foreach ($dealers as $dealer) {
		// 		$sql .= " OR orders.created_by =".$dealer->user_id;
		// 	}
		// }
    }
     elseif($category_id && $user_id && $brand_id)
	{
        $sql = "SELECT 
				   orders.*, products.*
				FROM
				    rw_orders AS orders
				        LEFT OUTER JOIN
				    rw_products AS products ON orders.product_id = products.product_id
				    where orders.order_confirm_ind = 1
				    and products.category_id = $category_id 
				    and products.brand_id = $brand_id
				    and orders.created_by = $user_id";
				//GROUP BY orders.order_id";
		// if(isDistributor(isLogin()))
		// {
		// 	$dealers = getDealersByDistributor(isLogin());
		// 	foreach ($dealers as $dealer) {
		// 		$sql .= " OR orders.created_by =".$dealer->user_id;
		// 	}
		// }
    }
    $wpdb->get_results($sql);
    return $wpdb->get_results($sql);
}

/**
 * getConfirmOrderByProduct function
 * this funciton is to get confirmed orders by providing the product Id, along with brand, sub brand, category, and user id.
 * the funciton is being for brand users or arvind ID users.
 * 
 * @param int product_id orders against a particular product
 * @param int category_id provide filter by a caregory in the results
 * @param int brand_id provide filter by a rband oor subbrand in the results
 * @param int user_id provide filter by a user in the results
 * @return stdObject
 * @author Ankit Balyan - sf.ankit@gmail.com
 **/
function getConfirmOrderByProduct($product_id = NULL, $category_id = NULL, $brand_id = NULL, $user_id = NULL)
{
	global $wpdb;
	if(isBrandUser() || isArvindUser())
	{
		if($product_id && $category_id && $brand_id)
		{
			$sql = "SELECT 
				   orders.*, products.*
				FROM
				    rw_orders AS orders
				        LEFT OUTER JOIN
				    rw_products AS products ON orders.product_id = products.product_id
				    where orders.order_confirm_ind = 1
				    and products.category_id = $category_id 
				    and products.brand_id = $brand_id
				    and products.product_id = $product_id";
			if($user_id)
			{
				$sql .= " and orders.created_by = ".$user_id;
			}

		}

		else
		{
			$sql = "SELECT 
				   orders.*, products.*
				FROM
				    rw_orders AS orders
				        LEFT OUTER JOIN
				    rw_products AS products ON orders.product_id = products.product_id
				    where orders.order_confirm_ind = 1";
		}
	}

    $wpdb->get_results($sql);
    return $wpdb->get_results($sql);
}

/**
 * brandLevelOrders function
 * this funciton is to get order's all details on a brand level distribution, the function is being used while generating the excel repports.
 * the function is also sessible to the dealers, distributors, brand and arvind IT users
 * @param int id - it is brand's id for which detials are required to be generated
 * @param bool confirmed, TRUE or FALSE if required confrimed orders or not, respectively
 * @return array
 * @author Ankit Balyan - sf.ankit@gmail.com
 **/
function brandLevelOrders($id= NULL, $confirmed = FALSE, $user_id = NULL)
{
	global $wpdb;
	$confirmed = ($confirmed) ? " and orders.order_confirm_ind = $confirmed " : ' ';
	if(isDealer())
	{
		$user_id = isLogin();
	}
	if(isArvindUser())
	{
		$sql = " SELECT 
				    orders.order_id,users_lvl.lvl_name, users.user_name, brand.brand_name, category.category_name, product.product_code, 
					product.product_desc, product.product_color, product.product_price, orders.product_grade,
				    orders.order_size1 as 'EXS/26',orders.order_size2 as 'XS/28',orders.order_size3 as 'S/30',
				    orders.order_size4 as 'M/32',orders.order_size5 as 'L/34',orders.order_size6 as 'XL/36',
				    orders.order_size7 as 'XXL/38',orders.order_size8 as '3XL/40',orders.total_qty, orders.total_price
				FROM
				    rw_orders AS orders
				        LEFT OUTER JOIN
				    rw_products AS product ON orders.product_id = product.product_id
				    	LEFT OUTER JOIN
				    rw_brands as brand ON product.brand_id = brand.brand_id 
				    	LEFT OUTER JOIN
				    rw_category as category ON product.category_id = category.category_id 
				    	LEFT OUTER JOIN
				    rw_users as users ON users.user_id = orders.created_by 
				    	LEFT OUTER JOIN
				    rw_user_lvl as users_lvl ON users.user_lvl = users_lvl.lvl_id 
				    	where 1 $confirmed ";
	}
	elseif(isBrandUser() && $id)
	{
		$sql = " SELECT 
				    orders.order_id,users_lvl.lvl_name, users.user_name, brand.brand_name, category.category_name, product.product_code, 
					product.product_desc, product.product_color, product.product_price, orders.product_grade,
				    orders.order_size1 as 'EXS/26',orders.order_size2 as 'XS/28',orders.order_size3 as 'S/30',
				    orders.order_size4 as 'M/32',orders.order_size5 as 'L/34',orders.order_size6 as 'XL/36',
				    orders.order_size7 as 'XXL/38',orders.order_size8 as '3XL/40',orders.total_qty, orders.total_price
				FROM
				    rw_orders AS orders
				        LEFT OUTER JOIN
				    rw_products AS product ON orders.product_id = product.product_id
				    	LEFT OUTER JOIN
				    rw_brands as brand ON product.brand_id = brand.brand_id 
				    	LEFT OUTER JOIN
				    rw_category as category ON product.category_id = category.category_id 
				    	LEFT OUTER JOIN
				    rw_users as users ON users.user_id = orders.created_by 
				    	LEFT OUTER JOIN
				    rw_user_lvl as users_lvl ON users.user_lvl = users_lvl.lvl_id 
				    	where brand.brand_parent_id = $id $confirmed ";
	}
	elseif(isDistributor())
	{
		$sql = " SELECT 
				    orders.order_id, users_lvl.lvl_name, users.user_name, brand.brand_name, category.category_name, product.product_code, 
					product.product_desc, product.product_color, product.product_price, orders.product_grade,
				    orders.order_size1 as 'EXS/26',orders.order_size2 as 'XS/28',orders.order_size3 as 'S/30',
				    orders.order_size4 as 'M/32',orders.order_size5 as 'L/34',orders.order_size6 as 'XL/36',
				    orders.order_size7 as 'XXL/38',orders.order_size8 as '3XL/40',orders.total_qty, orders.total_price
				FROM
				    rw_orders AS orders
				        LEFT OUTER JOIN
				    rw_products AS product ON orders.product_id = product.product_id
				    	LEFT OUTER JOIN
				    rw_brands as brand ON product.brand_id = brand.brand_id 
				    	LEFT OUTER JOIN
				    rw_category as category ON product.category_id = category.category_id 
				    LEFT OUTER JOIN
				    rw_users as users ON users.user_id = orders.created_by
				    LEFT OUTER JOIN
				    rw_user_lvl as users_lvl ON users.user_lvl = users_lvl.lvl_id 
				    	where brand.brand_parent_id = $id $confirmed ";
	}
	elseif($id)
	{
		$sql = " SELECT 
				    orders.order_id, brand.brand_name, category.category_name, product.product_code, 
					product.product_desc, product.product_color, product.product_price, orders.product_grade,
				    orders.order_size1 as 'EXS/26',orders.order_size2 as 'XS/28',orders.order_size3 as 'S/30',
				    orders.order_size4 as 'M/32',orders.order_size5 as 'L/34',orders.order_size6 as 'XL/36',
				    orders.order_size7 as 'XXL/38',orders.order_size8 as '3XL/40',orders.total_qty, orders.total_price
				FROM
				    rw_orders AS orders
				        LEFT OUTER JOIN
				    rw_products AS product ON orders.product_id = product.product_id
				    	LEFT OUTER JOIN
				    rw_brands as brand ON product.brand_id = brand.brand_id 
				    	LEFT OUTER JOIN
				    rw_category as category ON product.category_id = category.category_id 
				    	where brand.brand_parent_id = $id $confirmed ";
	}
	
	if($user_id)
			{
				$sql .= " and orders.created_by = ".$user_id;
			}
	$results =  $wpdb->get_results($sql);
	$data = array();
	foreach ($results as $key => $value) {
		$data[$key] = (array) $value;
	}
	return $data;
}

/**
 * matchOrderBrandCategoryUser function 
 * this funciton is to confirm match of a particular user to the specific brand and category, retruns true on sucess else false
 * @param int brand_id (required)
 * @param int category_id (required)
 * @param int user_id (required)
 * @return BOOL
 * @author Ankit Balyan - sf.ankit@gmail.com
 **/
function matchOrderBrandCategoryUser($brand_id= NULL, $category_id = null, $user_id= NULL)
{
	global $wpdb;
	if($brand_id && $category_id && $user_id)
	{
		$sql = "SELECT * FROM rw_orders as orders
		 			LEFT OUTER JOIN
				    	rw_products AS products ON orders.product_id = products.product_id
				    	where products.brand_id = $brand_id 
				    	and products.category_id = $category_id 
				    	and orders.created_by = $user_id";
	}
	else
	{
		$sql = "SELECT * FROM rw_orders as orders
		 			LEFT OUTER JOIN
				    	rw_products AS products ON orders.product_id = products.product_id
				    	where products.brand_id = $brand_id 
				    	and products.category_id = $category_id";	
	}
	$results = $wpdb->get_row($sql);
	if($results)
	{
		return TRUE;
	}
	else{
		return FALSE;
	}
}

/**
 * matchProductToBrandCategory function 
 * this funciton is to confirm match of a particular product to the specific brand and category, retruns true on sucess else false
 * @param int product_id (required)
 * @param int brand_id (required)
 * @param int category_id (required)
 * @return BOOL
 * @author Ankit Balyan - sf.ankit@gmail.com
 **/
function matchProductToBrandCategory($product_id = NULL, $brand_id= NULL, $category_id = null)
{
	global $wpdb;
	if($product_id && $brand_id && $category_id)
	{
		$sql = "SELECT * FROM rw_orders as orders
		 			LEFT OUTER JOIN
				    	rw_products AS products ON orders.product_id = products.product_id
				    	where products.brand_id = $brand_id 
				    	and products.category_id = $category_id 
				    	and products.product_id = $product_id";
		
		$results = $wpdb->get_row($sql);
		if($results)
		{
			return TRUE;
		}
	}
	else{
		return FALSE;
	}
	
}

/**
 * getOrderCategorySum function
 * this funciton is to get total sum or the total quantities of all the orders differenciating by categories, brand category and user and exclude sum for brand and arvind IT users.
 * @param int category id get sums with the category id
 * @param  sting qty, get the toal quantities of orders by providing qty = 'qty' string.
 * @param int user_id, user id to belong the ordere's sum to that user.
 * @param bool confirmed, TRUE or FALSE order is confrimed or not.
 * @param int brand id get sums with the brand id
 * @return int
 * @author Ankit Balyan - sf.ankit@gmail.com
 **/
function getOrderCategorySum($category_id = NULL, $qty = NULL, $user_id = NULL, $confirmed = FALSE,$brand_id =NULL)
{
	global $wpdb;
	$confirmed = ($confirmed) ? " and orders.order_confirm_ind = $confirmed " : ' ';

	if(isBrandUser() || isArvindUser())
	{
		if($category_id && $qty != 'qty' && $brand_id)
		{
	        $sql = "SELECT 
					   SUM(orders.total_price) AS category_total_price
					FROM
					    rw_orders AS orders
					        LEFT OUTER JOIN
					    rw_products AS products ON orders.product_id = products.product_id
					    where products.category_id = $category_id 
					    and products.brand_id = $brand_id $confirmed";
			if($user_id)
			{
				$sql .= " and orders.created_by = ".$user_id;
			}

	    }
	    elseif($category_id && $qty == 'qty')
		{
	        $sql = "SELECT 
					   SUM(orders.total_qty) AS category_total_qty
					FROM
					    rw_orders AS orders
					        LEFT OUTER JOIN
					    rw_products AS products ON orders.product_id = products.product_id
					    where  products.category_id = $category_id
					     and products.brand_id = $brand_id $confirmed";
			if($user_id)
			{
				$sql .= " and orders.created_by = ".$user_id;
			}
	    }		
	}
	elseif($category_id && $qty != 'qty' && $brand_id)
	{
        $sql = "SELECT 
				   SUM(orders.total_price) AS category_total_price
				FROM
				    rw_orders AS orders
				        LEFT OUTER JOIN
				    rw_products AS products ON orders.product_id = products.product_id
				    where products.category_id = $category_id 
				    and products.brand_id = $brand_id $confirmed";
		if($user_id)
		{
			$sql .= " and orders.created_by = ".$user_id;
		}
		else
		{
			$sql .= " and orders.created_by = ".isLogin();
		}

    }
    elseif($category_id && $qty == 'qty' && $brand_id)
	{
        $sql = "SELECT 
				   SUM(orders.total_qty) AS category_total_qty
				FROM
				    rw_orders AS orders
				        LEFT OUTER JOIN
				    rw_products AS products ON orders.product_id = products.product_id
				    where  products.category_id = $category_id 
				    and products.brand_id = $brand_id $confirmed";

		if($user_id)
		{
			$sql .= " and orders.created_by = ".$user_id;
		}
		else
		{
			$sql .= " and orders.created_by = ".isLogin();
		}

    }
    elseif($category_id){
    	$sql = "SELECT 
				   SUM(orders.total_price) AS category_total_qty
				FROM
				    rw_orders AS orders
				        LEFT OUTER JOIN
				    rw_products AS products ON orders.product_id = products.product_id
				    where  products.category_id = $category_id 
				    $confirmed";

		if($user_id)
		{
			$sql .= " and orders.created_by = ".$user_id;
		}
		else
		{
			$sql .= " and orders.created_by = ".isLogin();
		}
    }
    $results = $wpdb->get_var($sql);
    return $results;
}


/**
 * getOrderUsersSum function
 * this funciton is to get total sum or the total quantities of all the orders differenciating by products, brand, category or user and excludes brand and arvind IT users
 * @param int product_id get sum belongs to that product group, for different users
 * @param int brand id get sums with the brand id
 * @param int category id get sums with the category id
 * @param  sting qty, get the toal quantities of orders by providing qty = 'qty' string.
 * @param int user_id, user id to belong the ordere's sum to that user.
 * @param bool confirmed, TRUE or FALSE order is confrimed or not.
 * @return int
 * @author Ankit Balyan - sf.ankit@gmail.com
 **/
function getOrderProductSum($product_id = NULL, $brand_id = NULL, $category_id = NULL, $qty = NULL, $user_id = NULL, $confirmed = FALSE)
{
	global $wpdb;
	$confirmed = ($confirmed) ? " and orders.order_confirm_ind = $confirmed " : ' ';

	if(isBrandUser() || isArvindUser())
	{
		if($product_id && $brand_id && $category_id && $qty != 'qty')
		{
	        $sql = "SELECT 
					   SUM(orders.total_price) AS products_total_price
					FROM
					    rw_orders AS orders
					        LEFT OUTER JOIN
					    rw_products AS products ON orders.product_id = products.product_id
					    where products.category_id = $category_id 
					    and products.brand_id = $brand_id 
					    and products.product_id = $product_id $confirmed";
			if($user_id)
			{
				$sql .= " and orders.created_by = ".$user_id;
			}
	    }
	    elseif($product_id && $brand_id &&  $category_id && $qty == 'qty')
		{
	        $sql = "SELECT 
					   SUM(orders.total_qty) AS category_total_qty
					FROM
					    rw_orders AS orders
					        LEFT OUTER JOIN
					    rw_products AS products ON orders.product_id = products.product_id
					    where products.category_id = $category_id 
					    and products.brand_id = $brand_id 
					    and products.product_id = $product_id $confirmed";
			if($user_id)
			{
				$sql .= " and orders.created_by = ".$user_id;
			}

	    }		
	}
    $results = $wpdb->get_var($sql);
    return $results;
}

/**
 * getOrderUsersSum function
 * this funciton is to get total sum or the total quantities of all the orders differenciating by users and confirmed orders only.
 * il will also check whethere the user is brand user to provide full order's sum from their dealer or distributors
 * @param int user_id, user id to belong the ordere's sum to that user.
 * @param  sting qty, get the toal quantities of orders by providing qty = 'qty' string.
 * @param bool confirmed, TRUE or FALSE order is confrimed or not.
 * @return int
 * @author Ankit Balyan - sf.ankit@gmail.com
 **/
function getOrderUsersSum($user_id= null, $qty = NULL, $confirmed = FALSE)
{
	$confirmed = ($confirmed) ? " and orders.order_confirm_ind = $confirmed " : ' ';
	global $wpdb;
	if(isBrandUser())
	{
		if(!$qty)
		{
	 		$sql = "SELECT 
					   SUM(orders.total_price) AS products_total_price
					FROM
					    rw_orders AS orders
					        LEFT OUTER JOIN
					    rw_products AS product ON orders.product_id = product.product_id
					    LEFT OUTER JOIN
	                	rw_brand_user_map AS user_brand ON user_brand.user_id = ".isLogin()."
		                LEFT OUTER JOIN
		                	rw_brands AS brand ON brand.brand_id = user_brand.brand_id
		                OR brand.brand_parent_id = user_brand.brand_id
			            where
			                (product.brand_id = brand.brand_id
			            or
			                product.brand_id = brand.brand_parent_id) $confirmed";
			if($user_id)
			{
				$sql .= " and orders.created_by = ".$user_id;
			}
		}
		elseif($qty = 'qty')
		{
			$sql = "SELECT 
					   SUM(orders.total_qty) AS products_total_price
					FROM
					    rw_orders AS orders
					        LEFT OUTER JOIN
					    rw_products AS product ON orders.product_id = product.product_id
					    LEFT OUTER JOIN
		                	rw_brand_user_map AS user_brand ON user_brand.user_id = ".isLogin()."
		                LEFT OUTER JOIN
		                	rw_brands AS brand ON brand.brand_id = user_brand.brand_id
		                OR brand.brand_parent_id = user_brand.brand_id
			            where
			                (product.brand_id = brand.brand_id
			            or
			                product.brand_id = brand.brand_parent_id ) $confirmed";
			if($user_id)
			{
				$sql .= " and orders.created_by = ".$user_id;
			}
		}
		$wpdb->get_var($sql);
	    $wpdb->last_query;
	    return $wpdb->get_var($sql);
	}
	else{

	}
}

/**
 * getOrderBrandsSum function
 * this funciton is to get total sum or the total quantities of all the orders differenciating by users, brand, sub brand, confirmed orders.
 * il will also check whethere the user is brand user or arvind user to provide full order's sum
 * @param int user_id, user id to belong the ordere's sum to that user.
 * @param int brand_id order;s related to a particluar brand,
 * @param bool top, TRUE or false for sub brand or top brand respectivly
 * @param  sting qty, get the toal quantities of orders by providing qty = 'qty' string.
 * @param bool confirmed, TRUE or FALSE order is confrimed or not.
 * @return int
 * @author Ankit Balyan - sf.ankit@gmail.com
 **/
function getOrderBrandsSum($user_id = NULL, $brand_id = NULL, $top = FALSE, $qty = null, $confirmed = FALSE)
{

	//$user_id = ($user_id) ? $user_id : isLogin();
	$confirmed = ($confirmed) ? " and orders.order_confirm_ind = $confirmed " : ' ';
	global $wpdb;
	if(isBrandUser() || isArvindUser())
	{
		if($brand_id && $top && $qty !='qty')
		{
	        $sql = "SELECT 
					   SUM(orders.total_price) AS brand_total_price
					FROM
					    rw_orders AS orders
					        LEFT OUTER JOIN
					    rw_products AS products ON orders.product_id = products.product_id
					    	LEFT OUTER JOIN
					    rw_brands as brand ON products.brand_id = brand.brand_id 
					    		OR products.brand_id = brand.brand_parent_id
					    where brand.brand_parent_id = $brand_id $confirmed";
			if($user_id)
			{
				$sql .= " and orders.created_by = ".$user_id;
			}

	    }
		elseif($brand_id && $qty !='qty')
		{
	        $sql = "SELECT 
					   SUM(orders.total_price) AS brand_total_price
					FROM
					    rw_orders AS orders
					        LEFT OUTER JOIN
					    rw_products AS products ON orders.product_id = products.product_id
					    	LEFT OUTER JOIN
					    rw_brands as brand ON products.brand_id = brand.brand_id 
					    where brand.brand_id = $brand_id $confirmed";
			if($user_id)
			{
				$sql .= " and orders.created_by = ".$user_id;
			}

	    }
	    elseif($top && $qty == 'qty')
	    {
	    	$sql = "SELECT 
					   SUM(orders.total_qty) AS brand_total_qty
					FROM
					    rw_orders AS orders
					        LEFT OUTER JOIN
					    rw_products AS products ON orders.product_id = products.product_id
					    	LEFT OUTER JOIN
					    rw_brands as brand ON products.brand_id = brand.brand_id 
					    		OR products.brand_id = brand.brand_parent_id
					    where brand.brand_parent_id = $brand_id $confirmed";
			if($user_id)
			{
				$sql .= " and orders.created_by = ".$user_id;
			}
	    }
	    elseif($qty == 'qty')
	    {
	    	$sql = "SELECT 
					   SUM(orders.total_qty) AS brand_total_qty
					FROM
					    rw_orders AS orders
					        LEFT OUTER JOIN
					    rw_products AS products ON orders.product_id = products.product_id
					    	LEFT OUTER JOIN
					    rw_brands as brand ON products.brand_id = brand.brand_id 
					    where brand.brand_id = $brand_id $confirmed";
			if($user_id)
			{
				$sql .= " and orders.created_by = ".$user_id;
			}

	    }
	}
	elseif($brand_id && $top && $qty !='qty')
	{
        $sql = "SELECT 
				   SUM(orders.total_price) AS brand_total_price
				FROM
				    rw_orders AS orders
				        LEFT OUTER JOIN
				    rw_products AS products ON orders.product_id = products.product_id
				    	LEFT OUTER JOIN
				    rw_brands as brand ON products.brand_id = brand.brand_id 
				    		OR products.brand_id = brand.brand_parent_id
				    where brand.brand_parent_id = $brand_id and orders.created_by = ".$user_id . " $confirmed ";

    }
	elseif($brand_id && $qty !='qty')
	{
        $sql = "SELECT 
				   SUM(orders.total_price) AS brand_total_price
				FROM
				    rw_orders AS orders
				        LEFT OUTER JOIN
				    rw_products AS products ON orders.product_id = products.product_id
				    	LEFT OUTER JOIN
				    rw_brands as brand ON products.brand_id = brand.brand_id 
				    where brand.brand_id = $brand_id and orders.created_by = ".$user_id." $confirmed";

    }
    elseif($top && $qty == 'qty')
    {
    	$sql = "SELECT 
				   SUM(orders.total_qty) AS brand_total_qty
				FROM
				    rw_orders AS orders
				        LEFT OUTER JOIN
				    rw_products AS products ON orders.product_id = products.product_id
				    	LEFT OUTER JOIN
				    rw_brands as brand ON products.brand_id = brand.brand_id 
				    		OR products.brand_id = brand.brand_parent_id
				    where brand.brand_parent_id = $brand_id and orders.created_by = ".$user_id. " $confirmed";
    }
    elseif($qty == 'qty')
    {
    	$sql = "SELECT 
				   SUM(orders.total_qty) AS brand_total_qty
				FROM
				    rw_orders AS orders
				        LEFT OUTER JOIN
				    rw_products AS products ON orders.product_id = products.product_id
				    	LEFT OUTER JOIN
				    rw_brands as brand ON products.brand_id = brand.brand_id 
				    where brand.brand_id = $brand_id and orders.created_by = ".$user_id." $confirmed";

    }
    $wpdb->get_var($sql);
    $wpdb->last_query;
    return $wpdb->get_var($sql);
}

/**
 * getOrderSum function
 * this funciton is to get total sum or the total quantities of all the orders and also by differenciating by user or confirmed orders.
 * 
 * @todo by providing order id, get the total sums.
 * @param int id, product id not implemented, always null
 * @param  sting qty, get the toal quantities of orders by providing qty = 'qty' string.
 * @param bool confirmed, TRUE or FALSE
 * @return int
 * @author Ankit Balyan - sf.ankit@gmail.com
 **/
function getOrderSum($id= NULL, $qty = NULL, $confirmed = FALSE, $user_id = NULL)
{
	$confirmed = ($confirmed) ? " and orders.order_confirm_ind = $confirmed " : ' ';
	global $wpdb;
	if(isBrandUser())
	{
		$user_id = isLogin();
		if($qty != 'qty')
		{
			$sql = "SELECT SUM(orders.total_price) as dist_total_price 
				FROM 
					rw_orders AS orders 
				LEFT OUTER JOIN 
					rw_products AS product on orders.product_id = product.product_id
				LEFT OUTER JOIN
                	rw_brand_user_map AS user_brand ON user_brand.user_id = $user_id
                LEFT OUTER JOIN
                	rw_brands AS brand ON brand.brand_id = user_brand.brand_id
                OR brand.brand_parent_id = user_brand.brand_id
	            where
	                product.brand_id = brand.brand_id
	            or
	                product.brand_id = brand.brand_parent_id
						$confirmed";
		}
		elseif($qty == 'qty')
			{
				$sql = "SELECT SUM(orders.total_qty) as dist_total_qty 
					FROM 
						rw_orders AS orders 
					LEFT OUTER JOIN 
						rw_products AS product on orders.product_id = product.product_id
				    LEFT OUTER JOIN
                	rw_brand_user_map AS user_brand ON user_brand.user_id = $user_id
	                LEFT OUTER JOIN
	                	rw_brands AS brand ON brand.brand_id = user_brand.brand_id
	                OR brand.brand_parent_id = user_brand.brand_id
		            where
		                product.brand_id = brand.brand_id
		            or
		                product.brand_id = brand.brand_parent_id $confirmed";
			}

		$wpdb->get_var($sql);
	    $wpdb->last_query;
	    return $wpdb->get_var($sql);
	}
	elseif (isArvindUser()) {
		if($qty != 'qty')
		{
			$sql = "SELECT SUM(orders.total_price) as dist_total_price 
				FROM 
					rw_orders AS orders 
			    	where 1 $confirmed";
		}
		elseif($qty == 'qty')
			{
				$sql = "SELECT SUM(orders.total_qty) as dist_total_qty 
					FROM 
						rw_orders AS orders 
				    	where 1 $confirmed";
			}
		if($user_id)
			{
				$sql .= " and orders.created_by = ".$user_id;
			}
		$wpdb->get_var($sql);
	    $wpdb->last_query;
	    return $wpdb->get_var($sql);
	}

	// if(isBrandUser() || isArvindUser())
	// {
	// 	if($qty != 'qty')
	// 	{
	// 		$sql = "SELECT SUM(orders.total_price) as dist_total_price 
	// 			FROM 
	// 				rw_orders AS orders 
	// 		    	where 1 $confirmed";
	// 	}
	// 	elseif($qty == 'qty')
	// 		{
	// 			$sql = "SELECT SUM(orders.total_qty) as dist_total_qty 
	// 				FROM 
	// 					rw_orders AS orders 
	// 			    	where 1 $confirmed";
	// 		}
	// 	if($user_id)
	// 		{
	// 			$sql .= " and orders.created_by = ".$user_id;
	// 		}
	// 	$wpdb->get_var($sql);
	//     $wpdb->last_query;
	//     return $wpdb->get_var($sql);
	// }
	elseif(count(isDistributor($user_id)) && $user_id && $qty != 'qty')
		{
			$sql = "SELECT SUM(orders.total_price) as dist_total_price 
				FROM 
					rw_orders AS orders 
					LEFT OUTER JOIN rw_users as users
			    	on users.user_parent_id = $user_id 
			    	or users.user_id = $user_id
			    	where orders.created_by = users.user_id $confirmed";
		}
	elseif(count(isDistributor($user_id)) && $user_id && $qty == 'qty')
		{
			$sql = "SELECT SUM(orders.total_qty) as dist_total_qty 
				FROM 
					rw_orders AS orders 
					LEFT OUTER JOIN rw_users as users
			    	on users.user_parent_id = $user_id 
			    	or users.user_id = $user_id
			    	where orders.created_by = users.user_id $confirmed";
		}

	elseif($id && $qty != 'qty')
	{
		$sql = "SELECT 
				   SUM(orders.total_price) AS user_total_price
				FROM
				    rw_orders AS orders
				    where orders.created_by = $id $confirmed";
	}
	elseif($id && $qty == 'qty'){
		$sql = "SELECT 
				   SUM(orders.total_qty) AS user_total_qty
				FROM
				    rw_orders AS orders
				    where orders.created_by = $id $confirmed";
	}

	elseif(!$id && $qty =='qty')
	{
		$sql = "SELECT 
				   SUM(orders.total_qty) AS user_total_qty
				FROM
				    rw_orders AS orders
				    where orders.created_by = ".isLogin() . " $confirmed";
	}
	else{
			$sql = "SELECT 
				   SUM(orders.total_price) AS user_total_price
				FROM
				    rw_orders AS orders
				    where orders.created_by = ".isLogin()." $confirmed";
	}
	$wpdb->get_var($sql);
	// /echo $wpdb->last_query;
	return $wpdb->get_var($sql);
}

/**
 * getCategoryIdByProduct function
 * this funciton is to get category id by providing the id of a product
 * 
 * @param id - product id to get its category id
 * @return array
 * @author Ankit Balyan - sf.ankit@gmail.com
 **/
function getCategoryIdByProduct($id = NULL)
{
	global $wpdb;
	if($id)
	{
		$sql = "SELECT category_id from rw_products where product_id = $id";

		return $wpdb->get_var($sql);
	}
}

/**
 * getBrandIdByProduct function
 * this funciton is to get brand id by providing the id of a product
 * @param id - product id to get its brand id
 * @return array
 * @author Ankit Balyan - sf.ankit@gmail.com
 **/
function getBrandIdByProduct($id = NULL)
{
	global $wpdb;
	if($id)
	{
		$sql = "SELECT brand_id from rw_products where product_id = $id";

		return $wpdb->get_var($sql);
	}
}