var buyerURL = baseUrl + '/users/buyers/';
var defaultImage = baseUrl + '/img/default-avatar.svg'


$('table').on('click', 'tbody .viewBuyer', function() {
    var buyerId = $(this).attr('data-id')
    $.LoadingOverlay('show')
    $.ajax({
        type: 'get',
        dataType: 'json',
        url: buyerURL + buyerId + '/details',
        success: function(data) {
            var buyerDetail = data.data
            // console.log(data)
            var img = buyerDetail.avatar ? buyerDetail.avatarFullUrl : defaultImage
            var buyerDetailsHtml = '';
            buyerDetailsHtml += '<div class="card-body p-0">';
            buyerDetailsHtml += '<ul class="products-list product-list-in-card pl-2 pr-2">';
            buyerDetailsHtml += '<li class="item">';
            buyerDetailsHtml += '<div class="product-img">';
            buyerDetailsHtml += '<img src="' + img + '" alt="Product Image" class="img-size-50 img-circle" style="height: 60px">';
            buyerDetailsHtml += '</div>';
            buyerDetailsHtml += '<div class="product-info">';
            buyerDetailsHtml += '<a href="javascript:void(0)" class="product-title">' + buyerDetail.name + '<br>';
            buyerDetailsHtml += '<span></span></a>';
            buyerDetailsHtml += '<span class="product-description">';
            buyerDetailsHtml += '' + buyerDetail.phoneNumber + '';
            buyerDetailsHtml += '</span>';
            buyerDetailsHtml += '<span class="product-description">';
            buyerDetailsHtml += '' + buyerDetail.email + '';
            buyerDetailsHtml += '</span>';
            buyerDetailsHtml += '</div>';
            buyerDetailsHtml += '</li>';
            buyerDetailsHtml += '</ul>';
            buyerDetailsHtml += '</div>';
            // buyer active order details
            var buyer_orders = buyerDetail.buyer_orders ? buyerDetail.buyer_orders : null;
            if (buyer_orders.length > 10) {
                $('#display_all_orders').show()
            } else {
                $('#display_all_orders').hide()
            }
            var buyer_orders_active = buyerDetail.buyer_orders_active ? buyerDetail.buyer_orders_active : null;
            var activeOrders = 'No active order found.';
            if (buyer_orders_active.length > 0) {
                activeOrders = '';
                $.each(buyer_orders_active, function(i, v) {

                    activeOrders += '<hr>';                    
                    activeOrders += '<div class="row">';  // row start here

                    // order id
                    activeOrders += '<div class="col-6">';
                    activeOrders += '<b><i class="fa fa-tag text-success"></i> Order ID:</b>';
                    activeOrders += '</div>';
                    activeOrders += '<div class="col-6">';
                    activeOrders += '<p>' + v.orderTicket + '<a href="#"><i class="fa fa-eye ml-4" aria-hidden="true"  data-toggle="tooltip" title="View Order"></i></a>' + '</p>' ;
                    activeOrders += '</div>';

                    // order price
                    activeOrders += '<div class="col-6">';
                    activeOrders += '<b><i class="fas fa-dollar-sign text-success"></i> Order Price:</b>';
                    activeOrders += '</div>';
                    activeOrders += '<div class="col-6">';
                    activeOrders += '<p>$' + v.paidAmount + '</p>' ;
                    activeOrders += '</div>';


                    activeOrders += '<div class="col-12">';  // products start here
                    $.each(v.order_items, function(i, orderItem) {
                        if(i > 0)
                        {
                            var moreProducts =(v.order_items.length - 1);
                            activeOrders +=   ", +"+moreProducts + " more product(s). " + ' ';
                            return false;
                        }
                        var product = orderItem.product;
                        activeOrders += product.name;
                    });

                    activeOrders += '</div>'; // products close here
                    activeOrders += '</div>'; // row end here
                })
            }

            $('#activeorders').html(activeOrders);
            // buyer history details
            var histoy_orders = buyerDetail.buyer_orders ? buyerDetail.buyer_orders : null;
            var historyOrders = 'No history order found.';
            if (histoy_orders.length > 0) {
                historyOrders = '';
                $.each(histoy_orders, function(i, v) {

                    historyOrders += '<hr>';                    
                    historyOrders += '<div class="row">';  // row start here

                    // order id
                    historyOrders += '<div class="col-6">';
                    historyOrders += '<b><i class="fa fa-tag text-success"></i> Order ID:</b>';
                    historyOrders += '</div>';
                    historyOrders += '<div class="col-6">';
                    historyOrders += '<p>' + v.orderTicket + '<a href="#"><i class="fa fa-eye ml-4" aria-hidden="true"  data-toggle="tooltip" title="View Order"></i></a>' + '</p>' ;
                    historyOrders += '</div>';

                    // order price
                    historyOrders += '<div class="col-6">';
                    historyOrders += '<b><i class="fas fa-dollar-sign text-success"></i> Order Price:</b>';
                    historyOrders += '</div>';
                    historyOrders += '<div class="col-6">';
                    historyOrders += '<p>$' + v.paidAmount + '</p>' ;
                    historyOrders += '</div>';

                    // order STATUS
                    historyOrders += '<div class="col-6">';
                    historyOrders += '<b><i class="fas fa-stream text-success"></i> Order Status:</b>';
                    historyOrders += '</div>';
                    historyOrders += '<div class="col-6">';
                    historyOrders += '<p>' + v.statusHtml + '</p>' ;
                    historyOrders += '</div>';


                    historyOrders += '<div class="col-12">';  // products start here
                    $.each(v.order_items, function(i, orderItem) {
                        if(i > 0)
                        {
                            var moreProducts =(v.order_items.length - 1);
                            historyOrders +=   ", +"+moreProducts + " more product(s). " + ' ';
                            return false;
                        }
                        var product = orderItem.product;
                        historyOrders += product.name;
                    });

                    historyOrders += '</div>'; // products close here
                    historyOrders += '</div>'; // row end here
                })
            }
            $('#history').html(historyOrders);

            if (buyerDetail.status != 'Banned') {
                $('.dropdown-block-aside').show()
                $('.btn-block-aside').attr('href', buyerURL + 'Blocked/' + buyerDetail.id + '/status')
            } else {
                $('.dropdown-block-aside').hide()
            }

            $('.seller-details').html(buyerDetailsHtml)
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
