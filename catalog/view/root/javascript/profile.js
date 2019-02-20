// Profile Page
var member_id = $('input[name=\'profile_id\']').val();

if (member_id) {
    var profileOffsetTop = 80;

    $('#member-listings-jump').clickOrTouch(null, function(e) {
        e.preventDefault();
        scrollToSection('#member-listings', profileOffsetTop);
    });

    $("#scrollreview").clickOrTouch(null, function(e) {
        e.preventDefault();
        $('a[href=\'#tab-review\']').trigger('click');
        scrollToSection('#review-title', profileOffsetTop);
    });

    $("#scrolldiscuss").clickOrTouch(null, function(e) {
        e.preventDefault();
        $('a[href=\'#tab-discussion\']').trigger('click');
        scrollToSection('#discussion-title', profileOffsetTop);
    });

    // reviews
    if ($('#review').length) {
        $('#review').load('reviews-member&member_id=' + member_id);
    }

    $('#review').clickOrTouch('.pagination a', function(e) {
        e.preventDefault();
    	$('#review').load(this.href);
        scrollToSection('#tabs', profileOffsetTop);
    	return false;
    });

    $('#tab-review').clickOrTouch('#button-review', function() {
        var reviewName = $('#tab-review input[name=\'name\']').val();
        var reviewText = $('#tab-review textarea[name=\'text\']').val();
        var reviewRating = $('#tab-review input[name=\'rating\']:checked').val() || '';

    	$.ajax({
    		url: 'review-member&member_id=' + member_id,
    		type: 'post',
    		dataType: 'json',
    		data: 'name=' + encodeURIComponent(reviewName) + '&text=' + encodeURIComponent(reviewText) + '&rating=' + encodeURIComponent(reviewRating),
    		beforeSend: function() {
    			$('#tab-review .success, #tab-review .warning').remove();
    			$('#button-review').prop('disabled', true);
    			$('#review-title').after('<div class="information wait"><p>' + textWait + '</p><span class="icon"><i class="fa fa-spin fa-circle-o-notch icon-spin"></i></span></div>');
    		},
    		complete: function() {
    			$('#button-review').prop('disabled', false);
    			$('.wait').remove();
    		},
    		success: function(data) {
    			if (data['error']) {
    				$('#review-title').after('<div class="warning"><p>' + data['error'] + '</p><span class="close"><i class="fa fa-times"></i></span><span class="icon"><i class="fa fa-exclamation-triangle"></i></span></div>');
    			}

    			if (data['success']) {
    				$('#review-title').after('<div class="success"><p>' + data['success'] + '</p><span class="close"><i class="fa fa-times"></i></span><span class="icon"><i class="fa fa-check"></i></span></div>');
    				$('#tab-review textarea[name=\'text\']').val('');
    				$('#tab-review input[name=\'rating\']').prop('checked', false);
    			}

                //$('#review').load('reviews-member&member_id=' + member_id);
                scrollToSection('#review-title', profileOffsetTop);
    		},
            error: handleError
    	});
    });

    // discussion
    if ($('#discussion').length) {
        $('#discussion').load('discussion-member&member_id=' + member_id);
    }

    $('#discussion').clickOrTouch('.pagination a', function(e) {
        e.preventDefault();
    	$('#discussion').load(this.href);
    	scrollToSection('#tabs', profileOffsetTop);
    });

    $('#tab-discussion').clickOrTouch('#button-discussion', function() {
        var data = $('#tab-discussion input, #tab-discussion textarea').serialize();

    	$.ajax({
    		url: 'discuss-member&member_id=' + member_id,
    		type: 'post',
    		dataType: 'json',
    		data: data,
    		beforeSend: function() {
    			$('#tab-discussion .success, #tab-discussion .warning').remove();
    			$('#button-discussion').prop('disabled', true);
    			$('#discussion-title').after('<div class="information wait"><p>' + textWait + '</p><span class="icon"><i class="fa fa-spin fa-circle-o-notch icon-spin"></i></span></div>');
    		},
    		complete: function() {
    			$('#button-discussion').prop('disabled', false);
    			$('.information').remove();
                onloadReCaptcha();
    		},
    		success: function(data) {
                var scrollToId = '';

    			if (data['error']) {
    				$('#discussion-title').after('<div class="warning"><p>' + data['error'] + '</p><span class="close"><i class="fa fa-times"></i></span><span class="icon"><i class="fa fa-exclamation-triangle"></i></span></div>');
                    scrollToId = '#discussion-title';
    			}

    			if (data['success']) {
    				$('#discussion-title').after('<div class="success"><p>' + data['success'] + '</p><span class="close"><i class="fa fa-times"></i></span><span class="icon"><i class="fa fa-check"></i></span></div>');
    				$('textarea[name=\'text\']').val('');
                    scrollToId = '#tabs';
    			}

    			$('#discussion').load('discussion-member&member_id=' + member_id);

                setTimeout(function() {
                    scrollToSection(scrollToId, profileOffsetTop);
                }, 1500);
    		}
    	});
    });

}
