$(document).ready(function() {
    /* Blog Search */
    $('#blog-search').on('click', function() {
        url = 'blog-search';

        var search = $('#blog input[name=\'search\']').attr('value');

        if (search) {
            url += '&search=' + encodeURIComponent(search);
        }

        var blog_category_id = $('#blog select[name=\'blog_category_id\']').attr('value');

        if (blog_category_id > 0) {
            url += '&blog_category_id=' + encodeURIComponent(blog_category_id);
        }

        var filter_description = $('#blog input[name=\'description\']:checked').attr('value');

        if (filter_description) {
            url += '&description=true';
        }

        location = url;
    });

    $('.blog-search input[name=\'search\']').on('keydown', function(e) {
        if (e.keyCode == 13) {
            url = 'blog-search';

            var search = $('#blog input[name=\'search\']').attr('value');

            if (search) {
                url += '&search=' + encodeURIComponent(search);
            }

            location = url;
        }
    });
});
