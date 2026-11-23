$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

$('.select3').select2({
    placeholder: "Please select permissions",
    width: 'resolve',
    minimumInputLength: 1,
    allowClear: true,

});


function resetForm(id) {
    $('.select3').val('').trigger('change');
    document.getElementById(id).reset();
}

getData('.rolesTable')
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
            url: baseUrl + '/roles',
            type: "GET",
        },

        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex' },
            { data: 'name', name: 'Category Name' },
            { data: 'permissionscount', name: 'Total Permissions' },
            { data: 'permissionsaction', name: 'Permissions Action' },
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
        url: baseUrl + '/roles',
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
                resetDatatable('.rolesTable')
                getData('.rolesTable')
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

/* delete role using ajax */
$(function() {

    $('table').on('click', 'tbody .deletePermission', function() {
        $.LoadingOverlay("show");
        var name = $(this).attr('data-name')
        var id = $(this).attr('data-id');
        $.ajax({
            type: 'POST',
            dataType: 'json',
            data: {
                _method: 'DELETE'
            },
            url: baseUrl + '/roles/' + id + '/delete',
            success: function(data) {
                if (data.status) {
                    $.LoadingOverlay("hide");
                    toastr.success(data.data)
                    var table = $('.rolesTable').DataTable()
                    table.destroy()
                    getData('.rolesTable')
                }

            },
            error: function(data) {
                console.log(data)
                $.LoadingOverlay("hide");
                var message = data.responseJSON ? data.responseJSON.data : null
                if (message != null) {
                    toastr.error(data.responseJSON.data)
                }
            }

        });
    });
});

/* edit  */
$('table').on('click', 'tbody .permissionEdit', function() {
    $('#permissionEdit').modal('dispose');
    var roleId = $(this).attr('data-id')
    var roleName = $(this).attr('data-name')
    $('#editcategoryId').val(roleId)
    $('#updaterolesForm').attr('action', baseUrl + '/roles/' + roleId + '/update')
    $.ajax({
        type: 'get',
        dataType: 'json',
        url: baseUrl + '/roles/' + roleId + '/edit',
        success: function(data) {
            var role = data.data
            var selectedPermissions = data.selected
            var permissions = data.permissions
            var title = 'Edit ' + roleName + ' Role';
            $('#permissionEdit #exampleModalLabel').html(title)
            $('#permissionEdit .select3').val(selectedPermissions);
            $('#permissionEdit .select3').trigger('change');

            $('#editName').val(role.name)
            $('#editroleId').val(role.id)
            $('#permissionEdit').modal('show');
        },
        error: function(data) {
            toastr.error('Something went wrong!')
        }

    });


})
$('#addCategoriesForm').on('submit', function() {
    $.LoadingOverlay('show')
})
$('#updaterolesForm').on('submit', function() {
    $.LoadingOverlay('show')
})

/* showing permissions */
$('table').on('click', 'tbody .show', function() {
    var roleId = $(this).attr('data-id')
    var roleName = $(this).attr('data-name')
    $.ajax({
        type: 'get',
        dataType: 'json',
        url: baseUrl + '/roles/' + roleId + '/permissions',
        success: function(data) {
            var role = data.data

            if (role.permissions.length > 0) {
                $('#permissionsLoader').empty()
                var name = roleName + '`s' + '  ' + "permissions"
                $('#showPermissions #exampleModalLabel').text(name)
                $.each(role.permissions, function(index, value) {
                    var html = '';
                    html += '<tr>';
                    html += '<td>';
                    html += '<div class="row">';
                    html += '<div class="col-md-10">';
                    html += '' + value.name + '';
                    html += '</div>';
                    html += '<div class="col-md-2">';
                    html += '<a href="#"data-role="' + roleId + '" data-id="' + value.id + '" data-name="' + value.name + '"   class="btn btn-danger btn-flat btn-sm delete"><i class="fa fa-trash"></i></a>';
                    html += '</div>';
                    html += '</div>';
                    html += '</td>';
                    html += '<tr>';
                    $('#permissionsLoader').append(html)
                })
                $('#showPermissions').modal('show')
            } else {
                toastr.info('No Permissions Found!')

            }

        },
        error: function(data) {
            toastr.error('Something went wrong!')
        }

    });

})


/* delete*/
$('table').on('click', 'tbody .delete', function() {
    var permissionId = $(this).attr('data-id')
    var roleId = $(this).attr('data-role')
    var roleName = $(this).attr('data-name')
    if (confirm('Are you sure you want to delete ' + ucfirst(roleName) + ' permission. ?')) {
        $.ajax({
            type: 'POST',
            dataType: 'json',
            data: {
                _method: 'DELETE'
            },
            url: baseUrl + '/roles/' + permissionId + '/' + roleId + '/permissions',
            success: function(data) {
                if (data.status == true) {
                    toastr.success(data.data)
                    $('#showPermissions').modal('hide')
                    resetDatatable('.rolesTable')
                    getData('.rolesTable')
                } else {
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