<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'TechSwivel') }}</title>
    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <style>
        .logo{width:20%;margin-bottom:60px;margin-top:40px}
          .active{
            background-color: #19A2FF !important;
            color:white !important;
        }
    </style>
</head>
<body class="hold-transition login-page" style="background-color: #19A2FF;">
<div class="login-box" >
    <!-- /.login-logo -->
    <div class="card">

        <div class="card-body login-card-body">
            <div class="container text-center">
            <img src="{{asset('img/logo/logo.svg')}}" class="img-responsive logo">
         </div>
            
            @if (session('status'))
            <div class="alert alert-success">
                {{ session('status') }}
            </div>
        @endif
            <form action="{{ route('password.email') }}" method="post">
                @csrf
                <div class="form-group mb-3">
                    <label>Email Address</label>
                    <input type="email"  name="email" value="{{old('email')}}" class="form-control"
                           placeholder="Email">
                   @if ($errors->has('email'))
                        <span class="invalid-feedback" style="display: block;" role="alert">
                    <strong>{{ $errors->first('email') }}</strong>
                 </span>
                    @endif
                </div>

                <div class="row">
                 <div class="col-12">
                        <button class="btn btn-primary btn-block active">Verify</button>
                    </div>
                    <div class="col-12 text-center" style="margin-top:25px;">
                        <a href="{{ route('login') }}" class="text text-center" style="color:#19A2FF">Back</a>
                    </div>
                </div>
            </form>
         </div>
    </div>
</div>

<script src="{{asset('js/app.js')}}"></script>
</body>
</html>
