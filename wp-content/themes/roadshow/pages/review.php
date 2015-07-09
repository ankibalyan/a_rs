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
//$orders = getOrders();
	?>
	<section>
		<div class="container">
			<div class="row">
				<?php //get_sidebar('left-sidebar'); ?>
				<div class="col-sm-12 col-md-12">
					<header><h3>Order's Review</h3></header>
					<div class="massDel pull-right"><a href="javascript:void(0)" onclick="massDelform()" >Mass Delete</a></div>
					<div class="container">
						<div class="row">
							<div class="col-sm-12">
							<div id="reviewOrderTabs" class="reviewOrder">
								<ul>
								<?php if (!isDistributor()): ?>
									<li><a href="#review_order_tab1">My Order's Review </a></li>
								<?php endif; ?>
									<?php if (isDistributor()): ?>
									<li><a href="#review_order_tab2">All Orders</a></li>
								<?php endif; ?>
								</ul>
								<?php if (!isDistributor()): ?>
								<div id="review_order_tab1">
									<?php $ordered_ids = getOrederedId(); ?>
									<?php if (count($ordered_ids)): ?>
									<?php $ordered_ids['brand_id'] = array_values($ordered_ids['brand_id']) ?>
									<?php $ordlerLevels = get_nested(); ?>
									<ul class="accordion">
									<?php $countBrand = 0; ?>
									<?php foreach ($ordlerLevels as $key => $brand): ?>
										<?php if (in_array($brand['brand_id'], $ordered_ids['brand_id'])): ?>
											<?php $countBrand++; ?>
											<li><a href="#<?php echo $brand['brand_slug'] ?>"> <span class="liTitle"><?php echo ucfirst($brand['brand_name']);  ?></span><span class ="pull-right"> Rs <?php echo getOrderBrandsSum(isLogin(),$brand['brand_id'],TRUE) ?></span> <span class ="pull-right"><?php echo getOrderBrandsSum(isLogin(),$brand['brand_id'],TRUE,'qty').' pcs |&nbsp; ' ?> </span></a>
												<?php if (isset($brand['children']) && count($brand['children'])): ?>
													<ul>
													<?php $countSubbrand = 0; ?>
													<?php foreach ($brand['children'] as $key => $subBrand): ?>
														<?php if (in_array($subBrand['brand_id'], $ordered_ids['brand_id'])): ?>
															<?php $countSubbrand++ ?>
															<li><a href="#<?php echo $brand['brand_slug'].$subBrand['brand_slug'] ?>"> <span class="liTitle"><?php echo ucfirst($subBrand['brand_name']);  ?></span> <span class ="pull-right"> Rs <?php echo getOrderBrandsSum(isLogin(),$subBrand['brand_id']) ?></span><span class ="pull-right"><?php echo getOrderBrandsSum(isLogin(),$subBrand['brand_id'],FALSE,'qty').' pcs |&nbsp;  ' ?> </span></a>
															<?php if (isset($subBrand['category']) && count($subBrand['category'])): ?>
																	<ul>
																			<!-- <pre><?php // print_r($subBrand['category']) ?></pre> -->
																			<?php $countCategory = 0 ?>
																	<?php foreach ($subBrand['category'] as $key => $category): ?>
																		<!-- <pre><?php //print_r($ordered_ids['category_id']); ?></pre> -->
																		<?php //echo $category['category_id']; ?>
																		<?php if (in_array($category['category_id'], $ordered_ids['category_id']) && matchOrderBrandCategoryUser($subBrand['brand_id'],$category['category_id'],isLogin())): ?>
																			<?php $countCategory++ ?>
																			<?php  $category['category_id'] ?>
																			<?php $orders = getOrderByCategory($category['category_id'],isLogin(), $subBrand['brand_id']); ?>
																			<li><a href="#<?php echo $brand['brand_slug'].'-'.$subBrand['brand_slug'].'-'.$category['category_name'] ?>"> <span class="liTitle"><?php echo ucfirst($category['category_name']);  ?> (<?php echo count($orders); ?>) </span><span class="pull-right">Rs <?php echo getOrderCategorySum($category['category_id'],null,null,null,$subBrand['brand_id'])?></span><span class="pull-right"> <?php echo getOrderCategorySum($category['category_id'],'qty',null,null,$subBrand['brand_id']).' pcs |&nbsp;  '?></span></a>
																				<ul>
																					<?php $countOrders = 0; ?>
																					<?php foreach ($orders as $key => $order): ?>
																						<?php $countOrders++; ?>
																						<?php $product = getCatelog($order->product_id)[0] ?>
																						<?php $disabled = ($order->order_confirm_ind)? ' disabled= "TRUE" ' : ''; ?>
																						<li><?php echo ($order->order_confirm_ind)? '<i class="fa fa-check-circle fa-success fa-lg"></i> ' : ''; ?><a href="#order_id<?php echo $order->order_id ?>"><b>Order Id:</b> <?php echo $order->order_id ?> - 
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
																											<div class="col-sm-12 col-xs-12">
																												<?php if (isset($product->product_size1) && $product->product_size1 != null): ?>
																												<div class="col-sm-1 col-xs-2 size_label">
																													<span>&nbsp&nbsp&nbsp<?php echo $product->product_size1 ?></span>
																													<span><input type="text" class="sizeQtyGrid digits" id="order<?php echo $order->order_id ?>_size1" data-order-id="<?php echo $order->order_id ?>" name="product_size_type[product_size1]" value="<?php echo $order->order_size1 ?>" <?php echo $disabled; ?> /></span>
																												</div>
																											<?php endif; ?>
																											<?php if (isset($product->product_size2) && $product->product_size2 != null): ?>
																												<div class="col-sm-1 col-xs-2 size_label">
																													<span>&nbsp&nbsp&nbsp<?php echo $product->product_size2 ?></span>
																													<span><input type="text" class="sizeQtyGrid digits" id="order<?php echo $order->order_id ?>_size2" data-order-id="<?php echo $order->order_id ?>" name="product_size_type[product_size2]" value="<?php echo $order->order_size2 ?>" <?php echo $disabled; ?> /></span>
																																																																																																																																																																																								</div>
																											<?php endif; ?>
																											<?php if (isset($product->product_size3) && $product->product_size3 != null): ?>
																												<div class="col-sm-1 col-xs-2 size_label">
																													<span>&nbsp&nbsp&nbsp&nbsp<?php echo $product->product_size3 ?></span>
																													<span><input type="text" class="sizeQtyGrid digits" id="order<?php echo $order->order_id ?>_size3" data-order-id="<?php echo $order->order_id ?>" name="product_size_type[product_size3]" value="<?php echo $order->order_size3 ?>" <?php echo $disabled; ?> /></span>
																												</div>
																											<?php endif; ?>
																											<?php if (isset($product->product_size4) && $product->product_size4 != null): ?>
																												<div class="col-sm-1 col-xs-2 size_label">
																													<span>&nbsp&nbsp<?php echo $product->product_size4 ?></span>
																													<span><input type="text" class="sizeQtyGrid digits" id="order<?php echo $order->order_id ?>_size4" data-order-id="<?php echo $order->order_id ?>" name="product_size_type[product_size4]" value="<?php echo $order->order_size4 ?>" <?php echo $disabled; ?> /></span>
																												</div>
																											<?php endif; ?>
																											<?php if (isset($product->product_size5) && $product->product_size5 != null): ?>
																												<div class="col-sm-1 col-xs-2 size_label">
																													<span>&nbsp&nbsp<?php echo $product->product_size5 ?></span>
																													<span><input type="text" class="sizeQtyGrid digits" id="order<?php echo $order->order_id ?>_size5" data-order-id="<?php echo $order->order_id ?>" name="product_size_type[product_size5]" value="<?php echo $order->order_size5 ?>" <?php echo $disabled; ?> /></span>
																												</div>
																											<?php endif; ?>
																											<?php if (isset($product->product_size6) && $product->product_size6 != null): ?>
																												<div class="col-sm-1 col-xs-2 size_label">
																													<span>&nbsp&nbsp<?php echo $product->product_size6 ?></span>
																													<span><input type="text" class="sizeQtyGrid digits" id="order<?php echo $order->order_id ?>_size6" data-order-id="<?php echo $order->order_id ?>" name="product_size_type[product_size6]" value="<?php echo $order->order_size6 ?>" <?php echo $disabled; ?> /></span>
																												</div>
																											<?php endif; ?>
																											<?php if (isset($product->product_size7) && $product->product_size7 != null): ?>
																												<div class="col-sm-1 col-xs-2 size_label">
																													<span>&nbsp&nbsp<?php echo $product->product_size7 ?></span>
																													<span><input type="text" class="sizeQtyGrid digits" id="order<?php echo $order->order_id ?>_size7" data-order-id="<?php echo $order->order_id ?>" name="product_size_type[product_size7]" value="<?php echo $order->order_size7 ?>" <?php echo $disabled; ?> /></span>
																												</div>
																											<?php endif; ?>
																											<?php if (isset($product->product_size8) && $product->product_size8 != null): ?>
																												<div class="col-sm-1 col-xs-2 size_label">
																													<span>&nbsp&nbsp<?php echo $product->product_size8 ?></span>
																													<span><input type="text" class="sizeQtyGrid digits" id="order<?php echo $order->order_id ?>_size8" data-order-id="<?php echo $order->order_id ?>" name="product_size_type[product_size8]" value="<?php echo $order->order_size8 ?>" <?php echo $disabled; ?> /></span>
																												</div>
																											<?php endif; ?>
																											</div>
																											<?php if (!$order->total_qty): ?>
																											<div class="row usingRatio"><input type="checkbox" class="checkbox selectByRatioClass" data-order-id="<?php echo $order->order_id ?>" name="selectByRatioButtonId" id="selectByRatioButtonId<?php echo $order->order_id ?>"> Order by Ratio</div>
																											<script>
																												jQuery(document).ready(function($) {
																													jQuery('.selectByRatioClass').click(function(event) {
																														formOrderId = jQuery(this).attr('data-order-id');
																														if(jQuery('#selectByRatioButtonId'+formOrderId).is(":checked"))
																														{
																															jQuery('#total_qty'+formOrderId).val('0').trigger('change').attr("readonly", false);
																															jQuery('#order'+formOrderId+'_size1').val(<?php echo $product->category_size1 ?>);
																															jQuery('#order'+formOrderId+'_size2').val(<?php echo $product->category_size2 ?>);
																															jQuery('#order'+formOrderId+'_size3').val(<?php echo $product->category_size3 ?>);
																															jQuery('#order'+formOrderId+'_size4').val(<?php echo $product->category_size4 ?>);
																															jQuery('#order'+formOrderId+'_size5').val(<?php echo $product->category_size5 ?>);
																															jQuery('#order'+formOrderId+'_size6').val(<?php echo $product->category_size6 ?>);
																															jQuery('#order'+formOrderId+'_size7').val(<?php echo $product->category_size7 ?>);
																															jQuery('#order'+formOrderId+'_size8').val(<?php echo $product->category_size8 ?>);
																														}
																														else{
																															jQuery('#total_qty'+formOrderId).attr("readonly", true);
																														}
																													});
																												});
																											</script>
																											<!-- <div class="row"><button type="button" class=" btn btn-default selectByRatioButton" data-order-id="<?php echo $order->order_id ?>" id="selectByRatioButtonId<?php echo $order->order_id ?>"> select Ratio</button></div> -->
																										<?php endif; ?>
																										</div>
																										</div>
																										<div class="col-sm-2"><span><?php echo $product->product_name ?></span></div>
																										<div class="col-sm-2"><span>Rs <div id="product_price<?php echo $order->order_id ?>"><?php echo $product->product_price ?></div></span></div>
																										<div class="col-sm-1"><span> <input type="text" class="total_qty_class" name="total_qty" data-order-id="<?php echo $order->order_id ?>" id="total_qty<?php echo $order->order_id ?>" size="4" value="<?php echo $order->total_qty ?>" <?php echo $disabled; ?> readonly > Pieces</span></div>	
																									</div>
																									<div class="reviewButtons">
																											<div>
																											<?php if (!($order->order_confirm_ind)): ?>
																												<input type="hidden" name="total_price" id="total_price<?php echo $order->order_id ?>" value="<?php echo $order->total_price ?>">
																												<input type="hidden" name="order_id" value="<?php echo $order->order_id ?>">
																												<!-- <div class="confirmOrder"><input type="checkbox" class="checkbox" data-order-id="<?php echo $order->order_id ?>" name="confirmOrderStatus" value="1" id="confirmOrder<?php echo $order->order_id ?>"> Confirm Order</div> -->
																												<input type="submit" name="saveOrder" class="saveOrder btn btn-default get" id="saveOrderId<?php echo $order->order_id; ?>" data-order-id="<?php echo $order->order_id; ?>" class="btn" value="Save" onclick="saveReviewForm(<?php echo $order->order_id; ?>)" <?php echo $disabled; ?>>
																												<input type="button" class="deleteOrder btn btn-default get" id="cancelOrderId<?php echo $order->order_id; ?>" data-order-id="<?php echo $order->order_id; ?>" class="btn" value="Remove" <?php echo $disabled; ?>>
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
									<hr>
									<div class="finalTotal">All Total: <span class ="pull-right"> Rs <?php echo getOrderSum() ?></span><span class ="pull-right"> <?php echo getOrderSum(null, 'qty')." pcs |&nbsp;  " ?></span></div>
									<div class="confirmAllWrap"><input type="button" name="confirmOrder" class="saveOrder btn btn-default get" id="confirmOrders" class="btn" value="Confirm All" onclick="confirmDealerOrders(<?php echo isLogin() ?>)"></div>
								<?php else: ?>
								<p>Sorry! there are no orders have been made.</p>
								<?php endif ?>
								</div>
							<?php endif; ?>
								<?php if ($dist = isDistributor()): ?>
								<div id="review_order_tab1">
								<?php $count = 0; ?>
								<?php $countUsers = 0; ?>
									<?php $dealers = getDealersByDistributor(isLogin()); ?>
									<?php $dealers = array_merge($dist, $dealers) ?>
									<?php foreach ($dealers as $key => $dealer): ?>
										<?php $ordered_ids = getOrderedIdByUser($dealer->user_id); ?>
											<?php $isAllOrdersConfrimed = isAllOrdersConfrimed($dealer->user_id); ?>
										<?php if (count($ordered_ids)): ?>
										<?php $count++; ?>
										<?php $countUsers++; ?>
									<?php $ordered_ids['brand_id'] = array_values($ordered_ids['brand_id']) ?>
									<?php $ordlerLevels = get_nested(); ?>
									<ul class="accordion">
									<li><a class="confirrmLink" href="javascript:void()" <?php echo ($dealer->user_id == isLogin()) ? "onclick='confirmDistributorOrders(".$dealer->user_id.")'" : "onclick='confirmDealerOrders(".$dealer->user_id.")'" ?> ><i class="fa fa-check-circle <?php echo !($isAllOrdersConfrimed)? 'fa-success' : 'fa-danger' ?>  fa-lg "></i></a> <a href="#<?php echo $dealer->user_name ?>"><?php echo ($dealer->user_id == isLogin()) ? "SELF: ". $dealer->user_fullname : "DEALER: ". $dealer->user_fullname; ?><span class ="pull-right"> Rs <?php echo getOrderSum($dealer->user_id) ?></span><span class ="pull-right"> <?php echo getOrderSum($dealer->user_id, 'qty')." pcs |&nbsp;  " ?></span></a>
									<ul>
									<?php $countBrand = 0; ?>
									<?php foreach ($ordlerLevels as $key => $brand): ?>
										<?php if (in_array($brand['brand_id'], $ordered_ids['brand_id'])): ?>
											<?php $countBrand++; ?>
											<li><a href="#<?php echo $brand['brand_slug'] ?>"> <span class="liTitle"><?php echo ucfirst($brand['brand_name']);  ?></span><span class ="pull-right"> Rs <?php echo getOrderBrandsSum($dealer->user_id,$brand['brand_id'],TRUE) ?></span><span class ="pull-right"><?php echo getOrderBrandsSum($dealer->user_id,$brand['brand_id'],TRUE,'qty').' pcs |&nbsp;  ' ?> </span></a>
												<?php if (isset($brand['children']) && count($brand['children'])): ?>
													<ul>
													<?php $countSubbrand= 0; ?>
													<?php foreach ($brand['children'] as $key => $subBrand): ?>
														<?php if (in_array($subBrand['brand_id'], $ordered_ids['brand_id'])): ?>
															<?php $countSubbrand++; ?>
															<li><a href="#<?php echo $brand['brand_slug'].$subBrand['brand_slug'] ?>"> <span class="liTitle"><?php echo ucfirst($subBrand['brand_name']);  ?></span> <span class ="pull-right"> Rs <?php echo getOrderBrandsSum($dealer->user_id,$subBrand['brand_id']) ?></span><span class ="pull-right"><?php echo getOrderBrandsSum($dealer->user_id,$subBrand['brand_id'],FALSE,'qty').' pcs |&nbsp;' ?> </span></a>
															<?php if (isset($subBrand['category']) && count($subBrand['category'])): ?>
																	<ul>
																	<?php $countCategory = 0; ?>
																	<?php foreach ($subBrand['category'] as $key => $category): ?>
																		<?php //echo $category['category_id']; ?>
																		
																		<?php if (in_array($category['category_id'], $ordered_ids['category_id']) && matchOrderBrandCategoryUser($subBrand['brand_id'],$category['category_id'],$dealer->user_id )): ?>
																			<?php // echo $category['category_id'] ?>
																			<?php $countCategory++; ?>
																			<?php $orders = getOrderByCategory($category['category_id'], $dealer->user_id, $subBrand['brand_id']); ?>
																			<li><a href="#<?php echo $brand['brand_slug'].'-'.$subBrand['brand_slug'].'-'.$category['category_name'] ?>"> <span class="liTitle"><?php echo ucfirst($category['category_name']);  ?> (<?php echo count($orders); ?>) </span><span class="pull-right">Rs <?php echo getOrderCategorySum($category['category_id'],'', $dealer->user_id,null,$subBrand['brand_id'])?></span><span class="pull-right"> <?php echo getOrderCategorySum($category['category_id'],'qty',$dealer->user_id,null,$subBrand['brand_id']).' pcs |&nbsp;'?></span></a>
																				<ul>
																				<?php $countOrders = 0; ?>
																					<?php foreach ($orders as $key => $order): ?>
																						<?php $countOrders++; ?>
																						<?php $product = getCatelog($order->product_id)[0] ?>
																						<?php $disabled = ($order->order_confirm_ind)? ' disabled= "TRUE" ' : ''; ?>
																						<li><?php echo ($order->order_confirm_ind)? '<i class="fa fa-check-circle fa-success fa-lg"></i> ' : ''; ?><a href="#order_id<?php echo $order->order_id ?>"><b>Order Id:</b> <?php echo $order->order_id ?> - 
																								<b>Product Code:</b> <?php echo $product->product_code  ?> 
																								<?php echo ($order->product_grade != '') ? ' - <b>Grade:</b>'.$order->product_grade : ''  ?>
																								<?php echo ($product->product_color != '') ? ' - <b>Color:</b>'.$product->product_color : ''  ?>
																								<span class="pull-right"> Rs <span id="total_price_span<?php echo $order->order_id; ?>"><?php echo $order->total_price ?></span></span></a>
																							<div class="row singleReviewOrder" > 
																								<div class="review_order_box">
																								<form action="" name="reviewOrderForm" id="reviewOrderForm<?php echo $order->order_id ?>" class="reviewOrderFormClass" <?php echo $disabled; ?> >
																									<div class="row">
																										<div class="col-sm-2"><span class=""><img class="img img-thumbnail img-responsive" src="<?php echo $product->image_url?>" alt=""> </span></div>
																										<div class="col-sm-5">
																										<div class="row" id="sizeByRatioInp<?php echo $order->order_id ?>">
																											<div class="col-sm-12 col-xs-12">
																												<?php if (isset($product->product_size1) && $product->product_size1 != null): ?>
																												<div class="col-sm-2 col-xs-2 size_label">
																													<span><?php echo $product->product_size1 ?></span>
																													<span><input type="text" class="sizeQtyGrid " id="order<?php echo $order->order_id ?>_size1" data-order-id="<?php echo $order->order_id ?>" name="product_size_type[product_size1]" value="<?php echo $order->order_size1 ?>" <?php echo $disabled; ?> /></span>
																												</div>
																											<?php endif; ?>
																											<?php if (isset($product->product_size2) && $product->product_size2 != null): ?>
																												<div class="col-sm-2 col-xs-2 size_label">
																													<span>&nbsp&nbsp&nbsp<?php echo $product->product_size2 ?></span>
																													<span><input type="text" class="sizeQtyGrid " id="order<?php echo $order->order_id ?>_size2" data-order-id="<?php echo $order->order_id ?>" name="product_size_type[product_size2]" value="<?php echo $order->order_size2 ?>" <?php echo $disabled; ?> /></span>
																												</div>
																											<?php endif; ?>
																											<?php if (isset($product->product_size3) && $product->product_size3 != null): ?>
																												<div class="col-sm-2 col-xs-2 size_label">
																													<span>&nbsp&nbsp&nbsp&nbsp<?php echo $product->product_size3 ?></span>
																													<span><input type="text" class="sizeQtyGrid " id="order<?php echo $order->order_id ?>_size3" data-order-id="<?php echo $order->order_id ?>" name="product_size_type[product_size3]" value="<?php echo $order->order_size3 ?>" <?php echo $disabled; ?> /></span>
																												</div>
																											<?php endif; ?>
																											<?php if (isset($product->product_size4) && $product->product_size4 != null): ?>
																												<div class="col-sm-1 col-xs-2 size_label">
																													<span>&nbsp&nbsp<?php echo $product->product_size4 ?></span>
																													<span><input type="text" class="sizeQtyGrid " id="order<?php echo $order->order_id ?>_size4" data-order-id="<?php echo $order->order_id ?>" name="product_size_type[product_size4]" value="<?php echo $order->order_size4 ?>" <?php echo $disabled; ?> /></span>
																												</div>
																											<?php endif; ?>
																											<?php if (isset($product->product_size5) && $product->product_size5 != null): ?>
																												<div class="col-sm-2 col-xs-2 size_label">
																													<span>&nbsp&nbsp<?php echo $product->product_size5 ?></span>
																													<span><input type="text" class="sizeQtyGrid " id="order<?php echo $order->order_id ?>_size5" data-order-id="<?php echo $order->order_id ?>" name="product_size_type[product_size5]" value="<?php echo $order->order_size5 ?>" <?php echo $disabled; ?> /></span>
																												</div>
																											<?php endif; ?>
																											<?php if (isset($product->product_size6) && $product->product_size6 != null): ?>
																												<div class="col-sm-2 col-xs-2 size_label">
																													<span>&nbsp&nbsp<?php echo $product->product_size6 ?></span>
																													<span><input type="text" class="sizeQtyGrid " id="order<?php echo $order->order_id ?>_size6" data-order-id="<?php echo $order->order_id ?>" name="product_size_type[product_size6]" value="<?php echo $order->order_size6 ?>" <?php echo $disabled; ?> /></span>
																												</div>
																											<?php endif; ?>
																											<?php if (isset($product->product_size7) && $product->product_size7 != null): ?>
																												<div class="col-sm-2 col-xs-2 size_label">
																													<span>&nbsp&nbsp<?php echo $product->product_size7 ?></span>
																													<span><input type="text" class="sizeQtyGrid " id="order<?php echo $order->order_id ?>_size7" data-order-id="<?php echo $order->order_id ?>" name="product_size_type[product_size7]" value="<?php echo $order->order_size7 ?>" <?php echo $disabled; ?> /></span>
																												</div>
																											<?php endif; ?>
																											<?php if (isset($product->product_size8) && $product->product_size8 != null): ?>
																												<div class="col-sm-2 col-xs-2 size_label">
																													<span>&nbsp&nbsp<?php echo $product->product_size8 ?></span>
																													<span><input type="text" class="sizeQtyGrid " id="order<?php echo $order->order_id ?>_size8" data-order-id="<?php echo $order->order_id ?>" name="product_size_type[product_size8]" value="<?php echo $order->order_size8 ?>" <?php echo $disabled; ?> /></span>
																												</div>
																											<?php endif; ?>
																											</div>
																											<?php if (!$order->total_qty): ?>
																											<div class="row usingRatio"><input type="checkbox" class="checkbox selectByRatioClass" data-order-id="<?php echo $order->order_id ?>" name="selectByRatioButtonId" id="selectByRatioButtonId<?php echo $order->order_id ?>"> Order by Ratio</div>
																											<script>
																												jQuery(document).ready(function($) {
																													jQuery('.selectByRatioClass').click(function(event) {
																														formOrderId = jQuery(this).attr('data-order-id');
																														if(jQuery('#selectByRatioButtonId'+formOrderId).is(":checked"))
																														{
																															jQuery('#total_qty'+formOrderId).val('0').trigger('change').attr("readonly", false);
																															jQuery('#order'+formOrderId+'_size1').val(<?php echo $product->category_size1 ?>);
																															jQuery('#order'+formOrderId+'_size2').val(<?php echo $product->category_size2 ?>);
																															jQuery('#order'+formOrderId+'_size3').val(<?php echo $product->category_size3 ?>);
																															jQuery('#order'+formOrderId+'_size4').val(<?php echo $product->category_size4 ?>);
																															jQuery('#order'+formOrderId+'_size5').val(<?php echo $product->category_size5 ?>);
																															jQuery('#order'+formOrderId+'_size6').val(<?php echo $product->category_size6 ?>);
																															jQuery('#order'+formOrderId+'_size7').val(<?php echo $product->category_size7 ?>);
																															jQuery('#order'+formOrderId+'_size8').val(<?php echo $product->category_size8 ?>);
																														}
																														else{
																															jQuery('#total_qty'+formOrderId).attr("readonly", true);
																														}
																													});
																												});
																											</script>
																											<!-- <div class="row"><button type="button" class=" btn btn-default selectByRatioButton" data-order-id="<?php echo $order->order_id ?>" id="selectByRatioButtonId<?php echo $order->order_id ?>"> select Ratio</button></div> -->
																										<?php endif; ?>
																										</div>
																										
																										</div>
																										<div class="col-sm-2">
                                                      
                                                      <span><?php echo $product->product_name ?></span></div>
																										<div class="col-sm-2"><span>Rs <div id="product_price<?php echo $order->order_id ?>"><?php echo $product->product_price ?></div></span></div>
																										<div class="col-sm-1"><span> <input type="text" class="numOnly total_qty_class" name="total_qty" data-order-id="<?php echo $order->order_id ?>" id="total_qty<?php echo $order->order_id ?>" size="4" value="<?php echo $order->total_qty ?>" <?php echo $disabled; ?> readonly> Pieces</span></div>	
																											<div class="reviewButtons">

																											<?php if (!($order->order_confirm_ind)): ?>
																												<input type="hidden" name="total_price" id="total_price<?php echo $order->order_id ?>" value="<?php echo $order->total_price ?>">
																												<input type="hidden" name="order_id" value="<?php echo $order->order_id ?>">
																												<!-- <div class="confirmOrder"><input type="checkbox" class="checkbox" data-order-id="<?php echo $order->order_id ?>" name="confirmOrderStatus" value="1" id="confirmOrder<?php echo $order->order_id ?>"> Confirm Order</div> -->
																												<input type="submit" name="saveOrder" class="saveOrder btn btn-default get" id="saveOrderId<?php echo $order->order_id; ?>" data-order-id="<?php echo $order->order_id; ?>" class="btn" value="Save" onclick="saveReviewForm(<?php echo $order->order_id; ?>)" <?php echo $disabled; ?>>
																												<input type="button" class="deleteOrder btn btn-default get" id="cancelOrderId<?php echo $order->order_id; ?>" data-order-id="<?php echo $order->order_id; ?>" class="btn" value="Remove" <?php echo $disabled; ?>>
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
									<hr>
									<?php if ($count == 0): ?>
										<p>Sorry! there are no orders have been made.</p>
									<?php else: ?>	
									<?php $alltotal = getOrderSum(null,null,TRUE,isLogin()); $allpieces = getOrderSum(null,'qty',TRUE,isLogin()); ?>
									<div class="finalTotal">All Total: <span class ="pull-right"> Rs <?php echo ($alltotal)? $alltotal : '0';  ?></span><span class ="pull-right"> <?php echo ($allpieces)? $allpieces." pcs |&nbsp;  "  : '0' ." pcs |&nbsp;  " ?></span></div>
									<?php endif ?>
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

<?php if (isDealer()): ?>

	<div class="popupFrom">
	<form action="" class="form-horizontal" role = "form" id="massDelete" class="massDelete">
<?php /*<!-- 	<div class="form-group">
		<lable class="control-label col-xs-3">Brand</lable>
		 <div class="col-xs-9">
		<select name="massDel[brand]" class="" id="">
		<option value="">Null</option>
	<?php $o_ids = $ordered_ids; ?>
	<?php if (count($o_ids)): ?>
		<?php $o_ids['brand_id'] = array_values($o_ids['brand_id']) ?>
		<?php $ordlerLevels = get_nested(); ?>
		<?php foreach ($ordlerLevels as $key => $brand): ?>
			<?php if (in_array($brand['brand_id'], $o_ids['brand_id'])): ?>
				<option value="<?php echo $brand['brand_id'] ?>"><?php echo $brand['brand_name'] ?></option>
			<?php endif; ?>
		<?php endforeach; ?>
	<?php endif; ?>

			</select>
		</div>
	</div>
	<div class="form-group">
		<lable class="control-label col-xs-3">Sub Brand</lable>
		 <div class="col-xs-9">
		<select name="massDel[subbrand]" class="" id="">
		<option value="">Null</option>

	<?php $isAllOrdersConfrimed = isAllOrdersConfrimed($dealer->user_id); ?>
	<?php if (count($ordered_ids)): ?>
		<?php $ordered_ids['brand_id'] = array_values($ordered_ids['brand_id']) ?>
		<?php $ordlerLevels = get_nested(); ?>
		<?php foreach ($ordlerLevels as $key => $brand): ?>
			<?php if (in_array($brand['brand_id'], $ordered_ids['brand_id'])): ?>
				<?php if (isset($brand['children']) && count($brand['children'])): ?>
					<?php foreach ($brand['children'] as $key => $subBrand): ?>
						<?php if (in_array($subBrand['brand_id'], $ordered_ids['brand_id'])): ?>
							<option value="<?php echo $subBrand['brand_id'] ?>"><?php echo $subBrand['brand_name'] ?></option>
						<?php endif; ?>
					<?php endforeach; ?>
				<?php endif; ?>
			<?php endif; ?>
		<?php endforeach; ?>
	<?php endif; ?>

		</select>
		</div>
	</div>
	<div class="form-group">
		<lable class="control-label col-xs-3">Category</lable>
		 <div class="col-xs-9">
		<select name="massDel[category]" class="" id="">
		<option value="">Null</option>
	<?php if (count($ordered_ids)): ?>
		<?php $ordered_ids['brand_id'] = array_values($ordered_ids['brand_id']) ?>
		<?php $ordlerLevels = get_nested(); ?>
		<?php foreach ($ordlerLevels as $key => $brand): ?>
			<?php if (in_array($brand['brand_id'], $ordered_ids['brand_id'])): ?>
				<?php if (isset($brand['children']) && count($brand['children'])): ?>
					<?php foreach ($brand['children'] as $key => $subBrand): ?>
						<?php if (in_array($subBrand['brand_id'], $ordered_ids['brand_id'])): ?>
							<?php foreach ($subBrand['category'] as $key => $category): ?>
								<?php if (in_array($category['category_id'], $ordered_ids['category_id']) && matchOrderBrandCategoryUser($subBrand['brand_id'],$category['category_id'],$dealer->user_id )): ?>
									<option value="<?php echo $category['category_id'] ?>"><?php echo $category['category_name'] ?></option>
								<?php endif; ?>
							<?php endforeach; ?>
						<?php endif; ?>
					<?php endforeach; ?>
				<?php endif; ?>
			<?php endif; ?>
		<?php endforeach; ?>
	<?php endif; ?>

		</select>
		</div>
		</div> -->
		*/ ?>
	<div class="form-group">
		<lable class="control-label col-xs-3">Grade</lable>
		 <div class="col-xs-9">
		<select name="massDel[grade]" class="" id="">
			<option value="">Null</option>
			<option value="A">A</option>
			<option value="B">B</option>
			<option value="C">C</option>
			<option value="D">D</option>
		</select>
		</div>
	</div>

	<div class="text-center">
		<a href="#" class="btn btn-default get" onclick="cancelDel()">Cancel</a>
		<input type="submit" name="action" class="btn btn-default get" value="Delete Selected" id="">
	</div>
		
	</form>
</div>
<?php /***************else************************/ ?>
<?php elseif (isDistributor()): ?>
<div class="popupFrom">
	<form action="" class="form-horizontal" role = "form" id="massDelete" class="massDelete">
<?php /*<!-- 	<div class="form-group">
		<lable class="control-label col-xs-3">User</lable>
		 <div class="col-xs-9">
		<select name="massDel[user]" class="" id="">
		<option value="">Null</option>
		<?php foreach ($dealers as $key => $dealer): ?>
			<?php //if ($dealer->user_id == isLogin()): ?>
				<option value="<?php echo $dealer->user_id ?>"> <?php echo $dealer->user_fullname ?></option>	
			<?php //endif ?>
		<?php endforeach; ?>
		</select>
		</div>
	</div>
	<div class="form-group">
		<lable class="control-label col-xs-3">Brand</lable>
		 <div class="col-xs-9">
		<select name="massDel[brand]" class="" id="">
		<option value="">Null</option>
<?php foreach ($dealers as $key => $dealer): ?>
	<?php $o_ids = getOrderedIdByUser($dealer->user_id); ?>
	<?php //$isAllOrdersConfrimed = isAllOrdersConfrimed($dealer->user_id); ?>
	<?php if (count($o_ids)): ?>
		<?php $o_ids['brand_id'] = array_values($o_ids['brand_id']) ?>
		<?php $ordlerLevels = get_nested(); ?>
		<?php foreach ($ordlerLevels as $key => $brand): ?>
			<?php if (in_array($brand['brand_id'], $o_ids['brand_id'])): ?>
				<option value="<?php echo $brand['brand_id'] ?>"><?php echo $brand['brand_name'] ?></option>
			<?php endif; ?>
		<?php endforeach; ?>
	<?php endif; ?>
<?php endforeach; ?>

			</select>
		</div>
	</div>
	<div class="form-group">
		<lable class="control-label col-xs-3">Sub Brand</lable>
		 <div class="col-xs-9">
		<select name="massDel[subbrand]" class="" id="">
		<option value="">Null</option>
<?php foreach ($dealers as $key => $dealer): ?>
	<?php $ordered_ids = getOrderedIdByUser($dealer->user_id); ?>
	<?php $isAllOrdersConfrimed = isAllOrdersConfrimed($dealer->user_id); ?>
	<?php if (count($ordered_ids)): ?>
		<?php $ordered_ids['brand_id'] = array_values($ordered_ids['brand_id']) ?>
		<?php $ordlerLevels = get_nested(); ?>
		<?php foreach ($ordlerLevels as $key => $brand): ?>
			<?php if (in_array($brand['brand_id'], $ordered_ids['brand_id'])): ?>
				<?php if (isset($brand['children']) && count($brand['children'])): ?>
					<?php foreach ($brand['children'] as $key => $subBrand): ?>
						<?php if (in_array($subBrand['brand_id'], $ordered_ids['brand_id'])): ?>
							<option value="<?php echo $subBrand['brand_id'] ?>"><?php echo $subBrand['brand_name'] ?></option>
						<?php endif; ?>
					<?php endforeach; ?>
				<?php endif; ?>
			<?php endif; ?>
		<?php endforeach; ?>
	<?php endif; ?>
<?php endforeach; ?>

		</select>
		</div>
	</div>
	<div class="form-group">
		<lable class="control-label col-xs-3">Category</lable>
		 <div class="col-xs-9">
		<select name="massDel[category]" class="" id="">
		<option value="">Null</option>
<?php foreach ($dealers as $key => $dealer): ?>
	<?php $ordered_ids = getOrderedIdByUser($dealer->user_id); ?>
	<?php $isAllOrdersConfrimed = isAllOrdersConfrimed($dealer->user_id); ?>
	<?php if (count($ordered_ids)): ?>
		<?php $ordered_ids['brand_id'] = array_values($ordered_ids['brand_id']) ?>
		<?php $ordlerLevels = get_nested(); ?>
		<?php foreach ($ordlerLevels as $key => $brand): ?>
			<?php if (in_array($brand['brand_id'], $ordered_ids['brand_id'])): ?>
				<?php if (isset($brand['children']) && count($brand['children'])): ?>
					<?php foreach ($brand['children'] as $key => $subBrand): ?>
						<?php if (in_array($subBrand['brand_id'], $ordered_ids['brand_id'])): ?>
							<?php foreach ($subBrand['category'] as $key => $category): ?>
								<?php if (in_array($category['category_id'], $ordered_ids['category_id']) && matchOrderBrandCategoryUser($subBrand['brand_id'],$category['category_id'],$dealer->user_id )): ?>
									<option value="<?php echo $category['category_id'] ?>"><?php echo $category['category_name'] ?></option>
								<?php endif; ?>
							<?php endforeach; ?>
						<?php endif; ?>
					<?php endforeach; ?>
				<?php endif; ?>
			<?php endif; ?>
		<?php endforeach; ?>
	<?php endif; ?>
<?php endforeach; ?>

		</select>
		</div>
		</div> -->
*/ ?>
	<div class="form-group">
		<lable class="control-label col-xs-3">Grade</lable>
		 <div class="col-xs-9">
		<select name="massDel[grade]" class="" id="">
			<option value="">Null</option>
			<option value="A">A</option>
			<option value="B">B</option>
			<option value="C">C</option>
			<option value="D">D</option>
		</select>
		</div>
	</div>

	<div class="text-center">
		<a href="#" class="btn btn-default get" onclick="cancelDel()">Cancel</a>
		<input type="submit" name="action" class="btn btn-default get" value="Delete Selected" id="">
	</div>
		
	</form>
</div>

<?php endif ?>


	<script type="text/javascript">
				jQuery(document).ready(function($) {
					jQuery('.sizeQtyGrid').change(function(event) {
							formOrderId = jQuery(this).attr('data-order-id');

							var sum = 0;
							jQuery('#reviewOrderForm'+formOrderId+' .sizeQtyGrid').each(function(){
								if(this.value != '')
								{
									sum += parseInt(this.value);
								}
							    
							});
							if(jQuery('#selectByRatioButtonId'+formOrderId).length)
							{
								if(jQuery('#selectByRatioButtonId'+formOrderId).is(":not(:checked)"))
								{
									jQuery('#total_qty'+formOrderId).val(sum).trigger('change');;
								}
								else{
									jQuery('#total_qty'+formOrderId).val(0).trigger('change');
								}
							}
							else{
								jQuery('#total_qty'+formOrderId).val(sum).trigger('change');;
							}
					});
					jQuery('.selectByRatioClass').click(function(event) {
						formOrderId = jQuery(this).attr('data-order-id');
						if(jQuery('#selectByRatioButtonId'+formOrderId).is(":checked"))
						{
							jQuery('#total_qty'+formOrderId).val('0').trigger('change').attr("readonly", false);
						}
						else{
							jQuery('#total_qty'+formOrderId).attr("readonly", true);
						}
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
			                formData.append("action",'save_review_order');
			                jQuery.ajax({
			                        type: 'POST',
			                        url: ajaxurl,
			                        data: formData,
			                        cache: false,
			                        contentType: false,
			                        processData: false,
			                         beforeSend:function (argument) {
			                           jQuery('.overlay').fadeIn('fast'); 
			                           jQuery('.response').fadeIn('fast').html("Processing...");
			                        },
			                        success: function(result) {
			                           jQuery('.response').html(result).delay(3000).fadeOut('slow');
			                           jQuery('.overlay').delay(3100).fadeOut('slow');
			                           //if (window.location.hash.length > 0) window.location = window.location.hash.substring(1);
			                           location.reload(true);
			                        }
			                    });
			             }
			        	});
					});

					jQuery('#massDelete').submit(function(event) {
						event.preventDefault();
						formId = jQuery(this).attr('id');
						jQuery('#'+formId).validate({
			            submitHandler: function(form) {
			                var formData = new FormData(jQuery('#'+formId)[0]);
			                formData.append("action",'del_mass_order');
			                jQuery.ajax({
			                        type: 'POST',
			                        url: ajaxurl,
			                        data: formData,
			                        cache: false,
			                        contentType: false,
			                        processData: false,
			                         beforeSend:function (argument) {
			                           jQuery('.overlay').fadeIn('fast'); 
			                           jQuery('.response').fadeIn('fast').html("Processing...");
			                        },
			                        success: function(result) {
			                           jQuery('.response').html(result).delay(3000).fadeOut('slow');
			                           jQuery('.overlay').delay(3100).fadeOut('slow');
			                           cancelDel();
			                           //if (window.location.hash.length > 0) window.location = window.location.hash.substring(1);
			                           location.reload(true);
			                        }
			                    });
			             }
			        	});
					});

					jQuery('.deleteOrder').click(function(event) {
						formOrderId = jQuery(this).attr('data-order-id');
						jQuery('.overlay').fadeIn('fast'); 
						jQuery(".response").fadeIn('fast').html('<h3>Are you sure you want to delete? </h3> <p class="text-center" > <a href="javascript:void(0)" class="btn btn-default get" onclick="confrimDelete('+formOrderId+')">Yes</a> <a href="javascript:void(0)" class="btn btn-default get" onclick="cancel()">No</a></p>');

					});

					jQuery('ul.accordion').accordion();
					jQuery(function() {
						jQuery( "#reviewOrderTabs" ).tabs();
					});

					
				});
function saveReviewForm (argument) {
	jQuery('.reviewOrderFormClass').submit();
}
function calcRatio (id) {
	// body...
			var sum = 0;
			ttqty = jQuery('#ttqty').val();
			if(ttqty)
			{	
				//alert(ttqty);
				jQuery('.response .sizeQtyGrid').each(function(){
		    		sum += parseInt(this.value);
				});
			}
			each1 = parseInt(ttqty) / parseInt(sum);
			//alert(each1);
			jQuery('.response .sizeQtyGrid').each(function(){
			//		jQuery('.sizeQtyGrid input[data-order-id='+id+']').
					val = parseInt(jQuery(this).val()) * parseInt(each1);
					jQuery(this).val(val);
				});
			jQuery('#selectRatioForm').trigger('click');
			jQuery('#selectRatioForm').click(function(){
				jQuery('#selectRatioForm').submit();
			});
			
}
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
                   jQuery('.overlay').fadeIn('fast'); 
                   jQuery('.response').fadeIn('fast').html("Processing...");
                },
                success: function(result) {
                   jQuery('.response').html(result).delay(3000).fadeOut('slow');
                   jQuery('.overlay').delay(3100).fadeOut('slow');
                   // if (window.location.hash.length > 0) window.location = window.location.hash.substring(1);
                   location.reload(true);
                }
            });
}
function confirmDealerOrders(id) {
	jQuery.ajax({
                type: 'POST',
                url: ajaxurl,
                data: {'action': 'confrim_dealer_orders', 'user_id': id},
                cache: false,
                 beforeSend:function (argument) {
                   jQuery('.overlay').fadeIn('fast'); 
                   jQuery('.response').fadeIn('fast').html("Processing...");
                },
                success: function(result) {
                   jQuery('.response').html(result).delay(3000).fadeOut('slow');
                   jQuery('.overlay').delay(3100).fadeOut('slow');
                   // if (window.location.hash.length > 0) window.location = window.location.hash.substring(1);
                   location.reload(true);
                }
            });
}
function confirmDistributorOrders(id) {
	jQuery.ajax({
                type: 'POST',
                url: ajaxurl,
                data: {'action': 'confrim_distributor_orders', 'user_id': id},
                cache: false,
                 beforeSend:function (argument) {
                   jQuery('.overlay').fadeIn('fast'); 
                   jQuery('.response').fadeIn('fast').html("Processing...");
                },
                success: function(result) {
                   jQuery('.response').html(result).delay(3000).fadeOut('slow');
                   jQuery('.overlay').delay(3100).fadeOut('slow');
                   // if (window.location.hash.length > 0) window.location = window.location.hash.substring(1);
                   location.reload(true);
                }
            });
}
function massDelform (argument) {
	jQuery('.popupFrom').slideDown('slow');
}
function cancelDel (argument) {
	jQuery('.popupFrom').slideUp('slow');
}
			</script>

<?php get_footer();