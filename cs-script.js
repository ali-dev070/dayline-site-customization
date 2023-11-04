jQuery(document).ready(function(){

    jQuery("#sc_ajax_add_to_cart").click(function(e) {
        e.preventDefault();

        var n_data = {
			action: 'cs_add_to_cart',
			productid: jQuery(this).attr('data-product_id')
		};

        var ajaxurl = ilaa_ajax_object.ajaxurl;
		jQuery.post(ajaxurl, n_data, function(response) {

            if(response.indexOf("ERROR") != -1){
				alert("Error occured when setting up the delivery. " + response);
			} else {
                location.reload();
			}

		});

        return false;
    });

});