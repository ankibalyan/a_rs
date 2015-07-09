<?php defined('ABSPATH') or die("No script kiddies please!"); ?>
	<footer id="footer"><!--Footer-->
		
		<div class="footer-bottom">
			<div class="container">
				<div class="row">
					<p class="pull-left">Copyright © 2015 Retail Insights. All rights reserved.</p>
					<p class="pull-right">Designed by <span><a target="_blank" href="http://www.theretailinsights.com">RetailInsights</a></span></p>
				</div>
			</div>
		</div>
	</footer><!--/Footer-->
	<div class="overlay"></div>
    <div class="response" id="response"></div>
	<?php wp_footer(); ?>
</body>
</html>
<script>
jQuery(document).ready(function($) {
  var inFormOrLink;
jQuery('a').live('click', function() { inFormOrLink = true; });
jQuery('form').bind('submit', function() { inFormOrLink = true; });

jQuery(window).bind('beforeunload', function(eventObject) {
    var returnValue = undefined;
    if (! inFormOrLink) {
        returnValue = "Do you really want to close?";
    }

    eventObject.returnValue = returnValue;
    return returnValue;
}); 


});
var ajaxurl = "<?php echo admin_url('admin-ajax.php'); ?>";
function logout() {
    jQuery.ajax({
            type: 'POST',
            url: ajaxurl,
            data: {'action':'rw_logout'},
            cache: false,
             beforeSend:function (argument) {
              jQuery('.overlay').fadeIn('slow'); 
            },
            success: function(result) {
              location.reload(true);
            }
        });
   	}
</script>
<?php 
  if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > 1800)) {
      // last request was more than 30 minutes ago
      userLogout();
  }
  $_SESSION['LAST_ACTIVITY'] = time(); // update last activity time stamp
?>