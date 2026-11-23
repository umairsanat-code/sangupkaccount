var orderURL = baseUrl + '/orders/';
var defaultImage = baseUrl + '/img/default-avatar.svg'


$('table').on('click', 'tbody .viewBuyer', function() {
    var orderId = $(this).attr('data-id')

    $.LoadingOverlay('show')
    $.ajax({
        type: 'get',
        dataType: 'json',
        url: orderURL + orderId + '/show',
        success: function(data) {
            var orderDetails = data.data
            var orderItems = orderDetails.order_items
            var productimg = orderItems ? orderItems[0].productImageFullUrl : defaultImage
            var orderDetailssHtml = '';
            orderDetailssHtml += '<div class="card-body p-0">';
            orderDetailssHtml += '<ul class="products-list product-list-in-card pl-2 pr-2">';
            orderDetailssHtml += '<li class="item">';
            orderDetailssHtml += '<div class="product-img">';
            orderDetailssHtml += '<img src="' + productimg + '" alt="Product Image" class="img-size-50" style="border-radius: 5px">';
            orderDetailssHtml += '</div>';
            orderDetailssHtml += '<div class="product-info">';
            orderDetailssHtml += '<p class="product-title">' + orderItems[0].productName + '</p>';
            orderDetailssHtml += '<span></span></a>';
            orderDetailssHtml += '<span class="product-description" style="margin-top: -14px;">';

            // orderDetailssHtml += '' + orderItems[0].productSalePrice + ' $';
            if (orderItems[0].productSalePrice > 0) {
                orderDetailssHtml += '<p>' + orderItems[0].productSalePrice + '';
                orderDetailssHtml += '$</b> &nbsp;<del>' + orderItems[0].productPrice + '$</del> </p>';
            } else {
                orderDetailssHtml += '<p class="text-muted">' + orderItems[0].productPrice + ' $</p>';
            }

            orderDetailssHtml += '</span>';
            orderDetailssHtml += '<span class="product-description" style="margin-left: -11px;margin-top: -20px">';
            orderDetailssHtml += '<button data-id="' + orderDetails.id + '" class="btn btn-link product-title viewProduct">View Product(s) </button>';
            orderDetailssHtml += '</span>';
            orderDetailssHtml += '</div>';
            orderDetailssHtml += '</li>';
            orderDetailssHtml += '</ul>';
            orderDetailssHtml += '</div>';
            orderDetailssHtml += '<hr style="margin: 0;">';
            orderDetailssHtml += '<b class="mt-3 text-bold"><span class="dot"></span>  Buyer</b> <br>';
            orderDetailssHtml += '<div class="card-body p-0">';
            orderDetailssHtml += '<ul class="products-list product-list-in-card pl-2 pr-2">';
            orderDetailssHtml += '<li class="item" style="padding-bottom: 0px">';
            orderDetailssHtml += '<div class="product-img">';
            orderDetailssHtml += '<img src="' + orderDetails.buyer.avatarFullUrl + '" alt="Product Image" class="img-size-50 img-circle" style="height: 37px !important ;width: 37px; !important">';
            orderDetailssHtml += '</div>';
            orderDetailssHtml += '<div class="product-info" style="margin-left: 48px;">';
            orderDetailssHtml += '<p class="product-title">' + orderDetails.buyer.name + '<br><small class="product-description">' + orderDetails.buyer.phoneNumber + '</small></p>';
            orderDetailssHtml += '<span></span></a>';
            orderDetailssHtml += '</div>';
            orderDetailssHtml += '</li>';
            orderDetailssHtml += '</ul>';
            orderDetailssHtml += '</div>';
            orderDetailssHtml += '<b class="mt-3 text-bold"><span class="dot"></span> Seller</b> <br>';
            orderDetailssHtml += '<div class="card-body p-0">';
            orderDetailssHtml += '<ul class="products-list product-list-in-card pl-2 pr-2">';
            orderDetailssHtml += '<li class="item"  style="padding-bottom: 0px">';
            orderDetailssHtml += '<div class="product-img">';
            orderDetailssHtml += '<img src="' + orderDetails.seller.avatarFullUrl + '" alt="Product Image" class="img-size-50 img-circle" style="height: 37px !important ;width: 37px; !important">';
            orderDetailssHtml += '</div>';
            orderDetailssHtml += '<div class="product-info" style="margin-left: 48px;">';
            orderDetailssHtml += '<p class="product-title">' + orderDetails.seller.name + '<br><small class="product-description">' + orderDetails.seller.phoneNumber + '</small></p>';
            orderDetailssHtml += '<span></span></a>';
            orderDetailssHtml += '</div>';
            orderDetailssHtml += '</li>';
            orderDetailssHtml += '</ul>';
            orderDetailssHtml += '</div>';
            if(orderDetails.latitude) {
                orderDetailssHtml += '<b class="mt-3 text-bold"><span class="dot"></span> Location</b> <br>';
                orderDetailssHtml += '<div class="card-body p-0">';
                orderDetailssHtml += '<ul class="products-list product-list-in-card pl-2 pr-2">';
                orderDetailssHtml += '<li class="item">';
                orderDetailssHtml += '<div id="map-location" style="width:200px; height:100px; border-radius: 5px;"></div>';
                orderDetailssHtml += '</li>';
                orderDetailssHtml += '</ul>';
                orderDetailssHtml += '</div>';
            }
            orderDetailssHtml += '<b class="mt-3 text-bold"><span class="dot"></span> Order Info</b> <br>';
            orderDetailssHtml += '<div class="row" style="margin-bottom: -16px !important">';
            orderDetailssHtml += '<div class="col-md-4">';
            orderDetailssHtml += '<p><i class="fa fa-calendar"></i></p>';
            orderDetailssHtml += '</div>';
            orderDetailssHtml += '<div class="col-md-8 text-right">';
            orderDetailssHtml += '<p>' + orderDetails.orderDate + '</p>';
            orderDetailssHtml += '</div>';
            orderDetailssHtml += '</div>';
            orderDetailssHtml += '<div class="row">';
            orderDetailssHtml += '<div class="col-md-4">';
            orderDetailssHtml += '<p><i class="fa fa-tag"></i></p>';
            orderDetailssHtml += '</div>';
            orderDetailssHtml += '<div class="col-md-8 text-right">';
            orderDetailssHtml += '<p>' + orderDetails.orderTicket + '</p>';
            orderDetailssHtml += '</div>';
            orderDetailssHtml += '</div>';

            $('.status').html('<span  class = "badge badge-primary float-right">'+orderDetails.status+'</span >')
            if (orderDetails.status == 'CANCELLED' || orderDetails.status == 'COMPLETED') {
                $('.btn-cancel-aside').hide()
            } else {
                // $('.btn-cancel-aside').attr('href', orderURL + 'CANCELLED/' + orderDetails.id + '/status')
            }

            $('.seller-details').html(orderDetailssHtml)
            if(orderDetails.latitude){
                initMap(orderDetails.latitude, orderDetails.longitude)
            }
            $.LoadingOverlay('hide')
            $('body').addClass('sidebar-mini control-sidebar-slide-open')


        },
        error: function(data) {
            $.LoadingOverlay('hide')
            $('body').removeClass('sidebar-mini control-sidebar-slide-open')
            toastr.error('Something went wrong!')
        }

    });


});

$('#cancelBtn').click(function() {
    $('body').removeClass('sidebar-mini control-sidebar-slide-open')

});

function initMap(lat, long) {
    const uluru = { lat: parseFloat(lat), lng: parseFloat(long) };
    const map = new google.maps.Map(document.getElementById("map-location"), {
        zoom: 10,
        center: uluru,
        disableDefaultUI: true

    });

    const marker = new google.maps.Marker({
        position: uluru,
        map: map,
    });

}







$('body').on('dblclick', function() {
    $(this).removeClass('sidebar-mini control-sidebar-slide-open')
})

function statusStyle(param) {

    if (param == 'Active' || param == 'Approved') {
        return '<button class="btn btn-rounded-success btn-lg" style="background-color: #5cb85c;border-radius:20px;border: none;color: white;text-align: center;text-decoration: none;display: inline-block;font-size: 16px;margin: 4px 2px;">' + param + '</button';
    }
    if (param == 'Banned' || param == 'Rejected') {
        return '<button class="btn btn-rounded-success btn-lg" style="background-color: #d9534f;border:none;border-radius:20px;color: white;text-align: center;text-decoration: none;display: inline-block;font-size: 16px;margin: 4px 2px;">' + param + '</button';
    }
    if (param == 'NeedApproval') {
        return '<button class="btn btn-rounded-success btn-lg" style=" background-color: #FF7F50;border-radius:20px;border: none;color: white;text-align: center;text-decoration: none;display: inline-block;font-size: 16px;margin: 4px 2px;">' + param + '</button';
    }
    if (param == null) {
        return '<button class="btn btn-rounded-success btn-lg" style="background-color: #17a2b8;border:none;border-radius:20px;color: white;text-align: center;text-decoration: none;display: inline-block;font-size: 16px;margin: 4px 2px;">' + param + '</button';
    }

}
