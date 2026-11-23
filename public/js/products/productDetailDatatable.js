$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});


getData('.productDetailTable')
    /* Refereshing datatable when change occured in datatabe */
function getData(tableClass) {
    var table = $(tableClass);
    var productId = $('#hidden_product_id').val();
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
            processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading Refund payments...</span> ',
            "emptyTable": "No data available "
        },
        responsive: true,
        searching: true,
        ordering: true,

        "scrollY":        "45vh",
        "scrollCollapse": true,

        ajax: {
            url: baseUrl + '/product/' + productId +'/detail',
            type: "GET",
        },

        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex' },
            { data: 'orderTicket', name: 'Order Id' },
            { data: 'createdAt', name: 'Order Date' },
            // { data: 'itemName', name: 'Item Name' },
            // { data: 'sellerName', name: 'Seller Name' },
            { data: 'buyerName', name: 'Buyer Name' },
            { data: 'orderStatus', name: 'Order Status' },
            // { data: 'action', name: '' },


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

