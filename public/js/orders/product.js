$('body').on('click', '.viewProduct', function() {
    var orderId = $(this).attr('data-id')
    $.LoadingOverlay('show')
    $.ajax({
        type: 'get',
        dataType: 'json',
        url: orderURL + orderId + '/products',
        success: function(data) {
            var products = data.data
            $.LoadingOverlay('hide')
            var html = '';
            $.each(products, function(i, v) {
                html += '<div class="carousel-item">';
                html += '<div class="card mb-3">';
                html += '<div class="row no-gutters">';
                html += '<div class="col-md-4 offset-md-4">';
                html += '<img src="' + v.productImageFullUrl + '" class="card-img" alt="...">';
                html += '</div>';
                html += '</div>';
                html += '<div class="row">';
                html += '<div class="col-md-12">';
                html += '<div class="card-body">';
                html += '<h3 class="card-title"><b>' + v.productName + '</b><br>';
                if (v.productSalePrice > 0) {
                    html += '<small><b>' + v.productSalePrice + '';
                    html += '$</b> &nbsp;  &nbsp;<del>' + v.productPrice + '$</del> </small></h3>';
                    html += '<div class="text text-right"><b> Quantity: ' + v.quantity + '</b></div>';
                } else {
                    html += '<small><b>';
                    html += '</b> &nbsp;  &nbsp; ' + v.productPrice + '$ </small></h3>';
                    html += '<div class="text text-right"><b> Quantity: ' + v.quantity + '</b></div>';
                }
                html += '<p class="card-text">' + v.productDescription + '</p>';
                html += '<p class="card-text">';
                html += '</p>';
                html += '</div>';
                html += '</div>';
                html += '</div>';
                html += '</div>';
                html += '</div>';




            })
            $('#productModal .carousel-inner').append(html)
            $('#productModal .carousel-inner .carousel-item:first').addClass('active')
            $('#productModal').modal('show')
        },
        errors: function() {
            $.LoadingOverlay('hide')
            toastr.error('Something went wrong!')
        }
    })
})
