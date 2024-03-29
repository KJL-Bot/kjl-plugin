var busy = false;
var limit = 12;
var offset = 0;
var assetPath = siteConfig.ajaxUrl;
var loadCounter = 0;

function displayRecords(lim, offset) {
    jQuery.ajax({
            type: "GET",
            async: false,
            url: assetPath,
            data: {
                'limit': lim,
                "offset": offset,
                "action": 'kjl_load_morea',
                "kjl_author": jQuery('#author_input').val(),
                "kjl_publisher": jQuery('#publisher_input').val(),
                "kjl_title": jQuery('#title_input').val(),
                "kjl_location": jQuery('#location_input').val(),
                "kjl_date": jQuery('#date_input').val(),
                "sort_direction": jQuery('#sort_direction').val(),
                "djlp_filter": jQuery('#toggleswitch_djlp_input').val(),
                "kimi_filter": jQuery('#toggleswitch_kimi_input').val(),
                "ajax_nonce": siteConfig.ajax_nonce,
                "loadmore_kjl_nonce": siteConfig.loadmore_kjl_nonce
            },
            cache: false,
            beforeSend: function() {
                // jQuery("#loader_message").html("").hide();
                jQuery('#spinner').show();
            },
            success: function(html) {
                jQuery("#books").append(html);
                
                ++loadCounter;
                if (html == "") {
                    jQuery("#spinner").html('');
                    jQuery("#spinner").text('Keine weiteren Bücher.').show()
                } else if (loadCounter >= 1) {
                    jQuery("#load_more").css('display', 'block');
                } else {
                    jQuery("#spinner").show();
                }
                window.busy = false;
                jQuery('#spinner').hide();
            },
            done: function(e) {
                console.log('done');
            },
            fail: function(e) {
                console.log('error: ' + e);
            }
    });
}
(function( $ ) {
	// 'use strict';
    if (busy == false) {
        busy = true;
        // start to load the first set of data
        $("#spinner").show();
        displayRecords(limit, offset);
    }
    $('#load_more').on('click', function (e) {
        offset += parseInt(limit);
        // $(this).remove();
        $("#spinner").show();
        displayRecords(limit, offset);
    });
    // $(window).scroll(function() {
    //     // make sure u give the container id of the data to be loaded in.
    //     if ($(window).scrollTop() + $(window).height() > $("#books").height() && !busy && loadCounter < 1) {
    //       busy = true;
    //       offset = limit + offset;

    //       displayRecords(limit, offset);

    //     }
    // });
})( jQuery );