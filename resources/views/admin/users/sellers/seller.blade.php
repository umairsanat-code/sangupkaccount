@extends('layouts.master')
@section('title', 'Sellers List')
@section('style')
    {!! commonStyles() !!}
    {!! customDatatableCss() !!}
@endsection
@section('content')
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="mb-2 row">
                    <div class="col-sm-6">
                        <h1>App Users</h1>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12 col-sm-12 col-lg-12">
                        <div class="card"
                             style="background: #F3F4F6">
                            <div class="card-header"
                                 style="background-color: white">
                                <h3 class="card-title">All Sellers</h3>
                            </div>
                            <!-- /.box-header -->
                            @include('admin.users.sellers.inc.count')
                            <div class="card-body">

                                <table class="table table-striped table-bordered sellersTable"
                                       style="width: 100%;background: #F3F4F6">
                                    <thead>
                                        <tr class="bg-white">
                                            <th># No</th>
                                            <th>Seller Name</th>
                                            <th>Email</th>
                                            <th>Phone #</th>
                                            <th>Facebook/Google Login</th>
                                            <th>Status</th>
                                            <th></th>
                                        </tr>
                                    </thead>

                                </table>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- /.content -->
    </div>

    @include('admin.users.inc.aside')
      @include('admin.users.sellers.inc.rejectionMessage')

@endsection

@section('script')
    {!! commonScripts() !!}
    <script src="{{ asset('js/sellers/list.js') }}"></script>
    <script src="{{ asset('js/sellers/aside.js') }}"></script>
@endsection
