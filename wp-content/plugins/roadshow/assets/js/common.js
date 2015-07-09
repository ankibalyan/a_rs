

jQuery(document).ready(function ($) {
    //Tooltip script
    jQuery('[data-toggle="tooltip"]').tooltip();

    jQuery('.selectpicker').selectpicker({
                    style: 'btn-default',
                    size: 4
                  });
    jQuery('.help-box').popover();
    jQuery.extend( jQuery.fn.dataTable.defaults, {
        "searching": true,
        "pageLength": 20,
        "lengthChange" : false,
        "autoWidth": true,
        "info": false
    } );
    jQuery('#displayUsers').DataTable();
    jQuery('#displayBrand').DataTable();
    jQuery('#displayCategory').DataTable();
    jQuery.fn.bootstrapSwitch.defaults.size = 'mini';
    jQuery.fn.bootstrapSwitch.defaults.onText = 'Active';
    jQuery.fn.bootstrapSwitch.defaults.offText = 'Inctive';
    jQuery.fn.bootstrapSwitch.defaults.onColor = 'success';
    
    jQuery(".bootstrapSwitch").bootstrapSwitch();
    jQuery('.bootstrapSwitch').on('switchChange.bootstrapSwitch', function(event, state) {
      userid = jQuery(this).attr('data-user-id');
      if(userid)
      {
        data = {action: 'user_actv_state', user_id: userid, status: state}
      }
      else
      {
        data = {action: 'user_actv_state', status: state} 
        jQuery.fn.bootstrapSwitch.defaults.setState = state;
        jQuery(".bootstrapSwitch").bootstrapSwitch();
      }
      jQuery.post(ajaxurl, data, function(response) {
        console.log(response);
        });
    });
    
    //jQuery("[name='user_status_check']").bootstrapSwitch();
    

    //importer
    // Set blur click function on file input field
    jQuery('#csv_file').blur(function() {
        blur_file_upload_field();  // Function to blur file upload field (gets column count from .csv file)
    });
    
    // *******  Begin WP Media Uploader ******* //
    jQuery('#csv_file_button').click(function() {  // Run WP media uploader
        formfield = jQuery('#csv_file').attr('name');
        tb_show('', 'media-upload.php?type=image&amp;TB_iframe=true');
        return false;
    });

    window.send_to_editor = function(html) {  // Send WP media uploader response
        url = jQuery(html).attr('href');
        jQuery('#csv_file').val(url);
        tb_remove();
        blur_file_upload_field();  // Function to blur file upload field (gets column count from .csv file)
    }
    // *******  End WP Media Uploader ******* //
    
});
function blur_file_upload_field() {
        
        file_upload_url = jQuery('#csv_file').val();
        extension = file_upload_url.substr((file_upload_url.lastIndexOf('.') +1));
        
        // If the file upload does not contain a valid .csv file extension
        if(extension !== 'csv') {
            
            // File extension .csv popup error
            alert("please slect the CSV formated file");
            // jQuery( "#dialog_csv_file" ).dialog({
            //   modal: true,
            //   buttons: {
            //     Ok: function() {
            //       jQuery( this ).dialog( "close" );
            //     }
            //   }
            // });
            jQuery('#return_csv_col_count').text('0');
            return;
        }
        
        // Setup ajax variable
        var data = {
            action: 'wp_csv_to_db_get_csv_cols',
            file_upload_url: file_upload_url
        };
        
        // Run ajax request
        jQuery.post(ajaxurl, data, function(response) {
            //alert(response.column_count);
            jQuery('#return_csv_col_count').text(response.column_count);
            jQuery('#num_cols_csv_file').val(response.column_count);
            jQuery('#num_cols').val(response.column_count);
        });
    }

// ******* Begin 'Select Table' dropdown change function ******* //
    jQuery('#table_select').change(function() {  // Get column count and load table
        
        // Begin ajax loading image
        jQuery('#table_preview').html('<img src="'+wp_csv_to_db_pass_js_vars.ajax_image+'" />');
        
        // Clear 'disable auto_inc' checkbox
        jQuery('#remove_autoinc_column').prop('checked', false);
        
        // Get new table name from dropdown
        sel_val = jQuery('#table_select').val();
        
        // Setup ajax variable
        var data = {
            action: 'wp_csv_to_db_get_columns',
            sel_val: sel_val
            //disable_autoinc: disable_autoinc
        };
        
        // Run ajax request
        jQuery.post(wp_csv_to_db_pass_js_vars.ajaxurl, data, function(response) {
            
            // Populate Table Preview HTML from response
            jQuery('#table_preview').html(response.content);
            
            // Determine if column has an auto_inc value.. and enable/disable the checkbox accordingly
            if(response.enable_auto_inc_option == 'true') {
                jQuery("#remove_autoinc_column").prop('disabled', false);
            }
            if(response.enable_auto_inc_option == 'false') {
                jQuery("#remove_autoinc_column").prop('disabled', true);
            }
            
            
            // Get column count from ajax table and populate hidden div for form submission comparison
            var colCount = 0;
            jQuery('#ajax_table tr:nth-child(1) td').each(function () {  // Array of table td elements
                if (jQuery(this).attr('colspan')) {  // If the td element contains a 'colspan' attribute
                    colCount += +jQuery(this).attr('colspan');  // Count the 'colspan' attributes
                } else {
                    colCount++;  // Else count single columns
                }
            });
            
            // Populate #num_cols hidden input with number of columns
            jQuery('#num_cols').val(colCount);  
        });
    });
    // ******* End 'Select Table' dropdown change function ******* //
    
    
    
    // ******* Begin 'Reload Table Preview' button AND 'Disable auto-increment Column' checkbox click function ******* //
    jQuery('#repop_table_ajax, #remove_autoinc_column').click(function() {  // Reload Table
    
        // Begin ajax loading image
        jQuery('#table_preview').html('<img src="'+wp_csv_to_db_pass_js_vars.ajax_image+'" />');
    
        // Get value of disable auto-increment column checkbox
        if(jQuery('#remove_autoinc_column').is(':checked')){
            disable_autoinc = 'true';
        }else{
            disable_autoinc = 'false';
        }
        // Get new table name from dropdown
        sel_val = jQuery('#table_select').val();
        
        // Setup ajax variable
        var data = {
            action: 'wp_csv_to_db_get_columns',
            sel_val: sel_val,
            disable_autoinc: disable_autoinc
        };
        
        // Run ajax request
        jQuery.post(wp_csv_to_db_pass_js_vars.ajaxurl, data, function(response) {
            
            // Populate Table Preview HTML from response
            jQuery('#table_preview').html(response.content);
            
            // Determine if column has an auto_inc value.. and enable/disable the checkbox accordingly
            if(response.enable_auto_inc_option == 'true') {
                jQuery("#remove_autoinc_column").prop('disabled', false);
            }
            if(response.enable_auto_inc_option == 'false') {
                jQuery("#remove_autoinc_column").prop('disabled', true);
            }
            
            // Get column count from ajax table and populate hidden div for form submission comparison
            var colCount = 0;
            jQuery('#ajax_table tr:nth-child(1) td').each(function () {  // Array of table td elements
                if (jQuery(this).attr('colspan')) {  // If the td element contains a 'colspan' attribute
                    colCount += +jQuery(this).attr('colspan');  // Count the 'colspan' attributes
                } else {
                    colCount++;  // Else count single columns
                }
            });
            
            // Populate #num_cols hidden input with number of columns
            jQuery('#num_cols').val(colCount);
            
            // Re-populate column count value
            remove_auto_col_val = jQuery('#column_count').html('<strong>'+colCount+'</strong>');
        });
    });
    // ******* End 'Reload Table Preview' button AND 'Disable auto-increment Column' checkbox click function ******* //
    
    //
    // Delete DB Table button
    
    // jQuery('#dialog-confirm').dialog({
    //     autoOpen: false,
    //     width: 400,
    //     modal: true,
    //     resizable: false,
    //     buttons: {
    //         'Delete Table': function() {
    //             jQuery('#delete_db_button_hidden').val('true');
    //             jQuery(this).dialog('close');
    //             jQuery('#wp_csv_to_db_form').submit();
    //         },
    //         'Cancel': function() {
    //             jQuery(this).dialog("close");
    //         }
    //     }
    // });
    // jQuery('#delete_db_button').click(function(e) {
    //     if(jQuery('#table_select').val() === '') {
            
    //         // DB table not selected popup error
    //         alert("please slect CSV formated file");
    //         // jQuery( '#dialog_select_db' ).dialog({
    //         //   modal: true,
    //         //   width: 400,
    //         //   buttons: {
    //         //     Ok: function() {
    //         //       jQuery( this ).dialog( 'close' );
    //         //     }
    //         //   }
    //         // });
            
    //         // Reset .csv column count
    //         jQuery('#return_csv_col_count').text('0');
    //         return;
    //     }
    //     else {
    //         jQuery('#dialog-confirm').dialog('open');
    //     }
    // });
// ******* Begin 'Reload Table Preview' button AND 'Disable auto-increment Column' checkbox click function ******* //
    jQuery('#repop_table_ajax, #remove_autoinc_column').click(function() {  // Reload Table
    
        // Begin ajax loading image
        jQuery('#table_preview').html('<img src="'+wp_csv_to_db_pass_js_vars.ajax_image+'" />');
    
        // Get value of disable auto-increment column checkbox
        if(jQuery('#remove_autoinc_column').is(':checked')){
            disable_autoinc = 'true';
        }else{
            disable_autoinc = 'false';
        }
        // Get new table name from dropdown
        sel_val = jQuery('#table_select').val();
        
        // Setup ajax variable
        var data = {
            action: 'wp_csv_to_db_get_columns',
            sel_val: sel_val,
            disable_autoinc: disable_autoinc
        };
        
        // Run ajax request
        jQuery.post(wp_csv_to_db_pass_js_vars.ajaxurl, data, function(response) {
            
            // Populate Table Preview HTML from response
            jQuery('#table_preview').html(response.content);
            
            // Determine if column has an auto_inc value.. and enable/disable the checkbox accordingly
            if(response.enable_auto_inc_option == 'true') {
                jQuery("#remove_autoinc_column").prop('disabled', false);
            }
            if(response.enable_auto_inc_option == 'false') {
                jQuery("#remove_autoinc_column").prop('disabled', true);
            }
            
            // Get column count from ajax table and populate hidden div for form submission comparison
            var colCount = 0;
            jQuery('#ajax_table tr:nth-child(1) td').each(function () {  // Array of table td elements
                if (jQuery(this).attr('colspan')) {  // If the td element contains a 'colspan' attribute
                    colCount += +jQuery(this).attr('colspan');  // Count the 'colspan' attributes
                } else {
                    colCount++;  // Else count single columns
                }
            });
            
            // Populate #num_cols hidden input with number of columns
            jQuery('#num_cols').val(colCount);
            
            // Re-populate column count value
            remove_auto_col_val = jQuery('#column_count').html('<strong>'+colCount+'</strong>');
        });
    });
    // ******* End 'Reload Table Preview' button AND 'Disable auto-increment Column' checkbox click function ******* //