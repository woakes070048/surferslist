$('input[name=\'member[member_name]\']').autocomplete({
	delay: 0,
	minLength: 2,
	source: function(request, response) {
		$.ajax({
			url: 'autocomplete-member?member_name=' +  encodeURIComponent(request.term),
			dataType: 'json',
			success: function(json) {
				response($.map(json, function(item) {
					return {
						label: item.member_name,
						value: item.member_id,
						image: item.member_image
					}
				}));
			}
		});
	},
	change: function(event, ui) {
        if (ui.item == null || ui.item == undefined) {
            $('input[name=\'member[member_name]\']').val('');
            $('input[name=\'member[member_id]\']').val('');
        }
	},
	select: function(event, ui) {
		$('input[name=\'member[member_name]\']').val(ui.item.label);
		$('input[name=\'member[member_id]\']').val(ui.item.value);
		return false;
	},
	focus: function(event, ui) {
      	return false;
   	}
}).autocomplete("instance")._renderItem = function(ul, item) {
	var thisSpanAttr = {
		style: 'background-image: url(' + item.image + ')',
		class: 'ui-selectmenu-item-icon ui-icon'
	};
	var thisSpan = $('<span></span>', thisSpanAttr);
	var thisAAttr = {
		title: item.label,
		// href : '#nogo',
		tabindex : -1,
		role : 'option'
	};
	var thisA = $('<a></a>', thisAAttr)
		.append(thisSpan)
		.append(item.label)
		.removeClass('ui-corner-all')
		.on('focus,mouseover', function() {
			$(this).parent().mouseover();
		})
		.on('blur,mouseout', function() {
			$(this).parent().mouseout();
		});
	var thisLi = $('<li></li>')
		.append(thisA)
		.attr('role', 'presentation')
		.removeClass()
		.addClass('brand-logo ui-selectmenu-hasIcon')
		.data('item.autocomplete', item)
		.on('mouseover', function() {
			$(this).removeClass('ui-state-active').addClass('ui-selectmenu-item-focus ui-state-hover');
			$(this).children().removeClass();
		})
		.on('mouseout', function() {
			$(this).removeClass('ui-selectmenu-item-focus ui-state-hover');
			$(this).children().removeClass();
		});

	ul.addClass('ui-selectmenu-menu').removeClass('ui-corner-all');

    return thisLi.appendTo(ul);
};
