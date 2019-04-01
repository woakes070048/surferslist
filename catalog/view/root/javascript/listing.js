// Listing Page
var listing_id = $('input[name=\'listing_id\']').val();

if (listing_id) {
    var listingOffsetTop = 80;

    function quantityMore() {
        var quantity = parseInt($('#quantity').val());

        if (quantity > 0) {
            $('#quantity').val(quantity + 1);
        }
        return false;
    }

    function quantityLess() {
        var quantity = parseInt($('#quantity').val());

        if (quantity > 1) {
            $('#quantity').val(quantity - 1);
        }
        return false;
    }

    $('#buttons-item').clickOrTouch('#button-cart', function() {
    	$.ajax({
    		url: 'cart-add',
    		type: 'post',
    		data: $('.right-item input[type=\'text\'], .right-item input[type=\'hidden\'], .right-item input[type=\'radio\']:checked, .right-item input[type=\'checkbox\']:checked, .right-item select, .right-item textarea'),
    		dataType: 'json',
    		success: function(json) {
    			$('.success, .warning, .attention, information, .error').remove();

    			if (json['error']) {
    				if (json['error']['option']) {
    					for (i in json['error']['option']) {
    						$('#option-' + i).after('<span class="error"><i class="fa fa-exclamation-triangle"></i> ' + json['error']['option'][i] + '</span>');
    						json['error'] += '<br />' + json['error']['option'][i];
    					}
    				}

    				$('#notification').html('<div class="bg"><span class="close"><i class="fa fa-times-circle-o"></i></span><div class="widget widget-warning" style="display: none;"><h6><i class="fa fa-exclamation-triangle"></i></h6><p>' + json['error'] + '</p></div></div>');
    			}

    			if (json['success']) {
    				$('#notification').html('<div class="bg"><span class="close"><i class="fa fa-times-circle-o"></i></span><div class="widget widget-success" style="display: none;"><h6><i class="fa fa-check"></i></h6><p>' + json['success'] + '</p></div></div>');
    			}

                $('#notification .widget').fadeIn('slow');

                setTimeout(function() {
                    $('#notification .bg').fadeOut('slow');

                    if (json['success']) {
                        $('#cart-total').html(json['total']);
                        $('#cart .heading').click();
                        scrollToSection('#cart', 0);
                    }
                }, 2000);
    		}
    	});
    });

    if (typeof option_ids_file_type !== 'undefined' && option_ids_file_type.length) {
        $.foreach(option_ids_file_type, function(index, value) {
            new AjaxUpload('#button-option-' + value, {
            	action: 'upload-file-listing',
            	name: 'file',
            	autoSubmit: true,
            	responseType: 'json',
            	onSubmit: function(file, extension) {
            		$('#button-option-' + value).after('<img src="catalog/view/image/loading.gif" class="loading" style="padding-left: 5px;" />');
            		$('#button-option-' + value).prop('disabled', true);
            	},
            	onComplete: function(file, json) {
            		$('#button-option-' + value).prop('disabled', false);

            		$('.error').remove();

            		if (json['success']) {
            			alert(json['success']);

            			$('input[name=\'option[' + value + ']\']').val(json['file']);
            		}

            		if (json['error']) {
            			$('#option-' + value).after('<span class="error">' + json['error'] + '</span>');
            		}

            		$('.loading').remove();
            	}
            });
        });
    }

    $('#question').clickOrTouch('.pagination a', function(e) {
        e.preventDefault();
    	$('#question').load(this.href);
    	scrollToSection('#tabs', listingOffsetTop);
    });

    $('#question').load('discussion-listing?listing_id=' + listing_id);

    $('#tab-question').clickOrTouch('#button-question', function() {
        var data = $('input, textarea').serialize();

    	$.ajax({
    		url: 'discuss-listing?listing_id=' + listing_id,
    		type: 'post',
    		dataType: 'json',
    		data: data,
    		beforeSend: function() {
    			$('#tab-question .success, #tab-question .warning').remove();
    			$('#button-question').prop('disabled', true);
    			$('#question-title').after('<div class="information"><p>' + textWait + '</p><span class="icon"><i class="fa fa-spin fa-circle-o-notch icon-spin"></i></span></div>');
    		},
    		complete: function() {
    			$('#button-question').prop('disabled', false);
    			$('.information').remove();
                onloadReCaptcha();
    		},
    		success: function(data) {
                var scrollToId = '';

    			if (data['error']) {
    				$('#question-title').after('<div class="warning"><p>' + data['error'] + '</p><span class="close"><i class="fa fa-times"></i></span><span class="icon"><i class="fa fa-exclamation-triangle"></i></span></div>');
                    scrollToId = '#question-title';
    			}

    			if (data['success']) {
    				$('#question-title').after('<div class="success"><p>' + data['success'] + '</p><span class="close"><i class="fa fa-times"></i></span><span class="icon"><i class="fa fa-check"></i></span></div>');
    				$('textarea[name=\'text\']').val('');
                    scrollToId = '#tabs';
    			}

    			$('#question').load('discussion-listing?listing_id=' + listing_id);

                setTimeout(function() {
                    scrollToSection(scrollToId, listingOffsetTop);
                }, 1500);
    		}
    	});
    });

    if (typeof option_timepicker !== 'undefined' && option_timepicker) {
    	$('.date').datepicker({dateFormat: 'yy-mm-dd'});

    	$('.datetime').datetimepicker({
    		dateFormat: 'yy-mm-dd',
    		timeFormat: 'h:m'
    	});

    	$('.time').timepicker({timeFormat: 'h:m'});
    }

    $('#scroll-to-discussion').clickOrTouch(null, function(e) {
        e.preventDefault();
        $('a[href=\'#tab-question\']').trigger('click');
        scrollToSection('#question-title', listingOffsetTop);
    });

    function getZoomConfig(imgUrl) {
        return {
            url: imgUrl,
            callback: function() {
                $(this).on('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                });
            }
        }
    }

    $('.lightbox').colorbox(colorboxDefault);

    $(document).ready(function() {
        var largeImagePrimary = $('#image').attr('href');
        $('#image').zoom(getZoomConfig(largeImagePrimary));

        if ($('.images').length) {
            $('.images').on('click', '.zoom-gallery', function(e) {
                e.preventDefault();
                e.stopPropagation();

                $('#image').trigger('zoom.destroy');
                $('#image').before('<div class="loader"></div>');

                var smallImage = $(this).attr('data-small-image');
                var largeImage = $(this).attr('href');

                $('#image').attr('href', largeImage);
                $('#image + .lightbox').attr('href', largeImage);
                $('#image-small').attr('src', smallImage);

                $('.left-item .thumb').imagesLoaded(function() {
                    $('.left-item .thumb .loader').fadeOut(200, function() {
                        $(this).remove();
                    });
            	});

                $('#image').zoom(getZoomConfig(largeImage));

                scrollToSection('#image', listingOffsetTop);
            });
        }
    });

}
