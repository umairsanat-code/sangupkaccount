var paymentsUrl = baseUrl + '/payments/'
var defaultImage = baseUrl + '/img/default-avatar.svg'


$('table').on('click', 'tbody .viewPayments', function() {
    var userId = $(this).attr('data-id')
    console.log(userId)
    $('.btn-amount-aside').hide()
    $.LoadingOverlay('show')
    $.ajax({
        type: 'get',
        dataType: 'json',
        url: paymentsUrl + userId + '/show',
        success: function(data) {
            var payments = data.data
            var user = payments ? payments[0].user : []
            console.log(payments)


            var img = user ? user.avatarFullUrl : defaultImage
            var orderDetailssHtml = '';
            orderDetailssHtml += '<div class="card-body p-0">';
            orderDetailssHtml += '<ul class="products-list product-list-in-card pl-2 pr-2">';
            orderDetailssHtml += '<li class="item">';
            orderDetailssHtml += '<div class="product-img">';
            orderDetailssHtml += '<img src="' + img + '" alt="Product Image" class="img-size-50">';
            orderDetailssHtml += '</div>';
            orderDetailssHtml += '<div class="product-info">';
            orderDetailssHtml += '<p class="product-title">' + user.name + '</p>';
            orderDetailssHtml += '<span class="product-description">';
            orderDetailssHtml += '' + user.phoneNumber + ' $';
            orderDetailssHtml += '</span>';
            orderDetailssHtml += '<span class="product-description">';
            orderDetailssHtml += '<a data-id="' + user.id + '" class="product-title viewDocuments">View Documents </a><br>';
            orderDetailssHtml += '</span>';
            orderDetailssHtml += '</div>';
            orderDetailssHtml += '</li>';
            orderDetailssHtml += '</ul>';
            orderDetailssHtml += '</div>';
            orderDetailssHtml += '<br>';
            orderDetailssHtml += '<b class="mt-3 text-bold"><span class="dot"></span> Amount History</b> <br>';
            if (payments.length > 0) {
                $.each(payments, function(i, v) {

                    orderDetailssHtml += '<div class="row row-payment mt-1">';
                    orderDetailssHtml += '<div class="col-md-6">';
                    orderDetailssHtml += '<p>' + v.paymentDate + '</p>';
                    orderDetailssHtml += '</div>';
                    orderDetailssHtml += '<div class="col-md-6 text-right">';
                    orderDetailssHtml += '<p>' + v.clearedAmount + '</p>';
                    orderDetailssHtml += '</div>';
                    orderDetailssHtml += '</div>';


                })
                $('.btn-amount-aside').show()
            } else {
                orderDetailssHtml += '<div class="row mt-5">';
                orderDetailssHtml += '<div class="col-md-12">';
                orderDetailssHtml += '<b class="text-danger">Not Earned Any Amount Yet!</b>';
                orderDetailssHtml += '</div>';
                orderDetailssHtml += '</div>';

            }
            if (payments.length > 10){
                $('.btn-amount-aside').attr('href', paymentsUrl + userId + '/amounts-history')
            }else{
                $('.btn-amount-aside').hide();
            }

            $('.seller-details').html(orderDetailssHtml)
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

$('body').on('dblclick', function() {
    $(this).removeClass('sidebar-mini control-sidebar-slide-open')
})
$('#cancelBtn').click(function() {
    $('body').removeClass('sidebar-mini control-sidebar-slide-open')

});