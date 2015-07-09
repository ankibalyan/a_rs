<?php echo $slug = get_page_uri(null); 
	global $wp;
//echo $current_url = add_query_arg( $wp->query_string, '', home_url( $wp->request ) );
//echo "<br>";
$url = rtrim($_SERVER["REQUEST_URI"],'/');
$url = ltrim($_SERVER["REQUEST_URI"],'/');


$parts = parse_url($url);

$path = explode("/", $parts["path"]);
array_splice($path, 1, 0, array("controller")); //This line does the magic!
echo implode("/",$path);