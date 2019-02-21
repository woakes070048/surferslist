var $locationForm = $('#location-form');
var redirectUrl = $('#location-form').attr('action');
var countryId = $('#location-form select[name=\'country\']').val();
var countryCode = $('#location-form select[name=\'country\'] option:selected').attr('data-code');
var zoneId = $('#location-form select[name=\'zone\']').val();
var zoneCode = $('#location-form select[name=\'zone\'] option:selected').attr('data-code');
var countryMap = $('#location-form input[name=\'country_map\']').val();
var countryColors = $('#location-form input[name=\'country_colors\']').val();
var zoneColors = $('#location-form input[name=\'zone_colors\']').val();
var textZone = $('#location-form select[name=\'country\'] option[value=\'0\']').text();

if ($locationForm.length) {
	$locationForm.on('change', 'select[name=\'country\']', function() {
    	var country_id = $(this).val();
		var country_iso_code_2 = $(this).attr('data-code');
		var country_href = $(this).attr('data-url');

    	if (country_id > 0) {
    		$('#location-form select[name=\'zone\']').prop('disabled', false);
    	} else {
    		$('#location-form select[name=\'zone\']').prop('disabled', true);
    	}

    	$.ajax({
    		url: 'country?country_id=' + this.value,
    		dataType: 'json',
    		beforeSend: function() {
    			$('select[name=\'country\']').before('<span class="wait"><i class="fa fa-spin fa-circle-o-notch icon-spin"></i></span>');
    		},
    		complete: function() {
    			$('.wait').remove();
    		},
    		success: function(json) {
    			html = '<option value="0">' + textZone + '</option>';

    			if (typeof json['zone'] != 'undefined') {
    				for (i = 0; i < json['zone'].length; i++) {
            			html += '<option value="' + json['zone'][i]['zone_id'] + '"';

    					if (json['zone'][i]['zone_id'] == zoneId) {
    	      				html += ' selected="selected"';
    	    			}

    	    			html += '>' + json['zone'][i]['name'] + '</option>';
    				}
    			}

    			$('select[name=\'zone\']').html(html);

				if (country_id > 0) {
					$('select[name=\'zone\']').parent('.formbox').show();
				} else {
					$('select[name=\'zone\']').parent('.formbox').hide();
				}
    		},
    		error: handleError
    	});
    });

	$locationForm.on('change', 'select[name=\'zone\']', function() {
		var zone_id = $(this).val();

		if (zone_id > 0) {
			$('input[name=\'location\']').parent('.formbox').show();
		} else {
			$('input[name=\'location\']').parent('.formbox').hide();
		}
	});

	$locationForm.on('keydown', 'input[name=\'location\']', function(e) {
		if (e.keyCode == 13) {
			$locationForm.submit();
		}
	});
}

$('#vmap').vectorMap({
	map: countryMap.length ? countryMap : 'world_en',
	backgroundColor: null,
	borderColor: '#ffffff',
	color: '#0d8f91',
	colors: countryMap.length ? JSON.parse(zoneColors) : JSON.parse(countryColors),
	selectedColor: '#ffcc00',
	selectedRegion: countryMap.length ? zoneCode : countryCode,
	enableZoom: true,
	showTooltip: true,
	hoverColor: '#76cdd8',
	hoverOpacity: 0,
	// values: enabled_countries,
	// scaleColors: ['#76cdd8', '#0d8f91'],
	normalizeFunction: 'polynomial',
	onRegionClick: function(element, code, region) {
		window.location = countryMap.length
			? redirectUrl + '&country=' + countryCode + '&zone=' + code
			: redirectUrl + '&country=' + code;
	}
});
