jQuery(document).ready(function ($) {
    function updateRelatedPostsInput(relatedPosts) {
        var filteredPosts = relatedPosts.filter(function (postId) {
            return postId.trim() !== '';
        });
        $('input#mra_related_posts').val(filteredPosts.join(','));
        console.log('Updated related posts:', $('input#mra_related_posts').val());
    }

    $('#mra_modal').dialog({
        autoOpen: false,
        modal: true,
        width: 600,
        appendTo: "body",
        open: function () {
            $('.ui-widget-overlay').bind('click', function () {
                $('#mra_modal').dialog('close');
            });
        },
        buttons: {
            "Fechar": function () {
                $(this).dialog("close");
            },
            "Selecionar": function () {
                var relatedPosts = $('input#mra_related_posts').val().split(',').filter(Boolean);
                $('#mra_search_results input:checked').each(function () {
                    var postId = $(this).data('post-id').toString();
                    var postTitle = $(this).data('post-title');
                    if (!relatedPosts.includes(postId)) {
                        relatedPosts.push(postId);
                        $('#mra_related_posts_list').append('<li data-post-id="' + postId + '"><a href="#" class="mra-remove-post"><span>x</span></a><a class="mra-remove-post-item" href="' + mraAjax.ajaxurl + '/?p=' + postId + '" target="_blank">' + postTitle + '</a></li>');
                    }
                });
                updateRelatedPostsInput(relatedPosts);
                $(this).dialog("close");
            }
        }
    });

    $('#mra_open_modal').on('click', function () {
        $('#mra_modal').dialog('open');
    });

    $('#mra_search_button').on('click', function () {
        var search = $('#mra_search').val();
        if (search.length < 2) {
            $('#mra_search_results').html('');
            return;
        }
        $.ajax({
            url: mraAjax.ajaxurl,
            method: 'GET',
            data: {
                action: 'mra_search_posts',
                search: search
            },
            success: function (response) {
                $('#mra_search_results').html(response);
            }
        });
    });

    $(document).on('click', '.mra-remove-post', function () {
        var postId = $(this).closest('li').data('post-id').toString();
        var relatedPosts = $('input#mra_related_posts').val().split(',').filter(Boolean);
        relatedPosts = relatedPosts.filter(function (id) {
            return id != postId;
        });
        updateRelatedPostsInput(relatedPosts);
        $(this).closest('li').remove();
    });

    $('#mra_clear_list').on('click', function () {
        $('input#mra_related_posts').val('');
        $('#mra_related_posts_list').html('');
    });

    $('#post').on('submit', function () {
        var relatedPosts = $('input#mra_related_posts').val();
    });

    // Batch import process
    var offset = 0;
    var processing = false;

    $('#mra_start_import').on('click', function (e) {
        e.preventDefault();
        if (processing) return;
        processing = true;
        offset = 0;
        $('#mra_import_status').html('<p>Starting import...</p>');
        processBatch();
    });

    $('#mra_reset_related_posts').on('click', function (e) {
        e.preventDefault();
        if (processing) return;
        processing = true;
        $('#mra_reset_status').html('<p>Resetting related posts...</p>');
        $.post(mraAjax.ajaxurl, {
            action: 'mra_reset_related_posts'
        }, function (response) {
            if (response.success) {
                $('#mra_reset_status').html('<p>All related posts have been reset.</p>');
            } else {
                $('#mra_reset_status').html('<p>Failed to reset related posts. ' + (response.data ? response.data.message : '') + '</p>');
            }
            processing = false;
        });
    });

    function processBatch() {
        $.post(mraAjax.ajaxurl, {
            action: 'mra_import_baw_related_posts_batch',
            offset: offset
        }, function (response) {
            if (response.success) {
                if (response.data.completed) {
                    $('#mra_import_status').append('<p>Import completed.</p>');
                    processing = false;
                } else {
                    offset = response.data.offset;
                    $('#mra_import_status').append('<p>Processed batch up to offset ' + offset + '...</p>');
                    processBatch();
                }
            } else {
                $('#mra_import_status').append('<p>Error occurred during import.</p>');
                processing = false;
            }
        });
    }
});
