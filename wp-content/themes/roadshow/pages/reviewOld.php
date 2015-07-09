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
$orders = getOrders();
	?>
	<section>
		<div class="container">
			<div class="row">
				<?php //get_sidebar('left-sidebar'); ?>
				<!-- <pre><?php print_r($orders) ?></pre> -->
				<div class="col-sm-12 col-md-12">
					<header><h3>Order Review</h3></header>
					<div class="container">
						<div class="row">
							<div class="col-sm-12">
							<div id="reviewOrderTabs" class="reviewOrder">
								<ul>
									<li><a href="#review_order_tab1">My Orders</a></li>
									<?php if (isDistributor()): ?>
									<li><a href="#review_order_tab2">Dealer Products</a></li>
								<?php endif; ?>
								</ul>
								<div id="review_order_tab1">
									<?php $ordered_ids = getOrederedId(); ?>
									<?php if (count($ordered_ids)): ?>
									<?php $ordered_ids['brand_id'] = array_values($ordered_ids['brand_id']) ?>
									<?php $ordlerLevels = get_nested(); ?>
									<ul class="accordion">
									<?php foreach ($ordlerLevels as $key => $brand): ?>
										<?php if (in_array($brand['brand_id'], $ordered_ids['brand_id'])): ?>
											<li><a href="#<?php echo $brand['brand_slug'] ?>"> <span class="liTitle"><?php echo ucfirst($brand['brand_name']);  ?></span><span class ="pull-right"> Rs <?php echo getOrderBrandsSum($brand['brand_id'],TRUE) ?></span> <span class ="pull-right"><?php echo getOrderBrandsSum($brand['brand_id'],TRUE,'qty').' pcs, ' ?> </span></a>
												<?php if (isset($brand['children']) && count($brand['children'])): ?>
													<ul>
													<?php foreach ($brand['children'] as $key => $subBrand): ?>
														<?php if (in_array($subBrand['brand_id'], $ordered_ids['brand_id'])): ?>
															<li><a href="#<?php echo $brand['brand_slug'].$subBrand['brand_slug'] ?>"> <span class="liTitle"><?php echo ucfirst($subBrand['brand_name']);  ?></span> <span class ="pull-right"> Rs <?php echo getOrderBrandsSum($subBrand['brand_id']) ?></span><span class ="pull-right"><?php echo getOrderBrandsSum($subBrand['brand_id'],FALSE,'qty').' pcs, ' ?> </span></a>
															<?php if (isset($subBrand['category']) && count($subBrand['category'])): ?>
																	<ul>
																			<!-- <pre><?php // print_r($subBrand['category']) ?></pre> -->
																	<?php foreach ($subBrand['category'] as $key => $category): ?>
																		<!-- <pre><?php //print_r($ordered_ids['category_id']); ?></pre> -->
																		<?php //echo $category['category_id']; ?>
																		<?php if (in_array($category['category_id'], $ordered_ids['category_id'])): ?>
																			<?php  $category['category_id'] ?>
																			<?php $orders = getOrderByCategory($category['category_id']); ?>
																			<li> <a href="#<?php echo $brand['brand_slug'].'-'.$subBrand['brand_slug'].'-'.$category['category_name'] ?>"> <span class="liTitle"><?php echo ucfirst($category['category_name']);  ?> </span><span class="pull-right">Rs <?php echo getOrderCategorySum($category['category_id'])?></span><span class="pull-right"> <?php echo getOrderCategorySum($category['category_id'],'qty').' pcs, '?></span></a>
																				<ul>
																					<?php foreach ($orders as $key => $order): ?>
																						<?php $product = getCatelog($order->product_id)[0] ?>
																						<?php $disabled = ($order->order_confirm_ind)? ' disabled= "TRUE" ' : ''; ?>
																						<li> <?php echo ($order->order_confirm_ind)? '<i class="fa fa-check-circle fa-success fa-lg"></i> ' : ''; ?><a href="#order_id<?php echo $order->order_id ?>"><b>Order Id:</b> <?php echo $order->order_id ?> - 
																								<b>Product Code:</b> <?php echo $product->product_code  ?> 
																								<?php echo ($order->product_grade != '') ? ' - <b>Grade:</b>'.$order->product_grade : ''  ?>
																								<?php echo ($product->product_color != '') ? ' - <b>Color:</b>'.$product->product_color : ''  ?>
																								<span class="pull-right"> Rs <span id="total_price_span<?php echo $order->order_id; ?>"><?php echo $order->total_price ?></span></span></a>
																							<div class="row" style="border:1px solid #ccc;padding:5px;"> 
																								<div class="review_order_box">
																								<form action="" name="reviewOrderForm" id="reviewOrderForm<?php echo $order->order_id ?>" class="reviewOrderFormClass" <?php echo $disabled; ?> >
																									<div class="row">
																										<div class="col-sm-2"><span class=""><img class="img img-thumbnail img-responsive" src="<?php echo $product->image_url?>" alt=""> </span></div>
																										<div class="col-sm-5">
																										<div class="row">
																											<div class="col-sm-12">
																												<?php if (isset($product->product_size1) && $product->product_size1 != null): ?>
																												<div class="col-sm-1 size_label">
																													<span>&nbsp&nbsp&nbsp<?php echo $product->product_size1 ?></span>
																													<span><input type="text" class="sizeQtyGrid digits" data-order-id="<?php echo $order->order_id ?>" name="product_size_type[product_size1]" value="<?php echo $order->order_size1 ?>" <?php echo $disabled; ?> /></span>
																												</div>
																											<?php endif; ?>
																											<?php if (isset($product->product_size2) && $product->product_size2 != null): ?>
																												<div class="col-sm-1 size_label">
																													<span>&nbsp&nbsp&nbsp<?php echo $product->product_size2 ?></span>
																													<span><input type="text" class="sizeQtyGrid digits" data-order-id="<?php echo $order->order_id ?>" name="product_size_type[product_size2]" value="<?php echo $order->order_size2 ?>" <?php echo $disabled; ?> /></span>
																												</div>
																											<?php endif; ?>
																											<?php if (isset($product->product_size3) && $product->product_size3 != null): ?>
																												<div class="col-sm-1 size_label">
																													<span>&nbsp&nbsp&nbsp&nbsp<?php echo $product->product_size3 ?></span>
																													<span><input type="text" class="sizeQtyGrid digits" data-order-id="<?php echo $order->order_id ?>" name="product_size_type[product_size3]" value="<?php echo $order->order_size3 ?>" <?php echo $disabled; ?> /></span>
																												</div>
																											<?php endif; ?>
																											<?php if (isset($product->product_size4) && $product->product_size4 != null): ?>
																												<div class="col-sm-1 size_label">
																													<span>&nbsp&nbsp<?php echo $product->product_size4 ?></span>
																													<span><input type="text" class="sizeQtyGrid digits" data-order-id="<?php echo $order->order_id ?>" name="product_size_type[product_size4]" value="<?php echo $order->order_size4 ?>" <?php echo $disabled; ?> /></span>
																												</div>
																											<?php endif; ?>
																											<?php if (isset($product->product_size5) && $product->product_size5 != null): ?>
																												<div class="col-sm-1 size_label">
																													<span>&nbsp&nbsp<?php echo $product->product_size5 ?></span>
																													<span><input type="text" class="sizeQtyGrid digits" data-order-id="<?php echo $order->order_id ?>" name="product_size_type[product_size5]" value="<?php echo $order->order_size5 ?>" <?php echo $disabled; ?> /></span>
																												</div>
																											<?php endif; ?>
																											<?php if (isset($product->product_size6) && $product->product_size6 != null): ?>
																												<div class="col-sm-1 size_label">
																													<span>&nbsp&nbsp<?php echo $product->product_size6 ?></span>
																													<span><input type="text" class="sizeQtyGrid digits" data-order-id="<?php echo $order->order_id ?>" name="product_size_type[product_size6]" value="<?php echo $order->order_size6 ?>" <?php echo $disabled; ?> /></span>
																												</div>
																											<?php endif; ?>
																											<?php if (isset($product->product_size7) && $product->product_size7 != null): ?>
																												<div class="col-sm-1 size_label">
																													<span>&nbsp&nbsp<?php echo $product->product_size7 ?></span>
																													<span><input type="text" class="sizeQtyGrid digits" data-order-id="<?php echo $order->order_id ?>" name="product_size_type[product_size7]" value="<?php echo $order->order_size7 ?>" <?php echo $disabled; ?> /></span>
																												</div>
																											<?php endif; ?>
																											<?php if (isset($product->product_size8) && $product->product_size8 != null): ?>
																												<div class="col-sm-1 size_label">
																													<span>&nbsp&nbsp<?php echo $product->product_size8 ?></span>
																													<span><input type="text" class="sizeQtyGrid digits" data-order-id="<?php echo $order->order_id ?>" name="product_size_type[product_size8]" value="<?php echo $order->order_size8 ?>" <?php echo $disabled; ?> /></span>
																												</div>
																											<?php endif; ?>
																											</div>
																										</div>
																											
																										</div>
																										<div class="col-sm-2"><span><?php echo $product->product_name ?></span></div>
																										<div class="col-sm-2"><span>Rs <div id="product_price<?php echo $order->order_id ?>"><?php echo $product->product_price ?></div></span></div>
																										<div class="col-sm-1"><span> <input type="text" class="total_qty_class" name="total_qty" data-order-id="<?php echo $order->order_id ?>" id="total_qty<?php echo $order->order_id ?>" size="4" value="<?php echo $order->total_qty ?>" <?php echo $disabled; ?> readonly> Pieces</span></div>	
																									</div>
																									<div class="reviewButtons">
																											<div>
																											<?php if (!($order->order_confirm_ind)): ?>
																												<input type="hidden" name="total_price" id="total_price<?php echo $order->order_id ?>" value="<?php echo $order->total_price ?>">
																												<input type="hidden" name="order_id" value="<?php echo $order->order_id ?>">
																												<input type="submit" name="confirm" class="confirmOrder btn btn-default get" id="confirmOrderId<?php echo $order->order_id; ?>" data-order-id="<?php echo $order->order_id; ?>" class="btn" value="Confirm" <?php echo $disabled; ?>>
																												<input type="button" name="confirm" class="deleteOrder btn btn-default get" id="cancelOrderId<?php echo $order->order_id; ?>" data-order-id="<?php echo $order->order_id; ?>" class="btn" value="Remove" <?php echo $disabled; ?>>
																												<?php endif ?>
																											</div>
																									</div>
																								</form>
																							</div>
																						</div></li>
																					<?php endforeach ?>
																				</ul>
																			</li>											
																		<?php endif ?>										
																	<?php endforeach ?>
																	</ul>
															<?php endif ?>
															</li>
														<?php endif ?>
													<?php endforeach ?>
													</ul>
												<?php endif ?>
											</li>
										<?php endif ?>
									<?php endforeach ?>
									</ul>
								<?php else: ?>
								<p>Sorry! there are no orders have been made.</p>
								<?php endif ?>
								</div>
								<?php if ($dist = isDistributor()): ?>
								<div id="review_order_tab1">
									<?php $dealers = getDealersByDistributor(isLogin()); ?>
									<?php $dealers = array_merge($dist, $dealers) ?>
									<?php foreach ($dealers as $key => $dealer): ?>
										<?php $ordered_ids = getOrderedIdByUser($dealer->user_id); ?>
	
										<?php if (count($ordered_ids)): ?>
									<?php $ordered_ids['brand_id'] = array_values($ordered_ids['brand_id']) ?>
									<?php $ordlerLevels = get_nested(); ?>
									<ul class="accordion">
									<li><a href="#<?php echo $dealer->user_name ?>"><?php echo ($dealer->user_id == isLogin()) ? "SELF: ". $dealer->user_fullname : "DEALER: ". $dealer->user_fullname; ?><span class ="pull-right"> Rs <?php echo getOrderSum() ?></span><span class ="pull-right"> <?php echo getOrderSum(null, 'qty')." pcs, " ?></span></a>
									<ul>
									<?php foreach ($ordlerLevels as $key => $brand): ?>
										<?php if (in_array($brand['brand_id'], $ordered_ids['brand_id'])): ?>
											<li><a href="#<?php echo $brand['brand_slug'] ?>"> <span class="liTitle"><?php echo ucfirst($brand['brand_name']);  ?></span><span class ="pull-right"> Rs <?php echo getOrderBrandsSum($brand['brand_id'],TRUE) ?></span><span class ="pull-right"><?php echo getOrderBrandsSum($brand['brand_id'],TRUE,'qty').' pcs, ' ?> </span></a>
												<?php if (isset($brand['children']) && count($brand['children'])): ?>
													<ul>
													<?php foreach ($brand['children'] as $key => $subBrand): ?>
														<?php if (in_array($subBrand['brand_id'], $ordered_ids['brand_id'])): ?>
															<li><a href="#<?php echo $brand['brand_slug'].$subBrand['brand_slug'] ?>"> <span class="liTitle"><?php echo ucfirst($subBrand['brand_name']);  ?></span> <span class ="pull-right"> Rs <?php echo getOrderBrandsSum($subBrand['brand_id']) ?></span><span class ="pull-right"><?php echo getOrderBrandsSum($subBrand['brand_id'],FALSE,'qty').' pcs, ' ?> </span></a>
															<?php if (isset($subBrand['category']) && count($subBrand['category'])): ?>
																	<ul>
																			<!-- <pre><?php // print_r($subBrand['category']) ?></pre> -->
																	<?php foreach ($subBrand['category'] as $key => $category): ?>
																		<!-- <pre><?php //print_r($ordered_ids['category_id']); ?></pre> -->
																		<?php //echo $category['category_id']; ?>
																		<?php if (in_array($category['category_id'], $ordered_ids['category_id'])): ?>
																			<?php // echo $category['category_id'] ?>
																			<?php $orders = getOrderByCategory($category['category_id'], $dealer->user_id); ?>
																			<li> <a href="#<?php echo $brand['brand_slug'].'-'.$subBrand['brand_slug'].'-'.$category['category_name'] ?>"> <span class="liTitle"><?php echo ucfirst($category['category_name']);  ?> </span><span class="pull-right">Rs <?php echo getOrderCategorySum($category['category_id'],'', $dealer->user_id)?></span><span class="pull-right"> <?php echo getOrderCategorySum($category['category_id'],'qty').' pcs, '?></span></a>
																				<ul>
																					<?php foreach ($orders as $key => $order): ?>
																						<?php $product = getCatelog($order->product_id)[0] ?>
																						<?php $disabled = ($order->order_confirm_ind)? ' disabled= "TRUE" ' : ''; ?>
																						<li> <?php echo ($order->order_confirm_ind)? '<i class="fa fa-check-circle fa-success fa-lg"></i> ' : ''; ?><a href="#order_id<?php echo $order->order_id ?>"><b>Order Id:</b> <?php echo $order->order_id ?> - 
																								<b>Product Code:</b> <?php echo $product->product_code  ?> 
																								<?php echo ($order->product_grade != '') ? ' - <b>Grade:</b>'.$order->product_grade : ''  ?>
																								<?php echo ($product->product_color != '') ? ' - <b>Color:</b>'.$product->product_color : ''  ?>
																								<span class="pull-right"> Rs <span id="total_price_span<?php echo $order->order_id; ?>"><?php echo $order->total_price ?></span></span></a>
																							<div class="row" style="border:1px solid #ccc;padding:5px;"> 
																								<div class="review_order_box">
																								<form action="" name="reviewOrderForm" id="reviewOrderForm<?php echo $order->order_id ?>" class="reviewOrderFormClass" <?php echo $disabled; ?> >
																									<div class="row">
																										<div class="col-sm-2"><span class=""><img class="img img-thumbnail img-responsive" src="<?php echo $product->image_url?>" alt=""> </span></div>
																										<div class="col-sm-5">
																										<div class="row">
																											<div class="col-sm-12">
																												<?php if (isset($product->product_size1) && $product->product_size1 != null): ?>
																												<div class="col-sm-1 size_label">
																													<span>&nbsp&nbsp&nbsp<?php echo $product->product_size1 ?></span>
																													<span><input type="text" class="sizeQtyGrid digits" data-order-id="<?php echo $order->order_id ?>" name="product_size_type[product_size1]" value="<?php echo $order->order_size1 ?>" <?php echo $disabled; ?> /></span>
																												</div>
																											<?php endif; ?>
																											<?php if (isset($product->product_size2) && $product->product_size2 != null): ?>
																												<div class="col-sm-1 size_label">
																													<span>&nbsp&nbsp&nbsp<?php echo $product->product_size2 ?></span>
																													<span><input type="text" class="sizeQtyGrid digits" data-order-id="<?php echo $order->order_id ?>" name="product_size_type[product_size2]" value="<?php echo $order->order_size2 ?>" <?php echo $disabled; ?> /></span>
																												</div>
																											<?php endif; ?>
																											<?php if (isset($product->product_size3) && $product->product_size3 != null): ?>
																												<div class="col-sm-1 size_label">
																													<span>&nbsp&nbsp&nbsp&nbsp<?php echo $product->product_size3 ?></span>
																													<span><input type="text" class="sizeQtyGrid digits" data-order-id="<?php echo $order->order_id ?>" name="product_size_type[product_size3]" value="<?php echo $order->order_size3 ?>" <?php echo $disabled; ?> /></span>
																												</div>
																											<?php endif; ?>
																											<?php if (isset($product->product_size4) && $product->product_size4 != null): ?>
																												<div class="col-sm-1 size_label">
																													<span>&nbsp&nbsp<?php echo $product->product_size4 ?></span>
																													<span><input type="text" class="sizeQtyGrid digits" data-order-id="<?php echo $order->order_id ?>" name="product_size_type[product_size4]" value="<?php echo $order->order_size4 ?>" <?php echo $disabled; ?> /></span>
																												</div>
																											<?php endif; ?>
																											<?php if (isset($product->product_size5) && $product->product_size5 != null): ?>
																												<div class="col-sm-1 size_label">
																													<span>&nbsp&nbsp<?php echo $product->product_size5 ?></span>
																													<span><input type="text" class="sizeQtyGrid digits" data-order-id="<?php echo $order->order_id ?>" name="product_size_type[product_size5]" value="<?php echo $order->order_size5 ?>" <?php echo $disabled; ?> /></span>
																												</div>
																											<?php endif; ?>
																											<?php if (isset($product->product_size6) && $product->product_size6 != null): ?>
																												<div class="col-sm-1 size_label">
																													<span>&nbsp&nbsp<?php echo $product->product_size6 ?></span>
																													<span><input type="text" class="sizeQtyGrid digits" data-order-id="<?php echo $order->order_id ?>" name="product_size_type[product_size6]" value="<?php echo $order->order_size6 ?>" <?php echo $disabled; ?> /></span>
																												</div>
																											<?php endif; ?>
																											<?php if (isset($product->product_size7) && $product->product_size7 != null): ?>
																												<div class="col-sm-1 size_label">
																													<span>&nbsp&nbsp<?php echo $product->product_size7 ?></span>
																													<span><input type="text" class="sizeQtyGrid digits" data-order-id="<?php echo $order->order_id ?>" name="product_size_type[product_size7]" value="<?php echo $order->order_size7 ?>" <?php echo $disabled; ?> /></span>
																												</div>
																											<?php endif; ?>
																											<?php if (isset($product->product_size8) && $product->product_size8 != null): ?>
																												<div class="col-sm-1 size_label">
																													<span>&nbsp&nbsp<?php echo $product->product_size8 ?></span>
																													<span><input type="text" class="sizeQtyGrid digits" data-order-id="<?php echo $order->order_id ?>" name="product_size_type[product_size8]" value="<?php echo $order->order_size8 ?>" <?php echo $disabled; ?> /></span>
																												</div>
																											<?php endif; ?>
																											</div>
																										</div>
																											
																										</div>
																										<div class="col-sm-2"><span><?php echo $product->product_name ?></span></div>
																										<div class="col-sm-2"><span>Rs <div id="product_price<?php echo $order->order_id ?>"><?php echo $product->product_price ?></div></span></div>
																										<div class="col-sm-1"><span> <input type="text" class="total_qty_class" name="total_qty" data-order-id="<?php echo $order->order_id ?>" id="total_qty<?php echo $order->order_id ?>" size="4" value="<?php echo $order->total_qty ?>" <?php echo $disabled; ?> readonly> Pieces</span></div>	
																									</div>
																									<div class="reviewButtons">
																											<div>
																											<?php if (!($order->order_confirm_ind)): ?>
																												<input type="hidden" name="total_price" id="total_price<?php echo $order->order_id ?>" value="<?php echo $order->total_price ?>">
																												<input type="hidden" name="order_id" value="<?php echo $order->order_id ?>">
																												<input type="submit" name="confirm" class="confirmOrder btn btn-default get" id="confirmOrderId<?php echo $order->order_id; ?>" data-order-id="<?php echo $order->order_id; ?>" class="btn" value="Confirm" <?php echo $disabled; ?>>
																												<input type="button" name="confirm" class="deleteOrder btn btn-default get" id="cancelOrderId<?php echo $order->order_id; ?>" data-order-id="<?php echo $order->order_id; ?>" class="btn" value="Remove" <?php echo $disabled; ?>>
																												<?php endif ?>
																											</div>
																									</div>
																								</form>
																							</div>
																						</div></li>
																					<?php endforeach ?>
																				</ul>
																			</li>											
																		<?php endif ?>										
																	<?php endforeach ?>
																	</ul>
															<?php endif ?>
															</li>
														<?php endif ?>
													<?php endforeach ?>
													</ul>
												<?php endif ?>
											</li>
										<?php endif ?>
									<?php endforeach ?>
									</ul>
									</li>
									</ul>
								<?php endif ?>
									<?php endforeach ?>
									<?php else: ?>
								<p>Sorry! there are no orders have been made.</p>
								</div>
								<?php endif ?>

								<div id="review_order_tab3">
									<p></p>
								</div>
							</div>
								
							</div>
							<div class="sm-4"></div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>

	<script type="text/javascript">
				jQuery(document).ready(function($) {
					jQuery('.sizeQtyGrid').change(function(event) {
							formOrderId = jQuery(this).attr('data-order-id');

							var sum = 0;
							jQuery('#reviewOrderForm'+formOrderId+' .sizeQtyGrid').each(function(){
							    sum += parseInt(this.value);
							});

							jQuery('#total_qty'+formOrderId).val(sum).trigger('change');;
					});
					jQuery('.total_qty_class').change(function(event){
						formOrderId = jQuery(this).attr('data-order-id');
						total_qty = jQuery(this).val();
						product_price = parseInt(jQuery('#product_price'+formOrderId).html());
						newPrice = product_price * total_qty;
						jQuery('#total_price_span'+formOrderId).html(newPrice);
						jQuery('#total_price'+formOrderId).val(newPrice);
					});
					jQuery('.reviewOrderFormClass').submit(function(event) {
						event.preventDefault();
						formId = jQuery(this).attr('id');
						jQuery('#'+formId).validate({
			            submitHandler: function(form) {
			                var formData = new FormData(jQuery('#'+formId)[0]);
			                formData.append("confirmOrder",1);
			                formData.append("action",'save_review_order');
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
			                           jQuery('.response').fadeIn('slow').html(result).delay(3000).fadeOut('slow');
			                           jQuery('.overlay').delay(3100).fadeOut('slow');
			                           //if (window.location.hash.length > 0) window.location = window.location.hash.substring(1);
			                           location.reload(true);
			                        }
			                    });
			             }
			        	});
					});
					jQuery('.deleteOrder').click(function(event) {
						formOrderId = jQuery(this).attr('data-order-id');
						jQuery('.overlay').fadeIn('slow'); 
						jQuery(".response").fadeIn('slow').html('<h3>Are you sure you want to delete? </h3> <p class="text-center" > <a href="javascript:void(0)" class="btn btn-default get" onclick="confrimDelete('+formOrderId+')">Yes</a> <a href="javascript:void(0)" class="btn btn-default get" onclick="cancel()">No</a></p>');

					});

					jQuery('ul.accordion').accordion();
					jQuery(function() {
						jQuery( "#reviewOrderTabs" ).tabs();
					});
					// jQuery('#total_qty').attr('readonly','true');
					// jQuery('#order_type').change(function(event) {
					// 	order_type = jQuery(this).val();
					// 	if(order_type == "0")
					// 	{
					// 		jQuery('#total_qty').attr('readonly','true');
					// 	}
					// 	else
					// 	{
					// 		jQuery('#total_qty').removeProp('readonly');
					// 	}
					// });
					// jQuery('.sizeQtyGrid').change(function(event) {
					// 	order_type = jQuery('#order_type').val();
					// 	if(order_type == "0")
					// 	{
					// 		var sum = 0;
					// 		jQuery('.sizeQtyGrid').each(function(){
					// 		    sum += parseInt(this.value);
					// 		});

					// 		jQuery('#total_qty').val(sum).trigger('change');;
					// 	}
					// });
					// jQuery('#total_qty').change(function(event) {
					// 	total_qty = jQuery(this).val();
					// 	product_price = parseInt(jQuery('#product_price').html());
					// 	newPrice = product_price * total_qty;
					// 	jQuery('#total_price').html(newPrice);
					// });
					// jQuery('#productOrder').validate({
		   //          submitHandler: function(form) {
		   //              var formData = new FormData(jQuery('#productOrder')[0]);
		   //              formData.append("action",'product_order');
		   //              jQuery.ajax({
		   //                      type: 'POST',
		   //                      url: ajaxurl,
		   //                      data: formData,
		   //                      cache: false,
		   //                      contentType: false,
		   //                      processData: false,
		   //                       beforeSend:function (argument) {
		   //                         jQuery('.overlay').fadeIn('slow'); 
		   //                         jQuery('.response').fadeIn('slow').html("Processing...");
		   //                      },
		   //                      success: function(result) {
		   //                         jQuery('.response').fadeIn('slow').html(result).delay(5000).fadeOut('slow');
		   //                         jQuery('.overlay').delay(5100).fadeOut('slow');
		   //                         alert(result);
		   //                         window.location.href = window.location.href;
		   //                      }
		   //                  });
		   //           }
		   //      	});
					
				});
function cancel () {
	jQuery('#response').hide();
	jQuery('.overlay').hide();
}
function confrimDelete (id) {
			jQuery.ajax({
                type: 'POST',
                url: ajaxurl,
                data: {'action': 'delete_order', 'order_id': id},
                cache: false,
                 beforeSend:function (argument) {
                   jQuery('.overlay').fadeIn('slow'); 
                   jQuery('.response').fadeIn('slow').html("Processing...");
                },
                success: function(result) {
                   jQuery('.response').fadeIn('slow').html(result).delay(3000).fadeOut('slow');
                   jQuery('.overlay').delay(3100).fadeOut('slow');
                   // if (window.location.hash.length > 0) window.location = window.location.hash.substring(1);
                   location.reload(true);
                }
            });
}
			</script>
			<style>
				.reviewOrder .sizeQtyGrid{
					width: 30px;
					height: 30px;
					text-align: center;
					padding: 0;
					margin: 5px;
				}
				.reviewOrder .img.img-thumbnail{
					width: 90px;
					height: 100px;
				}
				.fa.fa-success {
					
					color: green;
				}
	
	/* Level 2  
	.accordion li ul li { background: #ddd; font-size: 0.9em; } */
			</style>

<?php get_footer();