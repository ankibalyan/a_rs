<?php
$time = microtime();
$time = explode(' ', $time);
$time = $time[1] + $time[0];
$start = $time;
?>
<?php defined('ASSETS') or define('ASSETS', get_template_directory_uri().'/assets' );
if(isset($_SESSION['remember_me_session']) && $_SESSION['remember_me_session'] == '1'){
	$year = time()+31536000;
	setcookie('remember_me', $_SESSION['logged_in_user'], $year, '/');
}

if(isset($_SESSION['remember_me_session']) && $_SESSION['remember_me_session'] == '0'){
	setcookie('remember_me', '', time()-3600, '/');
}

?>
<?php $slug = get_page_uri(null); 
	global $wp;
$current_url = add_query_arg( $wp->query_string, '', home_url( $wp->request ) );
$url = trim($_SERVER["REQUEST_URI"],'/');

$parts = parse_url($url);
$path = explode("/", $parts["path"]);
array_splice($path, 1, 0, array("controller")); //This line does the magic!

?>
<?php
//defined('EVENT') or define('EVENT',site_url().'/user-operations');
global $current_user, $post;
	get_currentuserinfo();
	$slug = get_page_uri(null);
	if(isLogin()):
		// if(is_page() && is_home())
		// 	get_header();
		// else
			//get_header();
		//$path = str_replace(home_url(),'',get_permalink());
		
		if(file_exists(get_template_directory().'/pages/'.$slug.'.php'))
		{
			get_template_part('pages/'.$slug);
		}
		elseif(is_search()){
			get_template_part('search');
		}
		elseif(file_exists(get_template_directory().'/user-operations/'.$slug)){
			include get_template_directory().'/user-operations/'.$slug;
		}
		else
		{
			get_template_part('page-products');
		}
		get_footer();
	else:

    $message = "ln";

    $url = home_url("/?sfi=$message");
    
    wp_redirect(site_url('login'));
	endif;
?>
<?php
$time = microtime();
$time = explode(' ', $time);
$time = $time[1] + $time[0];
$finish = $time;
$total_time = round(($finish - $start), 4);
//echo '<pre>Page generated in '.$total_time.' seconds.</pre>';
?>
