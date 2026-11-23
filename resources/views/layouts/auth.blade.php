<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" href="{{ asset('/img/logo/logo.svg') }}" type="image/svg+xml">

    <title>{{ config('app.name', 'TechSwivel') }}</title>
    <!-- Styles -->
    <link href="{{ asset('/css/app.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/v4-shims.min.css" />
    <style>
         .active{
            background-color: #19A2FF !important;
            color:white !important;
        }
        .logo{width:130px;max-width:100%;margin-bottom:16px;margin-top:0}
        .auth-page{
            min-height:100vh;
            background: radial-gradient(circle at 12% 10%, rgba(29, 78, 216, 0.35), transparent 35%),
                        radial-gradient(circle at 90% 85%, rgba(14, 165, 233, 0.28), transparent 40%),
                        linear-gradient(125deg, #0f172a 0%, #1e293b 48%, #111827 100%);
            display:flex;
            align-items:center;
            justify-content:center;
            padding: 24px;
        }
    </style>
    @yield('style')
</head>
<body class="hold-transition auth-page">


    @yield('content')


<script src="{{asset('/js/app.js')}}"></script>
    @yield('scripts')
</body>
</html>
