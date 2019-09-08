jQuery(function ($) {
    $('.add-to-wishlist').on('click', function() {

        var productId = $(this).data('id');
        $.ajax({
            url: ajax_object.ajax_url,
            type: 'post',
            dataType: 'json',
            data: {
                productId: productId,
                action: 'addtowishlist'
            },
            success: function(response) {
                console.log(response);
            }
        });
    });
});