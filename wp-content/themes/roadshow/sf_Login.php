<?php defined('ABSPATH') or die("No script kiddies please!"); ?>
<?php

/*

Template Name: Sf Login

*/

ob_start();
if ( !isLogin() ): // Display WordPress login form:
get_header();
?>
<script src="http://pupunzi.com/mb.components/mb.YTPlayer/demo/inc/jquery.mb.YTPlayer.js"></script>
<script>
jQuery(document).ready(function() {
  jQuery(".player").mb_YTPlayer();
});

</script>

<!--Video Section-->
<section class="content-section video-section">
  <div class="pattern-overlay">
  <a id="bgndVideo" class="player" data-property="{videoURL:'https://www.youtube.com/watch?v=VZA4V0CmeOM',containment:'.video-section', quality:'large', autoPlay:true, mute:true, opacity:1}">bg</a>
    <div class="container">
      <div class="row">
        <div class="col-lg-12">
        <section id="form"><!--form-->
            <div class="container">
                <div class="row">
                    <div class="col-sm-4 col-sm-offset-4">
                        <div class="login-form"><!--login form-->
                            <div class="loginWrap">
                                <h2>Login to your account</h2>
                                <form action="#" method="POST" id="rwLoginForm" name="rwLoginForm">
                                    <input type="text" name="user_id" class="text required" placeholder="User Id" />
                                    <input type="password" name="user_password" class="text required" placeholder="Password" />
                                    <p class="loginRespose"></p>
                                    <!-- <span>
                                        <input type="checkbox" class="checkbox"> 
                                        Keep me signed in
                                    </span> -->
                                    <button type="submit" class="btn btn-default">Login</button>
                                    <?php echo ((isset($_GET['login']) && trim($_GET['login']) == 'failed')) ? "<p class='login-error text-center'>Please Enter Valid Credentials!!!</p>" : ''; ?>
                                </form>
                            </div>
                        </div><!--/login form-->
                    </div>
                </div>
            </div>
        </section><!--/form-->
       </div>
      </div>
    </div>
  </div>
</section>
<!--Video Section Ends Here-->
    <!-- <video id="video_background" preload="auto" autoplay="true" loop="loop" muted="muted" volume="0">
        <source src="video/bubbles.webm" type="video/webm">
        <source src="video/bubbles.mp4" type="video/mp4">
        Video not supported
    </video> -->
    <script>
    jQuery(document).ready(function($) {
        jQuery('#rwLoginForm').validate({
                    submitHandler: function(form) {
                        var formData = new FormData(jQuery('#rwLoginForm')[0]);
                        formData.append("action",'rw_login');
                        jQuery.ajax({
                                type: 'POST',
                                url: ajaxurl,
                                data: formData,
                                cache: false,
                                contentType: false,
                                processData: false,
                                 beforeSend:function (argument) {
                                   jQuery('.loginRespose').fadeIn('fast').html("Validating...");
                                },
                                success: function(result) {
                                   jQuery('.loginRespose').html(result).delay(3000).fadeOut('fast');
                                   location.reload(true);
                                }
                            });
                     }
                    });
    });
    </script>
<?php get_footer(); ?>
<?php else: ?>

<?php 
    $message = "ln";

    $url = home_url("/?sfi=$message");

    //$dashboardPage = get_page_by_title('Brand','', 'page' );
    
    wp_redirect(site_url());

?>
<?php endif; ?>
<?php ob_flush(); ?>