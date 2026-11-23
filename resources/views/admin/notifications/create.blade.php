@extends('layouts.master')
@section('title','Notifcations')
@section('style')
<style type="text/css">
.content-centered{

    margin-left:25%;
    margin-right:25%;
    
}
.form-control{
    height:50px;
}
input[type=checkbox]{
    height:20px;
    width:20px;
}
</style>
@endsection
@section('content')
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Send Notifications <span class="dot"></span></h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item "><a href="#">Home</a></li>
                            <li class="breadcrumb-item active" style="color:#808000">Notifications</li>
                        </ol>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>
        <!-- Main content -->
        <section class="content">

            <!-- translations card -->
            
              <div class="container">
                    <div class="content-centered">
                        <div class="col-12">
                        <form  method="post" action="{{ route('send.notifications') }}" id="notificationsForm">
                        @csrf
                        <div class="row">
                            <div class="col-md-12 col-lg-12 col-sm-12">
                                <div class="form-group">
                                    <label>Title</label>
                                    <input type="text" name="title" class="form-control" placeholder="Notification title" required>
                                </div>
                            </div>
                            <div class="col-md-12 col-lg-12 col-sm-12">
                                <div class="form-group">
                                    <label>Description</label>
                                    <textarea name="description"
                                    rows="10" class="form-control" placeholder="Write notification description here..." required></textarea>
                                </div>
                            </div>
                             <div class="col-md-12 col-lg-12 col-sm-12">
                                <div class="icheck-primary d-inline">
                                <input type="checkbox" id="androidUsers" name="Android_Users" checked>
                                <label for="androidUsers">
                                Android Users</label>
                              </div>
                              &nbsp; &nbsp; 

                                 <div class="icheck-primary d-inline">
                                <input type="checkbox" id="iOSUsers" name="iOS_Users" checked>
                                <label for="iOSUsers">
                                iOS Users</label>
                              </div>

                            </div>

                             <div class="col-lg-12">
                                <div class="text text-right">
                               <button class="btn  active-nav pull-right btn-lg">Send</button>
                           </div>
                            </div>
                            
                        </div>

                    </form>
                </div>
                  
                </div>
                    
                    

                </div>
            
            <!-- /.card -->

        </section>
        <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->
@endsection
@section('script')
<script type="text/javascript">
$("#notificationsForm").on('submit', (function (e) {
  $.LoadingOverlay("show");
  }));
</script>
@endsection
