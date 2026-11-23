@extends('layouts.master')
@section('title','Team Management')
@section('style')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
 {!! commonStyles() !!}
 {!! customDatatableCss() !!}
 {!! select2CssCustom() !!}
<style>
    #chat-client {
        float: right;
        width: 300px;
    }
</style>
@endsection
@section('content')
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Clients Management</h1>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>

        <!-- Main content -->
        <section class="content">
        <div class="container-fluid">
        <div class="row">
                <!-- category create form -->
                <div class="col-md-3 col-sm-3 col-lg-3">
                    <div class="card">
                         <div class="card-header"style="background-color: white">
                            <h3 class="card-title" id="categoryTitle">Add New Client Member</h3>
                            </div>
                        <div class="card-body">
                            <form id="addForm" method="post" action="{{ route('clients.store') }}" enctype="multipart/form-data">
                                @csrf
                                <div class="form-group">
                                    <label>Name</label>
                                    <input type="text" name="firstName" class="form-control" value="{{ old('firstName') }}" required autocomplete="off" id="firstName" placeholder="Enter Name">
                                </div>
                                <div class="form-group">
                                    <label>Email</label>
                                    <input type="text" name="email" class="form-control" value="{{ old('email') }}" required autocomplete="off" id="email" placeholder="Enter Email">
                                </div>
                                <div class="form-group">
                                    <label>Phone Number</label>
                                    <input type="text" name="phoneNumber" class="form-control" id="phoneNumber" placeholder="Enter Phone Number">
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <button type="submit" class="btn btn-primary btn-flat text-white active-nav">Add Team</button>
                                    </div>
                                    <div class="col-md-4 mt-2">
                                        <a href="#" id="clearAll" class="text text-bold text-info">Clear all</a>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
          <!-- end category create form -->
                  <div class="col-md-9">


                  <div class="card">
                    <div class="card-header" style="background-color: white">
                      <h3 class="card-title">Clients Members</h3>
                    </div>
                       <!-- /.box-header -->
                    <div class="card-body">
                        <table id="clientsTable" class="display" style="width:100%">
                            <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                @if(\Illuminate\Support\Facades\Auth::id()==1)
                                    <th>Created By</th>

                                @endif

                                <th>Email</th>
                                <th>Phone Number</th>
                                <th>Action</th>
                                <!-- Add more table headers as needed -->
                            </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>


                    </div>
                      </div>

                  </div>
            <div class="row" >
                      <div class="col-md-3">
                          <!-- DIRECT CHAT WARNING -->
                          <div id="chat-client" class="card card-warning direct-chat direct-chat-warning"
                               style="position: absolute; top: 0; right: 0; z-index: 999; display: none;">
                              <div class="card-header">
                                  <h3 class="card-title">Direct Chat</h3>

                                  <div class="card-tools">
                                      <span title="3 New Messages" class="badge bg-danger">3</span>
                                      <button type="button" class="btn btn-tool" data-card-widget="remove">
                                          <i class="fas fa-times"></i>
                                      </button>
                                  </div>
                              </div>
                              <!-- /.card-header -->
                              <div class="card-body">
                                  <!-- Conversations are loaded here -->
                                  <div class="direct-chat-messages" id="chat-messages">
                                      <!-- Message. Default to the left -->

                                      <!-- /.direct-chat-msg -->

                                      <!-- Message to the right -->
                                      <!-- /.direct-chat-msg -->
                                  </div>
                                  <!--/.direct-chat-messages-->

                                  <!-- Contacts are loaded here -->
                                  <div class="direct-chat-contacts">

                                      <!-- /.contatcts-list -->
                                  </div>
                                  <!-- /.direct-chat-pane -->
                              </div>
                              <!-- /.card-body -->
                              <div class="card-footer">
                                  <form  id="send-sms-form">
                                      <div class="input-group">
                                          <input type="hidden" id="clientId" name="clientId">
                                          <input type="text" name="message" id="message" placeholder="Type Message ..." class="form-control">
                                          <span class="input-group-append">
                      <button type="submit" class="btn btn-warning send-sms-btn">Send</button>
                    </span>
                                      </div>
                                      <small id="character-count" class="form-text text-muted">0/160 characters</small>

                                  </form>
                              </div>
                              <!-- /.card-footer-->
                          </div>
                          <!--/.direct-chat -->
                      </div>

                  </div>
              </div>

          </div>

        </section>
        <!-- /.content -->
    </div>
    <!-- SMS Modal -->



    <!-- Add a chat window -->
    <!-- Add a chat window -->

    <div class="modal fade" id="editClientModal" tabindex="-1" role="dialog" aria-labelledby="editClientModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editClientModalLabel">Edit Client</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="editClientForm">
                        <input type="hidden" id="client-id">
                        <div class="form-group">
                            <label for="client-name">Name</label>
                            <input type="text" class="form-control" id="client-name" name="name">
                        </div>
                        <div class="form-group">
                            <label for="client-email">Email</label>
                            <input type="email" class="form-control" id="client-email" name="email">
                        </div>
                        <div class="form-group">
                            <label for="client-email">Email</label>
                            <input type="text" class="form-control" id="client-phoneNumber" name="phoneNumber">
                        </div>
                        <button type="button" class="btn btn-primary" id="save-client">Save changes</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- /.content-wrapper -->
    @include('admin.teams.inc.add')
    @include('admin.teams.inc.show')
{{--    @include('admin.teams.inc.update')--}}
     @include('admin.teams.inc.password')
@endsection

@section('script')
{!! commonScripts() !!}
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // Select the message input field
    const messageInput = document.getElementById('message');

    // Select the character count element
    const characterCount = document.getElementById('character-count');

    // Add an event listener to the input field
    messageInput.addEventListener('input', function() {
        // Get the current value of the input field
        const message = this.value;

        // Calculate the length of the message
        const messageLength = message.length;

        // Update the character count display
        characterCount.textContent = messageLength + '/160 characters';
    });
</script>

<script>
    $(document).ready(function() {
        $('#addForm').submit(function(event) {
            event.preventDefault();
            var formData = new FormData(this);
            $.ajax({
                url: $(this).attr('action'),
                method: 'POST',
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                success: function(response) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: 'Client added successfully!',
                    }).then(() => {
                        window.location.reload(); // Reload the page or redirect as needed
                    });
                },
                error: function(xhr, textStatus, errorThrown) {
                    var errors = xhr.responseJSON.errors;
                    var errorMessage = '';
                    $.each(errors, function(key, value) {
                        errorMessage += value + '<br>';
                    });
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        html: errorMessage,
                    });
                }
            });
        });
    });
    $(document).ready(function() {
        var isAdmin = {{ Auth::user()->hasRole('admin') ? 'true' : 'false' }};

        var columns = [
            { "data": "id" },
            { "data": "name" },
            { "data": "email" },
            { "data": "phoneNumber" },
            { "data": "action" }
            // Add more columns as needed
        ];

        // Include the "Created By" column only if the user is an admin
        if (isAdmin) {
            columns.splice(2, 0, { data: 'creator', name: 'creator', title: 'Created By' });
        }

        $('#clientsTable').DataTable({
            "processing": true,
            "serverSide": true,
            "ajax": "{{ route('clients') }}",
            "columns": columns
        });
    });
    $(document).ready(function() {
        // // Event listener for clicking on the SMS button
        // $(document).on('click', '.sms-btn', function() {
        //     var clientId = $(this).data('client-id');
        //     // alert(clientId);
        //     $('#clientId').val(clientId);
        //     $('#chat-client').show(); // Show the chat interface
        //
        //     // Show the overlay table
        //     $('#overlay-table').show();
        // });

        // Event listener for clicking on the Send SMS button
        $('.send-sms-btn').click(function(e) {
            e.preventDefault()
            var clientId = $('#clientId').val();
            var message = $('#message').val();
// alert(message);
            // Make AJAX request to send SMS
            $.ajax({
                url: "{{ route('send-sms') }}",
                method: "POST",
                data: {
                    client_id: clientId,
                    message: message,
                    "_token": "{{ csrf_token() }}"
                },
                success: function(response) {
                    console.log(1);
                    loadChatHistory(clientId);
                    // Handle success response
                    // $('#chat-client').show(); // Hide modal after sending SMS
                    // alert('SMS sent successfully!');

                },
                error: function(xhr, status, error) {
                    // Handle error response
                    console.error(xhr.responseText);
                    alert('Failed to send SMS. Please try again later.');
                }
            });
        });
    });
$(document).ready(function() {
    $(document).on('click', '.sms-btn', function () {
        var clientId = $(this).data('client-id');
        $('#clientId').val(clientId);
        // AJAX request to load previous chats
        $.ajax({
            url: '{{ route("load.previous.chats") }}',
            type: 'POST',
            data: {
                client_id: clientId,
                _token: '{{ csrf_token() }}'
            },
            success: function (response) {
                var chatMessages = '';
                var defaultImage = baseUrl + '/img/default-avatar.svg'
                response.forEach(function (chat) {
                    console.log(chat);
                    var messageClass = (chat.position === 'left') ? 'direct-chat-msg' : 'direct-chat-msg right';
                    var messageClassName = (chat.position === 'left') ? '' : 'float-right';
                    var messageClassTime = (chat.position === 'left') ? 'float-right' : 'float-left';
                    chatMessages += '<div class="' + messageClass + '">' +
                        '<div class="direct-chat-infos clearfix">' +
                        '<span class="direct-chat-name '+messageClassName+'">' + chat.user_name + '</span>' +
                        '<span class="direct-chat-timestamp '+messageClassTime+'">' + chat.created_at + '</span>' +
                        '</div>' +
                        '<img class="direct-chat-img" src="' +defaultImage + '" alt="Message User Image">' +
                        '<div class="direct-chat-text">' + chat.message + '</div>' +
                        '</div>';
                });
                $('#chat-messages').html(chatMessages);
                $('#chat-client').show()
                var clientId = $('#clientId').val();
                // setInterval(function() {
                //     loadChatHistory(clientId);
                // }, 3000);
            },

            error: function (xhr, status, error) {
                console.error(error);
            }
        });


    });

});
    function loadChatHistory(clientId) {
        $.ajax({
            url: '{{ route("load.previous.chats") }}',
            type: 'POST',
            data: {
                client_id: clientId,
                _token: '{{ csrf_token() }}'
            },
            success: function (response) {
                var chatMessages = '';
                var defaultImage = baseUrl + '/img/default-avatar.svg'
                response.forEach(function (chat) {
                    console.log(chat);
                    var messageClass = (chat.position === 'left') ? 'direct-chat-msg' : 'direct-chat-msg right';
                    var messageClassName = (chat.position === 'left') ? '' : 'float-right';
                    var messageClassTime = (chat.position === 'left') ? 'float-right' : 'float-left';
                    chatMessages += '<div class="' + messageClass + '">' +
                        '<div class="direct-chat-infos clearfix">' +
                        '<span class="direct-chat-name '+messageClassName+'">' + chat.user_name + '</span>' +
                        '<span class="direct-chat-timestamp '+messageClassTime+'">' + chat.created_at + '</span>' +
                        '</div>' +
                        '<img class="direct-chat-img" src="' +defaultImage + '" alt="Message User Image">' +
                        '<div class="direct-chat-text">' + chat.message + '</div>' +
                        '</div>';
                });
                $('#chat-messages').html(chatMessages);
                $('#chat-client').show();
            },

            error: function (xhr, status, error) {
                // Handle error response
                console.error('Failed to load chat history:', error);
            }
        });
    }
    $(document).on('click', '.edit-client', function() {
        var clientId = $(this).data('id');
        $.get('/clients/' + clientId + '/edit', function(data) {
            $('#client-id').val(data.id);
            $('#client-name').val(data.name);
            $('#client-email').val(data.email);
            $('#client-phoneNumber').val(data.phoneNumber);
            $('#editClientModal').modal('show');
        });
    });
    $('#save-client').click(function() {
        var clientId = $('#client-id').val();
        var formData = $('#editClientForm').serialize();
        // var url = $('#editClientForm').attr('action');
        $.ajax({
            url: '/client-update/' + clientId,
            type: 'POST',
            data: formData,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(data) {
                $('#editClientModal').modal('hide');
                table.ajax.reload();
                alert('Client updated successfully.');
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.log(jqXHR);
                if (jqXHR.status === 422) {
                    var errors = jqXHR.responseJSON.errors;
                    console.log(errors,'s');
                    $.each(errors, function(field, messages) {
                        var input = $('[name="' + field + '"]');
                        var errorDiv = $('<div class="error-message text-danger">' + messages[0] + '</div>');
                        input.after(errorDiv);
                    });
                } else {
                    alert('An error occurred while updating the client. Please try again.');
                    console.log(textStatus, errorThrown,'sadasd');
                }
            }
        });
    });
    // Close chat box popup when the close button is clicked

</script>
<script>

</script>

{{--<script  src="{{ asset('js/teams/list.js') }}"></script>--}}
@endsection
