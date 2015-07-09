<?php defined('ABSPATH') or die("No script kiddies please!"); ?>
<?php defined('ASSETS') or define('ASSETS', get_template_directory_uri().'/assets' );
/*
 * Template Name: Loyalty Card
 * Description: A Page Template with a darker design.
 */

?>
<?php get_header('home'); ?>
<?php global $current_user;
      get_currentuserinfo();
?>
    <div class="content-header-wrap">
        <center><h1> Sorry!!! You Lost Some where!</h1></center>
        <center><h1> Find Some Good Place</h1></center>
    </div>

<?php get_footer(); ?>

