<?php defined('ABSPATH') or die("No script kiddies please!"); ?>
<?php defined('ASSETS') or define('ASSETS', get_template_directory_uri().'/assets' ); ?>
<?php

/*

Template Name: 

*/

if (is_home()) {
	get_header('home');
}
else{
	get_header();
}
?>
	<section>
		<div class="container">
			<div class="row">
				<?php get_sidebar('left-sidebar'); ?>
				<div class="col-sm-9 padding-right">
					<div class="features_items"><!--features_items-->
						<h2 class="title text-center">Items</h2>
						<?php $products = getCatelog();
						if(count($products))
							{?>
								<div class="jscroll">
									<div id="product_list">
									<?php foreach ($products as $product): ?>
										<div class="col-sm-4">
											<div class="product-image-wrapper">
												<div class="single-products">
													<div class="productinfo text-center">
														<img src="<?php echo $product->image_url ?>" alt="" />
														<h2>MRP <?php echo $product->product_price ?></h2>
														<a href="<?php echo site_url().'/product/'.$product->product_id ?>"><?php echo $product->product_name ?></a>
														<!-- <a href="#" class="btn btn-default add-to-cart"><i class="fa fa-shopping-cart"></i>Add to cart</a> -->
													</div>
													
												</div>
											</div>
										</div>
									<?php endforeach ?>
									</div>
								</div>
							<?php }
							?>
						<?php if(count($products) > 24): ?>
						<ul class="pagination">
							<li class="active"><a href="">1</a></li>
							<li><a href="">2</a></li>
							<li><a href="">3</a></li>
							<li><a href="">&raquo;</a></li>
						</ul>
					<?php endif; ?>
					</div><!--features_items-->
				</div>
			</div>
		</div>
	</section>
	<script>
	// jQuery(document).ready(function($) {
	// 	jQuery('.jscroll').jscroll({
	// 	    loadingHtml: '<img src="loading.gif" alt="Loading" /> Loading...',
	// 	    padding: 20,
	// 	    nextSelector: 'div.col-sm-4',
	// 	    contentSelector: 'div.col-sm-4'
	// 	});
	// });
	</script>
<?php get_footer(); ?>

