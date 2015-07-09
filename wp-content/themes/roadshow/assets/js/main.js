/*price range*/
jQuery('#sl2').slider().on('slide', function(ev){
    setTimeout(function() {categoryFilter();},1250);
	
});
	var RGBChange = function() {
	  jQuery('#RGB').css('background', 'rgb('+r.getValue()+','+g.getValue()+','+b.getValue()+')')
	};	
jQuery('input[type="checkbox"]').checkbox();
/*scroll to top*/

jQuery(document).ready(function(){
	jQuery(function () {
		jQuery.scrollUp({
	        scrollName: 'scrollUp', // Element ID
	        scrollDistance: 300, // Distance from top/bottom before showing element (px)
	        scrollFrom: 'top', // 'top' or 'bottom'
	        scrollSpeed: 300, // Speed back to top (ms)
	        easingType: 'linear', // Scroll to top easing (see http://easings.net/)
	        animation: 'fade', // Fade, slide, none
	        animationSpeed: 200, // Animation in speed (ms)
	        scrollTrigger: false, // Set a custom triggering element. Can be an HTML string or jQuery object
					//scrollTarget: false, // Set a custom target element for scrolling to the top
	        scrollText: '<i class="fa fa-angle-up"></i>', // Text for element, can contain HTML
	        scrollTitle: false, // Set a custom <a> title if required.
	        scrollImg: false, // Set true to use image
	        activeOverlay: false, // Set CSS color to display scrollUp active point, e.g '#00FFFF'
	        zIndex: 2147483647 // Z-Index for the overlay
		});
	});

	jQuery('.logout').click(function(event) {
		logout();
	});
    jQuery('.sizeQtyGrid').focus(function(event) {
        if(this.value == 0 || this.value == '0')
        {
            this.value = null;
        }
    });
  	// Do not allow non-numeric characters in bill zip code
    jQuery('.sizeQtyGrid').keydown(function(e) {
        //console.log(e.keyCode);
        if (e.keyCode != 8 && e.keyCode != 9 && e.keyCode != 37 && e.keyCode != 39 
        	&& e.keyCode != 46 && e.keyCode != 96 && e.keyCode != 97 && e.keyCode != 98 
        	&& e.keyCode != 99 && e.keyCode != 100 && e.keyCode != 101 && e.keyCode != 102
        	&& e.keyCode != 103 && e.keyCode != 104 && e.keyCode != 105) {
              if(e.keyCode ===189||e.keyCode ===109)
                 if (numbersOnly(String.fromCharCode(45), true) != "")
                    return true;
                 else 
                     return false

            if (numbersOnly(String.fromCharCode(e.which), true) != "")
                return true;
            else return false
        }
    });
    jQuery('.numOnly').keydown(function(e) {
        //console.log(e.keyCode);
        if (e.keyCode != 8 && e.keyCode != 9 && e.keyCode != 37 && e.keyCode != 39 
        	&& e.keyCode != 46 && e.keyCode != 96 && e.keyCode != 97 && e.keyCode != 98
        	&& e.keyCode != 99 && e.keyCode != 100 && e.keyCode != 101 && e.keyCode != 102
        	&& e.keyCode != 103 && e.keyCode != 104 && e.keyCode != 105) {
              if(e.keyCode ===189||e.keyCode ===109)
                 if (numbersOnly(String.fromCharCode(45), true) != "")
                    return true;
                 else 
                     return false

            if (numbersOnly(String.fromCharCode(e.which), true) != "")
                return true;
            else return false
        }
    });
     jQuery(".loadMore").click(function(){
            //var total = jQuery(".eachProduct").length;
            var showing = jQuery(".eachProduct:visible").length;
            var hidden = jQuery(".eachProduct:hidden").length;
            if(hidden <= 3)
            {
                jQuery('.loadMore').hide();
            }
            jQuery(".eachProduct").slice(showing - 1, showing + 3).slideDown();

        });
     
		jQuery('.productDetailSelectPicker').selectpicker({
            style: 'btn-default',
            size: 4

        });

        
});


jQuery(document).ready(function(){
    

  var native_width = 0;
  var native_height = 0;
    jQuery(".small").mouseover(function(e){
        dataTrigger = jQuery(this).attr('data-trigger');
        bigImg = jQuery("#replacewith"+dataTrigger).attr("src");
        jQuery(".large").css("background","url('"+bigImg+"') no-repeat");
    });
    // jQuery(".small").mouseout(function(e){
    //     jQuery(".large").css("background","none");
    // });
  //Now the mousemove function
  jQuery(".magnify").mousemove(function(e){
    //When the user hovers on the image, the script will first calculate
    //the native dimensions if they don't exist. Only after the native dimensions
    //are available, the script will show the zoomed version.
    if(!native_width && !native_height)
    {
      //This will create a new image object with the same image as that in .small
      //We cannot directly get the dimensions from .small because of the 
      //width specified to 200px in the html. To get the actual dimensions we have
      //created this image object.
      var image_object = new Image();
      image_object.src = jQuery(".small").attr("src");
      
      //This code is wrapped in the .load function which is important.
      //width and height of the object would return 0 if accessed before 
      //the image gets loaded.
      native_width = image_object.width;
      native_height = image_object.height;
    }
    else
    {
      //x/y coordinates of the mouse
      //This is the position of .magnify with respect to the document.
      var magnify_offset = jQuery(this).offset();
      //We will deduct the positions of .magnify from the mouse positions with
      //respect to the document to get the mouse positions with respect to the 
      //container(.magnify)
      var mx = e.pageX - magnify_offset.left;
      var my = e.pageY - magnify_offset.top;
      
      //Finally the code to fade out the glass if the mouse is outside the container
      if(mx < jQuery(this).width() && my < jQuery(this).height() && mx > 0 && my > 0)
      {
        jQuery(".large").fadeIn(100);
      }
      else
      {
        jQuery(".large").fadeOut(100);
      }
      if(jQuery(".large").is(":visible"))
      {
        //The background position of .large will be changed according to the position
        //of the mouse over the .small image. So we will get the ratio of the pixel
        //under the mouse pointer with respect to the image and use that to position the 
        //large image inside the magnifying glass
        var rx = Math.round(mx/jQuery(".small").width()*native_width - jQuery(".large").width()/2)*-1;
        var ry = Math.round(my/jQuery(".small").height()*native_height - jQuery(".large").height()/2)*-1;
        var bgp = rx + "px " + ry + "px";
        
        //Time to move the magnifying glass with the mouse
        var px = mx - jQuery(".large").width()/2;
        var py = my - jQuery(".large").height()/2;
        //Now the glass moves with the mouse
        //The logic is to deduct half of the glass's width and height from the 
        //mouse coordinates to place it with its center at the mouse coordinates
        
        //If you hover on the image now, you should see the magnifying glass in action
        jQuery(".large").css({left: px, top: py, backgroundPosition: bgp});
      }
    }
  })
})

function numbersOnly(number, allowDash) {
    // Filter non-digits/dash from input value.
    //console.log(number);
    if (allowDash) {
        number = number.replace(/[^0-9\-]/g, '');
    } else {
        // Filter non-digits from input value.
        number = number.replace(/\D/, '');
    }
    return number;
}
function loadMore () {
        jQuery(".eachProduct").hide();
        jQuery(".eachProduct").slice(0, 9).slideDown();
    }