$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

$('.select2').select2({
    placeholder: 'Select a role for team member',
    width: 'resolve',
    minimumInputLength: 0,
    allowClear: true,

    initSelection: function(element, callback) {}
});
$('.select2').val('').trigger('change');

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

function resetForm(id) {
    $('.select2').val('').trigger('change');
    $('.dropify-clear').trigger('click')
    document.getElementById(id).reset();
}
getData()

function getData() {
    $('#profileLoader').empty();
    var html = '';
    $.ajax({
        type: 'get',
        dataType: 'json',
        url: baseUrl + '/teams/all',
        success: function(data) {
            if (data.status && data.data.length > 0) {
                /* appending team members data */
                $.each(data.data, function(k, v) {
                    html = '';
                    html += '<div class="col-md-4">';
                    html += '<div class="card card-default" style="background: #F9FAFB">';
                    html += '<div class="text text-center p-3">';
                    html += '<img src="' + v.avatarFullUrl + '"class="img-size-50 img-tumbnail"><br>';
                    html += ' <b>' + ucfirst(v.name) + '</b><br>';
                    html += '<p>' + v.email + '</p>';
                    html += '<hr>';

                    // if (v.roles.length > 0) {
                    //     html += '<div class="row m-1">';
                    //     html += '<div class="col-md-5">';
                    //     html += '<b>' + ucfirst(v.roles ? v.roles[0].name : '') + '</b>';
                    //     html += '</div>';
                    //     html += '<div class="col-md-5">';
                    //     html += '<b>' + ucfirst(v.roles ? 'Granted' : '') + '</b>';
                    //     html += '</div>';
                    //     html += '</div>';
                    //     html += '<hr>';
                    // }

                    html += '<div class="row">';
                    html += '<div class="col-md-5 ml-1">';
                    html += '<button data-id="' + v.id + '" data-name="' + v.name + '" class="btn btn-sm btn-primary active-nav" id="edit-profile">Edit Profile</button>';
                    html += '</div>';
                    html += '<div class="col-md-6">';
                    html += '<button data-id="' + v.id + '" data-name="' + v.name + '" class="btn btn-sm btn-primary active-nav " id="reset-password">Reset Password</button>';
                    html += '</div>';
                    html += '</div>';
                    html += '</div>';
                    html += '</div>';
                    html += '</div>';
                    $('#profileLoader').append(html)

                })

            } else {
                $('#profileLoader').html('<div class=" col-12  text-center alert alert-info"><h1>Currently you do not have any team member</h1></div>')
            }
        },
        error: function(data) {
            toastr.error('Something went wrong!')
        }
    });


}


/* add  */
// $(document).ready(function () {
//     $('#addForm').on('submit', function (e) {
//         e.preventDefault();
//         $.LoadingOverlay("show");

//         // Always setup CSRF before AJAX
//         $.ajaxSetup({
//             headers: {
//                 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
//             }
//         });

//         var formData = new FormData(this); // Initialize FormData properly

//         $.ajax({
//             type: 'POST',
//             url: baseUrl + '/teams',
//             data: formData,
//             contentType: false, // don't set content-type manually
//             processData: false, // don't process the data
//             cache: false,
//             dataType: 'json',
//             success: function (response) {
//                 $.LoadingOverlay("hide");
//               $('#addCustomerModal').modal('hide');
//                 if (response.success) {
//                     alert(1);
//                     toastr.success(response.success);
//                     $('#addForm')[0].reset();
//                     $('#addCustomerModal').modal('hide');
//                     getData('teamsTable');
//                 } else {
//                     alert(1);
//                           $('#addCustomerModal').modal('show');
//                     toastr.error(response.data || 'Something went wrong');
//                 }
//             },
//             error: function (xhr, textStatus, errorThrown) {
//                 $.LoadingOverlay("hide");
//                 $('#addCustomerModal').modal('show');

//                 if (xhr.responseJSON && xhr.responseJSON.message) {
//                     toastr.error(xhr.responseJSON.message);
//                 } else {
//                     toastr.error("An unexpected error occurred.");
//                 }

//                 console.error(xhr);
//             }
//         });
//     });
// });





/* edit  */
$('#profileLoader ').on('click', '#edit-profile', function() {
        $('#teamEdit').modal('dispose');
        var teamId = $(this).attr('data-id')
        var teamName = $(this).attr('data-name')
        $('#teamEdit #teamId').val(teamId)
        $('#updateteamForm').attr('action', baseUrl + '/teams/' + teamId + '/update')
        $('#teamEdit #deleteMember').attr('href', baseUrl + '/teams/' + teamId + '/delete')
        $.ajax({
            type: 'get',
            dataType: 'json',
            url: baseUrl + '/teams/' + teamId + '/edit',
            success: function(data) {
                var member = data.data;
                // console.log(member.phoneNumber);
                var selectedPermissions = data.selected
                var permissions = data.permissions
                var html = '<input type = "file"  data-show-errors="true"data-max-file-size="10M" name = "editedimage" data-default-file="' + member.avatarFullUrl + '" id = "dropifyImage" data-height = "100"accept = "image/x-png,image/gif,image/jpeg,image/gif,image/jpg,image/svg,image/tiff" data-allowed-file-extensions = "jpeg jpg png gif svg tiff" >';
                var title = 'Edit ' + ucfirst(member.name) + ' Team Member';
                $('#teamEdit  #exampleModalLabel').html(title)
                $('#teamEdit .select2').val(selectedPermissions);
                $('#teamEdit .select2').trigger('change');
                $('#teamEdit #teamfirstName').val(member.firstName)
                $('#teamEdit #teamlastName').val(member.lastName)
                $('#teamEdit #teamPhoneNumber').val(member.phoneNumber)
                $('#teamEdit .imageLoader').html(html)
                $('#teamEdit #dropifyImage').dropify();
                $('#teamEdit').modal('show');
            },
            error: function(data) {
                toastr.error('Something went wrong!')
            }

        });


    })
    /* change password  */
$('#profileLoader ').on('click', '#reset-password', function() {
    $('#passwordEdit').modal('dispose');
    var teamId = $(this).attr('data-id')
    $('#passwordEdit #teamId').val(teamId)
    $('#updatepasswordteamForm').attr('action', baseUrl + '/teams/' + teamId + '/password')
    $('#passwordEdit #deleteMember').attr('href', baseUrl + '/teams/' + teamId + '/delete')
    $.ajax({
        type: 'get',
        dataType: 'json',
        url: baseUrl + '/teams/' + teamId + '/edit',
        success: function(data) {
            var member = data.data
            var title = 'Edit ' + ucfirst(member.name) + '`s Password';
            $('#passwordEdit  #exampleModalLabel').html(title)
            $('#passwordEdit').modal('show');
        },
        error: function(data) {
            toastr.error('Something went wrong!')
        }

    });

})

$('#teamEdit').on('submit', function() {
    $.LoadingOverlay('show')
})







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
