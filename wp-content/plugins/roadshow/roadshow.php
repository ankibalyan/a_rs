<?php
/*
	Plugin Name: Roadshow
	Description: Add Meta information to roadshow
	Plugin URI: http://www.igotstudy.com/wordpress/plugins/roadshow
	Author: Ankit Balyan
	Author URI: http://facebook.com/beankit
	Version: 1.0
	License: GPL2
	Text Domain: En
	Domain Path: Domain Path
*/

defined('ROADSHOW') or define('ROADSHOW', plugin_dir_path(__FILE__));
defined('PLUGIN_ASSETS') or define('PLUGIN_ASSETS', plugin_dir_url(__FILE__).'assets/');
defined('ASSETS') or define('ASSETS', get_template_directory_uri().'/assets' );

add_action('admin_menu', 'addMenus');

/**
 * addMenus function
 * this funciton is to add the new menu items and pages in admin panel.
 * @return void
 * @author Ankit Balyan - sf.ankit@gmail.com
 **/
function addMenus()
{
	//add_menu_page( $page_title, $menu_title, $capability, $menu_slug, $function, $icon_url, $position );
		//add_submenu_page( $parent_slug, $page_title, $menu_title, $capability, $menu_slug, $function );
	$capability = 'manage_options';

	add_menu_page('Category', 'Category',$capability,'category','displayCategories','',6);
		add_submenu_page( 'category', "Add Category", "Add Category", $capability,'add-category', 'addCategory');
		add_submenu_page( 'category', "Bulk Upload", "Bulk Upload", $capability,'bulk-upload-category', 'uploadCategory');
	
	add_menu_page('Brands', 'Brands',$capability,'brands','displayBrands','',7);
		add_submenu_page( 'brands', "Add Brnads", "Add Brnads", $capability,'add-brand', 'addBrand');
		add_submenu_page( 'brands', "Bulk Upload", "Bulk Upload", $capability,'bulk-upload-brand', 'uploadBrands');

	add_menu_page('RW Users', 'RW Users',$capability,'users','displayUsers','',8);
		add_submenu_page( 'users', "Add User", "Add User", $capability,'add-user', 'addUser');
		add_submenu_page( 'users', "Bulk Upload", "Bulk Upload", $capability,'bulk-upload-user', 'uploadUsers');
	add_menu_page('Products', 'Products',$capability,'products','displayProducts','',9);
		add_submenu_page( 'products', "Bulk Upload", "Bulk Upload", $capability,'bulk-upload-product', 'uploadProducts');
		add_submenu_page( 'products', "Bulk Image Mapping", "Bulk Image Mapping", $capability,'bulk-image-mapping', 'mapImages');
}

add_action('admin_head', 'addScript');
global $status;
$status = array('0' => 'Inactive', '1' => 'Active');

//add_action( $tag, $function_to_add, $priority, $accepted_args );

add_action('wp_enqueue_scripts','addScript');
/**
 * addScript function
 * this funciton is to add the style and scripts files to the header or footer
 * @return void
 * @author Ankit Balyan - sf.ankit@gmail.com
 **/
function addScript()
{
	//wp_enqueue_style( $handle, $src, $deps, $ver, $media );
	//wp_enqueue_script( $handle, $src, $deps, $ver, $in_footer );
	//load bootstrap
	   wp_enqueue_style('bootstrapCss', PLUGIN_ASSETS.'css/bootstrap.min.css', array(), null);
	   wp_enqueue_style('font-awesome', PLUGIN_ASSETS.'css/font-awesome.min.css', array(), null);
	   //wp_enqueue_style('bootstrapSelectCss', PLUGIN_ASSETS.'css/bootstrap-select.min.css', array(), null);
	   ?>
		<link href="//netdna.bootstrapcdn.com/bootstrap/3.0.3/css/bootstrap.min.css" rel="stylesheet">
	   <?php
	   wp_enqueue_style('bootstrapSwitchCss',PLUGIN_ASSETS.'css/bootstrap-switch.min.css', array(), null);
       wp_enqueue_style('data-tables',PLUGIN_ASSETS.'datatables/css/jquery.dataTables.min.css', array(), null);
	
	//load main css
 	   wp_enqueue_style('screen', PLUGIN_ASSETS.'css/screen.css', array(), null);

	// load bootstrap scripts
	   wp_enqueue_script( 'bootstrapJs',  PLUGIN_ASSETS.'js/bootstrap.min.js', array(),'1122014',TRUE);
	   wp_enqueue_script( 'bootstrapSelectJs',  PLUGIN_ASSETS.'js/bootstrap-select.js', array(),'1122014',TRUE);
	   wp_enqueue_script( 'bootstrapSwitchJs', PLUGIN_ASSETS.'js/bootstrap-switch.min.js', array(), '20150123',TRUE);
	   wp_enqueue_script( 'bootstrapDateJs', PLUGIN_ASSETS.'js/bootstrap-datepicker.js', array(),'2122014',TRUE);
	   wp_enqueue_script( 'jquerySliderJs', PLUGIN_ASSETS.'/js/jquery.slider.js', array(),'3122014',TRUE);
	   wp_enqueue_script( 'treeviewJs', PLUGIN_ASSETS.'js/jquery.treeview.js', array(), '20141010',TRUE );
	   wp_enqueue_script( 'validationsJs', PLUGIN_ASSETS.'validate/jquery.validate.min.js', array(), '21012015' ,TRUE);
	   wp_enqueue_script( 'additionalValidationsJs', PLUGIN_ASSETS.'validate/additional-methods.min.js', array(), '20141210',TRUE );
	   wp_enqueue_script( 'dataTablesJs', PLUGIN_ASSETS.'datatables/js/jquery.dataTables.min.js', array(), '21012015',TRUE);
 	   wp_enqueue_script( 'commonJs', PLUGIN_ASSETS.'js/common.js', array(), '20141010',TRUE);

}

/**
 * load_admin_things function
 * this funciton is to add admin things to upload a files dependancies.
 * @return void
 * @author Ankit Balyan - sf.ankit@gmail.com
 **/
function load_admin_things() {
    wp_enqueue_script('media-upload');
    wp_enqueue_script('thickbox');
    wp_enqueue_style('thickbox');
    wp_enqueue_script('jquery-ui-tabs');  // For admin panel page tabs
	wp_enqueue_script('jquery-ui-dialog');  // For admin panel popup alerts
	wp_localize_script( 'commonJs', 'wp_csv_to_db_pass_js_vars', array( 'ajax_image' => plugin_dir_url( __FILE__ ).'images/loading.gif', 'ajaxurl' => admin_url('admin-ajax.php') ) );
}

add_action( 'admin_enqueue_scripts', 'load_admin_things' );

include ROADSHOW.'modules/user.php';
include ROADSHOW.'operations/user.php';

if(file_exists(ROADSHOW.'modules/brand.php'))
	include ROADSHOW.'modules/brand.php';
if(file_exists(ROADSHOW.'operations/brand.php'))
include ROADSHOW.'operations/brand.php';

if(file_exists(ROADSHOW.'modules/category.php'))
	include ROADSHOW.'modules/category.php';
if(file_exists(ROADSHOW.'operations/category.php'))
include ROADSHOW.'operations/category.php';

if(file_exists(ROADSHOW.'modules/product.php'))
	include ROADSHOW.'modules/product.php';
if(file_exists(ROADSHOW.'operations/product.php'))
include ROADSHOW.'operations/product.php';

if(file_exists(ROADSHOW.'operations/order.php'))
include ROADSHOW.'operations/order.php';

if(file_exists(ROADSHOW.'operations/export.php'))
include ROADSHOW.'operations/export.php';


add_action( 'widgets_init','rwWidgets');
function rwWidgets() {
    register_widget( 'rw_Search_Widget' );
    register_widget( 'rw_product_filter_Widget' );
    
}

/**
 * rw_Search_Widget widget Class to create a search widget on sidebars.
 * Wordpress 2.8 and above
 * @see http://codex.wordpress.org/Widgets_API#Developing_Widgets
 * @author Ankit Balyan - sf.ankit@gmail.com
 */
class rw_Search_Widget extends WP_Widget {

    /**
     * Constructor
     *
     * @return void
     **/
    function rw_Search_Widget() {
        $widget_ops = array( 'name' => 'Product Search', 'description' => 'Enable search functionality for catelog and products' );
        $this->WP_Widget( 'ProductSearch', 'Product Search', $widget_ops );
    }

    /**
     * Outputs the HTML for this widget.
     *
     * @param array  An array of standard parameters for widgets in this theme
     * @param array  An array of settings for this widget instance
     * @return void Echoes it's output
     * @author Ankit Balyan - sf.ankit@gmail.com
     **/
    function widget( $args, $instance ) {
        extract( $args, EXTR_SKIP );
        extract( $instance, EXTR_SKIP );
	        $title = apply_filters('widget_title', $title );
	        if(empty($title)) $title = "Search";
	        $before_title = "<h2>";
	        $after_title = "</h2>";
	        echo $before_widget;
	        echo $before_title;
	        echo $title; // Can set this with a widget option, or omit altogether
	        echo $after_title;

		    //
		    // Widget display logic goes here
		    //
	        ?>
        	<div class="search_box">
        	<form action="" method="POST" name="productSearch" id="productSearch">
				<input type="text" name="ps" id="searchProductWidget" placeholder="Search" class="text " />
			</form>
			</div>
			<br>
			<script type="text/javascript">
				jQuery(document).ready(function($) {
					jQuery('#productSearch').validate({
		            submitHandler: function(form) {
		                var formData = new FormData(jQuery('#productSearch')[0]);
		                formData.append("action",'search_product');
		                jQuery.ajax({
		                        type: 'POST',
		                        url: ajaxurl,
		                        data: formData,
		                        cache: false,
		                        contentType: false,
		                        processData: false,
		                        success: function(result) {
			                            var host = window.location.host;
			                            console.log(host);
		                           		jQuery("#product_list").empty();
							        	jQuery.each(result, function(i,product){
							          	content = '<div class="col-sm-4">';
										content += '<div class="product-image-wrapper">';
										content += '<div class="single-products">';
										content += '<a href="/product/'+product.product_id+'">';
										content += '<div class="productinfo text-center">';
										content += '<img src="'+product.image_url+'" alt="" />';
										content += '<h2>MRP '+product.product_price+'</h2>';
										content += '<a href="/product/'+product.product_id+'">'+product.product_name+'<?php //echo $product->product_name ?></a>';
										content += '</div>';
										content += '</a>';
										content += '</div>';
												// <div class="choose">
												// 	<div class="color_box" style="background-color:#0000ff;"></div>
												// 	<div class="color_box" style="background-color:#66FF66;"></div>
												// 	<div class="color_box" style="background-color:#FF0066;"></div>
												// 	<div class="color_box" style="background-color:#00CC99;"></div>
												// 	<div class="color_box" style="background-color:	#944DDB;"></div>
												// </div>
										content += '</div>';
										content += '</div>';
							            // content = '<p>' + product_name + '</p>';
							            // content += '<p>' + product.product_short_description + '</p>';
							            // content += '<img src="' + product.product_thumbnail_src + '"/>';
							            // content += '<br/>';
							            jQuery('#product_list').append(content);
							          });
		                        }
		                    });
		             }
		        	});
					
				});
			</script>
	        <?php
	    	echo $after_widget;
    }

    /**
     * Deals with the settings when they are saved by the admin. Here is
     * where any validation should be dealt with.
     *
     * @param array  An array of new settings as submitted by the admin
     * @param array  An array of the previous settings
     * @return array The validated and (if necessary) amended settings
     * @author Ankit Balyan - sf.ankit@gmail.com
     **/
    // function update( $new_instance, $old_instance ) {

    //     // update logic goes here
    //     $updated_instance = $new_instance;
    //     return $updated_instance;
    // }?

    /**
     * Displays the form for this widget on the Widgets page of the WP Admin area.
     *
     * @param array  An array of the current settings for this widget
     * @return void Echoes it's output
     * @author Ankit Balyan - sf.ankit@gmail.com
     **/
    
    public function form( $instance ) {
		$title = ! empty( $instance['title'] ) ? $instance['title'] : __( 'Category', 'text_domain' );
		?>
		<p>
		<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
		</p>
		<?php 
	}
}



/**** category filter widget ****/
/**
 * rw_product_filter_Widget wiget Class to create a filter widget for brand, categroy and price.
 * Wordpress 2.8 and above
 * @see http://codex.wordpress.org/Widgets_API#Developing_Widgets
 * @author Ankit Balyan - sf.ankit@gmail.com
 */
class rw_product_filter_Widget extends WP_Widget {

    /**
     * Constructor
     *
     * @return void
     **/
    function __construct() {
        $widget_ops = array( 'name' => 'Product Filter', 'description' => 'Enable Category, Brand, Price Filter functionality for catelog and products' );
        parent::__construct( 'productFilter', '', $widget_ops );
    }

    /**
     * Outputs the HTML for this widget.
     *
     * @param array  An array of standard parameters for widgets in this theme
     * @param array  An array of settings for this widget instance
     * @return void Echoes it's output
     * @author Ankit Balyan - sf.ankit@gmail.com
     **/
    function widget( $args, $instance ) {
        extract( $args, EXTR_SKIP );
        extract( $instance, EXTR_SKIP );
	        $category_filter_title = apply_filters('widget_title', $category_filter_title );
	        if(empty($category_filter_title)) $category_filter_title = "Category";

	        $brand_filter_title = apply_filters('widget_title', $brand_filter_title );
	        if(empty($brand_filter_title)) $brand_filter_title = "Brand";

	        $before_title = "<h2>";
	        $after_title = "</h2>";
	        echo $before_widget;
	        

		    //
		    // Widget display logic goes here
		    //
	        ?>
        	<form action="" method="POST" name="productFilterForm" id="productFilterForm">
					<?php $brands = get_nested(); ?>

					<?php if (count($brands)): 
						echo $before_title;
				        echo $brand_filter_title; // Can set this with a widget option, or omit altogether
				        echo $after_title;
				        echo $brandlistView = get_brandFilterList($brands);
					?>

        		<?php $categories = getCategories();
        			if (count($categories)): 
        			  	echo $before_title;
				        echo $category_filter_title; // Can set this with a widget option, or omit altogether
				        echo $after_title;
        			  	?>
		        		<div class="panel-group category-products" id="categoryFilterAccordian"><!--category-productsr-->
						<?php foreach ($categories as $category): ?>
									<div class="panel panel-default">
										<div class="panel-heading">
											<h4 class="panel-title">
												<input 
												type="checkbox" 
												name="selectCategoryBox[]"
												id="category_id_<?php echo $category->category_id; ?>" 
												value="<?php echo $category->category_id; ?>"
												class="large"
												>
												<a data-toggle="collapse" data-parent="#accordian" href="#sportswear" onclick ="submitFilter()"><?php echo $category->category_name ?></a>
											</h4>
										</div>
									</div>
						<?php endforeach ?>
						</div>
					<?php endif; ?>
	
					<?php endif ?>
					<div class="price-range"><!--price-range-->
					<?php
						echo $before_title;
				        echo $price_filter_title; // Can set this with a widget option, or omit altogether
				        echo $after_title;
				        $maxprice = maxProductPrice();
				        $minprice = minProductPrice();
				    ?>
						<div class="well">
							 <input type="text" class="span2" data-slider-min="<?php echo $minprice ?>" data-slider-max="<?php echo $maxprice ?>" data-slider-step="5" data-slider-value="[<?php echo $minprice ?>,<?php echo $maxprice ?>]" id="sl2" ><br />
							 <span><span><i class="fa fa-inr"></i></span></span><b> <?php echo money_format('%!.0i', $minprice); ?></b><b class="pull-right"><span><span><i class="fa fa-inr"></i></span></span> <?php echo money_format('%!.0i', $maxprice); ?></b>
						</div>
					</div><!--/price-range-->
			</form>
			<script type="text/javascript">
				jQuery(document).on("click",'input[type="checkbox"]',function() {
					type = jQuery(this).attr('name');
				    if(type != 'selectCategoryBox[]')
				    {
				    	brandFilter();
				    }
				    else
				    {
				    	categoryFilter();
				    }
				});

function brandFilter (argument) {
			var formData = new FormData(jQuery('#productFilterForm')[0]);
            data_slider_value = jQuery('.tooltip-inner').text();

            formData.append("action",'product_filter');
            formData.append("data_slider_value",data_slider_value);
            jQuery.ajax({
                    type: 'POST',
                    url: ajaxurl,
                    data: formData,
                    cache: false,
                    contentType: false,
                    processData: false,
                     beforeSend:function (argument) {
                       // jQuery('.overlay').fadeIn('slow'); 
                       // jQuery('.response').fadeIn('slow').html("Processing...");
                    },
                    success: function(result) {
                       //jQuery('.response').fadeIn('slow').html(result).delay(5000).fadeOut('slow');
                       //jQuery('.overlay').delay(5100).fadeOut('slow');
                       		content = '';
				        	jQuery.each(result.catelog, function(i,product){
					          	content += '<div class="col-sm-4 eachProduct jscroll-next">';
								content += '<div class="product-image-wrapper">';
								content += '<div class="single-products">';
								content += '<a href="/product/'+product.product_id+'">';
								content += '<div class="productinfo text-center">';
								content += '<img src="'+product.image_url+'" alt="" />';
								content += '<h2>MRP '+product.product_price+'</h2>';
								content += '<a href="/product/'+product.product_id+'">'+product.product_name+'<?php //echo $product->product_name ?></a>';
								content += '</div>';
								content += '</a>';
								content += '</div>';
								content += '</div>';
								content += '</div>';
				          });
				        	jQuery("#product_list").empty();
									jQuery('#product_list').html(content);
				    			//if(content !='')
								// {
								// 	jQuery("#product_list").empty();
								// 	jQuery('#product_list').append(content);
								// }
				        		content = '';
				        	jQuery.each(result.categories, function(i,category){
								content += '<div class="panel panel-default">';
								content += '<div class="panel-heading">';
								content += '<h4 class="panel-title">';
								content += '<input type="checkbox" name="selectCategoryBox[]" id="category_id_'+category.category_id+'" value="'+category.category_id+'" class="large">';
								content += '<a data-toggle="collapse" data-parent="#accordian" onclick ="submitFilter()"  href="#sportswear">'+category.category_name+'</a>';
								content += '</h4>';
								content += '</div>';
								content += '</div>';
				          });
				    			//     	if(content !='')
								// {
								// 	jQuery("#categoryFilterAccordian").empty();
								// 	jQuery('#categoryFilterAccordian').html(content);
								// }
							jQuery("#categoryFilterAccordian").empty();
							jQuery('#categoryFilterAccordian').html(content);
				        	jQuery('input[type="checkbox"]').checkbox();
				        	loadMore ();

                    }
                });
}

function categoryFilter (argument) {
	var formData = new FormData(jQuery('#productFilterForm')[0]);
		                data_slider_value = jQuery('.tooltip-inner').text();

		                formData.append("action",'product_filter');
		                formData.append("data_slider_value",data_slider_value);
		                jQuery.ajax({
		                        type: 'POST',
		                        url: ajaxurl,
		                        data: formData,
		                        cache: false,
		                        contentType: false,
		                        processData: false,
		                         beforeSend:function (argument) {
		                           // jQuery('.overlay').fadeIn('slow'); 
		                           // jQuery('.response').fadeIn('slow').html("Processing...");
		                        },
		                        success: function(result) {
		                           //jQuery('.response').fadeIn('slow').html(result).delay(5000).fadeOut('slow');
		                           //jQuery('.overlay').delay(5100).fadeOut('slow');
		                           		content = '';
							        	jQuery.each(result.catelog, function(i,product){
								          	content += '<div class="col-sm-4 eachProduct jscroll-next">';
											content += '<div class="product-image-wrapper">';
											content += '<div class="single-products">';
											content += '<a href="/product/'+product.product_id+'">';
											content += '<div class="productinfo text-center">';
											content += '<img src="'+product.image_url+'" alt="" />';
											content += '<h2>MRP '+product.product_price+'</h2>';
											content += '<a href="/product/'+product.product_id+'">'+product.product_name+'<?php //echo $product->product_name ?></a>';
											content += '</div>';
											content += '</a>';
											content += '</div>';
											content += '</div>';
											content += '</div>';
							          });
							        	jQuery("#product_list").empty();
												jQuery('#product_list').html(content);

							        	jQuery('input[type="checkbox"]').checkbox();
							        	loadMore ();
		                        }
		                    });
}
	</script>
        <?php
    	echo $after_widget;
    	
    }

    /**
     * Deals with the settings when they are saved by the admin. Here is
     * where any validation should be dealt with.
     *
     * @param array  An array of new settings as submitted by the admin
     * @param array  An array of the previous settings
     * @return array The validated and (if necessary) amended settings
     * @author Ankit Balyan - sf.ankit@gmail.com
     **/
    // function update( $new_instance, $old_instance ) {

    //     // update logic goes here
    //     $updated_instance = $new_instance;
    //     return $updated_instance;
    // }?

    /**
     * Displays the form for this widget on the Widgets page of the WP Admin area.
     *
     * @param array  An array of the current settings for this widget
     * @return void Echoes it's output
     * @author Ankit Balyan - sf.ankit@gmail.com
     **/
    
    public function form( $instance ) {
		$category_filter_title = ! empty( $instance['category_filter_title'] ) ? $instance['category_filter_title'] : __( 'Category', 'text_domain' );
		?>
		<p>
		<label for="<?php echo $this->get_field_id( 'category_filter_title' ); ?>"><?php _e( 'Category Title:' ); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'category_filter_title' ); ?>" name="<?php echo $this->get_field_name( 'category_filter_title' ); ?>" type="text" value="<?php echo esc_attr( $category_filter_title ); ?>">
		</p>
		<?php 
		$brand_filter_title = ! empty( $instance['brand_filter_title'] ) ? $instance['brand_filter_title'] : __( 'Category', 'text_domain' );
		?>
		<p>
		<label for="<?php echo $this->get_field_id( 'brand_filter_title' ); ?>"><?php _e( 'Category Title:' ); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'brand_filter_title' ); ?>" name="<?php echo $this->get_field_name( 'brand_filter_title' ); ?>" type="text" value="<?php echo esc_attr( $brand_filter_title ); ?>">
		</p>
		<?php
		$price_filter_title = ! empty( $instance['price_filter_title'] ) ? $instance['price_filter_title'] : __( 'Category', 'text_domain' );
		?>
		<p>
		<label for="<?php echo $this->get_field_id( 'price_filter_title' ); ?>"><?php _e( 'Category Title:' ); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'price_filter_title' ); ?>" name="<?php echo $this->get_field_name( 'price_filter_title' ); ?>" type="text" value="<?php echo esc_attr( $price_filter_title ); ?>">
		</p>
		<?php
	}
}
