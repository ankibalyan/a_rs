<?php defined('ABSPATH') or die("No script kiddies please!"); ?>
<?php 
setlocale(LC_MONETARY, 'en_US');
/**
 * Add styles and Scripts to theme
 * @return void
 * @author Ankit Balyan - sf.ankit@gmail.com
 **/
function opStyleScritps()
{
	//wp_enqueue_style( $handle, $src, $deps, $ver, $media );
	//wp_enqueue_script( $handle, $src, $deps, $ver, $in_footer );

	//load main css
    wp_enqueue_style('checkboxCss', ASSETS.'/checkbox/css/bootstrap-checkbox.css', array(), null);
    wp_enqueue_style('prettyPhotoCss', ASSETS.'/css/prettyPhoto.css', array(), null);
    wp_enqueue_style('priceRangeCss', ASSETS.'/css/price-range.css', array(), null);
   
    wp_enqueue_style('mainCss', ASSETS.'/css/main.css', array(), null);
    wp_enqueue_style('sliderCss', ASSETS.'/jquery.bxslider/jquery.bxslider.css', array(), null);
    wp_enqueue_style('responsiveCss', ASSETS.'/css/responsive.css', array(), null);
    ?>
<link rel="stylesheet" href="//code.jquery.com/ui/1.11.2/themes/smoothness/jquery-ui.css">
<script src="//code.jquery.com/jquery-1.11.1.js"></script>
<script src="//code.jquery.com/ui/1.11.2/jquery-ui.js"></script>


    <?php
    //wp_enqueue_script( 'jqueryMinJs', ASSETS.'/js/jquery.min.js', array(),'3122014',TRUE);
	//load bootstrap scripts
    
    
	wp_enqueue_script( 'bootstrapDateJs', ASSETS.'/js/bootstrap-datepicker.js', array(),'2122014',TRUE);
    wp_enqueue_script('jquery-ui-tabs');  // For admin panel page tabs
    wp_enqueue_script( 'checkboxJs', ASSETS.'/checkbox/js/bootstrap-checkbox.js', array(),'2122014',TRUE);

    wp_enqueue_script( 'accordionJJs', ASSETS.'/accordion/jquery.accordion.source.js', array(),'2122014',TRUE);
    wp_enqueue_script( 'accordionJJs', ASSETS.'/accordion/jquery.accordion.source.js', array(),'2122014',TRUE);
    wp_enqueue_script( 'jqueryScrollUpJs', ASSETS.'/js/jquery.scrollUp.min.js', array(),'3122014',TRUE);
    wp_enqueue_script( 'jScrollJs', ASSETS.'/jscroll/jquery.jscroll.min.js', array(),'3122014',TRUE);
    
    wp_enqueue_script( 'prettyPhotoJs', ASSETS.'/js/jquery.prettyPhoto.js', array(),'3122014',TRUE);
      wp_enqueue_script( 'elevatezoom', ASSETS.'/js/jquery.elevatezoom.js', array(),'3122014',TRUE);
    
	wp_enqueue_script( 'treeviewJs', ASSETS.'/js/jquery.treeview.js', array(), '20141010',TRUE );
    wp_enqueue_script( 'priceRangeJs', ASSETS.'/js/price-range.js', array(),'3122014',TRUE);
    wp_enqueue_script( 'mainJs', ASSETS.'/js/main.js', array(), '20141010',TRUE);
   wp_enqueue_script( 'sliderJs', ASSETS.'/jquery.bxslider/jquery.bxslider.min.js', array(), '20141010',TRUE);
}

//add_action( $tag, $function_to_add, $priority, $accepted_args );
add_action('wp_enqueue_scripts','opStyleScritps');

show_admin_bar(false);
/**
 * Register Navigation menu
 *
 * @return void
 * @author Ankit Balyan - sf.ankit@gmail.com
 **/
function opTopMenu()
{   
   // include_once (get_template_directory().'/wp-bootstrap-navwalker-master/wp_bootstrap_navwalker.php');
	register_nav_menus( array(
		'primary' => 'Primary Menu on Top for navigation',
		'footer' => 'Footer Menu',
	));
}
//add_action( $tag, $function_to_add, $priority, $accepted_args );
add_action( 'after_setup_theme', 'opTopMenu' );

//add_filter('nav_menu_css_class' , 'special_nav_class' , 10 , 2);
/**
 * filter the navigation menu with active classes
 *
 * @return void
 * @author Ankit Balyan - sf.ankit@gmail.com
 **/
function special_nav_class($classes, $item){
    if( in_array( 'current-menu-item', $classes ) ||
    in_array( 'current-menu-ancestor', $classes ) ||
    in_array( 'current-menu-parent', $classes ) ||
    in_array( 'current_page_parent', $classes ) ||
    in_array( 'current-page-ancestor', $classes )
    ){
        $classes[] = "active";
    }
     return $classes;
}

/**
 * filter the title of current page
 *
 * @return void
 * @author Ankit Balyan - sf.ankit@gmail.com
 **/
function opTitle( $title, $sep ) {
	global $paged, $page;

	if ( is_feed() )
		return $title;

	// Add the site name.
	$title .= get_bloginfo( 'name' );

	// Add the site description for the home/front page.
	$site_description = get_bloginfo( 'description', 'display' );
	if ( $site_description && ( is_home() || is_front_page() ) )
		$title = "$title $sep $site_description";

	return $title;
}
add_filter( 'wp_title', 'opTitle', 10, 2 );

/**
 * filter the logo of admin login page
 *
 * @return void
 * @author Ankit Balyan - sf.ankit@gmail.com
 **/
function my_login_logo() { ?>
    <style type="text/css">
        body.login div#login h1 a {
            background-image: url(<?php echo get_stylesheet_directory_uri(); ?>/images/site-login-logo.png);
            padding-bottom: 30px;
        }
    </style>
<?php }
add_action( 'login_enqueue_scripts', 'my_login_logo' );

/**
 * Register's the sidebar
 *
 * @return void
 * @author Ankit Balyan - sf.ankit@gmail.com
 **/
function left_sidebar() {

    $args = array(
        'id'            => 'left-sidebar',
        'name'          => __( 'Left Sidebar', 'text_domain' ),
        'description'   => __( 'Appears in the Left section of the site.', 'text_domain' ),
        'before_title'  => '<h3 class="widget-title">',
        'after_title'   => '</h3>',
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget'  => '</div>',
    );
    register_sidebar( $args );

}

// Hook into the 'widgets_init' action
add_action( 'widgets_init', 'left_sidebar' );
/**
 * it checks whether the supplied url is exist or not, also check the file is exist or not.
 *
 * @return BOOL
 * @author Ankit Balyan - sf.ankit@gmail.com
 **/
function is_url_exist($url){
    $ch = curl_init($url);    
    curl_setopt($ch, CURLOPT_NOBODY, true);
    curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    if($code == 200){
       $status = true;
    }else{
      $status = false;
    }
    curl_close($ch);
   return $status;
}

/**
 * get allary of all categories under a brand
 *
 *@apram int brand_id of which categories are needed.
 * @return array
 * @author Ankit Balyan - sf.ankit@gmail.com
 **/
function getBrandCats($brand_id= null)
{
    global $wpdb;
    $pages = array();
    if((int)($brand_id))
    {
        $sql = "SELECT 
                    category.*
                FROM
                    rw_brand_category_map AS catmap
                        LEFT OUTER JOIN
                        rw_category AS category ON catmap.category_id = category.category_id
                    where catmap.brand_id = $brand_id";
        $pages = (array) $wpdb->get_results($sql);
        foreach ($pages as $key => $value) {
            $pages[$key] = (array) $value;
        }
    }
    return $pages;
}

/**
 * generates the site menu for brand, sub brand and category.
 *
 * @return void
 * @author Ankit Balyan - sf.ankit@gmail.com
 **/
function get_menu($array, $child = FALSE, $category = FALSE)
    {
        $str = '';      
        if(count($array))
        {
            ?>
<?php
            if($category)
            {
                $str .= '<ul class="dropdown-menu">'. PHP_EOL;
            }
            elseif($child)
            {
                $str .= '<ul class="dropdown-menu multi-level">'. PHP_EOL;   
            }
            else
            {
                $str .= '<ul class="nav navbar-nav">'. PHP_EOL;   
            }
            static $SaveSubBrand ='';
            foreach($array as $item)
            {
                //$active = $CI->uri->segment(1) == $item['slug'] ?TRUE : FALSE;
                
                $active = null;
                if((isset($item['children']  ) && count($item['children'])))
                {   
                        $str .= $active ? '<li class="active">' : '<li>';
                        $str .='<a href="' . site_url(e($item['brand_slug'])) . '"  class="dropdown-toggle" data-toggle="dropdown">';
                        $str .= e($item['brand_name']). ' <b class="caret"></b> </a>'. PHP_EOL;
                        $str .= get_menu($item['children'], TRUE);
                }
                elseif((isset($item['category'] ) && count($item['category'])) || $category)
                {
                    if($category)
                    {
                        $str .= $active ? '<li class="active">' : '<li>';
                        $str .= '<a href="' . site_url(e($SaveSubBrand.'/'.$item['category_name'])) . '" >';
                        $str .= e($item['category_name']). '</a>'. PHP_EOL;
                    }
                    else
                    {
                        $SaveSubBrand = $item['brand_slug'];
                        $str .= $active ? '<li class="active dropdown-submenu">' : '<li class="dropdown-submenu">';
                        $str .= '<a href="' . site_url(e($item['brand_slug'])) . '"  >';
                        $str .= e($item['brand_name']). ' </a>'. PHP_EOL;
                        $str .= get_menu($item['category'], TRUE, TRUE);
                    }
                    
                }
                else
                {
                    $str .= $active ?'<li class="active">' : '<li>';
                    $str .='<a  href="' . site_url(e($item['brand_slug'])) . '">' . e($item['brand_name']). '</a>';                    
                }

                $str .= '</li>' . PHP_EOL;
            }
            $str .= '</ul>' . PHP_EOL;
        }
        return $str;
}

/**
 * generates the array list of brands sub brands and caregories in parent child array format
 *
 * @return array
 * @author Ankit Balyan - sf.ankit@gmail.com
 **/
function get_nested()
    {
        global $wpdb;
        $user_id = isLogin();
        if(is_admin())
        {
            $sql = "SELECT 
                brand.*, category.category_id
            FROM
                rw_brands AS brand
                    LEFT OUTER JOIN
                rw_brand_category_map AS catmap ON brand.brand_id = catmap.brand_id
                    LEFT OUTER JOIN
                rw_category AS category ON catmap.category_id = category.category_id
                group by brand.brand_id";
        }
        elseif(!isArvindUser())
        {
            $sql = "SELECT 
                brand.*, category.category_id
            FROM
                rw_brands AS brand
                    LEFT OUTER JOIN
                rw_brand_category_map AS catmap ON brand.brand_id = catmap.brand_id
                    LEFT OUTER JOIN
                rw_category AS category ON catmap.category_id = category.category_id
                    LEFT OUTER JOIN
                rw_brand_user_map AS user_brand ON user_brand.user_id = $user_id
            where
                brand.brand_id = user_brand.brand_id
            or
                brand.brand_parent_id = user_brand.brand_id
            group by brand.brand_id";
        }
        else
        {
            $sql = "SELECT 
                brand.*, category.category_id
            FROM
                rw_brands AS brand
                    LEFT OUTER JOIN
                rw_brand_category_map AS catmap ON brand.brand_id = catmap.brand_id
                    LEFT OUTER JOIN
                rw_category AS category ON catmap.category_id = category.category_id
                group by brand.brand_id";
        }
        $pages = $wpdb->get_results($sql);
        $array = array();
        foreach($pages as $page)
        {
            $page = (array) $page;
            if(!$page['brand_parent_id'] && !$page['category_id'] )
            {
                $array[$page['brand_id']] = $page;
            }
            elseif(($page['brand_parent_id'] && $page['category_id'] ))
            {
                $page ['category'] = getBrandCats($page['brand_id']);
                $array[$page['brand_parent_id']] ['children'][] = $page;
            }
            else
            {
                $array[$page['brand_parent_id']] ['children'][] = $page;
            }
        }
        
        return $array;
    }

/**
 * generates the brand's filer list
 *
 * @return void
 * @author Ankit Balyan - sf.ankit@gmail.com
 **/
function get_brandFilterList($array, $child = FALSE, $category = FALSE)
    {
        $str = '';
        if(count($array))
        {
            $str .= '<div class="panel panel-default">';
            static $topBrandId = '';
            if($category)
            {
                $str .= ''. PHP_EOL;
            }
            elseif($child)
            {
                    $str .= '<div id="'.$topBrandId.'" class="panel-collapse collapse">';
                    $str .= '<div class="panel-body">';
                    $str .= '<ul>';
            }
            else
            {
                $str .= '';   
            }
            
            foreach($array as $item)
            {
                //$active = $CI->uri->segment(1) == $item['slug'] ?TRUE : FALSE;
                
                $active = null;
                if((isset($item['children']  ) && count($item['children'])))
                {   
                        $topBrandId = "topBrandId_".$item['brand_id'];
                        $str .='<div class="panel-heading">'.PHP_EOL;
                        $str .='<h4 class="panel-title">'.PHP_EOL;
                        $str .= '<a data-toggle="collapse" data-parent="#brandFilterAcorrdian" href="#'.$topBrandId.'">'.PHP_EOL;
                        $str .='<span class="badge pull-right"><i class="fa fa-plus"></i></span>'.PHP_EOL;
                        $str .= $item['brand_name'].'</a>'.PHP_EOL;
                        $str .='</h4>'.PHP_EOL;
                        $str .='</div>'.PHP_EOL;
                        $str .= get_brandFilterList($item['children'], TRUE);
                }
                else
                {
                    $str .= '<li><input 
                                type="checkbox" 
                                name="selecteBrandBox[]" 
                                id="brand_id_'.$item['brand_id'].'"
                                class="large"
                                value="'.$item['brand_id'].'">
                                <a href="">'.$item['brand_name'].'</a></li>';
                }
            }
            if($category)
            {
                $str .= ''. PHP_EOL;
            }
            elseif($child)
            {
                $str .= '</ul>';
                $str .= '</div>';
                $str .= '</div>';
            }
            else
            {
                $str .= ''. PHP_EOL;   
            }
            $str .= '</div>' . PHP_EOL;
        }
        return $str;
}
/**
 * site's mailer function, 
 * it mails to the data array passed to it,
 *
 * @return true / string sting if error comes.
 * @author Ankit Balyan - sf.ankit@gmail.com
 **/
function rwMail($sendmail = array())
{
    include_once ROADSHOW.'php-mailer/class.phpmailer.php';
    $subj = (isset($sendmail['subject'])) ? $sendmail['subject'] : "Arvind Roadshow Admin";
                    
                    $mail = new PHPMailer;
                    $mail->IsSMTP();                            // Set mailer to use SMTP
                    $mail->Host = 'smtp.gmail.com';             // Specify main and backup server
                    $mail->Port = 465;                          // Specify main and backup server
                    $mail->SMTPAuth = true;                     // Enable SMTP authentication
                    $mail->Username = 'demoapp007@gmail.com';   // SMTP username
                    $mail->Password = 'demoapplication';        // SMTP password
                    $mail->SMTPSecure = 'ssl';                  // Enable encryption, 'ssl' also accepted
                    $mail->SMTPDebug = 0;
                    //$mail->From = $email;
                    $mail->FromName = 'Arvind Roadshow';
                    
                    $mail->AddAddress($sendmail['to']);   // Add a recipient
                    //$mail->AddAddress('ellen@example.com');                // Name is optional

                    $mail->AddReplyTo($sendmail['replyto'], $sendmail['replytoname']);
                    $mail->AddCC($sendmail['cc']);
                    $mail->AddBCC($sendmail['bcc']);
                    
                    $mail->WordWrap = 50;                                 // Set word wrap to 50 characters
                    if($sendmail['attachment'])
                    $mail->AddAttachment($sendmail['attachment']);       // Add attachments
                    $mail->IsHTML(true);                                  // Set email format to HTML
                    
                    $mail->Subject =  $subj;
                    $mail->Body    = $sendmail['message'];
                    
                    if(!$mail->Send()) {
                        return $mail->ErrorInfo;
                        exit;
                    }else{
                        return true;
                    }
}

/**
 * convers the plane anchor in to edit button
 *
 * @return string
 * @author Ankit Balyan - sf.ankit@gmail.com
 **/
function btn_edit($uri)
{
    return anchor($uri,'<i class="glyphicon glyphicon-edit"></i>');
}

/**
 * convers the plane anchor in to delete button
 *
 * @return string
 * @author Ankit Balyan - sf.ankit@gmail.com
 **/
function btn_delete($uri)
{
    return anchor($uri,'<i class="glyphicon glyphicon-remove"></i>',array('onclick'=>"return confirm('You are about to delete a record. This can not be undone. Are you sure?');"));
}

/**
 * filter the sting with htmlentities
 *
 * @return sting
 * @author Ankit Balyan - sf.ankit@gmail.com
 **/
function e($string)
{
    return htmlentities($string);
}
//Dum Helper
if(!function_exists('dump'))
{
    /**
    * dumps the array in preformatted style
    *
    * @return void
    * @author Ankit Balyan - sf.ankit@gmail.com
    **/
    function dump($var,$label='dump',$echo=TRUE)
    {
        //Store the dump variable
        ob_start();
        var_dump($var);
        $output = ob_get_clean();
        
        //Add Formating
        $output = preg_replace("/\]\=\>\n(\s+)/m","] => ",$output);
        $output = '<pre style="background:fff; color:000; border: 1px dotted #000; padding:10px; margin 10px 0; text-align:left;">' . $label . '=>' . $output . '</pre>';
        
        //output
        if($echo == TRUE)
        {
            echo $output;
        }
        else
        {
            return $output;
        }
    }
}

if(!function_exists('dump_exit'))
{
    /**
    * dumps the array in preformatted style with exit after dump.
    *
    * @return void
    * @author Ankit Balyan - sf.ankit@gmail.com
    **/
    function dump_exit($var, $label='dump', $echo = TRUE)
    {
        dump($var,$label,$echo);
        exit;
    
    }
}
?>