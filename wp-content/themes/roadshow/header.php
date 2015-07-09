<?php defined('ABSPATH') or die("No script kiddies please!"); ?>
<?php global $current_user;
      get_currentuserinfo();
      $parts = explode("/", trim($_SERVER['REQUEST_URI']));
      $partsCount = count($parts);
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?> class="no-js">
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
          <?php if(is_404())  { global $wpseo_front;
        remove_action('wp_head',array($wpseo_front,'head'),1);?>
    <title><?php echo $parts[$partsCount-1]." | ".$parts[$partsCount-2] ?></title>
    <?php }else{?>
    <title><?php wp_title( '|', true, 'right' ); ?></title>
   <?php } ?>
	<link rel="profile" href="http://gmpg.org/xfn/11">
	<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">
    <!--[if lt IE 9]>
    <script src="js/html5shiv.js"></script>
    <script src="js/respond.min.js"></script>
    <![endif]-->
    <link rel="shortcut icon" href="<?php echo ASSETS ?>/images/favicon.png" />
    <link rel="stylesheet" type="text/css" href="<?php echo ASSETS ?>/css/print.css" media="print" />
    <link rel="apple-touch-icon-precomposed" sizes="144x144" href="images/ico/apple-touch-icon-144-precomposed.png">
    <link rel="apple-touch-icon-precomposed" sizes="114x114" href="images/ico/apple-touch-icon-114-precomposed.png">
    <link rel="apple-touch-icon-precomposed" sizes="72x72" href="images/ico/apple-touch-icon-72-precomposed.png">
    <link rel="apple-touch-icon-precomposed" href="images/ico/apple-touch-icon-57-precomposed.png">
	<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
          <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
          <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->
	<script>(function(){document.documentElement.className='js'})();</script>
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php if (is_page('login')): ?>
    <div class="loginBg"></div>
<?php endif ?>
            <!-- <header id="header"> --><!--header--> 
        <!-- <div class="header_top"> --><!--header_top--> 
            <!-- <div class="container">
                <div class="row">
                    <div class="col-sm-6">
                        <div class="contactinfo">
                            <ul class="nav nav-pills">
                                <li><a href="#"><i class="fa fa-phone"></i> +91 7795001231</a></li>
                                <li><a href="#"><i class="fa fa-envelope"></i> info@theretailinsights.com</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div> -->
       <!-- </div> --><!--/header_top-->
        <div class="header-middle f-nav"><!--header-middle-->
            <div class="container">
                <div class="row">
                    <div class="col-xs-12 col-sm-5 col-md-7">
                    <div class="row">
                         <div class="logo col-xs-12 col-sm-12 col-md-3">
                            <a href="<?php echo home_url(); ?>"><img src="<?php echo ASSETS ?>/images/home/Arvind-logo.jpg" alt="logo"/></a>
                        </div>
                        <div class="hidden-xs hidden-sm col-md-8">
                            <a href="<?php echo home_url(); ?>"><img class="img img-responsive" src="<?php echo ASSETS ?>/images/brands/brands_header.png" alt="F U E L"/></a>
                        </div>
                    </div>
                        <!-- <div class="brand-logo hidden-xs hidden-sm pull-left">
                            <a href="<?php echo home_url(); ?>"><img src="<?php echo site_url() ?>/images/brands/FM_logo.png" alt="logo" style="height: 35px;"/></a>
                        </div>
                        <div class="brand-logo hidden-xs hidden-sm pull-left">
                            <a href="<?php echo home_url(); ?>"><img src="<?php echo site_url() ?>/images/brands/USPOLO.png" alt="logo" style="height: 35px;"/></a>
                        </div>
                        <div class="brand-logo hidden-xs hidden-sm pull-left">
                            <a href="<?php echo home_url(); ?>"><img src="<?php echo site_url() ?>/images/brands/EdHArdy.jpg" alt="logo" style="height: 35px;"/></a>
                        </div>
                        <div class="brand-logo hidden-xs hidden-sm last pull-left">
                            <a href="<?php echo home_url(); ?>"><img src="<?php echo site_url() ?>/images/brands/ELLE.gif" alt="logo" style="height: 35px;"/></a>
                        </div> -->
                        <!-- <div class="btn-group pull-right">
                            <div class="btn-group">
                                <button type="button" class="btn btn-default dropdown-toggle usa" data-toggle="dropdown">
                                    USA
                                    <span class="caret"></span>
                                </button>
                                <ul class="dropdown-menu">
                                    <li><a href="#">Canada</a></li>
                                    <li><a href="#">UK</a></li>
                                </ul>
                            </div>
                            
                            <div class="btn-group">
                                <button type="button" class="btn btn-default dropdown-toggle usa" data-toggle="dropdown">
                                    DOLLAR
                                    <span class="caret"></span>
                                </button>
                                <ul class="dropdown-menu">
                                    <li><a href="#">Canadian Dollar</a></li>
                                    <li><a href="#">Pound</a></li>
                                </ul>
                            </div>
                        </div> -->
                    </div>
                    <?php if (isLogin()) : ?>
                    <div class="col-xs-12 col-sm-7 col-md-5">
                        <div class="shop-menu pull-right">
                            <ul class="nav navbar-nav navbar-fixed">
                                <li><a href=""><i class="fa fa-user"></i> <?php echo "Hi! ".getRwUsers(isLogin())->user_fullname ?></a></li>
                                <li><a href="<?php echo site_url('my-orders'); ?>"><i class="fa fa-shopping-cart"></i> My Orders</a></li>
                                <!-- <li><a href="#"><i class="fa fa-star"></i> Wishlist</a></li>
                                <li><a href="checkout.html"><i class="fa fa-crosshairs"></i> Checkout</a></li> -->
                                <?php if(!isArvindUser() && !isBrandUser()): ?>
                                <li><a href="<?php echo site_url('review'); ?>"><i class="fa fa-eye"></i> Review </a></li>
                                <?php endif; ?>
                                <li><a href="javascript:void(0)" class="logout" onclick="logout();"><i class="fa fa-lock"></i> Logout</a></li>
                            </ul>
                        </div>
                    </div>
                <?php endif; ?>
                </div>
            </div>
        </div><!--/header-middle-->

        <?php if (isLogin()) { ?>
            <div class="header-bottom"><!--header-bottom-->
            <div class="container">
                <div class="row">
                    <div class="col-sm-9">
                        <div class="navbar-header">
                            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                                <span class="sr-only">Toggle navigation</span>
                                <span class="icon-bar"></span>
                                <span class="icon-bar"></span>
                                <span class="icon-bar"></span>
                            </button>
                        </div>
                        <div class="">
                        <div class="collapse navbar-collapse">
                            <?php $pages = get_nested(); 

                            $homeBrand = array('brand_name' => 'HOME',
                                                'brand_slug' => '/');
                            array_unshift($pages, $homeBrand);
                            echo get_menu($pages); ?>
                        </div>  
                          <!--/.nav-collapse -->

                        </div>
                    </div>
                </div>
            </div> 
        </div><!--/header-bottom-->
        <?php } ?>  
    </header><!--/header-->