// filter.js
function refineListings(e) {
    e.preventDefault();

    var url = $('#button-filter').attr('href');
    var type = [];
    var filter = [];
    var refine = [];
    var jumpTo;
    var filterSearch = $('.widget-filter input[name=\'filter_search\']').val();
    var $fieldForSale = $('.widget-filter input[name=\'forsale\']:checked');
    var $fieldType = $('.widget-filter input[name^=\'type\'][type=\'checkbox\']:checked');
    var $filters = $('.widget-filter input[name^=\'filter-\'][type=\'checkbox\']:checked');

    $filters.each(function(el) {
        filter.push(this.value);
    });

    if ($fieldType.length) {
        $fieldType.each(function(el) {
            type.push(this.value);
        });
    } else if ($fieldForSale.length) {
        type.push(0, 1);
    }

    if (type.length) {
        refine.push('type=' + encodeURIComponent(type.join()));
    }

    if (filter.length) {
        refine.push('filter=' + encodeURIComponent(filter.join()));
    }

    if (filterSearch) {
        refine.push('search=' + encodeURIComponent(filterSearch));
    }

    if (refine.length) {
        url += url.indexOf('?') !== -1 ? '&' : '?';
        url += refine.join('&');
    }

    jumpTo = $('#button-filter').closest('#sidebar').parent().attr('id');

    if (typeof jumpTo != 'undefined') {
        url += '#' + jumpTo;
    }

    location = url;
}

(function() {
    $('#filterwidget').on('keydown', 'input[name=\'filter_search\']', function(e) {
        if (e.keyCode == 13) {
            refineListings(e);
        }
    });

    $('#filterwidget').clickOrTouch('#button-filter', refineListings);

    $('#filterwidget').find('input[type="checkbox"]').iCheck({
        checkboxClass: 'icheckbox_minimal-custom',
        radioClass: 'iradio_minimal-custom',
        increaseArea: '20%' // optional
    });
})();
