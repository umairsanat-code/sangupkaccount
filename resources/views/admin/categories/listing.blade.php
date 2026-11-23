@extends('layouts.master')
@section('title','Categories')
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
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Categories Management</h1>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>

        <!-- Main content -->
        <section class="content">
        <div class="container-fluid">
        <div class="row">
                <!-- category create form -->
                @can('Create App Categories')
                <div class="col-md-3 col-sm-3 col-lg-3">
                    <div class="card">
                         <div class="card-header"style="background-color: white">
                            <h3 class="card-title" id="categoryTitle">Add New Category</h3>
                            </div>
                        <div class="card-body">
                          <form  class="products" id="addForm" method="post"  action="{{ route('categories.store') }}"  enctype="multipart/form-data">
                            @csrf
                            <div class="form-group">
                                <label>Category Name</label>
                                <input type="text" name="name" class="form-control" value="{{ old('name') }}" required autocomplete="off" id="name" placeholder="Enter Category Name">
                            </div>
                              <div class="form-group">
                                <label>Category Image</label>
                               <input type="file" name="image"
                                      class="dropify" value=""
                                       data-height="100"
                                       data-max-file-size="5M"
                                       data-show-errors="false"
                                      accept="image/x-png,image/gif,image/jpeg,image/gif,image/jpg,image/svg,image/tiff"
                                      data-allowed-file-extensions="jpeg jpg png gif svg tiff"required>

                                <span>Max size: 5MB</span>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <button class="btn btn-primary text-white active-nav">Create Category</button>
                                </div>
                                 <div class="col-md-4 mt-2">
                                    <a href="#"  id="clearAll"class="text text-bold text-info">Clear all</a>
                                </div>

                            </div>
                          </form>
                        </div>
                    </div>
                </div>
                @endcan
          <!-- end category create form -->
                  <div class="{{ auth()->user()->can('Create App Categories')?'col-md-9':'col-md-12' }}">
                  <div class="card" style="background: #F3F4F6">
                    <div class="card-header" style="background-color: white">
                      <h3 class="card-title">Category List</h3>
                    </div>
                       <!-- /.box-header -->
                    <div class="card-body">

                    <table class="table table-striped table-bordered categoriesTable" style="width: 100%;background: #F3F4F6">
                        <thead>
                        <tr class="bg-white">
                            <th># No</th>
                            <th>Category Name</th>
                            <th>Total Subcategories</th>
                            <th>Subcategories Action</th>
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
    @include('admin.categories.inc.add')
    @include('admin.categories.inc.show')
    @include('admin.categories.inc.update')
@endsection

@section('script')
<script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.10.24/js/dataTables.bootstrap4.min.js"></script>
<script data-require="datatables-responsive@*" data-semver="2.1.0" src="//cdn.datatables.net/responsive/2.1.0/js/dataTables.responsive.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Dropify/0.2.2/js/dropify.min.js"></script>
<script  src="{{ asset('js/categories/list.js') }}"></script>
@endsection
