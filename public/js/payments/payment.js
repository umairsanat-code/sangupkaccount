$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});


getData('.buyersTable')
    /* Refereshing datatable when change occured in datatabe */
function getData(tableClass) {
    var table = $(tableClass);
    table.DataTable({
        "lengthMenu": [
            [10, 50, 100, 250, 500, 1000 - 1],
            [10, 50, 100, 250, 500, 1000, "All"]
        ],
        pageLength: 50,
        processing: true,
        stateSave: true,
        "dom": '<"top"fli>rt<"bottom"p><"clear">',
        "language": {
            processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading Tours...</span> ',
            "emptyTable": "No data available "
        },
        responsive: true,
        searching: true,
        ordering: true,

        "scrollY":        "45vh",
        "scrollCollapse": true,

        ajax: {
            url: baseUrl + '/payments',
            type: "GET",
        },

        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex' },
            { data: 'name', name: 'Seller Name' },
            { data: 'email', name: 'Email' },
            { data: 'phone', name: 'Phone#' },
            { data: 'amount', name: 'Cleared Payment' },
            { data: 'action', name: '' },


        ]

    });

    $(tableClass).on('draw.dt', function() {
        $('[data-toggle="tooltip"]').tooltip();
    })


}



function ucfirst(str, force) {
    str = force ? str.toLowerCase() : str;
    return str.replace(/(\b)([a-zA-Z])/,
        function(firstLetter) {
            return firstLetter.toUpperCase();
        });
}


/* view documents */

$('body').on('click', '.viewDocuments', function() {
    var userId = $(this).attr('data-id')
    $.LoadingOverlay('show')
    $.ajax({
        type: 'get',
        dataType: 'json',
        url: paymentsUrl + userId + '/documents',
        success: function(data) {
            var user = data.data
            var documents = user.documents
            $.LoadingOverlay('hide')
            var html = '';
            $.each(documents, function(i, document) {
                $.each(document.images, function(i, v) {
                    html += '<div class="carousel-item">';
                    html += '<div class="card mb-3">';
                    html += '<div class="row no-gutters">';
                    html += '<div class="col-md-6 offset-md-3 offset-lg-3">';
                    html += '<img  src="' + v.fullDocumentUrl + '" class="card-img" alt="...">';
                    html += '</div>';
                    html += '</div>';
                    html += '</div>';
                    html += '</div>';

                })
            })
            $('#documentsModal .carousel-inner').append(html)
            $('#documentsModal .carousel-inner .carousel-item:first').addClass('active')
            $('#documentsModal').modal('show')
        },
        errors: function() {
            $.LoadingOverlay('hide')
            toastr.error('Something went wrong!')
        }
    })
})
