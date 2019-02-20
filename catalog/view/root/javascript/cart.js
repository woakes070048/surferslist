if (typeof submitShippingUrl === 'undefined') {
    var submitShippingUrl = 'cart';
}

if (typeof shippingMethod === 'undefined') {
    var shippingMethod = '';
}

if (typeof textShippingMethod === 'undefined') {
    var textShippingMethod = 'Please select a shipping method to use on this order.';
}

if (typeof buttonShipping === 'undefined') {
    var buttonShipping = 'Apply Shipping';
}

if (typeof textSelect === 'undefined') {
    var textSelect = ' --- Please Select --- ';
}

if (typeof textNone === 'undefined') {
    var textNone = ' --- None --- ';
}

if (typeof zoneId === 'undefined') {
    var zoneId = '';
}

// Cart Page
$('.cart-page').on('change', 'input[name=\'next\']', function() {
	$('.cart-module > div').hide();

	$('#' + this.value).show();
});

$('#shipping').clickOrTouch('#button-quote', function() {
	$.ajax({
		url: 'shipping-quote',
		type: 'post',
		data: 'country_id=' + $('select[name=\'country_id\']').val() + '&zone_id=' + $('select[name=\'zone_id\']').val() + '&postcode=' + encodeURIComponent($('input[name=\'postcode\']').val()),
		dataType: 'json',
		beforeSend: function() {
			$('#button-quote').prop('disabled', true);
			$('#button-quote').after('<span class="wait icon"><i class="fa fa-spin fa-circle-o-notch icon-spin"></i></span>');
		},
		complete: function() {
			$('#button-quote').prop('disabled', false);
		    $('.wait').fadeOut(300).remove();
		},
		success: function(json) {
			$('.success, .warning, .attention, .error').remove();

			if (json['error']) {
				if (json['error']['warning']) {
					$('#notification').html('<div class="bg"><span class="close"><i class="fa fa-times-circle-o"></i></span><div class="widget widget-success widget-warning" style="display: none;"><h6><i class="fa fa-exclamation-triangle"></i></h6><p>' + json['error']['warning'] + '</p></div></div>');

					$('.widget-warning').fadeIn('slow');
				}

				if (json['error']['country']) {
					$('select[name=\'country_id\']').after('<span class="error"><i class="fa fa-exclamation-triangle"></i> ' + json['error']['country'] + '</span>');
				}

				if (json['error']['zone']) {
					$('select[name=\'zone_id\']').after('<span class="error"><i class="fa fa-exclamation-triangle"></i> ' + json['error']['zone'] + '</span>');
				}

				if (json['error']['postcode']) {
					$('input[name=\'postcode\']').after('<span class="error"><i class="fa fa-exclamation-triangle"></i> ' + json['error']['postcode'] + '</span>');
				}
			}

			if (json['shipping_method']) {

				html = '<form action="' + submitShippingUrl + '" method="post" enctype="multipart/form-data">';
				html += '<div class="widget" style="margin:10px;"><h6>' + textShippingMethod + '</h6><div class="runshippingscroll"><div class="content">';

				for (i in json['shipping_method']) {
					html += '<div class="formbox"><p><b>' + json['shipping_method'][i]['title'] + '</b></p>';

					if (!json['shipping_method'][i]['error']) {
						for (j in json['shipping_method'][i]['quote']) {
							html += '<span class="label">';

							if (json['shipping_method'][i]['quote'][j]['code'] == shippingMethod) {
								html += '<input type="radio" name="shipping_method" value="' + json['shipping_method'][i]['quote'][j]['code'] + '" id="' + json['shipping_method'][i]['quote'][j]['code'] + '" checked="checked" />';
							} else {
								html += '<input type="radio" name="shipping_method" value="' + json['shipping_method'][i]['quote'][j]['code'] + '" id="' + json['shipping_method'][i]['quote'][j]['code'] + '" />';
							}

							html += '<label for="' + json['shipping_method'][i]['quote'][j]['code'] + '">' + json['shipping_method'][i]['quote'][j]['title'] + '</label>';
							html += '<label class="labelprice" for="' + json['shipping_method'][i]['quote'][j]['code'] + '"> ' + json['shipping_method'][i]['quote'][j]['text'] + '</label>';
							html += '</span>';
						}
					} else {
						html += '  <span class="error">' + json['shipping_method'][i]['error'] + '</span>';
					}

					html += '</div>';
				}

				html += '  </div></div><div class="buttons"><div class="left">';
				html += '  <input type="hidden" name="next" value="shipping" />';

				if (shippingMethod) {
				    html += '  <input type="submit" value="' + buttonShipping + '" id="button-shipping" class="button hidden" />';
                    html += '  <label for="button-shipping" class="button"><i class="fa fa-plane"></i> ' + buttonShipping + '</label>';
				} else {
				    html += '  <input type="submit" value="' + buttonShipping + '" id="button-shipping" class="button hidden" disabled="disabled" />';
                    html += '  <label for="button-shipping" class="button" disabled="disabled"><i class="fa fa-plane"></i> ' + buttonShipping + '</label>';
				}

				html += '</div></div></div>';
				html += '</form>';

				$.colorbox({
					overlayClose: true,
					close: "&xotime;",
					opacity: 0.4,
                    width: "100%",
					maxWidth: "320px",
                    height: "80%",
					maxHeight: "480px",
					href: false,
					html: html,
            		onLoad: function() {
            			$('#cboxClose').remove();
            		}
				});

				$('input[name=\'shipping_method\']').on('change', function() {
					$('#button-shipping').prop('disabled', false);
				});
			}
		}
	});
});

$('#shipping').on('change', 'select[name=\'country_id\']', function() {
	$.ajax({
		url: 'country&country_id=' + this.value,
		dataType: 'json',
		beforeSend: function() {
			$('select[name=\'country_id\']').after('<span class="wait icon"><i class="fa fa-spin fa-circle-o-notch icon-spin"></i></span>');
		},
		complete: function() {
			$('.wait').fadeOut(300).remove();
		},
		success: function(json) {
			if (json['postcode_required'] == '1') {
				$('#postcode-required').show();
			} else {
				$('#postcode-required').hide();
			}

			html = '<option value="">' + textSelect + '</option>';

			if (typeof json['zone'] !== 'undefined' && json['zone'] != '') {
				for (i = 0; i < json['zone'].length; i++) {
        			html += '<option value="' + json['zone'][i]['zone_id'] + '"';

					if (json['zone'][i]['zone_id'] == zoneId) {
	      				html += ' selected="selected"';
	    			}

	    			html += '>' + json['zone'][i]['name'] + '</option>';
				}
			} else {
				html += '<option value="0" selected="selected">' + textNone + '</option>';
			}

			$('select[name=\'zone_id\']').html(html);
		},
		error: function(xhr, ajaxOptions, thrownError) {
			alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
		}
	});
});

$('select[name=\'country_id\']').trigger('change');
