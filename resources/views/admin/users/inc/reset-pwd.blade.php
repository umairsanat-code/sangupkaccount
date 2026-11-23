@extends('layouts.master')
@section('title', 'Buyers')
@section('style')
    {!! commonStyles() !!}
    {!! customDatatableCss() !!}
@endsection
@section('content')
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="mb-2 row">
                    <div class="col-sm-6">
                        <h1>Password Reset</h1>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>
        <section>
            <div class="card-body">
            <form action="{{ route('update-reset-pwd') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label for="new-password">New Password</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                    @if ($errors->has('password'))
                        <span class="text-danger">{{ $errors->first('password') }}</span>
                    @endif
                </div>
                <div class="form-group">
                    <label for="new-password_confirmation">Confirm Password</label>
                    <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                    @if ($errors->has('password_confirmation'))
                        <span class="text-danger">{{ $errors->first('password_confirmation') }}</span>
                    @endif
                </div>
                <button type="submit" class="btn btn-primary">Update Password</button>
            </form>
                <br>
            @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif
            </div>
        </section>
    </div>

@endsection
