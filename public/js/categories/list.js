$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});


$('.dropify').dropify({
        messages: {
            'default': '<h6>Drop image here</h6>',
            'replace': 'Drop or click to replace',
            'remove': 'Remove',
            'error': 'File is not allowed.<br><small class="mb-5">allowed files are jpeg jpg png gif svg tiff</small>'
        },
        error: {
            'fileSize': 'The file size is too big ({{ value }} max).',
            'minWidth': 'The image width is too small ({{ value }}}px min).',
            'maxWidth': 'The image width is too big ({{ value }}}px max).',
            'minHeight': 'The image height is too small ({{ value }}}px min).',
            'maxHeight': 'The image height is too big ({{ value }}px max).',
            'imageFormat': 'The allowed extension are ({{ value }} only).'
        }
    })
    //reset the form
function resetForm(id) {
    $('.dropify-clear').trigger('click')
    document.getElementById(id).reset();
}
getData('.categoriesTable')
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


        ajax: {
            url: baseUrl + '/categories',
            type: "GET",
        },

        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex' },
            { data: 'name', name: 'Category Name' },
            { data: 'subcategoriescount', name: 'Total Subcategories' },
            { data: 'subcategoriesaction', name: 'Subcategories Action' },
            { data: 'action', name: '' },


        ]

    });


}
/* add category using ajax */
$("#addForm").on('submit', (function(e) {
    e.preventDefault()
    $.LoadingOverlay("show");

    $.ajax({
        type: 'POST',
        url: baseUrl + '/categories',
        data: new FormData(this),
        dataType: 'JSON',
        contentType: false,
        cache: false,
        processData: false,
        success: function(data) {
            resetForm('addForm')
            $.LoadingOverlay("hide");
            if (data.status == true) {
                toastr.success(data.data)
                $(this).trigger("reset");
                resetDatatable('.categoriesTable')
                getData('.categoriesTable')
            } else {
                $.LoadingOverlay("hide");
                toastr.error(data.data)
            }

        },
        error: function(data) {
            $.LoadingOverlay("hide");
            var message = data.responseJSON ? data.responseJSON.response : null
            if (message != null) {
                toastr.error(data.responseJSON.response.message)
            }


        }

    });


}));

/* delete category using ajax */
$(function() {

    $('table').on('click', 'tbody .deleteCategory', function() {
        $.LoadingOverlay("show");
        var name = $(this).attr('data-name')
        if (!confirm('Are you sure you want to delete ' + name + ' category. This action also delete associated  subcategories ?')) 
        {
            $.LoadingOverlay("hide"); return;
        }
        var id = $(this).attr('data-id');
        $.ajax({
            type: 'POST',
            dataType: 'json',
            data: {
                _method: 'DELETE'
            },
            url: baseUrl + '/categories/' + id + '/delete',
            success: function(data) {
                if (data.status) {
                    $.LoadingOverlay("hide");
                    toastr.success(data.data)
                    var table = $('.categoriesTable').DataTable()
                    table.destroy()
                    getData('.categoriesTable')
                } else {
                    $.LoadingOverlay("hide");
                    toastr.error(data.data)
                }

            },
            error: function(data) {
                toastr.error('Something went wrong!')
            }

        });
    });
});

/* edit category using ajax */
$('table').on('click', 'tbody .categoryEdit', function() {
    $('#EditCategory').modal('dispose');
    var categoryId = $(this).attr('data-id')
    var categoryName = $(this).attr('data-name')
    var categoryImage = $(this).attr('data-image')
    $('#editcategoryId').val(categoryId)
    $('#updateCategoriesForm').attr('action', baseUrl + '/categories/' + categoryId + '/update')
    $.ajax({
        type: 'get',
        dataType: 'json',
        url: baseUrl + '/categories/' + categoryId + '/edit',
        success: function(data) {
            var subcategories = data.data
            var html = '<input type = "file"  data-show-errors="false" name = "image" data-default-file="' + categoryImage + '" id = "dropifyImage" data-height = "100"accept = "image/x-png,image/gif,image/jpeg,image/gif,image/jpg,image/svg,image/tiff" data-allowed-file-extensions = "jpeg jpg png gif svg tiff" >';
            var title = '<img src="' + categoryImage + '" alt="Product 1" class="img-circle img-size-32 mr-2"> Edit ' + categoryName + ' Category';
            $('#EditCategory #exampleModalLabel').html(title)
            $('#EditCategory #imageUploader').html(html)
            $('#EditCategory #dropifyImage').dropify();
            $('#editName').val(subcategories.name)
            $('#EditCategory').modal('show');
        },
        error: function(data) {
            toastr.error('Something went wrong!')
        }

    });


})
$('#addCategoriesForm').on('submit', function() {
    $.LoadingOverlay('show')
})
$('#updateCategoriesForm').on('submit', function() {
    $.LoadingOverlay('show')
})

/* showing subcategories against given category */
$('table').on('click', 'tbody .show', function() {
    var categoryId = $(this).attr('data-id')
    var categoryName = $(this).attr('data-name')

    $.ajax({
        type: 'get',
        dataType: 'json',
        url: baseUrl + '/subcategories/' + categoryId + '/categories',
        success: function(data) {
            var subcategories = data.data
            if (data.status && subcategories.length > 0) {
                $('#subCategoriesLoader').empty()
                var name = categoryName + '`s' + '  ' + "Subcategories"
                $('#showSub #exampleModalLabel').text(name)
                $.each(subcategories, function(index, value) {
                    var html = '';
                    html += '<tr>';
                    html += '<td>';
                    html += '<div class="row">';
                    html += '<div class="col-md-10">';
                    html += '' + value.name + '';
                    html += '</div>';
                    html += '<div class="col-md-2">';
                    html += '<a href="#" data-id="' + value.id + '" data-name="' + value.name + '"   class="btn btn-danger btn-flat btn-sm delete"><i class="fa fa-trash"></i></a>';
                    html += '</div>';
                    html += '</div>';
                    html += '</td>';
                    html += '<tr>';
                    $('#subCategoriesLoader').append(html)
                })
                $('#showSub').modal('show')
            } else {
                toastr.info('No Subcategory Found!')

            }

        },
        error: function(data) {
            toastr.error('Something went wrong!')
        }

    });

})

/* add subcategories */
$('table').on('click', 'tbody .add', function() {
    var categoryName = $(this).attr('data-name')
    var categoryId = $(this).attr('data-id')
    var name = categoryName + '  ' + "Subcategory"
    $('#AddSub #exampleModalLabel').text(name)
    $('#categoryId').val(categoryId)
    $('#AddSub .file').dropify()
    $('#AddSub').modal('show')
});
/* delete subcategory using ajax */
$('table').on('click', 'tbody .delete', function() {
    var subcatgoryId = $(this).attr('data-id')
    var subcatgoryname = $(this).attr('data-name')
    if (confirm('Are you sure you want to delete ' + ucfirst(subcatgoryname) + ' subcategory. ?')) {
        $.ajax({
            type: 'POST',
            dataType: 'json',
            data: {
                _method: 'DELETE'
            },
            url: baseUrl + '/subcategories/' + subcatgoryId + '/delete',
            success: function(data) {
                if (data.status == true) {
                    toastr.success(data.data)
                    $('#showSub').modal('hide')
                    resetDatatable('.categoriesTable')
                    getData('.categoriesTable')
                } else {
                    toastr.error(data.data)
                }

            },
            error: function(data) {
                toastr.error('Something went wrong!')
            }

        });
    }
});



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