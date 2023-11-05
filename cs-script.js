jQuery(document).ready(function(){

    jQuery(".sc_ajax_add_to_cart").click(function(e) {
        e.preventDefault();
        jQuery(this).addClass("loading");

        var n_data = {
			action: 'cs_add_to_cart',
			productid: jQuery(this).attr('data-product_id')
		};

        var ajaxurl = ilaa_ajax_object.ajaxurl;
		jQuery.post(ajaxurl, n_data, function(response) {

            if(response.indexOf("ERROR") != -1){
				alert("Error occured when setting up the delivery. " + response);
			} else {

                var url = woocommerce_params.wc_ajax_url;
                url = url.replace("%%endpoint%%", "get_refreshed_fragments");
                jQuery.post(url, function (data, status) {

                    jQuery(".woocommerce.widget_shopping_cart").html(data.fragments["div.widget_shopping_cart_content"]);
                    if (data.fragments) {
                        jQuery.each(data.fragments, function (key, value) {

                        jQuery(key).replaceWith(value);
                        });
                    }
                    jQuery("body").trigger("wc_fragments_refreshed");
                    jQuery(".sc_ajax_add_to_cart").removeClass("loading");

                    jQuery("#fkcart-modal").addClass("fkcart-show").show();
                });
			}

		});

        return false;
    });

});