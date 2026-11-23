@extends('layouts.master')

@section('title', 'New Agency')

@section('content')
    <div class="content-wrapper">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-8 offset-md-2">
                    <div class="card">
                        <div class="card-header bg-white">
                            <h3 class="card-title mb-0">New Agency / Supplier</h3>
                            <a href="{{ route('visa_umrah.agencies.index') }}" class="btn btn-secondary btn-sm float-right">Back</a>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('visa_umrah.agencies.store') }}" method="POST">
                                @csrf
                                @include('admin.visa_umrah.agencies._form')
                                <button class="btn btn-primary">Create Agency</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection