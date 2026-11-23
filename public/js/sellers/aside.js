var sellerURL = baseUrl + '/users/sellers/';
var userURL = baseUrl + '/users/buyers/';
var defaultImage = baseUrl + '/img/default-avatar.svg'


$('table').on('click', 'tbody .viewSeller', function() {
    var sellerId = $(this).attr('data-id')
    $.LoadingOverlay('show')
    $.ajax({
        type: 'get',
        dataType: 'json',
        url: sellerURL + sellerId + '/details',
        success: function(data) {
            if (data.status) {
                var sellerDetail = data.data
                var images = sellerDetail.images ? sellerDetail.images : null
                var img = sellerDetail.user_documents.avatar? sellerDetail.user_documents.avatarFullUrl : defaultImage
                var name = sellerDetail.user_documents.firstName ? sellerDetail.user_documents.firstName +' '+sellerDetail.user_documents.lastName :'null';
                var sellerDetailsHtml = '';
                sellerDetailsHtml += '<div class="card-body box-profile"> ';
                sellerDetailsHtml += '<div class = "text-center" >';
                sellerDetailsHtml += '<img class = "profile-user-img img-fluid img-circle" style="height: 100px" src = "' + img + '" alt = "User profile picture" >';
                sellerDetailsHtml += '</div>';
                sellerDetailsHtml += '<h3 class = "profile-username text-center" > ' + name + ' </h3>';
                sellerDetailsHtml += '<p class="text-center"> ' + statusStyle(sellerDetail.status) + ' </p>';
                sellerDetailsHtml += '<ul class = "list-group list-group-unbordered mb-3">';
                sellerDetailsHtml += '<li class = "list-group-item text-center" >';
                sellerDetailsHtml += '' + sellerDetail.user_documents.email + '';
                sellerDetailsHtml += '</li>';
                sellerDetailsHtml += '<li class = "list-group-item text-center" >';
                sellerDetailsHtml += '' + sellerDetail.user_documents.phoneNumber + '';
                sellerDetailsHtml += '</li>';
                sellerDetailsHtml += '</ul >';
                sellerDetailsHtml += '';
                sellerDetailsHtml += '</div>';
                if (sellerDetail.user_documents.status == 'Banned') {
                    $('.btn-block-aside').hide()
                    $('.btn-unblock-aside').show()

                } else {
                    $('.dropdown-block-aside').show()
                    $('.btn-unblock-aside').hide()
                }
                if (sellerDetail.status == 'APPROVED') {
                    $('.btn-reject-aside').hide()
                    $('.btn-accept-aside').hide()
                }
                if (sellerDetail.status == 'REJECTED') {
                    $('.btn-reject-aside').hide()
                    $('.btn-accept-aside').hide()
                }
                if (sellerDetail.status == 'PENDING') {
                    $('.btn-reject-aside').show()
                    $('.btn-accept-aside').show()


                }
                $('.btn-block-aside').attr('href', sellerURL + 'Blocked/' + sellerDetail.userId + '/block')
                $('.btn-unblock-aside').attr('href', userURL + 'Active/' + sellerDetail.userId + '/status')
                $('.btn-reject-aside').attr('data-id', sellerDetail.id)
                $('.btn-accept-aside').attr('href', sellerURL + 'APPROVED/' + sellerDetail.id + '/status')


                $('.seller-details').html(sellerDetailsHtml);
                if (sellerDetail) {
                    var documentHtml = '';
                    if (images.length > 0) {
                        

                        $.each(images, function (k, v) {
                            documentHtml += '<div class="col-md-6 col-lg-6 col-sm-6">';
                            documentHtml += ' <div class="card mb-2">';
                            documentHtml += '<a href="' + v.fullDocumentUrl + '" target="_blank" id="documentOpener">';
                            documentHtml += '<img class="card-img-top" id="document-src" src="' + v.fullDocumentUrl + '" alt="Document"></a>';
                            documentHtml += ' </div>';
                            documentHtml += '</div>';
                        })
                    }
                    $('#seller-documents').html(documentHtml);
                }
                $.LoadingOverlay('hide')
                window.location = "#seller-details"
                $('body').addClass('sidebar-mini control-sidebar-slide-open')

            } else {
                $.LoadingOverlay('hide')
                toastr.error('Something went wrong!');
            }


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

/* reject reason */
$('body').on('click', '.btn-reject-aside', function() {
    var sellerId = $(this).attr('data-id')
    $('#rejectionModal #rejectionForm').attr('action', sellerURL + 'REJECTED/' + sellerId + '/status')
    $('#rejectionModal').modal('show')
})

$('body').on('dblclick', function() {
    $(this).removeClass('sidebar-mini control-sidebar-slide-open')
})

function statusStyle(param) {

    if (param == 'Active' || param == 'APPROVED') {
        return '<button class="btn btn-rounded-success btn-lg" style="background-color: #5cb85c;border-radius:20px;border: none;color: white;text-align: center;text-decoration: none;display: inline-block;font-size: 16px;margin: 4px 2px;">' + param + '</button';
    }
    if (param == 'Banned') {
        return '<button class="btn btn-rounded-success btn-lg" style="background-color: #d9534f;border:none;border-radius:20px;color: white;text-align: center;text-decoration: none;display: inline-block;font-size: 16px;margin: 4px 2px;">Blocked</button';
    }
    if (param == 'PENDING') {
        return '<button class="btn btn-rounded-success btn-lg" style=" background-color: #FF7F50;border-radius:20px;border: none;color: white;text-align: center;text-decoration: none;display: inline-block;font-size: 16px;margin: 4px 2px;">Need Approval</button';
    }
    if (param == 'REJECTED') {
        return '<button class="btn btn-rounded-success btn-lg" style="background-color: #d9534f;border:none;border-radius:20px;color: white;text-align: center;text-decoration: none;display: inline-block;font-size: 16px;margin: 4px 2px;">Rejected</button';
    }
    if (param == null) {
        return '<button class="btn btn-rounded-success btn-lg" style="background-color: #17a2b8;border:none;border-radius:20px;color: white;text-align: center;text-decoration: none;display: inline-block;font-size: 16px;margin: 4px 2px;">' + param + '</button';
    }


}
