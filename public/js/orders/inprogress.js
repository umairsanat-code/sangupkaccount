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
            url: baseUrl + '/orders/inprogress',
            type: "GET",
        },

        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex' },
            { data: 'orderTicket', name: 'Order Id' },
            { data: 'createdAt', name: 'Order Date' },
            { data: 'itemName', name: 'Item Name' },
            { data: 'sellerName', name: 'Seller Name' },
            { data: 'buyerName', name: 'Buyer Name' },
            { data: 'TotalAmount', name: 'Order Amount' },
            { data: 'action', name: '' },


        ]

    });


}


function ucfirst(str, force) {
    str = force ? str.toLowerCase() : str;
    return str.replace(/(\b)([a-zA-Z])/,
        function(firstLetter) {
            return firstLetter.toUpperCase();
        });
}



$('#clearAll').on('click', function() {
    resetForm('addForm')


})

function resetDatatable(tableclass) {
    var table = $(tableclass).DataTable()
    table.destroy()
}




function userAside() {

}