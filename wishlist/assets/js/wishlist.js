jQuery(function ($) {

    $('.overlay').on('click', function() {
        $(this).removeClass('wishlist-show');
    });

    $('.add-to-wishlist').on('click', function() {

        $('#wishlist-adding').addClass('wishlist-show');

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
                $('#wishlist-adding').removeClass("wishlist-show");
                if(response.status === 'added') {
                    $('#wishlist-added').addClass("wishlist-show");
                } else if(response.status === 'alreadyExists') {
                    $('#wishlist-already-exists').addClass("wishlist-show");
                } else if(response.status === 'limitReached') {
                    $('#wishlist-limit-reached').addClass("wishlist-show");
                }
            }
        });
    });

    $('.wishlist-remove').on('click', function() {
        var productId = $(this).data('id');

        $.ajax({
            url: ajax_object.ajax_url,
            type: 'post',
            dataType: 'json',
            data: {
                productId: productId,
                action: 'removefromwishlist'
            },
            success: function (response) {
                console.log(123);
            }
        });
    })
});