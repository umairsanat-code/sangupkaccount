@extends('layouts.auth')
@section('name')
    
@endsection
@section('content')
<div class="login-box" >
 <!-- /.login-logo -->
 {{-- @dd( $error ,$errorMessage); --}}
   <div class="card">
    <div class="card-body login-card-body">
         <div class="container text-center">
            <img src="{{asset('img/logo/logo.svg')}}" class="img-responsive logo">
         </div>

            @if(session()->has('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <strong>Error!</strong> {{ session()->get('error') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif
            <form action="{{route('artisanlogin')}}" method="post">
                @csrf
                <div class="row">              
                    <div class="col-md-12 col-lg-12 col-sm-12 col-sx-12">
                        <label>Password</label>
                        <div class="input-group mb-3">
                            <input type="password" id="password" required name="password" class="form-control"
                                placeholder="Password">
                            <div class="input-group-append">
                                <div class="input-group-text">
                                    <span class="input-group-addon"><i id="PassShowHide" toggle="#password" class="fa fa-eye toggle-password"></i></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                   <div class="col-12">
                        <button class="btn btn-primary btn-block active" >Sign In</button>
                    </div>
                </div>
            </form>

        </div>
    </div>
</div>

@endsection


