<?php defined('ABSPATH') or die("No script kiddies please!"); ?>
<?php defined('ASSETS') or define('ASSETS', get_template_directory_uri().'/assets' ); ?>
<?php

/*

Template Name: Products page

*/
ob_start();
if (isLogin()) {
$url = trim($_SERVER["REQUEST_URI"],'/');

$parts = parse_url($url);
$path = explode("/", $parts["path"]);
array_splice($path, 1, 0, array("controller")); //This line does the magic!
  

 // print_r($path);
// if (is_home() && is_page('home')) {
// 	get_header('home');
// }
// else{
// 	get_header();
// }
// mail('sf.ankit@gmail.com', 'the subject', 'the message', null,
//    'ab4ads@gmail.com'); 
get_header('home');
if(isset($path[0]) && $path[0] != '' && isset($path[2]) && $path[2] != '')
{
	$products = getCatelogBy($path[0],$path[2]);
}
elseif(isset($path[0]) && $path[0] != '')
{
	$products = getCatelogBy($path[0]);
	//wp_title( $path[2] );
}
else{
	$products = getCatelog();
}
global $wpdb;
?>
	<section>
		<div class="container">
			<div class="row">
				<?php get_sidebar('left-sidebar'); ?>
				
				<div class="col-sm-9 padding-right">
					<div class="features_items"><!--features_items-->
						<h2 class="title text-center">Items</h2>
						<?php 
						if(count($products))
							{?>	
									<div class="scroller">
									<div id="product_list">
									<?php foreach ($products as $product): ?>

										<div class="col-sm-4 eachProduct jscroll-next">
											<div class="product-image-wrapper">
												<div class="single-products">
													<a href="<?php echo site_url().'/product/'.$product->product_id ?>">
														<div class="productinfo text-center">
															<img src="<?php echo $product->image_url ?>" alt="" />
															<h2><span><span><i class="fa fa-inr"></i></span></span> <?php echo money_format('%!.0i', $product->product_price); ?></h2>
															<a href="<?php echo site_url().'/product/'.$product->product_id ?>"><?php echo $product->product_name ?></a>
															<!-- <a href="#" class="btn btn-default add-to-cart"><i class="fa fa-shopping-cart"></i>Add to cart</a> -->
														</div>
													</a>
												</div>
											</div>
										</div>
									<?php endforeach ?>
									</div>
									
									<div class="loadMore">Load More</div>
									</div>
							<?php }
							?>
					</div><!--features_items-->
				</div>
			</div>
		</div>
	</section>
	<script>
	jQuery(document).ready(function($) {
		jQuery("#tabs" ).tabs();
		// jQuery(window).scroll(function(event) {
		// 	//if(window.pageYOffset > )
		// 	winHeight = jQuery(window).height();
		// 	var height = jQuery(window).scrollTop();
		// 	if(jQuery(window).scrollTop() + jQuery(window).height() == jQuery(document).height())  //user scrolled to bottom of the page?
		// 	{
		// 		loadProducts();
		// 	}
		// 	//
		// 	//alert("scroll event detected! " + window.pageXOffset + " " + window.pageYOffset);
		// });
		loadMore();
	});
	
	function loadProducts(){
				        var formData;
		                data_slider_value = jQuery('.tooltip-inner').text();
		                countProducts = jQuery('.eachProduct').length;
		                var content = "";
		                jQuery.ajax({
		                        type: 'POST',
		                        url: ajaxurl,
		                        data: {'action': 'product_load','offset': countProducts, 'start': '12'},
		                        cache: false,

		                         beforeSend:function (argument) {
		                           // jQuery('.overlay').fadeIn('slow'); 
		                           // jQuery('.response').fadeIn('slow').html("Processing...");
		                        },
		                        success: function(result) {
		                           //jQuery('.response').fadeIn('slow').html(result).delay(5000).fadeOut('slow');
		                           //jQuery('.overlay').delay(5100).fadeOut('slow');
		                           		var numberOfElements = result.length;
		                           		if (numberOfElements != 0) {
							        	jQuery.each(result, function(i,product){
							          	content = '<div class="col-sm-4 eachProduct jscroll-next">';
										content += '<div class="product-image-wrapper">';
										content += '<div class="single-products">';
										content += '<a href="product/'+product.product_id+'">';
										content += '<div class="productinfo text-center">';
										content += '<img src="'+product.image_url+'" alt="" />';
										content += '<h2>MRP '+product.product_price+'</h2>';
										content += '<a href="product/'+product.product_id+'">'+product.product_name+'</a>';
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
							            if(content !='')
							            {
							            	jQuery('#product_list').append(content);
							            }
							          });
									}
		                        }
		                    });
		             }
	</script>
<?php get_footer(); ?>
<?php
 } // login check closed

else{

    $message = "ln";

    $url = home_url("/?sfi=$message");

    //$dashboardPage = get_page_by_title('Brand','', 'page' );
    
    wp_redirect(site_url('login'));
}
?>
<?php ob_flush(); ?>
