@extends('layouts.master')
@section('title','Categories')
@section('style')
<style type="text/css">
.content-centered{

    margin-left:25%;
    margin-right:25%;

}
.form-control{
    height:50px;
}

</style>
@endsection
@section('content')
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="content-centered">
                <div class="row">
                    <div class="col-sm-12">
                        <h1 class="content-centered text-center">Commission Management</h1>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>

        <!-- Main content -->
        <section class="content">
        <div class="content-centered">
        <div class="row">
                <!-- category create form -->
                <div class="col-md-12 col-sm-12 col-lg-12">
                 
                    <div class="card" style="{{ !is_null($commission)?'display:none':'' }}" id="editCommissionCard">
                         <div class="card-header"style="background-color: white">
                            <h3 class="card-title">Define Commission</h3>
                            </div>
                        <div class="card-body">
                          <form method="post" action="{{ url('commissions') }}" id="commissionForm">
                            @csrf
                            <div class="row">
                              <div class="col-12">
                                <div class="form-group">
                                  <label>Commission in %</label>
                                  <input class="form-control" type="text" name="commission"
                                  onkeypress="return isNumberKey(event)" value="{{ $commission->amount??'' }}" placeholder="Enter value" id="commission" required autocomplete="off"> 
                                </div>
                              </div>
                            </div>
                            <div class="row">
                              <div class="col-12">
                                <button class="btn btn-success btn-flat btn-lg active" type="submit">Done</button>
                              </div>
                            </div>
                          </form>
                    
                        </div>
                    </div>
                  
                     <div class="card"style="{{ is_null($commission)?'display:none':'' }}" id="commissionCard">
                         <div class="card-header"style="background-color: white">
                            <h3 class="card-title">Eloumo Commission</h3>
                            </div>
                           <div class="card-body">
                             <div class="row">
                              <div class="col-12">
                               <h1>{{ $commission->amount??'' }}%</h1>
                               <div class="text text-right">
                                 <a href="#" class="btn btn-info btn-flat btn-sm editComission"><i class="fa fa-edit text-white"></i></a>
                               </div>
                              </div>
                            </div>
                          </div>
                    </div>
                   
                </div>
          <!-- end category create form -->
    
          </div>
        </div>
        </section>
        <!-- /.content -->
    </div>

@endsection

@section('script')
<script>
    @if(count($errors) > 0)
        @foreach($errors->categoryAdd->all() as $error)
            toastr.error("{{ $error }}");
        @endforeach
    @endif
</script>
<script src="{{ asset('js/commissions/commissions.js') }}"></script>

@endsection
