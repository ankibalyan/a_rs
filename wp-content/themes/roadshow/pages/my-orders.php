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
					<header><h3>Confirmed Order Report</h3></header>
					<div class="container">
					<!-- <div class="pull-right"><a href="#" onclick="callExport()">Export to Csv</a></div> -->
					<div class="pull-right"><form action="#" method="POST" > <input type="submit" name="call_export" value="Export to Csv"></form></div>
						<div class="row">
							<div class="col-sm-12">
							<div id="reviewOrderTabs" class="reviewOrder">
								<ul>
								<?php if (isDealer()): ?>
									<li><a href="#review_order_tab1">My Orders </a></li>
									<?php elseif (isDistributor()): ?>
									<li><a href="#review_order_tab2">All Orders</a></li>
								<?php elseif (isBrandUser() || isArvindUser()): ?>
									<li><a href="#review_order_tab3">Brand Report</a></li>
									<li><a href="#review_order_tab4">Distributor Report</a></li>
								<?php elseif (isArvindUser()): ?>
									<!-- <li><a href="#review_order_tab5">All Orders</a></li> -->
								<?php endif; ?>
								</ul>
								<?php if (isDealer()): ?>
								<div id="review_order_tab1">
									<?php $ordered_ids = getOrederedId(null, TRUE); ?>
									<?php if (count($ordered_ids)): ?>
									<?php $ordered_ids['brand_id'] = array_values($ordered_ids['brand_id']) ?>
									<?php $ordlerLevels = get_nested(); ?>
									<ul class="accordion">
									<?php $countBrand = 0 ; ?>
									<?php foreach ($ordlerLevels as $key => $brand): ?>
										<?php if (in_array($brand['brand_id'], $ordered_ids['brand_id'])): ?>
											<?php $countBrand++; ?>
											<li><span><?php echo $countBrand; ?></span><a href="#<?php echo $brand['brand_slug'] ?>"> <span class="liTitle"><?php echo ucfirst($brand['brand_name']);  ?></span><span class ="pull-right"> Rs <?php echo getOrderBrandsSum(isLogin(),$brand['brand_id'],TRUE,NULL,TRUE) ?></span> <span class ="pull-right"><?php echo getOrderBrandsSum(isLogin(),$brand['brand_id'],TRUE,'qty',TRUE).' pcs |&nbsp; ' ?> </span></a>
												<?php if (isset($brand['children']) && count($brand['children'])): ?>
													<ul>
													<?php $countSubbrand = 0; ?>
													<?php foreach ($brand['children'] as $key => $subBrand): ?>
														<?php if (in_array($subBrand['brand_id'], $ordered_ids['brand_id'])): ?>
															<?php $countSubbrand++; ?>
															<li><span><?php echo $countBrand.".".$countSubbrand ?></span><a href="#<?php echo $brand['brand_slug'].$subBrand['brand_slug'] ?>"> <span class="liTitle"><?php echo ucfirst($subBrand['brand_name']);  ?></span> <span class ="pull-right"> Rs <?php echo getOrderBrandsSum(isLogin(),$subBrand['brand_id'],FALSE,NULL,TRUE) ?></span><span class ="pull-right"><?php echo getOrderBrandsSum(isLogin(),$subBrand['brand_id'],FALSE,'qty',TRUE).' pcs |&nbsp; ' ?> </span></a>
															<?php if (isset($subBrand['category']) && count($subBrand['category'])): ?>
																	<ul>
																			<!-- <pre><?php // print_r($subBrand['category']) ?></pre> -->
																		<?php $countCategory = 0; ?>
																	<?php foreach ($subBrand['category'] as $key => $category): ?>
																		<!-- <pre><?php //print_r($ordered_ids['category_id']); ?></pre> -->
																		<?php //echo $category['category_id']; ?>
																		<?php if (in_array($category['category_id'], $ordered_ids['category_id']) && matchOrderBrandCategoryUser($subBrand['brand_id'],$category['category_id'],isLogin())): ?>
																			<?php $countCategory++; ?>
																			<?php  $category['category_id'] ?>
																			<?php $orders = getConfirmOrderByCategory($category['category_id'],isLogin(), $subBrand['brand_id']); ?>
																			<li><span><?php echo $countBrand.".".$countSubbrand.".".$countCategory ?></span><a href="#<?php echo $brand['brand_slug'].'-'.$subBrand['brand_slug'].'-'.$category['category_name'] ?>"> <span class="liTitle"><?php echo ucfirst($category['category_name']);  ?> </span><span class="pull-right">Rs <?php echo getOrderCategorySum($category['category_id'],NULL,NULL,TRUE,$subBrand['brand_id'])?></span><span class="pull-right"> <?php echo getOrderCategorySum($category['category_id'],'qty',NULL,TRUE,$subBrand['brand_id']).' pcs |&nbsp; '?></span></a>
																				<ul>
																					<?php $countOrders = 0; ?>
																					<?php foreach ($orders as $key => $order): ?>
																						<?php $countOrders++; ?>
																						<?php $product = getCatelog($order->product_id)[0] ?>
																						<?php $disabled = ($order->order_confirm_ind)? ' disabled= "TRUE" ' : ''; ?>
																						<li><span><?php echo $countBrand.".".$countSubbrand.".".$countCategory.".".$countOrders ?></span> <?php echo ($order->order_confirm_ind)? '<i class="fa fa-check-circle fa-success fa-lg"></i> ' : ''; ?><a href="#order_id<?php echo $order->order_id ?>"><b>Order Id:</b> <?php echo $order->order_id ?> - 
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
																													<span><input type="text" class="sizeQtyGrid digits" data-order-id="<?php echo $order->order_id ?>" name="product_size_type[product_size1]" value="<?php echo $order->order_size1 ?>" <?php echo $disabled; ?> /></span>
																												</div>
																											<?php endif; ?>
																											<?php if (isset($product->product_size2) && $product->product_size2 != null): ?>
																												<div class="col-sm-1 col-xs-2 size_label">
																													<span>&nbsp&nbsp&nbsp<?php echo $product->product_size2 ?></span>
																													<span><input type="text" class="sizeQtyGrid digits" data-order-id="<?php echo $order->order_id ?>" name="product_size_type[product_size2]" value="<?php echo $order->order_size2 ?>" <?php echo $disabled; ?> /></span>
																												</div>
																											<?php endif; ?>
																											<?php if (isset($product->product_size3) && $product->product_size3 != null): ?>
																												<div class="col-sm-1 col-xs-2 size_label">
																													<span>&nbsp&nbsp&nbsp&nbsp<?php echo $product->product_size3 ?></span>
																													<span><input type="text" class="sizeQtyGrid digits" data-order-id="<?php echo $order->order_id ?>" name="product_size_type[product_size3]" value="<?php echo $order->order_size3 ?>" <?php echo $disabled; ?> /></span>
																												</div>
																											<?php endif; ?>
																											<?php if (isset($product->product_size4) && $product->product_size4 != null): ?>
																												<div class="col-sm-1 col-xs-2 size_label">
																													<span>&nbsp&nbsp<?php echo $product->product_size4 ?></span>
																													<span><input type="text" class="sizeQtyGrid digits" data-order-id="<?php echo $order->order_id ?>" name="product_size_type[product_size4]" value="<?php echo $order->order_size4 ?>" <?php echo $disabled; ?> /></span>
																												</div>
																											<?php endif; ?>
																											<?php if (isset($product->product_size5) && $product->product_size5 != null): ?>
																												<div class="col-sm-1 col-xs-2 size_label">
																													<span>&nbsp&nbsp<?php echo $product->product_size5 ?></span>
																													<span><input type="text" class="sizeQtyGrid digits" data-order-id="<?php echo $order->order_id ?>" name="product_size_type[product_size5]" value="<?php echo $order->order_size5 ?>" <?php echo $disabled; ?> /></span>
																												</div>
																											<?php endif; ?>
																											<?php if (isset($product->product_size6) && $product->product_size6 != null): ?>
																												<div class="col-sm-1 col-xs-2 size_label">
																													<span>&nbsp&nbsp<?php echo $product->product_size6 ?></span>
																													<span><input type="text" class="sizeQtyGrid digits" data-order-id="<?php echo $order->order_id ?>" name="product_size_type[product_size6]" value="<?php echo $order->order_size6 ?>" <?php echo $disabled; ?> /></span>
																												</div>
																											<?php endif; ?>
																											<?php if (isset($product->product_size7) && $product->product_size7 != null): ?>
																												<div class="col-sm-1 col-xs-2 size_label">
																													<span>&nbsp&nbsp<?php echo $product->product_size7 ?></span>
																													<span><input type="text" class="sizeQtyGrid digits" data-order-id="<?php echo $order->order_id ?>" name="product_size_type[product_size7]" value="<?php echo $order->order_size7 ?>" <?php echo $disabled; ?> /></span>
																												</div>
																											<?php endif; ?>
																											<?php if (isset($product->product_size8) && $product->product_size8 != null): ?>
																												<div class="col-sm-1 col-xs-2 size_label">
																													<span>&nbsp&nbsp<?php echo $product->product_size8 ?></span>
																													<span><input type="text" class="sizeQtyGrid digits" data-order-id="<?php echo $order->order_id ?>" name="product_size_type[product_size8]" value="<?php echo $order->order_size8 ?>" <?php echo $disabled; ?> /></span>
																												</div>
																											<?php endif; ?>
																											</div>
																											<?php if (!($order->order_confirm_ind) || !$order->total_qty): ?>
																											<div class="row usingRatio"><input type="checkbox" class="checkbox selectByRatioClass" data-order-id="<?php echo $order->order_id ?>" name="selectByRatioButtonId" id="selectByRatioButtonId<?php echo $order->order_id ?>"> Order by Ratio</div>
																											<!-- <div class="row"><button type="button" class=" btn btn-default selectByRatioButton" data-order-id="<?php echo $order->order_id ?>" id="selectByRatioButtonId<?php echo $order->order_id ?>"> select Ratio</button></div> -->
																										<?php endif; ?>
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
																												<div class="confirmOrder"><input type="checkbox" class="checkbox" data-order-id="<?php echo $order->order_id ?>" name="confirmOrderStatus" value="1" id="confirmOrder<?php echo $order->order_id ?>"> Confirm Order</div>
																												<input type="button" name="saveOrder" class="saveOrder btn btn-default get" id="saveOrderId<?php echo $order->order_id; ?>" data-order-id="<?php echo $order->order_id; ?>" class="btn" value="Save" onclick="saveReviewForm(<?php echo $order->order_id; ?>)" <?php echo $disabled; ?>>
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
									<div class="finalTotal">All Total: <span class ="pull-right"> Rs <?php echo getOrderSum(null, null, TRUE) ?></span><span class ="pull-right"> <?php echo getOrderSum(null, 'qty',TRUE)." pcs |&nbsp; " ?></span></div>
								<?php else: ?>
								<p>Sorry! there are no orders have been made.</p>
								<?php endif ?>
								</div>
								<?php elseif ($dist = isDistributor()): ?>
								<div id="review_order_tab2">
									<?php $count = 0; ?>
									<?php $dealers = getDealersByDistributor(isLogin()); ?>
									<?php $dealers = array_merge($dist, $dealers) ?>
									<?php foreach ($dealers as $key => $dealer): ?>
										<?php $ordered_ids = getOrderedIdByUser($dealer->user_id, TRUE); ?>
											
										<?php if (count($ordered_ids)): ?>
										<?php $count++; ?>
									<?php $ordered_ids['brand_id'] = array_values($ordered_ids['brand_id']) ?>
									<?php $ordlerLevels = get_nested(); ?>
									<ul class="accordion">
									<li><span><?php echo $count ?></span><i class="fa fa-check-circle fa-success fa-lg"></i> <a href="#<?php echo $dealer->user_name ?>"><?php echo ($dealer->user_id == isLogin()) ? "SELF: ". $dealer->user_fullname : "DEALER: ". $dealer->user_fullname; ?><span class ="pull-right"> Rs <?php echo getOrderSum($dealer->user_id,null,TRUE) ?></span><span class ="pull-right"> <?php echo getOrderSum($dealer->user_id, 'qty',TRUE)." pcs |&nbsp; " ?></span></a>
									<ul>
									<?php $countBrand = 0; ?>
									<?php foreach ($ordlerLevels as $key => $brand): ?>
										<?php if (in_array($brand['brand_id'], $ordered_ids['brand_id'])): ?>
											<?php $countBrand++; ?>
											<li><span><?php echo $count.".".$countBrand ?></span><a href="#<?php echo $brand['brand_slug'] ?>"> <span class="liTitle"><?php echo ucfirst($brand['brand_name']);  ?></span><span class ="pull-right"> Rs <?php echo getOrderBrandsSum($dealer->user_id,$brand['brand_id'],TRUE,null,TRUE) ?></span><span class ="pull-right"><?php echo getOrderBrandsSum($dealer->user_id,$brand['brand_id'],TRUE,'qty',TRUE).' pcs |&nbsp; ' ?> </span></a>
												<?php if (isset($brand['children']) && count($brand['children'])): ?>
													<ul>
													<?php $countSubbrand = 0; ?>
													<?php foreach ($brand['children'] as $key => $subBrand): ?>
														<?php if (in_array($subBrand['brand_id'], $ordered_ids['brand_id'])): ?>
															<?php $countSubbrand++; ?>
															<li><span><?php echo $count.".".$countBrand.".".$countSubbrand ?></span><a href="#<?php echo $brand['brand_slug'].$subBrand['brand_slug'] ?>"> <span class="liTitle"><?php echo ucfirst($subBrand['brand_name']);  ?></span> <span class ="pull-right"> Rs <?php echo getOrderBrandsSum($dealer->user_id,$subBrand['brand_id'],FALSE,null,TRUE) ?></span><span class ="pull-right"><?php echo getOrderBrandsSum($dealer->user_id,$subBrand['brand_id'],FALSE,'qty',TRUE).' pcs |&nbsp; ' ?> </span></a>
															<?php if (isset($subBrand['category']) && count($subBrand['category'])): ?>
																	<ul>
																			<!-- <pre><?php // print_r($subBrand['category']) ?></pre> -->
																		<?php $countCategory = 0; ?>
																	<?php foreach ($subBrand['category'] as $key => $category): ?>
																		<!-- <pre><?php //print_r($ordered_ids['category_id']); ?></pre> -->
																		<?php //echo $category['category_id']; ?>
																		
																		<?php if (in_array($category['category_id'], $ordered_ids['category_id']) && matchOrderBrandCategoryUser($subBrand['brand_id'],$category['category_id'],$dealer->user_id )): ?>
																			<?php $countCategory++; ?>
																			<?php // echo $category['category_id'] ?>
																			<?php $orders = getConfirmOrderByCategory($category['category_id'], $dealer->user_id, $subBrand['brand_id']); ?>

																			<li><span><?php echo $count.".".$countBrand.".".$countSubbrand.".".$countCategory ?></span><a href="#<?php echo $brand['brand_slug'].'-'.$subBrand['brand_slug'].'-'.$category['category_name'] ?>"> <span class="liTitle"><?php echo ucfirst($category['category_name']);  ?> </span><span class="pull-right">Rs <?php echo getOrderCategorySum($category['category_id'],'', $dealer->user_id,TRUE,$subBrand['brand_id'])?></span><span class="pull-right"> <?php echo getOrderCategorySum($category['category_id'],'qty',$dealer->user_id,TRUE,$subBrand['brand_id']).' pcs |&nbsp;&nbsp'?></span></a>
																				<ul>
																					<?php $countOrders = 0; ?>
																					<?php foreach ($orders as $key => $order): ?>
																						<?php if (($order->order_confirm_ind)): ?>
																						<?php $countOrders++; ?>
																						<?php $product = getCatelog($order->product_id)[0] ?>
																						<?php $disabled = ($order->order_confirm_ind)? ' disabled= "TRUE" ' : ''; ?>
																						<li><span><?php echo $count.".".$countBrand.".".$countSubbrand.".".$countCategory.".".$countOrders ?></span> <?php echo ($order->order_confirm_ind)? '<i class="fa fa-check-circle fa-success fa-lg"></i> ' : ''; ?><a href="#order_id<?php echo $order->order_id ?>"><b>Order Id:</b> <?php echo $order->order_id ?> - 
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
																											<div class="col-sm-12 col-xs-12" >
																												<?php if (isset($product->product_size1) && $product->product_size1 != null): ?>
																												<div class="col-sm-1 col-xs-2 size_label">
																													<span>&nbsp&nbsp&nbsp<?php echo $product->product_size1 ?></span>
																													<span><input type="text" class="sizeQtyGrid digits" data-order-id="<?php echo $order->order_id ?>" name="product_size_type[product_size1]" value="<?php echo $order->order_size1 ?>" <?php echo $disabled; ?> /></span>
																												</div>
																											<?php endif; ?>
																											<?php if (isset($product->product_size2) && $product->product_size2 != null): ?>
																												<div class="col-sm-1 col-xs-2 size_label">
																													<span>&nbsp&nbsp&nbsp<?php echo $product->product_size2 ?></span>
																													<span><input type="text" class="sizeQtyGrid digits" data-order-id="<?php echo $order->order_id ?>" name="product_size_type[product_size2]" value="<?php echo $order->order_size2 ?>" <?php echo $disabled; ?> /></span>
																												</div>
																											<?php endif; ?>
																											<?php if (isset($product->product_size3) && $product->product_size3 != null): ?>
																												<div class="col-sm-1 col-xs-2 size_label">
																													<span>&nbsp&nbsp&nbsp&nbsp<?php echo $product->product_size3 ?></span>
																													<span><input type="text" class="sizeQtyGrid digits" data-order-id="<?php echo $order->order_id ?>" name="product_size_type[product_size3]" value="<?php echo $order->order_size3 ?>" <?php echo $disabled; ?> /></span>
																												</div>
																											<?php endif; ?>
																											<?php if (isset($product->product_size4) && $product->product_size4 != null): ?>
																												<div class="col-sm-1 col-xs-2 size_label">
																													<span>&nbsp&nbsp<?php echo $product->product_size4 ?></span>
																													<span><input type="text" class="sizeQtyGrid digits" data-order-id="<?php echo $order->order_id ?>" name="product_size_type[product_size4]" value="<?php echo $order->order_size4 ?>" <?php echo $disabled; ?> /></span>
																												</div>
																											<?php endif; ?>
																											<?php if (isset($product->product_size5) && $product->product_size5 != null): ?>
																												<div class="col-sm-1 col-xs-2 size_label">
																													<span>&nbsp&nbsp<?php echo $product->product_size5 ?></span>
																													<span><input type="text" class="sizeQtyGrid digits" data-order-id="<?php echo $order->order_id ?>" name="product_size_type[product_size5]" value="<?php echo $order->order_size5 ?>" <?php echo $disabled; ?> /></span>
																												</div>
																											<?php endif; ?>
																											<?php if (isset($product->product_size6) && $product->product_size6 != null): ?>
																												<div class="col-sm-1 col-xs-2 size_label">
																													<span>&nbsp&nbsp<?php echo $product->product_size6 ?></span>
																													<span><input type="text" class="sizeQtyGrid digits" data-order-id="<?php echo $order->order_id ?>" name="product_size_type[product_size6]" value="<?php echo $order->order_size6 ?>" <?php echo $disabled; ?> /></span>
																												</div>
																											<?php endif; ?>
																											<?php if (isset($product->product_size7) && $product->product_size7 != null): ?>
																												<div class="col-sm-1 col-xs-2 size_label">
																													<span>&nbsp&nbsp<?php echo $product->product_size7 ?></span>
																													<span><input type="text" class="sizeQtyGrid digits" data-order-id="<?php echo $order->order_id ?>" name="product_size_type[product_size7]" value="<?php echo $order->order_size7 ?>" <?php echo $disabled; ?> /></span>
																												</div>
																											<?php endif; ?>
																											<?php if (isset($product->product_size8) && $product->product_size8 != null): ?>
																												<div class="col-sm-1 col-xs-2 size_label">
																													<span>&nbsp&nbsp<?php echo $product->product_size8 ?></span>
																													<span><input type="text" class="sizeQtyGrid digits" data-order-id="<?php echo $order->order_id ?>" name="product_size_type[product_size8]" value="<?php echo $order->order_size8 ?>" <?php echo $disabled; ?> /></span>
																												</div>
																											<?php endif; ?>
																											</div>
																											<?php if (!$order->order_confirm_ind && !$order->total_qty): ?>
																											<div class="row usingRatio"><input type="checkbox" class="checkbox selectByRatioClass" data-order-id="<?php echo $order->order_id ?>" name="selectByRatioButtonId" id="selectByRatioButtonId<?php echo $order->order_id ?>"> Order by Ratio</div>
																											<!-- <div class="row"><button type="button" class=" btn btn-default selectByRatioButton" data-order-id="<?php echo $order->order_id ?>" id="selectByRatioButtonId<?php echo $order->order_id ?>"> select Ratio</button></div> -->
																										<?php endif; ?>
																										</div>
																										
																										</div>
																										<div class="col-sm-2"><span><?php echo $product->product_name ?></span></div>
																										<div class="col-sm-2"><span>Rs <div id="product_price<?php echo $order->order_id ?>"><?php echo $product->product_price ?></div></span></div>
																										<div class="col-sm-1"><span> <input type="text" class="total_qty_class" name="total_qty" data-order-id="<?php echo $order->order_id ?>" id="total_qty<?php echo $order->order_id ?>" size="4" value="<?php echo $order->total_qty ?>" <?php echo $disabled; ?> > Pieces</span></div>	
																											<div class="reviewButtons">

																											<?php if (!($order->order_confirm_ind)): ?>
																												<input type="hidden" name="total_price" id="total_price<?php echo $order->order_id ?>" value="<?php echo $order->total_price ?>">
																												<input type="hidden" name="order_id" value="<?php echo $order->order_id ?>">
																												<div class="confirmOrder"><input type="checkbox" class="checkbox" data-order-id="<?php echo $order->order_id ?>" name="confirmOrderStatus" value="1" id="confirmOrder<?php echo $order->order_id ?>"> Confirm Order</div>
																												<input type="button" name="saveOrder" class="saveOrder btn btn-default get" id="saveOrderId<?php echo $order->order_id; ?>" data-order-id="<?php echo $order->order_id; ?>" class="btn" value="Save" onclick="saveReviewForm(<?php echo $order->order_id; ?>)" <?php echo $disabled; ?>>
																												<input type="button" class="deleteOrder btn btn-default get" id="cancelOrderId<?php echo $order->order_id; ?>" data-order-id="<?php echo $order->order_id; ?>" class="btn" value="Remove" <?php echo $disabled; ?>>
																												<?php endif ?>
																											</div>
																									</div>
																								
																								</form>
																							</div>
																						</div></li>
																						<?php endif; ?>
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
									<div class="finalTotal">All Total: <span class ="pull-right"> Rs <?php echo ($alltotal)? $alltotal : '0';  ?></span><span class ="pull-right"> <?php echo ($allpieces)? $allpieces." pcs |&nbsp; "  : '0' ." pcs |&nbsp; " ?></span></div>
									<?php endif ?>
								</div>

								<?php elseif ($user = isBrandUser() || isArvindUser()): ?>
								<div id="review_order_tab3">
									<?php $count = 0; ?>
										<?php $ordered_ids = getOrederedIdByBrand(null,null,TRUE); ?>
										<?php if (count($ordered_ids)): ?>
										<?php $count++; ?>
									<?php $ordered_ids['brand_id'] = array_values($ordered_ids['brand_id']) ?>
									<?php $ordlerLevels = get_nested(); ?>
									<?php $countBrand = 0; ?>
									<ul class="accordion">
									<?php foreach ($ordlerLevels as $key => $brand): ?>
										<?php if (in_array($brand['brand_id'], $ordered_ids['brand_id'])): ?>
											<?php $countBrand++; ?>
											<li><span><?php echo $countBrand ?></span><a href="#<?php echo $brand['brand_slug'] ?>"> <span class="liTitle"><?php echo ucfirst($brand['brand_name']);  ?></span><span class ="pull-right"> Rs <?php echo getOrderBrandsSum(null,$brand['brand_id'],TRUE,null,TRUE) ?></span><span class ="pull-right"><?php echo getOrderBrandsSum(null,$brand['brand_id'],TRUE,'qty',TRUE).' pcs |&nbsp; ' ?> </span></a>
												<?php if (isset($brand['children']) && count($brand['children'])): ?>
													<ul>
													<?php $countSubbrand = 0; ?>
													<?php foreach ($brand['children'] as $key => $subBrand): ?>
														<?php if (in_array($subBrand['brand_id'], $ordered_ids['brand_id'])): ?>
															<?php $countSubbrand++; ?>
															<li><span><?php echo $countBrand.".".$countSubbrand ?></span><a href="#<?php echo $brand['brand_slug'].$subBrand['brand_slug'] ?>"> <span class="liTitle"><?php echo ucfirst($subBrand['brand_name']);  ?></span> <span class ="pull-right"> Rs <?php echo getOrderBrandsSum(null,$subBrand['brand_id'],FALSE,null,TRUE) ?></span><span class ="pull-right"><?php echo getOrderBrandsSum(null,$subBrand['brand_id'],FALSE,'qty',TRUE).' pcs |&nbsp; ' ?> </span></a>
															<?php if (isset($subBrand['category']) && count($subBrand['category'])): ?>
																	<ul>
																	<?php $countCategory = 0; ?>
																	<?php foreach ($subBrand['category'] as $key => $category): ?>
																		
																		<?php if (in_array($category['category_id'], $ordered_ids['category_id']) && matchOrderBrandCategoryUser($subBrand['brand_id'],$category['category_id'],null )): ?>
																			<?php $countCategory++; ?>
																				<li><span><?php echo $countBrand.".".$countSubbrand.".".$countCategory ?></span><a href="#<?php echo $brand['brand_slug'].'-'.$subBrand['brand_slug'].'-'.$category['category_name'] ?>"> <span class="liTitle"><?php echo ucfirst($category['category_name']);  ?> </span><span class="pull-right">Rs <?php echo getOrderCategorySum($category['category_id'],'', null,TRUE,$subBrand['brand_id'])?></span><span class="pull-right"> <?php echo getOrderCategorySum($category['category_id'],'qty',null,TRUE,$subBrand['brand_id']).' pcs |&nbsp; '?></span></a>
																				<?php $countProducts = 0; ?>
																				<?php foreach ($ordered_ids['product_id'] as $key => $product_id): ?>
																					<?php if (matchProductToBrandCategory($product_id, $subBrand['brand_id'],$category['category_id'])): ?>
																						<?php $countProducts++; ?>
																						<?php $product = getProducts($product_id)[0]; ?>
																						<ul><?php //getOrderProductSum($product_id = NULL, $brand_id = NULL, $category_id = NULL, $qty = NULL, $user_id = NULL, $confirmed = FALSE) ?>
																							<li><span><?php echo $countBrand.".".$countSubbrand.".".$countCategory.".".$countProducts ?></span><a href="#<?php echo $product->product_code ?>"><span class="liTitle"><?php echo $product->product_code ?></span><span class="pull-right">Rs <?php echo getOrderProductSum($product_id,$subBrand['brand_id'],$category['category_id'],'', null,TRUE)?></span><span class="pull-right"> <?php echo getOrderProductSum($product_id,$subBrand['brand_id'],$category['category_id'],'qty',null,TRUE).' pcs |&nbsp; '?></span></a>
																								<?php //getConfirmOrderByProduct($product_id = NULL, $category_id = NULL, $brand_id = NULL, $user_id = NULL) ?>
																								<?php $orders = getConfirmOrderByProduct($product->product_id, $category['category_id'], $subBrand['brand_id'], null); ?>
																								<ul>
																								<?php $countOrders = 0; ?>
																								<?php foreach ($orders as $key => $order): ?>
																									<?php if (($order->order_confirm_ind)): ?>
																										<?php $countOrders++; ?>
																										<?php $product = getCatelog($order->product_id)[0] ?>
																										<li><span><?php echo $countBrand.".".$countSubbrand.".".$countCategory.".".$countProducts.".".$countOrders ?></span> <?php echo ($order->order_confirm_ind)? '<i class="fa fa-check-circle fa-success fa-lg"></i> ' : ''; ?><a href="#order_id<?php echo $order->order_id ?>"><b>Order Id:</b> <?php echo $order->order_id ?> - 
																												<b>Product Code:</b> <?php echo $product->product_code  ?> 
																												<?php echo ($order->product_grade != '') ? ' - <b>Grade:</b>'.$order->product_grade : ''  ?>
																												<?php echo ($product->product_color != '') ? ' - <b>Color:</b>'.$product->product_color : ''  ?>
																												<span class="pull-right"> Rs <span id="total_price_span<?php echo $order->order_id; ?>"><?php echo $order->total_price ?></span></span></a>
																										</li>
																									<?php endif; ?>
																								<?php endforeach ?>
																								</ul>
																							</li>
																						</ul>
																					<?php endif ?>
																				<?php endforeach ?>																				
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
								<?php endif ?>
									<hr>
									<?php if ($count == 0): ?>
										<p>Sorry! there are no orders have been made.</p>
									<?php else: ?>	
									<?php $alltotal = getOrderSum(null,null,TRUE,null); $allpieces = getOrderSum(null,'qty',TRUE,null); ?>
									<div class="finalTotal">All Total: <span class ="pull-right"> Rs <?php echo ($alltotal)? $alltotal : '0';  ?></span><span class ="pull-right"> <?php echo ($allpieces)? $allpieces." pcs |&nbsp; "  : '0' ." pcs |&nbsp; " ?></span></div>

									<?php endif ?>
								</div>
								
			
								<div id="review_order_tab4">
									<?php $count = 0; ?>
									<?php $buyers = get_nested_users(); ?>
									<?php foreach ($buyers as $key => $buyer): ?>
										<?php $buyerIds = getOrderedIdByUser($buyer['user_id'])?>
										<?php if ($buyerIds): ?>
											<?php $ordered_ids = getOrederedIdByBrand(null,$buyer['user_id'],TRUE); ?>
											<ul class="accordion">
											<li>Distributor: <a href="#<?php echo $buyer['user_id'] ?> "><span class="liTitle"><?php echo $buyer['user_fullname'] ?></span><span class ="pull-right"> Rs <?php echo getOrderUsersSum($buyer['user_id'],null,TRUE) ?></span><span class ="pull-right"><?php echo getOrderUsersSum($buyer['user_id'],'qty',TRUE).' pcs |&nbsp; ' ?> </span></a>
											<?php if (count($ordered_ids)): ?>
											<?php $count++; ?>
											<?php $ordered_ids['brand_id'] = array_values($ordered_ids['brand_id']) ?>
											<?php $ordlerLevels = get_nested(); ?>
											<ul>
											<?php $countBrand = 0; ?>
											<?php foreach ($ordlerLevels as $key => $brand): ?>
												<?php $countBrand++; ?>
												<?php if (in_array($brand['brand_id'], $ordered_ids['brand_id'])): ?>
													<li><span><?php echo $countBrand ?></span> <a href="#<?php echo $brand['brand_slug'] ?>"> <span class="liTitle"><?php echo ucfirst($brand['brand_name']);  ?></span>
													<span class ="pull-right"> Rs <?php echo getOrderBrandsSum($buyer['user_id'], $brand['brand_id'],TRUE,null,TRUE) ?></span>
													<span class ="pull-right"><?php echo getOrderBrandsSum($buyer['user_id'],$brand['brand_id'],TRUE,'qty',TRUE).' pcs |&nbsp; ' ?> </span></a>
														<?php if (isset($brand['children']) && count($brand['children'])): ?>
															<ul>
															<?php $countSubbrand = 0; ?>
															<?php foreach ($brand['children'] as $key => $subBrand): ?>
																<?php if (in_array($subBrand['brand_id'], $ordered_ids['brand_id'])): ?>
																	<?php $countSubbrand++; ?>
																	<li><span><?php echo $countBrand.".".$countSubbrand ?></span> <a href="#<?php echo $brand['brand_slug'].$subBrand['brand_slug'] ?>"> <span class="liTitle"><?php echo ucfirst($subBrand['brand_name']);  ?></span> <span class ="pull-right"> Rs <?php echo getOrderBrandsSum($buyer['user_id'],$subBrand['brand_id'],FALSE,null,TRUE) ?></span><span class ="pull-right"><?php echo getOrderBrandsSum($buyer['user_id'],$subBrand['brand_id'],FALSE,'qty',TRUE).' pcs |&nbsp; ' ?> </span></a>
																	<?php if (isset($subBrand['category']) && count($subBrand['category'])): ?>
																			<ul>
																			<?php $countCategory = 0; ?>
																			<?php foreach ($subBrand['category'] as $key => $category): ?>
																				
																				<?php if (in_array($category['category_id'], $ordered_ids['category_id']) && matchOrderBrandCategoryUser($subBrand['brand_id'],$category['category_id'],null )): ?>
																					<?php $countCategory++; ?>
																						<li><span><?php echo $countBrand.".".$countSubbrand.".".$countCategory ?></span>  <a href="#<?php echo $brand['brand_slug'].'-'.$subBrand['brand_slug'].'-'.$category['category_name'] ?>"> <span class="liTitle"><?php echo ucfirst($category['category_name']);  ?> </span><span class="pull-right">Rs <?php echo getOrderCategorySum($category['category_id'],'', $buyer['user_id'],TRUE,$subBrand['brand_id'])?></span><span class="pull-right"> <?php echo getOrderCategorySum($category['category_id'],'qty',$buyer['user_id'],TRUE,$subBrand['brand_id']).' pcs |&nbsp; '?></span></a>
																						<?php $countProducts = 0; ?>
																						<?php foreach ($ordered_ids['product_id'] as $key => $product_id): ?>
																							<?php if (matchProductToBrandCategory($product_id, $subBrand['brand_id'],$category['category_id'])): ?>
																								<?php $countProducts++; ?>
																								<?php $product = getProducts($product_id)[0]; ?>
																								<ul><?php //getOrderProductSum($product_id = NULL, $brand_id = NULL, $category_id = NULL, $qty = NULL, $user_id = NULL, $confirmed = FALSE) ?>
																									<li><span><?php echo $countBrand.".".$countSubbrand.".".$countCategory.".".$countProducts ?></span> <a href="#<?php echo $product->product_code ?>"><span class="liTitle"><?php echo $product->product_code ?></span><span class="pull-right">Rs <?php echo getOrderProductSum($product_id,$subBrand['brand_id'],$category['category_id'],'', $buyer['user_id'],TRUE)?></span><span class="pull-right"> <?php echo getOrderProductSum($product_id,$subBrand['brand_id'],$category['category_id'],'qty',$buyer['user_id'],TRUE).' pcs |&nbsp; '?></span></a>
																										<?php //getConfirmOrderByProduct($product_id = NULL, $category_id = NULL, $brand_id = NULL, $user_id = NULL) ?>
																										<?php $orders = getConfirmOrderByProduct($product->product_id, $category['category_id'], $subBrand['brand_id'], $buyer['user_id']); ?>
																										<ul>
																										<?php $countOrders = 0; ?>
																										<?php foreach ($orders as $key => $order): ?>
																											<?php if (($order->order_confirm_ind)): ?>
																												<?php $countOrders++; ?>
																												<?php $product = getCatelog($order->product_id)[0] ?>
																												<li><span><?php echo $countBrand.".".$countSubbrand.".".$countCategory.".".$countProducts.".".$countOrders ?></span> <?php echo ($order->order_confirm_ind)? '<i class="fa fa-check-circle fa-success fa-lg"></i> ' : ''; ?><a href="#order_id<?php echo $order->order_id ?>"><b>Order Id:</b> <?php echo $order->order_id ?> - 
																														<b>Product Code:</b> <?php echo $product->product_code  ?> 
																														<?php echo ($order->product_grade != '') ? ' - <b>Grade:</b>'.$order->product_grade : ''  ?>
																														<?php echo ($product->product_color != '') ? ' - <b>Color:</b>'.$product->product_color : ''  ?>
																														<span class="pull-right"> Rs <span id="total_price_span<?php echo $order->order_id; ?>"><?php echo $order->total_price ?></span></span></a>
																												</li>
																											<?php endif; ?>
																										<?php endforeach ?>
																										</ul>
																									</li>
																								</ul>
																							<?php endif ?>
																						<?php endforeach ?>																				
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
										<?php endif ?>
											</li>
											</ul>
										<?php endif ?>
										<?php if (isset($buyer) && count($buyer['children'])): ?>
											<?php foreach ($buyer['children'] as $key => $dealer): ?>
												<?php $dealerIds = getOrderedIdByUser($dealer['user_id'])?>
										<?php if ($dealerIds): ?>
											<?php $ordered_ids = getOrederedIdByBrand(null,$dealer['user_id'],TRUE); ?>
											<ul class="accordion">
											<li class="dealerLi">Dealer: <a href="#<?php echo $dealer['user_id'] ?> "><span class="liTitle"><?php echo $dealer['user_fullname'] ?></span><span class ="pull-right"> Rs <?php echo getOrderUsersSum($dealer['user_id'],null,TRUE) ?></span><span class ="pull-right"><?php echo getOrderUsersSum($dealer['user_id'],'qty',TRUE).' pcs |&nbsp; ' ?> </span></a>
											<?php if (count($ordered_ids)): ?>
											<?php $count++; ?>
											<?php $ordered_ids['brand_id'] = array_values($ordered_ids['brand_id']) ?>
											<?php $ordlerLevels = get_nested(); ?>
											<ul>
											<?php $countBrand = 0; ?>
											<?php foreach ($ordlerLevels as $key => $brand): ?>
												<?php if (in_array($brand['brand_id'], $ordered_ids['brand_id'])): ?>
													<?php $countBrand++; ?>
													<li><span><?php echo $countBrand ?></span><a href="#<?php echo $brand['brand_slug'] ?>"> <span class="liTitle"><?php echo ucfirst($brand['brand_name']);  ?></span>
													<span class ="pull-right"> Rs <?php echo getOrderBrandsSum($dealer['user_id'], $brand['brand_id'],TRUE,null,TRUE) ?></span>
													<span class ="pull-right"><?php echo getOrderBrandsSum($dealer['user_id'],$brand['brand_id'],TRUE,'qty',TRUE).' pcs |&nbsp; ' ?> </span></a>
														<?php if (isset($brand['children']) && count($brand['children'])): ?>
															<ul>
															<?php $countSubbrand = 0; ?>
															<?php foreach ($brand['children'] as $key => $subBrand): ?>
																<?php if (in_array($subBrand['brand_id'], $ordered_ids['brand_id'])): ?>
																	<?php $countSubbrand++; ?>
																	<li><span><?php echo $countBrand.".".$countSubbrand ?></span><a href="#<?php echo $brand['brand_slug'].$subBrand['brand_slug'] ?>"> <span class="liTitle"><?php echo ucfirst($subBrand['brand_name']);  ?></span> <span class ="pull-right"> Rs <?php echo getOrderBrandsSum($dealer['user_id'],$subBrand['brand_id'],FALSE,null,TRUE) ?></span><span class ="pull-right"><?php echo getOrderBrandsSum($dealer['user_id'],$subBrand['brand_id'],FALSE,'qty',TRUE).' pcs |&nbsp; ' ?> </span></a>
																	<?php if (isset($subBrand['category']) && count($subBrand['category'])): ?>
																			<ul>
																			<?php $countCategory = 0; ?>
																			<?php foreach ($subBrand['category'] as $key => $category): ?>
																				
																				<?php if (in_array($category['category_id'], $ordered_ids['category_id']) && matchOrderBrandCategoryUser($subBrand['brand_id'],$category['category_id'],null )): ?>
																					<?php $countCategory++; ?>
																					<?php // echo $category['category_id'] ?>
																						<li><span><?php echo $countBrand.".".$countSubbrand.".".$countCategory ?></span> <a href="#<?php echo $brand['brand_slug'].'-'.$subBrand['brand_slug'].'-'.$category['category_name'] ?>"> <span class="liTitle"><?php echo ucfirst($category['category_name']);  ?> </span><span class="pull-right">Rs <?php echo getOrderCategorySum($category['category_id'],'', $dealer['user_id'],TRUE,$subBrand['brand_id'])?></span><span class="pull-right"> <?php echo getOrderCategorySum($category['category_id'],'qty',$dealer['user_id'],TRUE,$subBrand['brand_id']).' pcs |&nbsp; '?></span></a>
																						<?php $countProducts = 0; ?>
																						<?php foreach ($ordered_ids['product_id'] as $key => $product_id): ?>
																							<?php if (matchProductToBrandCategory($product_id, $subBrand['brand_id'],$category['category_id'])): ?>
																								<?php $countProducts++; ?>
																								<?php $product = getProducts($product_id)[0]; ?>
																								<ul><?php //getOrderProductSum($product_id = NULL, $brand_id = NULL, $category_id = NULL, $qty = NULL, $user_id = NULL, $confirmed = FALSE) ?>
																									<li><span><?php echo $countBrand.".".$countSubbrand.".".$countCategory.".".$countProducts ?></span><a href="#<?php echo $product->product_code ?>"><span class="liTitle"><?php echo $product->product_code ?></span><span class="pull-right">Rs <?php echo getOrderProductSum($product_id,$subBrand['brand_id'],$category['category_id'],'', $dealer['user_id'],TRUE)?></span><span class="pull-right"> <?php echo getOrderProductSum($product_id,$subBrand['brand_id'],$category['category_id'],'qty',$dealer['user_id'],TRUE).' pcs |&nbsp; '?></span></a>
																										<?php //getConfirmOrderByProduct($product_id = NULL, $category_id = NULL, $brand_id = NULL, $user_id = NULL) ?>
																										<?php $orders = getConfirmOrderByProduct($product->product_id, $category['category_id'], $subBrand['brand_id'], $dealer['user_id']); ?>
																										<ul>
																										<?php $countOrders = 0; ?>
																										<?php foreach ($orders as $key => $order): ?>
																											<?php if (($order->order_confirm_ind)): ?>
																												<?php $countOrders++; ?>
																												<?php $product = getCatelog($order->product_id)[0] ?>
																												<li><span><?php echo $countBrand.".".$countSubbrand.".".$countCategory.".".$countProducts.".".$countOrders ?></span> <?php echo ($order->order_confirm_ind)? '<i class="fa fa-check-circle fa-success fa-lg"></i> ' : ''; ?><a href="#order_id<?php echo $order->order_id ?>"><b>Order Id:</b> <?php echo $order->order_id ?> - 
																														<b>Product Code:</b> <?php echo $product->product_code  ?> 
																														<?php echo ($order->product_grade != '') ? ' - <b>Grade:</b>'.$order->product_grade : ''  ?>
																														<?php echo ($product->product_color != '') ? ' - <b>Color:</b>'.$product->product_color : ''  ?>
																														<span class="pull-right"> Rs <span id="total_price_span<?php echo $order->order_id; ?>"><?php echo $order->total_price ?></span></span></a>
																												</li>
																											<?php endif; ?>
																										<?php endforeach ?>
																										</ul>
																									</li>
																								</ul>
																							<?php endif ?>
																						<?php endforeach ?>																				
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
										<?php endif ?>
											</li>
											</ul>
										<?php endif ?>
											<?php endforeach ?>
										<?php endif ?>
									<hr>
									<?php if ($count == 0): ?>
										<p>Sorry! there are no orders have been made.</p>
									<?php else: ?>	
									<?php $alltotal = getOrderSum(null,null,TRUE,null); $allpieces = getOrderSum(null,'qty',TRUE,null); ?>
									<div class="finalTotal">All Total: <span class ="pull-right"> Rs <?php echo ($alltotal)? $alltotal : '0';  ?></span><span class ="pull-right"> <?php echo ($allpieces)? $allpieces." pcs |&nbsp; "  : '0' ." pcs |&nbsp; " ?></span></div>

									<?php endif ?>
								<?php endforeach ?>
								</div>
								<?php endif ?>
								<div id="review_order_tab5">
									
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
							jQuery('#total_qty'+formOrderId).val('0').trigger('change');
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
function callExport (argument) {
	jQuery.ajax({
                type: 'POST',
                url: ajaxurl,
                data: {'action': 'call_export', 'call_export': '1'},
                cache: false,
                 beforeSend:function (argument) {
                   jQuery('.overlay').fadeIn('slow'); 
                   jQuery('.response').fadeIn('slow').html("Processing...");
                },
                success: function(result) {
                   jQuery('.response').fadeIn('slow').html(result).delay(3000).fadeOut('slow');
                   jQuery('.overlay').delay(3100).fadeOut('slow');
                   // if (window.location.hash.length > 0) window.location = window.location.hash.substring(1);
                   //document.location.href = '/data.csv;
                }
            });
}
			</script>

<?php get_footer();