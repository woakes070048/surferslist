if (typeof textWait === 'undefined') {
    var textWait = 'Please Wait';
}

if (typeof textSelectZone === 'undefined') {
    var textSelectZone = '--- Select State ---';
}

if (typeof textImageManager === 'undefined') {
    var textImageManager = 'Image Manager';
}

if (typeof helpTransfer === 'undefined') {
    var helpTransfer = 'Transfer Listing(s)';
}

if (typeof textConfirmTransfer === 'undefined') {
    var textConfirmTransfer = 'Confirm Transfer?';
}

if (typeof addressZoneId === 'undefined') {
    var addressZoneId = '';
}

if (typeof memberZoneId === 'undefined') {
    var memberZoneId = '';
}

if (typeof errorMaxImages === 'undefined') {
    var errorMaxImages = "Error: You have exceeded the max number of images!";
}

if (typeof errorNotChecked === 'undefined') {
    var errorNotChecked = "Error: No listing(s) selected!";
}

// All AJAX Select Dependencies
$(window).on('load', function() {
    $('#form.address-form select[name=\'country_id\']').trigger('change');
    $('#form.member-profile-form select[name=\'member_country_id\']').trigger('change');
});

// Address
if ($('#form.address-form').length) {
    $('#form.address-form').on('change', 'select[name=\'country_id\']', function() {
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

    					if (json['zone'][i]['zone_id'] == addressZoneId) {
    	      				html += ' selected="selected"';
    	    			}

    	    			html += '>' + json['zone'][i]['name'] + '</option>';
    				}
    			} else {
    				html += '<option value="0" selected="selected">' + textNone + '</option>';
    			}

    			$('select[name=\'zone_id\']').html(html);
    		},
    		error: handleError
    	});
    });
}

// Profile
if ($('#form.member-profile-form').length) {
    uploadProfileImage('button-upload', 'member_account_image_thumb', 'member_account_image', 'profile', memberNoImage);

    $('#form.member-profile-form').clickOrTouch('#button-clear-image', function(e) {
        e.preventDefault();
        $('#member_account_image_thumb').attr('src', memberNoImage);
        $('#member_account_image').val('');
    });

    // if (typeof memberNoBanner !== 'undefined' && memberNoBanner.length) {
    if ($('input[name=\'member_account_banner\']').length) {
        uploadProfileImage('button-upload-banner', 'member_account_banner_thumb', 'member_account_banner', 'banner', memberNoBanner);

        $('#form.member-profile-form').clickOrTouch('#button-clear-banner', function(e) {
            e.preventDefault();
            $('#member_account_banner_thumb').attr('src', memberNoBanner);
            $('#member_account_banner').val('');
        });
    }

    $('#form.member-profile-form').find('input[type="checkbox"]').iCheck({
    	checkboxClass: 'icheckbox_minimal-custom',
    	radioClass: 'iradio_minimal-custom',
    	increaseArea: '20%' // optional
    });

    // if ($('textarea[name=\'member_account_description\']').length) {
    //     //  to-do: add new mobile-friendly rich text editor
    // }

    $('#form.member-profile-form').on('change', 'select[name=\'member_country_id\']', function() {
    	$.ajax({
    		url: 'country&country_id=' + this.value,
    		dataType: 'json',
    		beforeSend: function() {
    			$('select[name=\'member_country_id\']').after('<span class="wait icon"><i class="fa fa-spin fa-circle-o-notch icon-spin"></i></span>');
    		},
    		complete: function() {
    			$('.wait').fadeOut(300).remove();
    		},
    		success: function(json) {
    			html = '<option value="">' + textSelectZone + '</option>';

    			if (typeof json['zone'] !== 'undefined' && json['zone'] != '') {
    				for (i = 0; i < json['zone'].length; i++) {
            			html += '<option value="' + json['zone'][i]['zone_id'] + '"';

    					if (json['zone'][i]['zone_id'] == memberZoneId) {
    	      				html += ' selected="selected"';
    	    			}

    	    			html += '>' + json['zone'][i]['name'] + '</option>';
    				}
    			} else {
    				html += '<option value="0" selected="selected">' + textNone + '</option>';
    			}

    			$('select[name=\'member_zone_id\']').html(html);
    		},
    		error: handleError
    	});
    });

    function uploadProfileImage(targetId, thumbId, imgInput, type, no_image) {
        var $target = $('#' + targetId);
        var $thumb = $('#' + thumbId);
        var $image = $('input[name=\'' + imgInput + '\']');

        //console.log(targetId, thumbId, imgInput, type)

        new AjaxUpload($target, {
            action: 'upload-image&type=' + type,
        	name: 'file',
        	autoSubmit: true,
        	responseType: 'json',
        	onSubmit: function(file, extension) {
                $('#' + targetId + '-error').fadeOut(300).remove();
                $thumb.css('opacity', '0.5');
                $thumb.after('<span class="wait icon"><i class="fa fa-spin fa-circle-o-notch icon-spin"></i></span>');
                $target.prop('disabled', true);
        	},
        	onComplete: function(file, json) {
        		$target.prop('disabled', false);
                $thumb.animate({'opacity': '1'}, 300, 'swing');

        		if (json['success']) {
        			$image.val(json['filename']);
        			$thumb.attr('src', json['thumb']); // .after('<p><span class="success"><i class="fa fa-check-circle-o"></i> ' + json['success'] + '</span></p>');
        		}

        		if (json['error']) {
        			$image.val('');
        			$thumb.attr('src', no_image).after('<p id="' + targetId + '-error"><span class="error"><i class="fa fa-exclamation-triangle"></i> ' + json['error'] + '</span></p>');
        		}

    			setTimeout(function() {
                    $thumb.siblings('.wait').fadeOut(300).remove();
    			}, 300);
        	}
        });
    }

    if ($('#member_account_image_browse').length || $('#member_account_banner_browse').length) {
        function image_upload(field, thumb) {
        	$('#dialog').remove();
        	// #content
        	$('.container-center').prepend('<div id="dialog"><iframe src="filemanager&field=' + encodeURIComponent(field) + '" style="padding:0; margin: 0; display: block; width: 100%; height: 100%;" frameborder="no" scrolling="auto"></iframe></div>');

        	$('#dialog').dialog({
        		title: textImageManager,
                open: function (e, ui) {
                    setTimeout(function() {
                        $('.ui-widget-overlay').clickOrTouch(null, function () {
                            $(this).siblings('.ui-dialog').find('.ui-dialog-content').dialog('close');
                        });
                    }, 2000);
                },
        		close: function (event, ui) {
        			if ($('#' + field).val()) {
        				$.ajax({
        					url: 'filemanager-image&field=' + encodeURIComponent(field) + '&image=' + encodeURIComponent($('#' + field).val()),
        					dataType: 'text',
        					success: function(text) {
        						$('#' + thumb).replaceWith('<img src="' + text + '" alt="" id="' + thumb + '" class="thumb" />');
        					}
        				});
        			}
        		},
        		width: '85%',
        		height: 540,
        		resizable: false,
        		modal: true,
                position: { my: "center top", at: "center top+10", of: ".container-page" }
        	});
        };
    }

    if ($('input[name=\'member_custom_field_06\']').length) {
    	if ($('#embed-settings-customcolor').val() == 'true') {
    		$('#embed-settings-hex').show();
    	} else {
    		$('input[name^=\'embed_settings_hex\']').val('');
    		$('#embed-settings-hex').hide();
    	}

    	$('.enable-disable-buttons').clickOrTouch('a', function () {
    		var data_value = $(this).attr('data-value');
            var selected_class = data_value == 'true' ? 'button_yes' : 'button_no';
            $(this).removeClass('button_cancel');
    		$(this).addClass(selected_class);
    		$(this).siblings('.button').addClass('button_cancel');
    		$(this).nextAll('input').val(data_value);

            if ($(this).attr('data-trigger') == 'embed-settings-hex') {
                if (data_value == 'true') {
                    $('#embed-settings-hex').show();
                } else {
                    $('input[name^=\'embed_settings_hex\']').val('');
                    $('input[name^=\'embed_settings_hex\']').css({'backgroundColor': '#fafafa', 'borderColor': '#cfcfcf'});
                    $('input[name^=\'embed_settings_hex\']').each(function() {
                        $(this).spectrum('set', '');
                    });
            		$('#embed-settings-hex').hide();
                }
            }
    	});

        $(document).ready(function() {
        	$('input[name^=\'embed_settings_hex\']').each(function() {
                $(this).spectrum({
                    color: this.value,
                    preferredFormat: "hex",
                    // showInput: true,
                    allowEmpty: true,
                    chooseText: "SELECT",
                    cancelText: "cancel",
                    showPaletteOnly: true,
                    togglePaletteOnly: true,
                    togglePaletteMoreText: 'MORE',
                    togglePaletteLessText: 'less',
                    hideAfterPaletteSelect: true,
                    palette: [
                        ["#000","#444","#666","#999","#ccc","#eee","#f3f3f3","#fff"],
                        ["#f00","#f90","#ff0","#0f0","#0ff","#00f","#90f","#f0f"],
                        ["#c00","#e69138","#f1c232","#6aa84f","#45818e","#3d85c6","#674ea7","#a64d79"],
                        ["#900","#b45f06","#bf9000","#38761d","#134f5c","#0b5394","#351c75","#741b47"],
                        ["#600","#783f04","#7f6000","#274e13","#0c343d","#073763","#20124d","#4c1130"]
                    ],
                    clickoutFiresChange: false,
                    change: function (color) {
                        var hexColor = color.toHex();
                        $(this).val(hexColor);
                        $(this).css({'backgroundColor': '#' + hexColor, 'borderColor': '#' + hexColor});
                    }
                });

                $(this).show();

                $(this).clickOrTouch(null, function() {
                    $(this).spectrum('toggle');
                    return false;
                });

                $(this).on('change', function() {
                    $(this).spectrum('set', this.value);
                    var hexColor = $(this).spectrum('get').toHex();
                    $(this).val(hexColor);
                    $(this).css({'backgroundColor': '#' + hexColor, 'borderColor': '#' + hexColor});
                });
            });
        });
    }
}

// Notifications
if ($('#form.member-notify-form').length) {
    $('.enable-disable-buttons').clickOrTouch('a', function () {
        var data_value = $(this).attr('data-value');
        var selected_class = data_value == 'true' || data_value == '1' ? 'button_yes' : 'button_no';
        $(this).removeClass('button_cancel');
        $(this).addClass(selected_class);
        $(this).siblings('.button').addClass('button_cancel');
        $(this).nextAll('input').val(data_value);
    });
}

// Order Info
if (typeof orderNo !== 'undefined' && orderNo.length) {
    $('.order-info-page .my-order #history').clickOrTouch('.pagination a', function() {
    	$('#history').load(this.href);
    	return false;
    });

    $('.order-info-page .my-order #history').load('account-order-history&order_no=' + orderNo);

    if (salesHistoryEnabled !== 'undefined' && salesHistoryEnabled) {
        $('.order-info-page .my-order').clickOrTouch('#button-history', function() {
        	$.ajax({
        		url: 'account-order-history&order_no=' + orderNo,
        		type: 'post',
        		dataType: 'html',
        		data: 'emailed=' + encodeURIComponent($('input[name=\'emailed\']:checked').length ? 1 : 0) + '&comment=' + encodeURIComponent($('textarea[name=\'comment\']').val()),
        		beforeSend: function() {
        			$('.success, .warning').remove();
        			$('#button-history').prop('disabled', true);
        			$('#history').append('<div class="attention information loading"><p>' + textWait + '</p><span class="icon"><i class="fa fa-spin fa-circle-o-notch icon-spin"></i></span></div>');
        		},
        		complete: function() {
        			$('#button-history').prop('disabled', false);
        			$('.loading').remove();
        		},
        		success: function(html) {
        			$('#history').html(html);
        			$('textarea[name=\'comment\']').val('');
        		},
                error: handleError
        	});
        });
    }
}

// Product List, Product Download, Return List, Review List
function submit_form(action) {
	$('#form').attr('action', action);

    if (action.indexOf('delete', 1) != -1 || action.indexOf('expire', 1) != -1) {
        if (typeof textConfirmDelete !== 'undefined' && textConfirmDelete.length) {
    		if (!confirm(textConfirmDelete)) {
    			return false;
    		} else {
    			$('#form').trigger('submit');
    		}
        } else {
            return false;
        }
	} else {
		$('#form').trigger('submit')
	}
}

if ($('#form.product-download-form').length) {
    new AjaxUpload('#button-upload', {
    	action: 'upload-file-member',
    	name: 'file',
    	autoSubmit: true,
    	responseType: 'json',
    	onSubmit: function(file, extension) {
    		$('#button-upload').after('<img src="catalog/view/image/loading.gif" class="loading" style="padding-left: 5px;" />');
    		$('#button-upload').prop('disabled', true);
    	},
    	onComplete: function(file, json) {
    		$('#button-upload').prop('disabled', false);

    		if (json['success']) {
    			alert(json['success']);

    			$('input[name=\'filename\']').val(json['filename']);
    			$('input[name=\'mask\']').val(json['mask']);
    		}

    		if (json['error']) {
    			alert(json['error']);
    		}

    		$('.loading').remove();
    	}
    });
}

// Product Viewed
function filterProductViewed() {
	var url = 'account-listings-viewed';

	var filter_name = $('input[name=\'filter_name\']').val();

	if (filter_name) {
		url += '&filter_name=' + encodeURIComponent(filter_name);
	}

	var filter_model = $('input[name=\'filter_model\']').val();

	if (filter_model) {
		url += '&filter_model=' + encodeURIComponent(filter_model);
	}

	var filter_status = $('select[name=\'filter_status\']').val();

	if (filter_status != '*') {
		url += '&filter_status=' + encodeURIComponent(filter_status);
	}

	location = url;
}

if ($('#form.product-viewed-form').length) {
    $('#form.product-viewed-form input[name=\'filter_name\']').autocomplete({
    	delay: 0,
    	source: function(request, response) {
    		$.ajax({
    			url: 'autocomplete-listing&filter_name=' +  encodeURIComponent(request.term),
    			dataType: 'json',
    			success: function(json) {
    				response($.map(json, function(item) {
    					return {
    						label: item.name,
    						value: item.product_id
    					}
    				}));
    			}
    		});
    	},
    	select: function(event, ui) {
    		$('input[name=\'filter_name\']').val(ui.item.label);

    		return false;
    	},
    	focus: function(event, ui) {
          	return false;
       	}
    });

    $('#form.product-viewed-form input[name=\'filter_model\']').autocomplete({
    	delay: 0,
    	source: function(request, response) {
    		$.ajax({
    			url: 'autocomplete-listing&filter_model=' +  encodeURIComponent(request.term),
    			dataType: 'json',
    			success: function(json) {
    				response($.map(json, function(item) {
    					return {
    						label: item.model,
    						value: item.product_id
    					}
    				}));
    			}
    		});
    	},
    	select: function(event, ui) {
    		$('input[name=\'filter_model\']').val(ui.item.label);

    		return false;
    	},
    	focus: function(event, ui) {
          	return false;
       	}
    });
}

// Product List
if ($('#form.account-product-list-form').length) {
    $('.my-listings').clickOrTouch('#copy-listing', function(e) {
        e.preventDefault();
        var action = $(this).attr('href');
        $('#form').attr('action', action);
        $('#form').trigger('submit');
    });

    $('.my-listings').clickOrTouch('#manage-images', function(e) {
        e.preventDefault();
        e.stopPropagation();
        manage_images();
    });

    $('.my-listings').clickOrTouch('.submit-form', function(e) {
        e.preventDefault();
        var action = $(this).attr('href');
        submit_form(action);
    });

    // Checkboxes
    $('#form.account-product-list-form input[type="checkbox"]').iCheck({
		checkboxClass: 'icheckbox_minimal-custom',
		radioClass: 'iradio_minimal-custom',
		increaseArea: '40%'
	});

    $('#form.account-product-list-form').clickOrTouch('tr:not(.filter) .image', function(e) {
        $(this).closest('tr').find('input[type=\'checkbox\']').iCheck('toggle');
    });

    $('#form.account-product-list-form input[name*=\'selected\']').on('ifChanged', function(e) {
        $(e.target).trigger('change');
        $(this).closest('tr').toggleClass('active');
    });

    $('#check-select-all').on('ifChanged', function(e) {
        $('input[name*=\'selected\']').iCheck('toggle');
    });

    // Preview / Quickview
    $('#form.account-product-list-form a.button_quickview').colorbox({
        rel: $(this).attr('rel'),
        opacity: 0.4,
        transition: "none",
        fixed: true,
        innerWidth: "100%",
        maxWidth: "100%",
        innerHeight: "80%",
        current: "",
        previous: "<i class='fa fa-chevron-left'></i>",
        next: "<i class='fa fa-chevron-right'></i>",
        close: "<i class='fa fa-times'></i>",
        onOpen: function() {
            $("#colorbox").css("opacity", 0);
        },
        onComplete: function() {
            $(this).resize();
            $("#colorbox").animate({"opacity": 1}, 300, 'swing');
        }
    });

    // Filter Product List
    $('#form.account-product-list-form').off('keydown', 'input'); // remove generic keydown event handler set for all #form elements in main.js

    $('#form.account-product-list-form .filter').on('keydown', 'input', function(e) {
    	if (e.keyCode == 13) {
    		filterProductList();
    	}
    });

    $('#form.account-product-list-form').clickOrTouch('#filter-product-list', function(e) {
        e.preventDefault();
        filterProductList();
    });

    function filterProductList() {
    	var url = $('#form.account-product-list-form').attr('action');

    	var filter_name = $('input[name=\'filter_name\']').val();

    	if (filter_name) {
    		url += '?filter_name=' + encodeURIComponent(filter_name);
    	} else {
            url += '?filter_name=';
        }

    	var filter_price = $('input[name=\'filter_price\']').val();

    	if (filter_price) {
    		url += '&filter_price=' + encodeURIComponent(filter_price);
    	}

    	var filter_type = $('select[name=\'filter_type\']').val();

    	if (filter_type != '*') {
    		url += '&filter_type=' + encodeURIComponent(filter_type);
    	}

    	var filter_status = $('select[name=\'filter_status\']').val();

    	if (filter_status != '*') {
    		url += '&filter_status=' + encodeURIComponent(filter_status);
    	}

    	var filter_model = $('input[name=\'filter_model\']').val();

    	if (filter_model) {
    		url += '&filter_model=' + encodeURIComponent(filter_model);
    	}

    	var filter_quantity = $('input[name=\'filter_quantity\']').val();

    	if (filter_quantity) {
    		url += '&filter_quantity=' + encodeURIComponent(filter_quantity);
    	}

    	location = url;
    }

    $('#form.account-product-list-form input[name=\'filter_name\']').autocomplete({
    	delay: 0,
    	source: function(request, response) {
    		$.ajax({
    			url: 'autocomplete-listing&filter_name=' +  encodeURIComponent(request.term),
    			dataType: 'json',
    			success: function(json) {
    				response($.map(json, function(item) {
    					return {
    						label: item.name,
    						value: item.product_id
    					}
    				}));
    			}
    		});
    	},
    	select: function(event, ui) {
    		$('input[name=\'filter_name\']').val(ui.item.label);

    		return false;
    	},
    	focus: function(event, ui) {
          	return false;
       	}
    });

    $('#form.account-product-list-form input[name=\'filter_model\']').autocomplete({
    	delay: 0,
    	source: function(request, response) {
    		$.ajax({
    			url: 'autocomplete-listing&filter_model=' +  encodeURIComponent(request.term),
    			dataType: 'json',
    			success: function(json) {
    				response($.map(json, function(item) {
    					return {
    						label: item.model,
    						value: item.product_id
    					}
    				}));
    			}
    		});
    	},
    	select: function(event, ui) {
    		$('input[name=\'filter_model\']').val(ui.item.label);

    		return false;
    	},
    	focus: function(event, ui) {
          	return false;
       	}
    });

    // Transfer Listings
    $('.my-listings').clickOrTouch('#transfer-listing', function(e) {
        e.preventDefault();

        var selectedListings = $('#form.account-product-list-form input[name*=\'selected\']:checked');

        if (selectedListings.length) {
            transferListingsPopup(selectedListings);
        } else {
            //alert(errorNotChecked);
            $('#notification').html('<div class="bg"><span class="close"><i class="fa fa-times-circle-o"></i></span><div class="widget widget-warning" style="display: none;"><h6><i class="fa fa-exclamation-triangle"></i></h6><p>' + errorNotChecked + '</p></div></div>');
            $('.widget-warning').fadeIn(600);
        }
    });

    function transferListingsPopup(listingIdsSelected) {
    	var $transferListingForm = $('#transfer-listing-form');

    	$.colorbox({
    		inline: true,
    		href: '#transfer-listing-wrapper',
            width: "100%",
            maxWidth: "480px",
            height: "80%",
            maxHeight: "320px",
    		opacity: 0.4,
    		transition: "none",
    		scrolling: false,
    		close: "<i class='fa fa-times'></i>",
    		onLoad: function() {
    			$('#cboxClose').remove();
    		},
    		onOpen: function(){
    			$("#colorbox").css("opacity", 0);
    		},
    		onComplete: function(){
    			$("#colorbox").animate({"opacity": 1}, 300, 'swing');
                $("[rel=tooltip]").tooltip().off("focusin focusout");
    		},
    		onCleanup: function() {
    			$transferListingForm.find('.warning').remove();
    		}
    	});

    	$transferListingForm.on('submit', function(e) {
    		e.preventDefault();

        	if (!confirm(textConfirmTransfer)) {
        		return false;
        	}

            var url = $(this).attr('action');
            var data = $(this).serialize() + '&' + listingIdsSelected.serialize();

    		$.ajax({
    			url: url,
    			type: 'post',
    			data: data,
    			dataType: 'json',
    			beforeSend: function() {
    				$transferListingForm.find('input[type="submit"]').prop('disabled', true);
    				$transferListingForm.find('.button-transfer').hide();
    				$transferListingForm.find('.button-transfer').after('<a class="button button-wait button_alt icon"><i class="fa fa-spin fa-circle-o-notch icon-spin"></i> ' + textWait + '</a>');
    				$transferListingForm.find('.warning').remove();
    			},
    			complete: function() {
    				$transferListingForm.find('input[type="submit"]').prop('disabled', false);
    				$transferListingForm.find('.button-transfer').show();
    				$transferListingForm.find('.button-wait').remove();
    				$.colorbox.resize();
    			},
    			success: function(json) {
    				if (json['status']) {
    					// $transferListingForm.find('.content').prepend('<div class="success"><p>' + json['message'] + '</p><span class="icon"><i class="fa fa-check"></i></span></div>');
    					$("#colorbox, #cboxOverlay").animate({"opacity": 0}, 300, 'swing');
    					$('#notification').html('<div class="bg"><span class="close"><i class="fa fa-times-circle-o"></i></span><div class="widget widget-success" style="display: none;"><h6><i class="fa fa-check"></i></h6><p>' + json['message'] + '</p></div></div>');
    					$('.widget-success').fadeIn(600);

    					setTimeout(function() {
    						$.colorbox.close();
                            if (json['redirect']) {
                                location = json['redirect'];
                            } else {
                                location.reload();
                            }
    					}, 2000);
    				} else {
    					$transferListingForm.find('.content').prepend('<div class="warning"><p>' + json['message'] + '</p><span class="icon"><i class="fa fa-exclamation-triangle"></i></span></div>');
    				}
    			},
    			error: handleError
    		});
    	});

    	$transferListingForm.on('keydown', 'input', function(e) {
    		if (e.keyCode == 13) {
    			$transferListingForm.trigger('submit');
    		}
    	});
    }


    // Enable/Disable Listings
    $('#form.account-product-list-form .enable-disable-buttons').clickOrTouch('a', function () {
        var this_button = $(this);
        var other_button = $(this).siblings('.button');
        var data_value = $(this).attr('data-value');
        var selected_class = data_value == 'true' || data_value == '1' ? 'button_yes' : 'button_no';
        var action = data_value == 'true' || data_value == '1' ? $('#enable-listings').attr('href') : $('#disable-listings').attr('href');
        var selected_listing = $(this).closest('tr').find('input[name*=\'selected\']');

        this_button.removeClass('button_cancel');
        this_button.addClass(selected_class);
        this_button.children('span').removeClass('hidden');
        other_button.addClass('button_cancel');
        other_button.children('span').addClass('hidden');
        selected_listing.prop('checked', true);

        // submit_form(action);
        // ajax call...
        $.ajax({
            url: action + '&response_type=json',
            type: 'post',
            data: selected_listing.serialize(),
            dataType: 'json',
            beforeSend: function() {
                this_button.hide(); // prop('disabled', true);
                other_button.hide(); // .prop('disabled', true);
                this_button.after('<a class="button button-wait button_alt icon"><i class="fa fa-spin fa-circle-o-notch icon-spin"></i> ' + textWait + '</a>');
                $('#form.account-product-list-form').find('.warning').remove();
            },
            complete: function() {
                this_button.siblings('.button-wait').remove();
                this_button.show(); // .prop('disabled', false);
                other_button.show(); // .prop('disabled', false);
            },
            success: function(json) {
                if (json['status']) {
                    $('#notification').html('<div class="bg"><span class="close"><i class="fa fa-times-circle-o"></i></span><div class="widget widget-success" style="display: none;"><h6><i class="fa fa-check"></i></h6><p>' + json['message'] + '</p></div></div>');
                }

                $('.widget-success').fadeIn(600);

                setTimeout(function() {
                    $('#notification .bg').fadeOut('slow');
                }, 1200);
            },
            error: handleError
        });
    });
}

// Images
function imageMaxWarning() {
    alert(errorMaxImages);
}

function manage_images() {
	//if(){
	//	imageMaxWarning();
	//	return;
	//}
	$('#dialog').remove();
	$('.content-page').prepend('<div id="dialog"><iframe src="filemanager" style="padding:0; margin: 0; display: block; width: 100%; height: 100%;" frameborder="no" scrolling="auto"></iframe></div>');
	$('#dialog').dialog({
		title: textImageManager,
        open: function (e, ui) {
            setTimeout(function() {
                $('.ui-widget-overlay').clickOrTouch(null, function () {
                    $(this).siblings('.ui-dialog').find('.ui-dialog-content').dialog('close');
                });
            }, 2000);
        },
		close: function (text) {},
		width: '85%',
		height: 540,
		resizable: false,
		modal: true,
        position: { my: "center top", at: "center top+10", of: ".container-page" }
	});
};

// Sales Info
$('.account-sales-info-page #history').clickOrTouch('.pagination a', function(e) {
    e.preventDefault();

	$('#history').load(this.href);
	return false;
});

if (typeof sale_id !== 'undefined' && sale_id) {
    $('.account-sales-info-page #history').load('account-sale-history&sale_id=' + sale_id);

    $('.account-sales-info-page').clickOrTouch('#button-history', function() {
        var buttonText = $(this).html();
        var order_status_id = $('select[name=\'order_status_id\']').val();
        var history_emailed = $('input[name=\'emailed\']:checked').length ? 1 : 0;
        var history_comment = $('textarea[name=\'comment\']').val();

    	$.ajax({
    		url: 'account-sale-history&sale_id=' + sale_id,
    		type: 'post',
    		dataType: 'html',
    		data: 'order_status_id=' + encodeURIComponent(order_status_id) + '&emailed=' + encodeURIComponent(history_emailed) + '&comment=' + encodeURIComponent(history_comment),
    		beforeSend: function() {
    			$('.success, .warning').remove();
    			$('#button-history').prop('disabled', true);
                $('#button-history').addClass('button-wait button_alt icon').html('<i class="fa fa-spin fa-circle-o-notch icon-spin"></i> ' + textWait);
                $('#history').append('<div class="information loading"><p>' + textWait + '</p><span class="icon"><i class="fa fa-spin fa-circle-o-notch icon-spin"></i></span></div>');
    		},
    		complete: function() {
    			$('#button-history').prop('disabled', false);
                $('#button-history').removeClass('button-wait button_alt icon').html(buttonText);
    			$('.loading').remove();
    		},
    		success: function(html) {
    			$('#history').html(html);
    			$('textarea[name=\'comment\']').val('');
    			$('#order-status').html($('select[name=\'order_status_id\'] option:selected').text());
    		},
            error: handleError
    	});
    });
}
