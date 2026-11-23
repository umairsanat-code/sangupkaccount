@extends('layouts.master')

@section('title', 'Edit Agency')

@section('content')
    <div class="content-wrapper">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-8 offset-md-2">
                    <div class="card">
                        <div class="card-header bg-white">
                            <h3 class="card-title mb-0">Edit Agency</h3>
                            <a href="{{ route('visa_umrah.agencies.show', $agency->id) }}" class="btn btn-secondary btn-sm float-right">Back</a>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('visa_umrah.agencies.update', $agency->id) }}" method="POST">
                                @csrf
                                @method('PUT')
                                @include('admin.visa_umrah.agencies._form')
                                <button class="btn btn-primary">Update Agency</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection