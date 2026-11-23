@extends('layouts.master')
@section('title','Roles & Permissions')
@section('style')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
 {!! commonStyles() !!}
 {!! customDatatableCss() !!}
{!! select2CssCustom() !!}
@endsection
@section('content')
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Roles Management</h1>
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
                            <h3 class="card-title" id="categoryTitle">Add New Role</h3>
                            </div>
                        <div class="card-body">
                          <form  class="products" id="addForm" method="post"  action="{{ route('categories.store') }}"  enctype="multipart/form-data">
                            @csrf
                            <div class="form-group">
                                <label>Role Title</label>
                                <input type="text" name="role" class="form-control" value="{{ old('name') }}" required autocomplete="off" id="name" placeholder="Enter role e.g Team,Manager">
                            </div>
                              <div class="form-group">
                                <label>Persmissions</label>
                                 <select class="form-control select3"style="width:100%" name="permissions[]"multiple="multiple" required>
                                 @foreach($permssions as $p)
                                  <option value="{{ $p->name }}">{{ ucfirst($p->moduleName) }}-{{ $p->name }}</option>
                                 @endforeach
                                 </select>
                                 <br>
                                <span>At least give 1 or more permissions to 1 role</span>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <button class="btn btn-primary btn-flat text-white active">Create Role</button> 
                                </div>
                                 <div class="col-md-4 mt-2">
                                    <a href="#"  id="clearAll"class="text text-bold text-info">Clear all</a> 
                                </div>
                                
                            </div>
                          </form>
                        </div>
                    </div>
                </div>
          <!-- end category create form -->
                  <div class="col-md-9">
                  <div class="card" style="background: #F3F4F6">
                    <div class="card-header" style="background-color: white">
                      <h3 class="card-title">Roles List</h3>
                    </div>
                       <!-- /.box-header -->
                    <div class="card-body">
                   
                    <table class="table table-striped table-bordered rolesTable" style="width: 100%;background: #F3F4F6">
                        <thead>
                        <tr class="bg-white">
                            <th># No</th>
                            <th>Role Title</th>
                            <th>Total Permissions</th>
                            <th>Permissions Action</th>
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
    <!-- /.content-wrapper -->
    @include('admin.roles.inc.add')
    @include('admin.roles.inc.show')
    @include('admin.roles.inc.update')
@endsection

@section('script')
{!! commonScripts() !!}
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script  src="{{ asset('js/roles/list.js') }}"></script>
@endsection
