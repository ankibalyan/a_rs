<?php defined('ABSPATH') or die("No script kiddies please!"); ?>
<?php defined('ASSETS') or define('ASSETS', get_template_directory_uri().'/assets' ); ?>
<?php $slug = get_page_uri(null); 
	global $wp;
//echo $current_url = add_query_arg( $wp->query_string, '', home_url( $wp->request ) );
//echo "<br>";
$url = rtrim($_SERVER["REQUEST_URI"],'/');
$url = ltrim($_SERVER["REQUEST_URI"],'/');


$parts = parse_url($url);

$path = explode("/", $parts["path"]);
array_splice($path, 1, 0, array("controller")); //This line does the magic!
implode("/",$path);
get_header();
$id = (int) $path[2];
if($id)
{
	$product = getCatelog($id);
	if(count($product))
	$product = $product[0];
	if(count($product) && $product->product_group_code)
	{
		$coloredProducts = getColoredProducts($id, $product->product_group_code);
	}
}
	?>
	<!-- <pre><?php // print_r($product) ?></pre> -->
	<section>
		<div class="container">
			<div class="row">
				<?php //get_sidebar('left-sidebar'); ?>
				
				<div class="col-sm-12 padding-right">
					<div id="product_list"><!--product_list-->
						<?php if(isset($product) && count($product)): ?>
						<div class="product-details"><!--product-details-->
							<div class="col-sm-5">
								<div class="view-product">                  
								<ul class="bxslider">
									<?php $images = getImages($product->product_id); ?>
									<?php $i = 0; ?>
									<?php foreach ($images as $key => $image): ?>
											<li>
												<div class="magnify">
													<div class="large" id="large<?php echo $i ?>"></div>
													<img class="small" data-trigger="<?php echo $i ?>" id="small<?php echo $i ?>" src="<?php echo $image->image_url ?>" />
												   	<img class="replacewith" id="replacewith<?php echo $i ?>" src='<?php echo $image->image_url ?>' alt="" />
												</div>
											</li>
											<?php $i++; ?>
									<?php endforeach ?>

								</ul>
								</div>
							</div>
<script>
jQuery(document).ready(function($) {
	      var slider = jQuery('.bxslider').bxSlider({
          infiniteLoop: false,
          hideControlOnEnd: true,
          adaptiveHeight: true,
          slideWidth: 600,
          slideHeight: 900,
          pager: 'true',
          onBeforeSlide: function(currentSlide, totalSlides, currentSlideHtmlObject){
              jQuery('.pager').removeClass('active-slide');   
              jQuery(currentSlideHtmlObject).addClass('active-slide');
              console.log('<p class="check">Slide index ' + currentSlide + ' of ' + totalSlides + ' total slides has completed.');
              // $('#sddf').html('<p class="check">Slide index ' + currentSlide + ' of ' + totalSlides + ' total slides has completed.');
            }
        });

});
</script>
<!-- Lets make a simple image magnifier -->
<!-- <div class="magnify"> -->
	
	<!-- This is the magnifying glass which will contain the original/large version -->
<!-- 	<div class="large"></div>
	<div class="replacewith">
        <img src='<?php echo $images[0]->image_url ?>' alt="" />
    </div>
 -->	<!-- This is the small image -->
	<!-- <img class="small" src="<?php echo $images[0]->image_url ?>" width="200"/> -->
	
<!-- </div> -->

<!-- Lets load up prefixfree to handle CSS3 vendor prefixes -->
<script src="http://thecodeplayer.com/uploads/js/prefixfree.js" type="text/javascript"></script>
<!-- You can download it from http://leaverou.github.com/prefixfree/ -->


							<div class="col-sm-7 col-xs-12">
							<?php $seqProducts = getSequenceProduct($product->brandId, $product->categoryId, $product->product_seq); ?>
									<?php if(count($seqProducts)): ?>
										<div class="sequece_wrap">
											<?php if (isset($seqProducts['prev']) && $seqProducts['prev']): ?>
												<a href="<?php echo site_url().'/product/'.$seqProducts['prev'] ?>" class="pull-left"><i class="fa fa-arrow-left fa-lg"></i> Prev</a>
											<?php endif ?>
											<?php if(isset($seqProducts['next']) && $seqProducts['next']): ?>
												<a href="<?php echo site_url().'/product/'.$seqProducts['next'] ?>" class="pull-right">Next <i class="fa fa-arrow-right fa-lg"></i></a>
											<?php endif ?>
											<div class="clearfix"></div>
										</div>
									<?php endif; ?>
								<div class="product-information"><!--/product-information-->
									<form action="" method="POST" name="productOrder" id="productOrder">
										<input type="hidden" name="product_id" value="<?php echo $product->product_id ?>" id="">
										
										<h2><?php echo $product->product_name ?></h2>
                    <p> <img src="<?php echo getBrands($product->brandId)[0]->brand_image; ?>" width="200" heigh ="150" alt="<?php echo getBrands($product->brandId)[0]->parent_brand ?>"> </p>
										<p>FG Code: <?php echo $product->product_code ?></p>
                      <p> <a id="details" href="javascript:void(0)">View Details...</a></p>
                        <div class="responsess">
                           <a href="javascript:void(0)" class="closedeails">&#10060</a>
                          <div class="col-sm-12 text-left">
										<p><?php $attrs = $product->product_attr; $attrs = explode(',', $attrs); ?></p>
										<ul class="productAttr">
										<?php foreach ($attrs as $key => $attr): ?>
											<?php echo "<li>".$attr."</li>" ?>
										<?php endforeach ?>
										</ul>
										<hr>
										<p><?php echo $product->product_desc ?></p>
										
						</div>	
                 
                  </div>
<?php if(!isArvindUser() && !isBrandUser()): ?>
<div class="row">
<div class="col-sm-4">
                    <p>Price  : <i class="fa fa-inr"></i>
												<b><?php echo money_format('%!.0i', $product->product_price) ?></b> </p>
</div>
	<div class="col-sm-5">
 <label>Grade:</label>
												<select class="grade" id="product_grade" class="orderType" name="product_grade">
													<option value="">Null</option>
													<option value="A">A</option>
													<option value="B" >B</option>
													<option value="C" >C</option>
													<option value="D" >D</option>
												</select>
												
</div>
<label for="product_grade" class="gradeError error"></label>
</div>
										<div class="row">
										<div class="col-sm-7"><br/>
												<label>Order Type:</label>
												<select class="ordertype" id="order_type" name="order_type">
													<option value="0">Order By Qty</option>
													<option value="1">Order By Ratio</option>
												</select>
                            <div id="ratio">
                         <input id="saveratio" name="save_ratio" type="checkbox" class="checkbox" />Save Ratio
                           </div>
											</div>
										
										</div>
										
										<div class="row">
											<div class="col-sm-12">
												<div class="row">
														<?php if (isset($product->product_size1) && $product->product_size1 != null): ?>
														<div class="col-sm-2 col-md-1 col-xs-4">
															
															<span>
<p>&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp<?php echo $product->product_size1 ?></p>
<input type="text" class="sizeQtyGrid digits" id="product_size1" name="product_size_type[product_size1]" value="0" /></span>
															</div>
														<?php endif ?>
														<?php if (isset($product->product_size2) && $product->product_size2 != null): ?>
														<div class="col-sm-2 col-md-1 col-xs-4">
															
															<span>
<p>&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp<?php echo $product->product_size2 ?></p>
<input type="text" class="sizeQtyGrid digits" id="product_size2" name="product_size_type[product_size2]" value="0" /></span>
														</div>	
														<?php endif ?>
														<?php if (isset($product->product_size3) && $product->product_size3 != null): ?>
														<div class="col-sm-2 col-md-1 col-xs-4">
															
															<span>
<p>&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp<?php echo $product->product_size3 ?></p>
<input type="text" class="sizeQtyGrid digits" id="product_size3" name="product_size_type[product_size3]" value="0" /></span>
														</div>	
														<?php endif ?>
														<?php if (isset($product->product_size4) && $product->product_size4 != null): ?>
														<div class="col-sm-2 col-md-1 col-xs-4">
															
															<span>
<p>&nbsp&nbsp&nbsp<?php echo $product->product_size4 ?></p>
<input type="text" class="sizeQtyGrid digits" id="product_size4" name="product_size_type[product_size4]" value="0" /></span>
														</div>	
														<?php endif ?>
                           
														<?php if (isset($product->product_size5) && $product->product_size5 != null): ?>
														<div class="col-sm-2 col-md-1 col-xs-4">
															
															<span>
<p>&nbsp&nbsp&nbsp<?php echo $product->product_size5 ?></p>
<input type="text" class="sizeQtyGrid digits" id="product_size5" name="product_size_type[product_size5]" value="0" /></span>
														</div>	
														<?php endif ?>
														<?php if (isset($product->product_size6) && $product->product_size6 != null): ?>
														<div class="col-sm-2 col-md-1 col-xs-4">
															
															<span>
<p>&nbsp&nbsp&nbsp<?php echo $product->product_size6 ?></p>
<input type="text" class="sizeQtyGrid digits" id="product_size6" name="product_size_type[product_size6]" value="0" /></span>
														</div>
														<?php endif ?>
														<?php if (isset($product->product_size7) && $product->product_size7 != null): ?>
														<div class="col-sm-2 col-md-1 col-xs-4">
															
															<span>
<p>&nbsp&nbsp&nbsp<?php echo $product->product_size7 ?></p>
<input type="text" class="sizeQtyGrid digits" id="product_size7" name="product_size_type[product_size7]" value="0" /></span>
														</div>
														<?php endif ?>
														<?php if (isset($product->product_size8) && $product->product_size8 != null): ?>
														<div class="col-sm-2 col-md-1 col-xs-4">
															
															<span>
<p>&nbsp&nbsp&nbsp<?php echo $product->product_size8 ?></p>
<input type="text" class="sizeQtyGrid digits" id="product_size8" name="product_size_type[product_size8]" value="0" /></span>
														</div>
														<?php endif ?>
												</div>
											</div>
										</div>
												<div class="row">
														<div class="col-sm-5 col-md-3 col-xs-5">
															<label>Total Qty: </label>
															<span>
<input type="text" class="numOnly orderType" id="total_qty" name="total_qty" value="" placeholder="Qty" /></span>
														</div>
	<div class="col-sm-3">
										<span><span class="totalprice"><i class="fa fa-inr"></i>&nbsp;
												<div id="product_price" class="hide hidden"><?php echo $product->product_price ?></div> 
												<div id="total_price" style="float: right;"> 0</div>
												 </span></span></div>
</div>
<div class="row">
	
										<div class="col-sm-4 col-xs-12">
														
									<span class="place">
												<button type="submit" class="btn btn-fefault cart">
													<i class="fa fa-shopping-cart"></i>
													Place Order
												</button>

											</span>
											</div>
										</div>
<?php endif; ?>
										</div>
									</div><!--/product-information-->
										<?php if (isset($coloredProducts) && count($coloredProducts)): ?>
											<div class="color-swatch">
											<?php foreach ($coloredProducts as $colorProduct): ?>
												<div class="color_box"><a href="<?php echo site_url().'/product/'.$colorProduct->product_id ?>"><img src="<?php echo $colorProduct->image_url ?>" style="border-bottom: 5px solid <?php echo $colorProduct->product_color ?>" alt="" /></a></div>
											<?php endforeach ?>
											</div>
										<?php endif ?>
							</form>
						</div>
					<?php endif; ?>
					</div><!--/product_list-->
					
				</div>
			</div>
		</div>
	</section>
	<script type="text/javascript">
				jQuery(document).ready(function($) {
					jQuery('#total_qty').attr('readonly','true');
					jQuery('#order_type').change(function(event) {
						order_type = jQuery(this).val();
						if(order_type == "0")
						{
							jQuery('#total_qty').attr('readonly','true');
						}
						else
						{
							jQuery('#total_qty').removeProp('readonly');

							jQuery('#product_size1').val(<?php echo $product->category_size1 ?>);
							jQuery('#product_size2').val(<?php echo $product->category_size2 ?>);
							jQuery('#product_size3').val(<?php echo $product->category_size3 ?>);
							jQuery('#product_size4').val(<?php echo $product->category_size4 ?>);
							jQuery('#product_size5').val(<?php echo $product->category_size5 ?>);
							jQuery('#product_size6').val(<?php echo $product->category_size6 ?>);
							jQuery('#product_size7').val(<?php echo $product->category_size7 ?>);
							jQuery('#product_size8').val(<?php echo $product->category_size8 ?>);
						}
					});
					jQuery('.sizeQtyGrid').change(function(event) {
						order_type = jQuery('#order_type').val();
						if(order_type == "0")
						{
							var sum = 0;
							jQuery('.sizeQtyGrid').each(function(){
								if(this.value != '')
								{
									sum += parseInt(this.value);
								}
							    
							});
							
							jQuery('#total_qty').val(sum).trigger('change');;
						}
					});
					jQuery('#total_qty').change(function(event) {
						total_qty = jQuery(this).val();
						product_price = parseInt(jQuery('#product_price').html());
						newPrice = product_price * total_qty;
						jQuery('#total_price').html(newPrice);
					});
					jQuery('#productOrder').validate({
						//  rules: {
						// 	product_grade: {
						// 		require_from_group: [1, ".orderType"]
						// 	},
						// 	total_qty: {
						// 		require_from_group: [1, ".orderType"]
						// 	}
						// },
						// message:{
						// 	product_grade: {
						// 		require_from_group: ["Please select the Grade"],
						// 		require: ["Please select the Grade"]
						// 	},
						// 	total_qty: {
						// 		require_from_group: ["Please select the Quantities"],
						// 		require: ["Please select the Quantities"]
						// 	}
						// },
		            submitHandler: function(form) {
		                var formData = new FormData(jQuery('#productOrder')[0]);
		                total_price = parseInt(jQuery('#total_price').text());

		                formData.append("action",'product_order');
		                formData.append("total_price",total_price);
		                formData.append("action",'product_order');
		                jQuery.ajax({
		                        type: 'POST',
		                        url: ajaxurl,
		                        data: formData,
		                        cache: false,
		                        contentType: false,
		                        processData: false,
		                         beforeSend:function (argument) {
		                           jQuery('.overlay').fadeIn('slow'); 
		                           jQuery('.response').fadeIn('slow').html("Processing...");
		                        },
		                        success: function(result) {
		                           jQuery('.response').html(result).delay(5000).fadeOut('slow');
		                           jQuery('.overlay').delay(3100).fadeOut('slow');
		                        }
		                    });
		             }
		        	});
          
          jQuery('#details').click(function(event) {
						
						jQuery(".responsess").fadeIn('slow');

					});
					 jQuery('.closedeails').click(function(event) {
						
						jQuery(".responsess").fadeOut('slow');

					});
          jQuery('.ordertype').on('change',function(){
            
          	val = jQuery(this).val();
            if(val == 1){
             
              jQuery("#ratio").fadeIn('slow');
              }
    else if (val != 1)
     {
      jQuery("#ratio").fadeOut('slow');
      }
          });
				});
			</script>
<?php get_footer();