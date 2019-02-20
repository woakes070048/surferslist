if (typeof textModify === 'undefined') {
    var textModify = 'Modify';
}

if (typeof textCheckoutPaymentAddress === 'undefined') {
    var textCheckoutPaymentAddress = 'Billing Details';
}

if (typeof shippingRequired === 'undefined') {
    var shippingRequired = false;
}

if (typeof quickConfirm === 'undefined') {
    var quickConfirm = false;
}

if (typeof logged === 'undefined') {
    var logged = false;
}

if (typeof textSelectZone === 'undefined') {
    var textSelectZone = ' --- Select State --- ';
}

if (typeof textNone === 'undefined') {
    var textNone = ' --- None --- ';
}

if (typeof paymentZoneId === 'undefined') {
    var paymentZoneId = '';
}

if (typeof shippingZoneId === 'undefined') {
    var shippingZoneId = '';
}

if (typeof customer_group === 'undefined') {
    var customer_group = [];
}

// First Action (quickConfirm temp disabled)
if (false && quickConfirm === true) {
    quickConfirm();
} else if (logged === false) {
    checkoutLogin();
} else {
    checkoutPaymentAddress();
}

function checkoutLogin() {
	$.ajax({
		url: 'checkout-login',
		dataType: 'html',
		success: function(html) {
			$('#checkout .checkout-content').html(html);
			$('#checkout .checkout-content').slideDown('slow');
		},
		error: handleError
	});
}

function checkoutPaymentAddress() {
	$.ajax({
		url: 'checkout-payment-address',
		dataType: 'html',
		success: function(html) {
			$('#payment-address .checkout-content').html(html);
			$('#payment-address .checkout-content').slideDown('slow');

            //$('#payment-new select[name=\'country_id\']').trigger('change');
		},
		error: handleError
	});
}

function quickConfirm(module) {
	$.ajax({
		url: 'checkout-confirm',
		dataType: 'html',
		success: function(html) {
			$('#confirm .checkout-content').html(html);
			$('#confirm .checkout-content').slideDown('slow');
			$('.checkout-heading a').remove();
			$('#checkout .checkout-heading a').remove();
			$('#checkout .checkout-heading').append('<a>' + textModify + '<i class="fa fa-refresh"></i></a>');
			$('#shipping-address .checkout-heading a').remove();
			$('#shipping-address .checkout-heading').append('<a>' + textModify + '<i class="fa fa-refresh"></i></a>');
			$('#shipping-method .checkout-heading a').remove();
			$('#shipping-method .checkout-heading').append('<a>' + textModify + '<i class="fa fa-refresh"></i></a>');
			$('#payment-address .checkout-heading a').remove();
			$('#payment-address .checkout-heading').append('<a>' + textModify + '<i class="fa fa-refresh"></i></a>');
			$('#payment-method .checkout-heading a').remove();
			$('#payment-method .checkout-heading').append('<a>' + textModify + '<i class="fa fa-refresh"></i></a>');
		},
		error: handleError
	});
}

function handleError (xhr, ajaxOptions, thrownError) {
    alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
}

// Headings
$('.checkout-heading').clickOrTouch('a', function() {
	$('.checkout-content').slideUp('slow');
	$(this).parent().parent().find('.checkout-content').slideDown('slow');
});

// Login
$('#checkout').clickOrTouch('#button-login', function() {
	$.ajax({
		url: 'checkout-login-validate',
		type: 'post',
		data: $('#checkout #login :input'),
		dataType: 'json',
		beforeSend: function() {
			$('#button-login').prop('disabled', true);
			$('#button-login').after('<span class="wait icon"><i class="fa fa-spin fa-circle-o-notch icon-spin"></i></span>');
		},
		complete: function() {
			$('#button-login').prop('disabled', false);
			$('.wait').remove();
		},
		success: function(json) {
			$('.warning, .error').remove();

			if (json['redirect']) {
				location = json['redirect'];
			} else if (json['error']) {
				$('#checkout #show-login-warning').prepend('<div class="warning" style="display: none;"><p>' + json['error']['warning'] + '</p><span class="close"><i class="fa fa-times"></i></span><span class="icon"><i class="fa fa-exclamation-triangle"></i></span></div>');
				$('.warning').fadeIn('slow');
			}
		},
		error: handleError
	});
});

// Registration
$('#checkout').clickOrTouch('#button-account', loadPopupRegisterForm);
// $('#checkout').clickOrTouch('#button-account', function() {
// 	$.ajax({
// 		url: 'checkout-register',
// 		dataType: 'html',
// 		beforeSend: function() {
// 			$('#button-account').prop('disabled', true);
// 			$('#button-account').after('<span class="wait icon"><i class="fa fa-spin fa-circle-o-notch icon-spin"></i></span>');
// 		},
// 		complete: function() {
// 			$('#button-account').prop('disabled', false);
// 			$('.wait').remove();
// 		},
// 		success: function(html) {
// 			$('.warning, .error').remove();
// 			$('#payment-address .checkout-content').html(html);
// 			$('#checkout .checkout-content').slideUp('slow');
// 			$('#payment-address .checkout-content').slideDown('slow');
// 			$('.checkout-heading a').remove();
// 			$('#checkout .checkout-heading').append('<a>' + textModify + '<i class="fa fa-refresh"></i></a>');
//
//             onloadReCaptcha();
// 		},
// 		error: handleError
// 	});
// });

$('#payment-address').clickOrTouch('#button-register', function() {
	$.ajax({
		url: 'checkout-register-validate',
		type: 'post',
		data: $('#payment-address input[type=\'text\'], #payment-address input[type=\'password\'], #payment-address input[type=\'hidden\'], #payment-address textarea[name=\'g-recaptcha-response\']'),
		dataType: 'json',
		beforeSend: function() {
			$('#button-register').prop('disabled', true);
			$('#button-register').after('<span class="wait icon"><i class="fa fa-spin fa-circle-o-notch icon-spin"></i></span>');
		},
		complete: function() {
			$('#button-register').prop('disabled', false);
			$('.wait').remove();
		},
		success: function(json) {
			$('.warning, .error').remove();

			if (json['redirect']) {
				location = json['redirect'];
			} else if (json['error']) {
				if (json['error']['warning']) {
					$('#payment-address #show-button-register').prepend('<div class="warning" style="display: none;"><p>' + json['error']['warning'] + '</p><span class="close"><i class="fa fa-times"></i></span><span class="icon"><i class="fa fa-exclamation-triangle"></i></span></div>');
					$('.warning').fadeIn('slow');
				}

				if (json['error']['firstname']) {
					$('#payment-address input[name=\'firstname\']').after('<span class="error"><i class="fa fa-exclamation-triangle"></i> ' + json['error']['firstname'] + '</span>');
				}

				if (json['error']['lastname']) {
					$('#payment-address input[name=\'lastname\']').after('<span class="error"><i class="fa fa-exclamation-triangle"></i> ' + json['error']['lastname'] + '</span>');
				}

				if (json['error']['email']) {
					$('#payment-address input[name=\'email\']').after('<span class="error"><i class="fa fa-exclamation-triangle"></i> ' + json['error']['email'] + '</span>');
				}

				if (json['error']['password']) {
					$('#payment-address input[name=\'password\']').after('<span class="error"><i class="fa fa-exclamation-triangle"></i> ' + json['error']['password'] + '</span>');
				}
				/*
				if (json['error']['confirm']) {
					$('#payment-address input[name=\'confirm\']').after('<span class="error"><i class="fa fa-exclamation-triangle"></i> ' + json['error']['confirm'] + '</span>');
				}*/

				if (json['error']['captcha']) {
					$('#payment-address .recaptcha-box').after('<span class="error"><i class="fa fa-exclamation-triangle"></i> ' + json['error']['captcha'] + '</span>');
				}

                if (json['captcha_widget_id'].length) {
                    grecaptcha.reset(json['captcha_widget_id']);
                }
			} else {
				if (shippingRequired) {
    				var shipping_address = $('#payment-address input[name=\'shipping_address\']:checked').val();

    				if (shipping_address) {
    					$.ajax({
    						url: 'checkout-shipping-method',
    						dataType: 'html',
    						success: function(html) {
    							$('#shipping-method .checkout-content').html(html);
    							$('#payment-address .checkout-content').slideUp('slow');
    							$('#shipping-method .checkout-content').slideDown('slow');
    							$('#checkout .checkout-heading a').remove();
    							$('#payment-address .checkout-heading a').remove();
    							$('#shipping-address .checkout-heading a').remove();
    							$('#shipping-method .checkout-heading a').remove();
    							$('#payment-method .checkout-heading a').remove();
    							$('#shipping-address .checkout-heading').append('<a>' + textModify + '<i class="fa fa-refresh"></i></a>');
    							$('#payment-address .checkout-heading').append('<a>' + textModify + '<i class="fa fa-refresh"></i></a>');

    							$.ajax({
    								url: 'checkout-shipping-address',
    								dataType: 'html',
    								success: function(html) {
    									$('#shipping-address .checkout-content').html(html);
    								},
    								error: handleError
    							});
    						},
    						error: handleError
    					});
    				} else {
    					$.ajax({
    						url: 'checkout-shipping-address',
    						dataType: 'html',
    						success: function(html) {
    							$('#shipping-address .checkout-content').html(html);
    							$('#payment-address .checkout-content').slideUp('slow');
    							$('#shipping-address .checkout-content').slideDown('slow');
    							$('#checkout .checkout-heading a').remove();
    							$('#payment-address .checkout-heading a').remove();
    							$('#shipping-address .checkout-heading a').remove();
    							$('#shipping-method .checkout-heading a').remove();
    							$('#payment-method .checkout-heading a').remove();
    							$('#payment-address .checkout-heading').append('<a>' + textModify + '<i class="fa fa-refresh"></i></a>');
    						},
    						error: handleError
    					});
    				}
				} else {
    				$.ajax({
    					url: 'checkout-payment-method',
    					dataType: 'html',
    					success: function(html) {
    						$('#payment-method .checkout-content').html(html);
    						$('#payment-address .checkout-content').slideUp('slow');
    						$('#payment-method .checkout-content').slideDown('slow');
    						$('#checkout .checkout-heading a').remove();
    						$('#payment-address .checkout-heading a').remove();
    						$('#payment-method .checkout-heading a').remove();
    						$('#payment-address .checkout-heading').append('<a>' + textModify + '<i class="fa fa-refresh"></i></a>');
    					},
    					error: handleError
    				});
				}

				$.ajax({
					url: 'checkout-payment-address',
					dataType: 'html',
					success: function(html) {
						$('#payment-address .checkout-content').html(html);
						$('#payment-address .checkout-heading span').html(textCheckoutPaymentAddress);
					},
					error: handleError
				});
			}
		},
		error: handleError
	});
});

// Payment Address
$('#payment-address').clickOrTouch('#button-payment-address', function() {
	$.ajax({
		url: 'checkout-payment-address-validate',
		type: 'post',
		data: $('#payment-address input[type=\'text\'], #payment-address input[type=\'password\'], #payment-address input[type=\'checkbox\']:checked, #payment-address input[type=\'radio\']:checked, #payment-address input[type=\'hidden\'], #payment-address select'),
		dataType: 'json',
		beforeSend: function() {
			$('#button-payment-address').prop('disabled', true);
			$('#button-payment-address').after('<span class="wait icon"><i class="fa fa-spin fa-circle-o-notch icon-spin"></i></span>');
		},
		complete: function() {
			$('#button-payment-address').prop('disabled', false);
			$('.wait').remove();
		},
		success: function(json) {
			$('.warning, .error').remove();

			if (json['redirect']) {
				location = json['redirect'];
			} else if (json['error']) {
				if (json['error']['warning']) {
					$('#payment-address #show-button-payment-address').prepend('<div class="warning" style="display: none;"><p>' + json['error']['warning'] + '</p><span class="close"><i class="fa fa-times"></i></span><span class="icon"><i class="fa fa-exclamation-triangle"></i></span></div>');
					$('.warning').fadeIn('slow');
				}

				if (json['error']['firstname']) {
					$('#payment-address input[name=\'firstname\']').after('<span class="error"><i class="fa fa-exclamation-triangle"></i> ' + json['error']['firstname'] + '</span>');
				}

				if (json['error']['lastname']) {
					$('#payment-address input[name=\'lastname\']').after('<span class="error"><i class="fa fa-exclamation-triangle"></i> ' + json['error']['lastname'] + '</span>');
				}

				if (json['error']['telephone']) {
					$('#payment-address input[name=\'telephone\']').after('<span class="error"><i class="fa fa-exclamation-triangle"></i> ' + json['error']['telephone'] + '</span>');
				}

				if (json['error']['company_id']) {
					$('#payment-address input[name=\'company_id\']').after('<span class="error"><i class="fa fa-exclamation-triangle"></i> ' + json['error']['company_id'] + '</span>');
				}

				if (json['error']['tax_id']) {
					$('#payment-address input[name=\'tax_id\']').after('<span class="error"><i class="fa fa-exclamation-triangle"></i> ' + json['error']['tax_id'] + '</span>');
				}

				if (json['error']['address_1']) {
					$('#payment-address input[name=\'address_1\']').after('<span class="error"><i class="fa fa-exclamation-triangle"></i> ' + json['error']['address_1'] + '</span>');
				}

				if (json['error']['city']) {
					$('#payment-address input[name=\'city\']').after('<span class="error"><i class="fa fa-exclamation-triangle"></i> ' + json['error']['city'] + '</span>');
				}

				if (json['error']['postcode']) {
					$('#payment-address input[name=\'postcode\']').after('<span class="error"><i class="fa fa-exclamation-triangle"></i> ' + json['error']['postcode'] + '</span>');
				}

				if (json['error']['country']) {
					$('#payment-address select[name=\'country_id\']').after('<span class="error"><i class="fa fa-exclamation-triangle"></i> ' + json['error']['country'] + '</span>');
				}

				if (json['error']['zone']) {
					$('#payment-address select[name=\'zone_id\']').after('<span class="error"><i class="fa fa-exclamation-triangle"></i> ' + json['error']['zone'] + '</span>');
				}
			} else {
				if (shippingRequired) {
    				$.ajax({
    					url: 'checkout-shipping-address',
    					dataType: 'html',
    					success: function(html) {
    						$('#shipping-address .checkout-content').html(html);
    						$('#payment-address .checkout-content').slideUp('slow');
    						$('#shipping-address .checkout-content').slideDown('slow');
    						$('#payment-address .checkout-heading a').remove();
    						$('#shipping-address .checkout-heading a').remove();
    						$('#shipping-method .checkout-heading a').remove();
    						$('#payment-method .checkout-heading a').remove();
    						$('#payment-address .checkout-heading').append('<a>' + textModify + '<i class="fa fa-refresh"></i></a>');
    					},
    					error: handleError
    				});
				} else {
    				$.ajax({
    					url: 'checkout-payment-method',
    					dataType: 'html',
    					success: function(html) {
    						$('#payment-method .checkout-content').html(html);
    						$('#payment-address .checkout-content').slideUp('slow');
    						$('#payment-method .checkout-content').slideDown('slow');
    						$('#payment-address .checkout-heading a').remove();
    						$('#payment-method .checkout-heading a').remove();
    						$('#payment-address .checkout-heading').append('<a>' + textModify + '<i class="fa fa-refresh"></i></a>');
    					},
    					error: handleError
    				});
				}

				$.ajax({
					url: 'checkout-payment-address',
					dataType: 'html',
					success: function(html) {
						$('#payment-address .checkout-content').html(html);
					},
					error: handleError
				});
			}
		},
		error: handleError
	});
});

$('#payment-address').on('change', 'input[name=\'payment_address\']', function() {
	if (this.value == 'new') {
		$('#payment-existing').hide();
		$('#payment-new').show();
	} else {
		$('#payment-existing').show();
		$('#payment-new').hide();
	}
});

$('#payment-address').on('change', 'select[name=\'country_id\']', function() {
	if (this.value == '') return;
	$.ajax({
		url: 'country&country_id=' + this.value,
		dataType: 'json',
		beforeSend: function() {
			$('select[name=\'country_id\']').after('<span class="wait icon"><i class="fa fa-spin fa-circle-o-notch icon-spin"></i></span>');
		},
		complete: function() {
			$('.wait').remove();
		},
		success: function(json) {
			if (json['postcode_required'] == '1') {
				$('#payment-postcode-required').show();
			} else {
				$('#payment-postcode-required').hide();
			}

			html = '<option value="">' + textSelectZone + '</option>';

			if (typeof json['zone'] !== 'undefined' && json['zone'] != '') {
				for (i = 0; i < json['zone'].length; i++) {
        			html += '<option value="' + json['zone'][i]['zone_id'] + '"';

					if (json['zone'][i]['zone_id'] == paymentZoneId) {
	      				html += ' selected="selected"';
	    			}

	    			html += '>' + json['zone'][i]['name'] + '</option>';
				}
			} else {
				html += '<option value="0" selected="selected">' + textNone + '</option>';
			}

			$('#payment-address select[name=\'zone_id\']').html(html);
		},
		error: handleError
	});
});

$('#payment-address').clickOrTouch('#payment-address-new', function() {
    $('#payment-new select[name=\'country_id\']').trigger('change');
});


// Shipping Address
$('#shipping-address').clickOrTouch('#button-shipping-address', function() {
	$.ajax({
		url: 'checkout-shipping-address-validate',
		type: 'post',
		data: $('#shipping-address input[type=\'text\'], #shipping-address input[type=\'hidden\'], #shipping-address input[type=\'password\'], #shipping-address input[type=\'checkbox\']:checked, #shipping-address input[type=\'radio\']:checked, #shipping-address select'),
		dataType: 'json',
		beforeSend: function() {
			$('#button-shipping-address').prop('disabled', true);
			$('#button-shipping-address').after('<span class="wait icon"><i class="fa fa-spin fa-circle-o-notch icon-spin"></i></span>');
		},
		complete: function() {
			$('#button-shipping-address').prop('disabled', false);
			$('.wait').remove();
		},
		success: function(json) {
			$('.warning, .error').remove();

			if (json['redirect']) {
				location = json['redirect'];
			} else if (json['error']) {
				if (json['error']['warning']) {
					$('#shipping-address #show-button-shipping-address').prepend('<div class="warning" style="display: none;"><p>' + json['error']['warning'] + '</p><span class="close"><i class="fa fa-times"></i></span><span class="icon"><i class="fa fa-exclamation-triangle"></i></span></div>');
					$('.warning').fadeIn('slow');
				}

				if (json['error']['firstname']) {
					$('#shipping-address input[name=\'firstname\']').after('<span class="error"><i class="fa fa-exclamation-triangle"></i> ' + json['error']['firstname'] + '</span>');
				}

				if (json['error']['lastname']) {
					$('#shipping-address input[name=\'lastname\']').after('<span class="error"><i class="fa fa-exclamation-triangle"></i> ' + json['error']['lastname'] + '</span>');
				}

				if (json['error']['email']) {
					$('#shipping-address input[name=\'email\']').after('<span class="error"><i class="fa fa-exclamation-triangle"></i> ' + json['error']['email'] + '</span>');
				}

				if (json['error']['telephone']) {
					$('#shipping-address input[name=\'telephone\']').after('<span class="error"><i class="fa fa-exclamation-triangle"></i> ' + json['error']['telephone'] + '</span>');
				}

				if (json['error']['address_1']) {
					$('#shipping-address input[name=\'address_1\']').after('<span class="error"><i class="fa fa-exclamation-triangle"></i> ' + json['error']['address_1'] + '</span>');
				}

				if (json['error']['city']) {
					$('#shipping-address input[name=\'city\']').after('<span class="error"><i class="fa fa-exclamation-triangle"></i> ' + json['error']['city'] + '</span>');
				}

				if (json['error']['postcode']) {
					$('#shipping-address input[name=\'postcode\']').after('<span class="error"><i class="fa fa-exclamation-triangle"></i> ' + json['error']['postcode'] + '</span>');
				}

				if (json['error']['country']) {
					$('#shipping-address select[name=\'country_id\']').after('<span class="error"><i class="fa fa-exclamation-triangle"></i> ' + json['error']['country'] + '</span>');
				}

				if (json['error']['zone']) {
					$('#shipping-address select[name=\'zone_id\']').after('<span class="error"><i class="fa fa-exclamation-triangle"></i> ' + json['error']['zone'] + '</span>');
				}
			} else {
				$.ajax({
					url: 'checkout-shipping-method',
					dataType: 'html',
					success: function(html) {
						$('#shipping-method .checkout-content').html(html);
						$('#shipping-address .checkout-content').slideUp('slow');
						$('#shipping-method .checkout-content').slideDown('slow');
						$('#shipping-address .checkout-heading a').remove();
						$('#shipping-method .checkout-heading a').remove();
						$('#payment-method .checkout-heading a').remove();
						$('#shipping-address .checkout-heading').append('<a>' + textModify + '<i class="fa fa-refresh"></i></a>');

						$.ajax({
							url: 'checkout-shipping-address',
							dataType: 'html',
							success: function(html) {
								$('#shipping-address .checkout-content').html(html);
							},
							error: handleError
						});
					},
					error: handleError
				});

				$.ajax({
					url: 'checkout-payment-address',
					dataType: 'html',
					success: function(html) {
						$('#payment-address .checkout-content').html(html);
					},
					error: handleError
				});
			}
		},
		error: handleError
	});
});

$('#shipping-address').on('change', 'input[name=\'shipping_address\']', function() {
	if (this.value == 'new') {
		$('#shipping-existing').hide();
		$('#shipping-new').show();
	} else {
		$('#shipping-existing').show();
		$('#shipping-new').hide();
	}
});

$('#shipping-address').on('change', 'select[name=\'country_id\']', function() {
	if (this.value == '') return;
	$.ajax({
		url: 'country&country_id=' + this.value,
		dataType: 'json',
		beforeSend: function() {
			$('select[name=\'country_id\']').after('<span class="wait icon"><i class="fa fa-spin fa-circle-o-notch icon-spin"></i></span>');
		},
		complete: function() {
			$('.wait').remove();
		},
		success: function(json) {
			if (json['postcode_required'] == '1') {
				$('#shipping-postcode-required').show();
			} else {
				$('#shipping-postcode-required').hide();
			}

			html = '<option value="">' + textSelectZone + '</option>';

			if (typeof json['zone'] !== 'undefined' && json['zone'] != '') {
				for (i = 0; i < json['zone'].length; i++) {
        			html += '<option value="' + json['zone'][i]['zone_id'] + '"';

					if (json['zone'][i]['zone_id'] == shippingZoneId) {
	      				html += ' selected="selected"';
	    			}

	    			html += '>' + json['zone'][i]['name'] + '</option>';
				}
			} else {
				html += '<option value="0" selected="selected">' + textNone + '</option>';
			}

			$('#shipping-address select[name=\'zone_id\']').html(html);
		},
		error: handleError
	});
});

$('#shipping-address').clickOrTouch('#shipping-address-new', function() {
    $('#shipping-new select[name=\'country_id\']').trigger('change');
});

// // Guest
// $('.checkout').clickOrTouch('#button-guest', function() {
// 	$.ajax({
// 		url: 'checkout-guest-validate',
// 		type: 'post',
// 		data: $('#payment-address input[type=\'text\'], #payment-address input[type=\'checkbox\']:checked, #payment-address input[type=\'radio\']:checked, #payment-address input[type=\'hidden\'], #payment-address select'),
// 		dataType: 'json',
// 		beforeSend: function() {
// 			$('#button-guest').prop('disabled', true);
// 			$('#button-guest').after('<span class="wait icon"><i class="fa fa-spin fa-circle-o-notch icon-spin"></i></span>');
// 		},
// 		complete: function() {
// 			$('#button-guest').prop('disabled', false);
// 			$('.wait').remove();
// 		},
// 		success: function(json) {
// 			$('.warning, .error').remove();
//
// 			if (json['redirect']) {
// 				location = json['redirect'];
// 			} else if (json['error']) {
// 				if (json['error']['warning']) {
// 					$('#payment-address #show-button-guest').prepend('<div class="warning" style="display: none;"><p>' + json['error']['warning'] + '</p><span class="close"><i class="fa fa-times"></i></span><span class="icon"><i class="fa fa-exclamation-triangle"></i></span></div>');
// 					$('.warning').fadeIn('slow');
// 				}
//
// 				if (json['error']['firstname']) {
// 					$('#payment-address input[name=\'firstname\']').after('<span class="error"><i class="fa fa-exclamation-triangle"></i> ' + json['error']['firstname'] + '</span>');
// 				}
//
// 				if (json['error']['lastname']) {
// 					$('#payment-address input[name=\'lastname\']').after('<span class="error"><i class="fa fa-exclamation-triangle"></i> ' + json['error']['lastname'] + '</span>');
// 				}
//
// 				if (json['error']['email']) {
// 					$('#payment-address input[name=\'email\']').after('<span class="error"><i class="fa fa-exclamation-triangle"></i> ' + json['error']['email'] + '</span>');
// 				}
//
// 				if (json['error']['telephone']) {
// 					$('#payment-address input[name=\'telephone\']').after('<span class="error"><i class="fa fa-exclamation-triangle"></i> ' + json['error']['telephone'] + '</span>');
// 				}
//
// 				if (json['error']['company_id']) {
// 					$('#payment-address input[name=\'company_id\']').after('<span class="error"><i class="fa fa-exclamation-triangle"></i> ' + json['error']['company_id'] + '</span>');
// 				}
//
// 				if (json['error']['tax_id']) {
// 					$('#payment-address input[name=\'tax_id\']').after('<span class="error"><i class="fa fa-exclamation-triangle"></i> ' + json['error']['tax_id'] + '</span>');
// 				}
//
// 				if (json['error']['address_1']) {
// 					$('#payment-address input[name=\'address_1\']').after('<span class="error"><i class="fa fa-exclamation-triangle"></i> ' + json['error']['address_1'] + '</span>');
// 				}
//
// 				if (json['error']['city']) {
// 					$('#payment-address input[name=\'city\']').after('<span class="error"><i class="fa fa-exclamation-triangle"></i> ' + json['error']['city'] + '</span>');
// 				}
//
// 				if (json['error']['postcode']) {
// 					$('#payment-address input[name=\'postcode\']').after('<span class="error"><i class="fa fa-exclamation-triangle"></i> ' + json['error']['postcode'] + '</span>');
// 				}
//
// 				if (json['error']['country']) {
// 					$('#payment-address select[name=\'country_id\']').after('<span class="error"><i class="fa fa-exclamation-triangle"></i> ' + json['error']['country'] + '</span>');
// 				}
//
// 				if (json['error']['zone']) {
// 					$('#payment-address select[name=\'zone_id\']').after('<span class="error"><i class="fa fa-exclamation-triangle"></i> ' + json['error']['zone'] + '</span>');
// 				}
// 			} else {
// 				if (shippingRequired) {
//     				var shipping_address = $('#payment-address input[name=\'shipping_address\']:checked').val();
//
//     				if (shipping_address) {
//     					$.ajax({
//     						url: 'checkout-shipping-method',
//     						dataType: 'html',
//     						success: function(html) {
//     							$('#shipping-method .checkout-content').html(html);
//     							$('#payment-address .checkout-content').slideUp('slow');
//     							$('#shipping-method .checkout-content').slideDown('slow');
//     							$('#payment-address .checkout-heading a').remove();
//     							$('#shipping-address .checkout-heading a').remove();
//     							$('#shipping-method .checkout-heading a').remove();
//     							$('#payment-method .checkout-heading a').remove();
//     							$('#payment-address .checkout-heading').append('<a>' + textModify + '<i class="fa fa-refresh"></i></a>');
//     							$('#shipping-address .checkout-heading').append('<a>' + textModify + '<i class="fa fa-refresh"></i></a>');
//
//     							$.ajax({
//     								url: 'checkout-guest-shipping',
//     								dataType: 'html',
//     								success: function(html) {
//     									$('#shipping-address .checkout-content').html(html);
//     								},
//     								error: handleError
//     							});
//     						},
//     						error: handleError
//     					});
//     				} else {
//     					$.ajax({
//     						url: 'checkout-guest-shipping',
//     						dataType: 'html',
//     						success: function(html) {
//     							$('#shipping-address .checkout-content').html(html);
//     							$('#payment-address .checkout-content').slideUp('slow');
//     							$('#shipping-address .checkout-content').slideDown('slow');
//     							$('#payment-address .checkout-heading a').remove();
//     							$('#shipping-address .checkout-heading a').remove();
//     							$('#shipping-method .checkout-heading a').remove();
//     							$('#payment-method .checkout-heading a').remove();
//     							$('#payment-address .checkout-heading').append('<a>' + textModify + '<i class="fa fa-refresh"></i></a>');
//     						},
//     						error: handleError
//     					});
//     				}
// 				} else {
//     				$.ajax({
//     					url: 'checkout-payment-method',
//     					dataType: 'html',
//     					success: function(html) {
//     						$('#payment-method .checkout-content').html(html);
//     						$('#payment-address .checkout-content').slideUp('slow');
//     						$('#payment-method .checkout-content').slideDown('slow');
//     						$('#payment-address .checkout-heading a').remove();
//     						$('#payment-method .checkout-heading a').remove();
//     						$('#payment-address .checkout-heading').append('<a>' + textModify + '<i class="fa fa-refresh"></i></a>');
//     					},
//     					error: handleError
//     				});
// 				}
// 			}
// 		},
// 		error: handleError
// 	});
// });
//
// $('#payment-address').on('change', 'input[name=\'customer_group_id\']:checked', function() {
// 	if (customer_group[this.value]) {
// 		if (customer_group[this.value]['company_id_display'] == '1') {
// 			$('#company-id-display').show();
// 		} else {
// 			$('#company-id-display').hide();
// 		}
//
// 		if (customer_group[this.value]['company_id_required'] == '1') {
// 			$('#company-id-required').show();
// 		} else {
// 			$('#company-id-required').hide();
// 		}
//
// 		if (customer_group[this.value]['tax_id_display'] == '1') {
// 			$('#tax-id-display').show();
// 		} else {
// 			$('#tax-id-display').hide();
// 		}
//
// 		if (customer_group[this.value]['tax_id_required'] == '1') {
// 			$('#tax-id-required').show();
// 		} else {
// 			$('#tax-id-required').hide();
// 		}
// 	}
// });
//
// $('#payment-address').clickOrTouch('#show-button-guest', function() {
//     $('#payment-address input[name=\'customer_group_id\']:checked').trigger('change');
// });
//
// $('#shipping-address').clickOrTouch('#button-guest-shipping', function() {
// 	$.ajax({
// 		url: 'checkout-guest-shipping-validate',
// 		type: 'post',
// 		data: $('#shipping-address input[type=\'text\'], #shipping-address select'),
// 		dataType: 'json',
// 		beforeSend: function() {
// 			$('#button-guest-shipping').prop('disabled', true);
// 			$('#button-guest-shipping').after('<span class="wait icon"><i class="fa fa-spin fa-circle-o-notch icon-spin"></i></span>');
// 		},
// 		complete: function() {
// 			$('#button-guest-shipping').prop('disabled', false);
// 			$('.wait').remove();
// 		},
// 		success: function(json) {
// 			$('.warning, .error').remove();
//
// 			if (json['redirect']) {
// 				location = json['redirect'];
// 			} else if (json['error']) {
// 				if (json['error']['warning']) {
// 					$('#shipping-address #show-button-guest-shipping').prepend('<div class="warning" style="display: none;"><p>' + json['error']['warning'] + '</p><span class="close"><i class="fa fa-times"></i></span><span class="icon"><i class="fa fa-exclamation-triangle"></i></span></div>');
// 					$('.warning').fadeIn('slow');
// 				}
//
// 				if (json['error']['firstname']) {
// 					$('#shipping-address input[name=\'firstname\']').after('<span class="error"><i class="fa fa-exclamation-triangle"></i> ' + json['error']['firstname'] + '</span>');
// 				}
//
// 				if (json['error']['lastname']) {
// 					$('#shipping-address input[name=\'lastname\']').after('<span class="error"><i class="fa fa-exclamation-triangle"></i> ' + json['error']['lastname'] + '</span>');
// 				}
//
// 				if (json['error']['address_1']) {
// 					$('#shipping-address input[name=\'address_1\']').after('<span class="error"><i class="fa fa-exclamation-triangle"></i> ' + json['error']['address_1'] + '</span>');
// 				}
//
// 				if (json['error']['city']) {
// 					$('#shipping-address input[name=\'city\']').after('<span class="error"><i class="fa fa-exclamation-triangle"></i> ' + json['error']['city'] + '</span>');
// 				}
//
// 				if (json['error']['postcode']) {
// 					$('#shipping-address input[name=\'postcode\']').after('<span class="error"><i class="fa fa-exclamation-triangle"></i> ' + json['error']['postcode'] + '</span>');
// 				}
//
// 				if (json['error']['country']) {
// 					$('#shipping-address select[name=\'country_id\']').after('<span class="error"><i class="fa fa-exclamation-triangle"></i> ' + json['error']['country'] + '</span>');
// 				}
//
// 				if (json['error']['zone']) {
// 					$('#shipping-address select[name=\'zone_id\']').after('<span class="error"><i class="fa fa-exclamation-triangle"></i> ' + json['error']['zone'] + '</span>');
// 				}
// 			} else {
// 				$.ajax({
// 					url: 'checkout-shipping-method',
// 					dataType: 'html',
// 					success: function(html) {
// 						$('#shipping-method .checkout-content').html(html);
// 						$('#shipping-address .checkout-content').slideUp('slow');
// 						$('#shipping-method .checkout-content').slideDown('slow');
// 						$('#shipping-address .checkout-heading a').remove();
// 						$('#shipping-method .checkout-heading a').remove();
// 						$('#payment-method .checkout-heading a').remove();
// 						$('#shipping-address .checkout-heading').append('<a>' + textModify + '<i class="fa fa-refresh"></i></a>');
// 					},
// 					error: handleError
// 				});
// 			}
// 		},
// 		error: handleError
// 	});
// });

// Shipping Method
$('#shipping-method').clickOrTouch('#button-shipping-method', function() {
	$.ajax({
		url: 'checkout-shipping-method-validate',
		type: 'post',
		data: $('#shipping-method input[type=\'radio\']:checked, #shipping-method textarea, #shipping-method input[type=\'checkbox\']:checked, #shipping-method input[type=\'hidden\']'),
		dataType: 'json',
		beforeSend: function() {
			$('#button-shipping-method').prop('disabled', true);
			$('#button-shipping-method').after('<span class="wait icon"><i class="fa fa-spin fa-circle-o-notch icon-spin"></i></span>');
		},
		complete: function() {
			$('#button-shipping-method').prop('disabled', false);
			$('.wait').remove();
		},
		success: function(json) {
			$('.warning, .error').remove();

			if (json['redirect']) {
				location = json['redirect'];
			} else if (json['error']) {
				if (json['error']['warning']) {
					$('#shipping-method #show-button-shipping-method').prepend('<div class="warning" style="display: none;"><p>' + json['error']['warning'] + '</p><span class="close"><i class="fa fa-times"></i></span><span class="icon"><i class="fa fa-exclamation-triangle"></i></span></div>');
					$('.warning').fadeIn('slow');
				}
			} else {
				$.ajax({
					url: 'checkout-payment-method',
					dataType: 'html',
					success: function(html) {
						$('#payment-method .checkout-content').html(html);
						$('#shipping-method .checkout-content').slideUp('slow');
						$('#payment-method .checkout-content').slideDown('slow');
						$('#shipping-method .checkout-heading a').remove();
						$('#payment-method .checkout-heading a').remove();
						$('#shipping-method .checkout-heading').append('<a>' + textModify + '<i class="fa fa-refresh"></i></a>');
					},
					error: handleError
				});
			}
		},
		error: handleError
	});
});

// Payment Method
$('#payment-method').clickOrTouch('#button-payment-method', function() {
	$.ajax({
		url: 'checkout-payment-method-validate',
		type: 'post',
		data: $('#payment-method input[type=\'radio\']:checked, #payment-method input[type=\'checkbox\']:checked, #payment-method textarea'),
		dataType: 'json',
		beforeSend: function() {
			$('#button-payment-method').prop('disabled', true);
			$('#button-payment-method').after('<span class="wait icon"><i class="fa fa-spin fa-circle-o-notch icon-spin"></i></span>');
		},
		complete: function() {
			$('#button-payment-method').prop('disabled', false);
			$('.wait').remove();
		},
		success: function(json) {
			$('.warning, .error').remove();

			if (json['redirect']) {
				location = json['redirect'];
			} else if (json['error']) {
				if (json['error']['warning']) {
					$('#payment-method #show-button-payment-method').prepend('<div class="warning" style="display: none;"><p>' + json['error']['warning'] + '</p><span class="close"><i class="fa fa-times"></i></span><span class="icon"><i class="fa fa-exclamation-triangle"></i></span></div>');
					$('.warning').fadeIn('slow');
				}
			} else {
				$.ajax({
					url: 'checkout-confirm',
					dataType: 'html',
					success: function(html) {
						$('#confirm .checkout-content').html(html);
						$('#payment-method .checkout-content').slideUp('slow');
						$('#confirm .checkout-content').slideDown('slow');
						$('#payment-method .checkout-heading a').remove();
						$('#payment-method .checkout-heading').append('<a>' + textModify + '<i class="fa fa-refresh"></i></a>');
					},
					error: handleError
				});
			}
		},
		error: handleError
	});
});

$('#confirm').clickOrTouch('#button-confirm-cod', function() {
    var checkoutSuccess = $(this).attr('data-success');

    $.ajax({
        type: 'get',
        url: 'payment-cod-confirm',
        success: function() {
            location = checkoutSuccess;
        },
        error: handleError
    });
});

$('#confirm').clickOrTouch('#button-confirm-free-checkout', function() {
    var checkoutSuccess = $(this).attr('data-success');

    $.ajax({
        type: 'get',
        url: 'payment-free-checkout-confirm',
        success: function() {
            location = checkoutSuccess;
        },
        error: handleError
    });
});

$('#confirm').clickOrTouch('#button-confirm-cheque', function() {
    var checkoutSuccess = $(this).attr('data-success');

    $.ajax({
        type: 'get',
        url: 'payment-cheque-confirm',
        success: function() {
            location = checkoutSuccess;
        },
        error: handleError
    });
});
